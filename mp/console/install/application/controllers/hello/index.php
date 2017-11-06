<?php
/**
 +------------------------------------------------------------------------------
 * 首页
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   
 +------------------------------------------------------------------------------
 */
class index extends helloController {
	
	function Run() {
		//$this->model("hello")->select($sql);
		//load("help","helper")->method();
		//$this->session->set("user_id",1);
		//$this->memmory->set("hello","MPPHP");
		$this->assign("hello", "welcome use MPPHP");
	}

}
?>