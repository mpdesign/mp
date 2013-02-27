<?php

/**
 +------------------------------------------------------------------------------
 * Mailer plugin
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * 
 * load('mail')->AddAddress($email);
 * load('mail')->Subject = $subject;
 * load('mail')->MsgHTML($content);
 * $result = load('mail')->Send();
 +------------------------------------------------------------------------------
 */

include_once MP_PATH.'/plugins/phpmailer/class.phpmailer';
include_once MP_PATH.'/plugins/phpmailer/class.smtp';
include_once MP_PATH.'/plugins/phpmailer/class.pop3';

class mail_plugin {
	
	function __construct(){
		if ($this->_mailer == null){
			
			$this->_mailer = new PHPMailer();
			
			$email_config = config_item('mail');
			
			
			$this->_mailer->CharSet = $email_config['charset'];			
			$this->_mailer->IsHTML(true);
			//$this->_mailer->IsSendmail();
			
			$this->_mailer->IsSMTP();
			
			
			$this->_mailer->Host = $email_config['host'];
			
			$this->_mailer->SMTPAuth = true;
			$this->_mailer->Username = $email_config['username'];
			$this->_mailer->Password = $email_config['password'];
		
			$this->_mailer->SetFrom($email_config['email'], $email_config['name']);
		}
		return $this->_mailer;
	}
	
}