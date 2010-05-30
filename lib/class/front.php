<?php
/**
 * Tzn Framework
 * 
 * @package tzn_core_classes
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.3
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */
 
/**
 * Front Controller
 * 
 * The is the root class of the application, the mother of initialization process
 * It's a singleton, defining mostly static methods
 * @since 0.1
 */
class FrontController extends HelpableSingleton {

	private static $instance;
	
	protected $autoPath;
	
	public $controller;
	public $action;
	public $request;
	
	public $settings;
	public $user;
	public $db;

	/**
	 * private constructor (singleton)
	 */
	private function __construct() {
		
		$this->autoPath = $authoPath = array();
		include APP_CONFIG_PATH.'path.php';
		$this->autoPath = $autoPath;
		unset($autoPath);
		
		$this->controller = $GLOBALS['config']['app']['default_controller'];
		$this->action = $GLOBALS['config']['app']['default_action'];
		$this->request = array();
		
		$this->_helpers = array();
		
		$this->db = array();
		
		if (!isset($_SESSION['appVariables'])) {
			$_SESSION['appVariables'] = array();
		}

	}

	/**
	 * the getInstance() method returns a single instance of the object
	 */
	public static function getInstance() {
		if(!isset(self::$instance)){
			spl_autoload_register(array('FrontController','autoLoad'));
			$object= __CLASS__;
			self::$instance=new $object;
			self::$instance->initApp();
		}
		return self::$instance;
	}
	
	// ---- APP INITIALIZATION ---------------------------------------------------
	
	/**
	 * app initialization (from config)
	 */
	protected function initApp() {
	
		if (APP_SETUP_DATABASE) {
		
			// load up database settings (host, username, etc...)
			include APP_CONFIG_PATH.'db.php';
		
			// connect to database
			$this->initDatabase();
		
		}
		
		if (APP_SETUP_MESSAGING) {
			// initialize messaging (trans-pages messages)
			$this->initMessaging();
		}
		
		if (APP_SETUP_DATABASE) {
			
			// load general settings
			if (APP_SETUP_GLOBAL_SETTINGS) {
				$this->loadSettings();
			}
			
			// load user (if needed)
			$this->loadUser(APP_SETUP_USER_MODEL);

			// load user preferences
			if (APP_SETUP_USER_SETTINGS) {
				$this->loadUserSettings();
			}
		}
		
		if (APP_SETUP_TRANSLATOR) {
			// initialize translations system
			$this->initTranslator();
		}
		
		if (APP_SETUP_NAVI) {
			// initialize navigation (referers, redirects and URL manipulation)
			$this->initNavi();
		}
	}
	
	/**
	 * initialize database connection
	 */
	public function initDatabase() {
		$i = count($this->db);
		$this->db[$i] = new DbConnector();
		$this->db[$i]->connect();
	}
	
	/**
	 * load application settings
	 * @todo load settings
	 * @todo set default controller and action
	 */
	public function loadSettings() {
		$this->settings = new SettingModel();
		// -TODO- load 'em up
	}
	
	/**
	 * load and authenticate user
	 */
	public function loadUser($class) {
		if (empty($class)) {
			return false;
		}
		$class = StringHelper::flatToCamel($class,true).'Model';
		$this->user = new $class;
		$this->user->enableAuthentication();
		$this->user->connectDb();
		$this->user->checkLogin();
	}
	
	/**
	 * load user settings
	 * @todo load user settings and override global settings
	 */
	public function loadUserSettings() {
		// might overload default controller and action
		// -TODO-
		if ($this->user->isLoggedIn()) {
			$this->setSessionDefault('usertask', $this->user->getUid());
		}
	}
	
	/**
	 * initialize messaging system
	 */
	public function initMessaging() {
		$this->addHelper('messaging');
	}
	
	/**
	 * initialize referrers system
	 */
	public function initNavi() {
		$this->addHelper('navi');
	}
	
	/**
	 * initialize translator
	 */
	public function initTranslator() {
		$this->addHelper('translator');
	}
	
