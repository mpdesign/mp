<?php
/**
 +------------------------------------------------------------------------------
 * Session handler 
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */
class session_lib {
	var $expire					= 7200;
	var $expire_close			= false;
	var $cookie_domain			= '';
	var $cookie_name			= 'SESSION';
	var $cookie_prefix			= 'MP_';
	var $cookie_path			= '/';
	var $entry_key              = 'mp@)!)';
	
	var $sess_id				= '';
	var $sess_data				= false;
	var $is_write				= false;
	
	var $_saver					= null;
	var $storage				= 'db';

	public function __construct($params = array()) {
		// session config
		$session_config = config_item("session");
		foreach (array('expire', 'entry_key', 'cookie_name','cookie_prefix', 'cookie_path', 'storage', 'cookie_domain') as $key) {
			 if (isset($session_config[$key])) {
		        $this->$key = $session_config[$key];
		    }
		    if (isset($params[$key])) {
		        $this->$key = $params[$key];
		    }
		}
		
		// Set the cookie name
		$this->cookie_name = $this->cookie_prefix.$this->cookie_name;

		// Run the Session routine. If a session doesn't exist we'll
		// create a new one.  If it does, we'll update it.
		if (!$this->sess_read()) {
			$this->sess_create();
		}


		
		if ($this->is_write == false) {
			$this->sess_write();
		}

		// Delete expired {$this->table} if necessary
		$this->_sess_gc();
	}
        
	function sess_read() {
		
		
		if (empty($_COOKIE[$this->cookie_name])) {
			return false; 
		}
        
		

		$this->sess_id = authcode($_COOKIE[$this->cookie_name], 'DECODE', $this->entry_key);
		
		if (strlen($this->sess_id) != 32) {
			$this->destroy();
			return false;
		}
		
		
		$this->sess_data = $this->_saver()->get($this->sess_id);
		
		
		if ($this->sess_data === false) {
			return false;
		}
		// end add
		
		if (empty($this->sess_data)) {
			
			$this->sess_data = array();
		}
		
		return true;
	}

	function sess_write() {
		
		$this->is_write = true;
		
		$this->_saver()->set($this->sess_id, $this->sess_data, $this->expire);

		// update COOKIE
		$this->_set_cookie();
	}

	function sess_create() {
		$this->sess_id = '';
		while (strlen($this->sess_id) < 32) {
			$this->sess_id .= mt_rand(0, mt_getrandmax());
		}
		
		$this->sess_id = md5(uniqid($this->sess_id.ip_address(), true));
		
		$this->_saver()->add($this->sess_id, $this->expire);
		
		$this->_set_cookie();
	}


	function destroy() {	
		if (strlen($this->sess_id) == 32) {
			
			$this->_saver()->delete($this->sess_id);
		}
		
		// Kill the cookie
		// ----------------------
		$this->_set_cookie(true);
	}

	function _set_cookie($is_expire = false) {
		$expire = 0;
		if ($is_expire) {
			$expire = (time() - 31500000);
		} else {
			$expire = ($this->expire_close === true) ? 0 : $this->expire + time();
		}
		
		setcookie(
			$this->cookie_name,
			authcode($this->sess_id, 'ENCODE', $this->entry_key),
			$expire,
			$this->cookie_path,
			$this->cookie_domain,
			0
		);
	}

	function get($item = '') {
		if ( $item ){
			return ( !isset($this->sess_data[$item]) ) ? false : $this->sess_data[$item];
		}else{
			return ( ! isset($this->sess_data)) ? false : $this->sess_data;
		}
		
	}


	function set($newdata = array(), $newval = '') {
		if (is_string($newdata)) {
			$newdata = array($newdata => $newval);
		}

		if (count($newdata) > 0) {
			foreach ($newdata as $key => $val)
			{
				$this->sess_data[$key] = $val;
			}
		}

		$this->sess_write();
	}

 
	function delete($newdata = array()) {
		if (is_string($newdata)) {
			$newdata = array($newdata => '');
		}

		if (count($newdata) > 0) {
			foreach ($newdata as $key => $val)
			{
				unset($this->sess_data[$key]);
			}
		}

		$this->sess_write();
	}



 
	function _sess_gc() {
		$this->_saver()->gc();
	}
    
