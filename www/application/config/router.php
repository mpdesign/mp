<?php
/**
 +------------------------------------------------------------------------------
 * 路由配置文件
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Config
 +------------------------------------------------------------------------------
 */

$config["router"]["admin"]["entrance"] = substr(md5("gapisy".date("Ymd")),0,4);	//后台URL入口前缀

$config["router"]["admin"]["prefix"] = "admin";										//后台控制器文件前缀

$config["router"]["method"] = "Run";												//默认执行方法

$config["router"]["url"]['da\/v([0-9\_]+)\/(:any)'] = 'da$1/$2';							//URL重定向
$config["router"]["url"]['7([0-9a-zA-Z]+)'] = 'da2_0/click/$1';							//URL重定向
$config["router"]["url"]['8([0-9a-zA-Z]+)'] = 'da3_0/click/$1';							//URL重定向