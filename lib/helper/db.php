<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.5
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * DbHelper
 * 
 * Database helper to give Models (or other classes) database capabilities
 */
class DbHelper extends Helper implements Callable {

	protected $db;
	protected $qry;
	
	protected $idx;
	protected $rows;
	protected $total;
	
	protected $table;
	
	public function __construct($obj, $table) {
		parent::__construct($obj);
		$this->db = DbConnector::getConnection();
		$this->qry = new DbQueryHelper();
		$this->table = $table;
	}
	
	public static function factory() {
		return new DbHelper(null, '');
	}
	
	// ----- INSERTion queries ---------------------------------------------------
	
	public function save() {
		// check UID
		$uid = $this->obj->getUid();
		if (empty($uid)) {
			return $this->insert();
		} else {
			return $this->update();
		}
	}
	
	public function insert() {
		// set creation date
		if (!$this->_creationDate()) {
			// if no field creation date, update last change date
			$this->_lastChangeDate();
		}
		// -TODO- INSERT IGNORE INTO
		$this->obj->ignore($this->obj->dbUid());
		// INSERT
		$sql = 'INSERT INTO `'.$this->obj->dbTable().'` SET '.$this->fields();
		if ($id = $this->db->query($sql)) {
			$this->obj->setUid($id);
			return $id;
		}
		return false;
	}
	
	public function replace() {
		$sql = 'REPLACE `'.$this->obj->dbTable().'` SET '.$this->fields();
		return $this->db->query($sql);
	}
	
	public function update($filter='') {
		if (!$this->buildWhere($filter)) {
			return false;
		}
		// update last change date (if field is defined)
		$this->_lastChangeDate();
		// UPDATE
		$sql = 'UPDATE `'.$this->obj->dbTable().'` SET '.$this->fields();
		$sql .= ' WHERE '.$filter;
		return $this->db->query($sql);
	}
	
	protected function fields() {
		$fields = $this->obj->getFields();
		$arr = '';
		foreach ($fields as $key => $type) {
			if (preg_match('/^OBJ/',$type)) {
				$arr[] = "`${key}_id`=".$this->sql($key.'::id');
			} else {
				$arr[] = "`$key`=".$this->sql($key);
			}
		}
		return implode(', ',$arr);
	}
	
	public function sql($key) {
		$val = $this->obj->get($key);
		return $this->sqlValue($val);
	}
	
	public function sqlValue($val) {
		if (is_integer($val)) {
			return $val;
		} else if (empty($val)) {
			return "''";
		} else {
			return "'".$this->db->escapeString($val)."'";
		}
	}
	
	// ----- DELETE query -------------------------------------------------------
	
	/**
	 * delete entry
	 * @param string $filter if not set, will delete by UID
	 * @todo delete files from IMG and DOC fields
	 */
	public function delete($filter='') {
		if (!$this->buildWhere($filter)) {
			return false;
		}
		$sql = 'DELETE FROM `'.$this->obj->dbTable().'`';
		$sql .= ' WHERE '.$filter;
		return $this->db->query($sql);
	}
	
	
	// ----- LOAD queries --------------------------------------------------------
	
	/**
	 * load one item
	 * @param mixed filter either an array (key/value), a string, or nothing if you want to load by ID
	 * @param boolean $auto use automatic query (set to false if query has been generated before)
	 */
	public function load($filter='', $auto=true) {
		if ($auto) {
			$this->buildSelect();
			$this->buildFrom();
		}
		if ($this->buildWhere($filter)) {
			$this->qry->where($filter);
		} else {
			$this->obj->setUid(0);
			return false;
		}
		$this->qry->limit(0,1); // only the first item
		if ($data = $this->db->query($this->qry->build())) {
			if (!empty($data[0])) {
				$this->obj->set($data[0]);
				return true;
			}
		}
		$this->obj->setUid(0);
		return false;
	}
		
	/**
	 * load a list of items
	 */
	public function loadList($auto=true) {
		if ($auto) {
			$this->buildSelect();
			$this->buildFrom();
			$this->obj->all();
		}
		if ($data = $this->db->query($this->qry->build())) {
			$this->idx = 0;
			$this->total = 0;
			$this->rows = $data;
			if ($this->doesLimit()) {
				$res = $this->db->query($this->qry->build(true));
				if (count($res) == 1) {
					// simple result with one row
					$this->total = intval($res[0]['total']);
				} else {
					// multiple rows, probably because of a group by
					$this->total = count($res);
				}
			} else {
				$this->total = count($this->rows);
			}
			return $this->total;
		}
		return false;
	}
	
