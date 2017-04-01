<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 슈퍼클래스
* 작성자: 김종태
* 설   명:  2012-02-10
*****************************************************************
* 
*/
class WebApp  {
		
		var $FILES_TABLE = "tab_files";
		var $Extension = "";
		var $IsTrueColor = false;

	function __construct() {
		
		$CI =& get_instance();
		$CI->load->library('display');
		foreach( $_GET as $val => $value )
		{	
			$CI->display->assign(array($val=>$value));
		} 
		//사이트 환경설정 불러오기 
		define('_OID', $CI->config->item('oid'));
		
		//모바일 스킨 보여주기
		if($this->isMobile()){
			
			$CI->display->assign(array('is_mobile'=>'Y'));

			if(!isset($_GET['platform']) && $_GET['platform'] != 'pc' && $_COOKIE['platform'] != 'pc'){
				$theme_set = 'user_page';
				
			}else{
				$theme_set = 'user_page';
			}
			
		}else{
			$theme_set = 'user_page';
		}
		
		$a_url = explode(".",$_SERVER[HTTP_HOST]);
		$host = $a_url[0];

		if($host == "keyword"){
			$theme_set = $host;
		}
			
		
		define('THEME', $theme_set);
		define('_SITEID', $CI->config->item('siteid'));
		define('FACEBOOK_APP_ID', $CI->config->item('facebook_app_id'));
		define('KAKAO_APP_ID', $CI->config->item('kakao_app_id'));
		define('NAVER_APP_ID', $CI->config->item('naver_app_id'));

		define('_DOC_ROOT', $CI->config->item('document_root'));
		define('_CRM_URL', $CI->config->item('crm_url'));
		define('_PLUG_ROOT', $CI->config->item('plugin_root'));
		define('_CHARSET', $CI->config->item('charset'));
		define('_CONT_ROOT', _DOC_ROOT."/application/controllers/");
		
		if($_SERVER[QUERY_STRING]) $qs = "?".$_SERVER[QUERY_STRING]; else $qs="";
		define('_QS', $qs);
		 
		 //추후에 손볼 필요가 있음 2012-02-14 종태
		 $CI->load->helper('url');
		 $baseurls = explode("/",uri_string());
		
		 define('_BASEURL',  "/".$baseurls[0]."/".$baseurls[1]."/".$baseurls[2]);
		 define('_MODEL', $baseurls[0]);
		 define('_FUNCTION', $baseurls[1]);
		 define('_FUNCTION2', $baseurls[2]);
		


		$site_data = @parse_ini_file(_DOC_ROOT.'/application/config/'.THEME.'/site_config.php',true);
		define('_ONAME', $site_data[oname]);
		define('_TITLE', $site_data[title]);
		define('_EMAIL', $site_data[email]);
		define('_OPHONE', $site_data[ophone]);
		define('_ADMIN_ID', $site_data[admin_id]);
		define('_ADMIN_PW', $site_data[admin_pw]);
		define('_END_DATE', $site_data[end_date]);
	
		if($_GET[cate]) define('_CATE', $_GET[cate]);
		if($_GET[mcode]) define('_MCODE', $_GET[mcode]);
		
		
		if(!is_dir(_DOC_ROOT.'/data')){ //데이터 폴더 체크 및 생성
			@exec("mkdir "._DOC_ROOT.'/data');
			@exec("chmod 707 "._DOC_ROOT.'/data');
		}

		if(!is_dir(_DOC_ROOT.'/data/files')){ //데이터 폴더 체크 및 생성
			@exec("mkdir "._DOC_ROOT.'/data/files');
			@exec("chmod 707 "._DOC_ROOT.'/data/files');
		}

		if(!is_dir(_DOC_ROOT.'/data/conf')){ //환경설정 파일 폴더 체크 및 생성
			@exec("mkdir "._DOC_ROOT.'/data/conf');
			@exec("chmod 707 "._DOC_ROOT.'/data/conf');
		}

		if(!is_dir(_DOC_ROOT.'/data/docs')){ //웹페이지 폴더 생성
			@exec("mkdir "._DOC_ROOT.'/data/docs');
			@exec("chmod 707 "._DOC_ROOT.'/data/docs');
		}

		
	}
	
