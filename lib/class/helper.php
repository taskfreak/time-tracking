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
 * Helper
 * 
 * @since 0.1
 */
abstract class Helper {

	protected $obj;

	public function __construct($obj=null) {
		if (is_object($obj)) {
			$this->obj = $obj;
		}
	}
	
}