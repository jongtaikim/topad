<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-29
* 작성자: 김종태
* 설   명: 메인 팝업 오픈
*****************************************************************
* 
*/
$CI =& get_instance();
$DB = $CI->db;
$tpl = $CI->display;

//개별팝업 Start
$sql = "SELECT * FROM ".TAB_POPUP." 
			WHERE num_oid = "._OID." and DT_START_DATE<= '".date("Y-m-d")."' AND DT_END_DATE >= '".date("Y-m-d")."' 
			  and str_view='Y'
			order by num_serial asc";


$row = $DB -> sqlFetchAll($sql);

$tpl->assign(array('popup_LIST'=>$row));

$param['template'] = 'application/controllers/src/tpl/popup.htm';

?>


