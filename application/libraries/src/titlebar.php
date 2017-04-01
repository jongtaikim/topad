<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-14
* 작성자: 김종태
* 설   명: 타이틀바
*****************************************************************
* 
*/

$sql = "select str_title from TAB_MENU where num_oid = '"._OID."' ";
$data = $DB -> sqlFetchOne($sql);
$tpl->assign(array('DOC_TITLE'=>$data));



?>