<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명: 로그인 없는 API
*****************************************************************
* 
*/
class load_no_sess extends CI_Controller {
	
	
	  
	 function __construct(){
		parent::__construct();
		 header('Content-Type: text/html; charset=UTF-8');
	}

	  /**
	   * 수술항목 api
	   *
	   * @return	return	$data
	   * @return	void
	   */	
	   function data_etc_1(){
		 $file = _DOC_ROOT."/application/tmp/etc_1.json";
		 if($fp = fopen($file, 'rb')){ 
		   $buffer = fread($fp, filesize($file));
		   fclose($fp);
	         echo $buffer;
		 } 
	  }

	   function data_etc_1_code(){
		 $file = _DOC_ROOT."/application/tmp/etc_1_code.json";
		 if($fp = fopen($file, 'rb')){ 
		   $buffer = fread($fp, filesize($file));
		   fclose($fp);
	         echo $buffer;
		 } 
	  }

	    /**
	   * 이용정책
	   *
	   * @return	return	$data
	   * @return	void
	   */	
	   function pra(){
		 $file = _DOC_ROOT."/application/tmp/pra.json";
		 if($fp = fopen($file, 'rb')){ 
		   $buffer = fread($fp, filesize($file));
		   fclose($fp);
	         echo $buffer;
		 } 
	  }

	





}

/* End of file load_no_sess.php */
/* Location: ./application/controllers/api/load_no_sess.php */