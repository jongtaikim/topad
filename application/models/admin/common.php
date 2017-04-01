<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-17
* 작성자: 김종태
* 설   명:  사이트 기본정보 모델
*****************************************************************
* 
*/

class common extends CI_Model {

   var $ORGAN_TABLE =  "tab_organ";
 
    function __construct()
    {
        parent::__construct();
	
    }

    /**
     * 사이트 기본정보 가져오기
     *
     * @return	object	
     * @return	void
     */	
    
    function load_data(){
		$sql = "select * from tab_organ where num_oid = '"._OID."' ";
		$data = $this->db -> sqlFetch($sql);
		$email = explode("@",$data[str_master_email]);

		$data[str_master_email1] = $email[0] ;
		$data[str_master_email2] = $email[1] ;
		return $data;
		
    }

    /**
     * 사이트 기본정보 저장하기
     *
     * @return	object	$_POST_DATA
     * @return	void
     */	
    
    function save_data($_POST_DATA){
		$datas[num_oid] = _OID;
		$datas[str_theme] = THEME;
		
		
		foreach( $_POST_DATA as $val => $value ){
			if(strstr($val,"num_") || strstr($val,"str_") || strstr($val,"chr_")){
				$datas[$val] = $value;
			}
		}

		$this->db->updateQuery('tab_organ',$datas," num_oid = '"._OID."'");
		
		$this->load->library('iniconf');
		$this->load->helper('file');
		$this->iniconf->load('application/config/'.THEME."/site_config.php");
		$this->iniconf->setVar("email",$datas[str_master_email]);
		$this->iniconf->setVar("oname",$datas[str_organ]);
		$this->iniconf->setVar("ophone",$datas[str_phone]);
		$this->iniconf->setVar("zipcode",$datas[chr_zip]);
		$this->iniconf->setVar("title",addslashes($datas['str_title']));
		
		write_file('application/config/'.THEME."/site_config.php", $this->iniconf->_combine());
		return 0;
		
    }
	

	/**
     * 관리자 암호 변경
     *
     * @return	object	$_POST_DATA
     * @return	void
     */	
    
    function change_admin_passwd($_POST_DATA){
		
		$site_data = @parse_ini_file(_DOC_ROOT.'/application/config/'.THEME.'/site_config.php',true);
		$datas[str_password] = md5($_POST_DATA[str_passwd]);

		if($site_data[admin_pw] == md5($_POST_DATA[def_passwd]) && $_POST_DATA[def_passwd] !=""){
			
			$this->db->updateQuery($this->ORGAN_TABLE,$datas," num_oid = '"._OID."'");
			$this->load->library('iniconf');
			$this->load->helper('file');
			$this->iniconf->load('application/config/'.THEME."/site_config.php");
			$this->iniconf->setVar("admin_pw",$datas[str_password]);

			write_file('application/config/'.THEME."/site_config.php", $this->iniconf->_combine());
			
			return "Y";
		}else{
			return "N";
		}

    }

   

	
}

?>