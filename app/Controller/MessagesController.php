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

		if ( $this->request->is('options') ) {
			$this->response->header('Access-Control-Allow-Origin', Configure::read('cors_allowed'));
			$this->response->header('Access-Control-Allow-Credentials', 'true');
			$this->response->header('Access-Control-Allow-Headers', 'Origin, Accept, Authorization, Content-Type,  Depth,  User-Agent,  X-File-Size,  X-Requested-With,  If-Modified-Since,  X-File-Name,  Cache-Control');
			$this->response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
			$this->response->send();
			// otherwise it send things that upset the CORS pre-flight request
			exit();
		}

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
		Controller::loadModel('ActionType'); // to access static methods on it
		Controller::loadModel('Status'); // to access static methods on it
		Controller::loadModel('Group'); // to access static methods on it
	}
	
	// index shows all messages... maybe filtered on is_outbound;
	public function index() {
		$title = "";
		if (isset($this->request->query['is_outbound'])) {
			if ($this->request->query['is_outbound'] == 1) {
				$title = __("Messages sent");
				$conditions = array('Message.is_outbound' => 1);
			} else {
				$title = __("Messages received");
				$conditions = array('Message.is_outbound' => 0);
			}
		} else {
			$title = __("All messages");
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
    public function available() {
		$this->Message->recursive = 1;
		$allowed_tags =  $this->Auth->user('allowed_tags');
		$conditions = array('Message.status' => Status::$STATUS_AVAILABLE);
		// TODO really, allowed tags should be comma-separated list; for now, consider it a single tag
		if (! empty($allowed_tags)) {
			$conditions['Message.tag'] = strtoupper(trim($allowed_tags));
		}
		$messages = $this->Message->find('all',
			array(
				'conditions' => $conditions,
				'recursive' => 0,
				'fields'	=> self::_json_fields(),
				'order' => array('Message.created ASC'),
				'limit' => 20 // for now FIXME -- paginate?
			)
		);
		$this->set('messages', $messages);	
	}

	public function view($id = null) {
		$this->Message->id = $id;
		if (!$this->Message->exists()) { // kinder than a 404
			$this->Session->setFlash(__('No such message (id ' . $id . ")"));
			$this->redirect(array('action' => 'index'));
		} else {
			$this->helpers[] = 'MessageUtils';
			$this->Message->recursive=2; // to get the user and type values
			$this->set('message', $this->Message->read(), $id);
			$this->set('is_locked', $this->Message->is_locked()? 1 : 0);
			$this->set('seconds_until_lock_expiry', $this->Message->seconds_until_lock_expiry());
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
				$this->Message->tag = $this->request->data['Message']['tag'];
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
		$this->set('message', $this->Message->data);
		$this->set('is_admin_group', $is_admin_group);
	}

	//-----------------------------------------------------------------
	// TODO: reply not implemented yet
	// reply creates a new message object, and queues it to be sent
	//-----------------------------------------------------------------
	public function reply($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		self::_load_record($id);
		$reply_text = $this->request->data('reply_text');
		$err_msg = "Reply to message (via source) not implemented yet: TODO";
		if (empty($reply_text)) {
			$err_msg = "Empty reply text: won't send reply";
		} elseif (! $this->Auth->user('can_reply')==1) {
			$err_msg = "User " . $this->Auth->user('username') . " lacks reply privilege";
			self::_logAction(ActionType::$ACTION_NOTE, "attempt to reply to message " . $id . " denied");
		} else {
			$lock_err = $this->Message->lock($this->Auth->user('id'));
			if (empty($lock_err)) {
				// fake success TODO
				$reply = new Message;
				$reply->create();
				$reply->save(array(
						'parent_id' =>  $id,
						'is_outbound' => 1,
						'message' => $reply_text,
						'status' => Status::$STATUS_PENDING,
						'from_address' => $this->Auth->user('username'),
						'to_address' => $this->Message->data['Message']['from_address']
				));
				// consider sending reply->id back with the success response
				self::_logAction(ActionType::$ACTION_REPLY, "Reply: " . $reply_text, $reply->id);
				if (! $this->Message->data['Message']['replied']) {
					$this->Message->data['Message']['replied']=1; // set the flag (hmm, not using this)
					$this->Message->save();
				}
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
		self::_load_record($id);
		$this->Message->hide(); 
		if ($this->Message->save()) {
			self::_logAction(ActionType::$ACTION_HIDE);
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
		// 	$this->Message->create();
		// 	if ($this->Message->save($this->request->data)) {
		// 		$this->Session->setFlash(__('The message has been saved'));
		// 		$this->redirect(array('action' => 'index'));
		// 	} else {
		// 		$this->Session->setFlash(__('The message could not be saved. Please, try again.'));
		// 	}
		// } 
		// $this->set('sources', $this->Message->Source->find('list')); // populate the drop-down
	}

	// alias for add... although this may be customised depending on currently unknown behaviour
	// of message sources (e.g. might be a get not a post, etc)
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
		Controller::loadModel('MessageSource'); // really?  This needs tidying!
		$source_user_id = $this->Auth->user('id');
		$source_by_user = $this->MessageSource->findByUserId($source_user_id, array('fields'=>'id'));
		// infer source_id from user unless it's been explicitly sent
		$source_id = empty($this->Message->data['Message']['source_id'])? $source_by_user : $this->Message->data['Message']['source_id'];
		if (empty($source_by_user)) {
			$return_code = 403;
			$response_text = __("Forbidden\nUser %s is not currently allocated to any message source: cannot submit incoming messages.",  $this->Auth->user('username'));
		} elseif ($source_by_user != $source_id) {
			$return_code = 403;
			$response_text = __("Forbidden\nUser %s is not this source's allocated user.",  $this->Auth->user('username'));
		} else {
			$this->Message->set('status', Status::$STATUS_AVAILABLE);
			if (! $this->Message->validates()) {
				$response_text = __("Failed\nthe incoming message had validation errors:\n\n");
				foreach ($this->Message->validationErrors as $field => $error) { 
					$response_text .= __("error: %s\n", $error[0]); 
				}
			} elseif ($this->Message->save()) {
				$response_text = __("OK\nSaved message id=%s", $this->Message->id);
			} else {
				$return_code = 500;
				$response_text = __('Failed\nunexpected error, the message could not be saved.');
			}
		}
		return new CakeResponse(array(
			'statusCode' => $return_code,
			'type' => 'text',
			'body' => $response_text . "\n" 
		));	}
	
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
			throw new NotFoundException(__('Invalid message'));
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
			'replied', 'sender_token', 
			'lock_expires', 'status', 'owner_id', 'fms_id', 'tag', 'Source.id',
			'Source.name', 'Status.name', 'Lockkeeper.username'
		);
	}
	
	private function _logAction($action_type, $custom_param_1=null, $custom_param_2=null) {
		$action = new Action;
		$params = array(
			'type_id' =>  $action_type,
			'user_id' => $this->Auth->user('id'),
			'message_id' => $this->Message->id,
		);
		if ($action_type==ActionType::$ACTION_NOTE) {
			$params['note'] = $custom_param_1;			
		} elseif ($action_type==ActionType::$ACTION_REPLY) {
			$params['note'] = $custom_param_1;
			$params['item_id'] = $custom_param_2;
		} elseif ($action_type==ActionType::$ACTION_ASSIGN) {
			$params['item_id'] = intval($custom_param_1);
		}
		$action->create($params);
		$action->save();
	}

}
