<?php
/**
 +------------------------------------------------------------------------------
 * 入口文件
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */


$time = microtime(TRUE);
$mem  = memory_get_usage();
/*********开始**************************************************************************************************************/



include_once  './application/config/config.php';		//载入配置文件

if (file_exists(HTML_PATH . '/' . $_REQUEST['_u'])){
	require HTML_PATH . '/' . $_REQUEST['_u'];
	exit;
}else{
	if (empty($_REQUEST['_u']) || $_REQUEST['_u'] == 'index.php'){
		$_REQUEST['_u'] = 'home/index';
	}
	
	include_once MP_PATH . '/core/bootstrap.php';
	new View();
}



/*********结束**************************************************************************************************************/
$end = microtime(TRUE);
echo 'The process has occupied ' . floor((memory_get_usage() - $mem)/1024/1024*10000)/10000 . ' M memory and cost  ' . floor(($end - $time)*10000)/10000 . ' seconds';
echo '<br /> Power by mpdesign 84086365@qq.com';
?>