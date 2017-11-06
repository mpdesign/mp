<?php
/**
 +------------------------------------------------------------------------------
 * Core Mp
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

abstract class Mp {

	
	
	function __construct(){
		$this->beforeFilter();
	}
	
	function __get($key){
			
		if(in_array($key, array('acl', 'filter', 'tree')) ){
			
			return load($key, 'behavior');
			
		}elseif(in_array($key, array('log', 'db', 'memory', 'session', 'cookie')) ){
			
			return load($key, 'storage');
			
		}
		
		return $this->{$key};
		
	}	
	
	function __destruct(){
		
		$this->afterFilter();
		
	}

/**
 * Called before the controller action.
 *
 * @access public
 */
	function beforeFilter(){}

/**
 * Called after the controller action is run and rendered.
 *
 * @access public
 */
	function afterFilter(){}

	function afterRun(){}
	

}
