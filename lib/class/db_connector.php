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
 * Database Connector
 * 
 * @since 0.1
 */
class DbConnector { // extends HelpableSingleton
	
	protected $connector;
	protected $critical;
	protected $debug;
	
	protected $sqlQueryCount;

	public function __construct() {
		$this->debug = APP_DB_DEBUG;
		$this->critical = APP_DB_CRITICAL;
		$this->sqlQueryCount = 0;
	}
	
	// ---- Managing CONNECTION --------------------------------------------------
	
	/**
	 * get DB connection
	 */
	public static function getConnection($i=0) {
		// -TODO-
		// connections shouldn't be hold by the front controller,
		// but by this class instantiated as a singleton
		$fc = FrontController::getInstance();
		if (empty($fc->db[$i])) {
			throw new AppException('Connection #'.$i.' does not exists');
		}
		try {
			if ($fc->db[$i]->isConnected()) {
				return $fc->db[$i];
			}
		} catch (Exception $e) {
			throw new AppException('Connection #'.$i.' not established : '.$e->getMessage());
		}		
	}
	
	protected function connectTheConnector(DbEngine $obj) {
		$this->connector = $obj;
	}

	public function connect($host=APP_DB_HOST, $user=APP_DB_USER, $pass=APP_DB_PASS, $base=APP_DB_BASE) {
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
		// $this->reportError();
		// return false;
		echo 'Cannot connect to database "'.APP_DB_BASE.'" on host "'.APP_DB_HOST.'" with user "'.APP_DB_USER.'"';
		exit;
	}
	
	public function isConnected() {
		return is_a($this->connector, 'DbEngine');
	}
    
    public function setDbDebug($level) {
    	$this->debug = $level;
    }
    
    public function setDbCritical($mode=true) {
    	$this->critical = $mode;
    }
    
    // ---- QUERYING the Database ---------------------------------------------

    public static function query($qry, $dbi=0) {
    	$db = self::getConnection($dbi);
    	return $db->_query($qry);
    }
    protected function _query($qry) {
    	$this->sqlQueryCount++;
    	
    	switch ($this->debug) {
		case 4:
			FC::log_debug($qry);
			break;
		case 5:
			echo "<code>".$qry."</code><br/>";
			break;
		}
    	
		if (preg_match("/^(SELECT|SHOW)/i",ltrim($qry))) {
			$r = $this->connector->querySelect($qry);
		} else {
			$r = $this->connector->queryAffect($qry);
		}
		
		if ($r === false) {
			// error !
			$this->reportError($qry);
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
	
		if (empty($this->debug)) {
			return false;
		}
		
		// if critical, should at least show an error code in browser
		$debug = ($this->critical && $this->debug < 2)?2:$this->debug;

		$html = '';
		$str = 'DB Error #'.$this->connector->getErrorNo().' : '.$this->connector->getErrorMsg();
		switch ($debug) {
		case 2:
			echo '<p>DB Error #'.$this->connector->getErrorNo().'</p>';
		case 1:
			if ($qry) {
				FC::log_error($qry);
			}
		case 4:
			FC::log_error($str);
			break;
		case 3:
			if ($qry) {
				$htm = '<br /><code>'.htmlentities($qry).'</code>';
			}
		case 5:
			$htm = nl2br(htmlentities($str)).$htm;
			break;
		default:
			echo '<p>DB DEBUG level undefined</p>';
			break;
		}
		echo '<p>'.$htm.'</p>';
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
	public function escapeString($str);
	public function querySelect($qry);
	public function queryAffect($qry);
	public function getTable($table);
	public function getErrorNo();
	public function getErrorMsg();
}