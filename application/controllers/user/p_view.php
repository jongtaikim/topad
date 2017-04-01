<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-12-04
* 작성자: 김종태
* 설   명: 하스피 상품보기
*****************************************************************
* 
*/
class p_view extends CI_Controller {
	
	

	 var $SUB_LAYOUT =  "@sub";
	 var $site_menu="";
	 var $PERM="";

	 function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');
		$_SESSION = $this->session->all_userdata();
		$tpl = $this->display;
	
		//메뉴 불러오기
		$this->load->model('user/menu');
		$this->site_menu = $this->menu->load_tree();
		
		$tpl->assign(array('site_menu_list'=>$this->site_menu));
		
		$this->load->library('permission');
		$this->PERM =$this->permission;

		if($_GET[cate]) {
			$_cate = $_GET[cate];
			while(strlen($_cate = substr($_cate,0,-2)) > 1) {
				$_location[] = $this->db->sqlFetchOne("SELECT str_title FROM ".tab_menu." WHERE num_oid="._OID." AND num_cate=$_cate");
			}
			$_location[] = '';
			$menu_location = implode(' > ',array_reverse($_location));
			$tpl->assign(array(
				'menu_location' => $menu_location,
			));
		$menu_title = $this->db->sqlFetchOne("SELECT str_title FROM ".tab_menu." WHERE num_oid="._OID." AND num_cate='".$_GET[cate]."'");
		$tpl->assign(array('menu_title'=>$menu_title));
		}


	}
	



	   function lists($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '6' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }


	   function lists2($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '7' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }

	    function lists3($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);

			 if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
				$contents = read_file('data/docs/'.$mcode.".html");
			 }else{
				$contents ="";
			 }

			$tpl->assign(array(
				'contents'=>$contents,
				'mcode'=>$mcode,
				'cate'=>$_GET[cate],
			 ));
		
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '12' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }

	   function lists4($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);

			 if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
				$contents = read_file('data/docs/'.$mcode.".html");
			 }else{
				$contents ="";
			 }

			$tpl->assign(array(
				'contents'=>$contents,
				'mcode'=>$mcode,
				'cate'=>$_GET[cate],
			 ));
		
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '9' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }
	

	function lists5($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);

			 if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
				$contents = read_file('data/docs/'.$mcode.".html");
			 }else{
				$contents ="";
			 }

			$tpl->assign(array(
				'contents'=>$contents,
				'mcode'=>$mcode,
				'cate'=>$_GET[cate],
			 ));
		
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '10' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }
	
	   function lists6($mcode='',$page=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;
		
		//메뉴데이터
		if($mcode){
			$data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
		
			$data[mcode] = $mcode;
			$tpl->assign($data);

			 if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
				$contents = read_file('data/docs/'.$mcode.".html");
			 }else{
				$contents ="";
			 }

			$tpl->assign(array(
				'contents'=>$contents,
				'mcode'=>$mcode,
				'cate'=>$_GET[cate],
			 ));
		
		}

		$listnum = 16;
		$row= $this->webapp->listPageingRow("hospi_product"," product_type = '11' ", " order by code asc ",$page,$listnum);
		for($ii=0; $ii<count($row); $ii++) {
			$row[$ii][img] = str_replace("/data/product_img","/data/docs/product_img",$row[$ii][img]);
			
		}
		
		$tpl->assign(array('LIST'=>$row));
		
		

		$tpl->setLayout('@sub');
		$tpl->define('CONTENT', $this->display->getTemplate('user_page/p_view.htm'));
		$tpl->printAll();

	  }

}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */