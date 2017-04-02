<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-18 
* 작성자: 김종태
* 설   명: 관리자모드 컨트로롤러
*****************************************************************
* 
*/
class Site_adm extends CI_Controller {
	  
	
	
	 var $FILES_TABLE =  "tab_files";
	 var $ORGAN_TABLE =  "tab_organ";
	
	 var $main_layout =  "admin_main";
	 var $sub_layout =  "admin_sub";

 
	function __construct()
	{
		parent::__construct();
		$_SESSION = $this->session->all_userdata();

		

		$this->load->database();
		//관리자 공통 모델
		$this->load->model('admin/admin_init');
	}
	

	/**
	 * 관리자 로그인
	 *
	 * @param	string	$userid	로그인 아이디
	 * @param	string	password	로그인 암호
	 */	

	function login(){
		
		$tpl = $this->display;
		
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
	
		$tpl->setLayout($this->main_layout);
		$tpl->define("CONTENT", $this->display->getTemplate("admin/login.htm"));
		
		$tpl->printAll();

		 break;
		case "POST":
		
		
		if(strstr($_POST[userid], "--") || strstr($_POST[userid], "1=1") || strstr($_POST[password], "--") || strstr($_POST[password], "1=1")){
			$this->webapp->moveBack('잘못된 아이디로 로그인을 시도 하였습니다.');
		}
		


			if(($_POST[userid] == _ADMIN_ID && md5($_POST[password]) == _ADMIN_PW) ){
				
				$member_type = $this->config->item('member_type');
				
				
				$site_data = @parse_ini_file(_DOC_ROOT.'/site_conf/'.THEME.'/conf/site_config.php',true);
				
				$this->session->set_userdata('ADMIN', TRUE);
				$this->session->set_userdata('NAME', $site_data[admin_name]);
				$this->session->set_userdata('EMAIL', $site_data[email]);
				$this->session->set_userdata('USERID', _ADMIN_ID);
				$this->session->set_userdata('PASSWORD', _ADMIN_PW);
				$this->session->set_userdata('MTYPE_NAME',$member_type['z']);
				$this->session->set_userdata('MTYPE', "z");
				$this->session->set_userdata('MEM_TYPE', array('z'));
				$this->session->set_userdata('AUTH', TRUE);
				$this->session->set_userdata('REMOTE_ADDR', getenv('REMOTE_ADDR'));

				$this->webapp->redirect('/adm/main');



		}else{
                $this->webapp->moveBack('비밀번호를 확인해주시기 바랍니다.');
            }

		 break;
		} 
	}
	
	/**
	 * 관리자 로그아웃
	 *
	 */	
	function logout(){
		$this->session->sess_destroy();
		if(THEME != "keyword"){ 
			$this->webapp->redirect('/main');
		}else{
			$this->webapp->redirect('/adm/login');
		}
	}
	
	/**
	 * 관리자 메인 화면
	 *
	 * @return	object	member_count	전체회원수	
	 */	
	
	function Main(){
		$tpl = $this->display;
		$tpl->setLayout($this->sub_layout);
		
		//관리자용 회원 모델
		$this->load->model('admin/member');
		
		$this->load->model('user/mdl_online');
		$online_cu = $this->mdl_online->load_total();

		$tpl->assign($online_cu);
		
		
		//전체 회원수
		$tpl->assign(array(
				'member_count'=>$this->member->total_member(),

			));

		$tpl->define("CONTENT", $this->display->getTemplate("admin/main.htm"));
		
		$tpl->printAll();
	}


	/**
	 * 사이트 기본 정보 관리
	 *
	 * @return	void
	 */	
	
	function common(){
		$tpl = $this->display;
		$DB = $this->db;
		$this->load->model('admin/common');
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		//데이터 불러오기
		$data = $this->common->load_data();
		$tpl->assign($data);
	
		$tpl->setLayout($this->sub_layout);
		$tpl->define("CONTENT", $this->display->getTemplate("admin/common.htm"));
		
		$tpl->printAll();

		 break;
		case "POST":

		$this->common->save_data($_POST);
		$this->webapp->moveBack('저장되었습니다.');
	
		
		 break;
		} 
	}

	/**
	 * 관리자 암호 변경
	 *
	 * @return	void
	 */	
	
	function passwd(){
		$tpl = $this->display;
		$DB = $this->db;
		$this->load->model('admin/common');

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":

		$tpl->setLayout($this->sub_layout);
		$tpl->define("CONTENT", $this->display->getTemplate("admin/passwd.htm"));
		
		$tpl->printAll();

		 break;
		case "POST":

		if($this->common->change_admin_passwd($_POST) == "Y"){
			$this->webapp->moveBack('저장되었습니다.');
		}else{
			$this->webapp->moveBack('기존 암호가 일치하지 않습니다.');
		}

		 break;
		} 
	}


	/**
	 * 이용약관 관리
	 *
	 * @param	string	$mode	pra1:사이트이용약관 , pra2 : 개인보호정책
	 * @return	void
	 */	
	
	function pra($mode=""){
		$tpl = $this->display;
		$DB = $this->db;

		$this->load->model('/admin/pra');

		if(!$mode) { //파일 업로드로 인하여 기본값처리 반드시 해줘야함
			$this->webapp->redirect(_BASEURL.'/member/'._QS);
			exit;
		}

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		//약관내용 불러오기
		$tpl->assign($this->pra->load_text());
		$tpl->assign(array('mode'=>$mode));
		
		$tpl->setLayout($this->sub_layout);
		$tpl->define("CONTENT", $this->display->getTemplate("admin/pra.htm"));
		
		$tpl->printAll();

		 break;
		case "POST":
		

		$this->pra->save_text($_POST);
		$this->webapp->redirect(_BASEURL.'/'._QS,'저장되었습니다.');

		 break;
		} 
	}


	
	
	function popup($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		$this->load->library('iniconf');
		$this->load->helper('file');

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		
		$site_data = @parse_ini_file(_DOC_ROOT.'/application/config/'.THEME.'/site_config.php',true);
		if(!$site_data[popup_start]) $site_data[popup_start] = date("Y-m-d");
		if(!$site_data[popup_end]) $site_data[popup_end] = date("Y-m-d",mktime() + (86400 *30));
		$tpl->assign(array(
			'popup_start'=>$site_data[popup_start],
			'popup_end'=>$site_data[popup_end],
			'popup_use'=>$site_data[popup_use],
		 ));
		
		

		 if(is_file(_DOC_ROOT.'/data/docs/popup.html')){
			$contents = read_file(_DOC_ROOT.'/data/docs/popup.html');
			
		 }else{
			$contents ="";

		 }
		$sect = "popup/popup";
		$tpl->assign(array(
			'contents'=>$contents,
			'sect'=>$sect,
		 ));
		 

		$tpl->setLayout($this->sub_layout);
		 $tpl->define("CONTENT", $this->display->getTemplate("admin/popup.htm"));
		 $tpl->printAll();
		
		 break;
		case "POST":
		
		$data[str_text] = $_POST[contents];
		
		$s = preg_match_all("/<img\s+.*?src=[\"\']([^\"\']+)[\"\'\s][^>]*>/is", $data[str_text], $m); 
		$tmp_img_list = $m[1];


		for($ii=0; $ii<count($tmp_img_list); $ii++) {
			if(!strstr($tmp_img_list[$ii],$_SERVER[HTTP_HOST]) && strstr($tmp_img_list[$ii],'http://')){
				$tmp_img = $this->curl->curl_url($tmp_img_list[$ii]);
				$filename = md5(array_pop(explode("/",$tmp_img_list[$ii]))).".gif";
					
				$s = fopen('data/files/popup_'.$filename, "w");
				fwrite($s, $tmp_img);
				fclose($s);

				$data[str_text] = str_replace($tmp_img_list[$ii],'/data/files/popup_'.$filename,$data[str_text]);
				
			}
		}
		
		
		
		write_file(_DOC_ROOT.'/data/docs/popup.html', $data[str_text]);
		
		$this->webapp->daumEditUpload($_POST[attach_img_file],"image");
		$this->webapp->daumEditUpload($_POST[attach_file],"file"); 
		
		
			
			$this->iniconf->load(_DOC_ROOT.'/application/config/'.THEME."/site_config.php");
			$this->iniconf->setVar("popup_start",$_POST[popup_start]);
			$this->iniconf->setVar("popup_end",$_POST[popup_end]);
			$this->iniconf->setVar("popup_use",$_POST[popup_use]);
			
			write_file(_DOC_ROOT.'/application/config/'.THEME."/site_config.php", $this->iniconf->_combine());

			$this->webapp->redirect('/admin/site_adm/popup/?PageNum='.$_POST[PageNum]);
		 
		 break;
		}
	}





	
}//

/* End of file .site_adm.php */
/* Location: ./application/controllers/site_adm.php */
