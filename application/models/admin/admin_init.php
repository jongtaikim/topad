<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-17
* 작성자: 김종태
* 설   명:  사이트 이용약관 모델
*****************************************************************
* 
*/

class admin_init extends CI_Model {

    function __construct()
    {
        parent::__construct();
		
		
			
		$this->load->helper('url');
		$baseurls = explode("/",uri_string());
		
		//관리자 권한 체크

		

		if(THEME == "keyword"){
			
			if(!$this->session->userdata('KEYWORD_SES') && $baseurls[1] != "login"){
				$this->webapp->redirect('/adm/login');
				exit;
			}

			$menu = $this->webapp->get('keyword_menu');

		}else{
			if(!$this->session->userdata('ADMIN') && $baseurls[1] != "login"){
				$this->webapp->redirect('/adm/login');
				exit;
			}

			$menu = $this->webapp->get('adm_menu');
		}
		
		$pn_1 = substr($_GET[PageNum],1,1)-1;
		$pn_2 = substr($_GET[PageNum],3,1)-1;
		$pn_3 = substr($_GET[PageNum],5,1);
		$pn_33 = substr($_GET[PageNum],5,1)-1;

		$menu_title =  $menu[$pn_1][submenu][$pn_2 ][title];	

		$menu_sub = $menu[$pn_1][submenu];
		$menu_sub_tab = $menu[$pn_1][submenu][$pn_2 ][submenu_sub];
		

		$this->display->assign(array(
			'admin_MENU'=>$menu,
			'admin_MENU_sub'=>$menu_sub,
			'admin_MENU_tab'=>$menu_sub_tab,
			'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/application/views/adm/",
			'pn_1'=>$pn_1,
			'pn_2'=>$pn_2,
			'pn_3'=>$pn_3,
			'pn_33'=>$pn_33,
			'menu_title'=>$menu_title,
		));
		
    }
	
    //인트라넷 회원으로 검색
    function select_hmis_member($user_id='',$pw=''){
		if($user_id && $pw){
			$DB2 = $this->load->database('hmis',TRUE);
			$sql = "select * from user_info where user_id = '".$user_id."' and status = '1' and passwd = '".md5($pw)."' ";
			$row = $DB2 -> sqlFetch($sql);
			
			if($row){
				$token = md5($user_id.$pw);
				$datas[sso_token] = $token;
				$DB2->updateQuery($table="user_info",$datas," user_id = '".$user_id."' and status = '1' and passwd = '".md5($pw)."' ");
				$row[sso_token] = $token;
				return $row;
			}else{
				return '';				
			}

			
		}
    }


    //키워드 업체 회원으로 검색
    function select_keyword_member($user_id='',$pw=''){
		if($user_id && $pw){
			$sql = "select * from keyword_company where userid = '".$user_id."'  and passwd = '".$pw."' ";
		
			$row = $this->db -> sqlFetch($sql);
			return $row;	
		}
    }
		
    

	
}

?>