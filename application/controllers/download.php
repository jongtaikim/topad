<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-22
* 작성자: 김종태
* 설   명:  파일 다운로드
*****************************************************************
* 
*/
class Download extends CI_Controller {
	  
	   var $FILES_TABLE =  "TAB_FILES";
	
	 function __construct(){
		parent::__construct();
		
	}

	function files(){
		$DB = $this->db;
		
		$path_info = getenv("PATH_INFO");
		$path_infos = explode("/",$path_info);
		array_shift($path_infos);
		
		$refilename = $path_infos[count($path_infos)-1];
		$serial = $path_infos[count($path_infos)-2];
		
		for($ii=2; $ii<count($path_infos)-2; $ii++) {
			$sect .= $path_infos[$ii]."/";
		}
		$sect = substr($sect,0,strlen($sect)-1);

		$sql = "select * from ".$this->FILES_TABLE." where num_oid = '"._OID."' and str_sect = '".$sect."' and num_serial = '".$serial."'";
		if($fdata = $this->db->sqlFetch($sql)){
		
			$sql = "update ".$this->FILES_TABLE." set num_down = num_down + 1 where num_oid = '"._OID."' and str_sect = '".$sect."' and num_serial = '".$serial."'";
			$DB->query($sql);

				$filepath = _DOC_ROOT.$fdata[str_refile];
			
				ini_set("memory_limit", "50M"); 
				if($filepath && is_file($filepath)) {
					$filesize = filesize($filepath);
					$_MIME_TYPE = $fdata[str_mime];
					$filename=$fdata[str_upfile];

					header("Content-type: $_MIME_TYPE");
					header("Content-Length: $filesize");
					header("Content-Disposition: ".($nocount ? "inline" : "attachment")."; filename=$filename");
					$fp = fopen($filepath,'r');
					fpassthru($fp);
					fclose($fp);
				} else {
					echo "<script>alert('파일이 존재하지 않습니다.');</script>";
				}

			}

	}


}


/* End of file .php */
/* Location: ./application/controllers/.php */
