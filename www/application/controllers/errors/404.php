<?php
/**
 +------------------------------------------------------------------------------
 * 404错误页
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */
class index extends appController {
	function Run() {
		$this->assign('secs',3);
		$this->assign('msg',"抱歉，找不到您要浏览的页面！");
		$this->assign('url',"/");
		$this->set_tpl( '404');
	}
}
?>