	/**
	 * query database and return result set directly
	 */
	public function loadRaw() {
		return $this->db->query($this->qry->build());
	}
	
	public function count() {
		return count($this->rows);
	}
	
	public function total() {
		return $this->total;
	}
	
	public function next() {
		if (array_key_exists($this->idx, $this->rows)) {
			if (is_a($this->obj, 'Model')) {
				$this->obj->set($this->rows[$this->idx]);
			}
			$this->idx++;
			return true;
		} else {
			// $this->obj->resetProperties();
			return false;
		}
	}
	
	public function reset() {
		$this->rows = array();
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
	
	// ---- TABLE & FIELDS MANIPULATION ------------------------------------------
	
	/**
	 * Get database table
	 * @todo add DB table prefix
	 */
	public function dbTable($table='') {
		if (empty($table)) {
			$table = $this->table;
		}
		return $this->qry->prefixTable($table);
	}
	
	/**
	 * returns a SQL statement table.field = value
	 */
	public function dbField($field, $value='') {
		$str = $this->dbTable().'.'.$field;
		if ($value) {
			$str .= '='.$this->sqlValue($value);
		}
		return $str;
	}
	
	/**
	 *
	 */
	public function dbUid($ext=false) {
		if ($ext) {
			return $this->dbField($this->obj->proUid());
		} else {
			return $this->obj->proUid();
		}
	}
	
	/**
	 * get SQL formatted UID
	 */
	public function sqlUid() {
		return $this->sql($this->obj->proUid());
	}
	
	// ---- MISC QUERIES ---------------------------------------------------------
	
	/**
	 * check if value already exists in table
	 */
	public function findOccurences($key, $val) {
		$sql = 'SELECT COUNT(*) AS cnt FROM `'.$this->obj->dbTable().'`'
			." WHERE `$key`='$val'";
		if ($data = $this->db->query($sql)) {
			if (!empty($data[0])) {
				return intval($data[0]->cnt);
			}
		}
		return 0;
	}
	
	// ---- QUERY BUILDER Methods ------------------------------------------------
	
	/**
	 * build default SELECT query
	 * @todo might not want to reset query every time
	 */
	public function buildSelect() {
		// reset query -TODO- not if in cache
		$this->qry->reset('select,from,join');
		// get list of fields to be selected
		$select = $this->buildFields();
		// add SELECT clause
		$this->qry->select($select);
	}
	
	/**
	 * build FROM clause
	 */
	public function buildFrom() {
		// add FROM clause
		$this->qry->from($this->dbTable());
	}
	
	/**
	 * build SELECT and JOIN clauses
	 */
	public function buildFields($nested='') {
		$select = array();
		$arr = $this->obj->getFields();
		foreach ($arr as $key => $type) {
			if ($this->obj->isObjectProperty($key, $class, $nkey)) {
				$obj = new $class;
				$obj->connectDb();
				$select = array_merge($select, $obj->buildFields($nkey));
				// add table join
				$this->qry->leftJoin($obj->dbTable(), array($this->dbField($key.'_id'),$obj->dbUid(true)));
			} else if ($nested) {
				// add joined table field to select
				$select[] = '`'.$nested.'`.`'.$key.'` AS '.$nested.'__'.$key;
			} else if ($key == 'id') {
				// aah IDs...
				$select[] = $this->dbUid(true);
			} else {
				// add field to select
				$select[] = '`'.$key.'`';
			}
		}
		return $select;
	}
	
	/**
	 * build WHERE clause based on filter
	 */
	public function buildWhere(&$filter) {
		if (is_array($filter)) {
			// filter 
			$filter = '`'.$filter[0]."`='".$filter[1]."'";
		} else if (preg_match('/^[a-zA-Z0-9\_]+$/',$filter)) {
			// filter on a field already set in DB
			$filter .= "=".$this->sql($filter);
		} else if (empty($filter)) {
			// nothing set, try on ID
			if ($id = $this->obj->getUid()) {
				$filter = $this->obj->dbUid($this->qry->doesJoin())."=".$this->obj->sqlUid();	
			} else {
				// not filter and no ID provided, don't even try
				$filter = '';
				return false;
			}
		}
		// overwise, consider param as a ready set where statement
		return true;
	}
	
	// ---- MISCELLANEOUS --------------------------------------------------------
	
	protected function _creationDate() {
		$fields = $this->obj->getFields();
		if (array_key_exists('creation_date', $fields)) {
			$this->obj->set('creation_date',APP_SQL_NOW);
			return true;
		}
		return false;
	}
	
	protected function _lastChangeDate() {
		$fields = $this->obj->getFields();
		if (array_key_exists('last_change_date', $fields)) {
			$this->obj->set('last_change_date',APP_SQL_NOW);
			return true;
		}
		return false;
	}
	
	public function __call($name, $args) {
		$arr = array('db','qry');
		foreach ($arr as $class) {
			if (method_exists($this->$class, $name)) {
				return call_user_func_array(array($this->$class, $name), $args);
			}
		}
		throw new AppException('Unknown method '.$name.' in '.__CLASS__.', '.implode(', ',$arr));
	}
	
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
		if (empty($field)) {
			return false;
		} else if ($field == '*') {
			$this->sql['select'] = '';
		} else {
			$field = StringHelper::mixedToArray($field);
			$this->sql['select'] = (($this->sql['select'])?($this->sql['select'].','):'').implode(',', $field);
		}
	}
	
