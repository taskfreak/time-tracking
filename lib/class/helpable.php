<?php
/**
 * Tzn Framework
 * 
 * @package tzn_core_classes
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.2
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Helpable
 * 
 * add possibility to have helpers, and call helper method if it doesn't exist
 * @since 0.1
 */
abstract class Helpable {

	protected $_helpers;

	public function __construct() {
		$this->_helpers = array();
	}
	
	/**
	 * add helper to the class
	 * @param string $helper name of helper (flat format)
	 * @param object $obj object to be passed on
	 */
	public function addHelper() {
		$args = func_get_args();
		// helper's name
		$helper = array_shift($args);
		$class = StringHelper::flatToCamel($helper, true).'Helper';
		// reference to this object
		$obj = array_shift($args);
		if (is_null($obj)) {
			$obj = $this;
		}
		// add helper (didn't find any better way to deal with variable constructor parameters)
		switch(count($args)) {
			case 4:
				$this->_helpers[$helper] = new $class($obj, $args[0], $args[1], $args[2], $args[3]);
				break;
			case 3:
				$this->_helpers[$helper] = new $class($obj, $args[0], $args[1], $args[2]);
				break;
			case 2:
				$this->_helpers[$helper] = new $class($obj, $args[0], $args[1]);
				break;
			case 1:
				$this->_helpers[$helper] = new $class($obj, $args[0]);
				break;
			default:
				$this->_helpers[$helper] = new $class($obj);
				break;	
		}
	}
	
	/**
	 * call helper method
	 * @param string $key the helper key
	 * @param string $method the method to be called
	 */
	public function callHelper() {
		$args = func_get_args();
		$helper = array_shift($args);
		$method = array_shift($args);
		return call_user_func_array(array($this->_helpers[$helper], $method), $args);
	}
	
	/**
	 * call helper's method (arguments are passed as an array
	 */
	public function callHelperArray() {
		$args = func_get_args();
		$helper = array_shift($args);
		$method = array_shift($args);
		if (!empty($args)) {
			return call_user_func_array(array($this->_helpers[$helper], $method), $args[0]);
		} else {
			return $this->_helpers[$helper]->$method();
		}
	}
	
	/**
	 * call all available helpers method
	 * @param string $method the method to be called
	 */
	protected function callHelpers($method) {
		if (!count($this->_helpers)) {
			return false;
		}
		$arr = array();
		$args = func_get_args();
		$method = array_shift($args);
		foreach ($this->_helpers as $key => $obj) {
			if (!method_exists($obj, $method)) {
				continue;
			}
			$r = call_user_func_array(array($obj,$method),$args);
			$arr[$key] = $r;
		}
		return $arr;
	}
	
	/**
	 * check if helper is available
	 */
	public function hasHelper($helper) {
		return array_key_exists($helper, $this->_helpers);
	}
	
	/**
	 * get helper
	 */
	public function &getHelper($helper) {
		if ($this->hasHelper($helper)) {
			return $this->_helpers[$helper];
		} else {
			throw new AppException('Unknown helper '.$helper.' in '.get_class($this));
		}
	}
	
	/**
	 * call helper method if not defined here
	 */
	public function __call($name, $args) {
		if (!count($this->_helpers)) {
			throw new AppException('Unknown method '.$name.' in '.get_class($this).' (and no helper available)');
		}
		
		// check if helper's name has been added to method's name eg. methodHelper
		/* TOO UGLY, TOO COSTY
		$subname = StringHelper::camelToFlat($name);
		if ($idx = strrpos($subname, '__')) {
			$subhelp = substr($subname, $idx+1);
			if (array_key_exists($subhelp, $this->_helpers)) {
				$subname = StringHelper::flatToCamel(substr($subname, 0, $idx));
				$obj = $this->_helpers[$subhelp];
				if (method_exists($obj, $name)) {
					return call_user_func_array(array($obj, $name), $args);
				} 
			}
		}
		*/
		
		// check if method exists in one of the helper
		foreach ($this->_helpers as $key => $obj) {
			if (method_exists($obj, $name)) {
				return call_user_func_array(array($obj, $name), $args);
			}
		}
		
		// try again within helper if __call is available
		foreach ($this->_helpers as $key => $obj) {
			if (method_exists($obj, '__call')) {
				return call_user_func_array(array($obj, $name), $args);
			}
		}
		
		// nope, forget it
		throw new AppException('Unknown method '.$name.' in '.get_class($this).' or any helper ('.implode(', ',array_keys($this->_helpers)).')');
	}
}

/**
 * Singleton helpable super class
 *
 * sucks duplicating code, but needed for singletons as constructor must be private
 * @since 0.1
 */
 
abstract class HelpableSingleton {

	private $_helpers;

	private function __construct() {
		$this->_helpers = array();
	}
	
	/**
	 * add helper to the class
	 */
	protected function addHelper($helper, $obj=null) {
		$class = StringHelper::flatToCamel($helper, true).'Helper';
		$this->_helpers[$helper] = new $class($obj);
	}
	
	/**
	 * check if helper is available
	 */
	public function hasHelper($helper) {
		return array_key_exists($helper, $this->_helpers);
	}
	
	/**
	 * get helper
	 */
	public function &getHelper($helper) {
		if ($this->hasHelper($helper)) {
			return $this->_helpers[$helper];
		} else {
			throw new AppException('Unknown helper '.$helper.' in '.get_class($this));
		}
	}
	
	/**
	 * call helper method if not defined here
	 */
	public function __call($name, $args) {
		if (!count($this->_helpers)) {
			throw new AppException('Unknown method '.$name.' in '.__CLASS__.' (and no helper available)');
		}
		foreach ($this->_helpers as $key => $obj) {
			foreach ($this->_helpers as $key => $obj) {
				if (method_exists($obj, $name)) {
					return call_user_func_array(array($obj, $name), $args);
				}
			}
			if (is_a($obj, 'Callable')) {
				return call_user_func_array(array($obj, $name), $args);
			}
		}
		throw new AppException('Unknown method '.$name.' in '.__CLASS__.' or any helper ('.implode(', ',array_keys($this->_helpers)).')');
	}
}

/**
 * Callable interface
 *
 * class implementing this will be able to send calls to other nested classes
 */
interface Callable {
	public function __call($name, $args);
	/*
	public function __call($name, $args) {
		$arr = array('class1','class2');
		foreach ($arr as $class) {
			if (method_exists($this->$class, $name)) {
				return call_user_func_array(array($this->$class, $name), $args);
			}
		}
		throw new AppException('Unknown method '.$name.' in '.__CLASS__.', '.implode(', ',$arr));
	}
	*/
}