<?php
/**
 +------------------------------------------------------------------------------
 * Core Mp
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

abstract class Mp {

	function __construct(){
		$this->beforFilter();
	}
	
	function __destruct(){
		$this->afterFilter();
	}

/**
 * Called before the controller action.
 *
 * @access public
 */
	function beforFilter(){}

/**
 * Called after the controller action is run and rendered.
 *
 * @access public
 */
	function afterFilter(){}
	

}
?>