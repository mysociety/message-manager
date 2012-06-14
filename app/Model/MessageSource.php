<?php
class MessageSource extends AppModel {
	public $name = 'MessageSource';
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => array('User.group_id' => '4'), // FIXME 'message-sources' hardcoded!
			'fields'		=> array('User.username'),
			'order' => array('User.username' => 'ASC')
		)
	);

}
