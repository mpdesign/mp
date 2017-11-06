<?php
/**
 +------------------------------------------------------------------------------
 * tpl php or smarty
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */
class View {

	public $template_dir = 'default';
	
	static $instance = null;
	
	public $file = "";
	
	function __construct() {
		
	}
	
	static function getInstance(){
		if (self::$instance == null){
			self::$instance = new View();
		}
		return self::$instance;
	}
	
	function method($tplEngine = "smarty"){
		
		if ($tplEngine == "smarty"){
		
				
			$tpl_obj = Smarty_tpl::getInstance();
			$tpl_obj->template_dir	= VIEW_PATH . '/' .$this->template_dir;
			$tpl_obj->config_dir    = $tpl_obj->template_dir.'/config';  
			$tpl_obj->compile_dir   = CACHE_PATH . '/smarty/' . $this->template_dir;
			if(!is_dir($tpl_obj->compile_dir)) {
				if(!is_dir(CACHE_PATH)) {
					if(!mkdir(CACHE_PATH,0777)){exit('make dir error: ' . CACHE_PATH );}
				}
				if(!mkdir($tpl_obj->compile_dir,0777)){exit('make dir error: ' . $tpl_obj->compile_dir );}
			}
			$tpl_obj->compile_check		= config_item("smarty", "check");
			$tpl_obj->caching        	= false; 
	
			$tpl_obj->left_delimiter 	= config_item("smarty", "left_tag");  
			$tpl_obj->right_delimiter 	= config_item("smarty", "right_tag");
			
		}elseif ($tplEngine == "php"){
			$tpl_obj = new php_tpl();
			$tpl_obj->template_dir = VIEW_PATH . '/' .$this->template_dir;
		}else{
			die("Unable to find template engine! ");
		}

		return $tpl_obj;
	}
	 
/**
 +------------------------------------------------------------------------------
 * make static html
 +------------------------------------------------------------------------------

 */
	function file_path($path, $params = array()){
		
		$params = $params ? sort($params) : array();
		$params = md5(serialize($params));
		$path = "static/" . $path . "/" . substr($params, 0, 1) . "/" . substr($params, 1, 2) . "/" . substr($params, 3, 3) . "/";
		
		load("attachment", "helper")->__mkdirs($path, CACHE_PATH);
		
		$this->file = CACHE_PATH . "/" . $path . $params . ".html";
		
		return $this;
		
	}
	
	function start_html(){
	
		ob_start();
		
	}
	
	function end_html(){
	
		ob_end_flush();
		
	}
	
	/**
	 * write static html
	 */
	function input_html(){
		
		if ( !$this->file )$this->file_path();
		
		$input = ob_get_contents();
		$input = $this->compress_html($input);
		$fp = fopen($this->file, 'w'); 
		fwrite($fp, $input); 
		fclose($fp); 
		
	}
	
	/**
	 * output static html
	 */
	function output_html(){
		
		if ( !$this->file )$this->file_path();
		
		if (file_exists($this->file)) return $this->file;
		else return false;
	}
	
	/**
	 * delete static html
	 */
	function del_html(){
	
		if ( !$this->file )$this->file_path();
		
		@unlink($this->file);
	
	}
	
	/**
	 * 
	 * compress html
	 * @param string $string
	 */
	function compress_html($string = "") {
	
//		$pattern = array ("(\r\n|\n|\t)", "/<!\-\-(?!\-\->)\-\->/i", '/([\s]){2,}/i');
//		
//		$replace = array ("", "", "\\1");
//		
//		$string = preg_replace($pattern, $replace, $string);
		
		return $string;
		
	}
	
}

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

/**
 +------------------------------------------------------------------------------
 * Smarty tpl
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */


class Smarty_tpl{
	
	static $instance = null;
	
	function __construct() {}
	
	static function getInstance(){
		if (self::$instance == null){
			include_once MP_PATH.'/plugins/smarty/Smarty.class.php';
			self::$instance = new Smarty();
		}
		return self::$instance;
	}
	
}

