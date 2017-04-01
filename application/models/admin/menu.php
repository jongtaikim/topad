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
		

		$this->make_json($row,'site_menu.json');
		return $row;
    }

    /**
     * 사이트 메뉴 트리
     *
     * @return	object	$data
     * @return	void
     */	
    
    function load_board_tree(){
		$DB = $this->db;

		$sql = "select a.*, b.num_cate from tab_board_config a , tab_menu b where a.num_oid="._OID." and a.num_mcode = b.num_mcode";
		$row = $DB -> sqlFetchAll($sql);


		$this->make_json($row,'site_menu_board.json');
		return $row;
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
	
	/**
	 * 
	 * json 파일 캐시
	 * @return	void
	 */	
	
	function make_json($data,$json_file=''){
	    $file = _DOC_ROOT."/application/tmp/".$json_file;
	    if(!($fp = fopen($file, "w"))) return 0;
	    if(fwrite($fp,json_encode($data)) === FALSE)
	    {
		  fclose($fp);
		  return 0;
	    }
	    fclose($fp);
	    return 1;
	}
     

	
}

?>