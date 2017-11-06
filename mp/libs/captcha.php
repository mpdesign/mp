<?php
/**
 +------------------------------------------------------------------------------
 * Captcha lib
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 * @link	  84086365@qq.com
 +------------------------------------------------------------------------------
 */

class captcha_lib extends Mp {	
	/**
	 * @var string
	 *
	 * 生成的验证码
	 */
	var $_code;
	
	/**
	 * @var string
	 * 
	 * 会话标识
	 */
	var $_sess_key;
	
	/**
	 * @var string
	 *
	 * 验证码过期时间
	 */
	var $_expired;
	
    /**
     * 图片宽度
     * 
     * @var
     */
    var $_width  = 120;

    /**
     * 图片高度
     * 
     * @var
     */
    var $_height = 50;
	
    /**
     * 验证码宽度
     * 
     * @var
     */
    var $_cwidth  = 0;

    /**
     * 验证码高度
     * 
     * @var
     */
    var $_cheight = 0;

    /**
     * 是否小图
     * 
     * @var boolean
     */
    var $_is_small = false;
    
    /**
     * 文本长度
     * 
     * @var int
     */
    var $_length = 4;
    
    /**
     * 背景颜色
     * 
     * @var int
     */
    var $_bgcolor = null;
    
    /**
     * 前景颜色
     * 
     * @var int
     */
    var $_fgcolor = null;
    
    /**
     * 阴影颜色
     * 
     * @var int
     */
    var $_sdcolor = null;
    
    /**
     * 噪点颜色
     * 
     * @var int
     */
    var $_nscolor = null;
	
	/**
	 * X轴起始位置
	 * 
	 * @var int
	 */
	var $_txbase = 5;
	
	/**
	 * Y轴起始位置
	 * 
	 * @var int
	 */
	var $_tybase = 5;
	
    /**
     * 是否画干扰线
     * 
     * @var boolean
     */
    var $_write_line = true;
	
    /**
     * 是否画杂点
     * 
     * @var boolean
     */
    var $_write_noise = true;
	
    /**
     * 是否画阴影
     * 
     * @var boolean
     */
    var $_write_shadow = true;
    
    /**
     * 字体大小
     * 
     * @var int
     */
    var $_fontszie = 0;
    
    /**
     * 字体路径
     * 
     * @var string
     */
    var $_fontfile = '';
    
    /**
     * 文字间距
     * 
     * @var float
     */
    var $_spacing = 0.75;
    
    /**
	 * 图片格式
	 *
	 * @var string
	 */
    var $_image_format = 'jpeg';

    /**
     * 图片资源
     * 
     * @var resource
     */
    var $_ims;
        
    function __construct($options = array()) {   
    	 	
   		$this->_sess_key = 'imgcode';
        if (!empty($options["position"])) {
        	$this->_sess_key .= '_' . $options["position"];
        }
		
		$this->_code = $this->session->get($this->_sess_key);
		if (empty($this->_code)) {
			$this->_code = '';
		}
		
		$this->_expired = $this->session->get($this->_sess_key . '_expired');
		if (empty($this->_expired)) {
			$this->_expired = '';
		}
		
    	// 是否画干扰线
    	// ------------------------------------------
    	if (isset($options['has_line'])) {
    		$this->_write_line = $options['has_line'];
    	}
    	
    	// 是否画杂点
    	// --------------------------------------------
    	if (isset($options['has_noise'])) {
    		$this->_write_noise = $options['has_noise'];
    	}
    	
    	// 是否画阴影
    	// -----------------------------------------------
    	if (isset($options['has_shadow'])) {
    		$this->_write_shadow = $options['has_shadow'];
    	}
    	
    	// 是否小图
    	// --------------------------------------------
    	if (isset($options['is_small'])) {
    		$this->_is_small = $options['is_small'];
    	}
    	
    	if ($this->_is_small) {
    		$this->_width = 60;
    		$this->_height = 25;
    	}
    }
	
