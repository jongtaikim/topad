<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2016-01-15
* 작성자: 김종태
* 설   명:  
*****************************************************************
* 
*/

class mdl_fb extends CI_Model {

    var $curlopt_referer = '';
    var $curlopt_header = '';

    function __construct()
    {
        parent::__construct();
	 
    }

    function fb_db_save($fbid='',$fbname='',$fbphoto='',$fbaccessToken='',$fb_email=''){
		$data[type] = 'facebook';
		$data[userid] = $fbid;
		$data[username] = $fbname;
		$data[userphoto] = $fbphoto;
		$data[accessToken] = $fbaccessToken;
		$data[email] = $fb_email;
		
		if($data[userid]){
			$sql = "select count(*) from sns_login where userid = '".$fbid."' and type = 'facebook' ";
			$ind = $this->db -> sqlFetchOne($sql);

			if(!$ind){
				$this->db->insertQuery($table="sns_login",$data);
			}

			$this->session->set_userdata('NAME', $fbid);
			$this->session->set_userdata('EMAIL', $fb_email);
			$this->session->set_userdata('USERID', $fbid);
			$this->session->set_userdata('PASSWORD', $fbid);
			/*$this->session->set_userdata('MTYPE',$member_data[chr_mtype]);
			$this->session->set_userdata('MTYPE_NAME',$member_type[$member_data[chr_mtype]]);
			$this->session->set_userdata('MEM_TYPE', array($member_data[chr_mtype]));*/
			$this->session->set_userdata('AUTH', TRUE);
			$this->session->set_userdata('REMOTE_ADDR', getenv('REMOTE_ADDR'));

			return $data;

		}
		
    }


     function kakao_db_save($id='',$name='',$photo=''){
		$data[type] = 'kakao';
		$data[userid] = $id;
		$data[username] = $name;
		$data[userphoto] = $photo;
		
		if($data[userid]){
			$sql = "select count(*) from sns_login where userid = '".$id."' and type = 'kakao' ";
			$ind = $this->db -> sqlFetchOne($sql);

			if(!$ind){
				$this->db->insertQuery($table="sns_login",$data);
			}

			$this->session->set_userdata('NAME', $name);
			//$this->session->set_userdata('EMAIL', $member_data[email]);
			$this->session->set_userdata('USERID', $id);
			$this->session->set_userdata('PASSWORD', $id);
			/*$this->session->set_userdata('MTYPE',$member_data[chr_mtype]);
			$this->session->set_userdata('MTYPE_NAME',$member_type[$member_data[chr_mtype]]);
			$this->session->set_userdata('MEM_TYPE', array($member_data[chr_mtype]));*/
			$this->session->set_userdata('AUTH', TRUE);
			$this->session->set_userdata('REMOTE_ADDR', getenv('REMOTE_ADDR'));

			return $data;

		}
		
    }

     function naver_db_save($email='',$name=''){
		$data[type] = 'naver';
		$a_ = explode("@",$email);
			
		$data[userid] = $a_[0];
		$data[username] = $name;
		$data[email] = $email;
		
		if($data[userid]){
			$sql = "select count(*) from sns_login where userid = '".$data[userid]."' and type = 'naver' ";
			$ind = $this->db -> sqlFetchOne($sql);

			if(!$ind){
				$this->db->insertQuery($table="sns_login",$data);
			}

			$this->session->set_userdata('NAME', $name);
			//$this->session->set_userdata('EMAIL', $member_data[email]);
			$this->session->set_userdata('USERID', $id);
			$this->session->set_userdata('PASSWORD', $id);
			/*$this->session->set_userdata('MTYPE',$member_data[chr_mtype]);
			$this->session->set_userdata('MTYPE_NAME',$member_type[$member_data[chr_mtype]]);
			$this->session->set_userdata('MEM_TYPE', array($member_data[chr_mtype]));*/
			$this->session->set_userdata('AUTH', TRUE);
			$this->session->set_userdata('REMOTE_ADDR', getenv('REMOTE_ADDR'));

			return $data;

		}
		
    }
	
}

?>