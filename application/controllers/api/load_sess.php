<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명: 로그인 없는 API
*****************************************************************
* 
*/
class load_sess extends CI_Controller {
	
	
	  
	 function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');
		
	}

	  /**
	   * 개인정보취급방침
	   *
	   * @return	return	$data
	   * @return	void
	   */	
	   function pra(){
		$this->load->database();
		$this->load->model('/admin/pra');
		$data = $this->pra->load_text();
		$data['no_tag_str_text1'] = str_replace('<p>','',$data['str_text']);

		//엔터 처리로 인하여 이와같이 처리함
		$data['no_tag_str_text1'] = str_replace('</p>','
		',$data['no_tag_str_text1']);
		$data['no_tag_str_text1'] = str_replace('<br>','
',$data['no_tag_str_text1']);

		$data['no_tag_str_text2'] = str_replace('<p>','',$data['str_text2']);
		$data['no_tag_str_text2'] = str_replace('</p>','
',$data['no_tag_str_text2']);
		$data['no_tag_str_text2'] = str_replace('<br>','
',$data['no_tag_str_text2']);
		
		

		$data['no_tag_str_text1'] = strip_tags($data['no_tag_str_text1']);
		$data['no_tag_str_text2'] = strip_tags($data['no_tag_str_text2']);
		$data['no_tag_str_text1'] = str_replace("&nbsp;"," ",$data['no_tag_str_text1']);
		$data['no_tag_str_text2'] = str_replace("&nbsp;"," ",$data['no_tag_str_text2']);
		echo json_encode($data);
		exit;
	  }






}

/* End of file load_no_sess.php */
/* Location: ./application/controllers/api/load_no_sess.php */