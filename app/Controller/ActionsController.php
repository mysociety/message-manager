<?php
class ActionsController extends AppController {
    public $helpers = array('Html', 'Form');
    var $paginate = array(
        'order' => array(
             'created' => 'desc'
        )
    );

	public function beforeFilter() {
	    parent::beforeFilter();
	    $this->Auth->deny('');
	}
	
    public function index($type = null) {
		$type = strtolower($type);
		Controller::loadModel('ActionType');
		$action_types_found = $this->ActionType->find(
			'all', 
			array('order' => array('ActionType.name ASC'))
		);
		$action_type_id = null;
		$action_types = array();
		$conditions = array();
		$title = __("Activity (all types)");
		$action_type_found = $type == false;
		$subtitle = "Showing all types of activity.";
		foreach ($action_types_found as $at) {
			$action_type = $at['ActionType'];
			$action_types[$action_type['name']] = $action_type['description'];
			if ($type && $type == strtolower($action_type['name'])) {
				$action_type_found = true;
				$conditions = array('Action.type_id' => $action_type['id']);
				$title = __("\"%s\" Activity: %s", $type, strtolower($action_type['description']));
			} 
		}
		if ($type && ! $action_type_found) {
			throw new NotFoundException(__('No such action type (%s)', $type));
		}

		$search_term = $this->request->query('q');
		if ($search_term) {
			array_push($conditions, array(
					'Action.note LIKE' => "%$search_term%"
				)
			);
			$title = __('%s, search results for "%s"', $title, h($search_term));
			$this->set('show_results', True);
		} else {
			$this->set('show_results', False);			
		}
		$this->set('search_term', $search_term);
		$this->paginate = array(
			'conditions' => $conditions
		);
		$this->set('actions', $this->paginate('Action'));
		$this->set('action_types', $action_types);
		
		$this->set('title', $title);
		$this->set('subtitle', $subtitle);
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
			throw new NotFoundException(__('No such action'));
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
