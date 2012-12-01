<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
   public $components = array(
        'Acl',
        'Auth' => array(
            'authorize' => array(
                'Actions' => array('actionPath' => 'Controllers')
            )
        ),
        'Session'
    );
    public $helpers = array('Html', 'Form', 'Session', 'Js' => array('Jquery'));
	
    public function beforeFilter() {
        //Configure AuthComponent
		
		// username is the default...
		// but see UsersController's beforeFilter: email address is also acceptable on login
		$this->Auth->fields = array('username' => 'username'); 
		
		$this->Auth->authorize = 'Actions';
		$this->Auth->actionPath = 'Controllers/';
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'display'); // to the home page

		// arcane CORS magic, which is summoned by some FMS AJAX spellcasting
		if ($this->request->is('options') ) {
			$cors_allowed = Configure::read('cors_allowed');
			if ($cors_allowed) {
				$this->response->header('Access-Control-Allow-Origin', $cors_allowed);
				$this->response->header('Access-Control-Allow-Credentials', 'true');
				$this->response->header('Access-Control-Allow-Headers', 'Origin, Accept, Authorization, Content-Type,  Depth,  User-Agent,	X-File-Size,  X-Requested-With,	 If-Modified-Since,	 X-File-Name,  Cache-Control');
				$this->response->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
				$this->response->send();
				// otherwise it sends things that upset the CORS pre-flight request
				exit();
			}
		}

		// make current user available across the whole application
		App::import('Model', 'User');
		User::store($this->Auth->user());
		
		// allow easy flagging of (e.g., dev sites) by changing the background color
		$cobrand_name = Configure::read('cobrand_name');
		if (empty($cobrand_name)) {
			$cobrand_name = 'Message Manager';
		}
		$cobrand_moniker = Configure::read('cobrand_moniker');
		
		if (empty($cobrand_moniker)) {
			$cobrand_moniker = 'default';
		}
		$site_css_class = "cobrand-" . strtolower($cobrand_moniker); // one day should use Cake's Themes for this
		
		$this->set("cobrand_moniker", $cobrand_moniker); 
		$this->set("cobrand_name",    $cobrand_name); 
		$this->set("site_css_class",  $site_css_class); 
	}
	
	public function mm_json_response($success=true, $data, $err_msg = "") {
		$retval = array(
			'success' => $success,
			'data'=> $data,
			'username' => $this->Auth->user('id')
		);
		if (! $success) {
			$retval['error'] = $err_msg;
		}
		return $retval;
	}
}
