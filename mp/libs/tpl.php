<?php
/**
 +------------------------------------------------------------------------------
 * tpl php or smarty
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */
class tpl_lib {

	public $template_dir = 'default';
	
	function __construct() {
		
	}
	
	function method($smarty = true){
		
		if ($smarty){	
				
			$tpl_obj = load('smarty','plugin');
			$tpl_obj->template_dir	= TPL_PATH . '/' .$this->template_dir;
			$tpl_obj->config_dir    = $tpl_obj->template_dir.'/config';  
			$tpl_obj->compile_dir   = CACHE_PATH;
			if(!is_dir($tpl_obj->compile_dir)) {
				if(!mkdir($tpl_obj->compile_dir,0777)){exit('make dir error!');}
			}
			$tpl_obj->compile_check		= SMARTY_CHECK;
			$tpl_obj->caching        	= SMARTY_CACHE;             
	
			$tpl_obj->left_delimiter 	= LEFT_TAG;  
			$tpl_obj->right_delimiter 	= RIGHT_TAG; 
			
		}else{
			$tpl_obj = new php_tpl();
			$tpl_obj->template_dir = TPL_PATH . '/' .$this->template_dir;
		}

		return $tpl_obj;
	}
	 
	
	
}
/****
 * smarty_tpl
 * class smarty_tpl {load('smarty');}
 */

/**
 * php_tpl
 *
 */
class php_tpl {
	
	public $data = array();
	
	function __construct() {}
	
	function assign($tpl_var=null, $value = null){
		
		$this->data[$tpl_var] = $value;
		
	}
	
	public function fetch($resource_name=null, $cache_id = null, $compile_id = null,$disp=false) {
		
		extract($this->data);
		include_once $this->template_dir . '/' . $resource_name;
		
	}
	
	function set_tpl($tpl_name = '') {
		
		$this->tpl_name = $tpl_name;
		
	}
}
?>