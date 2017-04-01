<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-10-02
* 작성자: 김종태
* 설   명: 온라인상담
*****************************************************************
* 
*/
class online_adm extends CI_Controller {
	 
	

	 var $site_menu="";
	 var $PERM="";

	 function __construct(){
		parent::__construct();
		$_SESSION = $this->session->all_userdata();

		$this->load->database();
	
		//모델
		$this->load->model('admin/admin_init');
		$this->load->model('admin/menu');
		
		$this->load->model('user/mdl_online');
		//성형카테고리 // config.php
		$this->mdl_online->category($_GET);
		
		$this->load->library('permission');
		$this->PERM =$this->permission;
	}
	
	 
	
	  //게시판 리스트
	   function list_view($page=1){
			
			$WA = $this->webapp;
			$tpl = $this->display;

			
			$tpl->assign(array('mcode'=>$mcode));
			
			$row = $this->mdl_online->load_list($page,'15',$_GET);
			$tpl->assign(array('LIST'=>$row));

			$tpl->setLayout('admin_sub');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/online/list.htm'));
			
			$tpl->printAll();

        }
	  
	//게시판 읽기
	function set_tmp_passwd($key='',$val=''){
		$this->session->set_flashdata($key, $val);
	}

	   //게시판 읽기
	   function item_view($idx=0){
			
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;

			
			$data = $this->mdl_online->load_view($idx);

			if($data){
				
		
				//이전글 이미지 경로 처리
				$data[comment] = str_replace('src="/images/notice/','src="/application/views/contents/images/notice/',$data[comment]);
				$data['text'] = str_replace('	','',$data['text']);
				$data['text'] = str_replace('<p>','',$data['text']);

				//엔터 처리로 인하여 이와같이 처리함
				$data['text'] = str_replace('</p>',PHP_EOL,$data['text']);
				$data['text'] = str_replace('<br>',PHP_EOL,$data['text']);


				$data['text'] = strip_tags($data['text']);

				$data['text'] = str_replace("&nbsp;"," ",$data['text']);
				$data[text] = 
				$tpl->assign($data);
			
	
			}else{
				WebApp::moveBack('게시물이 존재하지 않습니다.');
			}

			
			$tpl->assign(array('idx'=>$idx,'mcode'=>$mcode));
						
			
			$tpl->setLayout('none');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/online/add.htm'));
			
			$tpl->printAll();

        }

	  //게시판 답변
	   function item_re($idx=0){
			
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;

			switch ($_SERVER[REQUEST_METHOD]) {
			case "POST":
				
				$datas[lind] = $idx;
				$datas[id] = 'admin';
				$datas[text] = $_POST[re_text];
				$this->mdl_online->online_re($datas,$idx);
			
			break;
			}
		
        }

	  /**
	  *  삭제
	  *
	  * @return	void
	  */	
	 function item_del($mode='all',$idx=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;
		if($idx){
			$data=$this->mdl_online->online_del($mode,$idx);
		}
	  
	  }
	
	/**
	 * 상담현황 그래프
	 *
	 * @param	string	$aaa	주석
	 * @return	object	$aaa
	 * @return	void
	 */	
	
	function chat_view(){
		$tpl = $this->display;
		$json_1 = $this->mdl_online->state_data('json_1');

		$ym_data = $this->mdl_online->state_data('json_2');

		$tpl->assign(array(
				'json_1'=>$json_1,
				'ym_data'=>$ym_data,
			));
		
		

		$tpl->setLayout('admin_sub');
		$tpl->define('CONTENT', $this->display->getTemplate('admin/online/state.htm'));
		
		$tpl->printAll();

	}

	   

}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */