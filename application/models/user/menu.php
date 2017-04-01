<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-21
* 작성자: 김종태
* 설   명:  컨텐츠 메뉴 모델
*****************************************************************
* 
*/

class menu extends CI_Model {
   
   var $MENU_TABLE = "tab_menu";
   var $URL_LINK_TABLE =  "tab_content_url";

    function __construct()
    {
        parent::__construct();
	 $this->load->database();
    }

    /**
     * 사이트 메뉴 트리
     *
     * @return	object	$data
     * @return	void
     */	
    
    function load_tree(){
	
	$DB = $this->db;

	$sql = "select * from ".$this->MENU_TABLE." WHERE num_oid="._OID." AND LENGTH(num_cate)=2 ORDER BY num_step ";
	$row = $DB -> sqlFetchAll($sql);

	for($ii=0; $ii<count($row); $ii++) {
		
		
		$row[$ii][menu_url] = $this->type_menu_url($row[$ii][str_type],$row[$ii][num_mcode],$row[$ii][num_cate]);
		
		$sql = "SELECT  * FROM  ".$this->MENU_TABLE." ".
			"WHERE num_oid="._OID." AND num_cate LIKE '".$row[$ii][num_cate]."__'   ORDER BY num_step";
		$row[$ii][sub] = $DB -> sqlFetchAll($sql);
			
			for($i=0; $i<count($row[$ii][sub]); $i++) {
				
				$row[$ii][sub][$i][menu_url] = $this->type_menu_url($row[$ii][sub][$i][str_type],$row[$ii][sub][$i][num_mcode],$row[$ii][sub][$i][num_cate]);

				$sql = "SELECT  * FROM  ".$this->MENU_TABLE." ".
				"WHERE num_oid="._OID." AND num_cate LIKE '".$row[$ii][sub][$i][num_cate]."__'   ORDER BY num_step";
				$row[$ii][sub][$i][sub] = $DB -> sqlFetchAll($sql);

				for($ia=0; $ia<count($row[$ii][sub][$i][sub]); $ia++) {
					$row[$ii][sub][$i][sub][$ia][menu_url] = $this->type_menu_url($row[$ii][sub][$i][sub][$ia][str_type],$row[$ii][sub][$i][sub][$ia][num_mcode],$row[$ii][sub][$i][sub][$ia][num_cate]);

					$sql = "SELECT  * FROM  ".$this->MENU_TABLE." ".
					"WHERE num_oid="._OID." AND num_cate LIKE '".$row[$ii][sub][$i][sub][$ia][num_cate]."__'   ORDER BY num_step";
					$row[$ii][sub][$i][sub][$ia][sub] = $DB -> sqlFetchAll($sql);

					for($ib=0; $ib<count($row[$ii][sub][$i][sub][$ia][sub]); $ib++) {
						$row[$ii][sub][$i][sub][$ia][sub][$ib][menu_url] = $this->type_menu_url($row[$ii][sub][$i][sub][$ia][sub][$ib][str_type],$row[$ii][sub][$i][sub][$ia][sub][$ib][num_mcode],$row[$ii][sub][$i][sub][$ia][sub][$ib][num_cate]);
						
					}
					
				}
				
			
			}
			
	}
	
	return $row;
    }
    
