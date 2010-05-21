<?php
/**
 * Tzn Framework
 * 
 * @package tzn_deprecated
 * @author Stan Ozier <framework@tirzen.com>
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Database Connector
 * 
 * singleton database connector
 */
class DbConnector extends HelpableSingleton {

	private static $instance;
	
	protected $connector;
	protected $critical;
	protected $debug;
	
	protected $sqlQueryCount;

	private function __construct() {
		$this->debug = APP_DB_DEBUG;
		$this->critical = APP_DB_CRITICAL;
		$this->sqlQueryCount = 0;
		$this->connector = array();
	}
	
	/**
	 * the getInstance() method returns a single instance of the object
	 */
	public static function getInstance() {
		if(!isset(self::$instance)){
			$object= __CLASS__;
			self::$instance=new $object;
		}
		return self::$instance;
	}
	
	// ---- Managing CONNECTION --------------------------------------------------
	
	/**
	 * get DB connection
	 */
	public static function getConnection($i=0) {
		$db = self::getInstance();
		if (empty($db[$i])) {
			throw new AppException('Connection #'.$i.' does not exists');
		}
		try {
			if ($fc->db[$i]->isConnected()) {
				return $obj->db[$i];
			}
		} catch (Exception $e) {
			throw new AppException('Connection #'.$i.' not established : '.$e->getMessage());
		}		
	}
	
	protected function connectTheConnector(DbEngine $obj) {
		$this->connector = $obj;
	}

	public function connect($host=APP_DB_HOST, $user=APP_DB_USER, $pass=APP_DB_PASS, $base=APP_DB_BASE) {
		// -TODO- 
		if ($this->connector) {
			return true;
		}
		// not connected yet, just do it
		$dbc = 'Db'.ucfirst(APP_DB_CONNECTOR);
		$con = new $dbc($host, $user, $pass, $base);
		if ($con->connect()) {
			$this->connectTheConnector($con);
			return true;
		}
		// reaching this point means an error as occured
		$this->reportError();
		return false;
	}
	
	public function isConnected() {
		return is_a($this->connector, 'DbEngine');
	}
    
    public function setDbDebug($level) {
    	$this->debug = $level;
    }
    
    // ---- QUERYING the Database ---------------------------------------------

    public static function query($qry, $dbi=0) {
    	$db = self::getConnection($dbi);
    	return $db->_query($qry);
    }
    protected function _query($qry) {
    	$this->sqlQueryCount++;
    	
    	switch ($this->debug) {
		case 3:
			FC::log_debug($qry);
			break;
		case 4:
			echo "<code>".$qry."</code><br/>";
			break;
		}
    	
		if (preg_match("/^(SELECT|SHOW)/i",ltrim($qry))) {
			$r = $this->connector->querySelect($qry);
		} else {
			$r = $this->connector->queryAffect($qry, $this->debug, $this->critical);
		}
		
		if ($r === false) {
			// error !
			$this->reportError();
		}
		
		$this->setDbDebug(APP_DB_DEBUG); // back to normal debug level
		
		return $r;
    }
    
    public function transactionQueries($arrSql) {
    	if (!is_array($arrSql)) {
    		if (empty($arrSql)) {
    			return false;
    		}
    		$arrSql = array($arrSql);
    	}
    	if (!$this->transactionBegin()) {
    		FC::log_warn('can not begin transaction');
    	}
    	$ok = true;
    	foreach($arrSql as $sql) {
    		if (!$this->query($sql)) {
    			$ok = false;
    			break;
    		}
    	}
    	if ($ok) {
    		return $this->transactionCommit();
    	} else {
    		$this->transactionRollBack();
    		return false;
    	}
    }

	// ---- MISCELLANEOUS --------------------------------------------------------

	/**
	 * log or display DB errors in the browser
	 */
	public function reportError($qry='') {
		if (!$this->debug) {
			return false;
		}
		$str = 'DB Error #'.$this->connector->getErrorNo().' : '.$this->connector->getErrorMsg();
		switch ($this->debug) {
		case 1:
		case 3:
			FC::log_error($str);
			if ($qry) {
				FC::log_error($str);
			}
			break;
		case 2:
		case 4:
			echo '<p>'.nl2br(htmlentities($str));
			echo '<br /><code>'.htmlentities($qry).'</code></p>';
			break;
		default:
			echo '<p>DB DEBUG level undefined</p>';
			break;
		}
		if ($this->critical) {
			exit;
		}
	}
	
	/**
	 * pass on undefined method to selected connector
	 */
	public function __call($name, $args) {
		if ($this->connector) {
			if (method_exists($this->connector, $name)) {
				return call_user_func_array(array($this->connector, $name), $args);
			}
		}
		throw new AppException('Unknown method '.$name.' in '.__CLASS__.' or in the Db Connector ('.APP_DB_CONNECTOR.')');
	}
}

interface DbEngine {
	public function connect();
	public function querySelect($qry, $debug, $critical);
	public function queryAffect($qry, $debug, $critical);
	public function getTable($table);
	public function getErrorNo();
	public function getErrorMsg();
}