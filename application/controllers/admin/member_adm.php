<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-18 
* 작성자: 김종태
* 설   명: 관리자모드 회원쪽
*****************************************************************
* 
*/
class member_adm extends CI_Controller {
	  
	

	 var $MEMBER_TABLE =  "tamember";
	 var $GROUP_MEMBER_TABLE = "tab_group_member";
	 var $main_layout =  "admin_main";
	 var $sub_layout =  "admin_sub";


	function __construct()
	{
		parent::__construct();
		$_SESSION = $this->session->all_userdata();
		
		$this->load->database();		

		//모델 로드
		$this->load->model('admin/admin_init');
		$this->load->model('admin/member');
	}
	
	/**
	 * 관리자 회원목록 페이지
	 *
	 * @param	string	$page	현재페이지
	 * @param	string	LIST -> tpl assign
	 */	
	 function member($page=1){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
	
			//회원 리스트 불러오기
			$row = $this->member->member_list($page,'15',$_GET['search_key'],$_GET['search_value']);
			$tpl->assign(array('LIST'=>$row));
			

			$tpl->setLayout($this->sub_layout);
			$tpl->define('CONTENT', $this->display->getTemplate('admin/member/list_g.htm'));
			
			$tpl->printAll();

			break;
			case "POST":
			
			  $ids = $_POST['ids'];
			  $ids_in = "'".implode("','",$ids)."'";

			  switch($_POST['mode']) {
				case "delete":
				  
				    
				    $sql = "update  ".$this->MEMBER_TABLE." set delcheck = 'Y' WHERE userid IN (".$ids_in.")";
				    $DB->query($sql);
				    
				    WebApp::redirect('/admin/member_adm/member/'.$_POST[types].'/');

				break;
				
			} 

			
			break;
			} 
			
        }

	  /**
	 * 관리자 회원목록 페이지
	 *
	 * @param	string	$_GET	현재파라메터
	 * @param	string	LIST -> tpl assign
	 */	
	 function member_excel(){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
	
			//회원 리스트 불러오기
			$row = $this->member->member_list_excel();
			$tpl->assign(array('LIST'=>$row));
			
			$excelnm = date("Y-m-d")." 회원목록";
			header( "Content-type: application/vnd.ms-excel" ); 
			header( "Content-Disposition: attachment; filename=".$excelnm.".xls" ); 
			header( "Content-Description: PHP5 Generated Data" );

			$tpl->setLayout('none');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/member/list_excel.htm'));
			
			$tpl->printAll();

			break;
			case "POST":
			break;
			} 
			
        }

	/**
	 * 관리자 회원현황을 그래프로 표현
	 *
	 * @param	string	$page	현재페이지
	 * @param	string	 
	 */	
	   function member_state(){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			//{label:'Series 1', data: 45}, {label:'Series 2', data: 5}, {label:'Series 3', data: 30}, {label:'Series 4', data: 20}

			$json_1 = $this->member->state_data('addr');
			$tpl->assign(array('json_1'=>$json_1));

			$json_2 = $this->member->state_data('etc_1');
			$tpl->assign(array('json_2'=>$json_2));
			
			

			$tpl->setLayout($this->sub_layout);
			$tpl->define('CONTENT', $this->display->getTemplate('admin/member/state.htm'));
			
			$tpl->printAll();

			break;
			case "POST":
			
			break;
			} 
			
        }

	

	  /**
	 * 관리자 : 회원정보 상세보기 및 수정
	 *
	 * @param	string	$userid	회원아이디
	 * @param	string	 
	 * @param	ajax 용 페이지
	 */	
	   function member_view($userid){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			//회원가입 선택항목
			$area_text = $this->config->item('area_text');
			$etc_1 = $this->config->item('member_etc_1');
			$etc_2 = $this->config->item('member_etc_2');
			

			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
				//회원정보 로드
				$data = $this->member->member_view($userid);
				$tpl->assign($data);
				
				$tpl->assign(array(
					'area_text'=>$area_text,
					'member_etc_1'=>$etc_1,
					'member_etc_2'=>$etc_2,
				));
			
			
			$tpl->setLayout('none');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/member/view_g.htm'));
			
			$tpl->printAll();

			break;
			case "POST":
		
				$this->member->member_modify($_POST);
			
			break;
			} 
			
        }
	

}//

/* End of file ./member_adm.php */
/* Location: ./application/controllers/member_adm.php */
