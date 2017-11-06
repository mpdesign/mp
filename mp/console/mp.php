#!/usr/bin/php -q
<?php
/**
 +------------------------------------------------------------------------------
 * Command-line code generation utility to automate programmer chores.
 *
 * Shell dispatcher class
 *
 *@param    -working admin  
 *			admin == /admin/application/coloase/
 *			/admin == /admin/
 *@param	-app  hello
 *@param	-help	  usage
 *
 *@example  mp -w admin -app hello
 *@example  mp -h
 *
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Mp.console
 * @link      84086365@qq.com
 +------------------------------------------------------------------------------
 */

class ShellDispatcher {

/**
 * Standard input stream.
 *
 * @var filehandle
 * @access public
 */
	var $stdin;

/**
 * Standard output stream.
 *
 * @var filehandle
 * @access public
 */
	var $stdout;

/**
 * Standard error stream.
 *
 * @var filehandle
 * @access public
 */
	var $stderr;

/**
 * Contains command switches parsed from the command line.
 *
 * @var array
 * @access public
 */
	var $params = array();

/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
	var $args = array();

/**
 * The file name of the shell that was invoked.
 *
 * @var string
 * @access public
 */
	var $shell = null;

/**
 * The class name of the shell that was invoked.
 *
 * @var string
 * @access public
 */
	var $shellClass = null;

/**
 * The command called if public methods are available.
 *
 * @var string
 * @access public
 */
	var $shellCommand = null;


/**
 * The path to the current shell location.
 *
 * @var string
 * @access public
 */
	var $shellPath = null;

/**
 * The name of the shell in camelized.
 *
 * @var string
 * @access public
 */
	var $shellName = null;

/**
 * Constructor
 *
 * The execution of the script is stopped after dispatching the request with
 * a status code of either 0 or 1 according to the result of the dispatch.
 *
 * @param array $args the argv
 * @return void
 * @access public
 */
	function ShellDispatcher($args = array()) {
		//set_time_limit(0);
		
		error_reporting(E_ALL);
		$this->parseParams($args);
		$this->_initEnvironment();
		$this->_stop($this->dispatch() === false ? 1 : 0);
	}

	function configForApplication(){
		if (!empty($this->params['apppath'])){
			defined('MP_PATH') || define('MP_PATH'		,	$this->params['mppath']);
			defined('APP_PATH') || define('APP_PATH'		,	$this->params['apppath'] . '/application');
			define('CORE_PATH'		,	MP_PATH.'/core');								
			define('CACHE_PATH'		,	APP_PATH.'/cache');								
			define('LIB_PATH'		,	APP_PATH.'/libs');
			define('HELPER_PATH'	,	APP_PATH.'/helpers');	
			define('VIEW_PATH'		,	APP_PATH.'/views');
			define('MOD_PATH'		,	APP_PATH.'/models');
			define('PLUG_PATH'		,	APP_PATH.'/plugins');
			define('CTRL_PATH'		,	APP_PATH.'/controllers');
			define('LOG_PATH'		,	APP_PATH.'/logs');
			define('CONSOLE_PATH'	,	APP_PATH.'/console');
			
			include_once APP_PATH .  '/config/config.php';
			$GLOBALS["config"] = $config;
			include_once CORE_PATH . '/common.php';
			include_once APP_PATH .  '/config/common.php';			
			include_once CORE_PATH . '/mp.php';
			include_once CORE_PATH . '/model.php';
			include_once MOD_PATH . '/appModel.php';
		}
	}


