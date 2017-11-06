<?php  
/**
 +------------------------------------------------------------------------------
 * Core bootstrap
 * Handles loading of core files needed on every request
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */
define('CORE_PATH'		,	MP_PATH.'/core');								
define('CACHE_PATH'		,	APP_PATH.'/cache');								
define('LIB_PATH'		,	APP_PATH.'/libs');
define('HELPER_PATH'	,	APP_PATH.'/helpers');	
define('VIEW_PATH'		,	APP_PATH.'/views');
define('MOD_PATH'		,	APP_PATH.'/models');
define('PLUG_PATH'		,	APP_PATH.'/plugins');
define('CTRL_PATH'		,	APP_PATH.'/controllers');
defined('LOG_PATH') || define('LOG_PATH'		,	APP_PATH.'/logs');
define('CONSOLE_PATH'	,	APP_PATH.'/console');

include_once APP_PATH . '/config/router.php';
include_once CORE_PATH . '/common.php';
include_once APP_PATH . '/config/common.php';
		
include_once CORE_PATH . '/mp.php';	
include_once CORE_PATH . '/view.php';				
include_once CORE_PATH . '/controller.php';		
include_once CORE_PATH . '/model.php';	
include_once CORE_PATH . '/language.php';			
include_once CTRL_PATH . '/appController.php';	
include_once MOD_PATH . '/appModel.php';	
include_once CORE_PATH . '/dispatch.php';	