    /**
     * _saver method
     * 
     * Get session storage processor
     * 
     * @return object isess_saver
     */
    function _saver() {
    	if (!is_object($this->_saver))  {
        	
	        if ( $this->storage == 'db' ) {
				;
				$this->_saver = new db_sess_saver();
			} else  {
				//! extension_loaded('memcached')
				
        		$this->_saver = new mem_sess_saver();
			}
        }
        
        return $this->_saver;
    }
}


interface isess_saver {
	function get($sess_id);
	function set($sess_id, $sess_data, $expire);
	function add($sess_id, $expire);
	function delete($sess_id);
	function gc();
}

class db_sess_saver implements isess_saver {
	function __construct(){
		$session_config = config_item("session");
		$this->table = $session_config["table"];
	}
	function get($sess_id) {
		$cur_time = time();
		
		$sql = "SELECT `data` FROM `{$this->table}` WHERE `session_id`='{$sess_id}' AND `expire`>={$cur_time};";
		
		$result = load('dbmodel')->query($sql);
		
		
		$session = false;
		if (isset($result['data'])) {
			if ($result['data'] != ''){
				$custom_data = $this->_unserialize($result['data']);
	            
				if (is_array($custom_data)) {
					foreach ($custom_data as $key => $val) {
						$session[$key] = $val;
					}
				}
			}else{
				$session = array();
			}
		}
		
		return $session;
	}

 
	function set($sess_id, $sess_data, $expire) {
		$expire = time() + $expire;
		if (empty($sess_data)) {
			$sess_data = '';
		} else {
			$sess_data = $this->_serialize($sess_data);
		}
		
		$sql = "UPDATE `{$this->table}` SET `data`='{$sess_data}',`expire`={$expire} WHERE `session_id`='{$sess_id}';";
		$result = load('dbmodel')->query($sql);
		
		return $result;
	}

	// --------------------------------------------------------------------
	
	function add($sess_id, $expire) {
		$expire = time() + $expire;
		$sql = "insert into `{$this->table}`(`session_id`,`data`,`expire`) values('{$sess_id}','','{$expire}') ;";
		$result = load('dbmodel')->query($sql);
		
		return $result;
	}

	// --------------------------------------------------------------------
	
	function delete($sess_id) {

		$sql = "DELETE FROM `{$this->table}` WHERE `session_id`='{$sess_id}';";
		$result = load('dbmodel')->query($sql);
		
		return $result;
	}

	// --------------------------------------------------------------------
	
	function gc() {
		$cur_time = time();
		
		srand($cur_time);
		if ((rand() % 100) < 30) {	
			$sql = "DELETE FROM `{$this->table}` WHERE `expire`<={$cur_time};";
			load('dbmodel')->query($sql);
		}
	}

	// --------------------------------------------------------------------

  
	function _serialize($data) {
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_string($val)) {
					$data[$key] = str_replace('\\', '{{slash}}', $val);
				}
			}
		} else {
			if (is_string($data)) {
				$data = str_replace('\\', '{{slash}}', $data);
			}
		}

		return serialize($data);
	}

	// --------------------------------------------------------------------

	function _unserialize($data) {
		if (get_magic_quotes_gpc()) {
			$data = @unserialize(stripslashes($data));
		}else{
			$data = @unserialize($data);
		}
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				if (is_string($val)) {
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
			}

			return $data;
		}

		return (is_string($data)) ? str_replace('{{slash}}', '\\', $data) : $data;
	}
}

class mem_sess_saver {
	var $_memcached	= null;
	
    function __construct() {
    	$this->_memcached = load("memory")->set_saver("mem");
    }
	
	function get($sess_id) {
		return $this->_memcached->get($sess_id);
	}
	
	function set($sess_id, $sess_data, $expire) {
		
		if (!empty($sess_data)) {
			
			return $this->_memcached->set($sess_id, $sess_data, $expire);
		} else {
			
			return $this->_memcached->delete($sess_id);
		}
	}
	
	function add($sess_id, $expire) {
		return true;
	}
	
	function delete($sess_id) {
		return $this->_memcached->delete($sess_id);
	}

	// --------------------------------------------------------------------
	
	function gc() {
		return true;
	}
    

}
