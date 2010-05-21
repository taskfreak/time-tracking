<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.2
 * @since 0.2
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * AuthHelper ACL
 * 
 * authentication system with ACL support
 */
class AuthAclHelper extends AuthHelper {

	public $_arrAcl;

	public function __construct($obj) {
		parent::__construct($obj);
	}
	
	/**
	* check ACL
	*/
	public function checkAcl($code) {
		if (!is_array($this->_arrAcl)) {
			$this->_arrAcl = explode(',', $this->obj->get('actags'));
		}
		return in_array($code, $this->_arrAcl);
	}
    
    /**
     * update sessions variables (on login and when updating own profile)
     */
    public function updateSessionVariables() {
    	parent::updateSessionVariables();
    	if ($this->obj->getPropertyType('actags')) {
	    	$_SESSION['appUserAcl'] = $this->obj->get('actags');
	    }
    }
	
	/**
	* Logout logs the user out (yes indeed)
	*/
    public function logout() {
    	parent::logout();
        $this->obj->set('actags',''); // -TODO- set guest tags
    }
    
	/**
	* check if user is logged in. Do not load from DB by default
	*/
    public function isLoggedIn($load=false) {
    
    	if (parent::isLoggedIn($load)) {
    		$this->obj->set('actags', $_SESSION['appUserAcl']);
    		return true;
    	} else {
    		return false;
    	}
	}
	
	/**
	 * save User ACL settings
	 */
	public function updateACL($section='') {
		$id = $this->obj->getUid();
		if (!$id) {
			return false;
		}
	
		$objLst = new AclModel();
		$objLst->connectDb();
		if (is_string($section)) {
			$objLst->where("section='$section'");
		}
		if (!$objLst->loadList()) {
			return false;
		}
		$i=0;
		$fc = FrontController::getInstance();
		$db = DbConnector::getConnection();
		$table = $objLst->dbTable('acl_user');
		while ($objLst->next()) {
			if ($fc->getReqVar('acl_'.$objLst->get('name'))) {
				$db->query('INSERT IGNORE INTO `'.$table.'` SET `user_id`='.$id.', `acl_id`='.$objLst->getUid());
			} else {
				$db->query('DELETE IGNORE FROM `'.$table.'` WHERE `user_id`='.$id.' AND `acl_id`='.$objLst->getUid());
			}
			$i++;
		}
		return $i;
	}

}