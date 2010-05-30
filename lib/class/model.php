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
 * Model
 * 
 * The mother class of all models
	* @since 0.1
 */
abstract class Model extends Pluginable {

	protected $_error;		// errors 
	protected $_properties;	// properties definition
	protected $_fields;		// properties selection
	protected $data;		// properties values
	
	protected $_table;

	public function __construct($table='') {
		parent::__construct();
        $this->_error = array();
        $this->_fields = array();
      	if (empty($table)) {	
      		$this->_table = StringHelper::camelToFlat(__CLASS__);
      	} else {
	      	$this->_table = $table;
      	}
    }
    
    // ---- INITIALIZE OBJECT : PROPERTIES ------------------------------------
    
    /**
	 * addProperties : generic function
	 * add property(ies) to class/object
	 * @param mixed $prm1 array or name of property
	 * @param mixed $prm2 if first parameter is, second is type (else: not needed)
	 */
    protected function addProperties($prm1, $prm2=null) {
    	if (is_array($prm1)) {
			if (is_array($this->_properties)) {
				$this->_properties = array_merge($this->_properties,$prm1);
			} else {
				$this->_properties = $prm1;
			}
		} else if ($prm2) {
			if (!is_array($this->_properties)) {
				$this->_properties = array();
			}
			$this->_properties[$prm1] = $prm2;
		}
		$this->all(); // select all fields by default
    }
    
    /**
     * getPropertyType : returns property type
     * @param string $key name of property
     */
    public function getPropertyType($key) {
    	if (isset($this->_properties[$key])) {
			return substr($this->_properties[$key],0,3);
		} else {
			return false;
		}
	}
	
	/**
	 * getPropertyOption : returns property options
	 * @param string $key name of property
	 * @return array of options
	 */
	public function getPropertyOptions($key) {
		$str = substr($this->_properties[$key],4);
		if (empty($str)) {
			return array(); // return empty array
		} else {
			$arr = json_decode($str, true); // force array
			$arr['type'] = $this->getPropertyType($key);
			$arr['value'] = $this->value($key);
			if (is_null($arr['value'])) {
				// no value set ? look for default
				if (isset($arr['default'])) {
					$arr['value'] = $arr['default'];
				}
			}
			return $arr;
		}
	}
	 
	
    /**
	 * removeProperties : generic function
	 * remove property(ies) to class/object
	 * @param prm1 array or name of property
	 * @param prm2 if first parameter is, second is type (else: not needed)
	 */
    protected function removeProperties($prm1) {
    	if (is_array($this->_properties)) {
    		if (is_array($prm1)) {
    			foreach($prm1 as $key) {
    				unset($this->_properties[$key]);
    			}
    		} else {
    			unset($this->_properties[$prm1]);
    		}
    	}
    	$this->all(); // update fields selection
    }
    
    /**
     * initObjectProperties : initialize nested objects
     */
    public function initObjectProperties($nested = false) {
		foreach($this->_properties as $key => $type) {
			if (preg_match('/^OBJ/i',$type)) {
				$class = (strlen($type) > 3)?substr($type,4):$key;
				if ($nested) {
					$this->$key = new $class();
				} else {
					if (!is_object($this->$key) || strtolower(get_class($this->$key)) != strtolower($class)) {
						$this->$key = new $class();
					}
					$this->$key->initObjectProperties(true);
				}
			}
		}
		$this->callPlugins('initObjectProperties', $nested);
		$this->all(); // update fields selection
	}
	
	/**
	 * resetProperties : clear all data
	 */
	public function resetProperties($nested=false) {
		foreach($this->_properties as $key => $type) {
			if (preg_match('/^OBJ/i',$type)) {
				$class = (strlen($type) > 3)?substr($type,4):$key;
				$this->$key = new $class();
				if (!$nested) {
					$this->$key->resetProperties(true);
				}
			} else {
				unset($this->data[$key]);
			}
		}
		$this->callPlugins('resetProperties', $nested);
		$this->all(); // update fields selection
	}
	
