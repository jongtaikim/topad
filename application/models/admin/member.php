<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-17
* 작성자: 김종태
* 설   명:  회원정보 모델
*****************************************************************
* 
*/

class member extends CI_Model {

   var $MEMBER_TABLE =  "tamember";
 
    function __construct()
    {
        parent::__construct();
	
    }

    /**
     * 전체회원수 카운트
     *
     * @return	object	
     * @return	void
     */	
    
    function total_member(){
		//회원수 가져오기
		$sql = "select count(*) from tamember where isAdmin ='N' and delcheck = 'N'  ";
		$member_count = $this->db->sqlFetchOne($sql);
		return $member_count;
		
    }

    /**
     * 회원리스트 
     *
     * @return	object	$page : 현재 페이지, $listnum : 한페이지당 로드할 개수
     * @return	object	$list_data =리스트데이터 ,  array(page,total,listnum) => tpl assign 
     * @return	void
     */	
    
    function member_list($page=1,$listnum=15,$key="",$search=""){
		if($page==1) $this->member_birth_sync(); //생년월일 치환
		$list_data = $this->webapp->listPageingRow($this->MEMBER_TABLE," isAdmin ='N' and delcheck = 'N'  ", " order by regdate desc ",$page,$listnum,$key,$search);
		return $list_data;
    }

    /**
     * 엑셀용 회원리스트 
     *
     * @return	object	
     * @return	object	$list_data =리스트데이터 ,  array(page,total,listnum) => tpl assign 
     * @return	void
     */	
    
    function member_list_excel(){
		$sql = "select * from ".$this->MEMBER_TABLE." where isAdmin ='N' and delcheck = 'N'  order by regdate desc ";
		$list_data = $this->db-> sqlFetchAll($sql);
		return $list_data;
    }
    
     /**
     * 회원 생년월일 치환 
     *
     * @return	object	
     * @return	object	
     * @return	void
     * ... 할수있는데까지 해보자
     */	
    
    function member_birth_sync(){
		$sql = "update  `tamember` set birth2 = DATE_FORMAT(REPLACE(REPLACE(REPLACE(REPLACE(birth, ' ', '-'), '일', ''), '월', '-'), '년', '-') ,'%Y-%m-%d');";
		$this->db->query($sql);
    }


     /**
     * 회원상세보기 
     *
     * @return	object	$userid		회원아이디
     * @return	void
     */	
    
    function member_view($userid=''){
		if($userid){
			$sql = "select * from ".$this->MEMBER_TABLE." where  userid = '".$userid."'  ";
			$data = $this->db -> sqlFetch($sql);
		}
		return $data;
    }


     /**
     * 회원정보 변경
     *
     * @return	object	$userid		회원아이디
     * @return	void
     */	
    
    function member_modify($_POST_DATA){
		foreach( $_POST_DATA as $val => $value ){
			if(strstr($val,"db_")){
				$val_ = substr($val,3,100);
				$datas[$val_] = $value;
			}
		}
		
		
		if(!$datas[email_ch]) $datas[email_ch] = 'N';
		if(!$datas[ckyog]) $datas[ckyog] = 'N';

		
		/*if($datas[str_passwd]) {
			$datas[str_passwd2] = $datas[str_passwd];
			$datas[str_passwd] = md5($datas[str_passwd]);
		}else{
			unset($datas[str_passwd]);
		}*/
		

		$this->db->updateQuery($this->MEMBER_TABLE , $datas," userid = '".$_POST_DATA[userid]."' ");

    }

  

     /**
     * 회원 현황을 출력 
     *
     * @return	object	$type  addr:
     * @return	object	
     * @return	void
     * ... 할수있는데까지 해보자
     */	
    
    function state_data($type=''){

		switch ($type) {
		case "addr":
	
			$sql = "select if(addr='','없음',addr) as label , count(*) as data from tamember where isAdmin ='N' and delcheck = 'N' group by addr";
			$data = $this->db->sqlFetchAll($sql);
			return str_replace('"',"'",json_encode($data));
		
		 break;
		case "etc_1":
			$sql = "select if(etc_1='','없음',etc_1) as label , count(*) as data from tamember where isAdmin ='N' and delcheck = 'N' group by etc_1";
			$data = $this->db->sqlFetchAll($sql);
			return str_replace('"',"'",json_encode($data));
		 break;
		}
		
		
		
    }






	
}

?>