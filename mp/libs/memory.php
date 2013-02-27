<?php 
/**
 +------------------------------------------------------------------------------
 * Memory processing 
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class memory_lib {
	
    protected $_saver = null;
    
    protected $_spoon = null;
    
    protected $_fifo = '';
    
    
    
	function push($key, $value){	
		$this->_saver()->push($key, $this->_spoon()->encode($value));
	}
    
	function pop($key = ''){
		return $this->_spoon()->decode($this->_saver()->pop($key));
	}
	
	function get($key = ''){	
		return $this->_spoon()->decode($this->_saver()->get($key));
	}
    
	function set($key, $value, $expire=false){
		$this->_saver()->set($key, $this->_spoon()->encode($value), $expire);
	}

	function delete($key = ''){	
		return $this->_saver()->delete($key);
	}

    function set_saver($saver = "redis"){
    	$saver_class = $saver . "_saver";
    	$this->_saver = new $saver_class ();
    	return $this; 
    }
    
    function _saver( ){
    	if (!is_object($this->_saver))  {
        	$this->_saver = new redis_saver();
        }
        
        return $this->_saver;
    }
    
    function _spoon($method = "json"){
    	if (!is_object($this->_spoon))  {
        	$this->_spoon = new spoon($method);
        }        
        return $this->_spoon;
    }

}


interface imemory_saver {
	function __construct();
	function push($key = '', $value = '');
	function pop($key = '');
	function get($key = '');
	function set($key = '', $value = '', $expire = false);
	function delete($key = '');
	function instance();
}

//	redis engine

class redis_saver implements imemory_saver{  
	
    public function __construct(){
    	$redis_config = config_item("redis");
    	$pconnect = $redis_config["pconnect"];
    	$host = $redis_config["host"];
    	$port = $redis_config["port"];
    	$prefix = $redis_config["prefix"];
    	$auth = $redis_config["auth"];
    	
    	if (!is_object($this->_redis)) {
            
	        $this->_redis = new Redis();
	        
	        if ($pconnect) {
	        	$this->_redis->pconnect($host, $port);
	        } else {
	        	$this->_redis->connect($host, $port);
	        }
	        
	        $this->_redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
	        $this->_redis->setOption(Redis::OPT_PREFIX, $prefix);
        }
    	if ( !empty($auth) ){
        	$this->_redis->auth($auth);
        }
        return $this->_redis;
    }
    
    
	function push($key = '', $value = ''){
		return $this->_redis->rPush($key, $value);
	}
	
	
	function pop($key = ''){
		return $this->_redis->lPop($key);
	}
	
	
	function get($key = ''){
		return $this->_redis->get($key);
	}
	
	function set($key = '', $value = '', $expire = false){
		$this->_redis->set($key, $value);
		if ($expire !== false) {
			$this->_redis->expire($key, $expire);
		}
	}
	
	function delete($key = '') {
		return $this->_redis->delete($key);
	}
	
	
    function instance(){
    	return $this->_redis;
    }
}

//memcached engine
class mem_saver  implements imemory_saver {
	
	public function __construct(){
		if (!is_object($this->_memcached))  {
        	if (config_item('memcached')){
		        $this->_memcached = new Memcached();
				$this->_memcached->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
				$this->_memcached->setOption(Memcached::OPT_HASH, Memcached::HASH_DEFAULT);
				foreach (config_item('memcached') as $m_config){
					$this->_memcached->addServers($m_config[0], $m_config[1]);
				}
        	}else{
        		$this->_memcached = new Memcache;
        		foreach (config_item('memcache') as $m_config){
					$this->_memcached->addServer($m_config[0], $m_config[1]);
				}
        	}
			
        }
        
        return $this->_memcached;
	}
	
	function get($key = '') {
		return $this->_memcached->get($key);
	}
	
	function set($key = '', $value = '', $expire = false) {
		if ($expire !== false)
		return $this->_memcached->set($key, $value, MEMCACHE_COMPRESSED, $expire);	
		else return $this->_memcached->set($key, $value);	
	}
	
	
	function push($key = '', $value = ''){
		return false;
	}
	
	
	function pop($key = ''){
		return false;
	}
	
	function delete($key = '') {
		return $this->_memcached->delete($key);
	}
    
	
    function instance(){
    	return $this->_memcached;
    }
}

/*
 * data spoon
 */
class spoon{
	
	function __construct($method = 'json'){
		$this->method = $method;
	}
	
	function encode($data){
		if ($this->method == 'json')$data = json_encode($data);
		else if ($this->method == 'serialize') $data = serialize($data);
		return $data;
	}
	
	function decode($data){
		if ($this->method == 'json')$data = json_decode($data,true);
		else if ($this->method == 'serialize') $data = unserialize($data);
		return $data;
	}
}