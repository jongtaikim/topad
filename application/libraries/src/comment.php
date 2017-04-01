<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-24
* 작성자: 김종태
* 설  명:  코멘트
*****************************************************************
* 
*/
$CI =& get_instance();
$DB = $CI->db;
$tpl = $CI->display;
$CI->load->library('session');
	$_SESSION = $CI->session->all_userdata();

	if(!$param[comment_title]) $param[comment_title] = "덧글";
	if(!$param[len]) $param[len] = "4000";
	if(!$param[nologin]) $param[nologin] = "n";
	

	$oid = _OID;
	$code = $param[sect];
	
	$sql = "select 
	*
	from tab_comment where num_oid = '".$oid."' and num_code = '".$code."' order by       num_group asc ,    num_step asc";	
	
	$row = $DB -> sqlFetchAll($sql);

	for($ii=0; $ii<count($row); $ii++) {
		
		//if(!$_SESSION[ADMIN]) $row[$ii]['str_ip']= substr(md5($row[$ii]['str_ip']),0,8);
		// 그냥 네이년 따라해봐츰...쿠쿠...이런 노가닥말고 뭔가 잇을꺼 같은데...2010-02-01.juni
		$tmpIp = explode('.', $row[$ii]['str_ip']);
		for ($jj=0;sizeof($tmpIp)-1>$jj ;$jj++ ) {
			$ExpTmpIp.= $tmpIp[$jj].".";
		}
		if(!$_SESSION[ADMIN]) $row[$ii]['str_ip']= $ExpTmpIp."*";
		$ExpTmpIp = '';

	}

	$tpl->assign(array(
	'comment_LIST'=>$row,
	'code_url'=>$code,
	));

$param['template'] = 'application/libraries/src/tpl/comment.htm';

$tpl->assign($param);
?> 