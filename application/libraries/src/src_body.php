<?
$CI =& get_instance(); 
$tpl=$CI->display; 
$CI->load->helper('file');
$mou_name = $param['src'];
$cache_file = _DOC_ROOT.'/application/tmp/inc.'.$mou_name."_".md5($param[cate]);

if($param[cache] =="y"){
	if(!is_file($cache_file)){
	include 'application/libraries/src/'.$param['src'].".php";
	}else{
	$contents = read_file($cache_file);
	}
}else{
	include 'application/libraries/src/'.$param['src'].".php";
}


if(!$param['template']) {
	$param['template'] = "application/libraries/src/tpl/".$param['src'].".htm";
}

$tpl->define($mou_name.'_W_',$param['template']);

if($param[cache] =="y"){
	if(!is_file($cache_file)){
		write_file($cache_file, $tpl->fetch($mou_name.'_W_'));
		echo $tpl->fetch($mou_name.'_W_');
	}else{
		echo $contents;
	}
}else{
	echo $tpl->fetch($mou_name.'_W_');
}

unset($param);

?>