	/**
	 * 校验验证码
	 *
	 * @param string $code
	 * @return boolean
     * 
     */
	function check($code, $delete = true) { 
		
		if ($code == false || !preg_match('/^[A-Za-z0-9]{4}$/', $code)) {
			return '请输入验证码';
		} elseif (time() >= $this->_expired || empty($this->_code)) {
			return '验证码已失效';
		} elseif (strtoupper($code) != strtoupper($this->_code)) {
			return '验证码输入错误';
		}
		
		if ($delete)  {
			
			$this->session->delete($this->_sess_key);
			$this->session->delete($this->_sess_key . '_expired');
		}
		
		return true;
	}
    
    /**
     * Options
     * ---------
     * position
     * has_line
     * has_noise
     * has_shadow
     * line_type
     * 
     * @param array $options
     * @return void
     * 
     */
    function image() {	
    	
    	
    	$this->_set_font();
		
		$this->_code = '';
		$letters = array();
        $pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnprstuvwxyz2345678';
        $plength = strlen($pool);
		for ($i = 0; $i < $this->_length; $i++)  {
			$letter		= substr($pool, mt_rand(0, $plength -1), 1);
			$fontsize 	= $this->_fontsize + mt_rand(-1, 1) + ($this->_is_small ? $this->_width * 2 / 15 : 0);
			$angle  	= mt_rand(-$this->_width / 6, $this->_width / 6);
			$bound		= $this->_calculate_text_box($letter, $fontsize, $angle);

			$this->_cwidth = $this->_cwidth + $bound['width'] * $this->_spacing - $bound['left'];
			$this->_cheight = $this->_cheight > $bound['height'] ? $this->_cheight : $bound['height'];
			
			$letters[] = array('letter' => $letter, 'bound' => $bound, 'angle' => $angle, 'fontsize' => $fontsize);
			
			$this->_code .= $letter;
		}
		
		$this->_txbase	= ($this->_width - $this->_cwidth) / 2;
		$this->_tybase	= ($this->_height - $this->_cheight) / 2;
		
        // Cleanup
        if (!empty($this->_ims)) {
            $this->_cleanup();
        }

        $this->_ims = imagecreatetruecolor($this->_width, $this->_height);
    	
        $this->_set_color();
        
        imagefilledrectangle($this->_ims, 0, 0, $this->_width, $this->_height, $this->_bgcolor);
       
        // 存入SESSION
        // --------------------------------------------------------------------------------------
        
        $this->session->set($this->_sess_key, $this->_code);		
		$this->session->set($this->_sess_key . '_expired', time() + 600);

        // 写入杂点
        // ------------------------
        if ($this->_write_noise) {
            $this->_write_noise();
        }

        // 写入干扰线
        // --------------------
        if ($this->_write_line)  {
        	$line_type = $this->_wrand(array('ssin' => 30, 'csin' => 30, 'msin' => 30, 'mess' => 10));
        	$write_method = '_write_' . $line_type . '_line';
        	
        	call_user_func(array(&$this, $write_method));
        }
        
        // 写入文本
        // ---------------------------
        $this->_write_text($letters);

        // 输出
        // ---------------
        $this->_output();
        $this->_cleanup();
    }

    /**
     * Text insertion
     * 
     * @return void
     */
    private function _write_text($letters = array()) {
        $xmax = ($this->_width - $this->_txbase * 2) / 4;
        $ymax = $this->_height - $this->_tybase * 2;
        
        // Text generation (char by char)
        // --------------------------------
        $x = $this->_txbase;
        
        if ($this->_write_shadow) {
       		foreach ($letters as $info)  {
	            $letter		= $info['letter'];
	            $fontsize	= $info['fontsize'];
	            $angle		= $info['angle'];
	        	$bound		= $info['bound'];
	        	
	        	imagettftext($this->_ims, $fontsize, $angle, $x + mt_rand(-$xmax, $xmax), $this->_tybase + $bound['height'] + mt_rand(-$ymax, $ymax), $this->_sdcolor, $this->_fontfile, $letter);
	        	 $x += $bound['width'] * $this->_spacing - $bound['left'];
	        }
        }
        
        
        $x = $this->_txbase;
        
        foreach ($letters as $info)  {
            $letter		= $info['letter'];
            $fontsize	= $info['fontsize'];
            $angle		= $info['angle'];
        	$bound		= $info['bound'];
        	
            imagettftext($this->_ims, $fontsize, $angle, $x, $this->_tybase + $bound['height'], $this->_fgcolor, $this->_fontfile, $letter);
            
            $x += $bound['width'] * $this->_spacing - $bound['left'];
        }
    }

