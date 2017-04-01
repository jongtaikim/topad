<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 
* 작성자: 김종태
* 설   명: 
*****************************************************************
* 
*/
class Plug_daum_editor_upload extends CI_Controller {
		
		var $FILES_TABLE = "tab_files";

	  	function __construct()
		{
			parent::__construct();
			$this->load->database();
			
		}


	  public function index(){
		show_404('page', "잘못된 경로");
        }

	  function imageUpload(){
		
	
		$this->load->library('upload', $config);

		$tpl = $this->display;
		$tpl->setLayout('none');
		$tpl->define('CONTENT',$this->display->getTemplate("util/openeditor/pages/trex/image_ver1.html"));
		$tpl->assign(array(
			'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/util/openeditor/pages/trex/",
			
		));
		
		
		$tpl->printAll();

	  }

	    function fileUpload(){
		
	
		$this->load->library('upload', $config);
		
		$tpl = $this->display;
		$tpl->setLayout('none');
		$tpl->define('CONTENT',$this->display->getTemplate("util/openeditor/pages/trex/file_ver1.html"));
		$tpl->assign(array(
			'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/util/openeditor/pages/trex/",
			
		));
		
		
		$tpl->printAll();

	  }

	  function uploadCom(){
		if($_SERVER[REQUEST_METHOD] == "POST") {

	
			
			
			$CI =& get_instance(); 
			$DB = $CI->db;
			$CI->load->helper('file');
			$CI->load->library('session');

			if($_POST[str_sect]){
				$str_sect = $_POST[str_sect];
			}else{
				$str_sect = "tmp_files";
			}

			$config['upload_path'] = './data/files/';
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size'] = '4056';
			$config['encrypt_name'] = "김종태는 천재다";
			
			$this->load->library('upload', $config);
			$DB = $this->db;
			

			for($ii=0; $ii<11; $ii++) {
				$iia = $ii+1;
				$field_name = "upfile_image".$iia;
				if($_FILES[$field_name][name]){
					$this->upload->do_upload($field_name);
					$filelist[$ii] = $this->upload->data();
					
					if($filelist[$ii][is_image]==1){
						if($filelist[$ii][image_width] && $filelist[$ii][image_height])$filelist[$ii][image_size] = $this->imgSize("900","900",$filelist[$ii][image_width],$filelist[$ii][image_height]);
						
						$filelist[$ii][image_width] = $filelist[$ii][image_size][img_w];
						$filelist[$ii][image_height] = $filelist[$ii][image_size][img_h];
					
					$filelist[$ii][image_size2] = $this->imgSize("300","300",$filelist[$ii][image_width],$filelist[$ii][image_height]);
					
					
					$this->webapp->GDImageResize(_DOC_ROOT."/data/files/".$filelist[$ii][file_name],_DOC_ROOT."/data/files/thum_".$filelist[$ii][file_name],$filelist[$ii][image_size2][img_w],$filelist[$ii][image_size2][img_h]);
					
					

					$datas[$ii][num_oid] = _OID;
					$datas[$ii][str_sect] = $str_sect;
					$datas[$ii][num_serial] = $CI->webapp->maxValue($this->FILES_TABLE,'num_serial'," num_oid = '"._OID."' and str_sect='".$str_sect."'");
					$datas[$ii][str_refile] = "/data/files/".$filelist[$ii][file_name];
					$datas[$ii][str_upfile] = $filelist[$ii][orig_name];
					$datas[$ii][str_thum] = "/data/files/thum_".$filelist[$ii][file_name];
					$datas[$ii][str_ftype] =  array_pop(explode(".",$filelist[$ii][file_ext]));
					$datas[$ii][num_down] = 0;
					$datas[$ii][num_size] = $filelist[$ii]['file_size'];
					$datas[$ii][dt_date] = mktime();
					$datas[$ii][str_mime] = strtolower($filelist[$ii]['file_type']);
					$datas[$ii][str_type] = "image";
					$datas[$ii][str_ssid] = $CI->session->userdata('session_id');
					

					$DB->insertQuery($this->FILES_TABLE,$datas[$ii]);
					$filelist[$ii][pe_url] = "/download/files/".$str_sect."/".$datas[$ii][num_serial]."/".$datas[$ii][str_upfile];

					}
				}
			}
			//$this->session->set_userdata("tmp_filelist", $filelist);

		

			$tpl = $this->display;
			$tpl->setLayout('none');
			$tpl->define('CONTENT',$this->display->getTemplate("util/openeditor/pages/trex/image_ver1.html"));
			$tpl->assign(array(
				'file_LIST'=>$filelist,
				'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/util/openeditor/pages/trex/",
				'error'=>$this->upload->display_errors(),
				
			));
			
			
			$tpl->printAll();
		
		}
	  }

