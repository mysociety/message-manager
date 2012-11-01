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
	    $this->Auth->allow('*'); // allow access to the dummy client for testing Basic Auth
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

}
