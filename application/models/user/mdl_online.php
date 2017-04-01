<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-21
* 작성자: 김종태
* 설   명:  비포에프터
*****************************************************************
* 
*/

class mdl_online extends CI_Model {
   

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
	 * 수술항목 카테고리
	 *
	 * @return	void
	 */	
	function category($_GET_DATA=''){

	
		$etc_1 = $this->config->item('member_etc_1');
		$etc_1_section = $this->config->item('member_etc_1_section');
		$i=0;
		foreach($etc_1 as $val => $value )
		{	
			if($value){
				$data[$i]['item'] =$value;
				$data[$i]['section'] = $etc_1_section[$value];
				
				$i++;
			}
		} 
		
	
		$this->display->assign(array('cate_list'=>$data));
		return $data;
	 }

	 
	/**
	 * 온라인 전체 개수
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_total(){
		
		$sql = "
			select 
			(SELECT count(*) FROM t_consultation where `delete` = 'N' ) as total_cu,
			(SELECT count(*) FROM t_consultation_reply a , t_consultation b where a.lind= b.ind and b.delete='N'  )   as re_total_cu
		";

		$total = $this->db->sqlFetch($sql);

		return $total;
	}
	
	
	/**
	 * 온라인  리스트 가져오기
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_list($page=1,$listnum=15,$_GET_DATA=''){
		
		//리스트 로드

		if($_GET_DATA[db_title]) {
			$psql = " and a.subject = '".$_GET_DATA[db_title]."' ";
		}
			
		$row = $this->webapp->listPageingRow("t_consultation a"," a.`delete` = 'N'  $psql ", " order by a.ind desc ",$page,$listnum,"a.".$_GET_DATA['search_key'],$_GET_DATA[search_value],
		" 
			a.`subjectext`, a.`subject`, a.`title`, a.`name`, a.`id`, a.`date`, a.`ind`, a.`ref` , a.`pw` ,
			
			 (SELECT COUNT(*) FROM `t_consultation_reply` WHERE `lind` = a.`ind`) AS `reply`

		");


		return $row;
		
	}
	

	/**
	 * 온라인  내용가져오기
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_view($idx=''){
		
		if($idx){

			$sql = "select a.email, a.phone,a.subject, a.title, a.name, a.id, a.date, a.ind, a.ref, b.text, a.comment, a.pw
					from t_consultation a left join t_consultation_reply b on ( a.ind = b.lind)
					where a.ind = ".$idx."";

		
			$row = $this->db-> sqlFetch($sql);

			return $row;
		}
		
	}
	
	
	/**
	 * 온라인 상담 등록
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function online_add($data){
	

		foreach( $data as $val => $value ){
			
			$val = "`".$val."`";
			$datas[$val] = $value;
			
		} 

		$datas['date'] = date("Y-m-d H:i:s");
		$datas['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->db->insertQuery('t_consultation',$datas);
		$idx = $this->db->insert_id();

		
		
		return $idx;
	
	

	}

	/**
	 * 온라인 사진 등록
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function online_re($data,$idx){
		foreach( $data as $val => $value ){
			
			$val = "`".$val."`";
			$datas[$val] = $value;
			
		} 
		
		$sql = "delete from t_consultation_reply where  lind = '".$idx."' ";
		$this->db->query($sql);

		$datas['date'] = date("Y-m-d H:i:s");
		$datas['ip'] = $_SERVER['REMOTE_ADDR'];
		$this->db->insertQuery('t_consultation_reply',$datas);
		$idx = $this->db->insert_id();

		
		
		return $idx;
	

	}

	/**
	 * 온라인 상담 삭제
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function online_del($mode='',$idx=''){
	
		if($idx){

			if($mode == 'all'){
				$sql = "delete from t_consultation where ind = '".$idx."' ";
				$this->db->query($sql);
			}
		
			$sql = "delete from t_consultation_reply where  lind = '".$idx."' ";
			$this->db->query($sql);
	
		}
		return $idx;
	}
	
	/**
	 * 현황 그래프
	 *
	 * @param	string	$aaa	주석
	 * @return	object	$aaa
	 * @return	void
	 */	
	
	function state_data($type=''){

		switch ($type) {
		case "json_1":
	
			$sql = "
				select 
					if(subjectext='','없음',subjectext) as label , count(*) as data
					 
				from t_consultation group by subjectext

			";
			$data = $this->db->sqlFetchAll($sql);
			return str_replace('"',"'",json_encode($data));
		
		 break;
		case "json_2":
			
			$sql = "
				select 
					DATE_FORMAT(date,'%m') as label , count(*) as data
					 
				from t_consultation where date >= '".date("Y")."-01-01' and date <= '".date("Y")."-12-31' group by label

			";
			$data = $this->db->sqlFetchAll($sql);
			return $data;
		 break;
		}
		
		
		
    }

   
	
}///

?>