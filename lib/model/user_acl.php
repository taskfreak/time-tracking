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
 * User ACL
 * 
 * abstract model implementing common user properties and methods
 * with ACL support
 */
 
abstract class UserAclModel extends UserModel {

	public function __construct($table) {
		parent::__construct($table);
	}

	public function enableAuthentication() {
		$this->addProperties(array(
			'actags'	=> 'STR'
		));
		$this->addHelper('auth_acl', $this);
	}
	
	public function load($filter='') {
		$db = $this->getHelper('db');
		if ($this->getPropertyType('actags')) {
			$this->_generateQuery($db);
			return $db->load($filter, false);
		} else {
			$args = func_get_args();
			return $this->callHelperArray('db','load',$args);
		}
	}
	
	public function loadList() {
		$db = $this->getHelper('db');
		if ($this->getPropertyType('actags')) {
			$this->_generateQuery($db);
			return $db->loadList(false);
		} else {
			return $db->loadList(true);
		}
	}
	
	public function save($acl=false) {
		$this->removeProperties('actags');
		if (parent::save()) {
			if ($acl) {
				$this->saveACL($acl);
			}
			return true;
		}
		return false;
	}
	
	public function saveACL($section='') {
		$this->addHelper('auth_acl', $this);
		$this->updateACL($section);
	}
	
	protected function _generateQuery(&$db) {
		$db->select($db->dbTable().".*, GROUP_CONCAT(ac.name SEPARATOR ',') as actags");
		$db->from($db->dbTable());
		$db->leftJoin(array('acl_user','aa'),array($this->dbUid(true),'aa.user_id'));
		$db->leftJoin(array('acl','ac'),array('aa.acl_id','ac.id'));
		$db->groupBy('member.id');
	}
}