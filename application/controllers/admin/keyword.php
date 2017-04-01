<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2016-01-04
* 작성자: 김종태
* 설   명: 키워드
*****************************************************************
*
*/
class keyword extends CI_Controller {
	 var $site_menu="";
	 var $PERM="";

	 function __construct(){
		parent::__construct();
		$_SESSION = $this->session->all_userdata();

		$this->load->database();

		//모델
		$this->load->model('admin/admin_init');
		$this->load->model('admin/menu');

		$this->load->model('admin/mdl_keyword');

		$this->load->library('permission');
		$this->PERM =$this->permission;
	}



	  //업체관리 리스트
	   function company_list($page=1){

			$WA = $this->webapp;
			$tpl = $this->display;

			$row = $this->mdl_keyword->company_list($page,'15');
			$tpl->assign(array('LIST'=>$row));
			
			$tab_menu[0][title] = "업체 추가";
			$tab_menu[0][link] = "javascript:load_data_view();";
			$tab_menu[0][icon] = "fa fa-plus-square";
			$tpl->assign(array(
				'admin_MENU_tab'=>$tab_menu,
			));

			$tpl->setLayout('admin_sub');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/keyword/company_list.htm'));

			$tpl->printAll();

        }



	   //업체관리 읽기
	   function company_view($idx=0){

			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;

			switch ($_SERVER[REQUEST_METHOD]) {
				case "GET":

				$data = $this->mdl_keyword->company_view($idx);

				if($data){
					$tpl->assign($data);
				}

				$tpl->assign(array('idx'=>$idx,'mcode'=>$mcode));

				$tpl->setLayout('none');
				$tpl->define('CONTENT', $this->display->getTemplate('admin/keyword/company_view.htm'));

				$tpl->printAll();

				break;
				case "POST":

				foreach( $_REQUEST as $val => $value )
				{
					if(substr($val,0,3) == "db_"){
						$val_ = substr($val,3,255);
						$datas[$val_] = $value;
					}
				}
				 $this->mdl_keyword->company_data($idx,$datas);

				 break;
				}
        }



	 /**
	 *  업체관리 삭제
     */
	 function company_del($idx=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;
		if($idx){
			$data=$this->mdl_keyword->company_del($idx);
		}

	  }


	  //업체별 키워드 리스트
	   function keyword_list($company_idx='',$page=1){
			$WA = $this->webapp;
			$tpl = $this->display;

			if($company_idx){
				$this->session->set_userdata('_company_idx', $company_idx);

				//업체에 등록된 키워드 목록
				$row = $this->mdl_keyword->keyword_list($company_idx,$page,'15');
				$tpl->assign(array('LIST'=>$row));
				//업체정보
				$data = $this->mdl_keyword->company_view($company_idx);
				$tpl->assign(array(
						'company_name'=>$data[name],
						'company_idx'=>$company_idx,
					));


			}else{
				$row = $this->mdl_keyword->company_list($page,'100');
				
				$tab_menu[0][title] = "업체선택";
				$tab_menu[0]['link'] = "submenu";
				$tab_menu[0]['icon'] = "fa fa-building-o";
				for($ii=0; $ii<count($row); $ii++) {
					
					$tab_menu[0][submenu][$ii][title] = $row[$ii][name];
					$tab_menu[0][submenu][$ii]['link'] = "javascript:load_company_code(".$row[$ii][idx].")";
				}
				
				$tab_menu[1][title] = "키워드 추가";
				$tab_menu[1][link] = "javascript:load_data_view();";
				$tab_menu[1][icon] = "fa fa-plus-square";

				$tpl->assign(array(
					'company_LIST'=>$row,
					'admin_MENU_tab'=>$tab_menu,
					));
			}


			$tpl->assign(array('company_idx'=>$company_idx));
			$tpl->setLayout('admin_sub');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/keyword/text_list.htm'));

			$tpl->printAll();

        }



	   //업체별 키워드 수정/저장
	   function keyword_view($company_idx='',$idx=0){
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;

			switch ($_SERVER[REQUEST_METHOD]) {
				case "GET":
				
				
				$data = $this->mdl_keyword->keyword_view($company_idx,$idx);
				
				if($data){
					$tpl->assign($data);
				}

				$tpl->assign(array('idx'=>$idx,'company_idx'=>$company_idx));

				$tpl->setLayout('none');
				$tpl->define('CONTENT', $this->display->getTemplate('admin/keyword/text_view.htm'));

				$tpl->printAll();


				break;
				case "POST":

				foreach( $_REQUEST as $val => $value ){
					if(substr($val,0,3) == "db_"){
						$val_ = substr($val,3,255);
						$datas[$val_] = $value;
					}
				}

			
				 $this->mdl_keyword->keyword_data($idx,$datas);

				 break;
				}
        }



