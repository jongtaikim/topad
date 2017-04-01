<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-14
* 작성자: 김종태
* 설  명:  페이징 테그
*****************************************************************
* 
*/
if(!$attr[target]) $attr[target] = '_self';
if(!$attr[submit]) $attr[submit] = 'a';
if(!$attr[size]) $attr[size] = '2';
if(!$attr['class']) $attr['class'] = ' btn btn-default btn-xs ';
if($attr[style]) $styles = 'style="'.$attr[style].'"';

if($attr[icon]) {
	$iconView = "icon";
	$iconBdy = '<span class="'.$attr[icon].'"></span>';
}


switch ($attr[size]) {
	case "1":
		$size = "small";
	break;
	case "2":
		$size = "medium";
	break;
	case "3":
		$size = "large";
	break;
	case "4":
		$size = "xlarge";
	break;
}



switch ($attr[type]) {
	case "submit":
		$btn_body = '<input type="submit" value="'.$attr[value].'"  id="'.$attr[id].'" class="'.$attr['class'].' "  '.$attr[event].'="'.$attr[js].'">';
	break;
	case "a":
		$btn_body = '<a href="'.$attr[href].'" target="'.$attr[target].'"  id="'.$attr[id].'" class="'.$attr['class'].' "  '.$attr[event].'="'.$attr[js].'">'.$attr[value].'</a>';
	break;
	case "button":
		$btn_body = '<input type="button" value="'.$attr[value].'"   id="'.$attr[id].'" class="'.$attr['class'].' "  '.$attr[event].'="'.$attr[js].'">';
	break;
}


$ret = '<span class="btn_pack '.$size.' '.$iconView.'" '.$styles.'>'.$iconBdy.$btn_body.'</span>';
return $ret;
?>