	  	  function uploadComFile(){
		
		if($_SERVER[REQUEST_METHOD] == "POST") {
			
			$CI =& get_instance(); 
			$DB = $CI->db;
			$CI->load->helper('file');
			$CI->load->library('session');

			if($_POST[str_sect]){
				$str_sect = $_POST[str_sect];
			}else{
				$str_sect = "tmp_files";
			}

			$config['upload_path'] = './data/files/';
			$config['allowed_types'] = "zip|arj|rar|gz|tgz|ace|Z|exe|pdf|doc|docx|hwp|xls|xlsx|ppt|pptx|bmp|jpg|jpeg|png|gif|txt|mp3|mp4|ogg|aiff|avi|mpg|mpeg|mov|rm|swf|flv|wmv|wma|ra|html|htm|alz|dat|ios|psd|xps";
			$config['max_size'] = '20480';
			$config['encrypt_name'] = "김종태는 천재다";
			
			$this->load->library('upload', $config);
			$DB = $this->db;
			
		
			for($ii=0; $ii<11; $ii++) {
				$iia = $ii+1;
				$field_name = "upfile_name".$iia;
				
				if($_FILES[$field_name][name]){
					$this->upload->do_upload($field_name);
					$filelist[$ii] = $this->upload->data();
					
					
					$datas[$ii][num_oid] = _OID;
					$datas[$ii][str_sect] = $str_sect;
					$datas[$ii][num_serial] = $CI->webapp->maxValue($this->FILES_TABLE,'num_serial'," num_oid = '"._OID."' and str_sect='".$str_sect."'");
					$datas[$ii][str_refile] = "/data/files/".$filelist[$ii][file_name];
					$datas[$ii][str_upfile] = $filelist[$ii][orig_name];
					$datas[$ii][str_thum] = "/data/files/".$filelist[$ii][file_name];
					$datas[$ii][str_ftype] =  array_pop(explode(".",$filelist[$ii][file_ext]));
					$datas[$ii][num_down] = 0;
					$datas[$ii][num_size] = $filelist[$ii]['file_size'];
					$datas[$ii][dt_date] = mktime();
					$datas[$ii][str_mime] = strtolower($filelist[$ii]['file_type']);
					$datas[$ii][str_type] = "file";
					$datas[$ii][str_ssid] = $CI->session->userdata('session_id');
					
					$DB->insertQuery($this->FILES_TABLE,$datas[$ii]);
					$filelist[$ii][pe_url] = "/download/files/".$str_sect."/".$datas[$ii][num_serial]."/".$datas[$ii][str_upfile];
					
				}
			}

			$tpl = $this->display;
			$tpl->setLayout('none');
			$tpl->define('CONTENT',$this->display->getTemplate("util/openeditor/pages/trex/file_ver1.html"));
			$tpl->assign(array(
				'file_LIST'=>$filelist,
				'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/util/openeditor/pages/trex/",
				'error'=>$this->upload->display_errors(),
				
			));
			
			
			$tpl->printAll();
		
		}
	  }


