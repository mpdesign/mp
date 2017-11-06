 +------------------------------------------------------------------------------   
 * mongod --config=/home/linqm/mongodb_data/mongo.conf --logappend &
 * /home/linqm/mongodb_data/mongo.conf 配置
 * bind_ip=192.168.1.184
 * port=27017
 * logpath=/home/linqm/mongodb_data/mongodb_logs/mongodb.log
 * dbpath=/home/linqm/mongodb_data/mongodb_db
 * 有关于安装参数的说明：
 * --dbpath 是数据文件所在目录
 * --logpath 是日志文件所在文件路径，此参数必须为文件，不能为文件目录，否则会导致安装失败
 * 以上两个参数必须设置
 * --logappend 日志以追加的方式写入
 * --directoryperdb 为每个数据库建立单独的目录
 * --bind_ip 绑定服务器IP，此参数为安全起见建议使用127.0.0.1，因为如果不设置的话，远程是可以连接的
 * --port  端口号
 * --install 以服务形式安装,如果需要删除 MongoDB 服务请使用 mongod --remove,再执行 net start "MongoDB" 即可启动服务
 * --config  指定配置文件，用于加载命令行未指定的各种选项
 +------------------------------------------------------------------------------
 
@class mongo四个核心类
@link http://www.php.net/manual/zh/book.mongo.php
#链接mongo
 MongoClient {
	/* 常量 */
	const string VERSION ;
	const string DEFAULT_HOST = "localhost" ;
	const int DEFAULT_PORT = 27017 ;
	const string RP_PRIMARY = "primary" ;
	const string RP_PRIMARY_PREFERRED = "primaryPreferred" ;
	const string RP_SECONDARY = "secondary" ;
	const string RP_SECONDARY_PREFERRED = "secondaryPreferred" ;
	const string RP_NEAREST = "nearest" ;
	/* 属性 */
	public boolean $connected = FALSE ;
	public string $status = NULL ;
	protected string $server = NULL ;
	protected boolean $persistent = NULL ;
	/* 方法 */
	public __construct ([ string $server = "mongodb://localhost:27017" [, array $options = array("connect" => TRUE) ]] )
	public bool close ([ boolean|string $connection ] )
	public bool connect ( void )
	public array dropDB ( mixed $db )
	public MongoDB __get ( string $dbname )
	public static array getConnections ( void )
	public array getHosts ( void )
	public array getReadPreference ( void )
	public array getWriteConcern ( void )
	public bool killCursor ( string $server_hash , int|MongoInt64 $id )
	public array listDBs ( void )
	public MongoCollection selectCollection ( string $db , string $collection )
	public MongoDB selectDB ( string $name )
	public bool setReadPreference ( string $read_preference [, array $tags ] )
	public bool setWriteConcern ( mixed $w [, int $wtimeout ] )
	public string __toString ( void )
}

#选择数据库
  MongoDB {
	/* 常量 */
	const int PROFILING_OFF = 0 ;
	const int PROFILING_SLOW = 1 ;
	const int PROFILING_ON = 2 ;
	/* Fields */
	public integer $w = 1 ;
	public integer $wtimeout = 10000 ;
	/* 方法 */
	public array authenticate ( string $username , string $password )
	public array command ( array $command [, array $options = array() ] )
	public __construct ( MongoClient $conn , string $name )
	public MongoCollection createCollection ( string $name [, array $options ] )
	public array createDBRef ( string $collection , mixed $document_or_id )
	public array drop ( void )
	public array dropCollection ( mixed $coll )
	public array execute ( mixed $code [, array $args = array() ] )
	public bool forceError ( void )
	public MongoCollection __get ( string $name )
	public array getCollectionNames ([ bool $includeSystemCollections = false ] )
	public array getDBRef ( array $ref )
	public MongoGridFS getGridFS ([ string $prefix = "fs" ] )
	public int getProfilingLevel ( void )
	public array getReadPreference ( void )
	public bool getSlaveOkay ( void )
	public array getWriteConcern ( void )
	public array lastError ( void )
	public array listCollections ([ bool $includeSystemCollections = false ] )
	public array prevError ( void )
	public array repair ([ bool $preserve_cloned_files = FALSE [, bool $backup_original_files = FALSE ]] )
	public array resetError ( void )
	public MongoCollection selectCollection ( string $name )
	public int setProfilingLevel ( int $level )
	public bool setReadPreference ( string $read_preference [, array $tags ] )
	public bool setSlaveOkay ([ bool $ok = true ] )
	public bool setWriteConcern ( mixed $w [, int $wtimeout ] )
	public string __toString ( void )
}
 
