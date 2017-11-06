<?php
/**
 +------------------------------------------------------------------------------
 * Database storage 
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Db extends Mp {
	
	protected  $table_priex = "";
	
	public $debug = false;
	
	function db($conn = "default"){
		
		$config_db = config_item("db", $conn);
		
		$this->db = dbLink::getInstance()->db_config($conn)->connect();
		$this->table_priex = $config_db["prefix"];
		
		return $this;
		
	}
	
	function table_prefix($prefix = ''){
		
		$this->table_priex = $prefix;
		return $this;
		
	}
	
	function debug(){
		
		$this->debug = true;
		return $this;
		
	}
	
	
	function query($sql,$cond='one') {
		
		$this->sql = $sql;
		if ($this->debug)echo $sql;
		if(!is_object($this->db)) $this->db();
		$op6 = strtoupper(substr(trim($sql),0,6));
		$op4 = strtoupper(substr(trim($sql),0,4));
		if($op6 === 'SELECT' || $op4 === 'SHOW') {
			if($cond == 'one') {
				$data = $this->db->fetch_one($sql);
			} else {
				$data = $this->db->fetch_all($sql);
			}
		} else {
			return $this->db->query($sql);
		}
		return $data;
		
	}

	
	function lastInsertId() {
		
		if(!is_object($this->db)) $this->db();
		return $this->db->last_insert_id();
		
	}

	
	function fetchColumn($sql) {
		
		if(!is_object($this->db)) $this->db();
		$rs = $this->db->query($sql);
		return $rs->fetchColumn();

	}
	
	function begin(){$this->query("BEGIN");}
	
	function commit(){$this->query("COMMIT");}
	
	function rollback(){$this->query("ROLLBACK");}
	
	function end(){$this->query("END");}
	
	/**
	 * update or insertInto data
	 * @param string $table 
	 * @param array $data 
	 * @param array $conditions 
	 */
	function save($table = '', $data = array(), $conditions = array()){
		
		if ($table != '' && $data){
			if (empty($conditions) && isset($data["id"])){
				$conditions = array('id' => $data["id"]);
				unset($data["id"]);
			}
			if (!empty($conditions) && is_array($conditions)){
				return $this->update($table, $data, $conditions);
			}else{
				return $this->insertInto($table, $data, $conditions);
			}
		}else return false;
		
	}
	
	function insertInto($table = '', $data = array(), $conditions = array()){
		
		if ($table != '' && $data){
			
			$fields = $values = '';
			foreach ($data as $key => $value){
				$fields .= "`".$key."`,";
				$values .= "'".$value."',";
			}
			$fields = substr($fields, 0, -1);
			$values = substr($values, 0, -1);
			$sql = "insert into {$this->table_priex}{$table} ({$fields}) values ({$values})";
			
			$r = $this->query($sql);
			if($r)
			return $this->lastInsertId();
		}
		return false;
		
	}
	
	function update($table = '', $data = array(), $conditions = array()){
		if (empty($data))return false;
		
		if ($table != '' && !empty($data) && !empty($conditions) && is_array($conditions)){
			
			$where = $this->conditions($conditions);
			$sets = '';
			foreach ($data as $key => $value){
				if (strpos($value, '`') !== FALSE)
				$sets .= "`".$key."`=".$value.",";
				else
				$sets .= "`".$key."`='".$value."',";
			}
			$sets = substr($sets, 0, -1);
			
			$sql = "update {$this->table_priex}{$table} set {$sets} {$where}";
			return $this->query($sql);
		}else return false;	
		
	}
	
	function delete($table = '', $conditions = array()){
		
		if (!empty($conditions) && $table && is_array($conditions)){
			$where = $this->conditions($conditions);
			
			return $this->query("delete from {$this->table_priex}{$table} {$where}");
			
		}else return false;	
		
	}
	
	
