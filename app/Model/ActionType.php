<?php
class ActionType extends AppModel {
	public $name = 'ActionType';
	// public $actsAs = array('Acl' => array('type' => 'requester'));

	public static $ACTION_UNKNOWN   = 0;
	public static $ACTION_LOCK      = 1;
	public static $ACTION_UNLOCK    = 2;
	public static $ACTION_ASSIGN    = 3;
	public static $ACTION_UNASSIGN  = 4;
	public static $ACTION_HIDE      = 5;
	public static $ACTION_UNHIDE    = 6;
	public static $ACTION_NOTE      = 7;
	public static $ACTION_REPLY     = 8;
	public static $ACTION_GATEWAY   = 9;
	public static $ACTION_DETACH    = 10;

	// utility method: don't use it, use the constants above
	public static function getIdByName($type_name) {
		$type_id = $this->ActionType->find('first', array('conditions' => array('ActionType.name' => $type_name)));
		if (empty($type_id)) {
			return 0; // default 0 is "unknown"
		} else {
			return $type_id;
		}
	}


}