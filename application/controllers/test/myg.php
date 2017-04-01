<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-30
* 작성자: 김종태
* 설   명: 마이그레이션
*****************************************************************
* 
*/
class myg extends CI_Controller {
	
	  
	 function __construct(){
		parent::__construct();
		$tpl = $this->display;
		
	}
	
	function index(){
		exit;
		$this->notice_myg();
		$this->press_myg();
		$this->review_myg();
		$this->real_myg();
		$this->real_self();
	}
		  
	   function notice_myg(){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB1 = $this->load->database('default', TRUE);
		$DB2 = $this->load->database('old', TRUE);
		
		$mcode = "1817";
		
		$sql = "delete from tab_board where num_mcode = '".$mcode."'  ";
		$DB1 -> query($sql);

		$sql = "select * from t_notice order by ind desc  ";
		$row = $DB2 -> sqlFetchAll($sql);
		
		for($ii=0; $ii<count($row); $ii++) {
			unset($data);
			$data[num_oid] = "100";
			$data[num_mcode] = $mcode;
			$data[num_serial] = $DB1 -> sqlFetchOne("select max(num_serial)+1 from tab_board where num_mcode = '".$mcode."' ");
			if(!$data[num_serial]) $data[num_serial] =1;
			$data[num_notice] = "0";
			$data[num_group] = $data[num_serial];
			$data[num_depth] = "0";
			$data[str_user] = "admin";
			$data[str_name] = "admin";
			$data[str_pass] = "dkdlxlzjavjsl";
			$data[str_title] = $row[$ii][title];

			$data[str_text] = str_replace('"/images/notice/','"/data/notice/',$row[$ii][comment]);;
			$data[chr_html] = 'Y';
			$data[dt_date] = $row[$ii][date];
			$data[num_hit] = $row[$ii][ref];
				
			$DB1->insertQuery($table="tab_board",$data);
		}
		
		
			
	  }

	 



}

/* End of file sub.php */
/* Location: ./application/controllers/test/page.php */