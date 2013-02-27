<?php
/**
 +------------------------------------------------------------------------------
 * Database processing files
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class dbmodel_lib {
	
	protected  $table_priex = TABLE_PREFIX;
	
	public $debug = false;
	
	function execute(){}
	
	public function db($dbname = '', $dbhost = '', $dbuser = '', $dbpass = ''){
		$this->db = new dblink($dbname, $dbhost, $dbuser, $dbpass);
		return $this;
	}
	
	public function table_prefix($prefix = ''){
		$this->table_priex = $prefix;
		return $this;
	}
	
	public function debug(){
		$this->debug = true;
		return $this;
	}
	
	
	public function query($sql,$cond='one') {
		$this->sql = $sql;
		if ($this->debug)echo $sql;
		if(!is_object($this->db)) $this->db();
		if(trim(strtoupper(substr($sql,0,6)))==='SELECT') {
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

	
	public function lastInsertId() {
		if(!is_object($this->db)) $this->db();
		return $this->db->last_insert_id();
	}

	
	public function fetchColumn($sql) {
		if(!is_object($this->db)) $this->db();
		$rs = $this->db->query($sql);
		return $rs->fetchColumn();

	}
	
	/**
	 * update or insertInto data
	 * @param string $table 
	 * @param array $data 
	 * @param array $conditions 
	 */
	function save($table = '', $data = array(), $conditions = array()){
		if ($table != '' && $data){
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
			
			$this->query($sql);
			$this->query("update counts set count_table='{$table}'");
			return $this->lastInsertId();
		}else return false;	
	}
	
	function update($table = '', $data = array(), $conditions = array()){
		if ($table != '' && $data && !empty($conditions) && is_array($conditions)){
			$where = $sets = '';
			foreach ($conditions as $key => $value){
				if (is_array($value)){
					$in = "'".implode("','", $value)."'";
					$where .= "`".$key."` in ({$in}) and ";
				}else{
					$where .= "`".$key."`='".$value."' and ";
				}
			}
			$where = substr($where, 0, -4);
			foreach ($data as $key => $value){
				$sets .= "`".$key."`='".$value."',";
			}
			$sets = substr($sets, 0, -1);
			$sql = "update {$this->table_priex}{$table} set {$sets} where {$where}";
			return $this->query($sql);
		}else return false;	
	}
	
	function delete($table = '', $conditions = array()){
		if (!empty($conditions) && $table && is_array($conditions)){
			$where = '';
			foreach ($conditions as $key => $value){
				if (is_array($value)){
					$in = "'".implode("','", $value)."'";
					$where .= "`".$key."` in ({$in}) and ";
				}else{
					$where .= "`".$key."`='".$value."' and ";
				}
				
			}
			$where = substr($where, 0, -4);
			return $this->query("delete from {$this->table_priex}{$table} where {$where}");
			
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
		$where = '';
		if (!empty($conditions) && is_array($conditions)){
			foreach ($conditions as $key => $value){
				$where .= "`".$key."`='".$value."' and ";
			}
			$where = substr($where, 0, -4);
		}
		$sql = "select {$fields} from {$this->table_priex}{$table} where {$where} limit 1";
		return $this->query($sql);
	
	}
	
	
	function find($table = '', $conditions = array(), $limit = '', $fields = '*',$order='', $cond='all'){
		if ($table  ){
			$where = '';
			if (!empty($conditions) && is_array($conditions)){
				$where = 'where ';
				foreach ($conditions as $key => $value){
					if (is_array($value)){
						$in = "'".implode("','", $value)."'";
						$where .= "`".$key."` in ({$in}) and ";
					}elseif (strpos($value, '>') !== FALSE || strpos($value, '<') !== FALSE || strpos($value, '<>') !== FALSE || strpos($value, '!=') !== FALSE){
						$where .= "`".$key."` ".$value."  and ";
					}else{
						$where .= "`".$key."`='".$value."' and ";
					}
					
				}
				$where = substr($where, 0, -4);
			}
			if ($limit != '')$limit = " limit {$limit}";
			if ($order != '')$order = " order by {$order}";
			if (!empty($fields) && is_array($fields)){
				$fields = implode(',', $fields);
			}
			$sql = "select {$fields} from {$this->table_priex}{$table} {$where} {$order} {$limit}";
			return $this->query($sql,$cond);
			
		}else return false;	
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

class dblink {
	private $db;
	private $dbname;
	public function __construct($dbname = '', $dbhost = '', $dbuser = '', $dbpass = '') {
		$this->dbname = $dbname?$dbname:DB_NAME;
		$this->dbhost = $dbhost?$dbhost:DB_HOST;
		$this->dbuser = $dbuser?$dbuser:DB_USER;
		$this->dbpass = $dbpass?$dbpass:DB_PASS;
		$this->content();
	}

	public function content() {
		$this->db = mysql_connect($this->dbhost,$this->dbuser,$this->dbpass);
		if($this->db) {
			mysql_select_db($this->dbname,$this->db);
			mysql_query('set names utf8');
		} else {
			die('Database upgrade, understanding!');
		}
	}

	public function fetch_one($sql,$type = MYSQL_ASSOC) {
		$query = $this->query($sql);
		if($query) {
			while($row = mysql_fetch_array($query,$type)) {
				$result = $row;
				break;
			}
		}
		return $result;
	}

	public function query($sql) {
		return mysql_query($sql);
	}
	
	public function last_insert_id() {
		return mysql_insert_id();
	}
	public function fetch_all($sql,$type= MYSQL_ASSOC) {
		$query = $this->query($sql);
		if($query) {
			while($row = mysql_fetch_array($query,$type)) {
				$result[]=$row;
			}
		}
		return $result;
	}
}

?>