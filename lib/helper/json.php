<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.5
 * @since 0.5
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * JsonHelper
 * 
 * JSON format helper to export/import data in JSON format
 */
class JsonHelper extends Helper {
	
	public function __construct($obj) {
		parent::__construct($obj);
	}
	
	public function export($method='html') {
		$arr = $this->obj->getFields();
		$data = array();
		foreach ($arr as $key => $type) {
			$data[$key] = $this->obj->$method($key);
		}
		return json_encode($data);
	}
}
