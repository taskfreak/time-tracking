<?php
/**
 * Tzn Framework
 * 
 * @package tzn_models
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.2
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * User
 * 
 * abstract model implementing common user properties and methods
 */
abstract class UserModel extends Model {

	public function __construct($table) {
		parent::__construct($table);
    	$this->_properties = array(
    		'id'					=> 'UID',
    		'username'				=> 'USR',
    		'password'				=> 'PSS',
    		'salt'					=> 'STR',
    		'auto_login'			=> 'BOL',
    		'time_zone'				=> 'STR',
    		'date_format_us'		=> 'BOL',
    		'creation_date'			=> 'DTM',
    		'expiration_date'		=> 'DTE',
    		'last_login_date'		=> 'DTM',
    		'last_login_address'	=> 'STR',
			'last_change_date'		=> 'DTM',
    		'visits'				=> 'NUM',
    		'bad_access'			=> 'NUM',
    		'activation'			=> 'STR',
    		'enabled'				=> 'BOL',
   		);
	}
	
	public function enableAuthentication() {
		$this->addHelper('auth', $this);
	}
	
	public function setLogin($login) 
	{
		if (!trim($login)) {
			return false;
		}
        switch (APP_AUTH_FIELD) {
    	case 'email':
    		// login ID is email
    		if (!$this->set('email',$login)) {
    			return false;
    		}
    		if ($this->findOccurences('email',$login)) {
    			$this->_error["email"] = 'email_exists';
	            return false;
    		}
    		
    		break;
    	default:
    		if (!$this->set('username',$username)) {
    			return false;
    		}
        }
        
        // check nickname (username) is unique
		if ($this->findOccurences('username',$login)) {
			$this->_error['username'] = 'user_name_exists';
			return false;
		}
		return true;
	}
	
	public function setPassword($pass1, $pass2=false, $emptyIsOk=false)
	{
		if ($pass1 || $emptyIsOk) {
            // a pass has been set
            if (($pass2 !== false) && ($pass1 != $pass2)) {
                // a confirmation has been set but is different 
                $this->_error['password'] = 'user_pass_mismatch';
                return false;
            }
            $this->set('salt',StringHelper::genRandom(8,
            	'abcdefghijklmnopqrstuvwxyz'
            	.'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'));
            if ($pass1) {
                if ((strlen($pass1) >= APP_USER_PASS_MIN) 
                	&& (strlen($pass1) <= APP_USER_PASS_MAX))
                {
                	$salt = $this->get('salt');
                	
                    switch (APP_AUTH_PASSWORD_MODE) {
                    case 1:
                        $this->set('password',crypt($pass1 , $salt));
                        break;
                    case 2:
                        $this->set('password',AuthHelper::getDbPass("ENCRYPT('$pass1','$salt')"));
                        break;
                    case 3:
                        $this->set('password',AuthHelper::getDbPass("ENCODE('$pass1','$salt')"));
                        break;
					case 4:
					case 5:
						$this->set('password',AuthHelper::getDbPass("MD5('$pass1')"));
						break;
                    default:
                        $iv = mcrypt_create_iv (mcrypt_get_iv_size(MCRYPT_3DES
                        	, MCRYPT_MODE_ECB), MCRYPT_RAND);
                        $crypttext = mcrypt_encrypt(APP_AUTH_PASSWORD_MODE, $salt
                        	, $pass1, MCRYPT_MODE_ECB, $iv);
                        $this->set('password',bin2hex($crypttext));
                    }
                } else {
                    $this->_error['password'] = 'user_pass_length';
                    return false;
                }
            } else {
                $this->set('password','');
            }
            return true;
        } else {
            if (!$emptyIsOk) {
                $this->_error['password'] = 'user_pass_empty';
                return false;
            }
            return true;
        }
	}
	
	public function setupTimeZone() {
		if (!$this->isEmpty('time_zone')) {
			try {
				$GLOBALS['config']['datetime']['timezone_user'] = new DateTimeZone($this->get('time_zone'));
				// FC::log_debug('User::setupTimeZone : new time zone '.$this->get('time_zone'));
			} catch (Exception $e) {
				FC::log_error('User::setupTimeZone : unknown time zone ('.$this->get('time_zone').')');
			}
		}
	}
		
}