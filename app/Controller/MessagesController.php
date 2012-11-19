<?php

class MessagesController extends AppController {
	public $helpers = array('Js', 'Html', 'Form');
	
   //  RequestHandlerComponent lets json (and XML) work
	public $components = array(
		 'RequestHandler'
	);
	
	public $paginate = array(
		'order' => array('Message.created' => 'asc')
	);
	
	public function beforeFilter() {
		parent::beforeFilter();
		// these are the API methods for which Basic HTTP Auth is enabled
		$api_methods = array(
			'assign_fms_id',
			'available', 
			'incoming',
			'lock',
			'lock_unique',
			'reply',
			'unlock',
			'unlock_all',
			);

		// would prefer to try Form first, because Basic logins stick in htauth
		// but contrary to expectations, that doesn't seem to be working (?)
		if ($this->RequestHandler->accepts('json')) {
			$this->response->header('Access-Control-Allow-Origin', Configure::read('cors_allowed'));
			$this->Auth->authenticate = array('Basic');
			if ( in_array($this->action, $api_methods) ) {
				if ($this->Auth->loggedIn() || $this->Auth->login()) {
					$this->set('username', $this->Auth->user('username'));
					// do nothing more: they're logged in
				} else {
					throw new ForbiddenException('Not logged in');
				}
			}
		} else {
			$this->Auth->authenticate = array('Form');
		}
		// loading models here just to access the static methods on them (hmmm, seems a bit... heavyweight)
		Controller::loadModel('ActionType'); 
		Controller::loadModel('Group'); 
		Controller::loadModel('Status'); 
		Controller::loadModel('Message');
	}
	
	// index shows all messages... maybe filtered on is_outbound;
	// note: convenience utility: send ?recover_tree=1 to force a TreeBehaviour rebuild on Messages
	public function index($direction = null) {
		if (isset($this->request->query['recover_tree'])) {
			$success = $this->Message->recover();
			$this->Session->setFlash($success? __('Reply thread tree has been recovered.') : __('Reply thread tree recovery failed'));
			$this->redirect(array('action' => 'index'));
			return;
		}
		$title = "";
		if ($direction == 'sent') {
			$title = __("Messages sent");
			$conditions = array('Message.is_outbound' => 1);
		} elseif ($direction == 'received') {
			$title = __("Messages received");
			$conditions = array('Message.is_outbound' => 0);
		} else {
			$title = __("All messages (received and sent)");
			$conditions = array();
		}
		$this->paginate = array(
			'recursive' => 0,
			'conditions' => $conditions,
		);
		$this->set('title', $title);
		$this->set('messages', $this->paginate('Message'));
	}

	// get available messages: 
	// provided as the main AJAX call for FMS to populate its messages
	// NB strips unwanted message details, e.g., no from_address etc, because this is
	// used to serve requests "outside" the Message Manager itself
	// only serve messages with matching tag
	// ---------------------------------------------------------------------------------
	// note: this automatically exludes messages with status "available" if they are not
	//		 root-level messages, that is, if they are not replies
	//		 This might not be what you're expecting since it means not all messages with
	//		 status='available' are actually available. Hmm.

	public function available() {
		// note: auth user is cached, so if you edit tags the user concerned (and that's you if
		//       you edit your own tags) you'll need to log out for changes to take effect
		$allowed_tags = $this->Auth->user('allowed_tags'); 
		$conditions = array_merge(
			array(
				'Message.status' => Status::$STATUS_AVAILABLE, 
				'Message.parent_id' => null
			),
			Message::get_tag_conditions($allowed_tags)
		);
		$this->Message->recursive = 1;
		$this->Message->Behaviors->attach('Containable');
		$messages = $this->Message->find('threaded',
			array(
				'conditions' => $conditions,
				'recursive' => 1,
				'fields'	=> self::_json_fields(),
				'contain' => array('Source', 'Status', 'Lockkeeper'),
				'order' => array('Message.created ASC'),
				'limit' => 20 // for now FIXME -- paginate?
			)
		);
		foreach ($messages as &$message) {
			 $subtree = $this->Message->find('threaded', array(
				'conditions' => array(
					'Message.lft >=' => $message['Message']['lft'], 
					'Message.rght <=' => $message['Message']['rght'],
					'Message.status !=' => Status::$STATUS_HIDDEN 
				),
				'fields'	=> self::_json_fields(),
				'contain' => array('Source', 'Status', 'Lockkeeper'),
			));
			if (! empty($subtree)) {
				$message['children'] = $subtree[0]['children'];
			}
		}
		$this->set('messages', $messages);	
		$this->set('allowed_tags', $allowed_tags);
		$this->helpers[] = 'MessageUtils';
	}

