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
 * Timer
 * 
 * Class representing a task time log
 * @since 0.1
 */
class TimerModel extends Model {

	public function __construct() {
		parent::__construct('timer');
		$this->addProperties(array(
			'task_id'			=> 'NUM',
			'start'				=> 'DTM',
			'stop'				=> 'DTM',
			'spent'				=> 'DUR',
			'manual'			=> 'BOL', // 0:auto, 1:manual
		));
	}
	
	/**
	 * check submitted data before saving task
	 */
	public function check() {
		return parent::check('task_id,start,stop,spent');
	}
	
	/**
	 * set data and compute missing fields
	 * @param integer $start number of seconds since midnight
	 * @param integer $stop number of seconds since midnight
	 * @param integer $spent number of seconds spent on task
	 */
	public function setCheck() {
		$start = strtotime($this->get('start'));
		$stop = strtotime($this->get('stop'));
		$spent = intval($this->get('spent'));
		if (!$this->isEmpty('start')) {
			if (!$this->isEmpty('stop')) {
				// calc diff between start and stop
				$this->set('spent',$stop-$start);
			} else if ($this->get('spent')) {
				$this->set('stop',strftime(APP_DATETIME_SQL,$start+$spent));
			}
		} else if ($spent) {
			if (!$stop) {
				$this->set('stop',APP_SQL_NOW);
				$stop = APP_NOW;
			}
			$this->set('start',strftime(APP_DATETIME_SQL,$stop-$spent));
		}
	}
	
	/**
	 * starts a timer
	 */
	public static function start($tid) {
		$obj = new TimerModel();
		$obj->connectDb();
		$obj->set('task_id',$tid);
		$obj->set('start',APP_SQL_NOW);
		return $obj->insert();
	}
	
	/**
	 * stops a timer
	 */
	public static function stop($tid) {
		if (!($tid = VarUid::sani($tid))) {
			return false;
		}
		$filter = "task_id='$tid' AND stop='0000-00-00 00:00:00'";
		$obj = new TimerModel();
		$obj->connectDb();
		if (!$obj->load($filter)) {
			return false;
		}
		$obj->set('stop',APP_SQL_NOW);
		$obj->set('spent', strtotime($obj->get('stop')) - strtotime($obj->get('start')));
		$obj->fields('stop,spent');
		return $obj->update($filter);
	}
	
}
