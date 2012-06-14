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
	    $this->Auth->allow('initDB'); // uncomment to enable re-build of the aros_acos table
	}
	
	public $helpers = array('Js' =>  array('Jquery'));
	
	public function login() {
	    if ($this->request->is('post')) {
	        if ($this->Auth->login()) {
	            $this->redirect($this->Auth->redirect());
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
		} else {
			$this->set('groups', $this->User->Group->find('list')); // populate the drop-down
		}
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
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'));
			}
		} else {
	 		$this->User->id = $id;
			$this->set('groups', $this->User->Group->find('list')); // populate the drop-down
			$this->request->data = $this->User->read(null, $id);
		}
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
		//------------------------------ 
		// safety! only run this once, if you need to!
		$this->Session->setFlash(__('initDB is disabled!'));
		$this->redirect(array('action' => 'index'));
		return;
		//------------------------------ 

		$group = $this->User->Group;
		// allow admins to do everything
		$group->id = 1;
		$this->Acl->allow($group, 'controllers');

		// allow managers to deal with most things except users and groups
		$group->id = 2;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Actions/index');
		$this->Acl->allow($group, 'controllers/Actions/view');
		$this->Acl->allow($group, 'controllers/Groups/index');
		$this->Acl->allow($group, 'controllers/Groups/view');
		$this->Acl->allow($group, 'controllers/Messages');
		$this->Acl->allow($group, 'controllers/MessageSources/index');
		$this->Acl->allow($group, 'controllers/MessageSources/view');
		$this->Acl->allow($group, 'controllers/MessageSources/client');
		$this->Acl->allow($group, 'controllers/Pages');
		$this->Acl->allow($group, 'controllers/Users/logout');

		// allow api-users to only use the JSON API
		// note nothing here gives access to MSISDNs
		$group->id = 3;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Messages/assign_fms_id');
		$this->Acl->allow($group, 'controllers/Messages/available');
		$this->Acl->allow($group, 'controllers/Messages/lock');
		$this->Acl->allow($group, 'controllers/Messages/lock_unique');
		$this->Acl->allow($group, 'controllers/Messages/reply');
		$this->Acl->allow($group, 'controllers/Messages/unlock');
		$this->Acl->allow($group, 'controllers/Messages/unlock_all');
		$this->Acl->allow($group, 'controllers/MessageSources/client');
		$this->Acl->allow($group, 'controllers/Users/logout');

		// allow message-sources to only use the incoming
		$group->id = 4;
		$this->Acl->deny($group, 'controllers');
		$this->Acl->allow($group, 'controllers/Messages/incoming');
		$this->Acl->allow($group, 'controllers/Users/logout');
		$this->Acl->allow($group, 'controllers/MessageSources/client');

		// we add an exit to avoid an ugly "missing views" error message
		echo "all done";
		exit;
	}
}
