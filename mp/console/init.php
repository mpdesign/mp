<?php
/**
 +------------------------------------------------------------------------------
 * init a project.
 *
 * Shell InitProject class
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Mp.console
 * @link      84086365@qq.com
 +------------------------------------------------------------------------------
 */

class InitProject {


	function __construct() {
		
		$this->stdout = fopen('php://stdout', 'w');
		
		$console = dirname(__FILE__);
		$root = dirname(dirname(dirname(__FILE__)));
		$app = $root . "/" . PROJECT_NAME;
		$this->__mkdir($app);
		
		exec("cp -rf ".$console."/install/* ".$app."/ ");
		@chmod($app."/application/cache", 0777);
		@chmod($app."/application/logs", 0777);
		@chmod($app."/html/file", 0777);
		
		$this->stdout("---------------------------------------------------------");
		$this->stdout("Congratulations to you, the project successfully created");
		$this->stdout("---------------------------------------------------------");
		$this->stdout("Please create the database before running the system.");
		$this->stdout("CREATE TABLE `sessions` (");
		$this->stdout("  `session_id` varchar(40) NOT NULL DEFAULT '0' COMMENT '',");
		$this->stdout("  `data` text COMMENT '',");
		$this->stdout("  `expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '',");
		$this->stdout("  PRIMARY KEY (`session_id`),");
		$this->stdout("  KEY `session_id` (`session_id`)");
		$this->stdout(") ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='session'");
		
	}



	function stdout($string, $newline = true) {
		
		if ($newline) {
			return fwrite($this->stdout, $string . "\n");
		} else {
			return fwrite($this->stdout, $string);
		}
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

$project = new InitProject();