	//모바일 체크
	function isMobile() {
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}


	//문자열 자르기
	  function str_split($str,$len = 1) {
		  $len = ceil($len);
		  if($len < 1) $len = 1;
		  $cnt = ceil(strlen($str)/$len);
		  $ret = array();
		  for($i = 0;$i < $cnt;$i++) {
			$ret[] = substr($str,$i*$len,$len);
		  }
		  return $ret;
	    }
	
	//비율에 맞춰 이미지 사이즈 리턴
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

	//CI기본 페이지 이용하기
	  function pageings($total , $listnum,$currentPage,$idx=3){
			
			$CI =& get_instance();
			//페이징
			
		
			$CI->load->library('paging');
			$CI->paging->setConf("idx", $idx);
		
			$CI->paging->setConf("class", "paginate_complex");
			$CI->paging->setConf("itemPerPage", $listnum);
			$CI->paging->setConf("total", $total);
			
			$CI->paging->setConf("currentPage", $currentPage);
			return $CI->paging->output($total); 

			//return $CI->pagination->create_links();
	  }
	
	//필요에 따라 두번째 추가함
	  function pageings2($total , $listnum,$currentPage,$idx=3){
			
			$CI =& get_instance();
			//페이징

			$CI->load->library('paging2');
			$CI->paging2->setConf("idx", $idx);
		
			$CI->paging2->setConf("class", "paginate_complex");
			$CI->paging2->setConf("itemPerPage", $listnum);
			$CI->paging2->setConf("total", $total);
			
			$CI->paging2->setConf("currentPage", $currentPage);
			return $CI->paging2->output($total); 

			//return $CI->pagination->create_links();
	  }
	
	//단순한 텍스트 컷
	 function text_cut($str, $len, $suffix="...") { 
	   if ($len >= strlen($str)) return $str;
	   $klen = $len - 1;
	   while (ord($str{$klen}) & 0x80) $klen--;
	   return substr($str, 0, $len - ((($len + $klen) & 1) ^ 1)) . $suffix;
	 }
	
	//캐시폴더를 사용한다면 삭제
	function cache_del(){
		exec("rm -rf "._DOC_ROOT."/tmp/inc.*");
	}

	//리스트 쿼리
	  function listPageing($table,$where,$orderby="",$page=1,$listnum=15,$key="",$search=""){
		
		$CI =& get_instance(); 
		$DB = $CI->db;
		$tpl = $CI->display;

		if($where) $where = " where ".$where;

		if ($key && $search) {
			if($where){
				$whereadd = " AND $key LIKE '%$search%'";
			}else{
				$whereadd = " where $key LIKE '%$search%'";
			}
		}
		
		foreach( $_GET as $val => $value ){
			if(strstr($val,"sch_")){
				if($where || $whereadd){
					if($value !="") $add_where .=  " and ".str_replace("sch_","",$val)." = '$value' ";
				}else{
					if($value !="") $add_where .=  " where ".str_replace("sch_","",$val)." = '$value' ";
				}
				$tpl->assign(array($val =>$value));
			}

			if(strstr($val,"like_")){
				if($where || $whereadd){
					if($value !="") $add_where .=  " and ".str_replace("like_","",$val)." like '%$value%' ";
				}else{
					if($value !="") $add_where .=  " where ".str_replace("like_","",$val)." like '%$value%' ";
				}
				$tpl->assign(array($val =>$value));
			}
		}
		
		$sql = "select count(*) from ".$table." $where $whereadd $add_where $orderby ";
		$total = $DB->sqlFetchOne($sql);
		
		if(!$total) $total = 0;
		if (!$page) $page = 1;
		
		$seek = $listnum * ($page - 1);
		$offset = $seek + $listnum;
		
		$sql = " select * from ".$table." $where $whereadd $add_where  $orderby LIMIT $seek , $listnum  ";
		
		$data = $DB->sqlFetchAll($sql);
		
		
		
		$tpl->assign(array(
		$table=>$data,
		'page'=>$page,
		'total'=>$total,
		'listnum'=>$listnum,
		));
		
		return $total;

	  }
	
