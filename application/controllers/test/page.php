<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명: 테스트페이지
*****************************************************************
* 
*/
class page extends CI_Controller {
	
	  
	 function __construct(){
		parent::__construct();
		$tpl = $this->display;
		$this->load->database('scrolling_db');
		$this->load->model('test/mdl_test');
	}
	


	  //2012-02-21 메인페이지
	   function index(){
		echo "<meta http-equiv='Refresh' Content=\"0; URL='/test/page/smain'\">";
		exit;
	  }

	  function smain($page=1,$date=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB =  $this->db;
		
		if(!$date) $date = date("Y-m-d");
		$tpl->assign(array('this_date'=>$date));

		$row = $this->mdl_test->rank_list($page,100,'','',$date);
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@test');
		$tpl->define('CONTENT', $this->display->getTemplate('test/crontab_view.htm'));
		$tpl->printAll();

	  }

	   function view_keyword($site_name='',$keyword='',$dates='', $times=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB =  $this->db;
		
		if($times =='') $times = date('H');
		if($times =='9999') $times = '';

		$keyword = urldecode($keyword);
		$site_name = urldecode($site_name);
		$tpl->assign(array(
				'keyword'=>$keyword,
				'site_name'=>$site_name,
				'dates'=>$dates,
				'times'=>$times,
		));
		
		$row = $this->mdl_test->rank_view($site_name,$keyword,$dates,$times);
		$i = 0;
		for($ii=0; $ii<count($row); $ii++) {
			$i++;
			for($ia=0; $ia<count($row[$ii]['list']); $ia++) {
				$i++;
			}
			
		}
		
		$tpl->assign(array(
			'LIST'=>$row,
			'max_i'=>$i,
		));

		//$row = $this->mdl_test->rank_list();
		
		$tpl->setLayout('none');
		$tpl->define('CONTENT', $this->display->getTemplate('test/crontab_view2.htm'));
		$tpl->printAll();

	  }



	   function html_prview($scrolling_run_info_idx=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB =  $this->db;
		

		$tmp_html = $this->mdl_test->html_view($scrolling_run_info_idx);
		$tmp_html = str_replace('window.open(location, "_top")',"",$tmp_html);
		
		echo $tmp_html;
		
	  }

	  function info(){
		phpinfo();
	  }

	





}

/* End of file sub.php */
/* Location: ./application/controllers/test/page.php */