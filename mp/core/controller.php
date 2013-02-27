<?php
/**
 +------------------------------------------------------------------------------
 * Core Controller
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

abstract class Controller extends Mp {
	
	public abstract function execute();

/**
 * 
 * Is autoRender view
 * @var bool
 */	
	public $autoRender = true;
	
/**
 * 
 * Load smarty plugin
 * @var bool
 */
	public $smarty = SMARTY;
	
 /**
     * Smarty Configuration Section
     */

    /**
     * The name of the directory where templates are located.
     *
     * @var string
     */	
	public $template_dir = 'default';
	
	
/**
 * load template obj
 */
	public function tpl(){ $this->tpl = load('tpl')->method($this->smarty); }

/**
 * assigns values to template variables
 *
 * @param array|string $tpl_var the template variable name(s)
 * @param mixed $value the value to assign
 */
	public function assign($tpl_var=null, $value = null) {
		
		if(!is_object($this->tpl)) $this->tpl();
		if($tpl_var) {
			$this->tpl_act='assign';
			$this->tpl->assign($tpl_var, $value);			
		} else {
			return $this->tpl_act;
		}
		
	}

    /**
     * Smarty Configuration tpl_file
     */

    /**
     * Set the name of the tpl_file.
     *
     * @param string $resource_name
     */
	public function set_tpl($resource_name=null) {
		
		if($resource_name) {
			$this->tpl_name = $resource_name;
		} else {
			return $this->tpl_name;
		}
		
	}

/**
 * executes & returns or displays the template results
 *
 * @param string $resource_name
 * @param string $cache_id
 * @param string $compile_id
 * @param boolean $display
 */
	public function render($resource_name=null, $cache_id = null, $compile_id = null,$disp=false) {
		
		$this->beforeRender();
		
		if(!is_object($this->tpl)) $this->tpl();
		$this->tpl_act='fetch';
		$resource_name=$resource_name?$resource_name:$this->tpl_name;
		return $this->tpl->fetch($resource_name, $cache_id, $compile_id, $disp);
		
	}

/**
 * Called after the controller action is run, but before the view is rendered.
 *
 * @access public
 */
	function beforeRender() {}

/**
 * Redirects to given $url
 * Script execution is halted after the redirect.
 *
 */
	function redirect($url = '') {
		
		$this->autoRender = false;
		header("location: " . $url);
		exit;
		
	}
	
}



?>