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
	public $actsAs = array('Acl' => array('type' => 'requester'), 'PaginatesOnPostgres');
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
 * note: no passing of "password" here -- it must be done through new_password
 * since the views/controllers are enforcing that.
 *
 * @var array
 */
	public $validate = array(
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Username must not be empty',
			),
			'isunique' => array(
				'rule' => array('isUnique'),
				'message' => 'Username must be unique: someone else already has that username',
			),
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Not a valid email address',
				'allowEmpty' => true,
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'This email address is already associated with another username',
			),
		),
		'new_password' => array(
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => "New password must be at least 6 characters long",
			),
		),
		'confirm_password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => "Password confirmation mustn't be empty",
			),
			'password_match' => array(
				'rule' => array('password_match'),
				'message' => "Password confirmation doesn't match the password",
			),
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
			'group_exists' => array(
				'rule' => array('group_exists'),
				'message' => 'User must belong to a valid group',
			)
			
		),
	);
	
	//---------------------------------------------------
	// custom validation routines
	// including checking password against confirmation
	//---------------------------------------------------
	public function password_match($check) {
		return $check['confirm_password'] == $this->data['User']['new_password'];
	}

	public function group_exists($check) {
		$source = $this->Group->findById($check['group_id']);
		return !empty($source['Group']['id']);
	}
	

    // importantly hashes the new_password, which ultimately fails if new_password doesn't validate
	function beforeSave() {
	    parent::beforeSave();
	    if (isset($this->data['User']['new_password']) && !empty($this->data['User']['new_password'])) {
	        $this->data['User']['password'] = AuthComponent::password($this->data['User']['new_password']);
		}
		$tags = trim($this->data['User']['allowed_tags']);
		if (empty($tags)) {
			$this->data['User']['allowed_tags'] = null; // tidy up tags
		}
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
		),
		'MessageSource' => array(
			'className' => 'Action',
			'foreignKey' => 'user_id'
		)
	);

}
