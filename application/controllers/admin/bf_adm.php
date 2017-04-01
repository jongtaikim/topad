<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-10-01
* 작성자: 김종태
* 설   명: 전후사진 관리
*****************************************************************
* 
*/
class bf_adm extends CI_Controller {
	  
	
	 var $main_layout =  "admin_main";
	 var $sub_layout =  "admin_sub";


	function __construct()
	{
		parent::__construct();
		$_SESSION = $this->session->all_userdata();

		$this->load->database();

		//모델
		$this->load->model('admin/admin_init');
		$this->load->model('admin/menu');

		$this->load->model('user/mdl_bf');
		//성형카테고리 // config.php
		$this->mdl_bf->category($_GET);

	}
	
	/**
	 * 전후 사진 관리
	 *
	 * @return	object	$aaa
	 * @return	void
	 */	
	 function list_view($page=1){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;

		switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			$_GET['is_admin']='Y';

			$row = $this->mdl_bf->load_list($page,$_GET,15);
			$tpl->assign(array('LIST'=>$row));
			

			$tpl->setLayout($this->sub_layout);
			$tpl->define('CONTENT', $this->display->getTemplate('admin/bf/list.htm'));
			
			$tpl->printAll();

			break;
			case "POST":
			break;
			} 
	 }

	 /**
	  *  전후 사진 관리
	  *
	  * @param	string	$aaa	주석
	  * @return	object	$aaa
	  * @return	void
	  */	
	 function item_add($idx=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":

		if($idx){
			$data=$this->mdl_bf->load_view($idx);
			if($data){
				$tpl->assign($data);
			}
		}
		$tpl->setLayout('none');
		$tpl->define('CONTENT', $this->display->getTemplate('admin/bf/add.htm'));
		
		$tpl->printAll();
		
		break;
		case "POST":

			
			if($_POST[idx]){
				$datas['subject'] = $_POST[db_subject];
				$datas['delete'] = $_POST['db_delete'];
				$datas['db_how'] = $_POST['db_how'];
				$datas['db_pos'] = $_POST['db_pos'];
				$re_idx = $this->mdl_bf->bf_modify($_POST[idx],$datas);
			}else{
				$datas['subject'] = $_POST[db_subject];
				$datas['delete'] = $_POST['db_delete'];
				$datas['db_how'] = $_POST['db_how'];
				$datas['db_pos'] = $_POST['db_pos'];
				$re_idx = $this->mdl_bf->bf_add($datas);
				
			}

		
			$this->load->helper('file');
			$config['upload_path'] = './data/before_after/'.$re_idx."/";

			//만들자
			if(!is_dir($config['upload_path'])){
				mkdir($config['upload_path'],'0777');
				exec('chmod 777 '.$config['upload_path']);
			}
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '4056';
			$config['encrypt_name'] = "abscdkeww";
			
			$this->load->library('upload', $config);
			
			//전면 전
			$file_fname= "front_before";
			$file_del_fname= "del_".$file_fname;
			if($_POST[$file_del_fname]){
				$datas[$file_fname] = '';
			}else{
				if ($this->upload->do_upload($file_fname)){
					$fdata = $this->upload->data();
					rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
					$datas[$file_fname] = str_replace(".JPG",".jpg",$fdata[orig_name]);
				}
			}

			//전면 후
			$file_fname= "front_after";
			$file_del_fname= "del_".$file_fname;
			if($_POST[$file_del_fname]){
				$datas[$file_fname] = '';
			}else{
				if ($this->upload->do_upload($file_fname)){
					$fdata = $this->upload->data();
					rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
					$datas[$file_fname] = str_replace(".JPG",".jpg",$fdata[orig_name]);
					
				}
			}
			
			//측면 전
			$file_fname= "side_before";
			$file_del_fname= "del_".$file_fname;
			if($_POST[$file_del_fname]){
				$datas[$file_fname] = '';
			}else{
				if ($this->upload->do_upload($file_fname)){
					$fdata = $this->upload->data();
					rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
					$datas[$file_fname] = str_replace(".JPG",".jpg",$fdata[orig_name]);
				}
			}


			//측면 후
			$file_fname= "side_after";
			$file_del_fname= "del_".$file_fname;
			if($_POST[$file_del_fname]){
				$datas[$file_fname] = '';
			}else{
				if ($this->upload->do_upload($file_fname)){
					
					$fdata = $this->upload->data();
					rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
					$datas[$file_fname] = str_replace(".JPG",".jpg",$fdata[orig_name]);
				}
			}
			
			//적용된 파일 DB적용
			$re_idx = $this->mdl_bf->bf_modify($re_idx,$datas);


		break;
		} 
		
	  
	  }

	  /**
	  *  전후 사진 삭제
	  *
	  * @return	void
	  */	
	 function item_del($idx=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;
		if($idx){
			$data=$this->mdl_bf->bf_del($idx);
		}
	  
	  }


	

}//

/* End of file .menu_adm.php */
/* Location: ./application/controllers/menu_adm.php */
