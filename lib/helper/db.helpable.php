<?php
/**
 * Tzn Framework
 * 
 * @package tzn_deprecated
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.1
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * DbHelper
 * 
 * Database helper to give Models (or other classes) database capabilities
 */
class DbHelper extends Helpable {

	protected $obj;
	protected $db;
	protected $qry;
	
	protected $idx;
	protected $rows;
	
	public function __construct($obj) {
		parent::__construct();
		$this->obj = $obj;
		$this->db = DbConnector::getInstance();
		$this->qry = new DbQueryHelper();
	}
	
	// ----- INSERTion queries ---------------------------------------------------
	
	public function insert() {
		
	}
	
	public function replace() {
		
	}
	
	public function update() {
		// -TODO- what about files ?
	}
	
	// ----- DELETE query -------------------------------------------------------
	
	public function delete($filter) {
		// -TODO- what about files ?
	}
	
	
	// ----- LOAD queries --------------------------------------------------------
	
	/**
	 * build default SELECT query
	 */
	public function buildSelect() {
		$this->qry->select('*');
		// -TODO- only selected fields
		$this->qry->from($this->obj->table);
		// -TODO- JOIN for objects, and GROUP BY if necessary
	}
	
	/**
	 * load first item
	 */	
	public function load($filter) {
		$this->buildSelect();
		$this->qry->where($filter);
		$this->qry->limit(0,1); // only the first item
		if ($data = $this->db->query($this->qry->build())) {
			$this->obj->set($data[0]);
		}
	}
	
	/**
	 * load a list of items
	 */
	public function loadList() {
		$this->buildSelect();
		if ($data = $this->db->query($this->qry->build())) {
			$this->idx = 0;
			$this->rows = $data;
		}
	}
	
	public function count() {
		return count($this->rows);
	}
	
	public function next() {
		if (array_key_exists($this->idx, $this->rows)) {
			$this->obj->set($this->rows[$this->idx]);
			$this->idx++;
			return true;
		} else {	
			return false;
		}
	}
	
	/**
	 * set limit by giving a page size and page number
	 */
	public function page($size=10, $page=1) {
		if (!$size) {
			$this->qry->reset('limit');
		}
		if (!$page) {
			$page = 1;
		}
		$this->qry->limit(($page - 1) * $size, $size);	
	}
	
	// ---- MISCELLANEOUS --------------------------------------------------------
	
	public function setDbDebug($level) {
		$this->db->setDbDebug($level);
	}
	
	public function __call($name, $args) {
		if (method_exists($this->db, $name)) {
			return call_user_func_array(array($this->db, $name), $args);
		}
		if (method_exists($this->qry, $name)) {
			return call_user_func_array(array($this->qry, $name), $args);
		}
		throw new Exception('Unknown method '.$name.' in '.__CLASS__.', dbConnector or dbQueryHelper');
	}
	*/
	
}

/**
 * DbQueryHelper
 *
 * SQL SELECT Query Builder
 */
class DbQueryHelper {
	
	protected $sql;
	
	public function __construct() {
		$this->sql = array(
			'select' 	=> '',
			'from'		=> '',
			'join'		=> array(),
			'where'		=> '',
			'groupBy'	=> '',
			'orderBy'	=> '',
			'having'	=> '',
			'limit'		=> ''
		);
	}
	
	/**
	 * reset all or part of the SQL query
	 */
	public function reset($part='') {
		if ($part) {
			$this->sql[$part] = '';
		} else {
			foreach (array_keys($this->sql) as $key) {
				$this->sql[$key] = '';
			}
			$this->sql['join'] = array();
		}
		
	}
	
	/**
	 * add fields in SELECT
	 */
	public function select($field) {
		if ($field == '*') {
			$this->sql['select'] = '';
		} else if (is_array($field)) {
			$this->sql['select'] = implode(', ',$field);
		} else {
			$this->sql['select'] = $this->concatSQL($this->sql['select'], $field, ',');
		}
	}
	
	/**
	 * add FROM statement
	 */
	public function from($table) {
		if (is_array($table)) {
			$this->sql['from'] = $table[0].' AS '.$table[1];
		} else {
			$this->sql['from'] = $table;
		}
	}
	
	/**
	 * add any JOIN
	 */
	public function join($table, $on, $how = '') {
		$str = ($how?($how.' '):'').'JOIN';
		if (is_array($table)) {
			$str .= $table[0].' AS '.$table[1];
		} else {
			$str .= $table;
		}
		$str .= ' ON ';
		if (is_array($on)) {
			$str .= $on[0].'='.$on[1];
		} else {
			$str .= $on;
		}
		$this->sql['join'][] = $str;
	}
	
	/**
	 * add INNDER JOIN
	 */
	public function innerJoin() {
		$this->join($table, $on, 'INNER');
	}
	
	/**
	 * add LEFT JOIN
	 */
	public function leftJoin($table, $on) {
		$this->join($table, $on, 'LEFT');
	}
	
	/**
	 * add WHERE condition
	 */
	public function where($filter, $sep='AND') {
		$this->sql['where'] = $this->concatSQL($this->sql['where'], $filter, $sep);
	}
	
	/**
	 * add GROUP BY condition
	 */
	public function groupBy($filter, $sep=',') {
		$this->sql['groupBy'] = $this->concatSQL($this->sql['groupBy'], $filter, $sep);
	}
	
	/**
	 * add ORDER BY statement
	 */
	public function orderBy($filter, $sep=',') {
		$this->sql['orderBy'] = $this->concatSQL($this->sql['orderBy'], $filter, $sep);
	}
	
	/**
	 * add HAVING condition
	 */
	public function having($filter, $sep='AND') {
		$this->sql['having'] = $this->concatSQL($this->sql['having'], $filter, $sep);
	}
	
	/**
	 * add a LIMIT statement (only one statement allowed)
	 */
	public function limit($start=0, $length=1) {
		$this->sql['limit'] = $start.', '.$length;
	}
	
	/**
	 * Query builder routine 
	 */
	public function build() {
		if (!$this->sql['select']) {
			$this->sql['select'] = '*';
		}
		$sql = 'SELECT '.$this->sql['select']
			.' FROM '.$this->sql['from'];
		foreach ($this->sql['join'] as $j) {
			$sql .= ' '.$j;
		}
		if ($this->sql['where']) {
			$sql .= ' WHERE '.$this->sql['where'];
		}
		if ($this->sql['groupBy']) {
			$sql .= ' GROUP BY '.$this->sql['groupBy'];
		}
		if ($this->sql['orderBy']) {
			$sql .= ' ORDER BY '.$this->sql['orderBy'];
		}
		if ($this->sql['having']) {
			$sql .= ' HAVING '.$this->sql['having'];
		}
		if ($this->sql['limit']) {
			$sql .= ' LIMIT '.$this->sql['limit'];
		}
		return $sql;
	}
	
	// --- MISCELLANEOUS ---------------------------------------------------------
	
	/**
	 * concat string for SQL
	 */
	protected function concatSQL($begin, $end, $separator = 'AND', $instruction = '') {
		$separator = ' '.trim($separator).' ';
		if (!empty($instruction)) {
			$instruction .= ' ';
		}
		if (empty($begin)) {
			if (empty($end)) {
				return false;
			} else {
				return $instruction.$end;
			}
		} else if (empty($end)) {
			return $instruction.$begin;
		} else {
			return $begin.$separator.$end;
		}
	}
	
}