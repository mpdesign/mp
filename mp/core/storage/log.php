<?php 
/**
 +------------------------------------------------------------------------------
 * Log storage
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Log extends Mp {
	
	var $log_format = "\t";
	
	var $kv_format = "::";
    
    var $log_path = LOG_PATH;

    var $file_size_limit = 10000000;
    
    function __construct(){}

	
    function write($msg = array(), $mode = 'a+', $filepath = ''){
    	$filepath = $filepath ? $filepath : $this->path();
    	$this->_fwrite($filepath, $msg, $mode);
    }
    
/**
 * 
 * Read the last 10 lines of the log
 * @param int $limit
 * @param bool $print
 * @param string $filepath
 */
	function read($limit = 10, $print = false, $filepath = ''){
		$filepath = $filepath ? $filepath : $this->path();
		if ( !file_exists($filepath) ) return false;
		$fp = fopen($filepath, "r");
		if($fp){			
		    for($i=1;! feof($fp);$i++){		    	
		    	$line = fgets($fp);
		    	$line = preg_replace('(\\n|\\t|\\r)', '', $line);
		    	if(!empty($line))$data[] = $line;
		    	
		    }
		    if ($limit > 0){
		    	$len = count($data);
		    	for($i = $len - 1; $i >= $len - 1 - $limit;$i--){
		    		if (isset($data[$i])){
		    			if ($print)print_r($data[$i] . "<br \> ");
		    			else $newdata[] = $data[$i];
		    		}
		    	}
		    	if (!$print)$data = $newdata;
		    }
		    if (count($data) == 1)$data = $data[0];
		}else{
		    $data = FALSE;
		}
		fclose($fp);
		return $data;
	}
    
	
	function _fwrite($filepath = '', $msg = array(), $mode = 'a+'){	

		if (! $fp = fopen($filepath, $mode)){
			die("can't write {$filepath}");
		}
		$message = $this->format($msg);
		
		$start_time = microtime();
		do {
			$can_write = flock($fp, LOCK_EX|LOCK_NB);
			if (!$can_write) {
				usleep(round(rand(0, 100)*1000));
			}
		} while ((!$can_write) && ((microtime() - $start_time) > 1000));
		if ($can_write ) {
			fwrite($fp, $message);
		}
		fclose($fp);
		
		@chmod($filepath, 0666); 		
		return TRUE;
	}

	

    public function path($filepath = ''){
    	$this->__mkdirs(   date('Y/m/') , $this->log_path );
    	$folder = $this->log_path . date('/Y/m/');
    	
    	for ($i = 0; $i < 1000; $i++){
    		$filepath = $folder  . date('Y-m-d') . '_' . $i . '.log';
    		if (!file_exists($filepath))break;
	    	$filesize = abs(filesize($filepath));
	    	if ( $filesize < $this->file_size_limit){
	    		break;
	    	}
    	}
    	
    	return $filepath;
    }
    

    function set_path($log_path){
    	
        $this->log_path = $log_path;
        return $this;
        
    }
        

	function format( $data = array() ){
		if (!empty($data)){
			if (!is_array($data))$msgs["NOTE"] = $data;
			$msg_arr = array();
			array_push($msg_arr, date('Y-m-d H:i:s'));
			array_push($msg_arr, "IP" . $this->kv_format . ip_address());
			
			array_push($msg_arr, "GET" . $this->kv_format . $_SERVER["QUERY_STRING"]);
			array_push($msg_arr, "REFERER" . $this->kv_format . $_SERVER["HTTP_REFERER"]);
			if (!empty($msgs)){
				foreach ($msgs as $k => $v) {
					array_push($msg_arr, "{$k}{$this->kv_format}{$v}");
				}
			}
			$data = implode( $this->log_format, $msg_arr );
			
			$message .= $data."\r\n";
			
			return $message;
		}
	}
	
	
	function unformat( $data = '' ){
		if (!empty($data)){
			if (is_array($data)) $data = $data[0];
			$data = explode( $this->log_format, $data );
			$result = array();
			foreach ($data as $item) {
				$msg_arr = explode( $this->kv_format, $item );
				$result[$msg_arr[0]] = $msg_arr[1];
			}
			
			return $result;
		}else return false;
	}
	
	
	function __mkdirs($dir = '', $rootpath = '.') {
		
		if (! $rootpath)
			return false;
		if ($rootpath == '.')
			$rootpath = realpath ( $rootpath );
		$folder = explode ( '/', $dir );
		$path = '';
		for($i = 0; $i < count ( $folder ); $i ++) {
			if ($current_dir = trim ( $folder [$i] )) {
				if ($current_dir == '.')
					continue;
				$path .= '/' . $current_dir;
				if ($current_dir == '..') {
					continue;
				}
				if (file_exists ( $rootpath . $path )) {
					@chmod ( $rootpath . $path, 0777 );
				} else {
					if (! $this->__mkdir ( $rootpath . $path )) {
						return false;
					}
				}
			}
		}
		return true;
	}
	
	function __mkdir($dir = '') {
		if (file_exists ( $dir ))
			return true;
		$u = umask ( 0 );
		$r = @mkdir ( $dir, 0777 );
		umask ( $u );
		return $r;
	}
	

}
