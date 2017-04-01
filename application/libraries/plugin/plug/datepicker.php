<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-22
* 작성자: 김종태
* 설   명: 달력출력
*****************************************************************
* 
*/
if (!$attr['theme']) $attr['theme'] = 'blue';
if (!$attr['format']) $attr['format'] = '%Y-%m-%d';

if (!$attr['lang']) {
	$attr['lang'] = "ko"; //'ko' or 'ko_KR.eucKR';
    if (strpos($attr['lang'],'_') !== false) $attr['lang'] = substr($attr['lang'],0,strpos($attr['lang'],'_'));
}

$ret = ''; 
if(!defined('WA_DATEPICKER_LOADED')) {
    $ret = '<script type="text/javascript" src="/util/jscalendar/calendar_stripped.js"></script>
<script type="text/javascript" src="/util/jscalendar/calendar-setup_stripped.js"></script>
<script type="text/javascript" src="/util/jscalendar/lang/calendar-'.$attr['lang'].'.js"></script>';
    define('WA_DATEPICKER_LOADED',true);
}
$ret .=  '<style type="text/css">@import url(/util/jscalendar/css/calendar-'.$attr['theme'].'.css);</style>
<input type="text" name="'.$attr['name'].'" id="'.$attr['name'].'"  pl="'.$attr['pl'].'" data-required="'.$attr['data-required'].'" class="'.$attr['class'].'" value="'.$attr['value'].'" '.($attr['readonly'] ? 'readonly ': '').' required="false"/><img src="/images/ico_calendar.gif" id="datepicker-'.$attr['name'].'"  style="cursor:pointer;margin: 4px 0 0 8px;">
<script type="text/javascript">
Calendar.setup({"inputField":"'.$attr['name'].'", "button":"datepicker-'.$attr['name'].'","ifFormat":"'.$attr['format'].'","weekNumbers":false,"singleClick":true});
</script>';
return $ret;
?>