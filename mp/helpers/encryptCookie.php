<?php
/**
 +------------------------------------------------------------------------------
 * encryptCookie helper
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */
class encryptCookie_helper  {
	
	/* Public Variables */
	
	var $checkCode = '3bec1e5328b31bd65c50ca8a584efb2f'; //md5('xlkj');
	
	var $expires = 2592000; 
	
	function __construct(){
		$this->agent_ip = $_SERVER['HTTP_USER_AGENT'].$_SERVER['REMOTE_ADDR'];
	}
	
	/**
	 * encode
	 * @param $key
	 * @param $value
	 */
	function _encodeMP ( $key = 'loginname', $value = '' ) {
		
		
		$value = $this->agent_ip . $value;
		
		$key = md5($this->agent_ip.$key);
		
		$_md5checkCode = substr($this->checkCode,8,16);
		
		$value = $_md5checkCode.$value; //16 verification code + cookie value (minimum 5)
		
		$minNmu = 16 + 5;
		
		$value5 = substr($value,0,$minNmu);
		
		$valueOther = substr($value,$minNmu);
		
		$value5Arr = str_split($value5);
		
		$splitLength = ceil(32/$minNmu)-1;
		
		$_md5 = md5 ( uniqid ( rand (), true ) );
		
		$_md5Other = md5 ( $_md5 );
		
		$str = '';
		
		for($i = 0; $i < $minNmu; $i++){
			
			$str .= substr($_md5,$splitLength*$i,$splitLength).$value5Arr[$i];
			
		}
		
		$value = rand(1000,9999).$str.$valueOther.rand(1000,9999);

		$value = base64_encode ( $value.$_md5Other );
		
		setcookie(
			$key,
			$value,
			$this->expires
		);
		
	}
	
	/**
	 * decode
	 * @param $key
	 */
	function _decodeMP ( $key = 'loginname' ) {
		
		$key = md5($this->agent_ip.$key);
		
		$value = $_COOKIE [$key] ;
		
		if (!empty($value) && strlen($value) >= 21){
			
			$minNmu = 16 + 5;
			
			$RandStrRand = base64_decode ( $value ); 
			
			$length = strlen($RandStrRand);
			
			$str = substr($RandStrRand,4,$length-40);
			
			$_md5length = 32 - 32%$minNmu;
			
			$valueOther = substr($str,$minNmu+$_md5length);
			
			$str = substr($str,0,$minNmu+$_md5length);
			
			$splitLength = ceil(32/$minNmu)-1;
			
			$strArr = str_split($str,$splitLength+1); 
			
			$v = '';
			
			for($i = 0; $i < $minNmu; $i++){
				
				$v .= substr($strArr[$i],-1);
				
			}
			if(substr($v,0,16) == substr($this->checkCode,8,16)){
				
				$value = substr($v.$valueOther,16);
				
				$value1 = str_replace($this->agent_ip, '', $value);
				
				if ($this->agent_ip . $value1 == $value)
				return $value1;
				else return false;
				
			}else return false;
			
		}else{
			
			return false;
			
		}
		
	}
	
	function _unsetMP( $key = 'loginname' ){
		
		$key = md5($this->agent_ip.$key);
		
		setcookie(
			$key,
			'',
			$this->expires
		);
	}

}