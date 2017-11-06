<?php 
/**
 +------------------------------------------------------------------------------
 * Nosql storage basic memcache
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Nosql extends Mp {
    
	public $prefix1 = "oa";	//数据库
	
	public $prefix2 = "nosql";	//表
	
	public $key     = "nosql";	//字段 
	
	public $connect = FALSE;
	
    function __construct(){
    	$this->saver();
    }
	
    function saver($saver = "mem"){   	
    	$master_config = config_item("memcache","master");   	
    	$saver_class = $saver . "_saver";
    	$this->master = new $saver_class ( $master_config );
    	return $this; 
    }
       
	function connect($dbname = '', $tablename = ''){
		
		if ($dbname && $tablename){
			$this->prefix1 = $dbname;
			$this->prefix2 = $tablename;
			$this->connect = true;
				
			$push_db = $this->master->get($dbname);
			if (!$push_db){
				$this->master->set($dbname,true);
				//存储所有的库名
				$this->master->push("db", $this->prefix1);
			}
			$push_table = $this->master->get($dbname . '_' . $tablename);
			if (!$push_table){
				$this->master->set($dbname . '_' . $tablename,true);
				//存储这个库的所有表名
				$this->master->push($this->prefix1, $this->prefix2);
			}
			
		}else{
			if (!$this->connect){
				echo '未连接';exit;
			}
		}
		
		return $this;
	}
	
	//所有库
	function dbs($count = false){
		$this->connect();
		if ($count)return $this->master->count("db");
		else return $this->master->unpop("db");
	}
	//所有表
	function tables($count = false){
		$this->connect();
		if ($count)return $this->master->count($this->prefix1);
		else return $this->master->unpop($this->prefix1);
	}
	//所有键
	function keys($count = false){
		$this->connect();
		if ($count)return $this->master->count($this->prefix1 . '_' . $this->prefix2);
		else return $this->master->unpop($this->prefix1 . '_' . $this->prefix2);
	}

	//limit
	function limit($num = 0){
		$this->connect();
		$result = false;
		if($num <= 0)return false;
		$keys = $this->master->unpop($this->prefix1 . '_' . $this->prefix2);
		$i = 0;
		foreach ($keys as $key){
			if($i >= $num)break;
			$result[$key] = $this->master->get($this->prefix1 . '_' . $this->prefix2 . '_' . $key);
			$i++;
		}
		return $result;
	}
	
	
	//查找key为$fields的记录
	function find($fields = array()){
		
		$this->connect();
		if(empty($fields))return false;	
		if (is_array($fields)){
			foreach ($fields as $key){
				$result[$key] = $this->master->get($this->prefix1 . '_' . $this->prefix2 . '_' . $key);			
			}
		}else return $result = $this->master->get($this->prefix1 . '_' . $this->prefix2 . '_' . $fields);	
		return $result;
		
	}
	
	//匹配$match的所有记录
	function like($match = ''){
		$this->connect();
		$result = false;
		if(empty($match))return false;
		$keys = $this->master->unpop($this->prefix1 . '_' . $this->prefix2);
		
		foreach ($keys as $key){
			$r = $this->master->get($this->prefix1 . '_' . $this->prefix2 . '_' . $key);
			if (preg_match("/".$match."/i", $r))	$result[$key] = $r;
		}
		return $result;
		
	}
	
	//设置
	function save($data = array()){
		
		$this->connect();
		if(empty($data))return false;		
		foreach ($data as $key => $value){
			
			//存储这张表的所有键
			$push_key = $this->master->get($this->prefix1 . '_' . $this->prefix2 . '_' . $key);
			if (!$push_key){$this->master->push($this->prefix1 . '_' . $this->prefix2, $key);}
			
			$this->master->set($this->prefix1 . '_' . $this->prefix2 . '_' . $key, $value);
			
		}
		return $this;
		
	}
	
	//删除
	function delete($key = ''){
		$this->connect();
		if (!$key)return false;
		return $this->master->set($this->prefix1 . '_' . $this->prefix2 . '_' . $key);
		
	}

}


//memcached engine
class mem_saver  {
	
	var $readKey = "READ";
	var $writeKey = "WRITE";
	
	public function __construct($mem_config = array()){
		if (empty($mem_config)) $mem_config = config_item("memcache", "master");
		
		if (!is_object($this->master))  {
			
        	if (config_item("memcache","d")){
		        $this->master = new Memcached();
				$this->master->setOption(Memcached::OPT_DISTRIBUTION, Memcached::DISTRIBUTION_CONSISTENT);
				$this->master->setOption(Memcached::OPT_HASH, Memcached::HASH_DEFAULT);
				foreach ($mem_config as $m_config){
					$this->master->addServers($m_config[0], $m_config[1]);
				}
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
		if ($expire !== false)
		return $this->master->set($key, $value, MEMCACHE_COMPRESSED, $expire);	
		else return $this->master->set($key, $value);	
	}
	
	
	/**
	* Message queue push
	* @param string $key
	* @param mixed $value
	* @return bool
	*/
	function push($key = '', $value = ''){
		
		$w_key = $key . $this->writeKey;  
		$v_key = $key . $this->set_counter($w_key, 1);  
		return $this->master->set( $v_key, $value );
		
	}
	
	/**
	* Message queue pop
	* @param string $key
	* @param int $max  
	* @return array
	*/ 
	function pop($key = ''){
		
 		$out = false;
		$r_key = $key . $this->readKey;  
		$w_key = $key . $this->writeKey;  
		$r_p   = $this->set_counter( $r_key, 0 );//read pointer  
		$w_p   = $this->set_counter( $w_key, 0 );//write pointer
		
		if( $r_p == 0 ) $r_p = 1;  
		if( $w_p >= $r_p ){  
			$v_key = $key . $r_p;  
			$r_p = $this->set_counter( $r_key, 1 );  
			$out = $this->master->get( $v_key );  
			$this->master->delete( $v_key );  
		} 
		
		return $out; 
		
	}
	
	//获取队列所有数据
	function unpop($key = ''){
		
		$out = array();
		$r_key = $key . $this->readKey;  
		$w_key = $key . $this->writeKey;  
		$r_p   = $this->set_counter( $r_key, 0 );//read pointer  
		$w_p   = $this->set_counter( $w_key, 0 );//write pointer
		
		if( $r_p == 0 ) $r_p = 1;  
		for ($i == $r_p; $i <= $w_p; $i++){
			$v_key = $key . $i; 
			$out[] = $this->master->get( $v_key ); 
		}
		return $out;
	}
	
	//个数
	function count($key = ''){
		
		$r_p   = $this->set_counter( $key . $this->readKey, 0 ); 
		$w_p   = $this->set_counter( $key . $this->writeKey, 0 );
		if ($w_p < $r_p || $w_p == 0)return 0;
		return $w_p - $r_p + 1;
		
	}
	
	function delete($key = '') {
		return $this->master->delete($key);
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

