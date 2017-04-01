<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-14
* 작성자: 김종태
* 설  명:  모듈을 load
*****************************************************************
* 
*/

if ($innerHTML) {
    $hash = md5($innerHTML);
    $dynTemplate = 'application/cache/dynamic/'.$GLOBALS['__html__'].'/'.$hash;
    if (!is_file($dynTemplae)) {
        savetofile($dynTemplate,$innerHTML);
    }
   
}

foreach( $attr as $val => $value ){
	if(strstr($value,"{") && strstr($value,"}")){
		if($value) $values .= '$param['.$val.'] ='.$value.';';
	}else{
		if($value) $values .= '$param['.$val.'] ="'.$value.'";	';
	}
} 



$values .= '$param[template] ="'.addslashes($dynTemplate).'";';
$ret = "<?";
$ret .= "
	".$values." 
	include 'application/libraries/src/src_body.php';
	";
$ret.= "?>";

return $ret;
?>
