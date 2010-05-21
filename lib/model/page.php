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
 * Page
 * 
 * Object representing a page : contain title, headers, browsing history, and stuff
 */
class PageModel extends Model {

	public $header;

	public function __construct() {
		parent::__construct();
		$this->addProperties(array(
			'title'			=> 'STR',
			'description'	=> 'STR',
			'keywords'		=> 'STR',
			'rank'			=> 'INT'
		));
		$this->addHelper('html_asset');
	}
	
	public function dispatchHeader() {
		if ($this->isEmpty('description') && isset($GLOBALS['config']['page']['description'])) {
			$this->set('description',$GLOBALS['config']['page']['description']);
		}
		if ($this->isEmpty('keywords') && isset($GLOBALS['config']['page']['keywords'])) {
			$this->set('keywords',$GLOBALS['config']['page']['keywords']);
		}
		include APP_ASSET_PATH.'html/page-header.php';
	}
	
	public function dispatchFooter() {
		include APP_ASSET_PATH.'html/page-footer.php';
	}
	
}