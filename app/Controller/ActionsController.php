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
}