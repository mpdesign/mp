<?php
/**
 +------------------------------------------------------------------------------
 * redis helper
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class redis_helper{

	public $rdb_conn = "master";

	public $rdb_conns = array();

    public function __construct(){
		
        $this->redis();
    }
    
    function redis($rc = 'master', $dbno = 0){

		$this->rdb_conn = 'rdb_conn_' . $rc;
		if (empty($this->rdb_conns) || (!empty($this->rdb_conns) && !in_array($this->rdb_conn, $this->rdb_conns)) ){
			$this->rdb_conns[] = $this->rdb_conn;
		}

    	if (empty($this->{$this->rdb_conn})){
			$this->{$this->rdb_conn} = new Redis();
    	}
    	
    	if ( !is_resource($this->{$this->rdb_conn}->socket) || empty($this->{$this->rdb_conn}->socket) ){
    		
	        $redis_config = config_item("redis", $rc);
	    	if (empty($redis_config))die('redis config');
	    	$pconnect = $redis_config["pconnect"];
	    	$host = $redis_config["host"];
	    	$port = $redis_config["port"];
	    	$prefix = $redis_config["prefix"];
	    	$auth = $redis_config["auth"];
	    	$db = $redis_config["db"];

			if ($pconnect) {
				$this->{$this->rdb_conn}->pconnect($host, $port, 10);
			} else {
				$this->{$this->rdb_conn}->connect($host, $port, 10);
			}
	        
	        $this->{$this->rdb_conn}->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
	        $this->{$this->rdb_conn}->setOption(Redis::OPT_PREFIX, $prefix);
	        
	    	if ( !empty($auth) ){
	        	$this->{$this->rdb_conn}->auth($auth);
	        }
	        if (!$dbno && !empty($db)) $dbno = $db;
	        $this->select($dbno);
    	}elseif($dbno > 0){
    		$this->select($dbno);
    	}

        return $this;
    	
    }
    
    function rdb(){
    	return $this->{$this->rdb_conn};
    }
    
    function close(){
    	$this->rdb()->close();
    	$this->rdb = NULL;
    	return $this;
    }
    
    function select($db = 0){
    	$this->rdb()->select($db);
    	return $this;
    }
    
	function push($key = '', $value = ''){
		return $this->rdb()->rPush($key, mp_encode($value));
	}
	
	
	function pop($key = ''){
		$data = $this->rdb()->lPop($key);
		
		return mp_decode($data);
		
	}
	
	
	function get($key = ''){
		$data = $this->rdb()->get($key);

		return mp_decode($data);
		
	}
	
	function set($key = '', $value = '', $expire = false){
		$this->rdb()->set($key, mp_encode($value));
		if ($expire !== false) {
			$this->rdb()->expire($key, $expire);
		}
	}
	
	function delete($key = '') {
		return $this->rdb()->delete($key);
	}

	function __destruct(){
		$this->close();
	}
	
}