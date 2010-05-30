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
class Timer extends AppController {

	public function __construct() {
		parent::__construct(true);
				
		$this->fc->loadModel('TaskModel');
		
		$this->page->clean('css');
		$this->page->clean('js');
	}

	/**
	 * show timer creation form
	 */
	public function mainAction() {
		$this->setView('include/timer-form.php');
		$this->view();
	}
	
	/**
	 * create timer
	 */
	public function mainReaction() {
		$date = VarDte::sanitize($_POST['date'],$err);
		$start = VarTim::sanitize($_POST['start_time'],$err);
		$stop = VarTim::sanitize($_POST['stop_time'],$err);
		$spent = VarDur::sanitize($_POST['spent'],$err);
		$this->data = new TimerModel();
		$this->data->set('task_id', $_POST['id']);
		if ($start) {
			$this->data->set('start',$_POST['date'].' '.$_POST['start_time']);
		}
		if ($stop) {
			$this->data->set('stop',$_POST['date'].' '.$_POST['stop_time']);
		}
		if ($spent) {
			$this->data->set('spent', $_POST['spent']);
		}
		
		/*
		echo '<pre>';
		print_r($_POST);
		echo "\n\n";
		echo "date : $date\n";
		echo "start : $start : ".$this->data->get('start')."\n";
		echo "stop : $stop : ".$this->data->get('stop')."\n";
		echo "spent: $spent\n";
		echo '</pre>';
		*/
		
		$this->data->setCheck();
		if ($this->data->check()) {
			$this->data->connectDb();
			$this->data->set('manual',1);
			$this->data->insert();
		}
		
		/*		
		echo $this->data;
		exit;
		*/
		
		echo '<script type="text/javascript">';
		echo "reloadList(); window.setTimeout('$.fn.colorbox.close()',1000);";
		echo '</script>';
		echo '<p class="empty">time added</p>';
		return false;
	}
	
	public function deleteAction() {
		// check stuff
		$id = $this->fc->getReqVar('id');
		$start = $this->fc->getReqVar('start');
		
		if (empty($id) || empty($start)) {
			$this->fc->redirect(APP_WWW_URI,'ERROR:missing parameters');
		}
	
		// delete timer
		$this->data = new TimerModel();
		$this->data->connectDb();
		if ($this->data->delete('task_id='.$id." AND start='".$start."'"))
		{
			// successfully deleted
			if ($this->fc->getReqVar('ajax')) {
				$this->data = new TaskSummary();
				$this->data->connectDb();
				
				$ok = false;			
				if ($id = $this->fc->getReqVar('id')) {
					$this->data->where('id='.$id);
					if ($this->data->loadExpandList()) {
						$ok = $this->data->next();
					}
				}
				$this->setView('include/timer-list');
				$this->view();
				echo '<script type="text/javascript">';
				echo "reloadList();";
				echo '</script>';
			} else {
				$this->fc->redirect(APP_WWW_URI,'Timer deleted');
			}
		} else {
			// error deleting
			if ($this->fc->getReqVar('ajax')) {
				echo '<script type="text/javascript">';
				echo 'alert("can not delete timer");';
				echo '</script>';
			} else {
				$this->fc->redirect(APP_WWW_URI,'Timer NOT deleted');
			}
		}
		
	}
}