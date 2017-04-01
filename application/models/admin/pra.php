<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-17
* 작성자: 김종태
* 설   명:  사이트 이용약관 모델
*****************************************************************
* 
*/

class pra extends CI_Model {

   var $ORGAN_TABLE =  "tab_organ"; 
 
    function __construct()
    {
        parent::__construct();

		
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


    /**
     * 약관 내용 불러오기
     *
     * @return	object	$data
     * @return	void
     */	
    
    function load_text(){
		$sql = "select str_text,str_text2 from ".$this->ORGAN_TABLE." where num_oid = '"._OID."' ";
		$data = $this->db -> sqlFetch($sql);

		$datas[str_text] = stripslashes($data[str_text]);
		$datas[str_text2] = stripslashes($data[str_text2]);
		return $datas;
    }

     /**
     * 약관 내용 저장
     *
     * @return	object	$_POST_DATA = $_POST
     * @return	void
     */	
    
    function save_text($_POST_DATA){
		$this->webapp->daumEditUpload($_POST_DATA[attach_img_file],"image");
		$this->webapp->daumEditUpload($_POST_DATA[attach_file],"file");
	
		if($_POST_DATA[str_text]) $datas[str_text] = $_POST_DATA[str_text];
		if($_POST_DATA[str_text2])$datas[str_text2] = $_POST_DATA[str_text2];

		if($_POST_DATA[str_text]) $datas[str_text] = addslashes($datas[str_text]);
		if($_POST_DATA[str_text2]) $datas[str_text2] = addslashes($datas[str_text2]);
		
		$this->db->updateQuery($this->ORGAN_TABLE,$datas," num_oid = '"._OID."'");
		
		
	

		
		$this->load->model('/admin/pra');
		$data = $this->pra->load_text();
		$data['no_tag_str_text1'] = str_replace('	','',$data['str_text']);
		$data['no_tag_str_text1'] = str_replace('<p>','',$data['str_text']);

		//엔터 처리로 인하여 이와같이 처리함
		$data['no_tag_str_text1'] = str_replace('</p>',PHP_EOL,$data['no_tag_str_text1']);
		$data['no_tag_str_text1'] = str_replace('<br>',PHP_EOL,$data['no_tag_str_text1']);

		$data['no_tag_str_text2'] = str_replace('	','',$data['str_text2']);
		$data['no_tag_str_text2'] = str_replace('<p>','',$data['str_text2']);
		$data['no_tag_str_text2'] = str_replace('</p>',PHP_EOL,$data['no_tag_str_text2']);
		$data['no_tag_str_text2'] = str_replace('<br>',PHP_EOL,$data['no_tag_str_text2']);
		
		

		$data['no_tag_str_text1'] = strip_tags($data['no_tag_str_text1']);
		$data['no_tag_str_text2'] = strip_tags($data['no_tag_str_text2']);
		$data['no_tag_str_text1'] = str_replace("&nbsp;"," ",$data['no_tag_str_text1']);
		$data['no_tag_str_text2'] = str_replace("&nbsp;"," ",$data['no_tag_str_text2']);
		$this->make_json($data,'pra.json');
	

		return 0;

    }

	
}

?>