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
 * Messaging
 * 
 * manages session messages, alerts, error messages
 */
class MessagingHelper {

	public function __construct() {
		if (!isset($_SESSION['appMessage'])) {
			$_SESSION['appMessage'] = array();
		}
	}

	public function addMessage($str) {
		if (!array_search($str, $_SESSION['appMessage'])) {
			$_SESSION['appMessage'][] = $str;
		}
	}
	
	public function hasMessage() {
		return count($_SESSION['appMessage']);
	}
	
	public function getMessages(&$isError, $html=true, $clean=true) {
		$str = '';
		foreach($_SESSION['appMessage'] as $mess) {
			if (preg_match('/^ERROR:/i', $mess)) {
				$isError = true;
				$mess = substr($mess,6);
			}
			if ($str) {
				$str .= "\n";
			}
			$str .= $mess;
		}
		if ($clean) {
			$this->cleanMessages();
		}
		if ($html) {
			return VarTxt::html($str);
		} else {
			return $str;
		}
	}
	
	public function cleanMessages() {
		unset($_SESSION['appMessage']);
	}
}