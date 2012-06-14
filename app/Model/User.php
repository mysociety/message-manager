<?php
App::uses('AppModel', 'Model');
App::uses('AuthComponent', 'Controller/Component');

/**
 * User Model
 *
 * @property Group $Group
 */
class User extends AppModel {
	public $name = 'User';
	public $actsAs = array('Acl' => array('type' => 'requester'));
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => 'Group.name',
			'order' => array('Group.name' => 'ASC')
		)
	);
	public $hasOne = 'MessageSource'; // some users are associated with a message source

	public function parentNode() {
	    if (!$this->id && empty($this->data)) {
	        return null;
	    }
	    if (isset($this->data['User']['group_id'])) {
	        $groupId = $this->data['User']['group_id'];
	    } else {
	        $groupId = $this->field('group_id');
	    }
	    if (!$groupId) {
	        return null;
	    } else {
	        return array('Group' => array('id' => $groupId));
	    }
	}
	
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	public function beforeSave() {
	    $this->data['User']['password'] = AuthComponent::password($this->data['User']['password']);
	    return true;
	}
	
	// for ACL stuff
	public function bindNode($user) {
	    return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
	}


	/*
	 * Static methods that can be used to retrieve the logged in user
	 * from anywhere
	 *
	 * Copyright (c) 2008 Matt Curry
	 * www.PseudoCoder.com
	 * https://github.com/mcurry/cakephp_static_user
	 * http://www.pseudocoder.com/archives/2008/10/06/accessing-user-sessions-from-models-or-anywhere-in-cakephp-revealed/
	 *
	 * @author      Matt Curry <matt@pseudocoder.com>
	 * @license     MIT
	 *
	 */

	//in AppController::beforeFilter:
	//App::import('Model', 'User');
	//User::store($this->Auth->user());

	/* Usage */
	// User::get('id');
	// User::get('username');
	// User::get('Model.fieldname');
	
	function &getInstance($user=null) {
	  static $instance = array();

	  if ($user) {
	    $instance[0] =& $user;
	  }

	  if (!$instance) {
	   // trigger_error(__("User not set.", true), E_USER_WARNING);
	    return null;
	  }

	  return $instance[0];
	}

	function store($user) {
	  if (empty($user)) {
	    return false;
	  }

	  User::getInstance($user);
	}

	function get($path) {
	  $_user =& User::getInstance();

	  $path = str_replace('.', '/', $path);
	  if (strpos($path, 'User') !== 0) {
	    $path = sprintf('User/%s', $path);
	  }

	  if (strpos($path, '/') !== 0) {
	    $path = sprintf('/%s', $path);
	  }

	  $value = Set::extract($path, $_user);

	  if (!$value) {
	    return false;
	  }

	  return $value[0];
	}


	
	//The Associations below have been created with all possible keys, those that are not needed can be removed


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Action' => array(
			'className' => 'Action',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
