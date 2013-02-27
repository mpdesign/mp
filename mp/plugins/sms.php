<?php
/**
 +------------------------------------------------------------------------------
 * Sms plugin
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class sms_plugin {
	
	function __construct(){
		
	}
	
	function send($phones, $message = ''){
		
		$phones = (is_array($phones) ? implode(',', $phones) : $phones);
		
		$msg = urlencode($message);
		
		$sms_config = config_item('sms');
		
		
		$result = file_get_contents("http://vip.4001185185.com/sdk/smssdk!mt.action?sdk={$sms_config["sdk"]}&code={$sms_config["code"]}&phones={$phones}&msg={$msg}&subcode={$sms_config["subcode"]}");
		
		if ($result == '发送成功') {
			
			return true;
		} 
		
		
		return $result;
	}
	
}