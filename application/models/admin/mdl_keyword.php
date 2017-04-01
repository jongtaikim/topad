<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2016-01-04
* 작성자: 김종태
* 설   명:  키워드 모델
*****************************************************************
* 
*/

class mdl_keyword extends CI_Model {
   

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

	//업체목록
	function company_list($page=1,$listnum=15){
		$row = $this->webapp->listPageingRow($table='keyword_company','',$orderby=" order by idx desc ",$page,$listnum);
		return  $row;
	}

	//업체보기
	function company_view($idx=''){
		$sql = "select * from keyword_company where idx = '".$idx."' ";
		$data = $this->db->sqlFetch($sql);
		return  $data;
	}

	//업체 등록수정
	function company_data($idx='',$data){
		if($idx){
			$this->db->updateQuery($table="keyword_company",$data,$cond=" idx = '".$idx."' ");
		}else{
			$data[reg_date] = date("Y-m-d");
			$this->db->insertQuery($table="keyword_company",$data);
		}
	}

	//업체 삭제
	function company_del($idx=''){
		if($idx){
			$this->db->deleteQuery($table="keyword_company",$cond=" idx = '".$idx."' ");
		}
	}

	
	//키워드목록
	function keyword_list($company_code='',$page=1,$listnum=15){
		
		//업체코드 구분
		$crom = " b.company_idx = '".$company_code."' and b.company_idx = a.idx ";


		$row = $this->webapp->listPageingRow('keyword_company a , keyword_content b',$crom,$orderby=" order by b.use_stat desc, b.idx desc ",$page,$listnum);
		return  $row;
	}

	//키워드보기
	function keyword_view($company_code='',$idx=''){
	
		$sql = "select * from keyword_content where idx = '".$idx."' ";
		
		$data = $this->db->sqlFetch($sql);
		return  $data;
	}

	//키워드 등록수정
	function keyword_data($idx='',$data){

	
		if($idx){
			$this->db->updateQuery($table="keyword_content",$data,$cond=" idx = '".$idx."' ");
		}else{
			$data[reg_date] = date("Y-m-d");
			$this->db->insertQuery($table="keyword_content",$data);
		}
	}

	//키워드 삭제
	function keyword_del($idx=''){
		if($idx){
			$this->db->deleteQuery($table="keyword_content",$cond=" idx = '".$idx."' ");
		}
	}
   
   
	
}///

?>