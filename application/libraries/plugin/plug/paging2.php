<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-14
* 작성자: 김종태
* 설  명:  페이징 테그
*****************************************************************
* 
*/
if($attr["idx"]) {
	$ret = '<?$CI =& get_instance();echo $CI->webapp->pageings2('.$attr["total"].','.$attr["listnum"].','.$attr["page"].','.$attr["idx"].');?>';
}else{
	$ret = '<?$CI =& get_instance();echo $CI->webapp->pageings2('.$attr["total"].','.$attr["listnum"].','.$attr["page"].');?>';
}
return $ret;
?>