	  /**
	  *  업체별 키워드삭제
	  */
	 function keyword_del($idx=''){
		if($idx){
			$data=$this->mdl_keyword->keyword_del($idx);
		}
	  }

	  //수집한 네이버 검색어 리스트
	   function crontab_list($company_idx='',$page=1){
			$tpl = $this->display;

			if(!$_GET[year]) $year = date("Y"); else  $year= $_GET[year];
			if(!$_GET[month]) $month = date("m"); else  $month= $_GET[month];
			if(!$_GET[day]) $day = date("d"); else  $day= $_GET[day];

			$start_day = $year."-".$month."-01";
			$end_day = $year."-".$month."-31";

			$tpl->assign(array(
				'start_day'=>$start_day,
				'end_day'=>$end_day,

				'year'=>$year,
				'month'=>$month,
				'day'=>$day,
			));
			
		

			if($company_idx){
				$this->session->set_userdata('_company_idx', $company_idx);

				//업체에 등록된 키워드 목록
				$row = $this->mdl_keyword->keyword_list($company_idx,$page,'200');
				for($ii=0; $ii<count($row); $ii++) {

					for($i=0; $i<31; $i++) {

						unset($rank_data);
						$sql = "
						SELECT reg_date, keyword ,  rank as rank_num , keyword_text, target_url, reg_h FROM `keyword_rank`

						where

						reg_date = '".$year."-".$month."-".sprintf("%02d",($i+1))."' and
						keyword = '".$row[$ii][keyword]."' and
						target_url = '".$row[$ii][target_url]."' 
						/*and reg_h > 8 and reg_h < 22*/


						GROUP BY reg_date,keyword , target_url order by rank_num asc";
						$rank_data = $this->db -> sqlFetch($sql);
						
					
						

						$row[$ii][sub][$i][rank] = $rank_data[rank_num];
						$iia = $i-1;
						$row[$ii][sub][$i][d_rank] = $row[$ii][sub][$iia][rank];
						
						$row[$ii][sub][$i][keyword] = $rank_data[keyword];
						$row[$ii][sub][$i][keyword_text] = $rank_data[keyword_text];
						$row[$ii][sub][$i][reg_date] = $year."-".$month."-".sprintf("%02d",($i+1));
						$row[$ii][sub][$i][target_url] = $rank_data[target_url];
						$row[$ii][sub][$i][reg_h] = $rank_data[reg_h];
						if($rank_data[rank_num]) $row[$ii][r_total]++;

					}
				}


				$tpl->assign(array('LIST'=>$row));
				//업체정보
				$data = $this->mdl_keyword->company_view($company_idx);
				$tpl->assign(array(
					'company_name'=>$data[name],
					'company_idx'=>$company_idx,
				));

			}else{
				//업체 목록
				$row = $this->mdl_keyword->company_list($page,'100');
				
				$tab_menu[0][title] = "업체선택";
				$tab_menu[0]['link'] = "submenu";
				$tab_menu[0]['icon'] = "fa fa-building-o";
				for($ii=0; $ii<count($row); $ii++) {
					
					$tab_menu[0][submenu][$ii][title] = $row[$ii][name];
					$tab_menu[0][submenu][$ii]['link'] = "javascript:load_company_code(".$row[$ii][idx].")";
				}
				
				$tpl->assign(array(
					'company_LIST'=>$row,
					'admin_MENU_tab'=>$tab_menu,
					));
			}


			$tpl->assign(array('company_idx'=>$company_idx));
			$tpl->setLayout('admin_sub');
			$tpl->define('CONTENT', $this->display->getTemplate('admin/keyword/crontab_list.htm'));

			$tpl->printAll();

        }


	  //수집한 내역 확인
	  function crontab_view($date='',$keyword='',$reg_h='',$rank=''){

		
		 $keyword = urldecode($keyword);
		$sql = "select * from keyword_run where run_date='".$date."' and keyword='".$keyword."' and run_h='".$reg_h."'  ";
		
		$data = $this->db-> sqlFetch($sql);
		
		
		
		
		if($data){
			

			echo '<div class="" id="" style="padding:5px;text-align:center;background-color:#ffcc00;position:fixed;top:0px;z-index:9999;width:100%">과거에 저장된 HTML입니다. 실제와 차이가 있을수 있습니다.</div>';
			echo '<div class="" id="" style="padding:5px;text-align:center;background-color:#ffcc66;position:fixed;top:30px;z-index:9999;width:100%;">'.$date.' / '.$reg_h.'시 : '.$rank.'위</div>';
			echo "<br>";
			echo "<br>";
			echo "<br>";
			echo $data[html_text];
			echo "<script>document.getElementById('fusion_".$rank."').style.border='3px solid red'</script>";
		}

	  }


}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */
