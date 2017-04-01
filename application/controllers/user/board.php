<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-25
* 작성자: 김종태
* 설   명: 게시판
*****************************************************************
* 
*/
class board extends CI_Controller {
	 
	 var $BOARD_TABLE = "tab_board";
	 var $COMMENT_TABLE = "tab_comment";
	 var $BOARD_TABLE_CONFIG =  "tab_board_config";
	 var $BOARD_TABLE_CATEGORY =  "tab_board_category";
	 var $BOARD_TABLE_CATEGORY2 =  "tab_board_category2";
	 var $FILES_TABLE =  "tab_files";
	 
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
	
	 function pass_input($mcode=0,$id=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB =  $this->db;

		switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
			if($mcode=="member_modify"){
				list($views_type,$views_func) = explode("_",$mcode);
				$views_data = $this->webapp->get($views_type,array('key'=>$views_func));
				$sub_title = $views_data[title];
				$tpl->assign(array(
					'sub_title'=>$sub_title,
					'str_type'=>$mcode,
				 ));
				
			}else{

			$sql = "select * from ".$this->MENU_TABLE." where num_oid = '"._OID."' and num_mcode = '".$mcode."' ";
			$data = $DB -> sqlFetch($sql);
			$tpl->assign($data);
			
			$tpl->assign(array(
				'mcode'=>$mcode,
				'id'=>$id,
			 ));
			
			}
			$tpl->setLayout($this->SUB_LAYOUT);
			$tpl->define('CONTENT', $this->display->getTemplate('pass_input.htm'));
			$tpl->printAll();
			
			 break;
			case "POST":
			
			

			switch ($_POST[str_type]) {
					case "board":
						$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."' and num_mcode = '".$_POST[mcode]."' and num_serial='".$_POST[id]."'";
						$data = $DB -> sqlFetch($sql);
						if($_POST[mode] == "del"){
							$re_url = "/user/board_delete/".$mcode."/".$id."?cate=".$_POST[cate];
						}else{
							$re_url = "/user/board_read/".$mcode."/".$id."?cate=".$_POST[cate];
						}
					break;
					case "member_modify":
						$sql = "select str_passwd as str_pass from ".$this->MEMBER_TABLE." where num_oid = '"._OID."' and str_id = '".$_SESSION[USERID]."'";
						$data = $DB -> sqlFetch($sql);
						$re_url = "/user_page/member_modify/";
					
					break;
					
				} 
			
			if($data[str_pass] && $_POST[password]){
				if($data[str_pass] == md5($_POST[password]) || $_SESSION[ADMIN]){
					$this->session->set_flashdata('tmp_pass', TRUE);
					$this->webapp->redirect($re_url);
				}else{
				
					$this->webapp->moveBack("비밀번호가 일치하지 않습니다.");
				}
			}else{
				$this->webapp->moveBack("비밀번호는 공백일수 없습니다.");
			}


			 break;
			} 


	 }


	  function boardConf($mcode){
		$this->display->assign(array('mcode'=>$mcode));

		$sql = "select * from ".$this->BOARD_TABLE_CONFIG." where num_oid = '"._OID."' and num_mcode = '".$mcode."' ";
		if($_conf = $this->db->sqlFetch($sql)){
			$this->display->assign($_conf);
			return $_conf;
		}else{
			$this->webapp->moveBack('게시판이 존재하지 않습니다.');
			exit;
			return false;
		}

		
	  }

	
	  //게시판 리스트
	   function list_view($mcode=0,$page=1){
			
			$WA = $this->webapp;
			$tpl = $this->display;
			
			$_conf = $this->boardConf($mcode);

			$this->PERM->apply('menu',$mcode,'l');
			
			if($this->PERM->check('menu',$mcode,'w') || $_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('write_pre'=>TRUE));
			}

			if($_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('admin_pre'=>TRUE));
			}

			$tpl->assign(array('mcode'=>$mcode));
			
			
			if(!$_conf[str_skin])  $_conf[str_skin] = "board";
			
			if(!$_conf[num_listnum]) $listnum = 10; else $listnum = $_conf[num_listnum];

			if($_conf[str_skin] == "gallery"){
				$listnum = $listnum -2;
			}
			if(!$page)$page = 1;

			$sql = "select * from ".$this->BOARD_TABLE_CATEGORY." where num_oid = '"._OID."' and num_mcode = '".$mcode."' ";
			$cate_row = $this->db-> sqlFetchAll($sql);
			$tpl->assign(array('cate_LIST'=>$cate_row));

			$sql = "select * from ".$this->BOARD_TABLE_CATEGORY2." where num_oid = '"._OID."' and num_mcode = '".$mcode."' ";
			$cate_row2 = $this->db-> sqlFetchAll($sql);
			$tpl->assign(array('cate_LIST2'=>$cate_row2));

			//리스트 로드
			$row= $this->webapp->listPageingRow($this->BOARD_TABLE," num_oid = '"._OID."' and  num_mcode = '".$mcode."'  ", " order by num_serial desc ",$page,$listnum,$_GET['search_key'],$_GET[search_value]);
			
			for($ii=0; $ii<count($row); $ii++) {
				$row[$ii]['is_recent'] = date('U') - strtotime($row[$ii]['dt_date']) < 241920;

				$sql = "select * from tab_files where num_oid = '"._OID."' and str_sect = 'board/".$mcode."/".$row[$ii][num_serial]."' and str_type = 'image' order by num_serial asc limit 1  ";
				$img_info = $this->db -> sqlFetch($sql);
				$row[$ii]['thumb_url'] = $img_info[str_thum];

				$row[$ii]['str_text'] = $this->webapp->text_cut($row[$ii]['str_text'], 300);
			}
			
			$tpl->assign(array('TAB_BOARD'=>$row));

			
			//공지리스트 로드
			$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."' and  num_mcode = '".$mcode."'   and num_notice = 1  and  num_input_pass = 0";
			$gongji_row = $this->db-> sqlFetchAll($sql);
			$tpl->assign(array(
				'gong_LIST'=>$gongji_row,
				'LIST'=>$row,
			 ));
			
			
			//메뉴데이터
			$menu_data = $this->menu->load_menu_data($mcode);
			
			if(THEME=="mobile"){
				$tpl->setLayout('@sub');
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/mobile_board/list_'.$_conf[str_skin].'.htm'));
			}else{
				$tpl->setLayout($menu_data[str_layout]);
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/board/list_'.$_conf[str_skin].'.htm'));
			}
			
			$tpl->printAll();

        }
	 

	   //게시판 읽기
	   function read_view($mcode=0,$id=0){
			
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;

			//메뉴데이터
			$menu_data = $this->menu->load_menu_data($mcode);
			
			$this->PERM->apply('menu',$mcode,'r');
			
			if($this->PERM->check('menu',$mcode,'c') || $_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('comment_pre'=>TRUE));
			}

			if($this->PERM->check('menu',$mcode,'p') || $_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('reply_pre'=>TRUE));
			}

			if($this->PERM->check('menu',$mcode,'w') || $_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('wirte_pre'=>TRUE));
			}

			$_conf = $this->boardConf($mcode);
			if(!$_conf[str_skin])  $_conf[str_skin] = "board";
		
		
			
			

			$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."'  and num_mcode = '".$mcode."' and num_serial = '".$id."' ";
			$data = $DB -> sqlFetch($sql);

			
			if($data){
				
				$data[str_text] = str_replace('"/images/notice/','"/data/notice/',$data[str_text] );
				

				if($_SESSION['USERID'] != $data['str_user']) {
					if($data['num_input_pass']) {
						if(! $_SESSION[ADMIN]){
							if(!$this->session->flashdata('tmp_pass')) {
								$this->webapp->redirect("/user_page/pass_input/".$mcode."/".$id._QS);
							}
						}
					}
				}
				
				$sql = "update ".$this->BOARD_TABLE." set num_hit=num_hit+1 where num_oid = '"._OID."'  and num_mcode = '".$mcode."' and num_serial = '".$id."' ";
				$DB ->query($sql);

				$tpl->assign($data);


			}else{
				$this->webapp->moveBack('게시물이 존재하지 않습니다.');
			}

			$file_sect = "board/".$mcode;
			if($id) {
				$file_sect = "board/".$mcode."/".$id;
				$file_sect2 = "board_reply/".$mcode."/".$id;
			}
			
			$tpl->assign(array('id'=>$id,'mcode'=>$mcode,'sect'=>$file_sect,'sect2'=>$file_sect2));
						
			if(THEME=="mobile"){
				$tpl->setLayout('@sub');
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/mobile_board/read.htm'));
			}else{
				$tpl->setLayout($menu_data[str_layout]);
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/board/read.htm'));
			}
			$tpl->printAll();

        }

	   function delete_run($mcode=0,$id=0){
		$WA = $this->webapp;
		$tpl = $this->display;
		$DB = $this->db;

			$this->PERM->apply('menu',$mcode,'w');
			
			if($_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('admin_pre'=>TRUE));
			}

			$_conf = $this->boardConf($mcode);
			if(!$_conf[str_skin])  $_conf[str_skin] = "boardType1";
			
			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":

			$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."'  and num_mcode = '".$mcode."' and num_serial = '".$id."' ";
			$data = $DB -> sqlFetch($sql);
			if($data){
				
				if($_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				}else{
					if($_SESSION[USERID] && ($data[str_user] ==$_SESSION[USERID] )){
											
					}else{
						$this->webapp->moveBack('본인이 작성한 글이 아닙니다.');
					}
				}

				$tpl->assign($data);
				

				//메뉴데이터
				$menu_data = $this->menu->load_menu_data($mcode);
				$tpl->setLayout('none');
				$tpl->define('CONTENT', $this->display->getTemplate('common/pass_input.htm'));
				
				$tpl->printAll();

			}else{
				$this->webapp->moveBack('게시물이 존재하지 않습니다.');
			}

			
			 break;
			case "POST":

			$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."' and num_mcode = '".$_POST[mcode]."' and num_serial='".$id."'";
			$data = $DB -> sqlFetch($sql);
			
			
				if( ($data[str_pass] == md5($_POST[password])) || $data[str_user] ==$_SESSION[USERID] || $_SESSION[ADMIN]){
					
					
				$sect = 'board/'.$mcode."/".$id;
				$sect2 = 'board_reply/'.$mcode."/".$id;

				$DB->deleteQuery($this->BOARD_TABLE,"  num_oid = '"._OID."' and num_mcode = '".$_POST[mcode]."' and num_serial='".$id."' ");
				$DB->deleteQuery($this->COMMENT_TABLE," num_oid = "._OID." and num_code like '".$sect."%'");
				
				$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$sect."'";
				$delfile = $DB -> sqlFetchAll($sql);
				for($ii=0; $ii<count($delfile); $ii++) {
						@unlink(_DOC_ROOT."/".$delfile[$ii][str_refile]);
						if($type=="image") @unlink(_DOC_ROOT."/".$delfile[$ii][str_thum]);
						$sql = "delete from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$delfile[$ii][str_sect]."' and str_refile='".$delfile[$ii][str_refile]."'  ";
						$DB->query($sql);
				}

				$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$sect2."'";
				$delfile = $DB -> sqlFetchAll($sql);
				for($ii=0; $ii<count($delfile); $ii++) {
						@unlink(_DOC_ROOT."/".$delfile[$ii][str_refile]);
						if($type=="image") @unlink(_DOC_ROOT."/".$delfile[$ii][str_thum]);
						$sql = "delete from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$delfile[$ii][str_sect]."' and str_refile='".$delfile[$ii][str_refile]."'  ";
						$DB->query($sql);
				}
				
			
				echo "OK";
				exit;

				}else{
				
					echo "NO";
					exit;
				}
			
			 break;
			}
						
	  }

	  //게시판 글쓰기/글 수정
	   function write($mcode=0,$id=0,$meodth=""){
		
			$WA = $this->webapp;
			$tpl = $this->display;
			$DB = $this->db;
			
			$this->PERM->apply('menu',$mcode,'w');

			$_conf = $this->boardConf($mcode);
			if(!$_conf[str_skin])  $_conf[str_skin] = "board";
			
			if($_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a')){
				$tpl->assign(array('admin_pre'=>TRUE));
			}

			switch ($_SERVER[REQUEST_METHOD]) {
			case "GET":
			
		
			$tpl->assign($_conf);
			
			if($_conf[str_category_use]){
				$sql = "select num_serial, str_category from ".$this->BOARD_TABLE_CATEGORY." where num_oid = '"._OID."' and num_mcode = '$mcode' ";
				$row = $DB -> sqlFetchAll($sql);
				$tpl->assign(array('cate_LIST'=>$row));
			}

			if($_conf[str_category_use2]){
				$sql = "select num_serial, str_category from ".$this->BOARD_TABLE_CATEGORY2." where num_oid = '"._OID."' and num_mcode = '$mcode' ";
				$row = $DB -> sqlFetchAll($sql);
				$tpl->assign(array('cate_LIST2'=>$row));
			}
			
				
			if($id) {
				$file_sect = "board/".$mcode."/".$id;
			}else{
				$file_sect = "supernow17tmpboardfilesurl/tmp/".$mcode;
			}


			if($id){
				$sql = "select * from ".$this->BOARD_TABLE." where num_oid = '"._OID."'  and num_mcode = '".$mcode."' and num_serial = '".$id."' ";
				$data = $DB -> sqlFetch($sql);
			
				$data[board_title] = $data[str_title];
				$data[dt_date] = $data[dt_date];

				if(($data[str_user] != $_SESSION[USERID]) || !$_SESSION[USERID]){

					if(!$_SESSION[ADMIN]){
						$this->webapp->moveBack('글을 수정할 권한이 없습니다.');
						exit;
					}
				}
				
				if($meodth == "reply"){
					if(($_SESSION[ADMIN] || $this->PERM->check('menu',$mcode,'a') || $this->PERM->check('menu',$mcode,'p')) && $_SESSION[USERID]){
						$file_sect = "reply_supernow17tmpboardfilesurl/tmp/".$mcode;
						$tpl->assign(array('meodth'=>$meodth));	
					}else{
						$this->webapp->moveBack('답변을 작성할 권한이 불충분 합니다.');						
					}
					
				}

			}else{
				
				$data['dt_date'] = date("Y-m-d");
				$data['num_hit'] = 0;
			}
			
		
			
			$tpl->assign(array('id'=>$id,'mcode'=>$mcode,'file_sect'=>$file_sect));
						
			//메뉴데이터
			$menu_data = $this->menu->load_menu_data($mcode);

			if(THEME=="mobile"){
				$tpl->setLayout('@sub');
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/mobile_board/write.htm'));
			}else{
				$tpl->setLayout($menu_data[str_layout]);
				$tpl->define('CONTENT', $this->display->getTemplate('board/skin/board/write.htm'));
			}
			
			$tpl->assign($data);
			$tpl->printAll();
			
			 break;
			case "POST":

			
			foreach( $_POST as $val => $value ){
				if((strstr($val,"num_") || strstr($val,"str_")) && !strstr($val,"del_str") ){
					$datas[$val] = $value;
				}
			}
			
			if(strlen($datas[str_pass]) < 30) $datas[str_pass] = md5($datas[str_pass]);
			

			if(!$datas[num_input_pass]) $datas[num_input_pass] = "0";
			
			
				$this->load->model('curl');

				$s = preg_match_all("/<img\s+.*?src=[\"\']([^\"\']+)[\"\'\s][^>]*>/is", $_POST[content], $m); 
				$tmp_img_list = $m[1];


				for($ii=0; $ii<count($tmp_img_list); $ii++) {
					if(!strstr($tmp_img_list[$ii],$_SERVER[HTTP_HOST]) && strstr($tmp_img_list[$ii],'http://')){
						$tmp_img = $this->curl->curl_url($tmp_img_list[$ii]);

						
						$filename = "bbs_".md5(array_pop(explode("/",$tmp_img_list[$ii]))).".gif";
							
						$s = @fopen(_DOC_ROOT.'/data/files/'.$filename, "w");
						@fwrite($s, $tmp_img);
						@fclose($s);

						$_POST[content] = str_replace($tmp_img_list[$ii],'/data/files/'.$filename,$_POST[content]);
						
					}
				}
				

			

			if($_POST[id]){
				
				$sect = "board/".$mcode."/".$_POST[id];
				
				if($_POST[meodth] == "reply"){
					$file_sect = "reply_supernow17tmpboardfilesurl/tmp/".$mcode;
					$sect = "board_reply/".$mcode."/".$_POST[id];
					$_POST[str_re_text] = str_replace($file_sect,$sect,$_POST[str_re_text]);
				}

				

				

				
				if($_POST[content]) $datas[str_text] = addslashes($_POST[content]);
				if($_POST[str_re_text]) $datas[str_re_text] = addslashes($_POST[str_re_text]);
				if($_POST[str_title]) $datas[str_title] = addslashes($_POST[str_title]);
				

				$datas[str_user] = $_SESSION[USERID];
				$datas[str_ip] = getenv('REMOTE_ADDR');
		          
			     if($_POST['num_hit'])  $datas[num_hit];
				
				if($_POST[dt_date]) $datas[dt_date] = $_POST[dt_date];
				
				
				$this->load->helper('file');
				$config['upload_path'] = '/data/files/'.$mcode."/";
				
				//만들자
				if(!is_dir(_DOC_ROOT.$config['upload_path'])){
					
					exec('mkdir '._DOC_ROOT.$config['upload_path'].'');
					exec('chmod 777 '._DOC_ROOT.$config['upload_path'].'');
				}
				$config['upload_path'] = '/data/files/'.$mcode."/".$_POST[id]."/";
				if(!is_dir(_DOC_ROOT.$config['upload_path'])){
					
					exec('mkdir '._DOC_ROOT.$config['upload_path'].'');
					exec('chmod 777 '._DOC_ROOT.$config['upload_path'].'');
				}


				$config['upload_path'] = ".".$config['upload_path'];

			
				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size'] = '9056';
				$config['encrypt_name'] = "abscdkeww";
				
				$this->load->library('upload', $config);

				
				//파일1
				$file_fname= "str_file_url1";
				$file_del_fname= "del_".$file_fname;
				if($_POST[$file_del_fname]){
					$datas[$file_fname] = '';
				}else{
					if ($this->upload->do_upload($file_fname)){
						$fdata = $this->upload->data();
						rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
						$datas[$file_fname] = '/data/files/'.$mcode."/".$_POST[id]."/".$file_fname.".jpg";
					}
				}

				//파일2
				$file_fname= "str_file_url2";
				$file_del_fname= "del_".$file_fname;
				if($_POST[$file_del_fname]){
					$datas[$file_fname] = '';
				}else{
					if ($this->upload->do_upload($file_fname)){
						$fdata = $this->upload->data();
						rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
						$datas[$file_fname] = '/data/files/'.$mcode."/".$_POST[id]."/".$file_fname.".jpg";
					}
				}


				$DB->updateQuery($this->BOARD_TABLE,$datas," num_oid = '"._OID."'  and num_serial = '".$_POST[id]."' ");
				
				




				$this->webapp->daumEditUpload($_POST[attach_img_file],"image",$sect);
				$this->webapp->daumEditUpload($_POST[attach_file],"file",$sect);

				$this->webapp->cache_del();
				if($_POST[meodth] == "reply"){
					$this->webapp->redirect('/user/board/read_view/'.$mcode."/".$_POST[id]._QS ," 답변이 완료되었습니다.");
				}else{
					$this->webapp->redirect('/user/board/read_view/'.$mcode."/".$_POST[id]._QS ," 수정되었습니다.");
				}
			
			}else{
				
				$datas[num_oid] = _OID;
				$datas[num_mcode] = $mcode;
				$datas[num_serial] = $WA->maxValue($this->BOARD_TABLE,"num_serial"," num_oid = '"._OID."' and num_mcode = '".$mcode."' ");
				$datas[num_group] = $WA->maxValue($this->BOARD_TABLE,"num_group"," num_oid = '"._OID."' and num_mcode = '".$mcode."' ");
				
				if(!$_POST['content']) {
					$this->webapp->moveBack('본문을 입력해주십시오.');
				}
				
				$file_sect = "supernow17tmpboardfilesurl/tmp/".$mcode;
				$sect = "board/".$mcode."/".$datas[num_serial];
				$_POST[content] = str_replace($file_sect,$sect,$_POST[content]);
				

				$datas[str_text] =  addslashes($_POST[content]);
				$datas[str_user] = $_SESSION[USERID];
				$datas[str_ip] = getenv('REMOTE_ADDR');
		            $datas[num_hit] = $_POST['num_hit'] ? $_POST['num_hit'] : 0;

				if($_POST[dt_date]){
					$datas[dt_date] = $_POST[dt_date];
				}else{
					$datas[dt_date] = date("Y-m-d H:i:s");
				}
				
				
				$this->load->helper('file');
				$config['upload_path'] = '/data/files/'.$mcode."/";
				
				//만들자

				if(!is_dir(_DOC_ROOT.$config['upload_path'])){
					
					exec('mkdir '._DOC_ROOT.$config['upload_path'].'');
					exec('chmod 777 '._DOC_ROOT.$config['upload_path'].'');
				}
				$config['upload_path'] = '/data/files/'.$mcode."/".$datas[num_serial]."/";
				if(!is_dir(_DOC_ROOT.$config['upload_path'])){
					
					exec('mkdir '._DOC_ROOT.$config['upload_path'].'');
					exec('chmod 777 '._DOC_ROOT.$config['upload_path'].'');

					
				}
				$config['upload_path'] = ".".$config['upload_path'];

				$config['allowed_types'] = 'gif|jpg|png|jpeg';
				$config['max_size'] = '4056';
				$config['encrypt_name'] = "abscdkeww";
				
				$this->load->library('upload', $config);

				

				
				//파일1
				$file_fname= "str_file_url1";
				$file_del_fname= "del_".$file_fname;
				if($_POST[$file_del_fname]){
					$datas[$file_fname] = '';
				}else{
					if ($this->upload->do_upload($file_fname)){
						$fdata = $this->upload->data();
						rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
						$datas[$file_fname] = '/data/files/'.$mcode."/".$datas[num_serial]."/".$file_fname.".jpg";
					}
				}

				//파일2
				$file_fname= "str_file_url2";
				$file_del_fname= "del_".$file_fname;
				if($_POST[$file_del_fname]){
					$datas[$file_fname] = '';
				}else{
					if ($this->upload->do_upload($file_fname)){
						$fdata = $this->upload->data();
						rename($fdata[full_path],$config['upload_path'].$file_fname.".jpg");
						$datas[$file_fname] = '/data/files/'.$mcode."/".$datas[num_serial]."/".$file_fname.".jpg";
					}
				}

				$DB->insertQuery($this->BOARD_TABLE,$datas);
				
				$this->webapp->daumEditUpload($_POST[attach_img_file],"image",$sect);
				$this->webapp->daumEditUpload($_POST[attach_file],"file",$sect);
				
				$this->webapp->cache_del();
				$this->webapp->redirect('/user/board/list_view/'.$mcode._QS , "작성되었습니다.");

			}
			

			 break;
			}

			

        }

}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */