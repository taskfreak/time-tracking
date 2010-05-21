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
 * Setting
 * 
 * @todo try it
 */
class SettingModel extends Model {

	public function __construct() {
		parent::__construct('setting');
		$this->addProperties(array(
			'setting_key'	=> 'STR',
			'setting_value'	=> 'XML',
			'section'		=> 'STR',
			'user_id'		=> 'NUM'
		));
	}
}
