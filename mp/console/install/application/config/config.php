<?php
/**
 +------------------------------------------------------------------------------
 * 全局配置文件
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   
 +------------------------------------------------------------------------------
 */

error_reporting(7);				//错误提示级别E_ALL

//+++++++++++++++++++++++++++++++++路径配置++++++++++++++++++++++++++++++++++

define('PROJECT_PATH'	,	'/myweb');

define('MP_PATH'		,	PROJECT_PATH . '/mp');							//核心文件目录

define('ROOT_PATH'		,	PROJECT_PATH . '/myproject');						//项目根目录

define('APP_PATH'		,	ROOT_PATH . '/application');					//应用路径

define('HTML_PATH'		,	ROOT_PATH . '/html');							//静态文件目录


//+++++++++++++++++++++++++++++++++数据库配置++++++++++++++++++++++++++++++++++

$config["db"]["default"]["type"] = "mysql";

$config["db"]["default"]["host"] = "192.168.1.2:3306";

$config["db"]["default"]["user"] = "root";

$config["db"]["default"]["password"] = "123456";

$config["db"]["default"]["dbname"] = "test";

$config["db"]["default"]["prefix"] = "test_";

$config["db"]["default"]["pconnect"] = false;

$config["db"]["default"]["abnormal"] = true;								//mysql异常调试模式

//+++++++++++++++++++++++++++++++++站点信息配置++++++++++++++++++++++++++++++++++

define('AUTHCODE'		,	'123456');										//加密key，设置后不可修改

$config["site"]["domain"] = "kuailaio.com";

$config["site"]["title"] = "快来哦";

$config["cookie"]["domain"] = 'kuailaio.com';									//cookie配置
$config["cookie"]["expire"] = 7200;


$config["router"]["admin"]["entrance"] = "admin";							//后台URL入口前缀

$config["router"]["admin"]["prefix"] = "admin";								//后台控制器文件前缀

$config["router"]["method"] = "Run";							//默认执行方法

//$config["router"]["static"]["suffix"] = ".html";							//伪静态后缀


//+++++++++++++++++++++++++++++++++模板配置+++++++++++++++++++++++++++++++++++

$config["smarty"]["open"] = true;											//开启smarty模板:true   开启普通PHP模板:false

$config["smarty"]["ext"] = ".htm";											//模板文件后缀

$config["smarty"]["cache"] = false;											//这里是调试时设为false,发布时请使用true

$config["smarty"]["check"] = true;

$config["smarty"]["left_tag"] = "<!--{";

$config["smarty"]["right_tag"] = "}-->";

//+++++++++++++++++++++++++++++++++会话配置+++++++++++++++++++++++++++++++++++

$config["session"]["expire"] = 7200;

$config["session"]["cookie_name"] = "SESSION";

$config["session"]["cookie_prefix"] = "klo";

$config["session"]["cookie_path"] = "/";

$config["session"]["entry_key"] = "";									//sid加密串

$config["session"]["cookie_domain"] = "kuailaio.com";

$config["session"]["storage"] = "mem";										//存储方式db/mem

$config["session"]["table"] = "sessions";


//+++++++++++++++++++++++++++++++++内存配置+++++++++++++++++++++++++++++++++++

$config["memcache"]["d"] = false;//是否memcached扩展
$config["memcache"]["master"] = array(array('192.168.1.182',11211));
//$config["memcache"]["slave"] = array(array('192.168.1.182',11211),array('192.168.1.183',11211),array('192.168.1.184',11211));			//备份组内存,开启备份将影响性能

$config["redis"]["master"] = array(
	"host" => '192.168.1.188', 
	"port" => 6379,
	"pconnect" => false,
	"prefix" => "",
	"auth" => "redis");


