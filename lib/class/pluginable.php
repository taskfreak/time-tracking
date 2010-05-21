<?php
/**
 * Tzn Framework
 * 
 * @package tzn_core_classes
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.1
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Pluginable
 *
 * Abstract class implementing all plugin related methods 
 * All extendable / customizable class should implement Pluginable
 */
abstract class Pluginable extends Helpable {

	private $_plugins;

	public function __construct() {
		parent::__construct();
		$this->_plugins = array();
		$class = get_class($this); // get class name of implemented object (not the super class name)
		/*
		if (is_array($GLOBALS['config']['plugin']) && count($GLOBALS['config']['plugin'][$class])) {
			foreach ($GLOBALS['config']['plugin'][$class] as $key) {
				// error_log("INIT plugin for $class : $key");
				$this->addPlugin($key);
			}
		}
		*/
	}
	
	protected function addPlugin($key, $obj=null) {
		if (is_null($obj)) {
			$class = StringHelper::flatToCamel($key, true);
			$this->_plugins[$key] = new $class;
		} else {
			$this->_plugins[$key] = $obj;
		}
	}
	
	protected function getPlugin($key) {
		if (!is_array($this->_plugins)) {
			return false;
		}
		if (array_key_exists($key, $this->_plugins)) {
			return $this->_plugins[$key];
		} else {
			return false;
		}
	}
	
	protected function setPlugin($key, $obj) {
		if (!is_array($this->_plugins)) {
			return false;
		}
		if (array_key_exists($key, $this->_plugins)) {
			$this->_plugins[$key] = $obj;
			return true;
		} else {
			return false;
		}
	}
	
	protected function callPlugin() {
		$args = func_get_args();
		$key = array_shift($args);
		$method = array_shift($args);
		$obj = $this->getPlugin($key);
		if (!$obj) {
			return false;
		}
		$obj = $this->_plugins[$key];
		call_user_func_array(array($obj,$method),$args);
	}
	
	protected function callPlugins($method) {
		if (!count($this->_plugins)) {
			return false;
		}
		$arr = array();
		$args = func_get_args();
		$method = array_shift($args);
		foreach ($this->_plugins as $key => $obj) {
			if (!method_exists($obj, $method)) {
				continue;
			}
			if ($this->id) {
				$obj->id = $this->id;
			}
			$r = call_user_func_array(array($obj,$method),$args);
			$arr[$key] = $r;
		}
		return $arr;
	}
}