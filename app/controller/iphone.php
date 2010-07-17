<?php
/**
 * TaskFreak! Time Tracker
 *
 * @package taskfreak_tt 
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.5
 * @copyright GNU General Public License (GPL) version 3
 */
 
/**
 * iphone
 *
 * iphone version
 * @since 0.5
 */
class Iphone extends AppController {

	public function __construct() {
		parent::__construct(false, true);
		// parent::__construct(false, true);
				
		$this->fc->loadModel('TaskModel');
		
		// $this->page->clean('css');
		// $this->page->clean('js');
		$GLOBALS['config']['path']['css'][] = 'asset/jqtouch/';
		$GLOBALS['config']['path']['css'][] = 'asset/jqtouch/themes/jqt/';
		$GLOBALS['config']['path']['js'][] = 'asset/jqtouch/';

		$this->page->add('css',array('jqtouch.min.css','theme.css','iphone.css'));
		// $this->page->add('js',array('jquery.form.min.js','jquery.colorbox-min.js'));
		$this->page->add('js',array('jqtouch.js','iphone.js'));
		$jsCode = "var APP_LOGOUT='". $this->fc->getUrl('iphone','logout')."';\n";
		$jsCode .= "var APP_RELOAD='". $this->fc->getUrl('iphone','home')."';\n";
		$jsCode .= "var APP_VIEW='". $this->fc->getUrl('iphone','detail',array('id'=>''))."';\n";
		$jsCode .= "var APP_EDIT='". $this->fc->getUrl('iphone','edit',array('id'=>''))."';\n";
		$jsCode .= "var jQT = new $.jQTouch({
	    icon: '".APP_WWW_URI.'skin/'.$GLOBALS['config']['skin']."/img/iphone_icon.png',
	    addGlossToIcon: false,
	    startupScreen: '".APP_WWW_URI.'skin/'.$GLOBALS['config']['skin']."/img/iphone_startup.png',
	    statusBar: 'black',
	    formSelector: '.form',
	    touchSelector: '.button, .back, .cancel, .add, .touch',
	    preloadImages: [
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/back_button.png',
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/back_button_clicked.png',
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/button_clicked.png',
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/grayButton.png',
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/whiteButton.png',
	        '".APP_WWW_URI."skin/".$GLOBALS['config']['skin']."/img/lightButton.png',
	        '".APP_WWW_URI."skin/".$GLOBALS['config']['skin']."/img/darkButton.png',
	        '".APP_WWW_URI."asset/jqtouch/themes/jqt/img/loading.gif'
	        ]
		});";
		$this->page->add('jsCode',$jsCode);
	}
	
	/**
	 * main menu (list tasks)
	 */
	public function mainAction() {
		
		$this->_loadHomeView();
		
		$this->page->set('title','TaskFreak! Time Tracking');
		$this->setView('iphone/main');
		$this->viewIPhone();
	}
	
	/**
	 * main menu (list tasks)
	 */
	public function homeAction() {
		
		$this->_loadHomeView();
		
		$this->incView('iphone/inc_main');
		$this->incList('list-st0',TR::html('pages','todo'),$this->dataTodo);
		$this->incList('list-st1',TR::html('pages','done'),$this->dataDone);
		$this->incList('list-st2',TR::html('pages','valid'),$this->dataValid);
	}
	
	/**
	 * reacts to current task actions
	 */
	public function homeReaction() {
		// error_log('lets '.$this->fc->getReqVar('action').' on '.$this->fc->getReqVar('id'));
		return $this->_taskStatus();
	}
	
	/**
	 * list tasks for a specific tag
	 */
	public function listAction() {
	
		$this->_checkLogin(true);
	
		if ($tag = $this->fc->getReqVar('tag')) {
			$data = $this->_loadTaskList("title LIKE '$tag%'");
			$this->incList('list-'.StringHelper::cleanStrip($tag), $tag, $data);
			return true;
		}
		
		// reaching this point means error has occured
		header('HTTP/1.0 404 Not Found');
	
	}
	
	/**
	 * display task details
	 */
	public function detailAction() {
		$this->_checkLogin(true);
		sleep(1);
		// load current running task to check if it's actually running
		$this->current = TaskSummary::loadCurrent();
		// load task full details and history
		$this->_loadTaskSummary(true);
		$data = $this->data->exportData();
		$data['running'] = ($this->current && ($this->current->getUid() == $this->data->getUid()))?true:false;
		header('Content-type: application/json');
		echo json_encode($data);
	}
	
	/**
	 * reacts to displayed task actions
	 */
	public function detailReaction() {
		return $this->_taskStatus();
	}
	
	/**
	 * edit task (sends back data in JSON format)
	 */
	public function editAction() {
		$this->_checkLogin(true);
		sleep(1);
		$this->_loadTask(false);
		$this->edit->addHelper('json');
		header('Content-type: application/json');
		echo $this->edit->export('value');
	}
	
	/**
	 * save task (form reaction)
	 */
	public function editReaction() {
		$this->_checkLogin(true);
		$this->_loadTask(false);
		$this->edit->ignore('creation_date'); // do not submit or change creation date
		$this->edit->set($this->fc->request);
		if ($this->edit->check($this->fc->user->getUid())) {
			$this->edit->save();
		}
		echo '<script>';
		echo 'app.editClear();';
		echo '</script>';
		/*
		// send back list of tasks for this status
		$status = $this->edit->get('status');
		$data = $this->_loadTaskList("status=$status");
		$arr = array_keys($GLOBALS['lang']['pages']);
		$this->incList('list-st'.$status, TR::html('pages',$arr[$status]), $data);
		*/
	}
	
	/**
	 * display task timer history
	 */
	public function historyAction() {
	}
	
	/**
	 * displays new timer form
	 */
	public function timerAction() {
	}
	
	/**
	 * reacts to timer form
	 */
	public function timerReaction() {
	}
	
	/**
	 * login on iphone
	 */
	public function loginAction() {
		$this->fc->user->addHelper('html_form');
		$this->page->set('title','TaskFreak! '.TR::get('security','login'));
		$this->setView('iphone/login');
		$this->viewIPhone();
	}
	
	/**
	 * Logs user in
	 * @todo redirect to requested page
	 */
	public function loginReaction() {
		$this->fc->user->fields('username,password');
		$this->fc->user->set($this->fc->request);
		if ($this->fc->user->login($this->fc->user->get('username'), $this->fc->user->get('password'))) {
			$this->fc->user->setAutoLogin();
			header('HTTP/1.0 200 OK');
			$this->homeAction();			
		} else {
			header('HTTP/1.0 403 Forbidden');
		}
		return false;
	}
	
	/**
	 * Logs user out
	 * @todo show logout summary page
	 */
	public function logoutAction() {
		$this->fc->user->logout();
		// NaviHelper::redirect($this->fc->getUrl('iphone','main'));
		// echo '<script type="text/javascript">app.logout();</script>';
		return false;
	}
	
	/**
	 * check if user is logged in
	 * sends back a 403 if user is not allowed
	 */
	protected function _checkLogin($exit) {
		if (!$this->fc->user->isLoggedIn()) {
			if ($exit) {
				header('HTTP/1.0 403 Forbidden');
				exit;
			}
			return false;
		}
		return true;
	}
	
	/**
	 * change task status
	 */
	protected function _taskStatus() {
		$this->current = TaskSummary::loadCurrent();
		
		$arrReload = array();
		
		if ($id = $this->fc->getReqVar('id')) {
			if ($this->current && $this->current->getUid() != $id) {
				// we have a running task, but it's not the requested one
				// so first, we need to stop it.
				$cid = $this->current->getUid();
				TimerModel::stop($cid);
				// and we'll have to reload the page details later
				$arrReload[] = $cid;
			} else {
				$this->current = new TaskSummary();
				$this->current->connectDb();
				$this->current->setUid($id);
				$this->current->load();
			}
			
			if ($this->current->getUid()) {
				// -TODO- check rights
				switch($this->fc->getReqVar('action')) {
				case 'pause':
					// try to pause
					if ($this->current && $this->current->getUid() == $id) {
						// ok, task is actually running
						TimerModel::stop($id);
						$this->current->set('stop',APP_SQL_NOW);
						$this->jsCode .= "";
					} else {
						// nope, requested task is not running, show error
						FC::log_debug('error trying to pause non running task');
						header('HTTP/1.0 403 Forbidden');
						exit;
					}
					$this->setView('iphone/inc_timer');
					$this->viewRaw();
					break;
				case 'resume':
				case 'start':
					TimerModel::start($id);
					$this->current = TaskSummary::loadCurrent();
					$this->jsCode = "clockstart();";
					$this->setView('iphone/inc_timer');
					$this->viewRaw();
					break;
				case 'stop':
					if (TimerModel::stop($id)) {
						$this->jsCode = "clockstatus();";
					} else {
						$this->jsCode = "alert('".TR::get('error','action_failed')."');";
					}
					$this->current = false;
					break;
				case 'close':
					if (TimerModel::stop($id)) {
						$this->jsCode = "clockstatus();";
						$this->current->updateStatus(1); // mark as done
						$this->current = false;
					}
					break;
				}
				// need to reload newly modified task details
				$arrReload[] = $id;
			}
			
			return false;
		}
		
		// reaching this point means an error has occured
		header('HTTP/1.0 404 Not Found');
		exit;
	}
	
	/**
	 * load home screen
	 */
	protected function _loadHomeView() {
	
		$this->edit = new TaskModel();
		$this->edit->addHelper('html_form');
	
		if (!$this->_checkLogin(false)) {
			return false;
		}
		$this->current = TaskSummary::loadCurrent();
		
		$this->dataTodo = $this->_loadTaskList('status=0');
		$this->dataDone = $this->_loadTaskList('status=1');
		$this->dataValid = $this->_loadTaskList('status=2');
		
		/*
		$db = DbHelper::factory();
		$db->select("DISTINCT (SUBSTRING_INDEX( title,  ':', 1 )) as tag");
		$db->from('task');
		$db->where("title LIKE  '%:%'");
		$this->dataTags = $db->loadRaw();
		*/
		
		return true;
	}
	
	/**
	 * load task
	 */
	protected function _loadTask($exit) {
	
		if (isset($this->edit) && is_a($this->edit, 'TaskModel')) {
			// task already loaded
			return true;
		}
		
		$this->edit = new TaskModel();
		$this->edit->connectDb();
						
		if ($id = $this->fc->getReqVar('id')) {
			$this->edit->setUid($id);
			if ($this->edit->load()) {
				return true;
			}
		}
		
		// reaching this point means error has occured
		if ($exit) {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		return false;
	}
	
	/**
	 * load task with timer info
	 */
	protected function _loadTaskSummary($exit) {
	
		if (isset($this->data) && is_a($this->data, 'TaskSummary') && ($this->data->getUid())) {
			// task already loaded
			return true;
		}
	
		$this->data = new TaskSummary();
		$this->data->connectDb();
		
		$ok = false;			
		if ($id = $this->fc->getReqVar('id')) {
			$this->data->where('id='.$id);
			$this->data->orderBy('start ASC');
			if ($this->data->loadExpandList()) {
				if ($this->data->next()) {
					$this->data->chkDeadline();
					return true;
				}
			}
		}
		
		// reaching this point means error has occured
		if ($exit) {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		return false;
	}
	
	/**
	 * load task list
	 */
	protected function _loadTaskList($filter) {
		$data = new TaskSummary();
		$data->connectDb();
		$data->where($filter);
		$data->where('archived=0');
		$data->where('member_id='.$this->fc->user->getUid());
		$data->orderBy('status ASC, deadline ASC, priority ASC, title ASC, start ASC');
		$data->loadCompactList();
		return $data;
	}
	
	/**
	 * view list
	 */
	protected function incList($name, $title, &$data) {
		echo '<div id="'.$name.'" class="dyn">';
		echo '<div class="toolbar">';
		echo '<h1>'.$title.'</h1>';
		echo '<a href="#" class="back">Back</a>';
		echo '<a href="#edit" class="button new flip">New</a>';
		echo '</div>';
		if ($data) {
			echo '<ul class="rounded">';
			while ($data->next()) {
				$data->chkDeadline();
				// $link = $this->fc->getUrl('iphone','detail',array('id'=>$data->getUid()));
				echo '<li class="arrow t-'.$data->getUid().'">'
					.'<a href="#details" class="details" rel="'.$data->getUid().'">'.$data->html('title').'</a>'
					.'<a href="#details" class="details" rel="'.$data->getUid().'"><em>'
					.$data->htmlStatus().'</em> '.$data->get('priority').') '.$data->htmlDeadline().'</a>'
					.'</li>';
			}
			echo '</ul>';
		} else {
			echo '<ul class="edgtoedge"><li>No task</li></ul>';
		}
		// echo '<script type="text/javascript">$("#'.$name.' .edit").tap(function (e) {return app.edit(this);});';
		$this->incView('iphone/inc_foot');
		// echo '<script>app.bindList("'.$name.'");</script>';
		echo '</div>';
	}
}