	  function list_files(){
		$CI =& get_instance(); 
		$DB = $CI->db;
		$CI->load->helper('file');
		$CI->load->library('session');	
		$tpl = $this->display;

		if($_GET[str_sect]){
			$sql = "select * from tab_files where num_oid = '"._OID."' and str_sect = '".$_GET[str_sect]."' ";
			$row = $DB -> sqlFetchAll($sql);

			$i=0;
			$ia=0;
			for($ii=0; $ii<count($row); $ii++) {
				if($row[$ii][str_type] == 'image'){
					$imgs[$i] = $row[$ii];
					$i++;
				}else{
					$files[$ia] = $row[$ii];
					$ia++;
				}
			}
						
		}
		
	
		
		$tpl->setLayout('none');
		$tpl->define('CONTENT',$this->display->getTemplate("util/openeditor/pages/trex/file_list.html"));
		$tpl->assign(array(
			'file_LIST'=>$files,
			'img_LIST'=>$imgs,
			'baseTag'=>"http://".$_SERVER[HTTP_HOST]."/util/openeditor/pages/trex/",
			
		));
		
		
		$tpl->printAll();
		
	  }

	   function delete_file(){
		$CI =& get_instance(); 
		$DB = $CI->db;
		$CI->load->helper('file');
		$CI->load->library('session');	
		$tpl = $this->display;

		if($_GET[str_sect]){
			$a = explode(" (",$_GET[filename]);
			$filename = $a[0];
			$sql = "select * from tab_files where num_oid = '"._OID."' and str_upfile like '%".$filename."%' and str_sect = '".$_GET[str_sect]."'  ";
			$file_info = $DB -> sqlFetch($sql);
			
		
			@unlink(_DOC_ROOT."/".$file_info[str_refile]);
			if($file_info[str_type]=="image") @unlink(_DOC_ROOT."/".$file_info[str_thum]);
			
			$sql = "delete from tab_files where num_oid = '"._OID."' and str_sect = '".$_GET[str_sect]."'  and str_refile='".$file_info[str_refile]."'  ";
			
			$DB->query($sql);
		}
		
	  }

	  function imgSize($bbs_width,$bbs_height,$w,$h,$returns=''){
		
		if(!$bbs_width ) $bbs_width = 580;
		if(!$bbs_height ) $bbs_height = 580;
		
		$normal_gallery[0] = $w;
		$normal_gallery[1] = $h;

		$ratio1 = $bbs_width/$normal_gallery[0]; // 게시판 가로크기에 대한 이미지 가로 비율 계산
		$ratio2 = $bbs_height/$normal_gallery[1]; // 게시판 세로크기에 대한 이미지 세로 비율 계산

		if($ratio1 >= 1 && $ratio2 >= 1 )
		{
			$img_w = $normal_gallery[0]; // 지정된 크기보다 작을경우 원래 싸이즈데로 출력
			$img_h = $normal_gallery[1];
		}
		elseif($ratio1 > $ratio2)
		{
			$img_w = $normal_gallery[0]*$ratio2; // 포스터의 가로와 세로에 동일한 비율 적용
			$img_h = $normal_gallery[1]*$ratio2; // 높이 넓이 비율 적용
		}
		elseif($ratio1 <= $ratio2)
		{
			$img_w = $normal_gallery[0]*$ratio1; // 포스터의 가로와 세로에 동일한 비율 적용
			$img_h = $normal_gallery[1]*$ratio1; // 높이 넓이 비율 적용
		}
		else
		{
			$img_w = $normal_gallery[0]; // 지정된 크기보다 작을경우 원래 싸이즈데로 출력
			$img_h = $normal_gallery[1];
		}

		$arr[img_w] = $img_w;
		$arr[img_h] = $img_h;
		
		if(!$returns){
		return $arr;
		}elseif($returns == 1){
			return $img_w;
		}else if($returns == 2){
			return $img_h;
		}
	}

}

/* End of file plug_daum_editor_upload.php */
/* Location: ./application/controllers/plug_daum_editor_upload.php */
?>