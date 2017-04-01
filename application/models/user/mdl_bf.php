<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-21
* 작성자: 김종태
* 설   명:  비포에프터
*****************************************************************
* 
*/

class mdl_bf extends CI_Model {
   

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
		$i=0;

		foreach($etc_1 as $val => $value )
		{	
			
			if($value){
				$data[$i]['item'] =$value;
				if($_GET_DATA['how']){
					if(in_array($value, $_GET_DATA['how'])){
						$data[$i]['checked_tn'] ="Y";
					}
				}
				$i++;
			}
		} 

	

		$this->display->assign(array('cate_list'=>$data));
		return $data;
	 }

	 
	/**
	 * 전후 전체 개수
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_total(){
		
		$sql = "
		SELECT count(*)
				FROM `before_after` a 
				LEFT OUTER JOIN `_tags` e ON e.`board` = 'before_after' AND e.`board_idx` = a.`idx` AND e.`type` = 'how'
				
		";

		$total = $this->db->sqlFetchOne($sql);
		return $total;
	}
	
	
	/**
	 * 전후 사진 리스트 가져오기
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_list($page=1,$_GET_DATA='',$listnum=6){
		
		$__WHERE = "1";

		if(isset($_GET_DATA['how'])){
			$__WHERE .= " AND e.`tag` IN ('".implode("','", $_GET_DATA['how'])."')";
		}

		if($_GET_DATA['how2']){
			$__WHERE .= " AND e.`tag` = '".$_GET_DATA['how2']."'";
		}

		if(isset($_GET_DATA['idx'])){
			$__WHERE .= " AND a.`idx` = '{$_GET_DATA['idx']}'";
		}
		
		if($_GET_DATA['search_value']){
			$__WHERE .= " AND a.subject like '%".$_GET_DATA['search_value']."%' ";
		}
		
		if(!$_GET_DATA[is_admin]){
			$psql = " AND a.`delete`='N' ";
		}


		$sql = "
		SELECT count(*)
				FROM `before_after` a 
				LEFT OUTER JOIN `_tags` e ON e.`board` = 'before_after' AND e.`board_idx` = a.`idx` AND e.`type` = 'how'
				WHERE {$__WHERE} $add_where
				$psql
		";

		$total = $this->db->sqlFetchOne($sql);

		
		if(!$total) $total = 0;
		if (!$page) $page = 1;
		
		$seek = $listnum * ($page - 1);
		$offset = $seek + $listnum;


		$sql = "SELECT SQL_CALC_FOUND_ROWS a.* 
					, GROUP_CONCAT(DISTINCT b.`tag` ORDER BY b.`tag`) AS `pos`
					, GROUP_CONCAT(DISTINCT c.`tag` ORDER BY c.`tag`) AS `how` 
				FROM `before_after` a 
				LEFT OUTER JOIN `_tags` b ON b.`board` = 'before_after' AND b.`board_idx` = a.`idx` AND b.`type` = 'pos' 
				LEFT OUTER JOIN `_tags` c ON c.`board` = 'before_after' AND c.`board_idx` = a.`idx` AND c.`type` = 'how'
				LEFT OUTER JOIN `_tags` d ON d.`board` = 'before_after' AND d.`board_idx` = a.`idx` AND d.`type` = 'pos' 
				LEFT OUTER JOIN `_tags` e ON e.`board` = 'before_after' AND e.`board_idx` = a.`idx` AND e.`type` = 'how'
				WHERE {$__WHERE} $add_where
				$psql
				GROUP BY a.`idx`
				ORDER BY a.`idx` DESC
				LIMIT  $seek , $listnum ";

	
		$row = $this->db-> sqlFetchAll($sql);


		
		$this->display->assign(array(
		'page'=>$page,
		'total'=>$total,
		'listnum'=>$listnum,
		));
		

		return $row;
		
	}
	

	/**
	 * 전후 사진 내용가져오기
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function load_view($idx=''){
		
		if($idx){

			$sql = "SELECT SQL_CALC_FOUND_ROWS a.* 
						, GROUP_CONCAT(DISTINCT b.`tag` ORDER BY b.`tag`) AS `pos`
						, GROUP_CONCAT(DISTINCT c.`tag` ORDER BY c.`tag`) AS `how` 
					FROM `before_after` a 
					LEFT OUTER JOIN `_tags` b ON b.`board` = 'before_after' AND b.`board_idx` = a.`idx` AND b.`type` = 'pos' 
					LEFT OUTER JOIN `_tags` c ON c.`board` = 'before_after' AND c.`board_idx` = a.`idx` AND c.`type` = 'how'
					LEFT OUTER JOIN `_tags` d ON d.`board` = 'before_after' AND d.`board_idx` = a.`idx` AND d.`type` = 'pos' 
					LEFT OUTER JOIN `_tags` e ON e.`board` = 'before_after' AND e.`board_idx` = a.`idx` AND e.`type` = 'how'
					WHERE 
					  a.idx = '".$idx."'";

		
			$row = $this->db-> sqlFetch($sql);

			return $row;
		}
		
	}
	
	/**
	 * 전후 사진 내용수정하기
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function bf_modify($idx,$data){
	
	if($idx){
		foreach( $data as $val => $value ){
			if($val !="db_how" && $val !="db_pos"){
				$val = "`".$val."`";
				$datas[$val] = $value;
			}
		} 

		$datas['edit_date'] = date("Y-m-d H:i:s");
		$this->db->updateQuery('before_after',$datas," idx = '".$idx."' ");
		
		if($data[db_how]){
			$sql = "delete from _tags where type = 'how' and board_idx = '".$idx."' ";
			$this->db->query($sql);
			$how_row = explode(",",$data[db_how]);

			for($ii=0; $ii<count($how_row); $ii++) {
				$datas_how[board] = 'before_after';
				$datas_how[board_idx] = $idx;
				$datas_how[type] = 'how';
				$datas_how[tag] = $how_row[$ii];
				$this->db->insertQuery('_tags',$datas_how);
				unset($datas_how);
			}
			
		}

		if($data[db_pos]){
			$sql = "delete from _tags where type = 'pos' and board_idx = '".$idx."' ";
			$this->db->query($sql);
			$how_row = explode(",",$data[db_pos]);

			for($ii=0; $ii<count($how_row); $ii++) {
				$datas_how[board] = 'before_after';
				$datas_how[board_idx] = $idx;
				$datas_how[type] = 'pos';
				$datas_how[tag] = $how_row[$ii];
				$this->db->insertQuery('_tags',$datas_how);
				unset($datas_how);
			}
			
		}

		return $idx;
	}
	
	}
	/**
	 * 전후 사진 내용등록
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function bf_add($data){
	

		foreach( $data as $val => $value ){
			if($val !="db_how" && $val !="db_pos"){
				$val = "`".$val."`";
				$datas[$val] = $value;
			}
		} 

		$datas['date'] = date("Y-m-d H:i:s");
		$datas['edit_date'] = date("Y-m-d H:i:s");
		$this->db->insertQuery('before_after',$datas);
		$idx = $this->db->insert_id();

		if($data[db_how]){
			$sql = "delete from _tags where type = 'how' and board_idx = '".$idx."' ";
			$this->db->query($sql);
			$how_row = explode(",",$data[db_how]);

			for($ii=0; $ii<count($how_row); $ii++) {
				$datas_how[board] = 'before_after';
				$datas_how[board_idx] = $idx;
				$datas_how[type] = 'how';
				$datas_how[tag] = $how_row[$ii];
				$this->db->insertQuery('_tags',$datas_how);
				unset($datas_how);
			}
			
		}

		if($data[db_pos]){
			$sql = "delete from _tags where type = 'pos' and board_idx = '".$idx."' ";
			$this->db->query($sql);
			$how_row = explode(",",$data[db_pos]);

			for($ii=0; $ii<count($how_row); $ii++) {
				$datas_how[board] = 'before_after';
				$datas_how[board_idx] = $idx;
				$datas_how[type] = 'pos';
				$datas_how[tag] = $how_row[$ii];
				$this->db->insertQuery('_tags',$datas_how);
				unset($datas_how);
			}
			
		}
		
		
		return $idx;
	
	

	}

	/**
	 * 전후 사진 삭제
	 *
	 * @param	string	$page =1	, $_GET_DATA , $listnum
	 * @return	void
	 */	
	
	function bf_del($idx=''){
	
		if($idx){

			$sql = "delete from before_after where idx = '".$idx."' ";
			$this->db->query($sql);
		
			$sql = "delete from _tags where  board_idx = '".$idx."' ";
			$this->db->query($sql);
	
		}
		return $idx;
	
	
	
	}

   
	
}///

?>