	public function view($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) { // kinder than a 404
			$this->Session->setFlash(__('No such message (id ' . $id . ")"));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->helpers[] = 'MessageUtils';
			$this->Message->recursive=2; // to get the user and type values
			$message = $this->Message->find('first', array(
				'conditions' => array('Message.id' => $id),
			));
			$this->Message->Behaviors->attach('Containable');
			$messages = $this->Message->find('threaded', array(
				'conditions' => array(
					'Message.lft >=' => $message['Message']['lft'], 
					'Message.rght <=' => $message['Message']['rght']
				),
				'contain' => array(), // empty, restricts to just the Message
			));
			$children = array();
			if (! empty($messages)) {
				$children = $messages[0]['children'];
			}
			$this->set('message', $message);
			$this->set('children', $children);
			$this->set('is_locked', $this->Message->is_locked()? 1 : 0);
			$this->set('seconds_until_lock_expiry', $this->Message->seconds_until_lock_expiry());
			$this->set('has_send_failures', (
				$message['Message']['send_fail_count']  || 
				$message['Message']['send_fail_reason'] || 
				$message['Message']['send_failed_at']));
		}
	}

	// note: better if JSON returned the message data even on failure to grant a lock,
	// since it's an opportunity for the client to update the message list?
	public function lock($id = null, $want_unique_lock = false) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		self::_load_record($id);
		$flash_msg = "";
		$lock_err = $this->Message->lock($this->Auth->user('id'));
		if (empty($lock_err) && $this->Message->save()) {
			self::_logAction(ActionType::$ACTION_LOCK);
			$msg_unlocked = '';
			if ($want_unique_lock) {
				$all_locked = $this->Message->find('all', 
					array(
						'conditions' => array(
							'Message.id !=' => $id,
							'Message.owner_id' => $this->Auth->user('id'),
							"NOT" => array("Message.lock_expires" => null)
						)
					)
				);
				if (count($all_locked)>0) {
					foreach($all_locked as $message) {
						$this->Message->read(null, $message['Message']['id']);
						$this->Message->unlock();
						$this->Message->save();
					}
					$msg_unlocked = __(', other locks released: %s', count($all_locked));
				}				
			}
			if ($this->RequestHandler->accepts('json')) {
				$this->Message->recursive = 0;
				$message = $this->Message->read(self::_json_fields(), $id);
		$this->response->body( json_encode(self::mm_json_response(true, $message) ) );
		return $this->response;
			} else {
				$this->Session->setFlash(__('Message locked (expires in %s seconds)%s', 
					Configure::read('lock_expiry_seconds'), $msg_unlocked));
			}
		} else {
			$err_msg = __("Lock not granted: " . $lock_err );
			if ($this->RequestHandler->accepts('json')) {
		$this->response->body( json_encode(self::mm_json_response(false, null, $err_msg)) );
		return $this->response;
			}
			$this->Session->setFlash(__($err_msg));
		}
		$this->redirect(array('action' => 'view', $id));
	}

	// edit lets managers change status or tag (but nothing else)
	public function edit($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__('Invalid message'));
		}
		$is_admin_group = ($this->Auth->user('group_id') == Group::$ADMIN_GROUP_ID)? 1 : 0;
		if ($this->request->is('post') || $this->request->is('put')) {
			$saved_ok = false;
			if (! $is_admin_group) {
				// currently only the tag is editable by managers
				$this->Message->set('tag', $this->request->data['Message']['tag']);
				$this->Message->set('parent_id', $this->request->data['Message']['parent_id']);
				$save_ok = $this->Message->save();
			} else { // admins can edit anything (if the view exists)
				$saved_ok = $this->Message->save($this->request->data);
			}
			if ($saved_ok) {
				$this->Session->setFlash(__('The message has been updated'));
				$this->redirect(array('action' => 'view', $id));
			} else {
				$this->Session->setFlash(__('The message  could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Message->read(null, $id);
		}
		// actually, don't allow status edit: there are buttons for that which are better (i.e., reverting when unhiding)
		// $this->set('statuses', $this->Message->Status->find('list', array('conditions' => array('name !=' => 'unknown')))); // populate the drop-down
		$this->set('message', $this->Message->findById($id));
		$this->set('is_admin_group', $is_admin_group);
	}
	
	//-----------------------------------------------------------------
	// TODO: reply not implemented yet
	// reply creates a new message object, and queues it to be sent
	//-----------------------------------------------------------------
	public function reply($id = null) {
		if (! $this->Auth->user('can_reply')==1) {
			$deny_msg = "Cannot reply: user " . $this->Auth->user('username') . " lacks reply privilege";
			self::_logAction(ActionType::$ACTION_NOTE, "attempt to reply to message " . $id . " denied");
			if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(false, null, $deny_msg)) );
				return $this->response;
			} else {
				$this->Session->setFlash($deny_msg);
				$this->redirect(array('action' => 'view', $id));
			}
		} else {
			self::_load_record($id);
			if ($this->request->is('post')) {
				$reply_text = $this->request->data('reply_text');
				if (empty($reply_text)) {
						$err_msg = "Empty reply text: won't send reply";
				} else {
					$lock_err = $this->Message->lock($this->Auth->user('id'));
					if (empty($lock_err)) {
						// fake success TODO
						$reply = new Message;
						$reply->create();
						$reply->save(array(
								'parent_id' =>	$id,
								'is_outbound' => 1,
								'message' => $reply_text,
								'status' => Status::$STATUS_PENDING,
								'from_address' => $this->Auth->user('username'),
								'to_address' => $this->Message->data['Message']['from_address']
						));
						// consider sending reply->id back with the success response
						self::_logAction(ActionType::$ACTION_REPLY, "Reply: " . $reply_text, $reply->id);
						if (! $this->Message->data['Message']['replied']) {
							$this->Message->data['Message']['replied']=date('Y-m-d H:i:s'); // date of *most recent* reply
							$this->Message->save();
						}
						
						// FIXME: recent problems with treeBehaviour in demos? try forcing recover_tree
						// FIXME: once we've seen if this is the issue, deal with it
						// FIXME: don't leave this in production because it won't scale!
						$success = $this->Message->recover();
						
						if ($this->RequestHandler->accepts('json')) {
							$this->response->body( json_encode(self::mm_json_response(true, null)) );
							return $this->response;
						} else {
							$err_msg = "Reply sent OK.";
						}
					} else {
						$err_msg = "reply failed: " . $lock_err;
					}
				}
				if ($this->RequestHandler->accepts('json')) {
					$this->response->body( json_encode(self::mm_json_response(false, null, $err_msg)) );
					return $this->response;
				}
				$this->Session->setFlash($err_msg);
				$this->redirect(array('action' => 'view', $id));
			} else { // not a POST request
				if ($this->RequestHandler->accepts('json')) {
					throw new MethodNotAllowedException();
				}
				if (! $this->Auth->user('can_reply')==1) {
					$this->Session->setFlash($deny_msg);
					$this->redirect(array('action' => 'view', $id));
				} else {
					$this->set('message', $this->Message->data);
				}
			}
		}
	}
	
	// same as lock except also relinquishes all other locks held by this user
	// note: maybe this should be for this user in this session? 
	public function lock_unique($id = null) {
		return self::lock($id, true);
	}

	public function unlock($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$unlocked_msg = "";
		self::_load_record($id);
		$this->Message->unlock();
		if ($this->Message->save()) {
			self::_logAction(ActionType::$ACTION_UNLOCK);
			$unlocked_msg=__('Removed lock from message');
		} else {
			$unlocked_msg__('Failed to release lock on message');
		}
		if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(false, null, $unlocked_msg)) );
		return $this->response;
		}
		$this->Session->setFlash($unlocked_msg);
		$this->redirect(array('action' => 'view', $id));
	}

	public function unlock_all() {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$unlocked_msg = "";
		$all_locked = $this->Message->find('all', 
			array(
				'conditions' => array(
					'Message.owner_id' => $this->Auth->user('id'),
					"NOT" => array("Message.lock_expires" => null)
				)
			)
		);
		if (count($all_locked)>0) {
			foreach($all_locked as $message) {
				$this->Message->read(null, $message['Message']['id']);
				$this->Message->unlock();
				$this->Message->save();
			}
			$unlocked_msg = __('locks released: %s', count($all_locked));
		} else {
			$unlocked_msg = __('no locks to release');
		}
		if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(false, null, $unlocked_msg)) );
		return $this->response;
		}
		$this->Session->setFlash($unlocked_msg);
		$this->redirect(array('action' => 'index'));
	}

	public function assign_fms_id($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$fms_id = $this->request->data('fms_id');
		if (empty($fms_id)) {
			$err_msg = __("Not assigned: missing FMS ID");
			if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(false, null, $err_msg)) );
			}
			$this->Session->setFlash($err_msg);
		} else {
			self::_load_record($id);
			$lock_err = $this->Message->lock($this->Auth->user('id'));
			if ($lock_err) {
				$err_msg = __("Not assigned: " + $lock_err);
				if ($this->RequestHandler->accepts('json')) {
					$this->response->body( json_encode(self::mm_json_response(false, null, $err_msg)) );
					return $this->response;
				}
				$this->Session->setFlash($err_msg);
			} else {
				$this->Message->assign_fms_id($fms_id);
				if ($this->Message->save()) {
					self::_logAction(ActionType::$ACTION_ASSIGN, $fms_id);
					if ($this->RequestHandler->accepts('json')) {
							$this->response->body( json_encode(self::mm_json_response(true, null)) );
			  return $this->response;
					}
					$this->Session->setFlash(__('Message assigned to FMS report %s', $fms_id));
				} else {
					$err_msg = __('Failed to assign FMS report %s to message', $fms_id);
					if ($this->RequestHandler->accepts('json')) {
							$this->response->body( json_encode(self::mm_json_response(false, null, $err_msg)) );
			  return $this->response;
					}
					$this->Session->setFlash($err_msg);
				}
			}
		}
		$this->redirect(array('action' => 'view', $id));
	}

	public function unassign_fms_id($id = null) {
		self::_load_record($id);
		$fms_id = $this->Message->data['Message']['fms_id'];
		$this->Message->unassign_fms_id();
		if ($this->Message->save()) {
			self::_logAction(ActionType::$ACTION_UNASSIGN);
			$this->Session->setFlash(__('Unassigned message from FMS report %s', h($fms_id)));
		} else {
			$this->Session->setFlash(__('Failed to unassign message from FMS report %s',  h($fms_id)));
		}
		$this->redirect(array('action' => 'view', $id));
	}
	
	public function hide($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		self::_load_record($id);
		$reason_text = $this->request->data('reason_text');
		
		$this->Message->hide($reason_text); 
		if ($this->Message->save()) {
			self::_logAction(ActionType::$ACTION_HIDE, $reason_text);
			$msg = __('Message hidden');
			if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(true, null)) );
				return $this->response;
			}
		} else {
			$msg = __('Failed to hide message');
			if ($this->RequestHandler->accepts('json')) {
				$this->response->body( json_encode(self::mm_json_response(false, null, $msg)) );
				return $this->response;
			}
		}
		$this->Session->setFlash($msg);
		$this->redirect(array('action' => 'view', $id));
	}

	public function unhide($id = null) {
		self::_load_record($id);
		$this->Message->unhide();
		if ($this->Message->save()) {
			self::_logAction(ActionType::$ACTION_UNHIDE);
			$this->Session->setFlash(__('Message no longer hidden'));
		} else {
			$this->Session->setFlash(__('Failed to unhide message'));
		}
		$this->redirect(array('action' => 'view', $id));
	}
	
	/**
	 * delete method
	 *
	 * @param string $id
	 * @return void
	 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		self::_load_record($id);
		if ($this->Message->delete()) {
			$this->Session->setFlash(__('Message deleted'));
		} else {
			$this->Session->setFlash(__('Message was not deleted'));
		}
		$this->redirect(array('action' => 'index'));
	}
	
	// DON'T USE THIS! TODO: prevent?
	// should be generated by incoming messages from gateway
	public function add() {
		$this->Session->setFlash(__("Don't use /add to enter new messages: " .
			"use /messages/incoming instead, logged into the message-sources user group."));
		$this->redirect(array('controller' => 'Pages', 'action' => 'display/api'));
		// if ($this->request->is('post')) {
		//	$this->Message->create();
		//	if ($this->Message->save($this->request->data)) {
		//		$this->Session->setFlash(__('The message has been saved'));
		//		$this->redirect(array('action' => 'index'));
		//	} else {
		//		$this->Session->setFlash(__('The message could not be saved. Please, try again.'));
		//	}
		// } 
		// $this->set('sources', $this->Message->Source->find('list')); // populate the drop-down
	}

	//-----------------------------------------------------------------------------------------------------------
	// "incoming" is used to create a new message in the Message Manager: that is, an SMS gateway (for example)
	//	can pass incoming messages to MM by hitting this URL (with a POST, and params).
	//	Effectively this is an alias for the add action, which (see above) is disabled.
	//	This incoming method is a hypothetical once since you might not have the choice of how the gateway
	//	access the MM, but it's used by the dummy client for testing, and serves as an example if you need
	//	to roll your own.
	//-----------------------------------------------------------------------------------------------------------
	public function incoming() {
		if (!$this->request->is('post')) { // for now
			throw new MethodNotAllowedException(); 
		}
		$return_code = 200;
		$response_text = "";
		$this->Message->create();
		$this->Message->set($this->request->data);
		//---------------
		// If there's an explicit source_id, find the user it should be...
		// ...otherwise if there was no source_id, infer it from the current user.
		// After checking this, $source_user_id will be null if there's no match.
		//---------------
		Controller::loadModel('MessageSource'); // really?	This needs tidying!... should be using user's MessageSource association
		$source_user_id = $this->Auth->user('id');
		$source_by_user = $this->MessageSource->findByUserId($source_user_id);
		// for now, infer source_id from user unless it's been explicitly sent
		$source_id = empty($this->Message->data['Message']['source_id'])? $source_by_user : $this->Message->data['Message']['source_id'];
		if (empty($source_id)) {
			$return_code = 404;
			$response_text = __("No such source\nCould not determine which message source is submitting the message (log in as an allocated user perhaps?).");
		} elseif (empty($source_by_user)) {
			$return_code = 403;
			$response_text = __("Forbidden\nUser \"%s\" is not currently allocated to any message source: cannot submit incoming messages.",  $this->Auth->user('username'));
		} elseif ($source_by_user['MessageSource']['id'] != $source_id) {
			$return_code = 403;
			$response_text = __("Forbidden\nUser \"%s\" is not this source's allocated user (user's allocated gateway is %s)",
				$this->Auth->user('username'), 
				(empty($source_by_user['MessageSource']['name'])? $source_by_user['MessageSource']['id'] : $source_by_user['MessageSource']['name'])
			);
		} else {
			$this->Message->set('status', Status::$STATUS_AVAILABLE);
			$this->Message->set('is_outbound', false);
			if (! $this->Message->validates()) {
				$response_text = __("Failed\nthe incoming message had validation errors:\n\n");
				foreach ($this->Message->validationErrors as $field => $error) { 
					$response_text .= __("error: %s\n", $error[0]); 
				}
			} elseif ($this->Message->save()) {
				$response_text = __("OK\nSaved message id=%s", $this->Message->id);
				// check to see if this looks like a reply: 
				//	  -- has no tag (easiest to detect *after* the save, but not beautiful)
				//	  -- sent with a from_address that was the to_address of a message sent out in the last N days
				self::_load_record($this->Message->id);
				if (empty($this->Message->data['Message']['tag'])) {
					$response = $this->Message->find('first', array(
						'conditions' =>	 array(
							'Message.to_address' => $this->Message->data['Message']['from_address'],
							'Message.created >=' => date('Y-m-d', strtotime('-' . Configure::read('autodetect_reply_period')))
						),
						'order' => array('Message.created' => 'desc')
					));
					if ($response) {
						$this->Message->set('parent_id', $response['Message']['id']);
						if ($this->Message->save()) {
							$response_text .=  "\n" . __("Assumed to be a reply to message id=%s", $response['Message']['id']);
						} // fail silently: the initial message was saved, but its reply-status was not; not a crisis
					}
				}
			} else {
				$return_code = 500;
				$response_text = __('Failed\nunexpected error, the message could not be saved.');
			}
		}
		return new CakeResponse(array(
			'statusCode' => $return_code,
			'type' => 'text',
			'body' => $response_text . "\n" 
		)); 
	}
	
	// purge all expired locks from the data
	public function purge_locks() {
		$this->Message->recursive = 0;
		$this->Message->updateAll(
			array('Message.lock_expires' => null, 'Message.owner_id' => null),
			array('Message.lock_expires <=' => date('Y-m-d H:i:s', time()))
		);
		$this->Session->setFlash(__('All expired locks have been purged.'));
		$this->redirect(array('action' => 'index'));
	}

	private function _load_record($id) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) {
			throw new NotFoundException(__("No message found with id=\"%s\"", $id));
		}
		$this->Message->read(null, $id);
	}
	
	// note: check for null is redundant when ACLs/Auth is enabled
	private function _current_user_is_owner() {
		return $this->Message->data['Message']['owner_id']==$this->Auth->user('id')
			&& $this->Auth->user('id');
	}
	
	// fields that are OK to send with available/lock/etc AJAX calls 
	private function _json_fields() {
		return array(
			'id', 'source_id', 'external_id', 'message', 'created', 'received',
			'replied', 'sender_token', 'is_outbound',
			'lock_expires', 'status', 'owner_id', 'fms_id', 'tag', 'Source.id',
			'Source.name', 'Status.name', 'Lockkeeper.username',
			'lft', 'rght', 'parent_id'
		);
	}
	
	// logging an action creates an Action entry
	private function _logAction($action_type, $custom_param_1=null, $custom_param_2=null) {
		Controller::loadModel('Action');
		$action = new Action;
		$params = array(
			'type_id' =>  $action_type,
			'user_id' => $this->Auth->user('id'),
			'message_id' => $this->Message->id,
		);
		if (! Configure::read('log_lock_actions') &&
			($action_type==ActionType::$ACTION_LOCK || $action_type==ActionType::$ACTION_UNLOCK)) {
				return; // don't log lock activity unless explicitly configured to do so
		}
		if ($action_type==ActionType::$ACTION_NOTE) {
			$params['note'] = $custom_param_1;			
		} elseif ($action_type==ActionType::$ACTION_REPLY) {
			$params['note'] = $custom_param_1;
			$params['item_id'] = $custom_param_2;
		} elseif ($action_type==ActionType::$ACTION_ASSIGN) {
			$params['item_id'] = intval($custom_param_1);
		} elseif ($action_type==ActionType::$ACTION_HIDE) {
			$params['note'] = $custom_param_1;
		}
		$action->create($params);
		$action->save();
	}

}
