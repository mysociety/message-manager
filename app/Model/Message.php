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
		'parent_id' => array(
			'rule'    => 'parent_exists_if_specified',
			'required' => false,
			'allowEmpty' => true,
			'message' => 'The parent_id must be the ID for an existing message (and not the message itself)',
			'on'      => 'edit'
		)
	);
	
	// beforeSave:
	// * generate sender_token:
	//   Save a unique-per-from_address token (sender_token) so FMS users can tell when two messages
	//   are from the same sender without knowing their phone numbers, etc.
	//   In anticipation of from_address being non-numeric (twitter handles, etc) this is done case-insensitively.
	//   Note that outbound messages (that is, message sent from Message Manager (i.e.., replies via FMS)) do
	//   not strip/hide the from-address, which is the username of the MM user).
	//
	// * extract tag
	//   Scans the (presumably) incoming message for tags, possibly stripping them
	//   and storing in the record
	//   For example, incoming messages like "LUZ Hole in the road..." becomes
	//   tag: LUZ, message: "Hole in the road..."
	
	public function beforeSave() {
		if (!empty($this->data['Message']['from_address'])) {
			if ($this->data['Message']['is_outbound']) {
				$this->data['Message']['sender_token'] = $this->data['Message']['from_address'];
			} else {
				$this->data['Message']['sender_token'] = hash('md5', strtolower(trim($this->data['Message']['from_address'])));
			}
		}
		if (!empty($this->data['Message']['message'])) {
			$message_text = $this->data['Message']['message'];
			if (!empty($message_text)) {
				$tag_data = Message::separate_out_tags($message_text);
				foreach ($tag_data as $key => $value) {
					$this->data['Message'][$key] = $value; // sets 'message' and maybe 'tag'
				}
			}	
		}
		if (!empty($this->data['Message']['tag'])) {
			$this->data['Message']['tag'] = strtoupper($this->data['Message']['tag']);
		} else {
			$this->data['Message']['tag'] = null;
		}
		// if status isn't hidden, make sure status_prev is tracking status
		if (!empty($this->data['Message']['status']) && $this->data['Message']['status'] != Status::$STATUS_HIDDEN) {
			$this->data['Message']['status_prev'] = $this->data['Message']['status'];
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

	// hide a message (effectively deleting it from clients)
	public function hide($reason_text=null) {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			if (empty($reason_text)) {
				$this->data['Message']['hide_reason'] = null;
			} else {
				$this->data['Message']['hide_reason'] = $reason_text;
			}
			$this->data['Message']['status'] = Status::$STATUS_HIDDEN;
		}
	}

	public function unhide() {
		if (!$this->id && empty($this->data)) {
			return "missing id/data";
		} else {
			$this->data['Message']['hide_reason'] = null;
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
		
	// if a record is unhidden, revert to it's old (pre-hidden) status
	// There was some logic here that's a little wonky, but keeping it in in case
	// status_prev fails: its status possibly depends on whether or not it has an FMS_id
	private function _revert_status() {
		if (! empty($this->data['Message']['status_prev']) && $this->data['Message']['status_prev'] != Status::$STATUS_HIDDEN) {
			$this->data['Message']['status'] = $this->data['Message']['status_prev'];
		} elseif (empty($this->data['Message']['fms_id'])) {
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

	public function parent_exists_if_specified($check) {
		return self::exists($check['parent_id']) && $this->id != $check['parent_id'];
	}

	// tag_conditions array is suitable for using in cake find()s... the catch here is that 
	// the "no tag" tag is special, and matches an empty tag (null or empty string)
	// Maybe in the future we'll add !TAG to negate specific tags, but for now we don't need it
	public static function get_tag_conditions($allowed_tags = null) {
		if (is_null($allowed_tags)) {
			return array();
		} 
		if (is_string($allowed_tags)) {
			$allowed_tags = preg_split("/[\s,]+/", strtoupper($allowed_tags));
		}
		$empty_tag_indices = array_keys($allowed_tags, Configure::read('no_tag_symbol'));
		foreach ($empty_tag_indices as $ix) {
			array_splice($allowed_tags, $ix, 1); // delete that element (may be dups, so iterate)
		}
		$allowed_tags = array('Message.tag' => $allowed_tags);
		if ($empty_tag_indices) {
			$allowed_tags = array(
				'OR' => array(
					array('Message.tag' => null), 
					array('Message.tag' => ''), // really shouldn't be an empty string, but be helpful
					$allowed_tags
				)
			);
		}
		return $allowed_tags;
	}	
	
	// accepts message text, returns array suitable for saving:
	// array(
	//		'message' => message_text possibly with tag stripped
	//		'tag'     => extracted tag
	// )
	// If there are many tags, better to use a lookup rather than iterate through all $tags
	public static function separate_out_tags($message_text) {
		$tags = Configure::read('tags');
		$ret_val = array('message' => $message_text);
		foreach ($tags as $tag => $desc) {
			$pattern = '/^\s*' . $tag . '\s*\b/i';
			if (preg_match($pattern, $message_text)) {
				$ret_val['tag'] = strtoupper($tag);
				if (Configure::read('remove_tags_when_matched')) {
					$ret_val['message'] = preg_replace($pattern, "", $message_text);
				}
				break; // only test for one tag
			}
		}
		return $ret_val;
	}
}