	/**
	 * reset : full reset
	 */
	public function reset() {
		$this->resetProperties();
		$this->callHelpers('reset');
	}
	
	// ---- PREPARE DATA MANIPULATION --------------------------------------------
	
	public function fields() {
		if (!func_num_args()) {
			$this->all();
			return true;
		}
		$args = func_get_args();
		$arrFields = forward_static_call_array(array('StringHelper','mixedToArray'),$args);
		$this->_fields = array_keys($this->_properties);
		$this->_fields = array_intersect($this->_fields, $arrFields);
	}
	
	public function ignore() {
		if (!func_num_args()) {
			$this->all();
			return true;
		}
		$args = func_get_args();
		$arrFields = forward_static_call_array(array('StringHelper','mixedToArray'),$args);
		$this->_fields = array_keys($this->_properties);
		$this->_fields = array_diff($this->_fields, $arrFields);
	}
	
	public function all() {
		$this->_fields = array_keys($this->_properties);
		return $this->_fields;
	}
	
	public function getFields() {
		$arr = array();
		foreach($this->_fields as $key) {
			$arr[$key] = true;
		}
		return array_intersect_key($this->_properties,$arr);
	}
	
	// ---- SET PROPERTIES DATA --------------------------------------------------
	
	/**
	 * set : generic set function (for a single property)
	 */
	protected function _set($key, $value)
	{
		// set regular property
		$arr = explode(',',$this->_properties[$key]);
		$class = array_shift($arr);
		$class = 'Var'.ucfirst(strtolower($class));
		// and call the corresponding class/method
		$value = forward_static_call_array(array($class,'sanitize'),array($value, &$arr));
		if (!empty($arr['error'])) {
			// value is invalid
			$this->_error[$key] = $arr['error'];
			$this->data[$key] = $value; // -TODO- value might be empty, but you might want it anyway
			return false;
		} else {
			// value is valid
			$this->data[$key] = $value;
			return true;
		}
	}
	
	protected function _setObject($key, $data, $class='', $nkey='') {
		if (empty($class)) {
			if (!$this->isObjectProperty($key, $class, $nkey)) {
				return false;
			}
		}
		// setting an object
		FC::log_core_debug(get_class($this)."->_setObject($key, [data], $class, $nkey)");
		$obj = new $class;
		// set data set with interresting data only
		$part = array();
		foreach($data as $k => $v) {
			if (preg_match('/^('.$nkey.'__|'.$nkey.'::)([a-z0-9_]+)$/',$k,$m)) {
				$kk = $m[2];
				$part[$kk] = $v;
			}
		}
		FC::log_core_debug(' -> found '.count($part).' properties to be set ('.implode(', ',array_keys($part)).')');
		if (count($part)) {
			if ($obj->set($part)) {
				FC::log_core_debug(" => setting ".get_class($obj)." in $key");
				$this->data[$key] = $obj;
				return true;
			} else {
				FC::log_core_debug($obj->getAllErrors());
			}
		}
		return false;
	}
	
	/**
	 * set : generic set function
	 * @todo deal with files, images, multiple choices, switches
	 */
	public function set()
	{
		$args = func_get_args();
		if (!count($args)) {
			FrontController::log_error('Error: Tzn::get (empty 1st parameter)');
		}
		
		$data = $args[0];
		if (is_array($data)) {
			// first parameter is an array ? Setting all (selected) properties then !
			$ok = true;
			foreach($this->_fields as $key) {
				if ($this->isObjectProperty($key, $class, $nkey)) {
					$r = call_user_func_array(array($this,'_setObject'),array($key, $data, $class, $nkey));
				} else {
					// data not set ?
					$val = (array_key_exists($key, $data))?$data[$key]:NULL;
					// set data
					$r = call_user_func_array(array($this,'_set'),array($key,$val));
					if ($r == false) {
						// just one error and this is it, you're screwed
						$ok = false;
					}
				}
			}
			$this->all(); // select all fields again
			return $ok;
			
		} else {
			// set a single property
			// -TODO- set object property then
			return call_user_func_array(array($this,'_set'),array($data,$args[1]));
		}
	}

	
	// ---- GET PROPERTIES DATA --------------------------------------------------
	
