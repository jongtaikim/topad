<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-10-01
* 작성자: 김종태
* 설   명: 비포 에프터
*****************************************************************
* 
*/
class ctl_bf extends CI_Controller {
	 
	
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
		$this->load->model('user/menu');
		$this->site_menu = $this->menu->load_tree();
		$tpl->assign(array('site_menu_list'=>$this->site_menu));
		
		$this->load->model('user/mdl_bf');
		//성형카테고리 // config.php
		$this->mdl_bf->category($_GET);
		
		$this->load->library('permission');
		$this->PERM =$this->permission;
	}
	
	 
	
	  //게시판 리스트
	   function list_view($mcode=0,$page=1){
			
			$WA = $this->webapp;
			$tpl = $this->display;

			$this->PERM->apply('menu',$mcode,'r');
			$tpl->assign(array('mcode'=>$mcode));
			
			$row = $this->mdl_bf->load_list($page,$_GET);
			
			$tpl->assign(array('LIST'=>$row));

			if(THEME == 'mobile'){
				$data[str_layout] = "@sub";
				$tpl->setLayout($data[str_layout]);
				$tpl->define('CONTENT', $this->display->getTemplate('bf/list_mobile.htm'));
			}else{
				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_12_02"></div>';
				$data[str_layout] = "@sub_auto_menu";

				$tpl->assign($data);

				$tpl->setLayout($data[str_layout]);
				$tpl->define('CONTENT', $this->display->getTemplate('bf/list.htm'));
			}

			
			
			$tpl->printAll();

        }
	 

	   

}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */