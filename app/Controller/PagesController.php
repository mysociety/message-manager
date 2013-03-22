<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppController', 'Controller');

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Pages';

/**
 * Default helper
 *
 * @var array
 */
	public $helpers = array('Js' =>  array('Jquery'), 'Html', 'Session', 'MessageUtils');

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array();

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 */
	public function display() {
		
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set('is_logged_in', $this->Auth->loggedIn());
		$group_id = $this->Auth->user('group_id');
		Controller::loadModel('Group');
		$groups = $this->Group->find('list', array(
				'fields' => 'name',
				'conditions' => array('id' => $group_id )
			));
		$this->set('group_name', array_key_exists($group_id, $groups)? $groups[$group_id ]:"");

		$welcome_msg = Configure::read('cobrand_welcome');
		if (empty($welcome_msg)) {
			$welcome_msg = 'Messages for FixMyStreet and similar systems.';
		}
		$this->set('welcome_msg', $welcome_msg);
		$this->set(compact('page', 'subpage', 'title_for_layout'));
		if ($page == 'activity') {
			Controller::loadModel('ActionType');
			$action_types_found = $this->ActionType->find(
				'all', 
				array('order' => array('ActionType.name ASC'))
			);
			$action_type = array();
			foreach ($action_types_found as $at) {
				$action_type = $at['ActionType'];
				$action_types[$action_type['name']] = $action_type['description'];
			}
			$this->set('action_types', $action_types);
		}
		$user_tags = "";
		if ($this->Auth->loggedIn()) {
			$user_tags = $this->Auth->user('allowed_tags'); 
		}
		$this->set('user_tags', $user_tags);
		$this->render(implode('/', $path));
	}
	
	public function beforeFilter() {
		parent::beforeFilter();
		// after some dithering, best to allow all pages and be done with it!
		// $this->Auth->authError = 'Please log in to access the Message Manager.';
	    $this->Auth->allow('*'); 
	}
}
