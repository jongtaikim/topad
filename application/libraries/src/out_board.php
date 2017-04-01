<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-03-08
* 작성자: 김종태
* 설   명: 게시판 미리보기
*****************************************************************
* 
*/

$DB = $CI->db;
$tpl = $CI->display;
$url_ = explode("/",uri_string());

if(strstr($param[code],',')){
	$mcodes = explode(",",$param[code]);
}else{
	$mcodes[0] = $param[code];
}

if(!$param[listnum]) $listnum = 5; else $listnum = $param[listnum];
if(!$param[len]) $len = 100; else $len = $param[len];

$_OID = _OID;

for($ii=0; $ii<count($mcodes); $ii++) {
	$sql = "select * from ".TAB_BOARD." where num_oid = '$_OID' and num_mcode = '".$mcodes[$ii]."' and num_input_pass = 0 order by num_serial desc limit 0,$listnum ";
	$rows = $DB -> sqlFetchAll($sql);

	for($i=0; $i<count($rows); $i++) {
		$rows[$i][title] = $CI->webapp->text_cut($rows[$i][str_title],$len,'...');
		$rows[$i]['is_recent'] = date('U') - strtotime(date("Y-m-d",$rows[$i]['dt_date'])) < 241920;
	}
	
	$data[$ii][board_list] = $rows;
	unset($rows);


	$sql = "select num_cate from TAB_MENU where num_oid = '$_OID' and num_mcode = '".$mcodes[$ii]."' ";
	$data[$ii][cate] = $DB -> sqlFetchOne($sql);
	$data[$ii][mcode] = $mcodes[$ii];

	
	
	
}

$tpl->assign(array('outboard_DATA'=>$data));




/* End of file .php */
/* Location: ./application/controllers/.php */
