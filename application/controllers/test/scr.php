<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명: 스크롤링 테스트
*****************************************************************
* 
*/
class scr extends CI_Controller {
	
	  
	 function __construct(){
		parent::__construct();
		$tpl = $this->display;
		
		 $this->load->database();
	}
	
	



	  function naver_test(){
		$tpl = $this->display;
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		$this->load->model('test/mdl_scrolling');
		$url = "http://m.search.naver.com/search.naver?query=".urlencode('닭갈비')."";
		$res = $this->mdl_scrolling->curl_url($url,'GET');

		$res = str_replace("/acao/css/2015/w_1230.css",'http://m.search.naver.com/acao/css/2015/w_1230.css',$res);
		$res = str_replace("/acao/css/2014/e_0918.css",'http://m.search.naver.com/acao/css/2014/e_0918.css',$res);
		$res = str_replace("/acao/css/2015/w_1230.css",'http://m.search.naver.com/acao/css/2015/w_1230.css',$res);
		$res = str_replace("/acao/css/2014/e_0918.css",'http://m.search.naver.com/acao/css/2014/e_0918.css',$res);
		$res = str_replace("/acao/js/2015/nx_1210.js",'http://m.search.naver.com/acao/js/2015/nx_1210.js',$res);
		$res = str_replace("/acao/js/2015/nx_1210.js",'http://m.search.naver.com/acao/js/2015/nx_1210.js',$res);

		$res = str_replace("http://m.search.naver.comhttp://m.search.naver.com/",'http://m.search.naver.com/',$res);
		
		$res = str_replace('document.domain = "naver.com" ;','',$res);
		//$res = str_replace('<script','<!--script',$res);
		//$res = str_replace('</script>','</script-->',$res);
		
		$tpl->setLayout('none');
		$tpl->define("CONTENT", $tpl->getTemplate("web","test/naver_test.htm"));
		
		 break;
		case "POST":
		 break;
		}

	  }




}

/* End of file sub.php */
/* Location: ./application/controllers/test/page.php */