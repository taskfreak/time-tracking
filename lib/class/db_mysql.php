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
 * DbMysql
 * 
 * MySQL connector
 * @since 0.1
 */
class DbMysql implements DbEngine {

	private $host, $user, $pass, $base;
	
	protected $isConnected;
	protected $dblink;

	public function __construct($host, $user, $pass, $base) {
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->base = $base;
	}
	
	/**
	 * connect to mysql and select database
	 */
	public function connect() {
		$this->isConnected = false;
		
		if (@constant('APP_DB_PERMANENT')) {
			$this->dblink = @mysql_pconnect($this->host,$this->user,$this->pass);
		} else {
			$this->dblink = @mysql_connect($this->host,$this->user,$this->pass);
		}
		if (!$this->dblink) {
            return false;
		}
		if (!@mysql_select_db($this->base,$this->dblink)) {
            return false;
		}
		return true;
	}
	
	/**
	 * escape string for mySQL
	 */
	public function escapeString($str) {
		return mysql_real_escape_string($str,$this->dblink);
	}
	
	// ---- TRANSACTIONS ---------------------------------------------------------
	
	public function transactionBegin() {
		@mysql_query('SET AUTOCOMMIT=0',$this->dblink);
		$r = @mysql_query('BEGIN',$this->dblink);
    	// error_log('BEGIN : '.$r);
    	return $r;
    }
    
    public function transactionCommit() {
    	$r = @mysql_query('COMMIT',$this->dblink);
    	@mysql_query('SET AUTOCOMMIT=1',$this->dblink);
    	// error_log('COMMIT : '.$r);
    	return $r;
    }
    
	public function transactionRollBack() {
    	$r = @mysql_query('ROLLBACK',$this->dblink);
    	@mysql_query('SET AUTOCOMMIT=1',$this->dblink);
    	//error_log('ROLLBACK : '.$r);
    	return $r;
    }
    
    // ---- QUERIES -----------------------------------------------------------
    
    /**
     * SELECT and SHOW queries
     */
    public function querySelect($qry) {
		$r = @mysql_query($qry, $this->dblink);
		if (!$r) {
			// there's obviously an error
			return false;
		}
		$data = array();
		$i = 0;
		while ($row = mysql_fetch_assoc($r)) {
			$data[$i++] = $row;
		}
		return $data;
	}
	
	/**
	 * INSERT, REPLACE, UPDATE and DELETE queries
	 */
	public function queryAffect($qry) {
		if (!@mysql_query($qry,$this->dblink)) {
			// error_log('queryAffect (mysql) fails : '.$qry);
			return false;
		}
		$r = mysql_affected_rows($this->dblink);
		// error_log('queryAffect (mysql) returns : '.$r);
		if ($r == -1) {
			return false;
		} else {
			if ($r == 1) {
				if ($id = mysql_insert_id($this->dblink)) {
					return $id;
				}
			}
			return ($r)?$r:true;
		}
	}
    
    // ---- TABLE operations --------------------------------------------------
	
	public function getTable($table) {
		if ($result = @mysql_query('SHOW TABLES LIKE \''.$table.'\'')) {
			if ($row = mysql_fetch_row($result)) {
				if (strtolower($row[0]) == strtolower($table)) {
					return true;
				}
			}
		}
		return false;
	}
	
	public function getTables($table=null) {
        $arrTables = array();
        $sql = 'SHOW TABLES';
        if ($table) {
        	$sql .= ' LIKE \''.$table.'\'';
        }
        if ($result = @mysql_query($sql)) 
        {
            while($row = mysql_fetch_row($result)) {
                $arrTables[] = $row[0];
            }
        }
        return $arrTables;
    }
	
	// ---- ERROR reporting ------------------------------------------------------
	
	public function getErrorNo() {
		return mysql_errno();
	}
	
	public function getErrorMsg() {
		return mysql_error();
	}
	
}