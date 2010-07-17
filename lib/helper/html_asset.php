<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.5
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * HtmlAsset Helper
 * 
 * HTML Asset, links to CSS, Javascripts, etc...
 */
class HtmlAssetHelper extends Collectable {

	protected $css, $cssCode, $js, $jsCode, $jsEditor, $jsCalendar, $jsOnLoad, $rss;
	protected $_jsInHeader;

	public function __construct() {
		parent::__construct();
		$this->_jsInHeader = false;
	}
	
	protected function _init($key,$reset=false) {
		if (!is_array($this->$key) || $reset) {
			$this->$key = array();
			if (!$reset && !empty($GLOBALS['config']['header'][$key])) {
				$this->$key = StringHelper::mixedToArray($GLOBALS['config']['header'][$key]);
			}
		}
	}
	
	public function headerStuff($full=false) {
	
		if (count($this->jsCalendar)) {
			$this->add('css',APP_WWW_URI.'asset/css/calendar.css');
			$this->add('js', APP_WWW_URI.'asset/js/calendar.js');
			foreach($this->jsCalendar as $it) {
				if (is_string($it)) {
					$it=trim($it);
					$this->add('jsOnLoad',"new Calendar({ '$it': 'd/m/y' })");
				}
			}
        }
        
        if (count($this->jsEditor)) {
        	$this->add('js',APP_WWW_URI.'asset/ckeditor/ckeditor.js');
        	$str = '';
        	foreach ($this->jsEditor as $kit => $mode) {
        		$str .= "CKEDITOR.replace('$kit',{toolbar:'$mode'";	
				if (strpos($mode, 'Upl')) {
					$base = APP_WWW_URI.'asset/fmanager/';
					$str .= ", filebrowserBrowseUrl : '${base}browser.php', "
						."filebrowserImageBrowseUrl : '${base}browser.php?Type=images', "
						."filebrowserFlashBrowseUrl : '${base}browser.php?Type=flash'"; //,
					/*
					."filebrowserUploadUrl : '${base}uploader.php?command=QuickUpload&type=docs', "
					."filebrowserImageUploadUrl : '${base}uploader.php?command=QuickUpload&type=images', "
					."filebrowserFlashUploadUrl : '${base}uploader.php?command=QuickUpload&type=flash'";
					*/
				}
				$str .= "});\n";
			}
			$this->add('jsOnLoad',$str);
		}
		
        // css
        if (count($this->css)) {
			foreach($this->css as $it) {
				if (preg_match('/(^\/|http:\/\/)/', $it)) {
					echo '<link rel="stylesheet" type="text/css" href="'.$it.'" />'."\n";
				} else {
					foreach ($GLOBALS['config']['path']['css'] as $p) {
						if (file_exists(APP_WWW_PATH.$p.$it)) {
							echo '<link rel="stylesheet" type="text/css" href="'.APP_WWW_URI.$p.$it.'" />'."\n";
							break;
						}
					}
				}
			}
		}
		
		// css code (ie tests)
		if (count($this->cssCode)) {
            echo implode("\n",$this->cssCode)."\n";        
        }
        
        // javascript external scripts (if specifically requested)
        if ($full) {
        	$this->_jsInHeader = true;
			$this->_javascript();
		}
		
		// javascrpt direct code
		if (count($this->jsCode)) {
            echo '<script type="text/javascript">'."\n";
            echo implode("\n",$this->jsCode);
            echo "\n</script>\n";           
        }
        
        // javascript on load function
        if (count($this->jsOnLoad)) {
        	echo '<script type="text/javascript">'."\n";
        	echo "window.onload=function(){\n";
            echo implode("\n",$this->jsOnLoad);
            echo "\n}\n</script>\n";
        	
        }
        
		// xml/rss
        if (count($this->rss)) {
			foreach($this->rss as $it) {
				$title = 'RSS feed';
				$url = $it;
				if ($idx = strpos($it, '|')) {
					$url = substr($it, 0, $idx);
					$title = substr($it, $idx+1);
				}
				echo '<link rel="alternate" type="application/rss+xml" title="'.$title.'" href="'
					.APP_WWW_URL.$url.'" />'."\n";
			}
		}
		
	}
	
	public function footerStuff() {
		if (!$this->_jsInHeader) {
			$this->_javascript();
		}
	}
	
	protected function _javascript() {
		// javascript (include)
		if (count($this->js)) {
			foreach($this->js as $it) {
				$it = trim($it);
				if (preg_match('/^(\/|http:\/\/)/', $it)) {
					echo '<script type="text/javascript" src="'.$it.'" language="javascript"></script>'."\n";
				} else {
					foreach ($GLOBALS['config']['path']['js'] as $p) {
						if (file_exists(APP_WWW_PATH.$p.$it)) {
							echo '<script type="text/javascript" src="'.APP_WWW_URI.$p.$it.'" language="javascript"></script>'."\n";
							break;
						}
					}
				}
			}
		}
	}
	
}