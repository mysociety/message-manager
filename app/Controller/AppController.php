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

		if (isset($this->data['User']['login'])) {
			// allow email rather than username on login
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

		$this->Auth->authorize = 'Actions';
		$this->Auth->actionPath = 'Controllers/';
        $this->Auth->loginAction = array('controller' => 'users', 'action' => 'login');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'display'); // to the home page

		// make current user available across the whole application
		App::import('Model', 'User');
		User::store($this->Auth->user());
		
		// allow easy flagging of (e.g., dev sites) by changing the background color
		$site_css_class = "site-" . (preg_match("/(message-local|localhost)/i", $_SERVER['SERVER_NAME'])? "local":"default");
		$this->set("site_css_class", $site_css_class); 
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
