<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-14
* 작성자: 김종태
* 설  명:  다음에디터 로드, 변수처리
*****************************************************************
* 
*/
//$attrs = array2php($attr);
if ($innerHTML) {
    $hash = md5($innerHTML);
    
    $GLOBALS['__html__'] = str_replace(":","_",$GLOBALS['__html__']);
    $GLOBALS['__html__'] = str_replace("\\","/",$GLOBALS['__html__']);

    $dynTemplate = 'application/cache/dynamic/'.$GLOBALS['__html__'].'/'.$hash;
    if (!is_file($dynTemplae)) {
        savetofile($dynTemplate,$innerHTML);
    }
    
}
foreach( $attr as $val => $value ){
	if(strstr($value,"{") && strstr($value,"}")){
		if($value) $values .= '$attr['.$val.'] ='.$value.';';
	}else{
		if($value) $values .= '$attr['.$val.'] ="'.$value.'";	';
	}
}
$values .= '$attr[content] ="'.addslashes($dynTemplate).'";';
$ret = "<?php ".$values." include 'application/libraries/src/daum_editor.php';?>";
return $ret;
?> 