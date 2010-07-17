<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.4
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * AuthHelper
 * 
 * authentication system
 */
class AuthHelper extends Helper {

	protected $isLoggedIn;
	protected $isLoggingOut;
	
	protected $fc;
	protected $error;

	public function __construct($obj) {
		parent::__construct($obj);
		$this->fc = FrontController::getInstance();
		$this->error = '';
		$this->isLoggedIn = $this->isLoggingOut = false;
		
	}
	
	public function checkPassword($password) {
		$pass = $this->obj->get('password');
		$salt = $this->obj->get('salt');
        switch (APP_AUTH_PASSWORD_MODE) {
        case 1: 
            if ($pass == "") {
                $pass = crypt("", $salt);
            }    
            if (crypt($password, $salt) != $pass) {
                    // password invalid
                    $this->error = 'password_invalid';
                    $this->badAccess();
                    return false;
            }
            break;
        case 2:
            $sql = "ENCRYPT('$password','$salt')";
            if (!self::checkDbPass($sql, $pass)) {
            	// password not OK
	            $this->error = 'password_invalid';
        	    $this->badAccess();
            	return false; // error or password mismatch
            }
            break;
        case 3:
            $sql = "ENCODE('$password','$pass')";
            if (!self::checkDbPass($sql, $pass)) {
            	// password not OK
	            $this->error = 'password_invalid';
        	    $this->badAccess();
            	return false; // error or password mismatch
            }
            break;
		case 4:
			if (!$pass && !$password) {
				break;
			}
			$sql = "MD5('$password')";
            if (!self::checkDbPass($sql, $pass)) {
            	// password not OK
	            $this->error = 'password_invalid';
        	    $this->badAccess();
            	return false; // error or password mismatch
            }
            break;
        case 5:
        	if (!$pass && !$password) {
				break;
			}
			if ($_SESSION['challenge']) {
				$value = md5($pass.$_SESSION['challenge']);
				unset($_SESSION['challenge']);
				if ($value == $password) {
					break;
				}
			}
			$this->error = 'password_invalid';
            $this->badAccess();
            return false;
            break;
        default:
            for ($i = 0; $i < strlen($pass); $i += 2) { 
                $passBin .= chr(hexdec(substr($s,$i,2))); 
            }
            $iv = mcrypt_create_iv (mcrypt_get_iv_size (MCRYPT_3DES,
            	MCRYPT_MODE_ECB), MCRYPT_RAND);
            if (mcrypt_decrypt (MCRYPT_3DES, $pass, $passBin,
            	MCRYPT_MODE_ECB, $iv) == $password)
            {
                break;
            }
            $this->error = 'password_invalid';
            $this->badAccess();
            return false;
            break;
        }
        return true;
    }
    /**
    *
    */
    public function activateAccount($byUser=false) {
    	// update DB
    	$this->obj->set('activation','');
       	$this->obj->set('enabled',1);
       	if ($this->obj->isLoaded()) {
       		$this->obj->fields('enabled,activation');
	       	$this->obj->update();
       	}
       	
       	// send Emails
       	/* -TODO-
    	include CMS_INCLUDE_PATH.'language/'.$GLOBALS['objCms']->settings->get('default_language').'/system.php';
	    include_once(CMS_CLASS_PATH."pkg_com.php");
    
    	$objMessage = new EmailMessage();
    	
    	//email admin (if account activated by user)
    	if ($byUser) {
	    	if ($objMessage->loadByKey('description','sign_up_new')) {
				$bodyMessage = "\r\n\t"
					.$this->obj->getName()."\r\n\r\n"
					.CMS_WWW_URL.'admin/member.php?id='.$this->obj->getUid()."\r\n";
				if ($objMessage->send($bodyMessage, $this->obj->email)) {
					// well, ok, fine
				} else {
					$pErrorMessage = $GLOBALS['langSystemEmailStuff']['send_error'];
				}
			} else {
				$pErrorMessage = $GLOBALS['langSystemEmailStuff']['send_not_found'];
			}
    	}
	    
	    // email user
	    if ($this->obj->email) {
		    if ($objMessage->loadByKey('description','sign_up_confirmation')) {
		    	$bodyMessage = CMS_WWW_URL.'login.php?username='.$this->obj->username;
		    	if ($_POST['password1']) {
		    		$bodyMessage .= "\r\n\t"
		    			.$GLOBALS['langTznUser']['login_username'].': '
		    			.$this->obj->username."\r\n\t"
		    			.$GLOBALS['langTznUser']['login_password'].': '
		    			.$_POST['password1']."\r\n";
		    	}
				if ($objMessage->send($bodyMessage, $this->obj->email)) {
					// well, ok, fine
				} else {
					$this->_error['activation'] = $GLOBALS['langSystemEmailStuff']['send_error'];
				}
			} else {
				$this->_error['activation'] = $GLOBALS['langSystemEmailStuff']['send_not_found'];
			}
	    } else {
	    	$this->_error['activation'] = $GLOBALS['langSystemEmailStuff']['send_no_address'];
	    }
	    */
		
		return (empty($this->error));
    }
	/**
	* login and update DB
	*/
    protected function _activateLogin() {
    	$updTz = '';
    	if (isset($_REQUEST['appUserTimeZone'])) {
    		if ($this->obj->get('time_zone') != $_REQUEST['appUserTimeZone']) {
				$this->obj->set('time_zone',$_REQUEST['appUserTimeZone']);
				$updTz = ',time_zone';
			}
    	}
        // register session
        $_SESSION["appUserId"] = $this->obj->getUid();
        $_SESSION["appUserLastLogin"] = $this->obj->get('last_login_date');
		$_SESSION["appUserLastAddress"] = $this->obj->get('last_login_address');
		$this->updateSessionVariables();

        // update last login
        $this->obj->set('last_login_date',APP_SQL_NOW);
		$this->obj->set('last_login_address', $_SERVER['REMOTE_ADDR']);
		$_SESSION['appUserCurrentAddress'] = $this->obj->get('last_login_address');
		$this->obj->_isLoggedIn = true;
        $this->obj->set('bad_access',0);
		$this->obj->set('visits',$this->obj->get('visits')+1);
		$this->obj->fields('last_login_date,last_login_address,bad_access,visits'.$updTz);
        $this->obj->update();
    }
    
