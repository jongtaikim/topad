<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-20
* 작성자: 김종태
* 설  명:  다음에디터 로드
*****************************************************************
* 
*/
$CI =& get_instance();
$CI->load->helper('url');
$DB = $CI->db;
$tpl = $CI->display;

$mou_name="daum_edit";

if (empty($attr['height'])) $attr['height'] = '280';
if (empty($attr['width'])) $attr['width'] = '650';
if (empty($attr['form'])) $attr['form'] = 'editForm';
if (empty($attr['name'])) $attr['name'] = 'content';
if (empty($attr['sect'])) $attr['sect'] = 'tmp_files';



$CI->load->library('session');
$session_id = $CI->session->userdata('session_id');

$sql = "select * from tab_files where num_oid = '"._OID."' and str_sect = '".$attr['sect']."' and str_ssid in ('')   ";

$row = $DB -> sqlFetchAll($sql);
for($ii=0; $ii<count($row); $ii++) {
	$row[$ii][pe_url] = "/download/files/".$attr['sect']."/".$row[$ii][num_serial]."/".$row[$ii][str_upfile];
}
$tpl->assign(array('img_file_LIST' =>$row));


$template = 'application/util/openeditor/editor.html';


$tpl->assign($attr);
$tpl->define('tmp_W_',$attr['content']);
$content = $tpl->fetch('tmp_W_');

$tpl->assign(array('content' =>stripslashes($content)));

$tpl->define($mou_name.'_W_',$template);
$contents = $tpl->fetch($mou_name.'_W_');

echo $contents."<div id='file_lists'></div><input type='hidden' id='org_sect' value='".$attr['sect']."'/>";

?> 