	/**
	 * _get : private generic get function
	 * check property type and call corresponding method
	 * variable parameters
	 * first parameter : the method to call
	 * second parameter : the key
	 */
	private function _get() {
		if (!func_num_args()) {
			FrontController::log_error('Error: Model::get (empty 1st parameter)');
		}
		
		$args = func_get_args();
		// remove first parameter (method)
		$method = array_shift($args);
		// remove second parameter (key)
		$key = array_shift($args);
		
		if (!array_key_exists($key, $this->_properties)) {
			// property not defined, check if trying to access nested object prop ?
			if (preg_match('/^([a-z0-9]+)(__|::)([a-z0-9\_]+)$/', $key, $arr)) {
				// error_log (get_class($this).': check if '.$key.' exists as '.$arr[0].'/'.$arr[1]);
				// try again within nested object
				if (array_key_exists($arr[1], $this->_properties)) {
					if (!empty($this->data[$arr[1]])) {
						$obj = $this->data[$arr[1]];
						if (is_a($obj, 'Model')) {
							array_unshift($args, $arr[3]);
							return call_user_func_array(array($obj,$method),$args);
						}
					} else {
						return false;
					}
				}
			}
			// FrontController::log_error("Error: Model::get (property '$key' does not exist)");
			throw new AppException(get_class($this)."::get (property '$key' does not exist");
			return false;
		}
		
		
		// regular property (not an object), proceed
				
		$dfn = $this->_properties[$key];
		/* -TODO-
		if (is_array($dfn)) {
			return call_user_func_array(array(&$this,'getLst'),$dfn);
		}
		*/

		// parse property definition
		$arrType = explode(',',$dfn);
	
		 
		// guess class name corresponding to property type
		$class = 'Var'.ucfirst(strtolower($arrType[0]));
		// if property value is empty, don't bother and return an empty string
		
		if (!isset($this->data[$key])) {
			// property not set yet
			return null;
		}
		
		// add value as first parameter
		array_unshift($args,$this->data[$key]);
		// and call the corresponding class/method
		return forward_static_call_array(array($class,$method),$args);

	}
	
	/**
	 * get : returns property value, as it fucking is
	 * variable parameters
	 */
	public function get()
	{
		$args = func_get_args();
		array_unshift($args, 'get');
		return call_user_func_array(array($this,'_get'),$args);
	}
	
	/**
	 * html : returns property value, formatted in HTML
	 * variable parameters
	 */
	public function html()
	{
		$args = func_get_args();
		array_unshift($args, 'html');
		return call_user_func_array(array($this,'_get'),$args);
	}
	
	/**
	 * value : returns property value, formatted for HTML forms
	 * variable parameters
	 */
	public function value()
	{
		$args = func_get_args();
		array_unshift($args, 'value');
		return call_user_func_array(array($this,'_get'),$args);
	}
	
