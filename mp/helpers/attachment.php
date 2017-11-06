<?php
/**
 +------------------------------------------------------------------------------
 * Attachment helper
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   MP
 +------------------------------------------------------------------------------
 */

class attachment_helper {

	var $exts = array('jpg', 'gif', 'png', 'jpeg', 'bmp', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'zip', 'rar', 'pdf', 'chm');
	
	var $upload_max_filesize = "5M";
	
	var $mimetypes = array(
			'ez' => 'application/andrew-inset',
			'hqx' => 'application/mac-binhex40',
			'cpt' => 'application/mac-compactpro',
			'doc' => 'application/msword',
			'bin' => 'application/octet-stream',
			'dms' => 'application/octet-stream',
			'lha' => 'application/octet-stream',
			'lzh' => 'application/octet-stream',
			'exe' => 'application/octet-stream',
			'class' => 'application/octet-stream',
			'so' => 'application/octet-stream',
			'dll' => 'application/octet-stream',
			'oda' => 'application/oda',
			'pdf' => 'application/pdf',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'mif' => 'application/vnd.mif',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			'wbxml' => 'application/vnd.wap.wbxml',
			'wmlc' => 'application/vnd.wap.wmlc',
			'wmlsc' => 'application/vnd.wap.wmlscriptc',
			'bcpio' => 'application/x-bcpio',
			'vcd' => 'application/x-cdlink',
			'pgn' => 'application/x-chess-pgn',
			'cpio' => 'application/x-cpio',
			'csh' => 'application/x-csh',
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'dxr' => 'application/x-director',
			'dvi' => 'application/x-dvi',
			'spl' => 'application/x-futuresplash',
			'gtar' => 'application/x-gtar',
			'hdf' => 'application/x-hdf',
			'js' => 'application/x-javascript',
			'skp' => 'application/x-koan',
			'skd' => 'application/x-koan',
			'skt' => 'application/x-koan',
			'skm' => 'application/x-koan',
			'latex' => 'application/x-latex',
			'nc' => 'application/x-netcdf',
			'cdf' => 'application/x-netcdf',
			'sh' => 'application/x-sh',
			'shar' => 'application/x-shar',
			'swf' => 'application/x-shockwave-flash',
			'sit' => 'application/x-stuffit',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc' => 'application/x-sv4crc',
			'tar' => 'application/x-tar',
			'tcl' => 'application/x-tcl',
			'tex' => 'application/x-tex',
			'texinfo' => 'application/x-texinfo',
			'texi' => 'application/x-texinfo',
			't' => 'application/x-troff',
			'tr' => 'application/x-troff',
			'roff' => 'application/x-troff',
			'man' => 'application/x-troff-man',
			'me' => 'application/x-troff-me',
			'ms' => 'application/x-troff-ms',
			'ustar' => 'application/x-ustar',
			'src' => 'application/x-wais-source',
			'xhtml' => 'application/xhtml+xml',
			'xht' => 'application/xhtml+xml',
			'zip' => 'application/zip',
			'au' => 'audio/basic',
			'snd' => 'audio/basic',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'kar' => 'audio/midi',
			'mpga' => 'audio/mpeg',
			'mp2' => 'audio/mpeg',
			'mp3' => 'audio/mpeg',
			'aif' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'm3u' => 'audio/x-mpegurl',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'ra' => 'audio/x-realaudio',
			'wav' => 'audio/x-wav',
			'pdb' => 'chemical/x-pdb',
			'xyz' => 'chemical/x-xyz',
			'bmp' => 'image/bmp',
			'gif' => 'image/gif',
			'ief' => 'image/ief',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'jpe' => 'image/jpeg',
			'png' => 'image/png',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'djvu' => 'image/vnd.djvu',
			'djv' => 'image/vnd.djvu',
			'wbmp' => 'image/vnd.wap.wbmp',
			'ras' => 'image/x-cmu-raster',
			'pnm' => 'image/x-portable-anymap',
			'pbm' => 'image/x-portable-bitmap',
			'pgm' => 'image/x-portable-graymap',
			'ppm' => 'image/x-portable-pixmap',
			'rgb' => 'image/x-rgb',
			'xbm' => 'image/x-xbitmap',
			'xpm' => 'image/x-xpixmap',
			'xwd' => 'image/x-xwindowdump',
			'igs' => 'model/iges',
			'iges' => 'model/iges',
			'msh' => 'model/mesh',
			'mesh' => 'model/mesh',
			'silo' => 'model/mesh',
			'wrl' => 'model/vrml',
			'vrml' => 'model/vrml',
			'css' => 'text/css',
			'html' => 'text/html',
			'htm' => 'text/html',
			'asc' => 'text/plain',
			'txt' => 'text/plain',
			'rtx' => 'text/richtext',
			'rtf' => 'text/rtf',
			'sgml' => 'text/sgml',
			'sgm' => 'text/sgml',
			'tsv' => 'text/tab-separated-values',
			'wml' => 'text/vnd.wap.wml',
			'wmls' => 'text/vnd.wap.wmlscript',
			'etx' => 'text/x-setext',
			'xsl' => 'text/xml',
			'xml' => 'text/xml',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpe' => 'video/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'mxu' => 'video/vnd.mpegurl',
			'avi' => 'video/x-msvideo',
			'movie' => 'video/x-sgi-movie',
			'ice' => 'x-conference/x-cooltalk',
		);
	

	function show($type, $file, $file_name='') {
		$file = iconv('utf-8','gbk',$file);
		if (! file_exists (  $file )) {
			echo 'The file you found is not exists';
			exit ();
		}
		header ( 'Content-type:'.$this->mimetypes[$type] );
		header( "Content-Disposition:   attachment;   filename=$file_name ");
		readfile($file); 
		exit ();
	}
	
	function file_path($file_path = ''){
		if ($file_path){
			$this->file_path = $file_path;
		}else{
			$this->file_path = !empty($this->file_path) ? $this->file_path : HTML_PATH;
		}
		
		
		return $this;
		
	}
	
	function upload($filename = "", $oldfile = ""){
		$this->file_path();
		ini_set('upload_max_filesize', $this->upload_max_filesize);  //最大上传
		
		if(!isset($_FILES[$filename]) || empty($_FILES[$filename]['tmp_name'])) return "file";
		$files = $_FILES[$filename];

		$suffix = explode('.', $files['name']);
		$l = count($suffix);
		$ext = strtolower($suffix[$l-1]);
		if( !in_array($ext, $this->exts) ) return "ext";
		
		$date_dir = date('Y/m/');
		$this->__mkdirs(  $date_dir  , $this->file_path . '/file/' );
		$file_dir = $this->file_path . '/file/' . $date_dir;
		$tmpname = date('YmdHis') . mt_rand(1000, 9999) . '.' . $ext;
		if(!move_uploaded_file($files['tmp_name'], $file_dir . $tmpname)) {
			return false;
		}  else  {
			if ($oldfile)@unlink($this->file_path . $oldfile);
			chmod($file_dir . $tmpname, 0777);
			return '/file/' . $date_dir . $tmpname;
		}
		
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
