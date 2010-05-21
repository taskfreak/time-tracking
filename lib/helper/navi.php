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
 * Navi
 * 
 * manages referers, redirection and provides URL manipulation
 */
class NaviHelper {

	public function __construct() {
		if (!isset($_SESSION['appReferrers'])) {
			$_SESSION['appReferrers'] = array();
		}
	}
	
	// ---- REFERERS handling ----------------------------------------------------
	
	public function autoReferrer($historic=false) {
	
		$url = $_SERVER['REQUEST_URI'];
		if ($historic) {
			$url = substr($_SERVER['HTTP_REFERER'],strlen(APP_WWW_URL));
		} else {		
			/*
			$url = $_SERVER['PHP_SELF']
				.(($_SERVER['QUERY_STRING'])?('?'.$_SERVER['QUERY_STRING']):'');
			*/
		}
		
		if (!preg_match('/login/',$url)) {
			return $url;
		} else {
			// skip login, register, logout and password reminder pages
			return false;
		}
	}
	
	public function setReferrer($url='') {
		$_SESSION['appReferrers'] = array();
		if ($url) {
			$_SESSION['appReferrers'][] = StringHelper::camelToFlat($url);
		}
		
	}
		
	public function addReferrer($url='', $historic=false) {
		if (!$url) {
			if (!$url = $this->autoReferrer($historic)) {
				return false;
			}
		}
		$url = StringHelper::camelToFlat($url);
		
		$arr = $_SESSION['appReferrers']; // copy
	
		// search for previous entry with same url
		while ($tmp = @array_pop($arr)) {
			if ($tmp == $url) {
				// been to this page, need to clean referrers
				$_SESSION['appReferrers'] = $arr;
				break;	
			}
		}
		
		// add url to referrer
		$_SESSION['appReferrers'][] = $url;
		return true;
		
	}
	
	public function addAppReferrer($params=NULL) {
		$fc = FrontController::getInstance();
		if (!empty($params)) {
			$params = StringHelper::mixedToArray($params);
			$arr = array();
			foreach($params as $p) {
				if (array_key_exists($p, $fc->request)) {
					$arr[$p] = $fc->request[$p];
				}
			}
			$this->addReferrer($fc->thisUrl($arr));
		} else {
			$this->addReferrer($fc->thisUrl());
		}
	}
	
	public function getReferrer($skip=true, $clean=false) {
		$url = './';
		$arr = $_SESSION['appReferrers']; // copy
		if ($clean) {
			$arr =& $_SESSION['appReferrers']; // point
		}
		if (!is_array($arr)) {
			// normally not needed, but seems to happen for some reason
			$arr = array();
		}
		FrontController::log_debug('Refs : '.implode(', ',$arr));
		if ($skip) {
			array_pop($arr); // skip last one
		}
		while (count($arr)) {
			if ($tmp = array_pop($arr)) {
				$url = $tmp;
				break;
			}
		}
		return $url;
	}
	
	public function naturalReferrer($default, $avoid='') {
		$ref = $default;
		if ($_REQUEST['ref']) {
			$ref = $_REQUEST['ref'];
		} else if ($_SERVER['HTTP_REFERER']) {
			$ref = $_SERVER['HTTP_REFERER'];
		}
		if ($avoid && preg_match("/$avoid/i",$ref)) {
			$ref = $default;
		}
		return $ref;
	}
	
	public function delReferrer() {
		array_pop($_SESSION['appReferrers']);
	}
	
	public function printReferrers() {
		foreach ($_SESSION['appReferrers'] as $url) {
			echo ' &gt; <a href="'.$url.'">'.$url.'</a>';
		}
	}
	
	// ---- URLs and REDIRECTION -------------------------------------------------
	
	public function autoRedirect($message='') {
		$this->redirect($this->getReferrer(true, true), $message);
	}
	
	public static function redirect($url,$message='',$forceRef=false)
    {
    	if (@constant('TZN_TRANS_ID')) {
			if (session_id() && (!preg_match('/'.session_id().'/i',$url))) {
				$url = $this->concatUrl($url,session_name()
					.'='.session_id());
			}
    	}
    	if ($message) {
    		$message = preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is"
			,"", $message); 
			$message = preg_replace("/<\/?(div|span|iframe|frame|input|"
				."textarea|script|style|applet|object|embed|form)[^>]*>/is"
				,"", $message);
			if (@constant('TZN_TRANS_STATUS')) {
				$fc = FrontController::getInstance();
				$fc->addMessage($message);
			} else {
				$url = self::concatUrl($url,'tznMessage='.urlencode($message));
			}
    	}
		if ($forceRef) {
			$url = self::concatUrl($url,'ref='.rawurlencode($_SERVER['REQUEST_URI']));
		}
    	header("Location: ".str_replace('&amp;','&',$url));
    	exit;
    }
    
    public static function concatUrl($url,$param)
    {
    	// hash
    	$hash = '';
		if ($pos = strpos($url,'#')) {
			$hash = substr($url,$pos);
			$url = substr($url,0,$pos);
		}
		if ($pos = strpos($param,'#')) {
			$hash = substr($param,$pos);
		}
		// params
		$url = str_replace('&amp;','&',$url);
		if ($pos = strpos($url,'?')) {
			$arrParam = explode('=',$param);
			if (strpos($url,$arrParam[0].'=')) {
				// parameter already in url
				$strQuery = substr($url,$pos+1);
				$arrQuery = explode('&',$strQuery);
				$arrResult = array();
				$found = false;
				foreach ($arrQuery as $value) {
					if (preg_match('/^'.$arrParam[0].'=/', $value)) {
                        if ($arrParam[1]) {
                            // add only if has a value
    						$arrResult[] = $param;
                        }
						$found = true;
					} else {
						$arrResult[] = $value;
					}
				}
				if ($found) {
					$url = substr($url,0,$pos).'?'.implode('&',$arrResult);
				} else {
					$url .= '&'.$param;
				}
			} else {
				$url .= '&'.$param;
			}
    	} else {
    		$url .= '?'.$param;
    	}
    	return str_replace('&','&amp;',$url).$hash;
    }
    
}