	/**
	 * checks if value (or date) is empty
	 */
	public function isEmpty($key) {
		if (empty($this->data[$key]) || preg_match('/^(0000|9999)\-00\-00/',$this->data[$key])) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * checks if property is a class
	 * @param string $key property name
	 * @param string $class model's class name (returned)
	 * @param string $nkey key name for SQL statements (returned)
	 */
	 public function isObjectProperty($key, &$class, &$nkey) {
	 	$class = $nkey = '';
	 	if (empty($this->_properties[$key])) {
	 		return false;
	 	}
	 	$type = $this->_properties[$key];
		if (preg_match('/^OBJ(,([a-z0-9_]*))?$/', $type, $match)) {
			$nkey = (count($match)>2)?$match[2]:$key;
			$class = StringHelper::flatToCamel((count($match)>2)?$nkey:($nkey.'_model'), true);
			return true;
		} else {
			return false;
		}
	}
	
	// ---- DATABASE Capabilities ------------------------------------------------
	
	/**
	 * Add database capabilities
	 */
	public function connectDb() {
		$this->addHelper('db', $this, $this->_table);
	}
	
	/**
	 * get model UID
	 * @param ext : adds table name for SQL query purpose
	 */
	public function proUid() {
		foreach ($this->_properties as $key => $type) {
			if (preg_match('/^UID/i',$type)) {
				return $key;
			}
		}
		return false;
	}
	
	/**
	 * set the data object UID
	 */
	public function setUid($val) {
		if ($key = $this->proUid()) {
			return $this->set($key, $val);
		}
		return false;
	}
	
	/**
	 * get the data object UID
	 */
	public function getUid() {
		if ($key = $this->proUid()) {
			return $this->get($key);
		} else {
			return false;
		}
	}
	
	/**
	 * check data before submitting
	 */
	public function check($fields='') {
		if (empty($this->data)) {
			return false;
		}
		$arr = StringHelper::mixedToArray($fields);
		$ok = true;
		if (count($arr)) {
			foreach($arr as $key) {
				if (empty($this->data[$key])) {
					$this->_error[$key] = 'compulsory_field';
					$ok = false;
				}
			}
		}
		return $ok;
	}
	
	// ---- ERROR Reporting ------------------------------------------------------
	
	/**
	 * return error
	 * @param the field
	 */
	public function getError($key) {
		if (empty($this->_error[$key])) {
			return false;
		} else {
			$err = $this->_error[$key]; 
			if (is_array($err)) {
				$err = implode(', ', $err);
			}
			// -TODO- translate
			return $err;
		}
	}
	/**
	 * print out list of errors (debugging)
	 */
	public function getAllErrors() {
		$str = '';
		if (count($this->_error)) {
			foreach($this->_error as $key => $code) {
				$str .= '['.$key.'] '.$code."\n";
			}
		}
		return $str;
	}
	
	/**
	 * returns error in HTML format
	 */
	public function htmlError($key) {
		if ($str = $this->getError($key)) {
			return '<span class="error">'.VarStr::html($str).'</span>';
		} else {
			return false;
		}
	}
	/**
	 * print out list of errors (debugging)
	 */
	public function htmlAllErrors() {
		if (count($this->_error)) {
			echo '<ul class="error">';
			foreach($this->_error as $key => $code) {
				echo '<li>'.$key.' : '.$code.'</li>';
			}
			echo '</ul>';
		}
	}
	
	/**
	 * temporary debug method
	 */
	public function __toString() {
		$str = '<pre>';
		ob_start();
		print_r($this->data);
		$str .= ob_get_clean();
		$str .= '</pre>';
		return $str;
	}
}

/**
 * Any Variable (abstract class)
 * @since 0.1
 */
abstract class VarAbstract
{
	abstract public static function sanitize($val, &$info);
	
	public static function get($val) {
		return $val;
	}
	
	public static function html($val) {
		return $val;
	}
	
	public static function value($val) {
		return $val;
	}
	
	public static function sql($val, $db) {
		if (is_integer($val)) {
			return $val;
		} else if (empty($val)) {
			return "''";
		} else {
			return "'".$db->escapeString($val)."'";
		}
	}
	
	/**
	 * get current user's timezone offset in seconds
	 * @todo cache it
	 */
	public static function getUserTimeZoneOffset() {
		return intval($GLOBALS['config']['datetime']['timezone_user']->getOffset($GLOBALS['config']['datetime']['now']));
	}
}


/**
 * Data Object ID Variable
 * @since 0.1
 */

class VarUid extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (is_array($val)) {
			throw new AppException('VarUid Sanitize Hell : '.implode(', ',$val));
		}
		if (preg_match('/^[0-9A-Z]+$/i', $val)) {
			return $val;
		} else {
			return false;
		}
	}
}

