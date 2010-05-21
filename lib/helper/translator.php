<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @since 0.3
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * Translator Helper
 * 
 * translates everything
 * @todo test it all
 */
class TranslatorHelper {

	protected $langDefault;
	protected $langUser;

	public function __construct() {
		$this->langDefault = $GLOBALS['config']['lang']['default'];
		$this->langUser = $GLOBALS['config']['lang']['user'];
	}
	
	public function loadLangConfig() {
		include_once(APP_LANGUAGE_PATH.$this->langDefault.'/config.php');
		include_once(APP_LANGUAGE_PATH.$this->langUser.'/config.php');
	}
	
	public function loadLangFile($file) {
		if (file_exists(APP_LANGUAGE_PATH.$this->langUser.'/'.$file.'.php')) {
			include_once(APP_LANGUAGE_PATH.$this->langUser.'/'.$file.'.php');
		}
		if (file_exists(APP_LANGUAGE_PATH.$this->langDefault.'/'.$file.'.php')) {
			include_once(APP_LANGUAGE_PATH.$this->langDefault.'/'.$file.'.php');
		}
	}
	
	public function getTranslation($label, $section, $field='') {
		if (!array_key_exists($section, $GLOBALS['lang'][$this->langDefault])) {
			return $label;
		}
		if (array_key_exists($label, $GLOBALS['lang'][$this->langUser][$section])) {
			if (is_array($GLOBALS['lang'][$this->langUser][$section][$label])) {
				return $GLOBALS['lang'][$this->langUser][$section][$label][$field];
			} else {
				return $GLOBALS['lang'][$this->langUser][$section][$label];
			}
		} else if (array_key_exists($label, $GLOBALS['lang'][$this->langDefault][$section])) {
			if (is_array($GLOBALS['lang'][$this->langDefault][$section][$label])) {
				return $GLOBALS['lang'][$this->langDefault][$section][$label][$field];
			} else {
				return $GLOBALS['lang'][$this->langDefault][$section][$label];
			}
		} else {
			return $label;
		}
	}
}