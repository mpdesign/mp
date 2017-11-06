<?php
/**
 +------------------------------------------------------------------------------
 * Common functions
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

	/**
	  * $string： 明文 或 密文  
	  * $operation：DECODE表示解密,其它表示加密  
	  * $key： 密匙  
	  * $expiry：密文有效期  
	 **/
	function authcode($string, $operation = 'DECODE', $key = AUTHCODE, $expiry = 0) {	
	        // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙  
	        $ckey_length = 4;  
	        // 密匙  
	        $key = md5($key);  
	        // 密匙a会参与加解密  
	        $keya = md5(substr($key, 0, 16));  
	        // 密匙b会用来做数据完整性验证  
	        $keyb = md5(substr($key, 16, 16));  
	        // 密匙c用于变化生成的密文  
	        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';  
	        // 参与运算的密匙  
	        $cryptkey = $keya.md5($keya.$keyc);  
	        $key_length = strlen($cryptkey);  
	        // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，解密时会通过这个密匙验证数据完整性  
	        // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确  
	        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;  
	        $string_length = strlen($string);  
			
	        $result = '';  
	        $box = range(0, 255);  
	        $rndkey = array();  
	        // 产生密匙簿  
	        for($i = 0; $i <= 255; $i++) {  
	            $rndkey[$i] = ord($cryptkey[$i % $key_length]);  
	        }  
	        // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度  
	        for($j = $i = 0; $i < 256; $i++) {  
	            $j = ($j + $box[$i] + $rndkey[$i]) % 256;  
	            $tmp = $box[$i];  
	            $box[$i] = $box[$j];  
	            $box[$j] = $tmp;  
	        }  
	        // 核心加解密部分  
	        for($a = $j = $i = 0; $i < $string_length; $i++) {  
	            $a = ($a + 1) % 256;  
	          	$j = ($j + $box[$a]) % 256;  
	          
	            $tmp = $box[$a];  
	            $box[$a] = $box[$j];  
	            $box[$j] = $tmp;  
	            // 从密匙簿得出密匙进行异或，再转成字符  
	            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));  
	        }  
	
	        if($operation == 'DECODE') {  
	            // substr($result, 0, 10) == 0 验证数据有效性  
	            // substr($result, 0, 10) - time() > 0 验证数据有效性  
	            // substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16) 验证数据完整性  
	            // 验证数据有效性，请看未加密明文的格式  
	            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {  
	                return substr($result, 26);  
	            } else {  
	                return '';  
	            }  
	        } else {  
	            // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因  
	            // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码  
	            return $keyc.str_replace('=', '', base64_encode($result));  
	        }  
	}

	
	function load($name = '', $type = 'lib', $param = null)  {
		$loads = array(
			'lib' => array('path' => LIB_PATH, 'class' => $name.'_lib'),
			'model' => array('path' => MOD_PATH, 'class' => $name.'_model'),
			'helper' => array('path' => HELPER_PATH, 'class' => $name.'_helper'),
			'plugin' => array('path' => PLUG_PATH, 'class' => $name.'_plugin'),
			'storage' => array('path' => CORE_PATH . "/storage", 'class' => ucfirst($name)),
			'behavior' => array('path' => CORE_PATH . "/behavior", 'class' => ucfirst($name))
		);
		static $obj;
		if($name == 'db'){
			$class = 'db2';
		}else{

			$class = $loads[$type]['class'];
		}
		if(isset($obj[$class]) && $param && $param_re = $obj[$class][md5(serialize($param))])
		    return $param_re;
		if(isset($obj[$class]) && !$param && is_object($obj[$class]['noparam'])) {
			return $obj[$class]['noparam'];
		}
		$f_path	= $loads[$type]['path'] . "/{$name}.php";
		if (file_exists($f_path)){
			include_once($f_path);
		}else{
			$f_path = MP_PATH . '/' . $type. 's/' . $name . '.php';
			if (is_file($f_path)){
				include_once($f_path);										
			} else {
				echo 'Load error,File ',$name,'.php no found.';
				exit;
			}
		}
			
		if($param) {
			$re_obj = new $class($param);
			$obj[$class][md5(serialize($param))] = $re_obj;				
			return $re_obj;
		} else {
			if($name=='TreeModel'){
//				echo $class;exit;
			}
			$obj[$class]['noparam'] = new $class();
			return 	$obj[$class]['noparam'];							
		}
	}

	function ip_address($is_long = false){
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['REMOTE_ADDR']))
		{
			$proxies = preg_split('/[\s,]/', '118.26.201.2,118.26.201.3,118.26.201.4,118.26.201.5,192.168.1.3,192.168.1.4,192.168.1.5,192.168.1.6,192.168.1.14', -1, PREG_SPLIT_NO_EMPTY);
			$proxies = is_array($proxies) ? $proxies : array($proxies);

			$ip_address = in_array($_SERVER['REMOTE_ADDR'], $proxies) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		}
		elseif (!empty($_SERVER['REMOTE_ADDR']) AND !empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['REMOTE_ADDR']))
		{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		elseif (!empty($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if ($ip_address === false)
		{
			$ip_address = '0.0.0.0';
			if ($is_long) {
				return ip2long($ip_address);
			}
			return $ip_address;
		}

		if (strpos($ip_address, ',') !== false)
		{
			$x = explode(',', $ip_address);
			$ip_address = trim(end($x));
		}

		if ($is_long) {
			return ip2long($ip_address);
		}
		
		return $ip_address;
	}
	

if ( ! function_exists('remove_invisible_characters')) {
	function remove_invisible_characters($str, $url_encoded = TRUE) {
		$non_displayables = array();
		if ($url_encoded) {
			$non_displayables[] = '/%0[0-8bcef]/';	
			$non_displayables[] = '/%1[0-9a-f]/';	
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

		do {
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}	


/**
 * 
 * get global config item ...
 * @param string $item
 * @param string $key
 */	
function config_item($item = '', $key = '') {
	static $_config_item = array();

	
	if (isset($GLOBALS["config"])){
		$config = $GLOBALS["config"];
	}else{
		global $config;
	}
	//$config = isset($GLOBALS["config"])? $GLOBALS["config"] : $config;

	if ( ! isset($config[$item])) {
		return FALSE;
	}
	$_config_item[$item] = $config[$item];
	
	if ($key){
		return isset($_config_item[$item][$key]) ? $_config_item[$item][$key] : false;
	}else
		return $_config_item[$item];
}

/***
 * set global config item as value 
 * ADD array_merge at 2014-04-24
 */
function config_write($item = '', $value = null) {
	
	if ($item && $value){
	
		if (isset($GLOBALS["config"])){
			if (isset($GLOBALS["config"][$item]) && is_array($GLOBALS["config"][$item]) && is_array($value)){
				$GLOBALS["config"][$item] = array_merge($GLOBALS["config"][$item], $value);
			}else{
				$GLOBALS["config"][$item] = $value;
			}
			
			
		}else{
			
			global $config;
			if (isset($config[$item]) && is_array($config[$item]) && is_array($value)){
				$config[$item] = array_merge($config[$item], $value);
			}else{
				$config[$item] = $value;
			}
			
			
		}
		return true;
		
	}else return false;

}

function mp_encode($data, $method = "json"){
	if ($data){
		if ($method == 'json')$data = json_encode($data);
		else if ($method == 'serialize') $data = serialize($data);
	}
	return $data;
}
	
function mp_decode($data, $method = "json"){
	if ($data){
		if ($method == 'json')$data = json_decode($data,true);
		else if ($method == 'serialize') $data = unserialize($data);
	}
	return $data;
}

/**
 * 
 * html header no cache
 */
function no_cache(){
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");
}

function is_ajax_request() {
	
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
	
}

	function xss_clean($params = ''){
		
		
		$patterns = array( "/\s+/i","/%20/",
					'/</i',
					'/>/i',
		 			"/script/i",
					"/iframe/i",
					"/expression/i" // CSS and IE
					//"/vbscript/i"	 // IE, surprise!
		);
		
		$replacements = array('','','','','','','');
		
		$params = preg_replace($patterns, $replacements, $params);
		
		return $params;
	}

?>