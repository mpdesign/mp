<?php  
/**
 +------------------------------------------------------------------------------
 * Core bootstrap
 * Handles loading of core files needed on every request
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */
define('CORE_PATH'		,	MP_PATH.'/core');								
define('CACHE_PATH'		,	APP_PATH.'/cache');								
define('LIB_PATH'		,	APP_PATH.'/libs');
define('HELPER_PATH'	,	APP_PATH.'/helpers');	
define('TPL_PATH'		,	APP_PATH.'/template');
define('MOD_PATH'		,	APP_PATH.'/models');
define('PLUG_PATH'		,	APP_PATH.'/plugins');
define('CTRL_PATH'		,	APP_PATH.'/controllers');
define('LOG_PATH'		,	APP_PATH.'/logs');
define('WEBROOT_PATH'	,	APP_PATH.'/webroot');

include_once CORE_PATH . '/common.php';				
include_once CORE_PATH . '/mp.php';				
include_once CORE_PATH . '/controller.php';		
include_once CORE_PATH . '/model.php';			
include_once CTRL_PATH . '/appController.php';	
include_once MOD_PATH . '/appModel.php';	
include_once CORE_PATH . '/view.php';	