	/**
	 * add FROM statement
	 */
	public function from($table) {
		if (is_array($table)) {
			$this->sql['from'] = '`'.$this->prefixTable($table[0]).'` AS '.$table[1];
		} else {
			$this->sql['from'] = '`'.$this->prefixTable($table).'`';
		}
	}
	
	/**
	 * add any JOIN
	 */
	public function join($table, $on, $how = '') {
		$str = ($how?($how.' '):'').'JOIN ';
		if (is_array($table)) {
			$str .= '`'.$this->prefixTable($table[0]).'` AS '.$table[1];
		} else {
			$str .= $this->prefixTable($table);
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
	 * add INNER JOIN
	 */
	public function innerJoin($table, $on) {
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
		if ($length == 0) {
			return false;
		}
		$this->sql['limit'] = $start.', '.$length;
	}
	
	/**
	 * Query builder routine 
	 * @param boolean $total will query for total number of rows when set to true
	 */
	public function build($total=false) {
		$sql = 'SELECT ';
		if ($total) {
			$sql .= 'COUNT(*) AS total';
			/*
			if ($this->doesJoin()) {
				$sql .= 'COUNT(DISTINCT '.$this->sql['from'].'.id) AS total';
			} else {
				$sql .= 'COUNT(*) AS total';
			}
			*/
		} else {
			if (!$this->sql['select']) {
				$this->sql['select'] = '*';
			}
			$sql .= $this->sql['select'];
		}
		$sql .= ' FROM '.$this->sql['from'];
		foreach ($this->sql['join'] as $j) {
			$sql .= ' '.$j;
		}
		if ($this->sql['where']) {
			$sql .= ' WHERE '.$this->sql['where'];
		}
		if ($this->sql['groupBy']) {
			$sql .= ' GROUP BY '.$this->sql['groupBy'];
		}
		if ($this->sql['having']) {
			$sql .= ' HAVING '.$this->sql['having'];
		}
		if ($this->sql['orderBy']) {
			$sql .= ' ORDER BY '.$this->sql['orderBy'];
		}
		if ($this->sql['limit'] && !$total) {
			$sql .= ' LIMIT '.$this->sql['limit'];
		}
		return $sql;
	}
	
	// --- MISCELLANEOUS ---------------------------------------------------------
	
	/**
	 * add prefix to table name
	 */
	public function prefixTable($table) {
		if (defined('APP_DB_PREFIX') && APP_DB_PREFIX) {
			if (preg_match('/^'.APP_DB_PREFIX.'_/', $table)) {
				return $table;
			} else {
				return APP_DB_PREFIX.'_'.$table;
			}
		} else {
			return $table;
		}
	}
	
	/**
	 * check if joigning multi tables
	 */
	public function doesJoin() {
		return empty($this->sql['join'])?false:true;
	}
	
	/**
	 * check if limit has been set
	 */
	public function doesLimit() {
		return empty($this->sql['limit'])?false:true;
	}
	
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
	
	/**
	 * build condition to include in a WHERE clause
	 * @param string param the value searched. Can contain wildcards.
	 */
	public static function parseSearch($param) {
		if ($param = trim($param)) {
			if ($param == '*') {
				return false;
			}
			if (preg_match('/^".*"$/i',$param)) {
				$param = '%'.str_replace('"','',$param).'%';
			} else if (preg_match('/\*/',$param)) {
				$param = str_replace('*','%',$param);
			} else {
				$param = '%'.str_replace(' ','%',$param).'%';
			}
		} else {
			return false;
		}
		$db = DbConnector::getConnection();
		return $db->escapeString($param);
	}
	
}