	//리스트 쿼리 후 배열로 리턴
	   function listPageingRow($table,$where,$orderby="",$page=1,$listnum=15,$key="",$search="" ,$cum="*"){
		
		$CI =& get_instance(); 
		$DB = $CI->db;
		$tpl = $CI->display;

		if($where) $where = " where ".$where;

		if ($key && $search) {
			if($where){
				$whereadd = " AND $key LIKE '%$search%'";
			}else{
				$whereadd = " where $key LIKE '%$search%'";
			}
		}
		
		foreach( $_GET as $val => $value ){
			if(strstr($val,"sch_")){
				if($where || $whereadd){
					if($value !="") $add_where .=  " and ".str_replace("sch_","",$val)." = '$value' ";
				}else{
					if($value !="") $add_where .=  " where ".str_replace("sch_","",$val)." = '$value' ";
				}
				$tpl->assign(array($val =>$value));
			}

			if(strstr($val,"like_")){
				if($where || $whereadd){
					if($value !="") $add_where .=  " and ".str_replace("like_","",$val)." like '%$value%' ";
				}else{
					if($value !="") $add_where .=  " where ".str_replace("like_","",$val)." like '%$value%' ";
				}
				$tpl->assign(array($val =>$value));
			}

			if(strstr($val,"time_s_")){
				if($where || $whereadd){
					if($value !="") {
						
						$add_where .=  " and ".str_replace("time_s_","",$val)." >= '".$this->mkday($value)."' ";
					}
				}else{
					if($value !="") {
						 $add_where .=  " where ".str_replace("time_s_","",$val)." >= '".$this->mkday($value)."' ";
					}
				}
				$tpl->assign(array($val =>$value));
			}

			if(strstr($val,"time_e_")){
				if($where || $whereadd){
					if($value !="") {
						
						$add_where .=  " and ".str_replace("time_e_","",$val)." <= '".($this->mkday($value)+86400)."' ";
					}
				}else{
					if($value !="") {
						$add_where .=  " where ".str_replace("time_e_","",$val)." <= '".($this->mkday($value)+86400)."' ";
					}
				}
				$tpl->assign(array($val =>$value));
			}
		}
		
		$sql = "select count(*) from ".$table." $where $whereadd $add_where  ";
		$total = $DB->sqlFetchOne($sql);

		
		
		if(!$total) $total = 0;
		if (!$page) $page = 1;
		
		$seek = $listnum * ($page - 1);
		$offset = $seek + $listnum;

		$sql = " select ".$cum." from ".$table." $where $whereadd $add_where  $orderby LIMIT $seek , $listnum  ";
		$data = $DB->sqlFetchAll($sql);
		for($ii=0; $ii<count($data); $ii++) {
			$data[$ii][num] = $total - $seek - $ii;
		}
		
		
		
		$tpl->assign(array(
		'page'=>$page,
		'total'=>$total,
		'listnum'=>$listnum,
		));
		
		return $data;

	  }
	
	//해당 컬럼에 max값 가져오기
	 function maxValue($table,$cum,$where){
		$CI =& get_instance(); 
		$DB = $CI->db;

		$sql = "select max(".$cum.")+1 from ".$table." where $where";
		$maxnum = $DB->sqlFetchOne($sql);
		if(!$maxnum) $maxnum = 1;
		return $maxnum;

	 }
	
