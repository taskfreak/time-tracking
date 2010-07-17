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
 * String Helper
 * 
 * common functions to manipulate strings
 */
class StringHelper {

	public function __construct() {
	}
	
	/**
	 * transforms a string from CamelCasing to flat_with_underscores
	 */
	public static function camelToFlat($str, $sep='_') {
		$str = preg_replace('/(?<=\\w)(?=[A-Z])/',"$sep$1", trim($str));
		return strtolower($str);
	}
	
	/**
	 * transforms a string from flat_with_underscores to CamelCasing
	 */
	public static function flatToCamel($str, $firstCap=false, $sep='_') {
		$arr = explode($sep,trim($str));
		$str = '';
		foreach($arr as $sep) {
			if ((!$str && $firstCap) || $str) {
				$str .= ucfirst($sep);
			} else {
				$str .= $sep;
			}
		}
		return $str;
	}
	
	/**
	 * gets any kind of arguments and returns an array
	 * (an array, a string containing values separated by commas, or many arguments)
	 */
	public static function mixedToArray() {
		$arg = func_get_arg(0);
		if (empty($arg)) {
			return array();
		} else if (is_array($arg)) {
			return $arg;
		} else if (func_num_args() == 1) {
			$arr = explode(',',$arg);
			array_walk($arr, 'trim');
			return $arr;
		} else {
			return func_get_args();
		}
	}
	
	/**
	 * generate random string
	 */
	public static function genRandom($len = APP_KEY_LENGTH, $strChars = APP_KEY_STRING)
	{
		$strCode = "";
		$intLenChars = strlen($strChars);
		for ( $i = 0; $i < $len; $i++ )	{
			$n = mt_rand(1, $intLenChars);
			$strCode .= substr($strChars, ($n-1), 1);
		}
		return $strCode;
	}
	
	/**
	 * convert string to XML friendly string (no space, no special caracters)
	 */
	public static function cleanStrip($str) {
		$str = trim($str);
		if ($str) {
			/*
			if (constant('APP_CHARSET') == 'UTF-8') {
				$str = utf8_decode($str);
			}
			*/
			$str = utf8_decode($str);
			$str = preg_replace(
				array('/[יטךכ]/','/[אבגה]/','/[לםמן]/','/[שתûü]/','/[צפער]/','/[ח‡]/','/[ \'\?\/\\&"]/'),
				array('e','a','i','u','o','c','-'),
				strtolower($str));
			$str = preg_replace('/[^a-z0-9\-]/','_',$str);
			$str = str_replace('---','-',$str);
			$str = trim($str,'_');
			$str = trim($str,'-');
		}
		return $str;
	}
}