#选择表 
 MongoCollection {
	/* 常量 */
	const int ASCENDING = 1 ;
	const int DESCENDING = -1 ;
	/* Fields */
	public MongoDB $db = NULL ;
	public integer $w ;
	public integer $wtimeout ;
	/* 方法 */
	public array aggregate ( array $pipeline [, array $op [, array $... ]] )
	public mixed batchInsert ( array $a [, array $options = array() ] )
	public __construct ( MongoDB $db , string $name )
	public int count ([ array $query = array() [, int $limit = 0 [, int $skip = 0 ]]] )
	public array createDBRef ( mixed $document_or_id )
	public array deleteIndex ( string|array $keys )
	public array deleteIndexes ( void )
	public array distinct ( string $key [, array $query ] )
	public array drop ( void )
	public bool ensureIndex ( string|array $key|keys [, array $options = array() ] )
	public MongoCursor find ([ array $query = array() [, array $fields = array() ]] )
	public array findAndModify ( array $query [, array $update [, array $fields [, array $options ]]] )
	public array findOne ([ array $query = array() [, array $fields = array() ]] )
	public MongoCollection __get ( string $name )
	public array getDBRef ( array $ref )
	public array getIndexInfo ( void )
	public string getName ( void )
	public array getReadPreference ( void )
	public bool getSlaveOkay ( void )
	public array getWriteConcern ( void )
	public array group ( mixed $keys , array $initial , MongoCode $reduce [, array $options = array() ] )
	public bool|array insert ( array|object $a [, array $options = array() ] )
	public bool|array remove ([ array $criteria = array() [, array $options = array() ]] )
	public mixed save ( array|object $a [, array $options = array() ] )
	public bool setReadPreference ( string $read_preference [, array $tags ] )
	public bool setSlaveOkay ([ bool $ok = true ] )
	public bool setWriteConcern ( mixed $w [, int $wtimeout ] )
	static protected string toIndexString ( mixed $keys )
	public string __toString ( void )
	public bool|array update ( array $criteria , array $new_object [, array $options = array() ] )
	public array validate ([ bool $scan_data = FALSE ] )
}

#集合
 MongoCursor implements Iterator {
	/* Static Fields */
	static boolean $slaveOkay = FALSE ;
	static integer $timeout = 30000 ;
	/* 方法 */
	public MongoCursor addOption ( string $key , mixed $value )
	public MongoCursor awaitData ([ bool $wait = true ] )
	public MongoCursor batchSize ( int $batchSize )
	public __construct ( MongoClient $connection , string $ns [, array $query = array() [, array $fields = array() ]] )
	public int count ([ bool $foundOnly = FALSE ] )
	public array current ( void )
	public bool dead ( void )
	protected void doQuery ( void )
	public array explain ( void )
	public MongoCursor fields ( array $f )
	public array getNext ( void )
	public array getReadPreference ( void )
	public bool hasNext ( void )
	public MongoCursor hint ( mixed $index )
	public MongoCursor immortal ([ bool $liveForever = true ] )
	public array info ( void )
	public string key ( void )
	public MongoCursor limit ( int $num )
	public void next ( void )
	public MongoCursor partial ([ bool $okay = true ] )
	public void reset ( void )
	public void rewind ( void )
	public MongoCursor setFlag ( int $flag [, bool $set = true ] )
	public MongoCursor setReadPreference ( string $read_preference [, array $tags ] )
	public MongoCursor skip ( int $num )
	public MongoCursor slaveOkay ([ bool $okay = true ] )
	public MongoCursor snapshot ( void )
	public MongoCursor sort ( array $fields )
	public MongoCursor tailable ([ bool $tail = true ] )
	public MongoCursor timeout ( int $ms )
	public bool valid ( void )
}


#其他类


Types

    MongoId — MongoId 类
    MongoCode — The MongoCode class
    MongoDate — The MongoDate class
    MongoRegex — MongoRegex 类
    MongoBinData — The MongoBinData class
    MongoInt32 — MongoInt32 类
    MongoInt64 — MongoInt64 类
    MongoDBRef — MongoDBRef 类
    MongoMinKey — The MongoMinKey class
    MongoMaxKey — The MongoMaxKey class
    MongoTimestamp — MongoTimestamp 类

GridFS Classes

    MongoGridFS — The MongoGridFS class
    MongoGridFSFile — The MongoGridFSFile class
    MongoGridFSCursor — The MongoGridFSCursor class

Miscellaneous

    MongoLog — The MongoLog class
    MongoPool — The MongoPool class
    Mongo — The Mongo class [deprecated]

Mongo 函数

    bson_decode — 反序列化一个 BSON 对象为 PHP 数组
    bson_encode — 序列化一个 PHP 变量为 BSON 字符串

Exceptions

    MongoException — The MongoException class
    MongoResultException — MongoResultException 类
    MongoCursorException — The MongoCursorException class
    MongoCursorTimeoutException — The MongoCursorTimeoutException class
    MongoConnectionException — The MongoConnectionException class
    MongoGridFSException — The MongoGridFSException class
    MongoDuplicateKeyException — The MongoDuplicateKeyException class
    MongoProtocolException — The MongoProtocolException class
    MongoExecutionTimeoutException — The MongoExecutionTimeoutException class
    MongoWriteConcernException — The MongoWriteConcernException class