	//파일 배열에 url을 치환
	function fileMach($file_list,$filename){
		
		for($i=0; $i<count($file_list); $i++) {
				$re_file[$i][names] = str_replace("http://".$_SERVER[HTTP_HOST],"",$file_list[$i][files]);
				if($re_file[$i][names] == $filename ){
					return true;
				}	
			}
			
	}
	
	//바이트를 컨버팅
	function byte_convert($bytes){
	$symbol = array('byte', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');

	$exp = 0;
	$converted_value = 0;
	if( $bytes > 0 ){
		$exp = floor( log($bytes)/log(1024) );
		$converted_value = ( $bytes/pow(1024,floor($exp)) );
	}

	return sprintf( '%.2f'.$symbol[$exp], $converted_value );
	//return sprintf( '%d'.$symbol[$exp], $converted_value );
}
	
	//다음에디터용 업로드 처리 모듈
	 function daumEditUpload($file_list,$type="image",$re_sect=""){
		$CI =& get_instance(); 
		$DB = $CI->db;
		$CI->load->helper('file');
		$CI->load->library('session');
		$session_id = $CI->session->userdata('session_id');


		
		$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$_POST[sect] ."' and str_type='".$type."' and str_ssid = '".$session_id."' and str_ssid is not null";
	
		$delfile = $DB -> sqlFetchAll($sql);
		$ia = 0;

		if(!$re_sect) $re_sect = $_POST[sect];
		
	
		//임시파일 처리용
		for($ii=0; $ii<count($delfile); $ii++) {
				if($this->fileMach($file_list,$delfile[$ii][str_refile])){
					
					 $sql = "update ".$this->FILES_TABLE." set str_ssid='',str_sect = '".$re_sect."' where num_oid = '"._OID."' and str_sect = '".$_POST[sect]."' and str_type='".$type."' and str_refile='".$delfile[$ii][str_refile]."'  ";
					$DB->query($sql);	


				}
		}
		
		
		
		$sql = "select count(*) from ".$this->FILES_TABLE." WHERE num_oid="._OID." and str_sect='$re_sect' and str_type='".$type."'   ";
		$count_num = $DB -> sqlFetchOne($sql)+0;
		
		list($sect,$mcode,$id) = explode("/",$re_sect);
		
		if($type == "image"){
			//$sql = "UPDATE ".TAB_BOARD." SET num_image=$count_num WHERE num_oid="._OID." and num_mcode='$mcode' and num_serial = '".$id."' ";
		}else{
		//	$sql = "UPDATE ".TAB_BOARD." SET num_file=$count_num WHERE num_oid="._OID." and num_mcode='$mcode' and num_serial = '".$id."' ";
		}
		$DB->query($sql);

		return true;

	 }
	
	//ini파일을 만들어 기록
	function makeConf($filename,$attr){
		if(is_array($attr)){
			$CI =& get_instance(); 
			$CI->load->library('iniconf');
			$CI->load->helper('file');
			$CI->iniconf->load($filename);
			
			foreach( $attr as $val => $value ){
				$CI->iniconf->setVar($val,addslashes($value));
			} 
			write_file($filename, $this->iniconf->_combine());
			 return true;
		}else{
			return false;
		}
	}
	
	//xxs 체크
	function xssChk($content){
		if(strstr($content, "/\<[ \/]*(script)(.*?)\>/i")) {return false;}
		if(strstr($content ,"<script"))  {return false;}
		if(strstr($content ,"<SCRIPT")) {return false;}
		return true;
	}
	
	//숫자 필터링
	function sidFilter($str, $replace_str) 	{ 
	    $pattern = "(([0-9]{6})([-|\| |[:space:]]+)([0-9]{7}))";  //Pattern 
	    $result = ereg_replace($pattern, $replace_str, $str); 
	    return $result; 
	} 
	
	//날짜 유닉스 타임으로 변환
	function mkday($date){
		$a = explode("-",$date);
		$mkt = mktime(00, 00, 01, $a[1],  $a[2], $a[0]);
		return $mkt;
	}
	
	// GD로 이미지 정보를 불러옴
	function GDImageLoad($filename){

		$image_type = getimagesize($filename);
	
		 switch( $image_type[2] ) {
			  case 2: // JPEG일경우
				   $im = imagecreatefromjpeg($filename);
				   $this->Extension = "jpg";
				   break;
			  case 1: // GIF일 경우
				   $im = imagecreatefromgif($filename);
				   $this->Extension = "gif";
				   break;
			  case 3: // png일 경우
				   $im = imagecreatefrompng($filename);
				   $this->Extension = "png";
				   break;
			  default:
				   break;
		 }

		 $this->IsTrueColor = @imageistruecolor($im);

		 return $im;
	}

	//이미지 리사이징
	function GDImageResize($src_file, $dst_file, $width = "", $height = "", $type = "", $quality = 85){
		
		$im = $this->GDImageLoad($src_file);
		
		
		 if( !$im ) return false;

		 if( !$width ) $width = imagesx($im);
		 if( !$height ) $height = imagesy($im);

		 if( $this->IsTrueColor && $type != "gif" ) $im2 = imagecreatetruecolor($width, $height);
		 else $im2 = imagecreate($width, $height);

		 if( !$type ) $type = $this->Extension;

		 imagecopyresampled($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im), imagesy($im));

		 if( $type == "gif" ) {
			  imagegif($im2, $dst_file);
		 }
		 else if( $type == "jpg" || $type == "jpeg" ) {
			imagejpeg($im2, $dst_file, $quality);
		
		 }
		 else if( $type == "png" ) {
			  imagepng($im2, $dst_file);
		 }

		 imagedestroy($im);
		 imagedestroy($im2);

		 return true;
	}

