<?php
/**
 * TaskFreak! Time Tracker
 *
 * @package taskfreak_tt 
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.4
 * @copyright GNU General Public License (GPL) version 3
 */
 
/**
 * Admin
 * 
 * Administration pages
 * @since 0.2
 */
class Admin extends AppController {

	public function __construct() {
		parent::__construct(true);
		
		$this->fc->setSessionDefault('userlimit',$GLOBALS['config']['task']['pagination_default']);
		
		if ($this->fc->getReqVar('ajax')) {
			$this->page->clean('css');
			$this->page->clean('js');
		} else {
			$this->page->add('css',array('form.css','freak.css','list.css','tracker.css','colorbox.css'));
			$this->_addJsSettings();
			$this->page->add('js','freak.js');
		}
	}
	
	public function mainAction() {
	
		// check access rights
		if (!$this->fc->user->checkAcl('admin_user')) {
			$this->fc->redirect(APP_WWW_URI,'access_denied');
		}
		
		$this->filter = $this->fc->sessionVariable('userfilter');
		$this->limit = $this->fc->sessionVariable('userlimit');
		$this->search = $this->fc->sessionVariable('search');
		
		$title = TR::get('ui','all_users');
		$filter = '';
		
		switch ($this->filter) {
			case '1':
				$title = TR::get('ui','task_managers');
				$filter = "actags LIKE '%task_see_all%'";
				$this->limit = 0;
				break;
			case '2':
				$title = TR::get('ui','user_admins');
				$filter = "actags LIKE '%admin_user%'";
				$this->limit = 0;
				break;
			default:
				break;
		}
				
	
		$this->data = new MemberModel();
		$this->data->enableAuthentication();
		$this->data->connectDb();
		$this->data->orderBy('nickname ASC');
		if ($fs = DbQueryHelper::parseSearch($this->search)) {
			$this->data->where("nickname LIKE '$fs'");
		}
		if ($filter) {
			$this->data->having($filter);
		}
		if ($this->limit) {
			$this->data->limit(0, $this->limit);
		}
		$this->data->loadList();
		
		$this->page->set('title',$title.' | TaskFreak! Time Tracker Administration');
		
		$this->setView('admin/user-list');
		$this->view();
	}
	
	public function editAction() {
	
		$this->_loadUser();
		$this->data->addHelper('html_form');
				
		$this->setView('admin/user-edit');
		$this->view();
	}
	
	public function editReaction() {
	
		$this->_loadUser();
		
		$this->data->fields('nickname,username,time_zone,email');
		$this->data->set($this->fc->request);
		
		if ($this->fc->chkReqVar('pass1') && $this->fc->chkReqVar('pass2')) {
			if ($this->data->setPassword($this->fc->getReqVar('pass1'), $this->fc->getReqVar('pass2'))) {
				// save it all
				error_log('setting new password : '.$this->fc->getReqVar('pass1'));
				$this->data->ignore();
			} else {
				// do not save password
				error_log('can not save password');
				$this->data->ignore('password,salt');
			}
		}
		
		if ($this->data->check()) {
			$this->data->set('enabled',1); // always enable for now
			$myself = ($this->data->getUid() == $this->fc->user->getUid());
			if ($this->data->save(!$myself)) {
				if ($myself) {
					// editing own profile need to reset session
					error_log('updating session');
					$this->data->updateSessionVariables();
				}
				$this->fc->autoRedirect('saved');
			}
		}
		return true;
	}
	
	public function deleteAction() {
		
		$this->_loadUser();
		
		if ($this->canDeleteThisUser) {
			if ($this->data->delete()) {
				$db = DbConnector::getConnection();
				// delete this user's rights
				$db->query('DELETE FROM '.$this->data->dbTable('acl_user').' WHERE user_id='.$this->data->getUid());
				// delete this user's tasks
				$db->query('DELETE FROM '.$this->data->dbTable('task').' WHERE member_id='.$this->data->getUid());
				$this->fc->redirect($this->fc->getUrl('admin'),'deleted');
			}
		}
		
		$this->fc->redirect($this->fc->getUrl('admin'),'deleted');
		
	}
	
	public function switchAction() {
	
		if (!$this->fc->user->checkAcl('task_see_all')) {
			$this->fc->redirect(APP_WWW_URI,'[error]access_denied');
		}
	
		if ($id = $this->fc->getReqVar('id')) {
			// switch has been requested
			$obj = new MemberModel();
			$obj->connectDb();
			$obj->setUid($id);
			if ($obj->load()) {
				$this->fc->setSessionVariable('switch_id', $id);
				$this->fc->setSessionVariable('switch_name', $obj->get('nickname'));
			}
			$this->fc->redirect(APP_WWW_URI,'[ui]switched');
		}
	
		$this->switch_id = $this->fc->getSessionVariable('switch_id');
		
		$this->data = new MemberModel();
		$this->data->connectDb();
		$this->data->orderBy('nickname ASC');
		$this->data->loadList();
		
		$this->setView('admin/switch');
		$this->view();
	}
	
	protected function _loadUser() {
		if (isset($this->data)) {
			// already loaded
			return false;
		}
		
		$this->canDeleteThisUser = false;
	
		$this->data = new MemberModel();
		$this->data->enableAuthentication();
		$this->data->connectDb();
	
		// check access rights
		if ($this->fc->user->checkAcl('admin_user')) {
			if ($id = $this->fc->getReqVar('id')) {
				$this->data->setUid($id);
				$this->data->load();
				if ($id != $this->fc->user->getUid()) {
					$this->canDeleteThisUser = true;
				}
			} else {
				// create a ne user : set defaults
				$this->data->set('time_zone', $GLOBALS['config']['datetime']['timezone_server']->getName());
			}
		} else {
			// not an admin : can only load itself
			$this->data = $this->fc->user;
		}
		
		return true;
	}
	
	protected function _addJsSettings() {
		$js = "var RELOAD_URI='".APP_WWW_URI."task/main/ajax/1'; var URLMODREWRITE=true; ";
		
		if (!APP_URL_REWRITE) {
			$js = "var RELOAD_URI='".APP_WWW_URI."?c=task&amp;a=main&amp;ajax=1'; var URLMODREWRITE=false; ";
		}
		
		// translations
		$js .= "var LANGRUNNING='".TR::html('task','running')."'; ";
		$js .= "var LANGCONFIRM='".TR::html('data','delete_confirm')."'; ";
		
		$this->page->add('jsCode', $js);
	}
	
}
