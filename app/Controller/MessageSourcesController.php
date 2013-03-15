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
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Message source with id="%s" not found', $id));
		}
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
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Message source was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
	
	public function edit($id = null) {
		$this->MessageSource->id = $id;
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Message source with id="%s" not found', $id));
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
		$this->set('users', $this->MessageSource->User->find('list', array(
			'fields' => array('username'),
				'conditions' => array('group_id' => Group::$SOURCE_USER_GROUP_ID),
				'order' => array('username' => 'ASC')
			 )
		)); // populate the drop-down
		$source_group = $this->Group->findById(Group::$SOURCE_USER_GROUP_ID);
		$this->set('source_group_name', empty($source_group)? "message-sources" : $source_group['Group']['name']); 
	}

	public function client() {
		if (Configure::read('enable_dummy_client')==1) {
			$this->helpers[] = 'MessageUtils';
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
	
	public function gateway_test($id = null) {
        $this->MessageSource->id = $id;
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Message source with id="%s" not found', $id));
		}
		$source = $this->MessageSource->read();
		$url = $this->MessageSource->data['MessageSource']['url'];
		$this->_check_gateway_capability($url);
		$this->set('message_source', $source);
		$connection_test_result = 'No test was run.';
		$netcast_id = $this->MessageSource->data['MessageSource']['remote_id'];
		$error_msg = "";
		if (empty($url)) {
			$connection_test_result = 'No test was run: you need to specify a URL';
		} elseif (! preg_match('/^https?:\/\//', $url)) {
			$connection_test_result = 'No test was run: URL must start with protocol (http or https)';
		} else {
			try {
				$netcast = new SoapClient($url);
				$connection_test_result = $netcast->__soapCall("GETCONNECT", array($netcast_id)); 
				$connection_test_result = MessageSource::decode_netcast_retval($connection_test_result);
			}
			catch (Exception $e) {
				$error_msg = $e->getMessage();
			}
		}
		$this->set('error_msg', $error_msg);
		$this->set('message_source', $source);
		$this->set('connection_test_result', $connection_test_result);
	}

	public function gateway_logs($id = null) {
 		$this->MessageSource->id = $id;
		if (!$this->MessageSource->exists()) {
			throw new NotFoundException(__('Message source with id="%s" not found', $id));
		}
		$source = $this->MessageSource->read();
		$url = $this->MessageSource->data['MessageSource']['url'];
		$this->_check_gateway_capability($url);
		$gateway_logs = '';
		$subtitle = '';
		$date = 'today';
		$error_msg = "";
		if ($this->request->is('post')) {
			if (isset($this->request->data['date'])) {
				$date = $this->request->data['date'];
			}
			// Try to get the the netcast logs
			$netcast_id = $this->MessageSource->data['MessageSource']['remote_id'];
			if (empty($url)) {
				$gateway_logs = 'No logs retreived: you need to specify a URL';
			} elseif (! preg_match('/^https?:\/\//', $url)) {
				$gateway_logs = 'No logs retreived: URL must start with protocol (http or https)';
			} else {
				$date_param = strtotime($date);
				if (! $date_param) {
					$this->Session->setFlash(__("Can't parse date: %s", $date));
					$this->redirect(array('action' => 'gateway_logs', $id));
				}
				$date_param =  date('Ymd', $date_param);
				$subtitle = __("Transaction log for date %s from message source \"%s\"", 
					$date_param, $this->MessageSource->data['MessageSource']['name']);
				try {
					$netcast = new SoapClient($url);
					$gateway_logs = $netcast->__soapCall("GETLOGS", array($date_param, $netcast_id)); 
					if (preg_match("/^RET/", $gateway_logs)) { // netcast return values specifically look like "RET..."
						$gateway_logs = MessageSource::decode_netcast_retval($gateway_logs);
					}
				}
				catch (Exception $e) {
					$error_msg = $e->getMessage();
				}
			}
		}
		$this->set('error_msg', $error_msg);
		$this->set('message_source', $source);
		$this->set('subtitle', $subtitle);
		$this->set('date', $date); // user input (actual param sent is in the subtitle)
		$this->set('gateway_logs', $gateway_logs);
	}
	
	private function _check_gateway_capability($url) {
		$is_ok = true;
		if (! preg_match('/netcast.com/i', $url)) {
			$this->Session->setFlash(__('Gateway test only available for gateways on the Netcast domain'));
			$is_ok = false;
			$this->redirect(array('action' => 'index'));
		}
		if (! include_once("nusoap/nusoap.php")) {
			$this->Session->setFlash(__('Message logs require the nusoap library, which is not installed'));
			$is_ok = false;
		}
		if (! $is_ok) {
			$this->redirect(array('action' => 'index'));
		}
	}
	
}
