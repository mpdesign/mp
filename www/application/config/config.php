<?php
/**
 +------------------------------------------------------------------------------
 * 全局路径配置文件
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */

//+++++++++++++++++++++++++++++++++路径配置++++++++++++++++++++++++++++++++++

define('PROJECT_PATH'	,	'/myweb/wangmeng/oa');

define('MP_PATH'		,	PROJECT_PATH . '/mp');							//核心文件目录

define('ROOT_PATH'		,	PROJECT_PATH . '/admin');						//根目录

define('APP_PATH'		,	ROOT_PATH . '/application');					//应用路径

define('HTML_PATH'		,	ROOT_PATH . '/html');							//静态文件目录


//+++++++++++++++++++++++++++++++++数据库配置++++++++++++++++++++++++++++++++++

define('DB_TYPE'		,	'mysql');

define('DB_HOST'		,	'localhost');

define('DB_USER'		,	'root');

define('DB_PASS'		,	'root');

define('DB_NAME'		,	'test');

define('TABLE_PREFIX'	,	'test_');

//+++++++++++++++++++++++++++++++++站点信息配置++++++++++++++++++++++++++++++++++

$config["site"]["domain"] = "localhost";

$config["site"]["title"] = "站点名称";

define('C_DOMIN'		,	'localhost');									//cookie配置

define('AUTHCODE'		,	'localhost');										//加密key，设置后不可修改

define('VERCODE'		,	'root@localhost');								//跨域验证key


//+++++++++++++++++++++++++++++++++模板配置+++++++++++++++++++++++++++++++++++

define('TPL_EXT'		,	'.htm');										//模板文件后缀

define('SMARTY'			,	true);											//开启smarty模板:true   开启普通PHP模板:false

define('SMARTY_CACHE'	,	false);											//这里是调试时设为false,发布时请使用true

define('SMARTY_CHECK'	,	true);

define('LEFT_TAG'		,	'<!--{');

define('RIGHT_TAG'		,	'}-->');

//+++++++++++++++++++++++++++++++++会话配置+++++++++++++++++++++++++++++++++++

$config["session"]["expire"] = 7200;

$config["session"]["cookie_name"] = "SESSION";

$config["session"]["cookie_prefix"] = "MP_";

$config["session"]["cookie_path"] = "/";

$config["session"]["entry_key"] = "MP@)!)";									//sid加密串

$config["session"]["cookie_domain"] = "localhost";

$config["session"]["storage"] = "db";										//存储方式db/mem

$config["session"]["table"] = "test_sessions";

/***
 * CREATE TABLE `test_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0' COMMENT '会话标志',
  `data` text COMMENT '会话内容',
  `expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`session_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站点会话'
 */


//+++++++++++++++++++++++++++++++++内存配置+++++++++++++++++++++++++++++++++++

$config["memcache"] = array(array('127.0.0.1',11211));

$config["redis"]["host"] = '127.0.0.1';

$config["redis"]["port"] = 6379;

$config["redis"]["pconnect"] = false;

$config["redis"]["prefix"] = 'test_';

$config["redis"]["auth"] = 'redis';

//+++++++++++++++++++++++++++++++++邮件服务器配置+++++++++++++++++++++++++++++++++++

$config["mail"]["host"] = 'smtp.exmail.qq.com';

$config["mail"]["username"] = '84086365@qq.com';

$config["mail"]["password"] = '123456';

$config["mail"]["name"] = '邮箱标题';

$config["mail"]["email"] = '84086365@qq.com';

$config["mail"]["charset"] = 'UTF-8';

//+++++++++++++++++++++++++++++++++短信发送配置（中国移动接口）+++++++++++++++++++++++++++++++++++

$config["sms"]["sdk"] = '1340000000';

$config["sms"]["code"] = '123456';

$config["sms"]["sub_code"] = '1234';

//+++++++++++++++++++++++++++++++++支付系统配置+++++++++++++++++++++++++++++++++++

$config["payment"]["alipay"]["username"] = '84086365@qq.com';

$config["payment"]["bill"]["username"] = '84086365@qq.com';

$config["payment"]["tenpay"]["username"] = '84086365@qq.com';


?>