	/**
	 * launch application controller
	 */
	public function run() {
		// parse URL : look for controller, action, parameters...
		$this->parseUrl();
		
		// load up requested Application Controller
		$con = self::loadController($this->controller);
		
		if (!$con) {
			// controller not found, throw exception
			if (isset($GLOBALS['config']['error']['not_found'])) {
				$this->_sendErrorCodeToRobots('404');
				include $GLOBALS['config']['error']['not_found'];
				exit;
			} else {
				try {
					throw new AppException('Controller '.$this->controller.' not defined in '.implode(', ', $this->autoPath['controller']));
				} catch(Exception $e) {
					self::log_debug('error loading controller '.$this->controller);
					echo $e;
					exit;
				}
			}		
		}

		$obj = new $con;
		$act = $this->action;

		// if submitted, call requested action (mainReaction by default)
		if (!empty($_POST)) {
			if (method_exists($obj, $act.'Reaction')) {
				if (!call_user_func(array($obj,$act.'Reaction'))) {
					// stop here
					return true;
				}	
			} else {
				self::log_error('method '.$act.'Reaction not defined in controller '.$this->controller);
			}
		}
		
		// if still needed, call requested action (mainAction by default)
		if (method_exists($obj, $act.'Action')) {
			call_user_func(array($obj, $act.'Action'));
		} else {
			if (isset($GLOBALS['config']['error']['not_found'])) {
				$this->_sendErrorCodeToRobots('404');
				include $GLOBALS['config']['error']['not_found'];
				exit;
			}
			self::log_error('method '.$act.'Action not defined in controller '.$this->controller);
			return false;
		}
		return true;
	}
	
	// ---- HTTP QUERY PARSER ----------------------------------------------------
	
	/**
	 * clean $_POST, $_GET and $_REQUEST if magic quotes are on
	 */
	protected static function cleanRequest() {
		if (!get_magic_quotes_gpc()) {
			return true;
		}
		$arrReq = array('_POST','_GET','_REQUEST');
		foreach ($arrReq as $var) {
			if (count($GLOBALS[$var])) {
				foreach($GLOBALS[$var] as $key => $val) {
					$GLOBALS[$var][$key] = stripslashes($val);
				}
			}
		}
		return true;
	}
	
	/**
	 * gets HTTP variable
	 * @return null if not found, value if set
	 */
	public function getReqVar($key) {
		if (isset($this->request[$key])) {
			return $this->request[$key]; 
		} else {
			return null;
		}
	}
	
	/**
	 * checks if variable is submitted by HTTP request
	 * @param $mix an array or string containing key(s) to be checked
	 * @return first found in request
	 */
	public function chkReqVar($mix) {
		$arr = StringHelper::mixedToArray($mix);
		foreach($arr as $key) {
			if (isset($this->request[$key])) {
				return $key;
			}
		}
		return false;
	}
	
	/**
	 * parse request for controller, action and other parameters
	 */
	public function parseUrl() {
	
		// clean ugly magic quotes
		self::cleanRequest();
		
		$req = $_SERVER['REQUEST_URI'];
		
		// remove subfolder setup if non virtual host
		if (strlen(APP_WWW_URI) > 1) {
			$req = str_ireplace(APP_WWW_URI,'',$req);
		}
		// remove trailing slashes
		$req = trim($req,'/');
		// parse if non empty
		if ($req) {
			if ($pos = strrpos($req,'.html')) {
				$req = substr($req, 0, $pos);
			} else if ($pos = strrpos($req,'?')) {
				$req = substr($req, 0, $pos);
			}
			$arrReq = explode('/',$req);
			if (count($arrReq)) {
				$this->controller = array_shift($arrReq);
			}
			if (count($arrReq)) {
				$this->action = array_shift($arrReq);
			} else {
				$this->action = 'main';
			}

			while(count($arrReq)) {
				$key = array_shift($arrReq);
				if (count($arrReq)) {
					$val = array_shift($arrReq);
					$this->request[$key] = urldecode($val);
				} else {
					$this->request[$key] = '';
				}
			}
		}
		
		// parse query string
		if (count($_REQUEST)) {
			foreach($_REQUEST as $key => $val) {
				switch($key) {
				case 'c' :
					$this->controller = $val;
					break;
				case 'a' :
					$this->action = $val;
					break;
				default :
					$this->request[$key] = $val;
					break;		
				}
			}
		}
		
		// get real names
		$this->controller = StringHelper::flatToCamel($this->controller, true,'-');
		$this->action = StringHelper::flatToCamel($this->action, false,'-');
	}
	