/**
 * Integer Variable
 * @since 0.1
 */
class VarInt  extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$value = preg_replace(array('/[a-zA-Z]/','/ /','/,/'),'',$val);
		return intval($val);
	}

}

/**
 * Positive integer Variable
 * @since 0.1
 */
class VarNum extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$value = preg_replace(array('/[a-zA-Z]/','/ /','/,/'),'',$val);
		return abs(intval($val));
	}
	
}

/**
 * Float / Decimal Variable
 * @since 0.1
 */
class VarDec extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$val = preg_replace(array('/[a-zA-Z]/','/ /','/,/'),array('','','.'),$val);
		if (preg_match('/[0-9]*(\.[0-9]+)?/',$val)) {
			return $val;
		} else {
			return 0;
		}
	}
	
}

/**
 * Date variable
 * @since 0.1
 */
class VarDte extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (empty($val)) {
			return '0000-00-00';
		}
		if (preg_match('/^[0|2|9][0|1|9][0-9]{2}\-[0-1][0-9]\-[0-3][0-9]$/', $val)) {
			return $val;
		}
		if (!$GLOBALS['config']['datetime']['us_format']) {
			// try to parse non US format (dd/mm/yy)
			if (preg_match('/^([0-3]?[0-9])\/([0-1][0-9])(\/(20)?[0-9]{2})?$/',$val, $arr)) {
				if (empty($arr[3])) {
					$arr[3] = APP_YEAR;
				} else if (strlen($arr[3]) == 2) {
					$arr[3] = substr(APP_YEAR,0,2).substr($arr[3],1);
				} else {
					$arr[3] = substr($arr[3],1);
				}
				return $arr[3].'-'.$arr[2].'-'.$arr[1];
			}
		}
		// try human readable formats (english only)
		if ($t = self::strToUnix($val)) {
			return strftime(APP_DATE_SQL, $t);
		}
		$info['error'] = 'model_date_invalid';
		return false;
	}
	
	public static function html($val, $format='', $default='') {
		if (!$format) {
			$format = $format = $GLOBALS['config']['datetime']['us_format']?APP_DATE_USA:APP_DATE_EUR;;
		}
		$t = VarDte::strToUnix($val);
		if ($t === false) {
			return '/!\\';
		}
		if ($t) {
			return strftime($format, $t);
		} else {
			return $default;
		}
		// -TODO- back as human readable format
		// -TODO- and in different formats, possibly
		return $val;
	}
	
	public static function value($val) {
		if (empty($val) || preg_match('/^(0000|9999)\-00\-00/',$val)) {
			return '';
		}
		$arr = explode('-',$val);
		// -TODO- US format inverts day and month
		// -TODO- might want to use another separator instead of /
		$str = $arr[2].'/'.$arr[1].'/'.$arr[0];
		return $str;
	}
	
	public static function strToUnix($val) {
		if (!$val || preg_match('/^(0000|9999)/',$val)) {
			return 0;
		}
		$t = strtotime($val);
		if ($t === false) {
			return false;
		}
		return $t;
	}
}

/**
 * Duration Variable (stored as number of seconds)
 * @since 0.1
 */
