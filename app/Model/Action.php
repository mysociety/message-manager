<?php
class Action extends AppModel {
	public $name = 'Action';
	// public $actsAs = array('Acl' => array('type' => 'requester'));
	public $belongsTo = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'message_id',
			'conditions' => '',
			'fields' => 'Message.msisdn'
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

}