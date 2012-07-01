<?php
class ActionsController extends AppController {
    public $helpers = array('Html', 'Form');

	public function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->deny('');
	}
	
    public function index() {
		$this->set('actions', $this->paginate());
    }

    public function view($id = null) {
        $this->Action->id = $id;
        $this->set('action', $this->Action->read());
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
		$this->Action->id = $id;
		if (!$this->Action->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->Action->delete()) {
			$this->Session->setFlash(__('Action deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Action was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	// add method currently only for adding notes to a message
	// note: validation checks that note is not blank and is for valid message
	public function add() {
		$message_id = $this->request->data['Action']['message_id'];
		if (! $this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->request->data['Action']['user_id'] = $this->Auth->user('id');
		$this->Action->create();
		if ($this->Action->save($this->request->data)) {
			$this->Session->setFlash(__('Note has been added.'));
			$this->redirect(array('controller' => 'Messages', 'action' => 'view', $message_id));
		} else {
			$this->Session->setFlash(__('The note could not be saved.'));
			$this->redirect(array('controller' => 'Messages', 'action' => 'index'));
		}
	}
	
}