class VarDur extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (empty($val)) {
			return 0;
		}
		if (preg_match('/^[0-9]+$/', $val)) {
			// just a number of seconds
			return intval($val);
		} else if (preg_match('/^([0-2]?[0-9])\:([0-5][0-9])(\:([0-5][0-9]))?$/', $val, $arr)) {
			$t = 0;
			switch(count($arr)) {
			case 5:
				// seconds
				$t += intval($arr[4]);
			case 3:
				// minutes
				$t += intval($arr[2])*60;
				$t += intval($arr[1])*3600;
			}
			return $t;
		} else {
			$info['error'] = 'model_time_invalid';
			return false;
		}
	}
	
	public static function html($val) {
		return self::workout($val);
	}
	
	public static function value($val) {
		return self::workout($val);
	}
	
	/**
	 * format a number of seconds into HH:MM:SS
	 * @todo enable callbacks to round up seconds if not showing
	 */
	public static function workout($val, $seconds=true) {
		$h = floor($val / 3600);
		$m = floor($val / 60) - ($h*60);
		$s = $val - ($h*3600 + $m*60);
		if ($seconds) {
			return str_pad($h, 2, '0',STR_PAD_LEFT)
				.':'.str_pad($m, 2, '0',STR_PAD_LEFT)
				.':'.str_pad($s, 2, '0',STR_PAD_LEFT);
		} else {
			if ($s) {
				// -TODO- add callback to round up seconds
				$m++;
			}
			return str_pad($h, 2, '0',STR_PAD_LEFT)
				.':'.str_pad($m, 2, '0',STR_PAD_LEFT);
		}
	}
	
}

/**
 * Time Variable (stored in GMT)
 * @since 0.1
 */

class VarTim extends VarDur
{
	public static function sanitize($val, &$info) {
		if (empty($val)) {
			return 0;
		}
		if (preg_match('/^[0-9]+$/', $val)) {
			// number of seconds, must be coming from SQL
			return intval($val);
		}
		if (preg_match('/^([0-2]?[0-9])\:([0-5][0-9])(\:([0-5][0-9]))?$/', $val, $arr)) {
			// coming from a form
			$t = 0;
			switch(count($arr)) {
			case 5:
				// seconds
				$t += intval($arr[4]);
			case 3:
				// minutes
				$t += intval($arr[2])*60;
				$t += intval($arr[1])*3600;
			}
			// now make GMT
			return $t - self::getUserTimeZoneOffset();
		}
		$info['error'] = 'model_time_invalid';
		return false;
	}
	
	/**
	 * work on date to return human readable value
	 * considers user's time zone
	 */
	public static function workout($val, $seconds=true) {
		$val += self::getUserTimeZoneOffset();
		return parent::workout($val);
	}
}

/**
 * Date / Time Variable
 * @todo form field format
 * @since 0.1
 */
class VarDtm extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (empty($val)) {
			return '0000-00-00 00:00:00';
		}
		if (preg_match('/^[0|2|9][0|1|9][0-9]{2}\-[0-1][0-9]\-[0-3][0-9]( ([0-2][0-9]\:[0-5][0-9])(\:[0-5][0-9])?)?$/', $val)) {
			// SQL format, return it as it is
			return $val;
		}
		if ($val == 'NOW') {
			return APP_SQL_NOW;
		}
		$d = '';
		$t = 0;
		$arr = explode(' ',$val);
		foreach($arr as $v) {
			if ($tmp = VarTim::sanitize($v, $err)) {
				$t = $tmp;
			} else if ($tmp = VarDte::sanitize($v, $err)) {
				$d = $tmp;
			}
		}
		if ($d && is_integer($t)) {
			$dh = 3600*24;
			if  ($t < 0) {
				// got to go one day back
				$d = strftime(APP_DATE_SQL,strtotime($d)-$dh);
				$t = $dh+$t; // + means - as $t is < 0
			} else if ($t > $dh) {
				// got to move on one day
				$d = strftime(APP_DATE_SQL,strtotime($d)+$dh);
				$t = $t-$dh;
			}
			// return datetime in SQL format
			return $d.' '.VarDur::workout($t);
		}
		$info['error'] = 'model_datetime_invalid';
		return false;
	}
	
	public static function get($val) {
		return $val;
	}
	
	public static function html($val, $format='', $default='') {
		if (!$format) {
			$format = $GLOBALS['config']['datetime']['us_format']?APP_DATETIME_USA:APP_DATETIME_EUR;
		}
		$t = VarDtm::strToUnix($val);
		if ($t === false) {
			return '/!\\';
		}
		if ($t) {
			return strftime($format, $t);
		} else {
			return $default;
		}
		return $val;
	}
	
	public static function value($val) {
		// -TODO- back as form readable value
		return $val;
	}
	
	public static function strToUnix($val) {
		if (!$val || preg_match('/^(0000|9999)/',$val)) {
			return 0;
		}
		$t = strtotime($val);
		if ($t === false) {
			return false;
		}
		$t += self::getUserTimeZoneOffset();
		return $t;
	}
	
	/**
	 * work on date format to return human readable value
	 */
	public static function workout($val, $seconds=true) {
		$val += self::getUserTimeZoneOffset();
		return parent::workout($val);
	}
}