	/**
	 * generate URL
	 */
	public static function getUrl($controller='', $action = '', $params = '') {
		$url = APP_WWW_URI;
		
		if (!$controller) {
			return $url;
		}		
		
		if (@constant('APP_URL_REWRITE')) {
			$url .= StringHelper::camelToFlat($controller,'-');
			if ($action) {
				$url .= '/'.StringHelper::camelToFlat($action,'-');
			}
		} else {
			$url .= 'index.php?c='.StringHelper::camelToFlat($controller,'-');
			if ($action) {
				$url .= '&a='.StringHelper::camelToFlat($action,'-');
			}
		}
		
		if (is_array($params)) {
			if (@constant('APP_URL_REWRITE')) {
				foreach ($params as $key => $val) {
					$url .= '/'.urlencode($key).'/'.urlencode($val);
				}
			} else {
				foreach ($params as $key => $val) {
					$url .= '&'.urlencode($key).'='.urlencode($val);
				}
			}
		}
		return $url;
	}
	
	/**
	 * returns current URL
	 * @param mixed params false if no parameter, true if all current parameters, array to submit other parameters
	 */
	public function thisUrl($params=false) {
		if (is_array($params)) {
			return self::getUrl($this->controller, $this->action, $params);
		} else {
			return self::getUrl($this->controller, $this->action, $params?$this->request:false);
		}
	}
	
	/**
	 * return navigation menu
	 */
	public function menu($id='pages') {
    	$str = '<ul id="'.$id.'">';
		foreach ($GLOBALS['config'][$id] as $key => $url) {
			$arr = explode('/',$url);
			$arr[0] = ucfirst($arr[0]);
			if (!$arr[1]) {
				$arr[1] = 'main';
			}
			$str .= '<li';
			if ($this->controller == $arr[0] && $this->action == $arr[1]) {
				$str .= ' class="active"';
			}
			$str .= '><a href="'.FrontController::getUrl($url).'">'.$key.'</a></li>';
		}
    	$str .= '</ul>';
    	return $str;
    }
    
    /**
     * send error code (http header) to robots
     */
    protected function _sendErrorCodeToRobots($code) {
		if (preg_match('/'.APP_ROBOT_AGENT.'/', $_SERVER['HTTP_USER_AGENT'])) {
			header("Status : 404 Not Found");
			header("HTTP/1.1 404 Not Found");
		}
    }
    
    // ---- APPLICATION VARIABLES ------------------------------------------------
	
	/**
	 * get a persistent variable
	 * try first from $_REQUEST, then look into session variables
	 * if found in $_REQUEST, save it in session for later use
	 */
	public function sessionVariable($key) {
		if (isset($this->request[$key])) {
			$this->setSessionVariable($key, $this->request[$key]);
		}
		return $this->getSessionVariable($key);
	}
	
	/**
	 * set a session variable
	 */
	public function setSessionVariable($key, $value) {
		$_SESSION['appVariables'][$key] = $value;
	}
	
	/**
	 * set a session variable default's value
	 */
	public function setSessionDefault($key, $default) {
		if (!isset($_SESSION['appVariables'][$key])) {
			$_SESSION['appVariables'][$key] = $default;
		}
	}
	
	/**
	 * get a session variable
	 */
	public function getSessionVariable($key) {
		if (!isset($_SESSION['appVariables'][$key])) {
			return NULL;
		} else {
			return $_SESSION['appVariables'][$key]; 
		}
	}
	
