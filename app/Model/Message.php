<?php
class Message extends AppModel {
	public $hasMany = array(
		'Action' => array(
			'className' 	=> 'Action',
			'foreignKey'	=> 'message_id',
			'order'			=> 'Action.created ASC',
			'dependent'		=> true,
		)
	);
	public $belongsTo = array(
		'Status' => array(
			'className' 	=> 'Status',
			'foreignKey'	=> 'status',
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
		)
		
	);
	
	public $validate = array(
		'msisdn' => array(
			'rule'    => '/^[0-9]{8,17}$/i',
			'message'  => 'MSISDN must be comprised of 8-17 digits only',
			'on'         => 'create'
		),
		'message' => array(
			'rule'    => 'notEmpty',
			'message' => 'Message text may not be empty',
			'on'         => 'create'
		),
		'source_id' => array(
			'rule'    => 'source_exists',
			'message' => 'Message source (source_id) provided did not match any known to Message Manager',
			'on'         => 'create'
		)
	);
	
	// save a unique-per-msisdn token (sender_token) so FMS users can tell when two messages
	// are from the same sender without knowing their MSISDNs.
	// In anticipation of MSISDNs being non-numeric (twitter handles, etc) this is done case-insensitively.
	public function beforeSave() {
	    if (!empty($this->data['Message']['msisdn'])) {
	        $this->data['Message']['sender_token'] = hash('md5', strtolower(trim($this->data['Message']['msisdn'])));
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
	
	public function source_exists($check) {
		$source = $this->Source->findById($check['source_id']);
		return !empty($source['Source']['id']);
	}

		
}