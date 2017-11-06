<?php
/**
 +------------------------------------------------------------------------------
 * MongoDB plugin
 +------------------------------------------------------------------------------   
 + 格式=>("mongodb://用户名:密码 @地址:端口/默认指定数据库",参数)
	 + $conn=new Mongo(); #连接本地主机,默认端口.
	 + $conn=new Mongo("172.21.15.69″); #连接远程主机
	 + $conn=new Mongo("xiaocai.loc:27017″); #连接指定端口远程主机
	 + $conn=new Mongo("xiaocai.loc",array("replicaSet"=>true)); #负载均衡
	 + $conn=new Mongo("xiaocai.loc",array("persist"=>"t")); #持久连接
	 + $conn=new Mongo("mongodb://sa:123@localhost"); #带用户名密码
	 + $conn=new Mongo("mongodb://localhost:27017,localhost:27018″); #连接多个服务器
	 + $conn=new Mongo("mongodb:///tmp/mongo-27017.sock"); #域套接字
	 + $conn=new Mongo("mongodb://admin_miss:miss@localhost:27017/test",array('persist'=>'p',"replicaSet"=>true)); #完整
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------


 */

class mongo_plugin {

	public $db = "default";
	
	public $collection = "default";

    public function __construct() {
    	$this->conn();
    }

    /**
     * 连接mongodb
	 
	 
     * 
     */
    function conn($mongo = "default"){
    	
    	$this->mongo = $mongo;
    	if (empty($this->mongos) || !in_array($mongo, $this->mongos) ){
			$this->mongos[] = $mongo;
		}
		if (!is_object($this->{$this->mongo}))   {
			
			$config_mongo = config_item("mongo", $mongo);
			$host = $config_mongo['host'];
			unset($config_mongo['host']);
			
			try {
				$this->{$this->mongo} = new Mongo("mongodb://{$host}", $config_mongo);	
			} catch(MongoConnectionException $e) {
				load("log", "stroage")->write(" code: " . $e->getCode()."; \n msg: " . $e->getMessage());
				die("Failed to connect to mongodb ".$e->getMessage());
			}
		
    	} 
    	return $this;
    	
    }
    
    /**
     * database
     */
    function db($db = ""){
    	$this->db = $db;
    	return $this;	
    }
    
    /**
     * collection
     * mongodb object
     */
    function collection($collection = ""){

    	$this->collection = $collection;		
    	return $this;
    	
    }
    
    /**
     * core class
     */
    function mongoclient($mongo = ""){
    	if ($mongo) $this->conn($mongo);
    	return $this->{$this->mongo};
    }
    
    function mongodb($db = ""){
    	if ($db) $this->db($db);
    	return $this->{$this->mongo}->{$this->db};
    }
    
    function mongocollection($collection = ""){
    	if ($collection) $this->collection($collection);
    	return $this->{$this->mongo}->{$this->db}->{$this->collection};
    }
    
	function mongocursor($where = array(), $fields = array()){
    	$cursor = $this->mongocollection()->find($where, $fields);
		return $cursor;
    }
    
	/**
	 * 插入
	 * http://www.php.net/manual/zh/mongocollection.insert.php
	 * @param array(‘safe’=>false,’fsync’=>false,’timeout’=>10000)
	 * 安全模式插入,用于等待MongoDB完成操作,以便确定是否成功
	 * 参数:safe:默认false,是否安全写入
	 * batch 默认false  批量插入
	   fsync:默认false,是否强制插入到同步到磁盘
	   timeout:超时时间(毫秒)
	 */
	
	/**
	 * 更新
	 * 
	 * $this->mongocollection()->update($where,array('$set'=>$newdata)); #$set:让某节点等于给定值,类似的还有$pull $pullAll $pop $inc,在后面慢慢说明用法
	 * $this->mongocollection()->update($where,$newdata); 替换所有字段为$newdata
	 * $options = array(‘multiple’=>true) 批量更新
	 * $this->mongocollection()->update($where,array(’$inc’=>array(’91u’=>-5))); 自动累加  
	 * $this->mongocollection()->update($where,array(‘$unset’=>’column_exp’)); 删除节点 
	 */
	
	/**
	 * 如果参数不具有 _id 的键或者属性，将会创建并赋值一个新的 MongoId 实例
	 */
	function save($data = array(), $where = array(), $options = array()){
		if (!empty($where)){
			$options = array_merge(array('multiple' => true), $options); //默认批量更新
			$last_id = $this->mongocollection()->update($where, $data, $options);
		}else{
			if (!empty($options["batch"])){
				$last_id = $this->mongocollection()->batchInsert($data, $options);
			}else{
				$last_id = $this->mongocollection()->insert($data, $options);
			}
		}
		return $last_id;
	}
	
	/**
	 * 删除
	 * $this->mongocollection()->remove(array(‘column_name’=>’col399′));
	 * $this->mongocollection()->remove(); #清空集合
	 * 删除指定MongoId 
	 * $id = new MongoId(“4d638ea1d549a02801000011″);
	 * $this->mongocollection()->remove(array(‘_id’=>(object)$id));
	 */
	function delete($where = array()){
		return $this->mongocollection()->remove($where);
	}
	
	/**
	 * 删除数据库/表
	 * Enter description here ...
	 */
	function drop($collection = ''){
		if (empty($this->db))die('no database to drop');
		$drop = $collection ? "dropCollection" : "drop";
		return $this->{$this->mongo}->{$this->db}->{$drop}($collection);
	}
	
