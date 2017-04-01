<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: URL 클래스
* 작성자: 김종태
* 설   명:  2012-02-13
*****************************************************************
* 
*/

require_once "QueryString.php";

class WebAppURL extends QueryString
{
    var $base;

    function WebAppURL($str="") {
        $this->QueryString($str);
        if (defined(MODULE)) $this->vars['act'] = MODULE;
    }

    function getVar($alter="", $flag = true) {
        if ($flag) $alter = array_merge($this->vars,$alter);
        $buff = array();
        if (ereg('^(\.+)',$alter['act'],&$reg)) {
            $len = $i = strlen($reg[1]);
            $curr = MODULE;
            while ($i-- > 0) {
                $curr = substr($curr,0,strrpos($curr,'.'));
            }
            $alter['act'] = $curr.'.'.substr($alter['act'],$len);
        }

        if (defined('HUMAN_URI')) {
            $this->base = $alter['act'];
            unset($alter['act']);
        }
        foreach ($alter as $_key=>$_val) if ($_val !== '') $buff[] = "$_key=$_val";
        return $this->base . (($qs = implode("&",$buff)) ? "?$qs" : '');
    }
}


?>