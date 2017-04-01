<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-29
* 작성자: 김종태
* 설   명: 권한 설정
*****************************************************************
* 
*/

class Permission {

    var $var = array();

	function check($sect='menu',$code='00',$cond='r' , $mode = '') {
		$CI =& get_instance(); 
		$DB = $CI->db;
				
		if(!isset($this->var[$sect][$code][$cond])) {
            if(!is_array($_SESSION['MEM_TYPE'])) $_SESSION['MEM_TYPE'] = array('n');
      
		$cond = $cond{0};
            $sql = "
                SELECT
                    COUNT(*)
                FROM
                    ".tab_menu_right."
                WHERE
                    num_oid="._OID." AND 
                    str_sect='".$sect."' AND 
                    str_code='".$code."' AND 
                    str_group IN ('".implode("','",$_SESSION['MEM_TYPE'])."') AND
                    INSTR(str_right,'$cond') > 0";

			
			$this->var[$sect][$code][$cond] = $DB->sqlFetchOne($sql);	
			
          }
		return $this->var[$sect][$code][$cond]|| $_SESSION['ADMIN'];
	}

	function apply($sect='menu',$code='00',$cond='r',$mesg=false) {
		$CI =& get_instance(); 
		$DB = $CI->db;
		
		if (!$mesg) $mesg = '로그인이 필요하거나, 이 페이지를 열람할 수 있는 권한이 아닙니다.'; 
		if (!$this->check($sect,$code,$cond)){
			if(!$_SESSION[USERID]) {

				if($_SERVER[REDIRECT_URL] !="/user/member/member_login"){
					if($_SERVER[REDIRECT_QUERY_STRING]) {
						$_SESSION['reurl'] =  $_SERVER[REDIRECT_URL]."?".$_SERVER[REDIRECT_QUERY_STRING];	
						$_reurl = $_SERVER[REDIRECT_URL]."?".$_SERVER[REDIRECT_QUERY_STRING];
					}else{
						$_SESSION['reurl'] =  $_SERVER[REDIRECT_URL];
						$_reurl = $_SERVER[REDIRECT_URL];
					}
				}	
				//echo "<meta http-equiv='Refresh' Content=\"0; URL='/user_page/member_login/layer?reurl=".urlencode($_reurl)."'\">";
				
					if(THEME !='mobile'){
					echo '<script>alert("로그인이 필요합니다.");</script>';
					}
					echo "<meta http-equiv='Refresh' Content=\"0; URL='/user/member/member_login?reurl=".urlencode($_reurl)."'\">";
				}else{
					$CI->webapp->moveBack($mesg);
				}
			exit;
		}
	}
}




