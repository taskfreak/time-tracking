<?php
/**
 * Tzn Framework
 * 
 * @package tzn_helpers
 * @author Stan Ozier <framework@tirzen.com>
 * @version 0.2
 * @since 0.1
 * @copyright GNU Lesser General Public License (LGPL) version 3
 */

/**
 * HtmlFormHelper
 * 
 * HTML form fields helper
 */
class HtmlFormHelper extends Helper {

	// protected $fc;

	public function __construct($obj=null) {
		parent::__construct($obj);
		// $this->fc = FrontController::getInstance();
	}
	
	/**
	 * generates a complete form for model object given to constructor
	 */
	public function iAutoForm($name, $method='post', $action='', $class='') {
		if (!isset($this->obj)) {
			throw AppException('Can not generate form : no object given in Helper');
		}
		$str = $this->iForm($name, $method, $action);
		$str .= $this->obj->iFields($class);
		$str .= '</form>';
		return $str;
	}
	
	/**
	 * returns a <FORM> tag
	 * @param string $name name of form (id will be i_$name)
	 * @param string $method post, get, or file (post by default)
	 * @param string $action URL to which form is sent (current page by default)
	 */
	public function iForm($name, $method='post', $action='', $class='') {
		$fc = FrontController::getInstance();
		if (!$action) {
			$action = $fc->thisUrl();
		}
		$str = '<form id="f_'.$name.'" name="'.$name.'"';
		if ($method == 'file') {
			$str .= ' method="post" enctype="multipart/form-data"';
		} else {
			$str .= ' method="'.$method.'"';
		}
		if ($class) {
			$str .= ' class="'.$class.'"';
		}
		$str .= ' action="'.$action.'">';
		return $str;
	}
	
	/**
	 * generates form field (only selected fields)
	 */
	public function iFields($class='side') {
		if (!isset($this->obj)) {
			throw AppException('Can not generate fields : no object given in Helper');
		}
		$str = '<ol class="fields'.($class?(' '.$class):'').'">';
		foreach ($this->obj->getFields() as $key => $type) {
			$type = substr($type,0,3);
			// error_log(get_class($this->obj).' iFields : '.$key.' / '.$type);
			if ($type == 'UID') {
				// put UID in hidden field before <OL>
				$str = $this->iHidden($key).$str;
				continue;
			} else if ($this->obj->isObjectProperty($key, $class, $nkey)) {
				// get a simple input field for the ID
				// error_log(get_class($this->obj).' iFields found '.$key.' as an object of '.$class.' ('.$nkey.')');
				$obj = new $class;
				$obj->addHelper('html_form');
				$str .= '<li><label for="i_'.$nkey.'_id">'.$nkey.'</label>'
					.$this->obj->iNested('id', $nkey);
				
			} else if ($sf = $this->iFieldLabelled($key, $key, $type)) {
				$str .= $sf;
			}
		}
		$str .= '<li class="buttons"><button type="submit" name="save" value="1">Save</button></li>';
		$str .= '</ol>';
		return $str;
	}
	
	/**
	 * returns a field along with LABEL, all wrapped in a LI
	 * @todo make the wrapper optional
	 * @todo missing types
	 */
	public function iFieldLabelled($key, $label='', $type='', $wrap='li') {
		if (empty($type)) {
			$type = $this->obj->getPropertyType($key);
		}
		$sf = '';
		switch($type) {
			case 'UID': //-TODO- hidden ?
			case 'INT':
			case 'NUM':
			case 'DEC':
			case 'STR':
			case 'EML':
			case 'URL':
			case 'TIM': // -TODO- 
				$sf = $this->iText($key);
				break;
			case 'DTE':
				$sf = $this->iDate($key);
				break;
			case 'DTM':
				$sf = $this->iDateTime($key);
				break;
			case 'USR':
				$sf = $this->iText($key);
				break;
			case 'PSS':
				$sf = $this->iPass($key);
				break;
			case 'TXT':
			case 'BBS': // -TODO-
			case 'HTM': // -TODO-
			case 'XML': // -TODO-
				$sf = $this->iTextArea($key);
				break;
			case 'BOL':
				$sf = $this->iCheckBox($key);
				break;
			case 'LVL': // -TODO-
			case 'DOC': // -TODO-
			case 'IMG': // -TODO-
			case 'OBJ': // -TODO-
				break;
		}
		if (!$sf) {
			return false;
		}
		if (empty($label)) {
			$label = str_replace('_',' ',$key); // -TODO- translate
		}
		return $this->_htmlWrapBegin($wrap)
			.'<label for="i_'.$key.'">'.$label.'</label>'
			.$sf
			.$this->_htmlWrapEnd($wrap);		
	}
	
	/*
	 * generates a hidden field
	 */
	public function iHidden($key) {
		$str = '<input type="hidden" id="i_'.$key.'" name="'.$key.'" value="'
			.$this->_value($key)
			.'" />';
		return $str;
	}
	