	//특정 php에 값을 가져옴
	function get($module,$param=array()) {
		$path = 'application/config/__get/'.$module.'.php';
		
		if(is_file(_DOC_ROOT."/".$path)){
			return include $path;
		}
	}
	
	//다음에디터 로드
	function daumEditLoad($param=array()) {
	
		$path = 'application/controllers/src/daum_editor.php';
		if (is_file(_DOC_ROOT."/".$path)) {
			include $path;
		}
	}

	//얼럿
	function alert($msg) {
		$msg = str_replace(array("\n","'"),array("\\n","\'"),$msg);
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'"></head><body>';
		echo "<script>alert('$msg');</script>";
		echo "</body></html>";
	}

	//컨펌
	function confirm($msg,$yes,$no) {
		$msg = str_replace(array("\n","'"),array("\\n","\""),$msg);
		echo '<html><head><meta http-equiv="content-type" content="text/html; charset='._CHARSET.'"></head><body>';
		echo "<script>navigate(confirm('$msg') ? '$yes' : '$no');</script>";
		echo "</body></html>";
		exit;
	}
	
	//메세지 후 페이지 이동
	function redirect($url,$msg="") {
		if ($msg) WebApp::alert($msg);
		if (headers_sent()) {
			echo "<script>document.location.replace('$url');</script>";
			exit;
		} else {
			echo "<script>document.location.replace('$url');</script>";
			exit;
		}
	}
	
	//뒤로가기
	function moveBack($msg="") {
		if ($msg) WebApp::alert($msg);
		echo "<script>history.back();</script>";
		exit;
	}

	//얼럿2
	function halt($msg='') {
		if ($msg) WebApp::alert($msg);
		exit;
	}
	
	//새창닫기
	function closeWin($flag,$msg) {
		if ($flag) echo "<script>opener.location.reload();</script>";
		echo '<script>alert("'.$msg.'");</script>';
		echo "<script>self.close();</script>";
		exit;
	}

}


?>