    /**
     * _write_ssin_line method
     * ----------------------
     * 画简易正弦干扰线
     * 
     * @return void
     */
    private function _write_ssin_line()  {
		$w2 = mt_rand(10, 15);
        
		$h1 = mt_rand(-5, 5);
		$h2 = mt_rand(-1, 1);
		$h3 = mt_rand(4, 6);
		
		for ($x = 0; $x < $this->_width; $x = $x + 0.1)
		{
			$y = $this->_height / $h3 * sin(($x + 1) / (2 * $w2)) + $this->_height / 2 + $h1;
			imagesetpixel($this->_ims, $x, $y, $this->_fgcolor);
			if ($h2 != 0) {
				imagesetpixel($this->_ims, $x, $y + $h2, $this->_fgcolor);
			}
		}
    }

    /**
     * _write_csin_line method
     * -----------------------------------------------
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线 
     * 
     * 正弦型函数解析式：y=Asin(ωx+φ)+b  
     * 各常数值对函数图像的影响：  
     * 	A：决定峰值（即纵向拉伸压缩的倍数）  
     * 	b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）  
     * 	φ：决定波形与X轴位置关系或横向移动距离（左加右减）  
     *  ω：决定周期（最小正周期T=2π/∣ω∣） 
     * 
     * @return void
     */
    private function _write_csin_line()  {
    	$A = mt_rand(1, $this->_height / 2);					// 振幅   
        $T = mt_rand($this->_height * 1.5, $this->_width * 2);	// 周期   
        $w = (2 * M_PI) / $T;
        $f = mt_rand(-$this->_height / 4, $this->_height / 4);	// X轴方向偏移量   
        $b = mt_rand(-$this->_height / 4, $this->_height / 4);	// Y轴方向偏移量
                           
        $px_start = 0;	// 曲线横坐标起始位置   
        $px_end = mt_rand($this->_width / 2, $this->_width * 0.667);	// 曲线横坐标结束位置              
        for ($px = $px_start; $px <= $px_end; $px = $px + 0.9) {   
            if ($w != 0) {   
                $py = $A * sin($w * $px + $f) + $b + $this->_height / 2;  // y = Asin(ωx+φ) + b   
                $i = intval(($this->_fontsize - 6) / 4);   
                while ($i > 0) {
                    imagesetpixel($this->_ims, $px + $i, $py + $i, $this->_fgcolor);
                    $i--;   
                }   
            }   
        }   
           
        $A = mt_rand(1, $this->_height / 2);                  		// 振幅           
        $T = mt_rand($this->_height * 1.5, $this->_width * 2);		// 周期   
        $w = (2 * M_PI) / $T; 
        $f = mt_rand(-$this->_height / 4, $this->_height / 4);		// X轴方向偏移量   
        $b = $py - $A * sin($w * $px + $f) - $this->_height / 2;	// Y轴方向偏移量
        
        $px_start = $px_end;		// 曲线横坐标起始位置      
        $px_end = $this->_width;	// 曲线横坐标结束位置   
        for ($px = $px_start; $px <= $px_end; $px = $px + 0.9) {   
            if ($w != 0) {   
                $py = $A * sin($w*$px + $f)+ $b + $this->_height/2;  // y = Asin(ωx+φ) + b   
                $i = intval(($this->_fontsize - 8) / 4);   
                while ($i > 0) {            
                    imagesetpixel($this->_ims, $px + $i, $py + $i, $this->_fgcolor); 
                    $i--;   
                }   
            }   
        }
    }

