<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-17
* 작성자: 김종태
* 설   명: 사이트 메뉴구조별 컨텐츠
*****************************************************************
* 
*/
class menu_adm extends CI_Controller {
	  
	
	 var $MENU_TABLE = "tab_menu";
	 var $RIGHT_TABLE = "tab_menu_right";
	 var $GROUP_TABLE = "tab_group";
	 var $BOARD_TABLE = "tab_board";
	 var $BOARD_TABLE_CONFIG =  "tab_board_config";
	 var $BOARD_TABLE_CATE =  "tab_board_category";
	 var $BOARD_TABLE_CATE2 =  "tab_board_category2";
	 var $COMMENT_TABLE =  "tab_comment";
	 var $URL_LINK_TABLE =  "tab_content_url";
	 var $FILES_TABLE =  "tab_files";
	 var $MEMBER_TABLE =  "tab_member";
	 var $GROUP_MEMBER_TABLE = "tab_group_member";

	
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
		$this->load->model('curl');

	}
	

	
	/**
     *  메뉴관리 트리
     *
     * @return	object	$mode	트리프레임	
     * @return	void
     */
	function menu($mode="frame"){
		$tpl = $this->display;
		$DB = $this->db;
		

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		
		if($mode =="frame" ){
			$row = $this->menu->load_tree();
		}
		if($mode =="board" ){
			$row = $this->menu->load_board_tree();
		}

		$tpl->assign(array(
			'LIST'=>$row,
			'menusas'=>$menusas,
			'submenu_list'=>$submenu_list,
		 ));
				
		$tpl->setLayout($this->sub_layout);
		$tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_".$mode.".htm"));
		
		$tpl->printAll();

		 break;
		case "POST":
		
		
		 break;
		} 
	}
	
	
	


	function menu_add($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		$this->load->helper('cookie');
		
		$VAR_MENUTYPE = $this->config->item('menu_type');
		$member_type = $this->config->item('member_type');

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		if(!$mcode) $parent = 1; else $parent = $mcode;
		

		 if ($mcode) {
			if(strlen($parent) == 1) {
			    // 추가메뉴 처리
			    $cnt = $DB->sqlFetchOne("SELECT COUNT(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$parent."__'");
			    if ($cnt > 100) {
				  WebApp::alert("11개이상 생성할 수 없습니다.");
				   echo "<script type='text/javascript'>parent.layerPopupClose()</script>";
			    }
			} else {
			    $cnt = $DB->sqlFetchOne("SELECT COUNT(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$parent."__'");
			    if ($cnt > 100) {
				  WebApp::alert('15개 이상 생성할 수 없습니다.');
				 echo "<script type='text/javascript'>parent.layerPopupClose()</script>";
			    }
			}
		  } else {
			
			$cnt = $DB->sqlFetchOne("SELECT COUNT(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND LENGTH(num_mcode)=2");
				if ($cnt > 100) {
					WebApp::alert("11개이상 생성할 수 없습니다.");
					 echo "<script type='text/javascript'>parent.layerPopupClose()</script>";
				}
		  }
		
		if($mcode){
		$data = $DB->sqlFetch("select * from ".$this->MENU_TABLE." where num_oid='"._OID."' and num_mcode=$mcode");
		$cate = $data[num_cate];
		}

		$_cate = $cate;
		while(strlen($_cate = substr($_cate,0,-2)) > 1) {
			$_location[] = $DB->sqlFetchOne("SELECT str_title FROM ".$this->MENU_TABLE." WHERE NUM_OID="._OID." AND NUM_cate=".$_cate."");
		}
		$_location[] = '메인';
		$menu_location = implode(' > ',array_reverse($_location));

		
		if(get_cookie('MENU_SELECT')) {
			$_SESSION['MENU_SELECT2'] = get_cookie('MENU_SELECT');
		}
		
		$tpl->assign('menu_location',$menu_location);
		$tpl->assign('MENU_TYPE',$VAR_MENUTYPE);
		$tpl->assign('member_type',$member_type);
		$tpl->assign($data);


		$tpl->setLayout('none');
		$tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_add.htm"));
		
		$tpl->printAll();
		
		 break;
		case "POST":
		
		$_OID = _OID;
		$parent = $mcode;
		$menu_name = $_POST['str_title'];
		$new_step = $this->newStep($parent);
		$mcode = $this->newChild($parent);
		$cate = $this->newChild2($_POST[num_cate]);
		$str_type = $_POST['str_type'];
		$str_layout = "@sub";
		$_mcode = $mcode-1;
		
		$listnum = 15;
		

		if(!$_POST[str_skin]) $str_skin = "board"; else $str_skin = $_POST[str_skin];

		$sql = "
			INSERT INTO ".$this->MENU_TABLE."
				(num_oid, num_step, num_step_back, num_mcode, str_title, str_type, num_view, num_enable_remove,str_layout,num_cate)
			VALUES
				("._OID.",$new_step, $new_step,$mcode,'$menu_name','$str_type', 1, 1,'$str_layout',$cate)
		";
		$DB->query($sql);
		

		$menu_data = $this->webapp->get($str_type); //application/controllers/__get 설정있음
		
			switch ($str_type) {
			case "board":
			$datas[num_oid] = _OID;
			$datas[num_mcode] = $mcode;
			$datas[str_title] = $menu_name;
			$datas[str_skin] = $str_skin;
			$datas[num_listnum] = $listnum;
			$datas[num_navnum] = 10;
			
			$datas[num_titlelen] = 200;
			$datas[chr_listtype] = 'B';
			$datas[chr_comment] = 'Y';
			$datas[chr_recent] = 'N';
			$datas[chr_upload] = 'Y';
			
			$DB->insertQuery($this->BOARD_TABLE_CONFIG,$datas);
							
			 break;
			case "link":
			
			$datas[num_oid] = _OID;
			$datas[num_mcode] = $mcode;
			
			$datas[str_url] = '#';
			$datas[str_target] = '_self';
			$datas[dt_date] = mktime();
			$DB->insertQuery($this->URL_LINK_TABLE,$datas);

			 break;
			 case "ifr":
			 
			$datas[num_oid] = _OID;
			$datas[num_mcode] = $mcode;
			
			$datas[str_url] = '#';
			$datas[str_target] = '_self';
			$datas[dt_date] = mktime();
			$DB->insertQuery($this->URL_LINK_TABLE,$datas);
			
			 break;
			}
		 
		 
		 foreach($member_type as $mem_type => $_value) {
                    $sql = "INSERT INTO ".$this->RIGHT_TABLE." (num_oid,str_sect,str_code,str_group,str_right)".
                           "VALUES ("._OID.",'menu','$mcode','$mem_type','".$menu_data['default_rights']."')";
			  
                    $DB->query($sql);
                }
			
		  //추가그룹
		  if ($menu_data['default_group_rights']) {
	  
				$sql = "select * from ".$this->GROUP_TABLE." where num_oid = '"._OID."' ";
				$row = $DB -> sqlFetchAll($sql);
				for($ii=0; $ii<count($row); $ii++) {
				 $sql = "INSERT INTO ".$this->RIGHT_TABLE." (num_oid,str_sect,str_code,str_group,str_right)".
					   "VALUES ("._OID.",'menu','$mcode','".$row[$ii][str_group]."','".$menu_data['default_group_rights']."')";
				  $DB->query($sql);
				}
            }
		
		   
		 break;
		}
	}
	
	
	function menu_delete($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		if($mcode){
			$parent = substr($mcode,0,-2);
			
			$str_type = $DB->sqlFetchOne("SELECT str_type FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode = '".$mcode."' ");

			$menu_sub_count = $DB->sqlFetchOne("SELECT count(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$mcode."__' ");
			if($menu_sub_count > 0) {
				WebApp::alert('하위메뉴가 존재하여 삭제할 수 없는 메뉴입니다.');
				exit;
			}

			switch ($str_type) {
			case "board":
			
			$sect = 'board/'.$mcode."/";
			$DB->deleteQuery($this->BOARD_TABLE_CONFIG," num_oid = "._OID." and num_mcode = '".$mcode."'");
			$DB->deleteQuery($this->BOARD_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			$DB->deleteQuery($this->COMMENT_TABLE," num_oid = "._OID." and num_code like '".$sect."%'");
			
			$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect like '".$sect."%'";
			$delfile = $DB -> sqlFetchAll($sql);
			for($ii=0; $ii<count($delfile); $ii++) {
					@unlink(_DOC_ROOT."/".$delfile[$ii][str_refile]);
					if($type=="image") @unlink(_DOC_ROOT."/".$delfile[$ii][str_thum]);
					$sql = "delete from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$delfile[$ii][str_sect]."' and str_refile='".$delfile[$ii][str_refile]."'  ";
					$DB->query($sql);
			}
			
			
							
			 break;
			case "link":
			$DB->deleteQuery($this->URL_LINK_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			 break;
			 case "ifr":
			 $DB->deleteQuery($this->URL_LINK_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			 break;
			}
			
			$DB->query("DELETE FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode=$mcode");
			$DB->query("DELETE FROM ".$this->RIGHT_TABLE." WHERE num_oid="._OID." AND str_sect='menu' AND str_code='$mcode'");
			
			exec("rm -rf "._DOC_ROOT.'/tmp/*.xml'); //플래시용 xml 임시파일 삭제
			
			$json[result] = "ok";
			echo json_encode($json);
			
		}else{
			$json[result] = "error";
			$json[error_msg] = "메뉴가 존재하지 않습니다.";
			echo json_encode($json);
		}

		exit;

	}

	function menu_reset($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		if($mcode){
			$parent = substr($mcode,0,-2);
			
			$str_type = $DB->sqlFetchOne("SELECT str_type FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode = '".$mcode."' ");

			$menu_sub_count = $DB->sqlFetchOne("SELECT count(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$mcode."__' ");
			if($menu_sub_count > 0) {
				WebApp::alert('하위메뉴가 존재하여 삭제할 수 없는 메뉴입니다.');
				exit;
			}

			switch ($str_type) {
			case "board":
			
			$sect = 'board/'.$mcode."/";
			$DB->deleteQuery($this->BOARD_TABLE_CONFIG," num_oid = "._OID." and num_mcode = '".$mcode."'");
			$DB->deleteQuery($this->BOARD_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			$DB->deleteQuery($this->COMMENT_TABLE," num_oid = "._OID." and num_code like '".$sect."%'");
			
			$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect like '".$sect."%'";
			$delfile = $DB -> sqlFetchAll($sql);
			for($ii=0; $ii<count($delfile); $ii++) {
					@unlink(_DOC_ROOT."/".$delfile[$ii][str_refile]);
					if($type=="image") @unlink(_DOC_ROOT."/".$delfile[$ii][str_thum]);
					$sql = "delete from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$delfile[$ii][str_sect]."' and str_refile='".$delfile[$ii][str_refile]."'  ";
					$DB->query($sql);
			}
					
			break;
			case "link":
			$DB->deleteQuery($this->URL_LINK_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			 break;
			 case "ifr":
			 $DB->deleteQuery($this->URL_LINK_TABLE," num_oid = "._OID." and num_mcode = '".$mcode."'");
			 break;
			}
			
			$DB->query("update ".$this->MENU_TABLE." set str_type = 'menu' WHERE num_oid="._OID." AND num_mcode=$mcode");
			$DB->query("DELETE FROM ".$this->RIGHT_TABLE." WHERE num_oid="._OID." AND str_sect='menu' AND str_code='$mcode'");
			
			//exec("rm -rf "._DOC_ROOT.'/tmp/*.xml'); //플래시용 xml 임시파일 삭제
			$this->webapp->redirect("/admin/menu_adm/menu_option/".$mcode,'초기화되었습니다.');
		}else{
			WebApp::alert('메뉴코드가 없습니다.');
		}

	}


	
	function menu_option($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		$VAR_MENUTYPE = $this->config->item('menu_type');

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		
		//레이아웃 모음
		$_file = $this->display->getTemplate('layout.php');
		$layout_conf = @parse_ini_file($_file,true);
		$tpl->assign(array('layout_LIST'=>$layout_conf));
		
		

		$data = $DB->sqlFetch("select * from ".$this->MENU_TABLE." where num_oid='"._OID."' and num_mcode=$mcode");

		$module_name = $data['str_type'];
		$cate = $data[num_cate];

		$_cate = $cate;
		while(strlen($_cate = substr($_cate,0,-2)) > 1) {
			$_location[] = $DB->sqlFetchOne("SELECT str_title FROM ".$this->MENU_TABLE." WHERE NUM_OID="._OID." AND NUM_cate=".$_cate."");
		}
		$_location[] = '메인';
		$menu_location = implode(' > ',array_reverse($_location));
		
		$sub_cnt = $DB->sqlFetchOne("select count(*) from ".$this->MENU_TABLE." where num_oid="._OID." and num_cate like '".$cate."__'");
		
		$sect = $module_name."/".$mcode."/";

		switch ($module_name) {
			
			case "board":
			
			$sql = "select * from ".$this->BOARD_TABLE_CONFIG." where  num_oid="._OID."  and num_mcode = $mcode ";
			$module_data = $DB -> sqlFetch($sql);
			
			if($module_data[str_admin_id]){
				$sql = "select str_name from ".$this->MEMBER_TABLE." where num_oid = "._OID." and str_id  = '".$module_data[str_admin_id]."' ";
				$module_data[str_admin_name] = $DB -> sqlFetchOne($sql);
			}

			$skinlist = array();
			foreach (glob('application/views/board/skin/*',GLOB_ONLYDIR) as $str_skin) {
				$str_skin = array_pop(explode('/',$str_skin));
				$skininfo = parse_ini_file("application/views/board/skin/{$str_skin}/info.php");
				$skinlist[] = array(
					'str_skin' => $str_skin,
					'skinname' => $skininfo[name],
				);
			}
			
			$sql = "select sum(num_size) from tab_files where num_oid = "._OID." and str_sect = '".$sect."%' ";
			$max_disk = $DB -> sqlFetchOne($sql);
			$data[max_disk] = $this->webapp->byte_convert($max_disk);
			
			$data[menu_url] = $this->menu->type_menu_url($data[str_type],$data[num_mcode],$data[num_cate]);
			 break;
			
			case "link":
			$data[menu_url] = $this->menu->type_menu_url($data[str_type],$data[num_mcode],$data[num_cate]);
			 break;

			 case "doc":
			$data[menu_url] = $this->menu->type_menu_url($data[str_type],$data[num_mcode],$data[num_cate]);
			 break;
			 case "menu":
			$data[menu_url] = $this->menu->type_menu_url($data[str_type],$data[num_mcode],$data[num_cate]);
			 break;
			}

		

			$tpl->setLayout('none');
			$tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_option.htm"));
			$tpl->assign($data);
			$tpl->assign($module_data);
				$tpl->assign($ext_data);
				$tpl->assign(array(
					'bbs_skin' => $skinlist,
					'menu_location' => $menu_location,
					'env'           => $env,
					'mcode'         => $mcode,
					'sub_cnt'       => $sub_cnt
				  ));
			$tpl->assign('MENU_TYPE',$VAR_MENUTYPE);
			$tpl->printAll();

		 break;
		case "POST":

		$VAR_MENUTYPE = $this->config->item('menu_type');
		$member_type = $this->config->item('member_type');
		

		foreach( $_POST as $val => $value ){
			$$val = $value;
		} 

		$sql = "select * from ".$this->MENU_TABLE." where num_oid="._OID." and num_mcode=$mcode";
		$def_data = $DB -> sqlFetch($sql);

		$menu_data = $this->webapp->get($str_type); //application/controllers/__get 설정있음
		
		$_POST[str_mobile_text] = str_replace("../../contents/m/","/application/views/mobile/",$_POST[str_mobile_text]);
		$_POST[str_mobile_text] = str_replace("/contents/m/images/sub_new/","/application/views/mobile/images/sub_new/",$_POST[str_mobile_text]);
		$_POST[str_mobile_text] = addslashes($_POST[str_mobile_text]);
		$_POST[str_text] = addslashes($_POST[str_text]);

		 $sql = "update ".$this->MENU_TABLE." set str_title='$str_title',  str_title2='$str_title2', num_view=$num_view,  str_type='$str_type' , str_inc_file='".$_POST[str_inc_file]."' , str_inc_file2='".$_POST[str_inc_file2]."' , str_inc_file3='".$_POST[str_inc_file3]."' , str_layout = '".$_POST[str_layout]."'  , str_menu_top = '".$_POST[str_menu_top]."'
		 
		  , str_menu_bottom = '".$_POST[str_menu_bottom]."'
		  , str_lnb_file = '".$_POST[str_lnb_file]."'
		  , str_text = '".$_POST[str_text]."'
		  , str_mobile_inc_file = '".$_POST[str_mobile_inc_file]."'
		  , str_mobile_inc_file2 = '".$_POST[str_mobile_inc_file2]."'
		  , str_mobile_text = '".$_POST[str_mobile_text]."'
		 
		 where num_oid="._OID." and num_mcode=$mcode";

	
		$DB->query($sql);
		
		switch ($str_type) {
		
			case "board":
			
			$sql = "select count(*) from ".$this->BOARD_TABLE_CONFIG."  where num_oid="._OID." and num_mcode=$mcode ";
			$ins_menu = $DB -> sqlFetchOne($sql);
			
			if(!$ins_menu){

				
				
				$datas[num_oid] = _OID;
				$datas[num_mcode] = $mcode;
				$datas[str_title] = $str_title;
				$datas[str_skin] = $str_skin;
				$datas[num_listnum] = $listnum;
				$datas[num_navnum] = 10;
				
				$datas[num_titlelen] = 200;
				$datas[chr_listtype] = 'B';
				$datas[chr_comment] = "Y";
				$datas[chr_recent] = 'Y';
				$datas[chr_upload] = "Y";
				$datas[str_category_use] = $_POST[str_category_use];
				$datas[str_category_use2] = $_POST[str_category_use2];
				
				$DB->insertQuery($this->BOARD_TABLE_CONFIG,$datas);

			}else{

				if(!$chr_comment) $chr_comment = "N";
				if(!$chr_upload) $chr_upload = "N";

				//2008-04-15 종태 게시판 명까지
				$sql = "UPDATE tab_board_config SET 
				str_title='$str_title', 
				str_skin='$str_skin',
				
				num_listnum  = '$num_listnum',
				
				chr_comment  = '$chr_comment',
				chr_upload  = '$chr_upload',
				str_download   = '$str_download',
				str_admin_id = '$str_admin_id',
				chr_listtype = '$chr_listtype',
				str_category_use = '$str_category_use',
				str_category_use2 = '$str_category_use2',
				str_top='$str_top',
				str_top2='$str_top2'
				
				
				WHERE NUM_OID="._OID." AND NUM_MCODE=$mcode";
				
				$DB->query($sql);

			}

			break;
			case "link":
					
			//링크메뉴에도 서브메뉴코드를 자동으로 붙이자~ 종태
			if($str_url && strlen($str_url) >3){
				if(!strstr($str_url,"cate")) {
					if(strstr($str_url,"?")) {
					$str_url = $str_url."&cate=$cate";
					}else{
					$str_url = $str_url."?cate=$cate";
					}
				}
			}else{
				$str_url = "#";
			}
			
			
			
			$sql = "select count(*) from ".$this->URL_LINK_TABLE."  where num_oid="._OID." and num_mcode=$mcode ";
			$ins_menu = $DB -> sqlFetchOne($sql);
			
			if(!$ins_menu){
				$datass[num_oid] = _OID;
				$datass[num_mcode] = $mcode;
				$datass[num_cate] = $cate;
				$datass[str_url] = $str_url;
				$datass[str_target] = $str_target;
				$DB->insertQuery($this->URL_LINK_TABLE,$datass);
				
			}else{
				$sql = "
					UPDATE ".$this->URL_LINK_TABLE." SET
					    str_url='{$str_url}', str_target='{$str_target}'
					WHERE
					    num_oid="._OID." AND num_mcode='{$mcode}'
				  ";
				$DB->query($sql); 
			}
			break;

			case "ifr":
			
			if(!$str_height) $str_height = 700;

			$sql = "select count(*) from ".$this->URL_LINK_TABLE."  where num_oid="._OID." and num_mcode=$mcode ";
			$ins_menu = $DB -> sqlFetchOne($sql);
			
			if(!$ins_menu){
				$datass[num_oid] = _OID;
				$datass[num_mcode] = $mcode;
				$datass[num_cate] = $cate;
				$datass[str_url] = $str_url;
				$datass[str_target] = $str_target;
				$datass[str_height] = $str_height;
				$DB->insertQuery($this->URL_LINK_TABLE,$datass);
			}else{
			$sql = "
				UPDATE ".$this->URL_LINK_TABLE." SET
				    str_url='{$str_url}', str_target='self_', str_height='{$str_height}'
				WHERE
				    num_oid="._OID." AND num_mcode='{$mcode}'
					  ";
				$DB->query($sql);
			}
			break;


			}	
			

			if($def_data[str_type] != $str_type){
			$sql = "delete from ".$this->RIGHT_TABLE." where num_oid = '"._OID."' and str_sect='menu' and str_code = '".$mcode."'";
			$DB->query($sql);

			foreach($member_type as $mem_type => $_value) {
                    $sql = "INSERT INTO ".$this->RIGHT_TABLE." (num_oid,str_sect,str_code,str_group,str_right)".
                           "VALUES ("._OID.",'menu','$mcode','$mem_type','".$menu_data['default_rights']."')";
                    $DB->query($sql);
                }
		
			  //추가그룹
			  if ($menu_data['default_group_rights']) {
		  			$sql = "select * from ".$this->GROUP_TABLE." where num_oid = '"._OID."' ";
					$row = $DB -> sqlFetchAll($sql);
					for($ii=0; $ii<count($row); $ii++) {
					 $sql = "INSERT INTO ".$this->RIGHT_TABLE." (num_oid,str_sect,str_code,str_group,str_right)".
						   "VALUES ("._OID.",'menu','$mcode','".$row[$ii][str_group]."','".$menu_data['default_group_rights']."')";
					  $DB->query($sql);
					}
			}
		}

		//exec("rm -rf "._DOC_ROOT.'/tmp/*.xml'); //플래시용 xml 임시파일 삭제
		//WebApp::redirect('/site_adm/menu_option/'.$mcode);
		 break;
		}
	}
	
	


	function menu_docs($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		 $this->load->helper('file');
		
		//메뉴 불러오기
		$this->load->model('user/menu');
		$this->site_menu = $this->menu->load_tree();
		
		$tpl->assign(array('site_menu_list'=>$this->site_menu));
		
		
		
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

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		
		 if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
			$contents = read_file('data/docs/'.$mcode.".html");
		 }else{
			$contents ="";

		 }
		$sect = "docs/".$mcode;
		$tpl->assign(array(
			'contents'=>$contents,
			'sect'=>$sect,
			'mcode'=>$mcode,
			'layout'=>$_GET[layout],
			'cate'=>$_GET[cate],
		 ));

		
		if(!$contents){
			$sql = "select * from tab_menu where num_oid = '"._OID."' and num_mcode = '".$mcode."'  ";
			$menu_data = $this->db-> sqlFetch($sql);
			
			$contents = $menu_data[str_text];
		}


						 

		$sect = "docs/".$mcode;
		 $tpl->assign(array(
			'contents'=>$contents,
			'sect'=>$sect,
			'mcode'=>$mcode,
			'layout'=>$_GET[layout],
			'cate'=>$_GET[cate],
		 ));
		 

		$tpl->setLayout('@sub');
		 $tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_docs.htm"));
		 $tpl->printAll();
		
		 break;
		case "POST":
		
		$data[str_text] = $_POST[contents];
		
		$s = preg_match_all("/<img\s+.*?src=[\"\']([^\"\']+)[\"\'\s][^>]*>/is", $data[str_text], $m); 
		$tmp_img_list = $m[1];


		for($ii=0; $ii<count($tmp_img_list); $ii++) {
			if(!strstr($tmp_img_list[$ii],$_SERVER[HTTP_HOST]) && strstr($tmp_img_list[$ii],'http://')){
				$tmp_img = $this->curl->curl_url($tmp_img_list[$ii]);
				$filename = md5(array_pop(explode("/",$tmp_img_list[$ii]))).".gif";
					
				$s = fopen('data/files/'.$filename, "w");
				fwrite($s, $tmp_img);
				fclose($s);

				$data[str_text] = str_replace($tmp_img_list[$ii],'/data/files/'.$filename,$data[str_text]);
				
			}
		}

		
		write_file('data/docs/'.$mcode.".html", $data[str_text]);
		$data[str_text] = addslashes($data[str_text]);
		$this->db->updateQuery("tab_menu",$data," num_mcode = '".$mcode."' ");
		
		 
		
		$this->webapp->daumEditUpload($_POST[attach_img_file],"image");
		$this->webapp->daumEditUpload($_POST[attach_file],"file"); 
		

			$this->webapp->redirect('/user/page/doc/'.$_POST[mcode].'?cate='.$_POST[cate]);
		 
		 break;
		}
	}


	function menu_right($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		$VAR_MENUTYPE = $this->config->item('menu_type');
		$_mem_types = $this->config->item('member_type');
		
		$menu_type = $DB->sqlFetch("SELECT * FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode=$mcode");

		$menu_data = $this->webapp->get($menu_type[str_type]); //application/controllers/__get 설정있음
		$enable_rights = $menu_data['rights'];

		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
			
			 $sub_cnt = $DB->sqlFetchOne("SELECT COUNT(*) FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$mcode."__'");

			foreach($_mem_types as $key_ => $value_) {
				$mem_types[] = array('name' => $value_, 'code' => $key_);
			}
			
			for($ii=0; $ii<count($mem_types); $ii++) {
				$mem_types[$ii][RIGHTS] = $enable_rights;
			}

			// 추가그룹
			$sql = "SELECT * FROM ".$this->GROUP_TABLE." WHERE num_oid="._OID."";
			if($gdata = $DB->sqlFetchAll($sql)) {
				 for($ii=0; $ii<count($gdata); $ii++) {
					$gdata[$ii][RIGHTS] = $enable_rights;
				}
			}
			
			// 현재 설정된 데이타
			  $sql = "SELECT * FROM ".$this->RIGHT_TABLE." WHERE num_oid="._OID." AND str_sect='menu' AND str_code='$mcode'";
			  if($rdata = $DB->sqlFetchAll($sql)) {
				$data = array();
				foreach($rdata as $row) {
				    $_rights = str_split($row['str_right']);
				    foreach($_rights as $_right) {
					  $data[] = array('str_group'=>$row['str_group'],'right'=>$_right);
				    }
				}
			  }
			 
			 
			
			// {{{ 메뉴위치 로케이션바 만들기
			$_mcode = $menu_type[num_cate];
			while(strlen($_mcode = substr($_mcode,0,-2)) > 1){
				$_location[] = $DB->sqlFetchOne("SELECT str_title FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_cate=$_mcode");
			} 
			$_location[] = '메인';
			$menu_location = implode(' > ',array_reverse($_location));

			
			// }}}
		  
		
		  $tpl->assign($menu_data);
		  $tpl->assign($menu_type);
		  $tpl->assign(array(
			'mcode'     =>  $mcode,
			'sub_cnt'   =>  $sub_cnt,
			'LIST'      =>  $data,
			'TYPES'     =>  $mem_types,
			'GROUPS'    =>  $gdata,
			'menu_location' => $menu_location,
			
		  ));
		
		$tpl->setLayout('none');
		 $tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_rights.htm"));
		 $tpl->printAll();

		break;
		case "POST":
		$sql = "DELETE FROM ".$this->RIGHT_TABLE." WHERE num_oid="._OID." AND str_sect='menu' AND str_code='$mcode'";
		  $DB->query($sql);

		  $rights = $_POST['rights'];
		  foreach($rights as $group => $row) {
			$str_right = '';
			foreach($row as $right => $value) {
			    $str_right .= $right;
			}
			$sql = "INSERT INTO ".$this->RIGHT_TABLE." (num_oid,str_sect,str_code,str_group,str_right) ".
				 "VALUES ("._OID.",'menu','$mcode','$group','$str_right')";
			$DB->query($sql);
		  }
		 
		  WebApp::redirect(_BASEURL);
		break;
		}

	}


	function menu_listorder($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
			
			if($mcode){
				
				$data = $DB->sqlFetchAll("SELECT * FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode LIKE '".$mcode."__' order by num_step asc ");
				
				$VAR_MENUTYPE = $this->config->item('menu_type');
				$menu_type = $DB->sqlFetch("SELECT * FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_mcode=$mcode order by num_step asc");

				$menu_data = $this->webapp->get($menu_type[str_type]); //application/controllers/__get 설정있음
				$enable_rights = $menu_data['rights'];
				
				// {{{ 메뉴위치 로케이션바 만들기
				$_mcode = $menu_type[num_cate];
				while(strlen($_mcode = substr($_mcode,0,-2)) > 1){
					$_location[] = $DB->sqlFetchOne("SELECT str_title FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND num_cate=$_mcode");
				} 
				$_location[] = '메인';
				$menu_location = implode(' > ',array_reverse($_location));
			

			}else{
				$data = $DB->sqlFetchAll("SELECT * FROM ".$this->MENU_TABLE." WHERE num_oid="._OID." AND LENGTH(num_cate)=2");

				$_location[] = '메인';
				$menu_location = implode(' > ',array_reverse($_location));
				$menu_data[str_title] = "메뉴";
			}
		  
		
		  $tpl->assign($menu_data);
		  $tpl->assign($menu_type);
		  $tpl->assign(array(
			'mcode'     =>  $mcode,
			
			'LIST'      =>  $data,
			'menu_location' => $menu_location,
			
		  ));
		
		$tpl->setLayout('none');
		 $tpl->define("CONTENT", $this->display->getTemplate("admin/menu/menu_listorder.htm"));
		 $tpl->printAll();

		break;
		case "POST":

			$cates = $_POST['cates'];
			foreach ($cates as $cate) {
				$i++;
				$DB->query("UPDATE ".tab_menu." SET num_step=$i , num_step_back=$i WHERE num_oid="._OID." AND num_cate=$cate");
			}
		
		break;
		}

	}
	
	
	function board_category($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		if($mcode){
			$sql = "select num_serial, str_category from ".$this->BOARD_TABLE_CATE." where num_oid = '"._OID."' and num_mcode = '$mcode' ";
			$row = $DB -> sqlFetchAll($sql);
		}
		$tpl->assign(array('cate_LIST'=>$row,'mcode'=>$mcode));
		$tpl->setLayout('none_src');
		$tpl->define("CONTENT", $this->display->getTemplate("admin/menu/board_category.htm"));
		$tpl->printAll();
		 break;
		case "POST":
		
		$mode = $_POST[mode];
		$str_category_text = $_POST[str_category_text];
		$str_category = $_POST[str_category];

		 $sql = "select count(*) from ".$this->BOARD_TABLE_CATE." where num_oid = '"._OID."' and num_mcode=".$mcode." and str_category='".$_POST[str_category_text]."'";
		$cat_count = $DB -> sqlFetchOne($sql);

		if($mode == 'add'){	//카테고리 추가,수정
			if($str_category_text && ($str_category_text != '일반')){
				if(!$cat_count){

					if($str_category){
						//카테고리수정
						$sql = "update  ".$this->BOARD_TABLE_CATE."  set str_category='$str_category_text' where num_oid='"._OID."' and num_mcode=$mcode and str_category='$str_category'";
						$DB->query($sql);
					
						WebApp::moveBack('수정되었습니다.');
					}else{
						//카테고리추가

						$cat_serial = $this->webapp->maxValue($this->BOARD_TABLE_CATE,' num_serial ', "  num_oid = "._OID." and num_mcode=$mcode ");
						if (!$cat_serial) $cat_serial = 1;

						$sql = "INSERT INTO ".$this->BOARD_TABLE_CATE." (num_oid, num_mcode, num_serial, str_category) VALUES("._OID.", $mcode, $cat_serial, '$str_category_text')";
						$DB->query($sql);
						
						WebApp::redirect('/admin/menu_adm/board_category/'.$mcode);
					}
				}else{
					WebApp::moveBack('동일한 카테고리가 있습니다.');
				}
			}else{
				WebApp::moveBack('카테고리를 입력해주세요.');
			}

		}elseif($mode == 'del'){	//카테고리 삭제
			if($cat_count){
				$sql = "delete from ".$this->BOARD_TABLE_CATE." where num_oid='"._OID."' and num_mcode=$mcode and str_category='$str_category_text'";
				if($DB->query($sql)){
					

					$sql = "UPDATE ".$this->BOARD_TABLE." set str_category='일반' WHERE num_oid="._OID." AND num_mcode=$mcode and str_category='$str_category_text'";
					$DB->query($sql);
					WebApp::redirect('/admin/menu_adm/board_category/'.$mcode,'삭제되었습니다.');
               		
				}else{
					WebApp::moveBack('Error!!!');
				}
			}
		}

		 break;
		}
	}


	function board_category2($mcode=""){
		$tpl = $this->display;
		$DB = $this->db;
		
		switch ($_SERVER[REQUEST_METHOD]) {
		case "GET":
		
		if($mcode){
			$sql = "select num_serial, str_category from ".$this->BOARD_TABLE_CATE2." where num_oid = '"._OID."' and num_mcode = '$mcode' ";
			$row = $DB -> sqlFetchAll($sql);
		}
		$tpl->assign(array('cate_LIST'=>$row,'mcode'=>$mcode));
		$tpl->setLayout('none_src');
		$tpl->define("CONTENT", $this->display->getTemplate("admin/menu/board_category.htm"));
		$tpl->printAll();
		 break;
		case "POST":
		
		$mode = $_POST[mode];
		$str_category_text = $_POST[str_category_text];
		$str_category = $_POST[str_category];

		 $sql = "select count(*) from ".$this->BOARD_TABLE_CATE2." where num_oid = '"._OID."' and num_mcode=".$mcode." and str_category='".$_POST[str_category_text]."'";
		$cat_count = $DB -> sqlFetchOne($sql);

		if($mode == 'add'){	//카테고리 추가,수정
			if($str_category_text && ($str_category_text != '일반')){
				if(!$cat_count){

					if($str_category){
						//카테고리수정
						$sql = "update  ".$this->BOARD_TABLE_CATE2."  set str_category='$str_category_text' where num_oid='"._OID."' and num_mcode=$mcode and str_category='$str_category'";
						$DB->query($sql);
					
						WebApp::moveBack('수정되었습니다.');
					}else{
						//카테고리추가

						$cat_serial = $this->webapp->maxValue($this->BOARD_TABLE_CATE2,' num_serial ', "  num_oid = "._OID." and num_mcode=$mcode ");
						if (!$cat_serial) $cat_serial = 1;

						$sql = "INSERT INTO ".$this->BOARD_TABLE_CATE2." (num_oid, num_mcode, num_serial, str_category) VALUES("._OID.", $mcode, $cat_serial, '$str_category_text')";
						$DB->query($sql);
						
						WebApp::redirect('/admin/menu_adm/board_category2/'.$mcode);
					}
				}else{
					WebApp::moveBack('동일한 카테고리가 있습니다.');
				}
			}else{
				WebApp::moveBack('카테고리를 입력해주세요.');
			}

		}elseif($mode == 'del'){	//카테고리 삭제
			if($cat_count){
				$sql = "delete from ".$this->BOARD_TABLE_CATE2." where num_oid='"._OID."' and num_mcode=$mcode and str_category='$str_category_text'";
				if($DB->query($sql)){
					

					$sql = "UPDATE ".$this->BOARD_TABLE." set str_category2='일반' WHERE num_oid="._OID." AND num_mcode=$mcode and str_category2='$str_category_text'";
					$DB->query($sql);
					WebApp::redirect('/admin/menu_adm/board_category2/'.$mcode,'삭제되었습니다.');
               		
				}else{
					WebApp::moveBack('Error!!!');
				}
			}
		}

		 break;
		}
	}
	

	


	///function //



	function newChild($mcode) {
		$DB = $this->db;
		$_OID = _OID;
		$sql = "SELECT MAX(num_mcode) FROM ".$this->MENU_TABLE." WHERE num_oid=$_OID AND num_mcode LIKE '".$mcode."__'";
		$result = $DB->sqlFetchOne($sql);
		if (!$result) $max_mcode = 10;
		else $max_mcode = substr($DB->sqlFetchOne($sql),-2) + 1;
		if ($max_mcode > 99) WebApp::raiseError(_('Can not add menu'));
		return $mcode.sprintf("%02d",$max_mcode);
	}

	function newChild2($mcode) {
		$DB = $this->db;
		$_OID = _OID;
		$sql = "SELECT MAX(num_cate) FROM ".$this->MENU_TABLE." WHERE num_oid=$_OID AND num_cate LIKE '".$mcode."__'";
		$result = $DB->sqlFetchOne($sql);
		if (!$result) $max_mcode = 10;
		else $max_mcode = substr($DB->sqlFetchOne($sql),-2) + 1;
		if ($max_mcode > 99) WebApp::raiseError(_('Can not add menu'));
		return $mcode.sprintf("%02d",$max_mcode);
	}


	function newStep($mcode) {
		$DB = $this->db;
		$_OID = _OID;
		$sql = "SELECT MAX(num_step) FROM ".$this->MENU_TABLE." WHERE num_oid=$_OID AND num_mcode LIKE '".$mcode."__'";
		return $DB->sqlFetchOne($sql) + 1;
	}

	//=================================================
	// _REQUEST 에 $URL->vars 값 덮어씌움
	// overwriteREQUEST($_REQUEST,$_POST,$URL);
	//=================================================


	///function end//

}//

/* End of file .menu_adm.php */
/* Location: ./application/controllers/menu_adm.php */