	function _initEnvironment() {
		$this->stdin = fopen('php://stdin', 'r');
		$this->stdout = fopen('php://stdout', 'w');
		$this->stderr = fopen('php://stderr', 'w');

		if (!isset($this->args[0]) || !isset($this->params['working'])) {
			$this->stderr("\nMPPHP Console: ");
			$this->stderr('This file has been loaded incorrectly and cannot continue.');
			$this->stderr('Please make sure that console is in your system path,');
			$this->stderr('and check the manual for the correct usage of this command.');
			$this->_stop();
		}

//		$env_path = $_ENV["PATH"] ? $_ENV["PATH"] : $_ENV["Path"];
//		$env_path = explode(":", $env_path);
//		$working_path = false;
//		$current_path = dirname(__FILE__);
//		foreach ($env_path as $path){
//			if ( $current_path == $path){
//				$working_path = true;
//				break;
//			}
//		}
//		if (!$working_path){
//			exec("export PATH=" . $current_path . ':$PATH');
//		}

	}

/**
 * Clear the console
 *
 * @return void
 * @access public
 */
	function clear() {
		if (empty($this->params['noclear'])) {
			
			passthru('clear');
			
		}
	}

/**
 * Dispatches a CLI request
 *
 * @return boolean
 * @access public
 */
	function dispatch() {
		$this->params["args"] = $this->shiftArgs();
		
		
		
		if (!empty($this->params["init"])) {
			$this->initProject($this->params["init"]);
			return true;
		}
		
		if (isset($this->params["help"])) {
			$this->help();
			return true;
		}
		
		if (isset($this->params["version"])) {
			$this->version();
			return true;
		}

		if (!$this->params["app"]) {
			$this->help();
			return false;
		}
		
		
		$this->shellName = $this->params["app"];
		$this->shellClass = $this->shellName . 'Shell';
		$this->shellCommand = !empty($this->params["command"]) ? $this->params["command"] : "main";
	

		$Shell = $this->_getShell();
	
		if (!$Shell) {
			$title = sprintf('Error: Class %s could not be loaded.', $this->shellClass);
			$this->stderr($title . "\n");
			return false;
		}
		
		return $Shell->{$this->shellCommand}();

	}

/**
 * Get shell to use, either plugin shell or application shell
 *
 * All paths in the shellPaths property are searched.
 * shell, shellPath and shellClass properties are taken into account.
 *
 * @param string $plugin Optionally the name of a plugin
 * @return mixed False if no shell could be found or an object on success
 * @access protected
 */
	function _getShell() {
		
		$this->configForApplication();
		
		if (!class_exists($this->shellClass)) {
			$shell_file = $this->params["working"] . $this->shellName . '.php';
			if (file_exists($shell_file)){
				require $shell_file;
			}else{
				$this->help("can't find this file : " . $shell_file); 
				return false;
			}
		}

		//if ($this->getInput('The operation will execute the file :' . $shell_file . ' . Continue anyway?', array('y', 'n'), 'y') == 'n') {
		//	$this->_stop();
		//}
		
		$Shell = new $this->shellClass($this);
		return $Shell;
	}

/**
 * Prompts the user for input, and returns it.
 *
 * @param string $prompt Prompt text.
 * @param mixed $options Array or string of options.
 * @param string $default Default input value.
 * @return Either the default value, or the user-provided input.
 * @access public
 */
	function getInput($prompt, $options = null, $default = null) {
		if (!is_array($options)) {
			$printOptions = '';
		} else {
			$printOptions = '(' . implode('/', $options) . ')';
		}

		if ($default === null) {
			$this->stdout($prompt . " $printOptions \n" . '> ', false);
		} else {
			$this->stdout($prompt . " $printOptions \n" . "[$default] > ", false);
		}
		$result = fgets($this->stdin);

		if ($result === false) {
			exit;
		}
		$result = trim($result);

		if ($default != null && empty($result)) {
			return $default;
		}
		return $result;
	}

/**
 * Outputs to the stdout filehandle.
 *
 * @param string $string String to output.
 * @param boolean $newline If true, the outputs gets an added newline.
 * @return integer Returns the number of bytes output to stdout.
 * @access public
 */
	function stdout($string, $newline = true) {
		if ($newline) {
			return fwrite($this->stdout, $string . "\n");
		} else {
			return fwrite($this->stdout, $string);
		}
	}

/**
 * Outputs to the stderr filehandle.
 *
 * @param string $string Error text to output.
 * @access public
 */
	function stderr($string) {
		fwrite($this->stderr, $string);
	}

/**
 * Parses command line options
 *
 * @param array $params Parameters to parse
 * @access public
 */
	function parseParams($params) {
		$this->__parseParams($params);
		$root = dirname(dirname(dirname(__FILE__)));
		
		unset($params);
		
		if (isset($this->params["h"]) || isset($this->params["help"]))$params["help"] = true;
		if (isset($this->params["v"]) || isset($this->params["version"]))$params["version"] = true;
		$app = !empty($this->params["a"]) ? $this->params["a"] : "hello";
		$params["app"] = !empty($this->params["app"]) ? $this->params["app"] : $app;
		
		$working = !empty($this->params["w"]) ? $this->params["w"] : "admin";
		$params["working"] = !empty($this->params["working"]) ? $this->params["working"] : $working;
		
		$command = !empty($this->params["c"]) ? $this->params["c"] : "main";
		$params["command"] = !empty($this->params["command"]) ? $this->params["command"] : $command;
		
		$params["init"] = !empty($this->params["init"]) ? $this->params["init"] : "";
		
		
		//$params = array_merge($defaults, array_intersect_key($this->params, $defaults));
		
		$params = str_replace('\\', '/', $params);
		
		$params['app'] = basename($params['app']);
		
		if (strpos($params['working'], "/") === FALSE){
			//application root for config
			$params['apppath'] = rtrim($root, '/') . '/' .$params['working']; 
			$params['working'] = '/' .$params['working'] . '/application/console/';
		}else{
			$params['working'] =  '/' . trim($params['working'],'/') .  '/' ;
		}
		
		$params['working'] = rtrim($root, '/') .$params['working'];
		
		$params['mppath'] = dirname(dirname(__FILE__));
		
		$this->params = array_merge($this->params, $params);
	}

/**
 * Helper for recursively parsing params
 *
 * @return array params
 * @access private
 */
	function __parseParams($params) {
		$count = count($params);
		for ($i = 0; $i < $count; $i++) {
			if (isset($params[$i])) {
				if ($params[$i]{0} === '-') {
					$key = substr($params[$i], 1);
					$this->params[$key] = true;
					unset($params[$i]);
					if (isset($params[++$i])) {
						if (!$params[$i] || $params[$i]{0} !== '-') {
							$this->params[$key] = str_replace('"', '', $params[$i]);
							unset($params[$i]);
						} else {
							$i--;
							$this->__parseParams($params);
						}
					}
				} else {
					$this->args[] = $params[$i];
					unset($params[$i]);
				}

			}
		}
	}

/**
 * Removes first argument and shifts other arguments up
 *
 * @return mixed Null if there are no arguments otherwise the shifted argument
 * @access public
 */
	function shiftArgs() {
		
		return array_shift($this->args);
	}

/**
 * Shows console help
 *
 * @access public
 */
	function help($usage = "") {
		$this->clear();
		
		if ($usage){
			$this->stdout("notice:");
			$this->stdout("\n{$usage}\n");
		}else{

			$this->stdout("---------------------------------------------------------------");
			$this->stdout("\n * Command-line code generation utility to automate programmer chores.");
			$this->stdout("\n * Shell dispatcher class");
			$this->stdout("\n *");
			$this->stdout("\n *@param	-w|-working   working path or application name example:/admin/application/console/ or admin ");
			$this->stdout("\n *@param	-a|-app      Include the app class:app.php");
			$this->stdout("\n *@param	-c|-command  To run a default command:main");
			$this->stdout("\n *@param	-h|-help	 usage:To get help on a specific command");
			$this->stdout("\n *@param	-v|-version	 To get the console version");
			$this->stdout("\n *@param	-l	 process numbers");
			$this->stdout("\n *");
			$this->stdout("\n *@example  mp -w myworking -app myapp -command mycommand");
			$this->stdout("\n *@example  mp -h ");
			$this->stdout("\n *@example  mp -v ");
			$this->stdout("\n---------------------------------------------------------------");
		}
	}

/**
 * Shows console version
 *
 * @access public
 */
	function version() {
		$this->clear();
		$this->stdout("+------------------------------------------------------------------------------");
		$this->stdout("\n * mp -v: ");
		$this->stdout("+------------------------------------------------------------------------------");
		$this->stdout(" * @author    Mpdesign");
		$this->stdout(" * @version   v1.0");
		$this->stdout(" * @package   MPPHP CONSOLE");
		$this->stdout(" * @date      2013-03-27");
		$this->stdout(" +------------------------------------------------------------------------------");
		
	}

/**
 * Stop execution of the current script
 *
 * @return void
 * @access protected
 */
	function _stop($status = 0) {
		exit($status);
	}
	
	
	/**
	 * init a project
	 */
	function initProject($projectName = ''){
		if ($projectName != ''){
			define("PROJECT_NAME", $projectName);
			$current_path = dirname(__FILE__);
			include_once $current_path . '/init.php';
		}else return false;
	}
	

}

$dispatcher = new ShellDispatcher($argv);

