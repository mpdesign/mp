<?php
/**
 +------------------------------------------------------------------------------
 * 入口文件
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Admin
 +------------------------------------------------------------------------------
 */

include_once  './application/config/config.php';		//载入配置文件

if (file_exists(HTML_PATH . '/' . $_REQUEST['_u'])){
	require HTML_PATH . '/' . $_REQUEST['_u'];
	exit;
}else{
	if (empty($_REQUEST['_u']) || $_REQUEST['_u'] == 'index.php'){
		$_REQUEST['_u'] = 'home/index';
	}
	
	include_once MP_PATH . '/core/bootstrap.php';
	new Dispatch();
}


?>