    /**
     * _write_msin_line method
     * ----------------------
     * 画多正弦干扰线
     * 
     * @return void
     */
    private function _write_msin_line() {
		$a = mt_rand($this->_height / 8, $this->_height / 5);
		$t = mt_rand($this->_width / 4, $this->_width / 2);
		$w = 2 * M_PI / $t;
		$f = $b = mt_rand($this->_height / 4, $this->_height * 3 / 4);
		
		$x_s = 0;
		$x_e = $x_s + $t;
		for ($i = 0, $c = mt_rand(2, $this->_width / $t); $i < $c; $i++)
		{
			$tmp = mt_rand(-1, 1);
			for ($x = $x_s; $x < $x_e; $x = $x + 0.1)
			{
				$y = $a * sin($w * $x + $f) + $b;
				imagesetpixel($this->_ims, $x, $y, $this->_fgcolor);
				if ($tmp != 0) {
					imagesetpixel($this->_ims, $x, $y + $tmp, $this->_fgcolor);
				}
			}
			$x_s = $t + mt_rand(10, 18);
			$x_e = $x_s + $t;
		}
		
		if ($x_e < $this->_width) 
		{
			$tmp = mt_rand(-1, 1);
			for ($x = $x_e; $x < $this->_width; $x = $x + 0.1)
			{
				$y = $a * sin($w * $x + $f) + $b;
				imagesetpixel($this->_ims, $x, $y, $this->_fgcolor);
				if ($tmp != 0) {
					imagesetpixel($this->_ims, $x, $y + $tmp, $this->_fgcolor);
				}
			}
		}
    }

    /**
     * _write_mess_line method
     * -----------------------
     * 画杂乱的干扰线
     * 
     * @return void
     */
    private function _write_mess_line() {
    	$num_lines = 5;
    	for ($line = 0; $line < $num_lines; ++$line)  {
            $x = $this->_width * (1 + $line) / ($num_lines + 1);
            $x += (0.5 - $this->_frand()) * $this->_width / $num_lines;
            $y = rand($this->_height * 0.1, $this->_height * 0.9);
             
            $theta = ($this->_frand() - 0.5) * M_PI * 0.7;
            $w = $this->_width;
            $len = rand($w * 0.4, $w * 0.7);
            $lwid = rand(0, 2);
             
            $k = $this->_frand() * 0.6 + 0.2;
            $k = $k * $k * 0.5;
            $phi = $this->_frand() * 6.28;
            $step = 0.5;
            $dx = $step * cos($theta);
            $dy = $step * sin($theta);
            $n = $len / $step;
            $amp = 1.5 * $this->_frand() / ($k + 5.0 / $len);
            $x0 = $x - 0.5 * $len * cos($theta);
            $y0 = $y - 0.5 * $len * sin($theta);
             
            $ldx = round(-$dy * $lwid);
            $ldy = round($dx * $lwid);
             
            for ($i = 0; $i < $n; ++$i) {
                $x = $x0 + $i * $dx + $amp * $dy * sin($k * $i * $step + $phi);
                $y = $y0 + $i * $dy - $amp * $dx * sin($k * $i * $step + $phi);
                imagefilledrectangle($this->_ims, $x, $y, $x + $lwid, $y + $lwid, $this->_fgcolor);
            }
        }
    }

    /**
     * _write_noise method
     * ------------------
     * 画杂点
     * 
     * @return void
     */
    private function _write_noise()  {
    	$pool = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnprstuvwxyz2345678';
        $plength = strlen($pool);
        $c = 32;
        if ($this->_is_small) {
        	$c = 15;
        }
    	for($i = 0; $i < $c; $i++) {	
        	imagestring($this->_ims, 4, mt_rand(-10, $this->_width), mt_rand(-10, $this->_height), 
        		substr($pool, mt_rand(0, $plength -1), 1), $this->_nscolor); 
        }   
    }

	/**
	 * _calculate_text_box method
	 * ---------------------------
	 * 通过字体角度得到字体矩形宽度
	 * 
	 * @param string $letter 写入字符
	 * @param int $fontsize 字体大小
	 * @param int $angle 字体角度
	 * @return array 返回长宽高
	 */
	private function _calculate_text_box($letter, $fontsize, $angle) {
		$box = imagettfbbox($fontsize, $angle, $this->_fontfile, $letter);

		$min_x = min(array($box[0], $box[2], $box[4], $box[6]));
		$max_x = max(array($box[0], $box[2], $box[4], $box[6]));
		$min_y = min(array($box[1], $box[3], $box[5], $box[7]));
		$max_y = max(array($box[1], $box[3], $box[5], $box[7]));

		return array(
			'left'	=> ($min_x >= -1) ? -abs($min_x + 1) : abs($min_x + 2),
			'top'	=> abs($min_y),
			'width' => $max_x - $min_x,
			'height'=> $max_y - $min_y,
			'box' 	=> $box
		);
	}
	