	/**
	 * 统计
	 * $this->mongocollection()->count(); #全部
	 * $this->mongocollection()->count(array(‘type’=>’user’)); #可以加上条件
	 * $this->mongocollection()->count(array(‘age’=>array(‘$gt’=>50,’$lte’=>74))); #大于50小于等于74
	 * 注:$gt为大于、$gte为大于等于、$lt为小于、$lte为小于等于、$ne为不等于、$exists不存在
	 * $this->mongocollection()->find()->limit(5)->skip(0)->count(true); #获得实际返回的结果数
	 */

	
	
	/**
	 * 查询
	 * 集合中所有文档 
	 * $this->mongocollection()->find()->snapshot();
	 * 注意:
	 * 在我们做了find()操作，获得$cursor游标之后，这个游标还是动态的.
	 * 换句话说,在我find()之后,到我的游标循环完成这段时间,如果再有符合条件的记录被插入到collection,那么这些记录也会被$cursor 获得.
	 * 如果你想在获得$cursor之后的结果集不变化,需要这样做：
	 * $this->mongocollection()->find()->snapshot();
	 * 查询一条数据  $cursor = $this->mongocollection()->findOne();
	 * 注意:findOne()获得结果集后不能使用snapshot(),fields()等函数;
	 * age,type 列不显示   只显示user 列 
	 * $this->mongocollection()->find()->fields(array(“age”=>false,”type”=>false,“user”=>true));
	 * (存在type,age节点) and age!=0 and age<50 
	 * $where=array(‘type’=>array(‘$exists’=>true),’age’=>array(‘$ne’=>0,’$lt’=>50,’$exists’=>true));
	 * $this->mongocollection()->find($where);
	 * 分页获取结果集   $this->mongocollection()->find()->limit(5)->skip(0);
	 * 排序   $this->mongocollection()->find()->sort(array(‘age’=>-1,’type’=>1)); ##1表示降序 -1表示升序,参数的先后影响排序顺序
	 * 详见http://www.bumao.com/index.php/201008/mongo_php_cursor.html
	 */
	
	function find($where = array(), $fields = array()){
	
		$cursor = $this->mongocollection()->find($where, $fields);
		return $cursor;
	
	}

	/**
	 * 
	 * cursor to array
	 */
	function data($cursor, $id = false){
		if (empty($cursor))return false;
		$result = array();
		foreach ($cursor as $key => $value) {
			if ($id){
				$value['_id'] = (array)$value['_id'];
				$value['_id'] = $value['_id']['$id'];
			}
			$result[] = $value;
		}
		return count($result) == 1 ? $result[0] : $result;
	}
	
	
	/**
	 * cache::cache::get
	 */
	function get($key = ''){
	
		$value = $this->{$this->mongo}->cache->cache->findOne(array("_id" => $key));
		if (empty($value))return false;
		if (isset($value["expire"]) && $value["expire"] <= time() )	{
			$this->{$this->mongo}->cache->cache->remove(array("_id" => $key));
			return FALSE;
		}else{
			return $value[$key];
		}
		
	}
	
	/*
	 * cache::cache::set
	 */
	function set($key = '', $value = '', $expire = FALSE){
		//$_id= $this->mongoid($key);
		if ($expire > 0){
			$expire = $expire + time();
			$data = array('_id' => $key, $key => $value, 'expire' => $expire);	
		}else{
			$data = array('_id' => $key, $key => $value);
		}
		return $this->{$this->mongo}->cache->cache->save($data);
	}
	
	/*
	 * cache::cache::clear
	 */
	function clear($key = ''){
		
		if ($key){
			$this->{$this->mongo}->cache->cache->remove(array("_id" => $_id));
		}else{
			$this->{$this->mongo}->cache->cache->remove(array('expire' => array('$lte' => time())));
		}
	}
	

	
	/**
	 * 索引  
	 * $this->mongocollection()->ensureIndex(array(‘age’ => 1,’type’=>-1)); #1表示降序 -1表示升序
	 * $this->mongocollection()->ensureIndex(array(‘age’ => 1,’type’=>-1),array(‘background’=>true)); #索引的创建放在后台运行(默认是同步运行)
	 * $this->mongocollection()->ensureIndex(array(‘age’ => 1,’type’=>-1),array(‘unique’=>true)); #该索引是唯一的
	 * 详见:http://www.php.net/manualen/mongo/collection.ensureindex.php
	 */
	
	function index($fields = array(), $options = array()){
		return $this->mongocollection()->ensureIndex($fields, $options);
	}
	
	/**
	 * MongoLog::
	 */
	function log(){
	
	}
	
	/**
	 * console
	 */
	function console(){
		MongoConsole::getInstance()->top($this->{$this->mongo});
	}
	
	/**
	 * file storage
	 * new MongoBinData(file_get_contents("gravatar.jpg"), MongoBinData::GENERIC)
	 */
	
	/**
	 * image storage
	 */
	
    /**
     * close db
     */
	public function close($mongo = ''){
		
		if (!$mongo)$mongo = $this->mongo ;
		
		if (is_object($this->{$mongo})){
		
			$this->{$mongo}->close();
		}
		
		unset($this->{$mongo});
		
		$this->mongos = array_diff($this->mongos, array($mongo));
	}

	
	public function close_all(){
	
		foreach ($this->mongos as $mongo){
			
			$this->close($mongo);
			
		}
		
	}
	
    public function __destruct() {
    	
        $this->close_all();
    
    }

      
		
  
}

/***
 	* console 
 	* rows
 	* size
 	* tables
 	* dbs
 	* index
 	*/
class MongoConsole	{

	static $instance = null;

	static function getInstance(){
		if (self::$instance == null){
			self::$instance = new MongoConsole();
		}
		return self::$instance;
	}

	function rows(){
		
	}
	
	function top($mongo = null){
		$this->mongo = $mongo;
	}

}
	

