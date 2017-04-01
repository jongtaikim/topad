<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2016-01-06
* 작성자: 김종태
* 설   명: 스크롤링 네이버
*****************************************************************
* 
*/
class naver extends CI_Controller {
	
	  
	 function __construct(){
		parent::__construct();
		$this->load->library('simple_html_dom');
	}
	
	function run(){
		echo "\n=================".date("Y-m-d H:i:s")."==============\n";
		$sql = " SELECT keyword , count(*) as cout  FROM `keyword_content` where use_st = 'Y' GROUP BY keyword order by keyword desc; ";
		$row = $this->db-> sqlFetchAll($sql);

		echo "=================".count($row)."건 검색어 수집 ==============\n";
		for($ii=0; $ii<count($row); $ii++) {
			echo "[".$row[$ii][keyword]."] 키워드 네이버 검색 저장중...";
			$this->search($row[$ii][keyword]);
			
			
			echo "...완료\n";

		}
		
		
	}
	

	  //모바일 검색 후 디비로 저장
	  function search($keyword=''){
		
		$this->load->model('mdl_scrolling');
		
	
		
		$url = "http://m.search.naver.com/search.naver?query=".urlencode($keyword)."";
		$res = $this->mdl_scrolling->curl_url($url,'GET');

		$res = str_replace("/acao/css/",'http://m.search.naver.com/acao/css/',$res);
		$res = str_replace("/acao/js/",'http://m.search.naver.com/acao/js/',$res);
		$res = str_replace("http://m.search.naver.comhttp://m.search.naver.com/",'http://m.search.naver.com/',$res);
		
		$res = str_replace('document.domain = "naver.com" ;','',$res);
		$res = str_replace('window.open(location, "_top")',"",$res);

		$data[keyword] = $keyword;
		$data[res] = addslashes($res);
		
		echo ">>스크랩핑>>";

		//디비로 저장
		$this->mdl_scrolling->db_save($data);

		echo ">>디비저장>>";

		$this->rank_chk($keyword,$res);

		return $res;
	  }



	   //모바일 검색 후 디비로 저장
	  function rank_chk($keyword='',$res=''){
		if($keyword && $res){
			

			$sql = " SELECT *  FROM `keyword_content` where use_st = 'Y'  and keyword = '".$keyword."' ";
			$row = $this->db-> sqlFetchAll($sql);
			
			echo ">>키워드 추출>>";

			$html = str_get_html($res);
			
			for($i=0; $i<count($row); $i++) {

				$ii=0;
				foreach($html->find('.lst_total > li > .total_wrap') as $element) {
					$rows[$ii][rank] = $ii+1;
					$rows[$ii][url] = $element->href; 
					$rows[$ii][text] = strip_tags($element->innertext); 
					$rows[$ii][text] = str_replace("&gt;",">",$rows[$ii][text]);
					$rows[$ii][text] = str_replace("&lt;","<",$rows[$ii][text]);
					
					//순위 캡쳐
					
					if($row[$i][target_url] == $rows[$ii][url]){
					
						$this->mdl_scrolling->db_rank_save($rows[$ii][rank],$keyword,$rows[$ii][text],$rows[$ii][url]);
					}

					$ii++;
				}

			}

	

		}
		
		return $res;
	  }

	


}