/**
	 * 
	 * query one by id
	 * @param  $table
	 * @param  $id
	 */
	function find_by_id($table = '', $id = '', $fields = '*'){
		
		if (!$table || !$id )return false;
		if (!empty($fields) && is_array($fields)){
			$fields = implode('`,`', $fields);
			$fields = '`'.$fields.'`';
		}
		
		$sql = "select {$fields} from {$this->table_priex}{$table} where id='{$id}' limit 1";
		
		return $this->query($sql);
 	
	}
	
	/**
	 * query one by field
	 * @param  $table
	 * @param  $conditions
	 */
	function find_by_field($table = '', $conditions = array(), $fields = '*'){
		
		if (!$table || empty($conditions) )return false;
		if (!empty($fields) && is_array($fields)){
			$fields = implode('`,`', $fields);
			$fields = '`'.$fields.'`';
		}
		$where = $this->conditions($conditions);
		
		$sql = "select {$fields} from {$this->table_priex}{$table} {$where} limit 1";
		
		return $this->query($sql);
	
	}
	
	
	function find($table = '', $conditions = array(), $limit = '', $fields = '*',$order = '', $cond = 'all'){
		
		if ($table  ){
			$where = $this->conditions($conditions);
			if ($limit != '')$limit = " limit {$limit}";
			if ($order != '')$order = " order by {$order}";
			if (!empty($fields) && is_array($fields)){
				$fields = implode(',', $fields);
			}
			
			$sql = "select {$fields} from {$this->table_priex}{$table} {$where} {$order} {$limit}";
			
			return $this->query($sql,$cond);
			
		}else return false;	
		
	}
	
	
	private function conditions($conditions = array()){
		$where = '';
		if (!empty($conditions) && is_array($conditions)){
			$where = '';
			foreach ($conditions as $key => $value){
				$kv = strtolower($key . ' ' . $value);
				if (is_array($value)){
					$in = "'".implode("','", $value)."'";
					$where .= " and `".$key."` in ({$in}) ";
				}elseif (strpos($kv, '>') !== FALSE || strpos($kv, '<') !== FALSE || strpos($kv, '<>') !== FALSE || strpos($kv, '!=') !== FALSE || strpos($kv, 'like') !== FALSE){
					if (strpos($kv, 'or ') !== FALSE)
					$where .= $key." ".$value;
					else $where .= "  and ".$key." ".$value;
				}else{
					if (strpos($kv, 'or ') !== FALSE)
					$where .= $key." ".$value;
					else $where .= " and `".$key."`='".$value."'";
				}
				
			}
			$where = "where " . substr($where, 5);
		}
		return $where;
	}
	
	function reconnect(){
		
		$this->db->reconnect();
		return $this;
	}
	
	function close(){
		
		$this->db->close();
		
	}
	
	function close_all(){
		
		$this->db->close_all();
		
	}	
	
}

/**
 +------------------------------------------------------------------------------
 * Database connection class
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 +------------------------------------------------------------------------------
 */

class dbLink {
	
	private $db;
	
	public $host = "";
	
	public $user = "";
	
	public $password = "";
	
	public $type = "mysql";
	
	public $pconnect = false;
	
	public $abnormal = true;
	
	public $prefix = "";
	
	static $instance = null;
	
	public $db_conn = "default";
	
	public $db_conns = array();
	
	
	static function getInstance(){
		if (self::$instance == null){
			self::$instance = new dbLink();
		}
		return self::$instance;
	}
	
	function db_config( $conn = "default" ){
		
		$config_db = config_item("db", $conn);
		foreach ($config_db as $key => $value){
			$this->$key = $value;
		}
		
		$this->db_conn = $conn;
		if (empty($this->db_conns) || (!empty($this->db_conns) && !in_array($conn, $this->db_conns)) ){
			$this->db_conns[] = $conn;
		}
		
		return $this;
	}
	
	function connect(  ) {
		
		if ( !is_resource($this->{$this->db_conn}) ){
			
			$this->{$this->db_conn} = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
			if(mysqli_connect_errno($this->{$this->db_conn})) {

				echo('Database upgrade, understanding!' . mysqli_connect_error());
			}
			mysqli_query($this->{$this->db_conn}, 'set names utf8');
			
		}
		
		
		return $this;
	}

	function reconnect(  ){
		
		$this->close();
		
		$this->connect();
		
		return $this;
		
	}
	
	function close($conn = ''){
		
		if (!$conn)$conn = $this->db_conn ;
		
		if (is_resource($this->{$conn})){
			mysqli_close($this->{$conn});
			
		}
		unset($this->{$conn});
		//push conn
		$this->db_conns = array_diff($this->db_conns, array($conn));
	}
	
	function close_all(){
	
		foreach ($this->db_conns as $conn){
			
			$this->close($conn);
			
		}
		
	}

	function fetch_one($sql,$type = MYSQLI_ASSOC) {
		
		$query = $this->query($sql);
		if($query) {
			while($row = mysqli_fetch_array($query,$type)) {
				$result = $row;
				break;
			}
		}
		return $result;
		
	}

	function query($sql) {
		
		$result = mysqli_query($this->{$this->db_conn}, $sql);
		$mysqli_errno = mysqli_errno($this->{$this->db_conn});
        if ($this->abnormal && $mysqli_errno)  echo "MySQL error ".$mysqli_errno.": ".$mysqli_errno." <br>When executing:<br> $sql <br>";
        
        return $result;
		
	}
	
	function last_insert_id() {
		
		return mysqli_insert_id($this->{$this->db_conn});
		
	}
	
	function fetch_all($sql,$type= MYSQLI_ASSOC) {
		
		$query = $this->query($sql);
		if($query) {
			while($row = mysqli_fetch_array($query,$type)) {
				$result[]=$row;
			}
		}
		return $result;
		
	}
}

?>