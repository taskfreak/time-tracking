<?php
/**
 * Tzn Framework
 * 
 * @package tzn_core_classes
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Colectable
 *
 * A FIFO implementation
 * @since 0.1
 */
class Collectable extends Pluginable {

	protected $skip;

	public function __construct() {
		parent::__construct();
		$this->skip = array();
		foreach(get_object_vars($this) as $key => $val) {
			if ($key == 'skip') {
				continue;
			}
			$this->_init($key);
			$this->skip[$key] = array();
		}
	}
	/**
	* initialize an empty stack
	*/	
	protected function _init($key,$reset=false) {
		if (!is_array($this->$key) || $reset) {
			$this->$key = array();
		}
	}
	/**
	 * reset a stack
	 */
	public function clean($key, $full=false) {
		$this->_init($key, true);
		if ($full) {
			$this->skip[$key] = array();
		}
	}
	/**
	* initialize the stack with a value
	*/	
	public function set($key, $value) {
		$this->add($key,$value,true,true);
	}
	/**
	* add a value in the stack
	*/
	public function add($key, $value, $force=false, $reset=false) {
		if (!$reset && !$force) {
			if ($this->checkSkip($key, $value)) {
				return false;
			}
		}
		if (is_array($value)) {
			$this->$key = array_unique(array_merge($this->$key, $value));
		} else if (!in_array($value, $this->$key)) {
			$this->{$key}[] = $value;
		}
		return true;
	}
	/**
	* remove value from the stack
	*/
	public function remove($key, $value) {
		$this->addSkip($key, $value);
		if (is_array($this->$key) && in_array($value, $this->$key)) {
			$newArray = array();
			foreach ($this->$key as $val) {
				if ($val == $value) {
					continue;
				}
				$newArray[] = $val;
			}
			$this->$key = $newArray;
		}
	}
	/**
	* skip a value : use remove is a safer option
	*/
	protected function addSkip($key, $value) {
		if (!in_array($value,$this->skip[$key])) {
			$this->skip[$key][] = $value;
		}
	}
	/**
	* check if a value must be skipped
	* @return boolean true if value must be ignored
	*/
	protected function checkSkip($key, $value) {
		if (!is_array($this->skip[$key])) {
			return false;
		}
		return in_array($value, $this->skip[$key]);
	}
}