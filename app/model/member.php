<?php
/**
 * TaskFreak! Time Tracker
 * 
 * @package taskfreak_tt
 * @author Stan Ozier <taskfreak@gmail.com>
 * @version 0.4
 * @copyright GNU General Public License (GPL) version 3
 */
 
/**
 * MemberModel
 * @since 0.2
 */
class MemberModel extends UserAclModel {

	public function __construct() {
		parent::__construct('member');
		$this->addProperties(array(
			'nickname'	=> 'STR',
			'email'		=> 'EML'
		));
	}
	
	public function htmlRights() {
		if ($this->isEmpty('actags')) {
			return '';
		}
		$arr = explode(',', $this->get('actags'));
		$arrTrans = array();
		if (in_array('task_see_all', $arr)) {
			$arrTrans[] = TR::html('ui','task_manager');
		}
		if (in_array('admin_user', $arr)) {
			$arrTrans[] = TR::html('ui','user_admin');
		}
		return implode(', ',$arrTrans);
	}
	
	public function check() {
		return parent::check('nickname,username');
	}
	
}
