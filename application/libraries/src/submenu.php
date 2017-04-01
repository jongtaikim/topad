<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2012-02-18
* 작성자: 김종태
* 설   명: 메뉴구조
*****************************************************************
* 
*/

$DB = $CI->db;
$tpl = $CI->display;
$url_ = explode("/",uri_string());

$mcode = $param[code];
$cate = $param[cate];
if(!$cate) {
	$leftmenu = $CI->webapp->get(_VIEWS_TYPE,array('key'=>'leftmenu'));
	if(!$leftmenu)$cate = $_SESSION[ses_cate];
}
$_OID = _OID;
$_cate = substr($cate,0,2);


if($cate){
$tabidx = 1;
$sql = "SELECT * FROM ".TAB_MENU." WHERE num_oid=$_OID AND num_cate like '".$_cate."__' AND num_view=1 ORDER BY num_step asc";


	if($data = $DB->sqlFetchAll($sql)) {
		
		for($ii=0; $ii<count($data); $ii++) {


		$data[$ii]['str_title'] = htmlspecialchars($data[$ii]['str_title']);
		$module_name =$data[$ii]['str_type'];
		$data[$ii][str_link] = menuLink($module_name,$data[$ii][num_mcode],$data[$ii][num_cate]);
		
		$data[$ii]['tabindex'] = $tabidx;
		$tabidx++;
			

			$sql = "SELECT * FROM ".TAB_MENU."
					WHERE
						NUM_OID=$_OID AND
						num_cate LIKE '".$data[$ii]['num_cate']."__' AND
				num_view=1
					ORDER BY NUM_STEP";

			if($data[$ii]['MENUITEM'] = $DB->sqlFetchAll($sql)) {
				
				for($i=0; $i<count($data[$ii]['MENUITEM']); $i++) {

						$data[$ii]['MENUITEM'][$i]['str_title'] = htmlspecialchars($data[$ii]['MENUITEM'][$i]['str_title']);
						
						$data[$ii]['MENUITEM'][$i]['str_link'] = menuLink($data[$ii]['MENUITEM'][$i][str_type],$data[$ii]['MENUITEM'][$i][num_mcode],$data[$ii]['MENUITEM'][$i][num_cate]);
						$data[$ii]['MENUITEM'][$i]['tabindex'] = $tabidx;
						$tabidx++;
					
				}
				
			}
		}
	}
}else{
$data = $leftmenu;
}


$tpl->assign(array(
'SUBMENU'      => $data,
'cate__1'        => $cate,
'cate_2'        => substr($cate,0,$mlen-2),
'mcode'=> $mcode,
'cate'=> $cate,
));


function menuLink($module_name,$mcode,$cate){
		switch ($module_name) {
				case "board":
					$menu_url = "/user_page/board_list/".$mcode."?cate=".$cate;
				 break;
				case "link":
					$CI =& get_instance(); 
					$DB = $CI->db;
					$sql = "select * from ".TAB_CONTENT_URL." where num_oid = "._OID." and num_mcode = $mcode";
					$module_data = $DB -> sqlFetch($sql);
					$menu_url = $module_data[str_url];
				 break;
				case "ifr":
					$menu_url = "/user_page/ifr_view/".$mcode."?cate=".$cate;
				 break;
				 case "doc":
					$menu_url = "/user_page/doc_view/".$mcode."?cate=".$cate;
				 break;
				 case "menu":
					$CI =& get_instance(); 
					$DB = $CI->db;
					$sql = "select * from ".TAB_MENU." where num_oid = "._OID." and num_cate like '".$cate."__'";
					$module_data = $DB -> sqlFetch($sql);	
					
					$menu_url = $this->menuLink($module_data[str_type],$module_data[num_mcode],$module_data[num_cate]);

				 break;
			}
		
		return $menu_url;

}


/* End of file .php */
/* Location: ./application/controllers/.php */
