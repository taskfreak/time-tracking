<?php
/**
 *  TaskFreak! Time Tracker
 * 
 * @package taskfreak_tt
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.2
 * @copyright GNU General Public License (GPL) version 3
 */
 
/**
 * Login
 * 
 * List of current tasks
 * @since 0.2
 */
class Login extends AppController {

	public function __construct() {
		parent::__construct(false);
	}
	
	/**
	 * Login form
	 */
	public function mainAction() {
		$this->fc->user->addHelper('html_form');
		$this->page->set('title','TaskFreak! Login');
		$this->page->add('css',array('form.css','freak.css','login.css'));
		$this->page->add('js','form.js');
		$this->setView('login/login');
		$this->view();
	}
	
	/**
	 * Logs user in
	 * @todo redirect to requested page
	 */
	public function mainReaction() {
		$this->fc->user->fields('username,password');
		$this->fc->user->set($this->fc->request);
		if ($this->fc->user->login($this->fc->user->get('username'), $this->fc->user->get('password'))) {
			NaviHelper::redirect(APP_WWW_URI);
		}
		return true; // show action again
	}
	
	/**
	 * Logs user out
	 * @todo show logout summary page
	 */
	public function outAction() {
		$this->fc->user->logout();
		NaviHelper::redirect(APP_WWW_URI);
	}
}