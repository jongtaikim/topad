<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-18
* 작성자: 김종태
* 설  명:  이미지폰트
*****************************************************************
* 
*/
if(!$attr['align']) $attr['align'] = "absmiddle";

if($attr['img']){
$str = '<img src="/util/imgfont/%img%/%font%/%size%/%color%/%bgcolor%/?text=%text%" alt="%text%"  id="%id%" class="%class%"  %event%="%js%" align="%align%" style="%style%"/>';
$attr['img'] = str_replace("/","|",$attr['img']);


return str_replace(
	array('%img%','%font%','%size%','%color%','%bgcolor%','%text%','%id%','%class%','%event%','%js%','%style%'),
	array($attr['img'],$attr['font'],$attr['size'],$attr['color'],$attr['bgcolor'],$attr['text'],$attr['id'],$attr['class'],$attr['event'],$attr['js'],$attr['style']),
	$str
);

}else{
$str = '<img src="/util/imgfontnobg/%font%/%size%/%bgcolor%/%color%/?text=%text%" alt="%text%"  id="%id%" class="%class%"  %event%="%js%" align="%align%"  style="%style%"/>';
$attr['img'] = str_replace("/","|",$attr['img']);


return str_replace(
	array('%font%','%size%','%color%','%bgcolor%','%text%','%id%','%class%','%event%','%js%','%style%'),
	array($attr['font'],$attr['size'],$attr['color'],$attr['bgcolor'],$attr['text'],$attr['id'],$attr['class'],$attr['event'],$attr['js'],$attr['style']),
	$str
);

}


?>