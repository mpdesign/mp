<?php 
/**
 +------------------------------------------------------------------------------
 * Cookie storage
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Cookie extends Mp {
	
	/**
	 * 
	 * @param string $name cookie name
	 * @param string $value cookie vlue
	 * @param int $expire cookie expire after $expire seconds
	 * @param string $path Server path
	 * @param string $domain
	 * @param bool $secure
	 */
	function set($name, $value, $expire = 0, $path = '/', $domain = '', $secure = false){
		
		if (!$domain)$domain = config_item('cookie', 'domain');
		
		if ($expire < 1)$expire = time() + config_item('cookie', 'expire');
		
		else $expire = time() + $expire;
		
		return setcookie($name, $value, $expire, $path, $domain, $secure);
		
	}
	
	function get($name = ''){
		
		return $_COOKIE[$name];
		
	}
	
	function delete($name = ''){
		setcookie($name, "", time() - 3600);
	}
	
//	set cookie value is array
//	setcookie("cookie[three]","cookiethree");
//	setcookie("cookie[two]","cookietwo");
//	setcookie("cookie[one]","cookieone");
}
