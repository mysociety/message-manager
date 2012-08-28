<?php
class Message extends AppModel {
	public $hasMany = array(
		'Action' => array(
			'className' 	=> 'Action',
			'foreignKey'	=> 'message_id',
			'order'			=> 'Action.created ASC',
			'dependent'		=> true,
		),
		'Reply' => array(
			'className' 	=> 'Message',
			'foreignKey'	=> 'parent_id',
		)
	);
	public $belongsTo = array(
		'Status' => array(
			'className' 	=> 'Status',
			'foreignKey'	=> 'status',
			'fields'		=> 'name'
		),
		'Lockkeeper' => array(
			'className' 	=> 'User',
			'foreignKey'	=> 'owner_id',
			'fields'		=> 'username'
		),
		'Source' => array(
			'className' 	=> 'MessageSource',
			'foreignKey'	=> 'source_id',
			'fields'		=> array('name', 'url')
		),
		'Parent' => array(
			'className' 	=> 'Message',
			'foreignKey'	=> 'parent_id',
			'fields'		=> array('from_address', 'message', 'is_outbound')
		),
	);
	public $actsAs = array('Tree');
	
	public $validate = array(
		// note MSISD is now also used for outgoing messages, e.g. username
		// In future may also be used for, e.g, twitter status: later, refactor by changing name?
		'from_address' => array(
			'rule'    => 'notEmpty',
			'message' => 'Address of sender may not be empty',
			'on'      => 'create'
		),
		'message' => array(
			'rule'    => 'notEmpty',
			'message' => 'Message text may not be empty',
			'on'      => 'create'
		),
		'source_id' => array(
			'rule'    => 'source_exists',
			'message' => 'Message source (source_id) provided did not match any known to Message Manager',
			'on'      => 'create'
		),
		'external_id' => array(
			'rule'    =>  'unique_id_by_source',
			'message' => 'A message from this source with this external ID already exists',
			'on'      => 'create'
		),
	);
	
	// beforeSave:
	// * generate sender_token:
	//   Save a unique-per-from_address token (sender_token) so FMS users can tell when two messages
	//   are from the same sender without knowing their phone numbers, etc.
	//   In anticipation of from_address being non-numeric (twitter handles, etc) this is done case-insensitively.
	//
	// * extract tag
	//   Scans the (presumably) incoming message for tags, possibly stripping them
	//   and storing in the record
	//   For example, incoming messages like "LUZ Hole in the road..." becomes
	//   tag: LUZ, message: "Hole in the road..."
	
	public function beforeSave() {
		if (!empty($this->data['Message']['from_address'])) {
			$this->data['Message']['sender_token'] = hash('md5', strtolower(trim($this->data['Message']['from_address'])));
		}
		if (!empty($this->data['Message']['message'])) {
			$message_text = $this->data['Message']['message'];
			if (!empty($message_text)) {
				$tags = Configure::read('tags');
				foreach ($tags as $tag => $desc) {
					$pattern = '/^\s*' . $tag . '\s*\b/i';
					if (preg_match($pattern, $message_text)) {
						$this->data['Message']['tag'] = $tag;
						if (Configure::read('remove_tags_when_matched')) {
							$this->data['Message']['message'] = preg_replace($pattern, "", $message_text);
						}
						break; // only test for one tag
					}
				}
			}	
		}
		return true;
	}
	
	
	public function is_locked() {
		return $this->seconds_until_lock_expiry() > 0;
	}
	
	// lock the record:
	// must have a user
	// won't do anything if the record is currently locked by someone else
	// returns null if it worked
	public function lock($user_id) {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} elseif (empty($user_id)) {
			return "no user (not logged in?)";
		} else {
			$current_owner = $this->data['Message']['owner_id'];
			if (!self::is_locked() || empty($current_owner) || $current_owner==$user_id) {
				$lock_expires = time() +  Configure::read('lock_expiry_seconds');
				$this->data['Message']['lock_expires'] =  date('Y-m-d H:i:s', $lock_expires);
				$this->data['Message']['owner_id'] =  $user_id;
				$this->data['Message']['session_key'] = null; // TODO: make locks session-based
				return null;
			} else {
				return "message locked by someone else";
			}
		}
	}

	// relinquish record lock
	public function unlock() {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			$this->data['Message']['lock_expires'] = null; 
			$this->data['Message']['owner_id'] = null;
			$this->data['Message']['session_key'] = null;
		}
	}

	// relinquish record lock
	public function hide() {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			$this->data['Message']['status'] = Status::$STATUS_HIDDEN;
		}
	}

	public function unhide() {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			$this->_revert_status();
		}
	}
	
	// assign this message to a FMS report
	public function assign_fms_id($fms_id) {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} elseif (empty($fms_id)) {
			throw new InternalErrorException('assign_fms_id called without an fms_id');
		} else {
			$this->data['Message']['fms_id'] = $fms_id; 
			$this->data['Message']['status'] = Status::$STATUS_ASSIGNED; 
		}
	}

	// unassign this message from a FMS report
	public function unassign_fms_id() {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			$this->data['Message']['fms_id'] = null; 
			$this->data['Message']['status'] = Status::$STATUS_AVAILABLE; 
		}
	}
		
	// returns number of seconds until lock expires
	// negative number indicates the expiry has passed
	public function seconds_until_lock_expiry() {
		if (empty($this->data['Message']['lock_expires'])) {
			return 0;
		} else {
			$expiry_time = strtotime($this->data['Message']['lock_expires']);
			return $expiry_time - time();
		}
	}
		
	// if a record is unhidden, its status depends on whether or not it has an FMS_id
	private function _revert_status() {
		if (empty($this->data['Message']['fms_id'])) {
			$this->data['Message']['status'] = Status::$STATUS_AVAILABLE;
		} else {
			$this->data['Message']['status'] = Status::$STATUS_ASSIGNED;
		}
	}
	
	// validation 
	public function unique_id_by_source($check) {
		$ex_id = array_values($check);
		$ex_id = $check['external_id'];
		if (empty($ex_id)) {
			return true;
		} else {
			$source_id = $this->data['Message']['source_id'];
			if (empty($source_id)) {
				$source_id = $this->Auth->user('source_id');
			}
			array_push($check, array('source_id' => $source_id));
			$msgs_with_ext_id = $this->find('count', array(
				'conditions' => $check,
				'recursive' => -1
			));
			return $msgs_with_ext_id == 0;
		}
	}
	
	public function source_exists($check) {
		$source = $this->Source->findById($check['source_id']);
		return !empty($source['Source']['id']);
	}

}