    /**
     * 메뉴 데이터 가져오기
     *
     */	
    function load_menu_data($mcode){
		$tpl = $this->display;
		 $this->load->helper('file');
		$sql = "select * from ".$this->MENU_TABLE." where num_mcode = '".$mcode."' ";
		$data = $this->db-> sqlFetch($sql);
		$data[menu_url] = $this->type_menu_url($data[str_type],$mcode,$data[num_cate]);
		
		
		if(THEME=="mobile"){
			$data[str_layout] = "@sub";
			if($data[str_mobile_inc_file]){
			
			//이미지 일 경우 이미지 처리
			$file_ext = substr($data[str_mobile_inc_file],strlen($data[str_mobile_inc_file])-4,strlen($data[str_mobile_inc_file]));
			if($file_ext == ".jpg" || $file_ext == ".png" || $file_ext == ".gif"){
				
				$bodys = '<img src="'.$data[str_mobile_inc_file].'"/>';
				$tpl->assign(array('bodys'=>$bodys));
				
				$tpl->define('CONTENT', $this->display->getTemplate('user_page/blank.htm'));

			}else{

				if(strstr($data[str_mobile_inc_file],"?")){
					$a = explode("?",$data[str_mobile_inc_file]);
					$data[str_mobile_inc_file] = $a[0];
					
					//웹페이지의 GET값을 존중해주자..
					$doc_get_val = explode("&",$a[1]);
					//$_GET 포함시킴
					for($ii=0; $ii<count($doc_get_val); $ii++) {
						list($val,$value) = explode("=",$doc_get_val[$ii]);
						$_GET[$val]=$value;
						unset($val);
						unset($value);
					}
				}
				if($data[str_mobile_text]){
					$data[str_mobile_text] = stripslashes($data[str_mobile_text]);
					$tpl->assign(array('doc_mobile_text'=>$data[str_mobile_text]));
				}
			
				$tpl->define('CONTENT', $data[str_mobile_inc_file]);
			}
		}
		
		if($data[str_mobile_inc_file2]){
			

				if(strstr($data[str_mobile_inc_file2],"?")){
					$a = explode("?",$data[str_mobile_inc_file2]);
					$data[str_mobile_inc_file2] = $a[0];
					
					//웹페이지의 GET값을 존중해주자..
					$doc_get_val = explode("&",$a[1]);
					//$_GET 포함시킴
					for($ii=0; $ii<count($doc_get_val); $ii++) {
						list($val,$value) = explode("=",$doc_get_val[$ii]);
						$_GET[$val]=$value;
						unset($val);
						unset($value);
					}
				}
				if($data[str_mobile_text]){
					$data[str_mobile_text] = stripslashes($data[str_mobile_text]);
					$tpl->assign(array('doc_mobile_text'=>$data[str_mobile_text]));
				}
			
				$tpl->define('CONTENT2', $data[str_mobile_inc_file2]);
			
		}
		
		if(!$data[str_mobile_inc_file] && !$data[str_mobile_inc_file2]){

			if($data[str_mobile_text]){
				
				$data[str_mobile_text] = stripslashes($data[str_mobile_text]);
				$bodys = $data[str_mobile_text];
			}else{
				$bodys = 'NO PAGE';
			}


			$tpl->assign(array('bodys'=>$bodys));
			$tpl->define('CONTENT', $this->display->getTemplate('user_page/blank.htm'));
		}


		}else{
		if($data[str_inc_file]){
			
			//이미지 일 경우 이미지 처리
			$file_ext = substr($data[str_inc_file],strlen($data[str_inc_file])-4,strlen($data[str_inc_file]));
			if($file_ext == ".jpg" || $file_ext == ".png" || $file_ext == ".gif"){
				
				$bodys = '<img src="'.$data[str_inc_file].'"/>';
				$tpl->assign(array('bodys'=>$bodys));
				
				$tpl->define('CONTENT', $this->display->getTemplate('user_page/blank.htm'));

			}else{

				if(strstr($data[str_inc_file],"?")){
					$a = explode("?",$data[str_inc_file]);
					$data[str_inc_file] = $a[0];
					
					//웹페이지의 GET값을 존중해주자..
					$doc_get_val = explode("&",$a[1]);
					//$_GET 포함시킴
					for($ii=0; $ii<count($doc_get_val); $ii++) {
						list($val,$value) = explode("=",$doc_get_val[$ii]);
						$_GET[$val]=$value;
						unset($val);
						unset($value);
					}
				}
				if($data[str_text]){
					$data[str_text] = stripslashes($data[str_text]);
					$tpl->assign(array('doc_text'=>$data[str_text]));
				}
				
				$tpl->define('CONTENT', $data[str_inc_file]);
			}
		}else{
			if(is_file(_DOC_ROOT.'/data/docs/'.$mcode.".html")){
				$contents = read_file('data/docs/'.$mcode.".html");
				$data[str_text] = stripslashes($contents);
				$bodys = $data[str_text];
			 }else{
				if($data[str_text]){
					$data[str_text] = stripslashes($data[str_text]);
					$bodys = $data[str_text];
				}else{
					$bodys = 'NO PAGE';
				}
			 }
			
			$tpl->assign(array('bodys'=>$bodys));
			$tpl->define('CONTENT', $tpl->getTemplate('user_page/blank.htm'));
		}
			
		
			if($data[str_inc_file2]){
				//이미지 일 경우 이미지 처리
				$file_ext = substr($data[str_inc_file2],strlen($data[str_inc_file2])-4,strlen($data[str_inc_file2]));
				if($file_ext == ".jpg" || $file_ext == ".png" || $file_ext == ".gif"){
					
					$bodys = '<img src="'.$data[str_inc_file2].'"/>';
					$tpl->assign(array('bodys'=>$bodys));
					
					$tpl->define('CONTENT2', $this->display->getTemplate('user_page/blank.htm'));

				}else{
					if(strstr($data[str_inc_file2],"?")){
						$a = explode("?",$data[str_inc_file2]);
						$data[str_inc_file2] = $a[0];
						
						//웹페이지의 GET값을 존중해주자..
						$doc_get_val = explode("&",$a[1]);
						//$_GET 포함시킴
						for($ii=0; $ii<count($doc_get_val); $ii++) {
							list($val,$value) = explode("=",$doc_get_val[$ii]);
							$_GET[$val]=$value;
							unset($val);
							unset($value);
						}
						
							
					}
					
					$tpl->define('CONTENT2', $data[str_inc_file2]);
					
				}
			}


			if($data[str_inc_file3]){
				
				//이미지 일 경우 이미지 처리
				$file_ext = substr($data[str_inc_file3],strlen($data[str_inc_file3])-4,strlen($data[str_inc_file3]));
				if($file_ext == ".jpg" || $file_ext == ".png" || $file_ext == ".gif"){
					
					$bodys = '<img src="'.$data[str_inc_file3].'"/>';
					$tpl->assign(array('bodys'=>$bodys));
					
					$tpl->define('CONTENT3', $this->display->getTemplate('user_page/blank.htm'));

				}else{
					if(strstr($data[str_inc_file3],"?")){
						$a = explode("?",$data[str_inc_file3]);
						$data[str_inc_file3] = $a[0];
						
						//웹페이지의 GET값을 존중해주자..
						$doc_get_val = explode("&",$a[1]);
						//$_GET 포함시킴
						for($ii=0; $ii<count($doc_get_val); $ii++) {
							list($val,$value) = explode("=",$doc_get_val[$ii]);
							$_GET[$val]=$value;
							unset($val);
							unset($value);
						}
						
							
					}
					
					$tpl->define('CONTENT3', $data[str_inc_file3]);
				}
			}
			
		

			if($data[str_lnb_file]){
				
				if(strstr($data[str_lnb_file],"?")){
					$a = explode("?",$data[str_lnb_file]);
					$data[str_lnb_file] = $a[0];
					
					//웹페이지의 GET값을 존중해주자..
					$doc_get_val = explode("&",$a[1]);
					
					//$_GET 포함시킴
					for($ii=0; $ii<count($doc_get_val); $ii++) {
						list($val,$value) = explode("=",$doc_get_val[$ii]);
						$_GET[$val]=$value;
						unset($val);
						unset($value);
					}
						
				}
				

				$tpl->define('LNB', $data[str_lnb_file]);
				
			}

			
		}
		$tpl->assign($data);

		return $data;

    }

