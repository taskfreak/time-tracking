<?php
/**
 * Tzn Framework
 * 
 * @package tzn_models
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.2
 * @since 0.2
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * ACL
 * 
 * Object representing user rights
 */
class AclModel extends Model {

	public function __construct() {
		parent::__construct('acl');
		$this->addProperties(array(
			'id'			=> 'UID',
			'name'			=> 'STR',
			'section'		=> 'STR'
		));
	}
	
}

/**
 * User ACL
 * 
 * Object representing association between users and rights
 */
class AclUserModel extends Model {

	public function __construct() {
		parent::__construct('acl_user');
		$this->addProperties(array(
			'user_id'		=> 'NUM',
			'acl_id'		=> 'NUM'
		));
	}
	
}