    /**
     * update sessions variables (on login and when updating own profile)
     */
    public function updateSessionVariables() {
    	$_SESSION["appUserName"] = $this->obj->get('username');
		$_SESSION["appUserTimeZone"] = $this->obj->get('time_zone');
        $this->obj->setupTimeZone();
    }
    
	/**
	* Verify username and password
	*/
    public function login($username, $password, $activation=false) {
    	$this->error = '';
        if ($username == '') {
            $this->error = 'username_required';
            return false;
        }
        if (APP_AUTH_FIELD == 'username' && (!VarUsr::sanitize($username, $error))) {
        	$this->error = $error;
        	return false;
        }
        $this->obj->set(APP_AUTH_FIELD,$username);
        if ($this->obj->load(APP_AUTH_FIELD)) {
        	$activ = $this->obj->get('activation');
            if (!$this->obj->get('enabled')) {
            	if (!$activation || $activation != $activ) {
	                //Account Disabled
    	            $this->error = 'account_disabled';
            	} else {
            		$this->error = 'account_not_active';
            	}
            }
            if (!$this->checkPassword($password)) {
                $this->error = 'password_invalid';
            } else if ($activation && $activation == $activ) {
            	// activate account
            	$this->activateAccount(true);
            }
			if (!empty($this->error)) {
				$this->badAccess();
				return false;
			}
        } else {
            $this->error = 'username_not_found';
            return false;
        }
        
    	$this->_activateLogin();
        return true;
    }
	/**
	*coming soon
	*/
    public function silentLogin($username, $password) {
    	$this->error = '';
        if ($username == '') {
            return false;
        }
        $this->obj->set(APP_AUTH_FIELD, $username);
        if ($this->obj->load(APP_AUTH_FIELD)) {
            if (!$this->obj->get('enabled')) {
                //Account Disabled
                $this->error = 'user_disabled';
            }
            if (!$this->checkPassword($password)) {
                $this->error = 'user_password_invalid';
            }
        } else {
            $this->error = 'user_name_not_found';
            return false;
        }
        return (empty($this->error));
    }
	/**
	* Is there a cookie for AutoLogin ??
	*/
	public function checkAutoLogin($forReal=true) {
		if (empty($_COOKIE['auto_login'])) {
			return false;
		}
        $arrVal = explode(":",$_COOKIE['auto_login']);
		$id = VarUid::sani($arrVal[0]);
		$salt = VarStr::sani($arrVal[1]);
		if (!$id || !$salt) {
			return false;
		}
        if ($this->obj->load($this->obj->dbUid(true)."='".$id
        	."' AND ".$this->obj->dbField('salt', $salt)." AND "
        	.$this->obj->dbField('auto_login',1)." AND ".$this->obj->dbField('enabled',1)) 
        ) {
			if (!$forReal) {
				return true;
			}
			setCookie('auto_login',$this->obj->getUid().":".$this->obj->get('salt')
				,time()+(3600*24*30));
            $this->_activateLogin();
            return true;
        } else {
            return false;
        }
	}
	/**
	* Activate AutoLogin
	*/
    public function setAutoLogin() {
        if (($this->obj->getUid()) && ($this->obj->get('salt'))) {
            setCookie('auto_login',$this->obj->getUid().":".$this->obj->get('salt')
            	,time()+(3600*24*30));
            $this->obj->set('auto_login','1');
            $this->obj->fields('auto_login');
            $this->obj->update();
            return true;
        }
        return false;
    }
	/**
	* De-activate AutoLogin
	*/
    public function resetAutoLogin() {
        if (($this->obj->getUid()) && ($this->obj->get('salt'))) {
            setCookie('auto_login');
            if ($this->obj->get('auto_login')) {
	            $this->obj->set('auto_login', 0);
    	        $this->obj->fields('auto_login');
           		$this->obj->update();
    	    }
            return true;
        }
        return false;
    }
	/**
	* Logout logs the user out (yes indeed)
	* Note: This will destroy the session, and not just the session data!
	*/
    public function logout() {
		$_SESSION = array();
		// If you want to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if (isset($_COOKIE[session_name()])) {
			setcookie(session_name(), '', time()-42000, '/');
		}
		// Finally, destroy the session.
		@session_destroy();
		// while you're at it, delete auto login
		$this->resetAutoLogin();
		// set internal variable
		$this->isLoggedIn = false;
        $this->isLoggingOut = true;
    }
	/**
	* check if user is logged in. Do not load from DB by default
	*/
    public function isLoggedIn($load=false) {
        
    	// --- login previously checked ---
    	
    	if (empty($_SESSION['appUserId'])) {
    		$_SESSION['appUserId'] = 0;
    	}
    
    	if ($this->isLoggedIn && !$this->isLoggingOut && $this->obj->getUid()) {
    		// user seems already logged in
    		if ($load) {
    			// load is requested
    			if ($this->obj->load() != $_SESSION['appUserId']) {
    				return false;
    			}
    		} else if ($this->obj->getUid() != $_SESSION['appUserId']) {
    			// ID in session and in object are different
    			return false;
    		}
    	}
    
    	// --- first login check ---
        if ($_SESSION['appUserId'] == 0 || $this->isLoggingOut) {
        	// invalid ID in session or currently logging out
            // $this->fc->addMessage('ERROR:#security_expired');
            return false;
            
        } else {
        
        	// login seems OK : initialize properties
        	$this->obj->set('id',$_SESSION['appUserId']);
			$this->obj->set('username', $_SESSION['appUserName']);
			
			// time zone
			$this->obj->set('time_zone', $_SESSION['appUserTimeZone']);
			$this->obj->setupTimeZone();
			
			$this->isLoggedIn = true;
	        
	        // check user IP
	        if ($_SESSION['appUserCurrentAddress'] && $_SERVER['REMOTE_ADDR'] != $_SESSION['appUserCurrentAddress']) {
	        	$this->fc->addMessage('ERROR:#security_ip '.$_SERVER['REMOTE_ADDR'].' / '.$_SESSION['appUserCurrentAddress']);
	        	return false;
	        }
	        
	        // check that User ID is same in cookie and in session
	        if ($load) {
	        	if ($this->obj->load() != $_SESSION['appUserId']) {
	        		$this->fc->addMessage('ERROR:#security_expired');
	        		return false;
	        	}
	        }
	        
	        // reset last session stats with previous login info
	        $this->obj->set('last_login_date', $_SESSION['appUserLastLogin']);
			$this->obj->set('last_login_address', $_SESSION['appUserLastAddress']);
			
            return true;
        }
    }

    /** 
    * All in one user login and access check function
    */
    public function checkLogin($canAutoLogin=true) {
    
    	if ($this->isLoggedIn(true)) {
    		return true;
    	} else {
    		if ($canAutoLogin && $this->checkAutoLogin()) {
    			// auto logged in
    			return true;
    		} else {
    			return false;
    		}
    	}
    	
	}
		
	/**
    * forgotten password? Try to get it back or generate new one
    * type can be 'username' or 'email'
	*/
    public function forgotPassword($key, $value) {
        if ($this->obj->get('salt') == "") {
            if (!$this->obj->loadByKey($key,$value)) {
                // user not found
                $this->error = $key."_not_found";
                return false;
            }
        }
        switch (APP_AUTH_PASSWORD_MODE) {
        case 1:
        case 2:
        case 4:
        case 5:
        	$this->generateNewSalt();
	        $newpass = StringHelper::genRandom(6,"123456789");
    	    $this->obj->setRawPassword($newpass, $this->obj->get('salt'));
        	$this->updatePassword();
        	return $newpass;
            break;
        case 3:
            $strSql = "SELECT DECODE(password, '".$this->obj->get('salt')
            	."') as pass FROM ".$this->_table
            	." WHERE ".$this->obj->dbUid()."='".$this->obj->getUid()."'";
            if ($rows = DbConnnector::query($strSql)) {
                if (!empty($rows[0])) {
                    return $rows[0]->pass;
                }
            }
            break;
        default:
            $iv = mcrypt_create_iv (mcrypt_get_iv_size (MCRYPT_3DES,
            	MCRYPT_MODE_ECB), MCRYPT_RAND);
            return mcrypt_decrypt (MCRYPT_3DES, $this->obj->get('salt'),
            	$passBin, MCRYPT_MODE_ECB, $iv);
            break;
        }
        $this->error = 'password_recover';
        return false;
    }
    
    public function getLoginCountry() {
    	$myIP = escapeshellarg($_SERVER['REMOTE_ADDR']);
		$pCountry = '00000'; // $_SERVER['REMOTE_ADDR'];
		if (@defined('APP_GEOLOCATION_SCRIPT') && is_file(APP_GEOLOCATION_SCRIPT)) {
			$arrOutput = array();
			exec(APP_GEOLOCATION_SCRIPT.' '.$myIP, $arrOutput);
			$strOutput = implode(' ',$arrOutput);
			if (preg_match('/(located in)/i',$strOutput)) {
				$strOutput = trim(substr($strOutput,strpos($strOutput,'in')+2));
				$pCountry = $strOutput;
			}
		}
		return $pCountry;
    }
    
    /**
     * get error 
     */
    public function getAuthError($clean=true) {
    	if (!$this->error) {
    		return false;
    	} else {
    		$str = $this->error;
    		if ($clean) {
    			$this->error = '';
    		}
    		return $str;
    	}
    }
    
    // ---- Database manipulation ---------------------------------------------
    
    protected function generateNewSalt() {
    	$this->obj->set('salt', 
    		StringHelper::genRandom(
	    		8,
	        	'abcdefghijklmnopqrstuvwxyz'
	        	.'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
	        )
	    );
    }
    
    public static function checkDbPass($mode, $pass) {
    	return (self::getDbPass($mode) == $pass);
    }
    
    public static function getDbPass($mode) {
    	$sql = 'SELECT '.$mode.' as passhash';
    	if ($rows = DbConnector::query($sql)) {
            if (!empty($rows[0])) {
                return $rows[0]['passhash'];
            }
        }
        return false;
    }
    
    protected function badAccess() {
        $sql = "UPDATE ".$this->obj->dbTable()." SET"
            ." bad_access=bad_access+1"
            ." WHERE ".$this->obj->dbUid()." = '".$this->obj->getUid()."'";
        DbConnector::query($sql);
    }
    
    protected function updatePassword() {
    	$sql = "UPDATE ".$this->obj->dbTable()." SET"
            ." password=".$this->obj->sql('password')
            ." WHERE ".$this->obj->dbUid()." = '".$this->obj->getUid()."'";
        DbConnector::query($sql);
    }
}