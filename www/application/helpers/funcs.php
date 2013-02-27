<?php
/**
 +------------------------------------------------------------------------------
 * 函数助手
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */

class funcs_helper {
	//http加密请求
	function var_file_get_contents($url,$post='',$debug=false) {
		$post['vercode'] = authcode(VERCODE,'ENCODE');
	 	$data = htmlspecialchars_decode(http_build_query($post));   
		$opts = array(   
			'http'=>array(   
			'method'=>"POST",   
			'header'=>"Content-type: application/x-www-form-urlencoded\r\n".   
						 "Content-length:".strlen($data)."\r\n" .    
						 "Cookie: foo=bar\r\n" .    
						 "\r\n",   
			'content' => $data,   
			 )   
		);   
		$cxContext = stream_context_create($opts);   
		$sFile = file_get_contents($url, false, $cxContext); 
		if($debug){var_dump($sFile);exit;}
		return $sFile;
	}
	
	//字符过滤
	function filter($value,$html = false)  {
		if (!get_magic_quotes_gpc()) { // 判断magic_quotes_gpc是否打开     
			if(!is_array($value)) {
				return $html?addslashes(htmlentities($value)):addslashes($value);
			} else {
	
				return $html?array_map('filter', htmlentities($value)):array_map('filter', $value);   
			}
		} else {
			
			return $html?htmlentities($value,ENT_QUOTES,'UTF-8'):$value;
		
		}
	}


	// 浏览器友好的变量输出
	function dump($var, $echo=true, $label=null, $strict=true)  {
	    $label = ($label === null) ? '' : rtrim($label) . ' ';
	    if (!$strict) {
	        if (ini_get('html_errors')) {
	            $output = print_r($var, true);
	            $output = "<pre>" . $label . htmlspecialchars($output, ENT_QUOTES) . "</pre>";
	        } else {
	            $output = $label . print_r($var, true);
	        }
	    } else {
	        ob_start();
	        var_dump($var);
	        $output = ob_get_clean();
	        if (!extension_loaded('xdebug')) {
	            $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
	            $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
	        }
	    }
	    if ($echo) {
	        echo($output);
	        return null;
	    }else
	        return $output;
	}

	// 自动转换字符集 支持数组转换
	function auto_charset($fContents, $from='gbk', $to='utf-8') {
	    $from = strtoupper($from) == 'UTF8' ? 'utf-8' : $from;
	    $to = strtoupper($to) == 'UTF8' ? 'utf-8' : $to;
	    if (strtoupper($from) === strtoupper($to) || empty($fContents) || (is_scalar($fContents) && !is_string($fContents))) {
	        //如果编码相同或者非字符串标量则不转换
	        return $fContents;
	    }
	    if (is_string($fContents)) {
	        if (function_exists('mb_convert_encoding')) {
	            return mb_convert_encoding($fContents, $to, $from);
	        } elseif (function_exists('iconv')) {
	            return iconv($from, $to, $fContents);
	        } else {
	            return $fContents;
	        }
	    } elseif (is_array($fContents)) {
	        foreach ($fContents as $key => $val) {
	            $_key = auto_charset($key, $from, $to);
	            $fContents[$_key] = auto_charset($val, $from, $to);
	            if ($key != $_key)
	                unset($fContents[$key]);
	        }
	        return $fContents;
	    } else {
	        return $fContents;
	    }
	}

}	
?>