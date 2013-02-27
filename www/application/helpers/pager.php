<?php
/**
 +------------------------------------------------------------------------------
 * 分页助手
 +------------------------------------------------------------------------------
 * @author    Mpdesign
 * @version   v1.0
 * @package   Www
 +------------------------------------------------------------------------------
 */
class pager_helper {

	var $each_disNums;//每页显示的条目数
	var $nums;//总条目数
	var $current_page;//当前被选中的页
	var $sub_pages;//每次显示的页数
	var $pageNums;//总页数
	var $page_array = array();//用来构造分页的数组
	var $subPage_link;//每个分页的链接
	var $subPage_type;//显示分页的类型
	var $_lang = array(
		'index_page' => '首页',
		'pre_page' => '上一页',
		'next_page' => '下一页',
		'last_page' => '尾页',
		'current_page' => '当前页：',
		'total_page' => '总页数：',
		'current_show' => '当前显示：',
		'total_record' => '总记录数：'
	);
	/*
	@nums    总记录数
	@current_num    当前被选中的页
	@sub_pages      每次显示的页数
	@subPage_link   每个分页的链接
	@subPage_type   显示分页的类型
	
	当@subPage_type=1的时候为普通分页模式
	example： 共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页]
	当@subPage_type=2的时候为经典分页样式
	example： 当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页]
	*/

	
	function pager($nums,$current_page,$sub_pages=10,$subPage_link='',$subPage_type=2){
		$this->nums = $nums;
		$total_page = ceil($nums/$sub_pages);
		if(!$current_page){
			$this->current_page=1;
		}else{
			$this->current_page=intval($current_page);
		}
		$this->sub_pages=intval($sub_pages);
		$this->pageNums=ceil($total_page);
		if($subPage_link){
			if(strpos($subPage_link,'?page=') === false AND strpos($subPage_link,'&page=') === false){
				$subPage_link .= (strpos($subPage_link,'?') === false ? '?' : '&') . 'page=';
			}
		}
		$this->subPage_link=$subPage_link ? $subPage_link : '/'.$_REQUEST['_u'] . '?page=';
		$this->subPage_type = $subPage_type;
	}
	
	/*
	   show_SubPages函数用在构造函数里面。而且用来判断显示什么样子的分页
	*/
	function showpager(){
		if($this->subPage_type == 1){
			return $this->pagelist1();
		}elseif ($this->subPage_type == 2){
			return $this->pagelist2();
		}
	}
	
	
	/*
	   用来给建立分页的数组初始化的函数。
	*/
	function initArray(){
		for($i=0;$i<$this->sub_pages;$i++){
			$this->page_array[$i]=$i;
		}
		return $this->page_array;
	}
	
	
	/*
	   construct_num_Page该函数使用来构造显示的条目
	   即使：[1][2][3][4][5][6][7][8][9][10]
	*/
	function construct_num_Page(){
		if($this->pageNums < $this->sub_pages){
			$current_array=array();
			for($i=0;$i<$this->pageNums;$i++){
				$current_array[$i]=$i+1;
			}
		}else{
			$current_array=$this->initArray();
			if($this->current_page <= 3){
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=$i+1;
				}
			}elseif ($this->current_page <= $this->pageNums && $this->current_page > $this->pageNums - $this->sub_pages + 1 ){
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=($this->pageNums)-($this->sub_pages)+1+$i;
				}
			}else{
				for($i=0;$i<count($current_array);$i++){
					$current_array[$i]=$this->current_page-2+$i;
				}
			}
		}
		
		return $current_array;
	}
	
	/*
	构造普通模式的分页
	共4523条记录,每页显示10条,当前第1/453页 [首页] [上页] [下页] [尾页]
	*/
	function pagelist1(){
		$subPageCss1Str=$this->_lang['total_record'].$this->nums;
		$subPageCss1Str.= $this->_lang['current_page'] . $this->current_page." / " .$this->pageNums." &nbsp; ";
		if($this->current_page > 1){
			$firstPageUrl=$this->subPage_link."1";
			$prewPageUrl=$this->subPage_link.($this->current_page-1);
			$subPageCss1Str.="<a href='$firstPageUrl'>{$this->_lang['index_page']}</a> ";
			$subPageCss1Str.="<a href='$prewPageUrl'>{$this->_lang['pre_page']}</a> ";
		}else {
			$subPageCss1Str.="{$this->_lang['index_page']} ";
			$subPageCss1Str.="{$this->_lang['pre_page']} ";
		}
		
		if($this->current_page < $this->pageNums){
			$lastPageUrl=$this->subPage_link.$this->pageNums;
			$nextPageUrl=$this->subPage_link.($this->current_page+1);
			$subPageCss1Str.=" <a href='$nextPageUrl'>{$this->_lang['next_page']}</a> ";
			$subPageCss1Str.="<a href='$lastPageUrl'>{$this->_lang['last_page']}</a> ";
		}else {
			$subPageCss1Str.="{$this->_lang['next_page']} ";
			$subPageCss1Str.="{$this->_lang['last_page']} ";
		}
		
		return $subPageCss1Str;
	}
	
	
	/*
	构造经典模式的分页
	当前第1/453页 [首页] [上页] 1 2 3 4 5 6 7 8 9 10 [下页] [尾页]
	*/
	function pagelist2(){
		$subPageCss2Str=$this->_lang['total_record'].$this->nums." ";
		$subPageCss2Str.=$this->_lang['current_page'] . $this->current_page."/" . $this->pageNums." ";
		
		if($this->current_page > 1){
			$firstPageUrl=$this->subPage_link."1";
			$prewPageUrl=$this->subPage_link.($this->current_page-1);
			$subPageCss2Str.="<a href='$firstPageUrl'>{$this->_lang['index_page']}</a> ";
			$subPageCss2Str.="<a href='$prewPageUrl'>{$this->_lang['pre_page']}</a> ";
		}else {
			$subPageCss2Str.="{$this->_lang['index_page']} ";
			$subPageCss2Str.="{$this->_lang['pre_page']} ";
		}
		
		$a=$this->construct_num_Page();
		for($i=0;$i<count($a);$i++){
			$s=$a[$i];
			if($s == $this->current_page ){
			 	$subPageCss2Str.="[<span style='color:red;font-weight:bold;'>".$s."</span>]";
			}else{
				 $url=$this->subPage_link.$s;
				 $subPageCss2Str.="[<a href='$url'>".$s."</a>]";
			}
		}
		
		if($this->current_page < $this->pageNums){
			$lastPageUrl=$this->subPage_link.$this->pageNums;
			$nextPageUrl=$this->subPage_link.($this->current_page+1);
			$subPageCss2Str.=" <a href='$nextPageUrl'>{$this->_lang['next_page']}</a> ";
			$subPageCss2Str.="<a href='$lastPageUrl'>{$this->_lang['last_page']}</a> ";
		}else {
			$subPageCss2Str.="{$this->_lang['next_page']} ";
			$subPageCss2Str.="{$this->_lang['last_page']} ";
		}
		return $subPageCss2Str;
	}

}
?>