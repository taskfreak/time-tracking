<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.1
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Array Helper
 * 
 * common functions to manipulate arrays
 */
class ArrayHelper {

	public function __construct() {
	}
	
	public static function arrayTrim(&$arr)
	{
	    array_walk($arr,"_trim");
	}
}


function _trim(&$value)
{
    $value = trim($value);   
}