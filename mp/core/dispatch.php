<?php
/**
 +------------------------------------------------------------------------------
 * Core Dispatch
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class Dispatch extends Mp {
	
	function __construct() {

		//ips
		$this->Access();

		$params = $this->parseParams($_REQUEST['_u']);

		$this->Run($params);
	}

/**
 * Run given Controller->action
 *
 * @param  Controller $obj
 * @param  Action     $func
 * @access public
 */
	private function Run($params = array()) {

		$obj = new $params['action']();
		
		$obj->is_admin = $params["is_admin"];
		$func = config_item("router", "method");
		
		if(!method_exists($obj, $func)) {$obj->redirect("/errors/404");}
		
		$obj->data = $_REQUEST;
		
		$action = !empty($params['prefix']) ? str_replace($params['prefix'] . "_", "", $params['action']) : $params['action'];
		$obj->url = array("prefix" => $params['prefix'],
			"entrance" => $params['entrance'],
			"module" => $params['module'],
			"action" => $action,
			"params" => $params['params']
		);


		//staic html
		$html = false;
		if ($obj->autoStatic){
			View::getInstance()->start_html();
			$path = $params['module'] . "/" . $params['action'] ;
			$html = View::getInstance()->file_path($path, $params['params'])->output_html();
			
		}
		if ( !$html ){
			$obj->init();
			$obj->$func();
			$obj->afterRun();
			if ($obj->autoRender){

				$tpl_name = $obj->set_tpl();
				$tpl_name = $tpl_name ? $tpl_name : $params['action'];
				$obj->set_tpl($params['module'] . '/' . $tpl_name . config_item("smarty", "ext"));

				//language package
				if ($obj->autoLanguage){

					$language = $obj->language();
					if ($language){

						foreach ($language as $key => $value){

							$obj->assign($key, $value);

						}

					}

				}

				echo $obj->render(null,null,null);
				if ($obj->autoStatic){

					View::getInstance()->input_html();
					View::getInstance()->end_html();

				}
			}
		}else{

			include_once $html;
			exit;
		}



	}