	/**
	 * _set_color method
	 * -----------------
	 * 设置颜色信息
	 *  
	 * @return void
	 */
	private function _set_color() {
		// 颜色信息
		// -----------------------------------------
	    $color_infos = array(
	    	array(
	    		'bgcolor' => array(247, 254, 236), 
	    		'fgcolor' => array(77, 119, 37), 
	    		'sdcolor' => array(227, 238, 217), 
	    		'nscolor' => array(227, 238, 217)
	    	), 
	    	array(
	    		'bgcolor' => array(248, 248, 248), 
	    		'fgcolor' => array(194, 56, 9), 
	    		'sdcolor' => array(230, 229, 229), 
	    		'nscolor' => array(230, 229, 229)
	    	), 
	    	array(
	    		'bgcolor' => array(248, 248, 248), 
	    		'fgcolor' => array(4, 84, 133),
	    		'sdcolor' => array(230, 229, 229), 
	    		'nscolor' => array(230, 229, 229)
	    	)
	    );
	    
	    $rand_one = mt_rand(0, count($color_infos) - 1);
	    foreach ($color_infos[$rand_one] as $type => $color)  {
	    	$type = '_' . $type;
	    	$this->{$type} = imagecolorallocate($this->_ims, $color[0], $color[1], $color[2]);
	    }
	}
	
	/**
	 * _set_font method
	 * ----------------
	 * 设置字体信息
	 * 
	 * @return void
	 */
	private function _set_font() {
		/**
	     * Font configuration
	     *
	     * - font: TTF file
	     * - spacing: relative pixel space between character
	     * - min_size: min font size
	     * - max_size: max font size
	     * 
	     * @var array
	     */
	    $fontconf = array(
	        'Antykwa'  	=> array('spacing' => 0.75, 'size' => $this->_width / 6, 'font' => 'AntykwaBold.ttf'),
	        'Jura'     	=> array('spacing' => 0.75, 'size' => $this->_width / 6 + 4, 'font' => 'Jura.ttf'),
	        'Times'	 	=> array('spacing' => 0.75, 'size' => $this->_width / 6, 'font' => 'TimesNewRomanBold.ttf'), 
	        'VeraSans' 	=> array('spacing' => 0.75, 'size' => $this->_width / 6, 'font' => 'VeraSansBold.ttf')
	    );
	    
		$fonttype = $this->_wrand(array('Times' => 40, 'VeraSans' => 30, 'Jura' => 20, 'Antykwa' => 10));
		$fontinfo = $fontconf[$fonttype];
		
		$this->_spacing 	= $fontinfo['spacing'];
		$this->_fontsize	= $fontinfo['size'];
		$this->_fontfile	= MP_PATH . '/plugins/fonts/' . $fontinfo['font'];
	}
    
    /**
     * Generate random number less than 1
     * @access private
     * @return float
     */
    private function _frand()  {
    	return 0.0001 * mt_rand(0,9999);
    }
    
    /**
     * _wrand method
     * ---------------
     * 权重随机值
     * 
     * @param array $wlist
     * @access private
     * @return string
     */
    private function _wrand($wlist = array()) {
        $r = mt_rand(0, array_sum(array_values($wlist)));
        $c = 0;
        foreach ($wlist as $t => $w) {
        	$c += $w;
        	if ($r < $c) {
        		return $t;
        	}
        }
        return $t;
    }
    
    /**
     * File generation
     * 
     * @return void
     */
    private function _output()  {
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache"); 
    	switch (strtolower($this->_image_format)) {
			case 'png':
				header("Content-type: " . image_type_to_mime_type(IMAGETYPE_PNG));
				imagepng($this->_ims);
				break;
			case 'gif':
				header("Content-type: " . image_type_to_mime_type(IMAGETYPE_GIF));
				imagegif($this->_ims);
				break;
			case 'jpg':
			default:
				header("Content-type: " . image_type_to_mime_type(IMAGETYPE_JPEG));
				imagejpeg($this->_ims);
		}
    }
    
    /**
     * Cleanup
     * 
     * @return void
     */
    private function _cleanup()  {
        imagedestroy($this->_ims);
    }
}
