<?php
/**
 * TaskFreak! Time Tracker
 *
 * @package taskfreak_tt 
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.2
 * @copyright GNU General Public License (GPL) version 3
 */
 
/**
 * Task
 *
 * List of current tasks
 * @since 0.1
 */
class Task extends AppController {

	public function __construct() {
		parent::__construct(true);
		
		$this->fc->setSessionDefault('order','deadline');
		$this->fc->setSessionDefault('limit',$GLOBALS['config']['task']['pagination_default']);
		if (APP_SETUP_USER_MODEL) {
			$this->fc->setSessionDefault('switch_id',$this->fc->user->getUid());
			$this->fc->setSessionDefault('switch_name',$this->fc->user->get('nickname'));
			$this->switch_id = $this->fc->getSessionVariable('switch_id');
			$this->user_id = $this->fc->user->getUid();
		} else {
			$this->switch_id = $this->user_id = 0;
		}
		
		$this->fc->loadModel('TaskModel');
		$this->current = TaskSummary::loadCurrent();
		if ($this->fc->getReqVar('ajax')) {
			$this->page->clean('css');
			$this->page->clean('js');
		} else {
			$this->page->add('css',array('form.css','freak.css','list.css','tracker.css','colorbox.css'));
			// $this->page->add('js',array('jquery.form.min.js','jquery.colorbox-min.js'));
			$this->page->add('js','freak.js');
		}
	}
	
	public function mainAction() {
		$this->filter = $this->fc->sessionVariable('filter');
		$title = 'Todo';
		$filter = 'status=0 AND archived=0';
		$this->actions = array();
		switch ($this->filter) {
			case '1':
				$title = 'Completed';
				$filter = 'status=1 AND archived=0';
				$this->actions['open'] = 'Re-open';
				$this->actions['valid'] = 'Validate';
				$this->actions['archive'] = 'Archive';
				break;
			case '2':
				$title = 'Valid'; // -TODO- check english term
				$filter = 'status=2 AND archived=0';
				$this->actions['open'] = 'Re-open';
				$this->actions['archive'] = 'Archive';
				break;
			case '3':
				$title = 'Archives';
				$filter = 'archived=1';
				$this->actions['unarchive'] = 'Unarchive';
				break;
			default:
				$this->actions['report'] = 'Report 1 day';
				$this->actions['close'] = 'Mark as done';
				$this->actions['valid'] = 'Validate';
				$this->actions['archive'] = 'Archive';
				break;
		}
		
		$this->_taskList($filter);
		
		$this->page->set('title',$title.' | TaskFreak! Time Tracker');
		
		if ($this->fc->getReqVar('ajax')) {
			$this->setView('include/list-'.($this->expand?'expand':'compact'));
		} else {
			$this->setView('main');
		}
		$this->view();
		
	}
	
	public function mainReaction() {
		// drop if no checkbox have been checked
		if (!isset($_POST['chk'])) {
			// -TODO- show error
			$this->fc->redirect(APP_WWW_URI.'task/main/','please check at least one task');
		}
		// check action request
		$action = $this->fc->chkReqVar('report,open,close,valid,archive,unarchive');
		// do it then
		TaskModel::updateManyAtOnce($action, $_POST['chk']);
		// reload entire page
		$this->fc->redirect(APP_WWW_URI.'task/main/');
	}
	
	/**
	 * called to start a task (from the task list)
	 * will only update the timer panel and change the task in the list
	 * (method called by ajax request only)
	 */
	public function timerAction() {
		if ($id = $this->fc->getReqVar('id')) {
			$obj = new TaskSummary();
			$obj->connectDb();
			$obj->setUid($id);
			$obj->load();
			if ($obj->getUid()) {
				// -TODO- check rights
				// first stop any running task (TODO for current user only)
				if ($this->current) {
					$cid = $this->current->getUid();
					TimerModel::stop($cid);
					$this->jsCode = "clockreport('$cid');";
				}
				// now start selected task
				TimerModel::start($id);
				$this->current = TaskSummary::loadCurrent();
				$this->jsCode .= "clockstart('$id')";
			}
			$this->setView('include/timer');
			$this->view();
			return false;
		}
	}
	