     /**
     * 하위 메뉴 데이터 가져오기
     *
     */	
    function load_menu_data_sub($cate){
			
		$sql = "select * from ".$this->MENU_TABLE." where num_cate LIKE '".$cate."__' ORDER BY num_step limit 1 ";
		$data_ = $this->db-> sqlFetch($sql);
		$data_[menu_url] = $this->type_menu_url($data_[str_type],$data_[num_mcode],$data_[num_cate]);

		return $data_;

    }
    
    /**
     * 사이트 타입에 따른 url
     *
     * @return	object	$data
     * @return	void
     */	
    
    function type_menu_url($module_name,$mcode,$cate){
		switch ($module_name) {
			case "board": $menu_ur = "/user/board/list_view/".$mcode."?cate=".$cate; break;
			case "link": 
				$sql = "select * from ".$this->URL_LINK_TABLE." where num_oid = "._OID." and num_mcode = $mcode";
				$module_data = $this->db-> sqlFetch($sql);
				//$module_data[menu_url] = $module_data[str_url];
				//$menu_ur = "/user/page/golink/".$mcode."?cate=".$cate; 
				$menu_ur = $module_data[str_url]; 

			break;
			case "ifr": $menu_ur = "/user/page/ifr_view/".$mcode."?cate=".$cate; break;
			case "doc": $menu_ur = "/user/page/doc/".$mcode."?cate=".$cate; break;
			case "menu": $menu_ur = "/user/page/blank_menu/".$mcode."?cate=".$cate; break;
			}
		
		
		return $menu_ur;
    }
	
	
}

?>