<?php 
/**
 +------------------------------------------------------------------------------
 * Memory storage 
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Memory extends Mp {
    
    function __construct(){
    	$this->set_saver();
    }

	function mark($mark = ''){
		$this->mark = $mark;
		$this->master->mark = $mark;
		return $this;
	}
	
	function push($key, $value){
		
		$this->master->push($key, $value);
		if ($this->slave) $this->slave->push($key, $value);
	}
    
	function pop($key = ''){
		$value = $this->master->pop($key);
		if (!$value && $this->slave) {
			$value = $this->slave->pop($key);
		}
		return $value;
	}
	
	function get($key = ''){	
		$value = $this->master->get($key);
		if (!$value && $this->slave) {
			$value = $this->slave->get($key);
		}
		return $value;
	}
    
	function set($key, $value, $expire=false){
		
		$this->master->set($key, $value, $expire);
		if ($this->slave) $this->slave->set($key, $value, $expire);
	}

	function delete($key = ''){	
		
		$this->master->delete($key);
		if ($this->slave) $this->slave->delete($key);
		
	}

    function set_saver($saver = "redis"){
//    	if ($saver == "mem"){
//    		$master_config = config_item("memcache","master");
//    		$slave_config = config_item("memcache","slave");
//    	}else{
//    		$master_config = config_item("redis","master");
//    		$slave_config = config_item("redis","slave");
//    	}
		if (!$this->master){
	    	$master_config = config_item("redis","master");
	    	$slave_config = config_item("redis","slave");
	    	$saver_class = "redis_saver";
	    	$this->master = new $saver_class ( $master_config );
	    	if ($slave_config)
	    	$this->slave = new $saver_class ( $slave_config );
		}
    	return $this; 
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
	function set_counter( $key = '', $offset = 1, $time = 0 );
}

//	redis engine

class redis_saver implements imemory_saver{  
	
    public function __construct( $redis_config = array() ){
		if (empty($redis_config)) $redis_config = config_item("redis", "master");
    	$pconnect = $redis_config["pconnect"];
    	$host = $redis_config["host"];
    	$port = $redis_config["port"];
    	$prefix = $redis_config["prefix"];
    	$auth = $redis_config["auth"];
    	
    	if (!is_object($this->master)) {
            
	        $this->master = new Redis();
	        
	        if ($pconnect) {
	        	$this->master->pconnect($host, $port);
	        } else {
	        	$this->master->connect($host, $port);
	        }
	        
	        $this->master->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
	        $this->master->setOption(Redis::OPT_PREFIX, $prefix);
        }
    	if ( !empty($auth) ){
        	$this->master->auth($auth);
        }
        return $this->master;
    }
    
    
	function push($key = '', $value = ''){
		return $this->master->rPush($key, mp_encode($value));
	}
	
	
	function pop($key = ''){
		$data = $this->master->lPop($key);
		//load("log","storage")->write($data);
		if($this->mark == 'sy_ads'){
			return $data;
		}else{
			return mp_decode($data);
		}
	}
	
	
	function get($key = ''){
		$data = $this->master->get($key);
		if($this->mark == 'sy_ads'){
                        return $data;
         }else{
			return mp_decode($data);
		}
	}
	
	function set($key = '', $value = '', $expire = false){
		$this->master->set($key, mp_encode($value));
		if ($expire !== false) {
			$this->master->expire($key, $expire);
		}
	}
	
	function delete($key = '') {
		return $this->master->delete($key);
	}
	
	
    function instance(){
    	return $this->master;
    }
    
    function set_counter( $key = '', $offset = 1, $time = 0 ){return false;}
}

//memcached engine
class mem_saver  implements imemory_saver {
	
	public function __construct($mem_config = array()){
		if (empty($mem_config)) $mem_config = config_item("memcache", "master");
		
		if (!is_object($this->master))  {
			
        	if (config_item("memcache","d")){
		        $this->master = new Memcached();
				$this->master->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
				$this->master->setOption(Memcached::OPT_HASH, Memcached::HASH_DEFAULT);
				$this->master->addServers($mem_config);
				
        	}else{
        		$this->master = new Memcache;
        		foreach ($mem_config as $m_config){
					$this->master->addServer($m_config[0], $m_config[1]);
				}
        	}
			
        }
        
        return $this->master;
	}
	
	function get($key = '') {
		return $this->master->get($key);
	}
	
	function set($key = '', $value = '', $expire = false) {
		if ($expire !== false){
			if (config_item("memcache","d"))$this->master->set($key, $value, $expire);	
			else return $this->master->set($key, $value, MEMCACHE_COMPRESSED, $expire);	
		}else return $this->master->set($key, $value);	
	}
	
	
	/**
	* Message queue push
	* @param string $key
	* @param mixed $value
	* @return bool
	*/
	function push($key = '', $value = ''){
		
		$w_key = $key . 'Write';  
		$v_key = $key . $this->set_counter($w_key, 1);  
		return $this->set( $v_key, $value );
		
	}
	
	/**
	* Message queue pop
	* @param string $key
	* @param int $max  
	* @return array
	*/ 
	function pop($key = ''){
		
 		$out = false;
		$r_key = $key . 'Read';  
		$w_key = $key . 'Write';  
		$r_p   = $this->set_counter( $r_key, 0 );//read pointer  
		$w_p   = $this->set_counter( $w_key, 0 );//write pointer
		
		if( $r_p == 0 ) $r_p = 1;  
		if( $w_p >= $r_p ){  
			$v_key = $key . $r_p;  
			$r_p = $this->set_counter( $r_key, 1 );  
			$out = $this->get( $v_key );  
			$this->delete( $v_key );  
		} 
		
		return $out; 
		
	}
	
	function delete($key = '') {
		return $this->master->delete($key);
	}
    
	
    function instance(){
    	return $this->master;
    }
    
     

	/**
	*  Counter, increase the count and returns a new count
	* @param string $key   
	* @param int $offset   
	* @param int $time     
	* @return int/false   
	*/  
	function set_counter( $key = '', $offset = 1, $time = 0 ){  
		 
		$val = $this->master->get($key);  
		if( !is_numeric($val) || $val < 0 ){  
			$ret = $this->master->set( $key, 0, $time );  
			if( !$ret ) return false;  
			$val = 0;  
		}  
		
		$offset = intval( $offset );  
		if( $offset > 0 ){  
			return $this->master->increment( $key, $offset );  
		}elseif( $offset < 0 ){  
			return $this->master->decrement( $key, -$offset );  
		}  
		
		return $val;  
	}  
 
}

