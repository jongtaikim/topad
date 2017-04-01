<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 페이징 클래스
* 작성자: 김종태
* 설   명:  2012-02-13
*****************************************************************
* 
*/

class Paging2{
    var $config;
    var $totalItem;
    var $qs;
    var $total;
    var $baseurl;
    var $self_page;

	

    function __construct($total=1,$qs='')
    {
        $this->config = array(
            'total'   => null,
            'currentPage'   => null,
            'pageVariable'   => '',
            'numberFormat'   => '%n',
            'showFirstLast'  => true,   // 맨처음, 맨 마지막으로 가는 링크를 만들것인가.
            'thisPageStyle'  => 'font-weight: bold;',
            'otherPageStyle' => '',
            'itemPerPage'    => 10, // 리스트 목록수
            'pagePerView'    => 10, // 페이지당 네비게이션 항목수
            'prevIcon'       => null,   // 이전페이지 아이콘
            'nextIcon'       => null,   // 다음페이지 아이콘
            'firstIcon'      => null,   // 첫페이지로 아이콘
            'lastIcon'       => null,    // 마지막페이지 아이콘
            'class'       => null,    // 마지막페이지 아이콘
            'idx'       => 3    // 마지막페이지 아이콘
        );

        if($_SERVER[QUERY_STRING]){
		$this->qs = "?".$_SERVER[QUERY_STRING];
	  }
	
	
	
	 //if($baseurls[3]) $this->baseurl = "/".$baseurls[0]."/".$baseurls[1]."/".$baseurls[2]."/".$baseurls[3];
	
	  if($this->config['prevIcon'] || $this->config['nextIcon'] || $this->config['firstIcon'] || $this->config['lastIcon'] ){
		  $this->config['class'] = "btn-group";
	  }else{
	  	  $this->config['class'] = "";
	  }

    }

    function setConf($key,$val='')
    {
        if (is_array($key)) $this->config = array_merge($this->config,$key);
        else $this->config[$key] = $val;
    }

    function setTotal($total) {
        $this->totalItem = $total;
    }

    function output() {
        return $this->__toString();
    }

    function calculate ()
    {	  
	 
		 $CI =& get_instance();
		 $CI->load->helper('url');
		 $baseurls = explode("/",uri_string());
		
		for($ii=0; $ii<$this->config['idx']; $ii++) {
			 $this->baseurl .= "/".$baseurls[$ii];
		}

	  if($this->config['total']) $this->totalItem = 	$this->config['total'];
	  if($this->config['currentPage']) $this->currentPage = $this->config['currentPage'];

        $this->totalPage = max(ceil($this->totalItem / $this->config['itemPerPage']),1);
   
        if (!$this->currentPage) $this->currentPage = 1;
        if ($this->currentPage > $this->totalPage) $this->currentPage = $this->totalPage;
        $this->lastPageItems = $this->totalPage % $this->config['itemPerPage'];

        $this->prevPage = $this->currentPage - 1;
        $this->nextPage = $this->currentPage + 1;
        $this->seek = $this->prevPage * $this->config['itemPerPage'];
        $this->currentScale = intval($this->currentPage / $this->config['pagePerView']);
        if ($this->currentPage % $this->config['pagePerView'] < 1) $this->currentScale--;
        $this->totalScale = intval($this->totalPage / $this->config['pagePerView']);
        $this->lastScalePages = $this->totalPage % $this->config['pagePerView'];
        if ($this->lastScalePages == 0) $this->totalScale--;
        $this->prevPage = $this->currentScale * $this->config['pagePerView'];
        $this->nextPage = $this->prevPage + $this->config['pagePerView'] + 1;
    }

    function getOffset() {
        return ($this->config['itemPerPage'] * ($this->currentPage - 1));
    }

    function __toString()
    {	 
	  
		
        $this->calculate();
        if ($this->config['showFirstLast']) {
            if ($this->config['firstIcon'])
                $firstBtn = '<img src="'.$this->config['firstIcon'].'" border="0" align="absmiddle" alt="처음"/>';
            else
                $firstBtn = '<i class="fa fa-angle-double-left"></i>';
           
		$firstBtn = $this->_link($firstBtn,  $this->baseurl."/1".$this->qs,'direction prev');

            if ($this->config['lastIcon'])
                $lastBtn = '<img src="'.$this->config['lastIcon'].'" border="0" align="absmiddle" alt="마지막"/>';
            else
                $lastBtn = '<i class="fa fa-angle-double-right"></i>';
          
		$lastBtn = $this->_link($lastBtn,  $this->baseurl."/".$this->totalPage.$this->qs,'direction next');
        } else {
            $firstBtn = $lastBtn = '';
        }

        if ($this->config['prevIcon'])
            $prevBtn ='<img src="'.$this->config['prevIcon'].'" border="0" align="absmiddle" alt="이전 10페이지"/>';
        else
            $prevBtn = '<i class="fa fa-angle-left"></i>';
        if ($this->currentPage > $this->config['pagePerView']){
            $prevBtn = $this->_link($prevBtn,   $this->baseurl."/".$this->prevPage.$this->qs,'direction prev');
	  }else{
		if($this->currentPage >1){
			$prevBtn = $this->_link($prevBtn, $this->baseurl."/".($this->currentPage -1).$this->qs,'direction prev');
		}else{
			$prevBtn = $this->_link($prevBtn, '#','direction prev');
		}
	  }

        $ss = $this->prevPage + 1;
        if (($this->currentScale >= $this->totalScale) && ($this->lastScalePages != 0))
            $se = $ss + $this->lastScalePages;
        else if ($this->currentScale <= -1)
            $se = $ss;
        else
            $se = $ss + $this->config['pagePerView'];

        $navBtn = '';
        for ($i = $ss; $i<$se; $i++) {
            $pageText = str_replace('%n', $i, $this->config['numberFormat']);
		
            if ($i == $this->currentPage) {
                $_btn = '<a class="btn btn-default waves-effect active">'.$pageText.'</a>';
            } else {
                $_btn = '<a href="'.  $this->baseurl."/".$i.$this->qs.'" class="btn btn-default waves-effect">'.$pageText.'</a>';
            }
            $navBtn.= $_btn;
        }

        if ($this->config['prevIcon'])
            $nextBtn ='<img src="'.$this->config['nextIcon'].'" border="0" align="absmiddle" alt="다음 10페이지"/>';
        else

            $nextBtn = '<i class="fa fa-angle-right"></i>';
        if ($this->totalPage > $this->nextPage){
            $nextBtn = $this->_link($nextBtn ,  $this->baseurl."/".$this->nextPage.$this->qs,'direction next');
	  }else{
	      
		$nextBtn = $this->_link($nextBtn ,$this->baseurl."/".($this->currentPage +1).$this->qs,'direction next');
	  }
        return '<div class="text-center padder-v m-b10"><div class="btn-group text-center">'.$firstBtn.''.$prevBtn.$navBtn.$nextBtn.''.$lastBtn.'</div></div>';
    }

    function _link($text,$href,$cssa)
    {
	
        return '<a href="'.$href.'" class="'.$cssa.' btn btn-default waves-effect"">'.$text.'</a>';
    }
}


?>