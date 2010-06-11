<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.4
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Translator Helper
 * 
 * translates everything
 * @since 0.3
 * @todo test it all
 */
class TranslatorHelper {

	protected $langDefault;
	protected $langUser;

	public function __construct() {
		$this->langDefault = $GLOBALS['config']['lang']['default'];
		$this->langUser = $GLOBALS['config']['lang']['user'];
		$GLOBALS['lang'] = array();
	}
	
	public function loadLangConfig() {
		if (file_exists(APP_LANGUAGE_PATH.$this->langUser.'/config.php')) {
			include_once(APP_LANGUAGE_PATH.$this->langUser.'/config.php');
		} else {
			include_once(APP_LANGUAGE_PATH.$this->langDefault.'/config.php');
		}
	}
	
	public function loadLangFilesFromConfig() {
		foreach($GLOBALS['config']['lang']['files'] as $file => $path) {
			$full = $path.$this->langUser.'/'.$file;
			if (file_exists($full)) {
				include_once($full);
			} else {
				$full = $path.$this->langDefault.'/'.$file;
				if (file_exists($full)) {
					include_once($full);
				}
			}
		}
	}
	
	public function loadLangFile($file) {
		// try user language
		foreach ($GLOBALS['config']['path']['lang'] as $path) {
			if (file_exists(APP_INCLUDE_PATH.$path.$this->langUser.'/'.$file)) {
				include_once(APP_INCLUDE_PATH.$path.$this->langUser.'/'.$file);
				return true;
			}
		}
		// not found ? try default language
		foreach ($GLOBALS['config']['path']['lang'] as $path) {
			if (file_exists(APP_INCLUDE_PATH.$path.$this->langDefault.'/'.$file)) {
				include_once(APP_INCLUDE_PATH.$path.$this->langDefault.'/'.$file);
				return true;
			}
		}
		return false;
	}
	
	public static function getRawTranslation($section, $label, $field='') {
		if (!array_key_exists($section, $GLOBALS['lang'])) {
			FC::log_debug('can not find section '.$section);
			return str_replace('_',' ',$label);
		}
		if (array_key_exists($label, $GLOBALS['lang'][$section])) {
			if (is_array($GLOBALS['lang'][$section][$label])) {
				return $GLOBALS['lang'][$section][$label][$field];
			} else {
				return $GLOBALS['lang'][$section][$label];
			}
		} else if (array_key_exists($label, $GLOBALS['lang'][$section])) {
			if (is_array($GLOBALS['lang'][$section][$label])) {
				return $GLOBALS['lang'][$section][$label][$field];
			} else {
				return $GLOBALS['lang'][$section][$label];
			}
		} else {
			FC::log_debug('really can not find section '.$section);
			return str_replace('_',' ',$label);
		}
	}
	
	public static function getTranslation($section, $label='', $field='') {
		if (preg_match('/^\[([a-z_]+)\](.*)$/',$section, $matches)) {
			$section = $matches[1];
			$label = $matches[2];
		}
		$str = self::getRawTranslation($section, $label, $field);
		if ($GLOBALS['config']['lang']['ucfirst']) {
			return ucfirst($str);
		}
		return $str;
	}
	
}

class TR extends TranslatorHelper {

	public static function get($section, $label, $field='') {
		return self::getTranslation($section, $label, $field);
	}

	public static function html($section, $label, $field='') {
		return VarStr::html(self::getTranslation($section, $label, $field));
	}
	
	public static function phtml($section, $label, $field='') {
		echo self::html($section, $label, $field);
	}

}