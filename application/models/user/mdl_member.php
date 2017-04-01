<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-21
* 작성자: 김종태
* 설   명:  컨텐츠 메뉴 모델
*****************************************************************
* 
*/

class mdl_member extends CI_Model {
   

    function __construct()
    {
        parent::__construct();
	 $this->load->database();
	 $WA = $this->webapp;
	 $tpl = $this->display;
	 $DB = $this->db;
	 $this->load->library('session');
	 $_SESSION = $this->session->all_userdata();
    }
   
   /**
    * ajax로 아이디 중복검사
    *
    * @param	string	$types	타입
    * @return	void
    */	
   
   function ajaxCont($types="",$userid=""){
	
		 switch ($types) {
		 
		  case "userid":

			  $sql = "select count(*) from tamember where  userid = '".$userid."' ";
			  $counter = $this->db-> sqlFetchOne($sql);
			  if($counter > 0){
				echo "N";
			  }else{
				echo  "Y";
			  }
		  break;
		} 
	
		exit;
  }

  /**
   * 회원가입
   *
   * @param	string	$_POST_DATA	POST데이터
   * @return	void
   */	
  
   function member_join($_POST_DATA){
	$datas[userid] = $_POST_DATA[userid];
	$datas[username] = $_POST_DATA[username];
	if($_POST_DATA[username]) $datas[username] =$_POST_DATA[username];
	$datas[addr] = $_POST_DATA[addr1];
	
	if(!$_POST_DATA[email_full]){
		$datas[email] = $_POST_DATA[email]."@".$_POST_DATA[email_value2];
	}else{
		$datas[email] =$_POST_DATA[email_full];
	}
	if(!$datas[phone]){
		$datas[celphone] = $_POST_DATA[celphone1]."-".$_POST_DATA[celphone2]."-".$_POST_DATA[celphone3];
	}else{
		$datas[celphone] =$_POST_DATA[phone];
	}
	$datas[password] = md5($_POST_DATA[pwd]);
	$datas[ckyog] = $_POST_DATA[ckyog];
	if($_POST_DATA[in_path]){
		$datas[in_path] = $_POST_DATA[in_path];
	}else{
		$datas[in_path] = "web";
	}
	$datas[etc_1] = $_POST_DATA[etc_1];
	$datas[etc_2] = $_POST_DATA[etc_2];
	$datas[birth] = $_POST_DATA[birth];
	$datas[birth2] = $_POST_DATA[birth];
	$datas[ip_addr] = $_SERVER['REMOTE_ADDR'];
	
	$datas[regdate] = date("Y-m-d h:i:s");
	$datas[edit_date] = date("Y-m-d h:i:s");
	
	$this->db->insertQuery('tamember',$datas);

   }


   /**
   * 회원 정보 수정
   *
   * @param	string	$userid 회원아이디, $_POST_DATA 	POST데이터
   * @return	void
   */	
   function member_modify($userid='',$_POST_DATA){
	  if($userid){
		$datas[username] = $_POST_DATA[usernames];
		if($_POST_DATA[username]) $datas[username] =$_POST_DATA[username];
		$datas[addr] = $_POST_DATA[addr1];
		
		if(!$_POST_DATA[email_full]){
			$datas[email] = $_POST_DATA[email]."@".$_POST_DATA[email_value2];
		}else{
			$datas[email] =$_POST_DATA[email_full];
		}
		if(!$datas[phone]){
			$datas[celphone] = $_POST_DATA[celphone1]."-".$_POST_DATA[celphone2]."-".$_POST_DATA[celphone3];
		}else{
			$datas[celphone] =$_POST_DATA[phone];
		}
		if($_POST_DATA[pwd]) $datas[password] = md5($_POST_DATA[pwd]);
		$datas[ckyog] = $_POST_DATA[ckyog];
		$datas[etc_1] = $_POST_DATA[etc_1];
		$datas[etc_2] = $_POST_DATA[etc_2];
		$datas[birth] = $_POST_DATA[birth];
		$datas[birth2] = $_POST_DATA[birth];
		
		$datas[edit_date] = date("Y-m-d h:i:s");

		
		$this->db->updateQuery('tamember',$datas," userid = '".$userid."' ");
	  }

   }
   

   /**
    * 회원명과 이메일로 데이터 조회
    *
    * @param	string	$usernames	이름, $emailval 완전한 이메일
    * @return	void
    */	
   function select_name_email($usernames,$emailval){
	$sql = "select * from tamember where username='".$usernames."' and email='".$emailval."'";
	$data = $this->db->sqlFetch($sql);
	if($data){
		$this->display->assign($data);
		return $data;
	}
    }

   
    /**
    * 회원명과 휴대폰 번호로 조회
    *
    * @param	string	$usernames	이름, $number 휴대폰 번호
    * @return	void
    */	
   function select_name_celphone($usernames,$number){
	$sql = "select * from tamember where userid='".$usernames."' and REPLACE(celphone,'-', '') = '".str_replace( '-', '', $number )."'  ";
	$data = $this->db->sqlFetch($sql);
	if($data){
		$this->display->assign($data);
		return $data;
	}
    }

     /**
    * 아이디와 비밀번호로 조회
    *
    * @param	string	$userid	아이디, $passwd 비밀번호
    * @return	void
    */	
   function select_id_pw($userid,$passwd){
	$sql = "select * from tamember where userid = '".trim($userid)."' and  password = '".md5(trim($passwd))."' and delcheck='N'  ";
	$data = $this->db->sqlFetch($sql);
	
	if($data){
		$data[chr_mtype] = "g"; //일반회원입니다.
		return $data;
	}
    }

     /**
    * 회원정보 수정일 업데이트
    *
    * @param	string	$userid	아이디
    * @return	void
    */	
   function update_edit_date($userid){
	  $this->db->query("update tamember  set  edit_date = '".date("Y-m-d")."' where userid = '".trim($userid)."'  and delcheck='N' ");
    }

  
	
    /**
    * 회원아이디로 정보 조회
    *
    * @param	string	$userid	회원아이디
    * @return	void
    */	
   function select_id($userid){
	$sql = "select * from tamember where userid = '".trim($userid)."' and  delcheck='N'  ";
	$data = $this->db->sqlFetch($sql);
	
	if($data){
		$data[chr_mtype] = "g"; //일반회원입니다.
		list($data[email1],$data[email2]) = explode("@",$data[email]);
		list($data[celphone1],$data[celphone2],$data[celphone3]) = explode("-",$data[celphone]);

		$this->display->assign($data);
		return $data;
	}
    }

     /**
    * 회원 암호 변경
    *
    * @param	string	$userid	아이디, $passwd 암호
    * @return	void
    */	
   function change_password($userid,$passwd){
	$this->db->query("update tamember set password='".md5($passwd)."' where userid='".$userid."'  ");
    }

     /**
    * 회원 탈퇴처리
    *
    * @param	string	$userid	아이디, $passwd 암호
    * @return	void
    */	
   function del_member($userid,$passwd){
	//$passwd = $this->mysql_password($passwd);

	$this->db->query(" update tamember set delcheck='Y' where password='".md5($passwd)."' and userid='".$userid."'  ");
    }

     /**
    * password 쿼리 처리
    *
    * @return	void
    */	
   function mysql_password($passwd){
	   $sql = "SELECT PASSWORD('".$passwd."') as pass  ";
	   $rpas = $this->db -> sqlFetchOne($sql);
	  return $rpas;
    }
	
}///

?>