/**
 * Parses given $params and returns an array of controller, action and parameters
 * taken from that URL.
 *
 * @param string $params URL to be parsed
 * @return array Parsed elements from URL
 * @access public
 */
	public function parseParams($params) {
		//$params = preg_replace("/[^a-zA-Z\_\-\%\/\.0-9]/i", "", $params);
		//$params = $this->xss_clean($params);

		$params = str_replace(".", "_", $params);

		$strpos = strpos($params, '?');
		if ( $strpos !== false){
			$params = explode("?", $params);
			$params = $params[0];
		}

		//url clean
		if (!preg_match('/^[a-zA-Z0-9\_\-\/]+$/i', $params)){
			die('url error');
		}

		//router
		$params = $this->router($params);

		$result["is_admin"] = false;
		$config_admin = config_item("router", "admin");
		$result["entrance"] = "";
		$result["prefix"] = "";

		if($params) {
			$params = substr($params, 0, 1) == "/" ? substr($params, 1) : $params;
			$params = explode("/",$params);$count = count($params);

			if (!empty($params) && $params[0] == $config_admin["entrance"]){

				unset($params[0]);
				$result["is_admin"] = true;
				$params = array_values($params);
				$count = count($params);
				$result["entrance"] = $config_admin["entrance"];
				$result["prefix"] = $config_admin["prefix"];

			}

			if(file_exists(CTRL_PATH.'/'.$params[0].'/'.$params[0].'.php')){

				include_once CTRL_PATH.'/'.$params[0].'/'.$params[0].'.php';

			}
			if($count >2) {

				for($i=1;$i<$count;$i++) {
					if($result["is_admin"]){
						$params[$i] = $config_admin["prefix"] . "_" . $params[$i];
					}else{
						if(substr($params[$i], 0, strlen($config_admin["prefix"])) == $config_admin["prefix"]){
							// 非法入口
							header("location: /errors/404");exit;
						}
					}
					if(file_exists(CTRL_PATH.'/'.$params[0].'/'.$params[$i].'.php')) {

						include_once CTRL_PATH.'/'.$params[0].'/'.$params[$i].'.php';
						$result['module']		= $params[0];
						$result['action']		= intval($params[$i]) ? 'index' : $params[$i] ;
						unset($params[0]);unset($params[$i]);
						$result['params']		= array_values($params);
						break;

					}

				}
			} else {

					if($result["is_admin"]){
						$params[1] = $config_admin["prefix"] . "_" . $params[1];
					}else{
						if(substr($params[1], 0, strlen($config_admin["prefix"])) == $config_admin["prefix"]){
							// 非法入口
							header("location: /errors/404");exit;
						}
					}
					if(file_exists(CTRL_PATH.'/'.$params[0].'/'.$params[1].'.php')) {

						include_once CTRL_PATH.'/'.$params[0].'/'.$params[1].'.php';
						$result['module']	= $params[0];
						$result['action']	= intval($params[1]) ? 'index' : $params[1] ;

					} else {

						$params[1] = $result["is_admin"] ? $config_admin["prefix"] . "_index" : "index";
						if(file_exists(CTRL_PATH.'/'.$params[0].'/'. $params[1] . '.php')) {

							include_once CTRL_PATH.'/'.$params[0].'/'. $params[1] . '.php';
							$result['module']	= $params[0];
							$result['action']	= $params[1];

						}

					}
			}



		} else {

			include_once CTRL_PATH.'/home/'.'index.php';
			$result['module']	= 'home';
			$result['action']	= 'index';

		}


		if($result['module'] && $result['action']) {

			return $result;

		} else {

			header("location: /errors/404");exit;

		}

	}

	/**
	 *  Parse router
	 *
	 * This function matches any routes that may exist in
	 * the config/routes.php file against the URI to
	 * determine if the class/method need to be remapped.
	 *
	 * @param   string $params
	 * @access	private
	 * @return	string
	 */
	function router($params = '') {

		$routes = config_item("router", "url");
		if (!empty($routes)){

			// Is there a literal match?  If so we're done
			if (!empty($routes[$params])) {
				return $routes[$params];
			}

			// Loop through the route array looking for wild-cards

			foreach ($routes as $key => $val) {
				// Convert wild-cards to RegEx
				$key = str_replace(':any', '.+', str_replace(':num', '[0-9]+', $key));

				// Does the RegEx match?
				if (preg_match('#^'.$key.'$#', $params)) {

					// Do we have a back-reference?
					if (strpos($val, '$') !== FALSE AND strpos($key, '(') !== FALSE) {

						$val = preg_replace('#^'.$key.'$#', $val, $params);

					}

					return $val;
				}
			}
		}
		return $params;

	}

	/**
	 * ip access restrictions
	 * Enter description here ...
	 */
	function Access(){

		if ( !config_item("ips","open") )return fasle;

		$ip = ip_address();
		$ips_allow = config_item("ips","allow");
		$ips_deny = config_item("ips","deny");

		$Forbidden = "<!DOCTYPE HTML PUBLIC \"-\/\/IETF//DTD HTML 2.0\/\/EN\"><html><head><title>403 Forbidden</title></head><body>
					<h1>Forbidden</h1>
					<p>You don't have permission to access /on this server.</p>
					</body></html>";

		if (!empty($ips_allow)){

			if ( !in_array($ip, $ips_allow) ){

				die($Forbidden);

			}

		}elseif (!empty($ips_deny)){

			if ( in_array($ip, $ips_deny) ){

				die($Forbidden);

			}

		}


	}

	function xss_clean($params = ''){


		$patterns = array( "/\s+/i","/%20/",
					'/</i',
					'/>/i',
		 			"/script/i",
					"/iframe/i",
					"/expression/i" // CSS and IE
					//"/vbscript/i"	 // IE, surprise!
		);

		$replacements = array('','','','','','','');

		$params = preg_replace($patterns, $replacements, $params);

		return $params;
	}
}
?>