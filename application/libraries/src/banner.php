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


//학교관리자가 등록한 통합배너
$sql = "SELECT
			NUM_OID, NUM_SERIAL,STR_TITLE,STR_LINK,CHR_OPEN,STR_FILE
			FROM ".TAB_BANNER." 
			WHERE NUM_OID="._OID." ";
$data = $DB->sqlFetchAll($sql);


if($data) {
	$width_att = $conf['width'] ? ' width="'.$conf['width'].'"' : '';
	$height_att = $conf['height'] ? ' height="'.$conf['height'].'"' : '';

	for($i=0,$cnt=count($data);$i<$cnt;$i++) {
		$data[$i]['file_url'] = '/data/banner/'.$data[$i]['str_file'];
		$data[$i]['banner_tag'] = '<img src="'.$data[$i]['file_url'].'"'.$width_att.$height_att.' border="0">';
	}

	$tpl->assign("LIST",$data);
}
	
$tpl->assign($param);


?> 