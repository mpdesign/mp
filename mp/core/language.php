<?php
/**
 +------------------------------------------------------------------------------
 * Core Language
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Language {
	
	public $dict = array();
	
	static $instance = null;
	
	function __construct(){
		
		$language = config_item("site", "language");
		$language_file = APP_PATH . '/lanuages/' . $language . '/' . $language . '.php';
		
		if (file_exists($language_file)){
			
			include_once $language_file;
			global $_LANGUAGE;
			$this->dict = $_LANGUAGE;
		
		}
		
		
		
	}
	
	static function getInstance(){
		if (self::$instance == null){
			self::$instance = new Language();
		}
		return self::$instance;
	}

	function get($key = ''){
		
		return isset($this->dict[$key]) ? $this->dict[$key] : null;
		
	}
	
	function set($key = '', $value = ''){
		
		$this->dict[$key] = $value;
		return $this;
		
	}
	
	function dict(){
		
		return $this->dict;
		
	}
}



?>