	/*
	 * generates a text field
	 */
	public function iText($key) {
		$str = '<input type="text" id="i_'.$key.'" name="'.$key.'" value="'
			.$this->_value($key)
			.'" />';
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	
	/*
	 * generates a text field
	 */
	public function iNested($key, $nested) {
		$str = '<input type="text" id="i_'.$nested.'__'.$key.'" name="'.$nested.'__'.$key.'" value="'
			.$this->_value("$nested::$key")
			.'" />';
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	
	/**
	 * generates date field
	 * @todo real date field
	 */
	public function iDate($key) {
		$str = '<input type="text" id="i_'.$key.'" name="'.$key.'" value="'
			.$this->_value($key)
			.'" class="date" />';
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	/**
	 * generates date time field
	 * @todo real date time field
	 */
	public function iDateTime() {
		$args = func_get_args();
		return call_user_func_array(array($this,'iText'),$args);
	}
	
	/**
	 * generates a password field
	 */
	public function iPass($key, $autocomplete=true) {
		$str = '<input type="password" id="i_'.$key.'" name="'.$key.'" value=""';
		if (!$autocomplete) {
			$str .= ' autocomplete="off"';
		}
		$str .= ' />';
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	
	/**
	 * generates a simple text area
	 */
	public function iTextArea($key) {
		$str = '<textarea id="i_'.$key.'" name="'.$key.'">'
			.$this->_value($key)
			.'</textarea>';
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	
	/**
	 * generates a check box
	 */
	public function iCheckBox($key, $label='') {
		$str = '<input type="checkbox" id="i_'.$key.'" name="'.$key.'"';
		if ($this->_value($key)) {
			$str .= ' checked="checked"';
		}
		$str .= ' value="1" />';
		if (empty($label)) {
			$options = $this->obj->getPropertyOptions($key);
			if (!empty($options['label'])) {
				// -TODO- translate
				$label = $options['label'];
			}
		}
		if (!empty($label)) {
			$str .= '<label for="_'.$key.'" class="inline">'.VarStr::html($label).'</label>';
		}
		if ($err = $this->_htmlError($key)) {
			$str .= $err;
		}
		return $str;
	}
	
	/**
	 * generates a series of radio inputs
	 */
	public function iRadio($key, $options='', $wrap='ul', $subwrap='li') {
		if (isset($this->obj)) {
			$options = $this->obj->getPropertyOptions($key);
		}
		if (empty($options['options'])) {
			FC::log_warn("iRadio $key does not provide options");
			return $key.' (no option)';
		}
		$str = $this->_htmlWrapBegin($wrap);
		$i = 1;
		foreach($options['options'] as $val => $lbl) {
			$str .= $this->_htmlWrapBegin($subwrap);
			$str .= '<input type="radio" id="i_'.$key.'_'.$i.'" name="'.$key.'"';
			if ($options['value'] == $val) {
				$str .= ' checked="checked"';
			}
			$str .= ' value="'.$val.'" />';
			$str .= '<label for="i_'.$key.'_'.$i.'">'.$lbl.'</label>';
			$str .= $this->_htmlWrapEnd($subwrap);
			$i++;
		}
		return $str.$this->_htmlWrapEnd($wrap);
	}
	
	/**
	 * generates a select (drop down)
	 */
	public function iSelect($key, $options='') {
		if (isset($this->obj)) {
			$options = $this->obj->getPropertyOptions($key);
		}
		if (empty($options['options'])) {
			FC::log_warn("iRadio $key does not provide options");
			return $key.' (no option)';
		}
		$str = '<select id="i_'.$key.'" name="'.$key.'">';
		$i = 1;
		foreach($options['options'] as $val => $lbl) {
			$str .= '<option value="'.$val.'"';
			if ($options['value'] == $val) {
				$str .= ' selected="selected"';
			}
			$str .= ' value="'.$val.'">'.$lbl.'</option>';
			$i++;
		}
		return $str.'</select>';
	}
	
	/**
	 * generates a time zone drop down list
	 */
	public function iTimeZone($key, $options='') {
		if (isset($this->obj)) {
			$default = $this->obj->get($key);
			// $options = $this->obj->getPropertyOptions($key);
		}
		
		$timezone_identifiers = DateTimeZone::listIdentifiers();
		$str = '<select id="id_'.$key.'" name="'.$key.'">';
		$continent = '';
		foreach( $timezone_identifiers as $value ) {
	        if ( preg_match( '/^(America|Antartica|Arctic|Asia|Atlantic|Europe|Indian|Pacific)\//', $value ) ) {
	            $ex=explode("/",$value);//obtain continent,city   
	            if ($continent!=$ex[0]){
	                if ($continent!="") echo '</optgroup>';
	                $str .= '<optgroup label="'.$ex[0].'">';
	            }
	            $city=$ex[1];
	            $continent=$ex[0];
	            $str .= '<option value="'.$value.'"';
	            if ($value == $default) {
	            	$str .= ' selected="selected"';
	            }
	            $str .='>'.$city.'</option>';               
	        }
	    }
	    $str .= '</optgroup>';
	    return $str.'</select>';
    }
    	
	/**
	 * returns a form field formatted value
	 */
	protected function _value($key) {
		if (!isset($this->obj)) {
			return '';
		}
		return $this->obj->value($key);
	}
	
	/**
	 * returns error (HTML formatted)
	 */
	protected function _htmlError($key) {
		if (!isset($this->obj)) {
			return '';
		}
		return $this->obj->htmlError($key);
	}
	
	/**
	 * HTML wrapper begin tag
	 */
	protected function _htmlWrapBegin($wrap) {
		if ($wrap) {
			return '<'.$wrap.'>';
		} else {
			return '';
		}
	}
	
	/**
	 * HTML wrapper end tag
	 */
	protected function _htmlWrapEnd($wrap) {
		if ($wrap) {
			if ($idx = strpos($wrap, ' ')) {
				$wrap = substr($wrap,0,$idx);
			}
			return '</'.$wrap.'>';
		} else {
			return '';
		}
	}
}
