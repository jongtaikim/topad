<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-19
* 작성자: 김종태
* 설   명: 매인비주얼 롤링 플러그인
*****************************************************************
* 
*/

$sql = "select * from TAB_MAIN_VIS where num_oid = '"._OID."' and str_view = 'y'";
$row = $DB -> sqlFetchAll($sql);

$ia = 0;
for($ii=0; $ii<count($row); $ii++) {
	if(is_file(_DOC_ROOT."/data/vis/".$row[$ii][str_file])){
		$rows[$ia] = $row[$ii];
		$ia++;
	}
}

$tpl->assign(array('vis_LIST'=>$rows));


?>