<?php
class Action extends AppModel {
	public $name = 'Action';
	
	// public $actsAs = array('Acl' => array('type' => 'requester'));
	public $belongsTo = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'message_id',
			'conditions' => '',
			'fields' => 'Message.from_address, Message.to_address'
			// 'order' => array('Group.name' => 'ASC')
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'ActionType' => array(
			'classname' => 'ActionType',
			'foreignKey' => 'type_id',
			'fields' => 'ActionType.name, ActionType.description'
		)
	);

	public $validate = array(
		'note' => array(
			'rule'    => 'prevent_blank_notes',
			'message' => 'Notes must not be empty',
			'on'      => 'create'
		),
		'message_id' => array(
			'rule'    => 'message_exists',
			'message' => 'Message (message_id) could not be found',
			'on'      => 'create'
		),
	);

	public function parentNode() {
		if (!$this->id && empty($this->data)) {
			return null;
		}
		if (isset($this->data['Action']['message_id'])) {
			$messageId = $this->data['Action']['message_id'];
		} else {
			$messageId = $this->field('message_id');
		}
		if (!$messageId) {
			return null;
		} else {
			return array('Message' => array('id' => $messageId));
		}
	}

	public function message_exists($check) {
		$source = $this->Message->findById($check['message_id']);
		return !empty($source['Message']['id']);
	}

	function prevent_blank_notes($check) {
		// Controller::loadModel('ActionType'); // to access static var
		if ($this->Message->action_type==ActionType::$ACTION_NOTE) {
			$note = trim($this->Message->note);
			return(!empty($note));
		} else {
			return true; // notes field can be blank for any actions that are not "notes"
		}
	}
}