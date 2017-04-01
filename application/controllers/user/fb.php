<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-03-13
* 작성자: 김종태
* 설   명: 사이트 유저 페이지
*****************************************************************
*
*/
class fb extends CI_Controller {



	 var $SUB_LAYOUT =  "@sub";
	 var $site_menu="";
	 var $PERM="";

	 function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');
		$_SESSION = $this->session->all_userdata();
		$tpl = $this->display;

		//메뉴 불러오기
		$this->load->model('mdl_fb');
		
	}



	  //2012-02-21 메인페이지
	   function index(){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;

		$tpl->setLayout('@none');
		
		$tpl->define('CONTENT', $this->display->getTemplate(THEME.'/fb.htm'));
		$tpl->printAll();

	  }
	  
	  //2016-01-15 유저정보 저장
	  function fb_user_save(){
		
		switch ($_SERVER[REQUEST_METHOD]) {
			
		case "POST":
	
			$this->mdl_fb->fb_db_save($_POST[fb_userid],$_POST[fb_username],$_POST[fb_userphoto], $_POST[fb_accesstoken], $_POST[fb_email]);
			
			$json[state] = "ok";
		 break;
		}
	  
	  }


	   //2016-01-15 카카오 저장
	  function kakao_user_save(){
		
		switch ($_SERVER[REQUEST_METHOD]) {
			
		case "POST":

			$this->mdl_fb->kakao_db_save($_POST[userid],$_POST[username],$_POST[userphoto]);
			
			$json[state] = "ok";
		 break;
		}
	  
	  }
	
	  function naver_return_api(){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		$tpl->setLayout('@none');
		
		$tpl->define('CONTENT', $this->display->getTemplate(THEME.'/naver_return.htm'));
		$tpl->printAll();
	  }

	   //2016-01-15 카카오 저장
	  function naver_user_save(){
		
				

		switch ($_SERVER[REQUEST_METHOD]) {
			
		case "POST":

			$this->mdl_fb->naver_db_save($_POST[email],$_POST[username]);
			
			$json[state] = "ok";
		 break;
		}
	  
	  }






}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */
