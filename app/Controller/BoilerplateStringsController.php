<?php
class BoilerplateStringsController extends AppController {
	
	//  RequestHandlerComponent lets json (and XML) work
	public $components = array(
		 'RequestHandler'
	);
	
    public $helpers = array('Js' =>  array('Jquery'), 'Html', 'Form');

    public $paginate = array(
        'order' => array('BoilerplateString.sort_index' => 'asc')
    );

	public function beforeFilter() {
		parent::beforeFilter();
		// currently allow JSON API access without any auth, since this isn't sensitive data (yet?)
		if ($this->RequestHandler->accepts('json')) {
		 	$this->Auth->allow('index');
			$this->response->header('Access-Control-Allow-Origin', Configure::read('cors_allowed'));
		}
	}
	
	/* returns list of boilerplate strings, by type (really for AJAX-populated UI) */
    public function index($boilerplate_type=null) {
		$msg = null;
		$conditions = array();
		if (! empty($boilerplate_type)) {
			$conditions = array('BoilerplateString.type' => $boilerplate_type);
		}
		if ($this->RequestHandler->accepts('json')) {
			$entries = $this->BoilerplateString->find('list', array(
				'fields' => array('BoilerplateString.id', 'BoilerplateString.text_value', 'BoilerplateString.lang'),
				'conditions' => $conditions
			));
			$langs = array_keys($entries);
			sort($langs);
			$entries['langs'] = $langs;
			$this->response->body( json_encode(self::mm_json_response(true, $entries, null)) );
			return $this->response;
		}
		$this->paginate = array(
			'recursive' => 0,
			'conditions' => $conditions,
		);
		$this->set('title', "Boilerplate strings for " . ($boilerplate_type? $boilerplate_type : "all types") );
		$this->set('boilerplate_strings', $this->paginate('BoilerplateString'));
    }

	public function edit($id = null) {
		$this->BoilerplateString->id = $id;
		if (!$this->BoilerplateString->exists()) {
			throw new NotFoundException(__('Invalid boilerplate string'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->BoilerplateString->save($this->request->data)) {
				$this->Session->setFlash(__('The string has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The string could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->BoilerplateString->read(null, $id);
		}
	}

	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->BoilerplateString->id = $id;
		if (!$this->BoilerplateString->exists()) {
			throw new NotFoundException(__('Invalid boilerplate string ID'));
		}
		if ($this->BoilerplateString->delete()) {
			$this->Session->setFlash(__('Boilerplate string deleted'));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Boilerplate string was not deleted'));
		$this->redirect(array('action' => 'index'));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->BoilerplateString->create();
			if ($this->BoilerplateString->save($this->request->data)) {
				$this->Session->setFlash(__('The new string has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The string could not be saved. Please, try again.'));
			}
		}
	}

}
