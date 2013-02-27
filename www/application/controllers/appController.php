<?php
/**
 +------------------------------------------------------------------------------
 * Controller for App
 * 
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.

 * This is a placeholder class.
 * Create the same file in Application/appController.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.

 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */

class appController  extends Controller {
	
	function execute(){}

	function beforFilter(){$this->init();}
	
	function init(){
		
		$this->assign('title',config_item("site","title"));
			
		
		
		load('filter','helper')->enable_xss = $this->enable_xss ? true : false;
		$this->data = load('filter','helper')->input($_REQUEST);
		
		$obj->params = $this->data['params'];
		$_SERVER = load('filter','helper')->input($_SERVER);
		$this->data['post'] = $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
	}
	
	
	
	public function display($msg='',$url='',$secs = 1) {
		if($secs == -1) {
			$msg_prompt .= "<script language=javascript>alert('" . $msg . "');";
			if($backurl) {
				$msg_prompt .= "window.location.href = '" . $url . "';";
			} elseif(!$url) {
				$msg_prompt .= "history.go(-1);";
			}
			$msg_prompt .= "</script>";
			echo  $msg_prompt;
		} else {
			if($secs == 0) {
				header("Location: ".$url);
			} else {	
				$this->assign('secs',$secs);
				$this->assign('msg',$msg);
				$this->assign('url',$url);
				echo $this->render('errors/404.htm');
			}
		}
		exit;
	}
	
	
	function afterFilter(){}

}



?>