	/**
	 * reset a session variable
	 */
	public function cleanSessionVariable($keys) {
		$keys = StringHelper::mixedToArray($keys);
		foreach($keys as $key) {
			if (isset($_SESSION['appVariables'][$key])) {
				unset($_SESSION['appVariables'][$key]);
			}
		}
	}
	
	// ---- AUTOLOAD setup -------------------------------------------------------
	
	/**
	 * generic method loading classes
	 */
	public function _load($file, $type) {
		self::log_front("loading $file [$type]");
		foreach($this->autoPath[$type] as $path) {
			if (file_exists($path.$file)) {
				self::log_front("-> Yes in $path");
				include_once($path.$file);
				return true;
			} else {
				self::log_front("!> No $file in $path");
			}
		}
		return false;
	}
	
	/**
	 * static method loading any type of class definition
	 * check all accessible folder that may contain the class definition
	 * and include it if necessary
	 */
	protected static function load($class, $type) {
		$file = StringHelper::camelToFlat($class);
		if ($idx = strrpos($file, '_'.$type)) {
			$file = substr($file, 0, $idx);
		}
		$file .= '.php';
		if (!class_exists($class)) {
			$obj = self::getInstance();
			if (!$obj->_load($file, $type)) {
				return false;
			}
		}
		return $class;
	}
	
	/**
	 * load core class
	 */
	public static function loadClass($class) {
		return self::load($class, 'class');
	}
	
	/**
	 * load helper class
	 */
	public static function loadHelper($class) {
		return self::load($class, 'helper');
	}
	
	/**
	 * load a model class
	 */
	public static function loadModel($class) {
		return self::load($class, 'model');
	}
	
	/**
	 * load a controller class
	 */
	public static function loadController($class) {
		return self::load($class, 'controller');
	}
	
	/**
	 * load a view class
	 */
	public static function loadView($class) {
		return self::load($class, 'view');
	}
	
	/**
	 * autoload class
	 * @parameter would require the type of class to be said in there
	 */
	private static function autoLoad($name) {
		$str = StringHelper::camelToFlat($name);
		$type = 'class';
		$sep = strrpos($str,'_');
		if ($sep) {
			$class = substr($str, 0, $sep);
			$type = substr($str, $sep+1);
			switch ($type){
				case 'helper':
				case 'class':
				case 'controller':
				case 'model':
				case 'view':
					// valid type
					break;
				default:
					// invalid type, must be a core class
					$type = 'class';
					break;
			}
		}
		return self::load($name, $type);
	}
	
	// ---- LOGGING and DEBUGGING ------------------------------------------------
	
	public static function log_front($str) {
		self::log_any($GLOBALS['config']['log_front'], 'front', $str);
	}
	
	public static function log_debug($str) {
		self::log_any($GLOBALS['config']['log_debug'], 'debug', $str);
	}
	
	public static function log_message($str) {
		self::log_any($GLOBALS['config']['log_message'], 'message', $str);
	}
	
	public static function log_warn($str) {
		self::log_any($GLOBALS['config']['log_warn'], 'warning', $str);
	}
	
	public static function log_error($str) {
		self::log_any($GLOBALS['config']['log_error'], 'error', $str);
	}
	
	public static function log_core_debug($str) {
		self::log_any($GLOBALS['config']['log_core'], 'error', $str);
	}
	
	public static function log_any($mode, $head, $str) {
		switch ($mode) {
		case 1:
			$arr = explode("\n", trim($str));
			foreach ($arr as $s) {
				error_log($GLOBALS['config']['log_signature']." $head : $s");
			}
			break;
		case 2:
			echo VarStr::html($GLOBALS['config']['log_signature']." $head : ".nl2br($str));
			break;
		}
	}
}

class FC extends FrontController
{
	
}

class AppException extends Exception {

    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0, Exception $previous = null) {
        // make sure everything is assigned properly
        parent::__construct($message, $code, $previous);
    }

    // custom string representation of object
    public function __toString() {
        echo "<pre>".parent::__toString()."</pre>";
        return __CLASS__;
    }

}