<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-23
* 작성자: 김종태
* 설  명:  첨부파일 로드
*****************************************************************
* 
*/
$CI =& get_instance();
$DB = $CI->db;
$tpl = $CI->display;

$sql = "select * from tab_files where num_oid = '"._OID."' and str_sect = '".$param['sect']."' ";
$row = $DB -> sqlFetchAll($sql);
for($ii=0; $ii<count($row); $ii++) {
	$row[$ii][pe_url] = "/download/files/".$param['sect']."/".$row[$ii][num_serial]."/".$row[$ii][str_upfile];
	$row[$ii][bytes] = $CI->webapp->byte_convert($row[$ii][num_size]);
}
$tpl->assign(array('files_LIST' =>$row));

$param['template'] = 'application/libraries/src/tpl/file_list.htm';

$tpl->assign($param);


?> 