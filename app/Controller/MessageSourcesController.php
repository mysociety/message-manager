<?php
class MessageSourcesController extends AppController {
    public $helpers = array('Js' =>  array('Jquery'), 'Html', 'Form');

	public function beforeFilter() {
	    parent::beforeFilter();
		
	    $this->Auth->allow('client'); // allow access to the dummy client for testing Basic Auth
		Controller::loadModel('Group'); // to access static methods on it
	}
	
    public function index() {
		$this->set('message_sources', $this->paginate());	
    }

    public function view($id = null) {
        $this->MessageSource->id = $id;
        $this->set('message_source', $this->MessageSource->read());
    }

	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->MessageSource->id = $id;
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Invalid message source'));
		}
		if ($this->MessageSource->delete()) {
			$this->Session->setFlash(__('Message source deleted'));
			$this->redirect(array('message_source' => 'index'));
		}
		$this->Session->setFlash(__('Message source was not deleted'));
		$this->redirect(array('message_source' => 'index'));
	}
	
	public function edit($id = null) {
		$this->MessageSource->id = $id;
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Invalid message source'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->MessageSource->save($this->request->data)) {
				$this->Session->setFlash(__('The message source has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message source could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->MessageSource->read(null, $id);
		}
		$this->set('users', $this->MessageSource->User->find('list', array(
			'fields' => array('username'),
				'conditions' => array('group_id' => Group::$SOURCE_USER_GROUP_ID),
				'order' => array('username' => 'ASC')
			 )
		)); // populate the drop-down
		$source_group = $this->Group->findById(Group::$SOURCE_USER_GROUP_ID);
		$this->set('source_group_name', empty($source_group)? "message-sources" : $source_group['Group']['name']); 
	}
	
	
	public function add() {
		if ($this->request->is('post')) {
			$this->MessageSource->create();
			if ($this->MessageSource->save($this->request->data)) {
				$this->Session->setFlash(__('The message source has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The message source could not be saved. Please, try again.'));
			}
		}
	}

	public function client() {
		if (Configure::read('enable_dummy_client')==1) {
			$this->set('username', $this->Auth->user('username'));
			$group_id = $this->Auth->user('group_id');
			Controller::loadModel('Group');
			$groups = $this->Group->find('list', array(
					'fields' => 'name',
					'conditions' => array('id' => $group_id )
				));
			$this->set('group_name', array_key_exists($group_id, $groups)? $groups[$group_id ]:"");
			$this->set('allowed_tags', $this->Auth->user('allowed_tags'));
			$this->set('remove_tags_when_matched', Configure::read('remove_tags_when_matched'));
			$this->set('messageSources', $this->MessageSource->find('list'));
		} else {
			$this->Session->setFlash(__('The dummy client has been disabled.'));
			$this->redirect("/");
		}
	}
}
