<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public function beforeFilter() {
		parent::beforeFilter();
		Controller::loadModel('Group'); // to access static methods on it
		$this->Auth->allow('login');
		if (isset($this->data['User']['login'])) {
			// allow email as an alternative to username on login
		    if (Validation::email($this->data['User']['login'])) {
				// find the user with this email, and change the username
		        // Note, also considered changing Auth's behaviour with:
		  		//    $this->Auth->fields = array('username' => 'email');
	 			// but had more success with the explicit lookup below
				$users = $this->User->findAllByEmail($this->data['User']['login']);
				if (count($users)==1) { 
					// critically important that it's an unambiguous email match because we
					// are not yet policing unique email adresseses: for now, fail silently
					$this->request->data['User']['username'] = $users[0]['User']['username'];
				}
			} else {
				$this->request->data['User']['username'] = $this->data['User']['login'];
			}
		}

		$this->Auth->allow('initDB'); // uncomment to enable re-build of the aros_acos table
	}
	
	public $helpers = array('Js' =>  array('Jquery'));
	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				// 'source' user has no useful access to anything (!) except maybe the dummy client
				if ($this->Auth->user('group_id')==Group::$SOURCE_USER_GROUP_ID) { // hard-coded!
					if (Configure::read('enable_dummy_client')==1) {
						$this->redirect(array('controller' => 'MessageSources', 'action' => 'client'));
					} else { // home page?
						$this->redirect(array('controller' => 'Pages', 'action' => 'display'));
					}
				} else { // redirecting to login seems confusing (e.g., after login failure)
					if ($this->Auth->redirect() == '/Users/login') {
						$this->redirect(array('controller' => 'Pages', 'action' => 'display'));
					} else {
						$this->redirect($this->Auth->redirect());
					}
				}
			} else {
				$this->Session->setFlash('Your username or password was incorrect.');
			}
		}
	}

	public function logout() {
	    $this->redirect($this->Auth->logout());
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->recursive = 2;
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} 
		$this->set('groups', $this->User->Group->find('list')); // populate the drop-down
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		//$this->User->read(null, $id);
		if ($this->request->is('post') || $this->request->is('put')) {

			// if there's no attempt to change the password, remove the variables to bypass validation
			if (empty($this->request->data['User']['new_password']) && 
					empty($this->request->data['User']['confirm_password'])) {
				unset($this->request->data['User']['new_password']);
				unset($this->request->data['User']['confirm_password']);
				$this->User->read(null, $id);
			}
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->User->read(null, $id);
		}
		$this->set('groups', $this->User->Group->find('list')); // populate the drop-down
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('User was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function initDB() {
		//-------------------------------------------------------------------------------------
		// safety! only run this once, if you need to!
		// see comments at the top of db/initial_auth.sql to understand why you might need to.
		$ENABLE_INIT_DB = false; // <-- should be false unless you're sure you need to run it
		//--------------------------------------------------------------------------------------
		
		if (! $ENABLE_INIT_DB) { 
			$this->Session->setFlash(__('initDB is disabled!'));
			$this->redirect(array('controller' => 'Pages', 'action' => 'display'));
			return;
		}

		$group = $this->User->Group;
		// allow admins to do everything
		$group->id = Group::$ADMIN_GROUP_ID;
		$this->Acl->allow($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Users/edit'); // see if this fixes the user-edit bug

		// allow managers to deal with most things except users and groups
		$group->id = Group::$MANAGER_GROUP_ID;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Actions/add');  // for adding notes to messages
		$this->Acl->allow($group, 'controllers/Actions/index');
		$this->Acl->allow($group, 'controllers/Actions/view');
		$this->Acl->allow($group, 'controllers/BoilerplateStrings');
		$this->Acl->allow($group, 'controllers/Groups/index');
		$this->Acl->allow($group, 'controllers/Groups/view');
		$this->Acl->allow($group, 'controllers/Messages');
		$this->Acl->allow($group, 'controllers/MessageSources/index');
		$this->Acl->allow($group, 'controllers/MessageSources/edit'); // NB edit is restricted to certain fields within the code
		$this->Acl->allow($group, 'controllers/MessageSources/view');
		$this->Acl->allow($group, 'controllers/MessageSources/client');
		$this->Acl->allow($group, 'controllers/Pages');
		$this->Acl->allow($group, 'controllers/Users/logout');

		// allow api-users to only use the JSON API
		// note nothing here gives access to from_address, etc.
		$group->id = Group::$API_USER_GROUP_ID;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/BoilerplateStrings/index');
		$this->Acl->allow($group, 'controllers/Messages/assign_fms_id');
		$this->Acl->allow($group, 'controllers/Messages/available');
		$this->Acl->allow($group, 'controllers/Messages/lock');
		$this->Acl->allow($group, 'controllers/Messages/lock_unique');
		$this->Acl->allow($group, 'controllers/Messages/reply');
		$this->Acl->allow($group, 'controllers/Messages/unlock');
		$this->Acl->allow($group, 'controllers/Messages/unlock_all');
		$this->Acl->allow($group, 'controllers/MessageSources/client');
		$this->Acl->allow($group, 'controllers/Pages');
		$this->Acl->allow($group, 'controllers/Users/logout');

		// allow message-sources to only use the incoming
		$group->id = Group::$SOURCE_USER_GROUP_ID;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Messages/incoming');
		$this->Acl->allow($group, 'controllers/Users/logout');
		$this->Acl->allow($group, 'controllers/MessageSources/client');

		// we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	}
}