	/**
	 * change a task status (start, stop, close) or save a new one
	 * will only update the timer panel and reload the task list
	 * (method called by ajax request only)
	 */
	public function timerReaction() {
		$this->jsCode = '';
		if ($id = $this->fc->getReqVar('id')) {
			// start / stop timer
			$obj = new TaskSummary();
			$obj->connectDb();
			$obj->setUid($id);
			$obj->load();
			
			// what action then ?
			$action = $this->fc->chkReqVar('pause,resume,start,stop,close');
			
			FC::log_debug('loaded task ID='.$obj->getUid());
			switch($action) {
			case 'pause':
				// try to pause
				if ($this->current && $this->current->getUid() == $id) {
					// ok, task is actually running
					TimerModel::stop($id);
					$this->current->set('stop',APP_SQL_NOW);
					$this->jsCode .= "clockreport('$cid');clockstatus('paused');";
				} else {
					// nope, requested task is not running, show error
					$this->jsCode = "alert('error trying to pause')";
					FC::log_debug('error trying to pause non running task');
				}
				break;
			case 'resume':
			case 'start':
				TimerModel::start($id);
				$this->current = TaskSummary::loadCurrent();
				$this->jsCode = "clockstart();";
				break;
			case 'stop':
				if (TimerModel::stop($id)) {
					$this->jsCode = "clockstatus();";
				} else {
					$this->jsCode = "alert('task already stopped');";
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
		} else if ($title = $this->fc->getReqVar('title')) {
			// creating a new task ?
			$obj = TaskModel::parse($title, $def, $dte);
			$obj->connectDb();
			if ($this->fc->getReqVar('start')) {
				$obj->set('deadline', APP_SQL_TODAY);
			}
			if ($obj->check($this->switch_id)) {
				$obj->insert();
			}
			if ($this->fc->chkReqVar('start')) {
				TimerModel::start($obj->getUid());
				$this->current = TaskSummary::loadCurrent();
				$this->jsCode = "clockstart();";
			} else {
				$this->current = false;
				$this->jsCode = "clockstatus();";
			}
		}
		$this->jsCode .= 'reloadList();';
		$this->setView('include/timer');
		$this->view();
		return false;
	}
		
	protected function _taskList($filter) {
	
		$this->expand = $this->fc->sessionVariable('expand');
		$this->search = $this->fc->sessionVariable('search');
		$this->order = $this->fc->sessionVariable('order');
		$this->limit = $this->fc->sessionVariable('limit');
		
		$this->data = new TaskSummary();
		$this->data->connectDb();
		$this->data->where($filter);
		if ($this->search) {
			$this->data->where("title LIKE '%".$this->search."%'");
		}
		if ($this->switch_id) {
			// user login enabled, filter by users
			$this->data->where('member_id='.$this->switch_id);
		}
		switch ($this->order) {
		case 'priority':
			$this->data->orderBy('priority ASC, deadline ASC, title ASC, start ASC');
			break;
		case 'start':
			$this->data->orderBy('start ASC, deadline ASC, priority ASC, title ASC');
			break;
		case 'stop':
			$this->data->orderBy('stop ASC, start ASC, deadline ASC, priority ASC, title ASC');
			break;
		case 'spent':
			$this->data->orderBy('spent ASC, start ASC');
		default:
			$this->data->orderBy('deadline ASC, priority ASC, title ASC, start ASC');
			break;	
		}
		if ($this->expand) {
			$this->limit = 0;
			$this->data->loadExpandList();
		} else {
			$this->data->limit(0,$this->limit);
			$this->data->loadCompactList();
		}
	}
	
	public function viewAction() {
		$this->data = new TaskSummary();
		$this->data->connectDb();
		
		$ok = false;			
		if ($id = $this->fc->getReqVar('id')) {
			$this->data->where('id='.$id);
			$this->data->orderBy('start ASC');
			if ($this->data->loadExpandList()) {
				$ok = $this->data->next();
			}
		}
		if (!$ok) {
			echo 'Todo not found';
			exit;
		}
		$this->data->chkDeadline();
		
		$this->timer = new TimerModel();
		$this->timer->addHelper('html_form');
		
		$this->page->set('title','Todo details | TaskFreak! Time Tracker');
		// $this->page->add('js',array('jquery.dateentry.pack.js','jquery.timeentry.pack.js'));
		$this->setView('view');
		$this->view();
	}
	
	public function createAction() {
		$this->_loadTask();
		$this->data->addHelper('html_form');
		$this->page->set('title','Create Multiple Todos | TaskFreak! Time Tracker');
		$this->setView('create');
		$this->view();
	}
	
	public function createReaction() {
		$this->result = array();
		$data = explode("\n",$this->fc->getReqVar('data'));
		ArrayHelper::arrayTrim($data);
		$t = count($data); // total to parse
		$i = 0; // successfully parsed counter
		$defDate = ''; // date of previous created task
		$defValue = ''; // default value for multiple tasks creation (set by *)
		foreach ($data as $val) {
			if ($objTask = TaskModel::parse($val, $defValue, $defDate)) {
				// really creating a task
				$objTask->connectDb();
				if ($objTask->check($this->switch_id)) {
					$objTask->insert();
					$i++;
				}
			}
		}
		$this->fc->redirect(APP_WWW_URI.'task/main',$i.' task(s) created !');
	}
	
	public function editAction() {
		$this->_loadTask();
		$this->data->addHelper('html_form');
		
		$this->page->set('title',($this->data->getUid()?'Edit':'Create').' Todo | TaskFreak! Time Tracker');
		$this->setView('edit');
		$this->view();
	}
	
	public function editReaction() {
		$id = $this->_loadTask();
		$this->data->ignore('creation_date'); // do not submit or change creation date
		$this->data->set($this->fc->request);
		if ($this->data->check($this->switch_id)) {
			$this->data->save();
			$this->fc->redirect(APP_WWW_URI,($id)?'task_updated':'task_created');
		}
		return true; // show action again
	}
	
	protected function _loadTask() {
		if (empty($this->data)) {
			$this->data = new TaskModel();
			$this->data->connectDb();
						
			$uid = $this->data->dbUid();
			if ($id = $this->fc->getReqVar($uid)) {
				$this->data->setUid($id);
				$this->data->load();
			}
		}
		return $this->data->getUid();
	}
	
}