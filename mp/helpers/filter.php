<?php
/**
 +------------------------------------------------------------------------------
 * Filter input filter code XSS, SQL injection
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class filter_helper {
	
	var $enable_xss = true;
	
	function _clean_data($str, $type = 'input', $_standardize_newlines = true) {
		if (is_array($str)) {
			$new_array = array();
			foreach ($str as $key => $val) {
				$new_array[$this->_clean_keys($key)] = $this->_clean_data($val, $type, $_standardize_newlines);
			}
			return $new_array;
		}

		/* We strip slashes if magic quotes is on to keep things consistent

		   NOTE: In PHP 5.4 get_magic_quotes_gpc() will always return 0 and
			 it will probably not exist in future versions at all.
		*/
		
		if ( !get_magic_quotes_gpc()) {	
			if ($type == 'input'){			
				$str = addslashes($str);
			}else{ 
				$str = stripslashes($str);
			}
		}

		// Remove control characters
		$str = remove_invisible_characters($str);

		// Should we filter the input data?
		if ($this->enable_xss) {
			$str = load('security')->xss_clean($str);
		}

		// Standardize newlines if needed
		if ($_standardize_newlines) {
			if (strpos($str, "\r") !== FALSE) {
				$str = str_replace(array("\r\n", "\r", "\r\n\n"), PHP_EOL, $str);
			}
		}

		return $str;
	}

	function _clean_keys($str) {
		if ( ! preg_match("/^[a-z 0-9~%.:_\-]+$/i", rawurlencode($str))) {
			exit('Disallowed Key Characters.');
		}
		return $str;
	}

	/**
	 * Filter input filter code XSS, SQL injection
	 *
	 * @param mixed $input
	 * @return mixed
	 */
	public function input($input){
		return $this->_clean_data($input,'input');
	}
	
	public function output($output){
		return load('security')->xss_clean($output,'output');
	}
	
}	
