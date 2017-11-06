<?php
/**
 +------------------------------------------------------------------------------
 * hello Shell
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Admin
 +------------------------------------------------------------------------------
 */
class helloShell extends Mp {

	function __construct( $shell = null){
		
		$this->shell = $shell;
		
	}
	
	function main(){
		echo 'Hello MPphp';
		
	}
	
}