/**
 * String Variable
 * @since 0.1
 * @todo Check the workout and specialchars methods
 */
class VarStr extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (is_array($val)) {
			throw new AppException('VarStr Sanitize Hell : '.implode(', ',$val));
		}
		return strip_tags($val);
	}
	
	public static function html($val) {
		return self::specialchars($val);
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
	
	public static function workout($value, $cut = 0, $pos = 1) {
		return $value;
		// -TODO-
		if (is_int($arg[1])) {
			$cut = $arg[1];
			$pos++;
		}
		$default = $arg[$pos++];
		$style = $arg[$pos];
		$value = self::sanitize($value);
		if ($cut) {
			$value = str_replace("\r\n"," ",$value);
			if (($cut > 2) && (strlen($value) > $cut)) {
				$value = trim(substr($value,0,($cut-2))).".."; 
			}
		}
		return $value;
	}
	
	public static function specialchars($val) {
		if (is_array($val)) {
			throw new AppException('VarStr SpecialChars Hell : '.implode(', ',$val));
		}
		switch ($GLOBALS['config']['lang']['specialchars']) {
		case 3:
			$val = htmlentities($val);
			break;
		case 2:
			$val = htmlspecialchars($val);
			break;
		case 1:
		default:
			$spe = array('&','<','>');
			$sfe = array('&amp;','&lt;','&gt;');
			$val = str_replace($spe,$sfe,$val);
			break;
		}
		return str_replace('"','&quot;',$val);
	}
}

/**
 * Username variable - simple text, no special chars, no spaces
 * @since 0.1
 */
class VarUsr extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (!$val && in_array('compulsory', $info)) {
			$info['error'] = 'user_name_invalid';
			return false;
		}
		if ((strlen($val) < APP_USER_NAME_MIN) 
        	|| (strlen($val) > APP_USER_NAME_MAX)) {
            $info['error'] = 'user_name_length';
            return false;
        } else if (preg_match(APP_USER_SANITIZE, $val)) {
            return $val;
        } else {
			$info['error'] = 'user_name_invalid';
			return false;
		}
		return $val;
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
} 

/**
 * Password variable
 * @since 0.1
 */
class VarPss extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (empty($val)) {
			if (in_array('compulsory',$info)) {
				$info['error'] = 'password_required';
				return false;
			} else {
				return true;
			}
		}
		if (preg_match(APP_PASSWORD_SANITIZE, $val)) {
            return $val;
        } else {
			$info['error'] = 'password_invalid';
			return false;
		}
		return $val;
	}

}

/**
 * Text Variable - unformatted
 * @since 0.1
 */
class VarTxt extends VarAbstract
{
	public static function sanitize($val, &$info) {
		return preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is","", $val);
	}
	
