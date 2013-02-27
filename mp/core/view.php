<?php
/**
 +------------------------------------------------------------------------------
 * Core View
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class View extends Mp {
	
	function __construct() {
		
		$params = $this->access($_REQUEST['_u']);		
		
		$_REQUEST['params'] = $params['params'];
		$_REQUEST['module'] = $this->module	=$params['module'];
		$_REQUEST['action'] = $this->action	=$params['action'];
		$this->run(new $params['action']());
	}

/**
 * Run given Controller->action 
 *
 * @param  Controller $obj 
 * @param  Action     $func 
 * @access public
 */	
	private function run( $obj,$func = 'execute') {

		if(method_exists($obj,$func)) {
			$obj->$func();
		} else {
			$obj->redirect("/errors/404");	
		}	
		
		
		if ($obj->autoRender){
			$tpl_name = $obj->set_tpl();							
			$tpl_name = $tpl_name ? $tpl_name : $this->action;
			$obj->set_tpl($this->module . '/' . $tpl_name . TPL_EXT);
			$obj->render(null,null,null,true);
		}

	}



/**
 * Parses given $params and returns an array of controller, action and parameters
 * taken from that URL.
 *
 * @param string $params URL to be parsed
 * @return array Parsed elements from URL
 * @access public
 */
	public function access($params) {
		$params = preg_replace("/[^a-zA-Z\_\/\.0-9]/i", "", $params);
		
		$strpos = strpos($params, '.');
		if ( $strpos !== false){
			$params = str_replace(substr($params, $strpos), "", $params);
		}
		
		if($params) {
			$params = explode("/",$params);$count = count($params);
			if($count >2) {
				for($i=1;$i<$count;$i++) {	 
					if(file_exists(CTRL_PATH.'/'.$params[0].'/'.$params[$i].'.php')) {
						include_once CTRL_PATH.'/'.$params[0].'/'.$params[$i].'.php';
						$result['module']		= $params[0];
						$result['action']		= intval($params[$i]) ? 'index' : $params[$i] ;
						unset($params[0]);unset($params[$i]);
						$result['params']		= array_values($params);
						break;
					}
				}
			} else {
					if(file_exists(CTRL_PATH.'/'.$params[0].'/'.$params[1].'.php')) {
						include_once CTRL_PATH.'/'.$params[0].'/'.$params[1].'.php';
						$result['module']	= $params[0];
						$result['action']	= intval($params[1]) ? 'index' : $params[1] ;
					} else {	
						if(file_exists(CTRL_PATH.'/'.$params[0].'/'.'index.php')) {
							include_once CTRL_PATH.'/'.$params[0].'/'.'index.php';
							$result['module']	= $params[0];
							$result['action']	= 'index';
							$result['params'][]	= $params[1];
						}
					}
			}
		} else {	
			include_once CTRL_PATH.'/home/'.'index.php';
			$result['module']	= 'home';
			$result['action']	= 'index';
		}
		if($result) {
			if($result['params'])$result['params'];
			return $result;
		} else {
			header("location: /errors/404");exit;
		}
	}
}
?>