	public static function html($val, $cut=0) {
		if ($cut && $cut < strlen($val)) {
			$val = substr($val,0,$cut).' [...]';
		}
		return str_replace(array("\r\n", "\r", "\n"), "<br />", VarStr::specialchars($val));
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
}

/**
 * Text Variable - simple style
 * @since 0.1
 */
class VarBbs extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$val = preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is"
			,"", $val); 
		$val = preg_replace("/<\/?(div|span|iframe|frame|input|"
			."textarea|script|style|applet|object|param|embed|form)[^>]*>/is"
			,"", $val);
		return $val;
	}
	
	public static function html($val, $cut=0) {
		if ($cut) {
			$val = strip_tags($val);
			if ($cut < strlen($val)) {
				$val = substr($val,0,$cut).' [...]';
			}
		} else {
			$val = preg_replace("/(?<!\")((http|ftp)+(s)?"
				.":\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $val);
		}
		return str_replace(array("\r\n", "\r", "\n"), "<br />", str_replace('"','&quot;',$val));
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
}

/**
 * Text Variable - Full HTML (but no scripting)
 * @since 0.1
 */
class VarHtm extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$val = preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is","", $val);
		return $val;
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
}

/**
 * Text Variable - Full HTML (even scripting)
 * better be careful with that one ! really not secure...
 * @since 0.1
 */
class VarXml extends VarAbstract
{
	public static function sanitize($val, &$info) {
		return $val;
	}
	
	public static function value($val) {
		return str_replace('"','&quot;',$val);
	}
}

/**
 * URL Variable - URL Address
 * @since 0.1
 */
class VarUrl extends VarAbstract
{
	public static function sanitize($val, &$info) {
		$val = trim($val);
		if ($val && (!preg_match("/^(http|https|ftp)?:\/\//i",$val))) {
			$val = "http://".$val;
		}
		if ($val == "http://") {
			$val = "";
		}
		// -TODO- check if URL is valid
		return $val;
	}
	
	public static function html($val) {
		// -TODO- might want to create a link there
		return $val;
	}

}

/**
 * Email Variable
 * @since 0.1
 */
class VarEml extends VarAbstract
{
	public static function sanitize($val, &$info) {
		if (empty($val) || preg_match("/^[a-z0-9]([a-z0-9_\-\.\+]*)@([a-z0-9_\-\.]*)\.([a-z]{2,4})$/i",$val)) {
			return $val;
		} else {
			$info['error'] = 'model_email_invalid'; // -TODO-TRANSLATE-
			return '';
		}
	}
	
	public static function html($val) {
		// -TODO- might want to create a link there
		return $val;
	}
	
}

/**
 * Document Variable
 * @todo Everything
 * @since 0.4
 */
class VarDoc extends VarAbstract
{
	public static function sanitize($val, &$info) {
		// -TODO-
	}
	
	public static function get($val) {
		// -TODO-
	}
	
	public static function html($val) {
		// -TODO-
	}
	
	public static function value($val) {
		// -TODO-
	}
}

/**
 * Image Variable
 * @todo Everything
 * @since 0.4
 */
class VarImg extends VarAbstract
{
	public static function sanitize($val, &$info) {
		// -TODO-
	}
	
	public static function get($val) {
		// -TODO-
	}
	
	public static function html($val) {
		// -TODO-
	}
	
	public static function value($val) {
		// -TODO-
	}
}

/**
 * Boolean Variable - Just one or zero, true or false
 * @since 0.4
 */
class VarBol extends VarAbstract
{
	public static function sanitize($val, &$info) {
		return ($val)?1:0;
	}
	
}

/**
 * Level Variable - Used for preferences, rights and switches
 * @todo Everything
 * @since 0.5
 */
class VarLvl extends VarAbstract
{
	public static function sanitize($val, &$info) {
		// -TODO-
		return intval($val);
	}
	
	public static function get($val) {
		// -TODO-
		return $val;
	}
	
	public static function html($val) {
		// -TODO-
		return $val;
	}
	
	public static function value($val) {
		// -TODO-
		return $val;
	}
}

/**
 * Level Variable - Used for preferences, rights and switches
 * @since 0.1
 */
class VarObj extends VarAbstract
{
	public static function sanitize($val, &$info) {
		echo 'VarObj::sanitize should not be called';
		exit;
	}
	
	public static function html($val) {
		throw new AppException('can not render object');
	}
	
	public static function value($val) {
		throw new AppException('can not render object');
	}
	
}