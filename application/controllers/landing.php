<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2016-01-22
* 작성자: 김종태
* 설   명: 렌딩페이지
*****************************************************************
*
*/
class landing extends CI_Controller {



	 var $SUB_LAYOUT =  "@sub";
	 var $site_menu="";
	 var $PERM="";

	 function __construct(){
		parent::__construct();
		$this->load->library('session');
		$this->load->helper('cookie');
		$_SESSION = $this->session->all_userdata();
		$tpl = $this->display;

	}



	  //2012-02-21 메인페이지
	   function index($css_number=''){
		$WA = $this->webapp;
		$tpl = $this->display;
		$this->db =  $this->db;

		$this->load->library('iniconf');
		$this->load->helper('file');


		$site_data = @parse_ini_file(_DOC_ROOT.'/application/config/'.THEME.'/site_config.php',true);

		$tpl->assign(array(
			'popup_start'=>$site_data[popup_start],
			'popup_end'=>$site_data[popup_end],
			'popup_use'=>$site_data[popup_use],
		 ));


		if($site_data[popup_start] <= date("Y-m-d") && $site_data[popup_end] >= date("Y-m-d")){

			 if(is_file(_DOC_ROOT.'/data/docs/popup.html')){
				$contents = read_file(_DOC_ROOT.'/data/docs/popup.html');
			 }else{
				$contents ="";
			 }
			$sect = "popup/popup";
			$tpl->assign(array(
				'popup_contents'=>$contents,
				'sect'=>$sect,
			 ));
		}
		
		//랜덤
		//if(!$css_number) $css_number = mt_rand(1, 15);

		
		//렌딩 페이지 스타일
		switch ($css_number) {
			case "1": $css_style = "dofollow-businessgrey.css";break;
			case "2": $css_style = "dofollow-citynightred.css";break;
			case "3": $css_style = "dofollow-coffeebrown.css";break;
			case "4": $css_style = "dofollow-constructionyellow.css";break;
			//case "5": $css_style = "dofollow-deepoceanorange.css";break;
			case "6": $css_style = "dofollow-elegantblack.css";break;
			case "7": $css_style = "dofollow-muffinblue.css";break;
			case "8": $css_style = "dofollow-purplesensation.css";break;
			case "9": $css_style = "dofollow-red.css";break;
			case "10": $css_style = "dofollow-seogreen.css";break;
			case "11": $css_style = "dofollow-startupblue.css";break;
			//case "12": $css_style = "dofollow-sunsetorange.css";break;
			case "13": $css_style = "dofollow-techrainbow.css";break;
			//case "14": $css_style = "dofollow-valentinepink.css";break;
			case "15": $css_style = "dofollow-vintagered.css";break;
		}

		if(!$css_style) $css_style = "dofollow-techrainbow.css";
		
	
		$tpl->assign(array('css_style'=>$css_style));
		
		

		$tpl->setLayout('@no_userpage');
		$tpl->define('CONTENT', $this->display->getTemplate('index.html'));
		$tpl->printAll();

	  }


	/**
	 *
	 * 404 처리
	 * @return	object	$mcode
	 * @return	void
	 */

	function go404(){
		if(ENVIRONMENT == "development"){
			show_404('page');
		}else{
			echo "<meta http-equiv='Refresh' Content=\"0; URL='/'\">";
			exit;
		}
	}

	
	function mail_test(){
	
		
		$this->load->library('email');
		$this->email->from('now17@kutest.co.kr','잇컴퍼니');
		$this->email->to("jongtainow17@gmail.com",'김종태'); 
		//$this->email->to("now17@nate.com",'김종태'); 
		//$this->email->to("root@localhost",'김종태'); 
		$this->email->subject('test mail입니다.');
		
		$msg = "test mail send ";
		$this->email->message($msg);
		if($this->email->send()){
			echo "발송완료";
		}else{
			echo "발송실패";
		}


	}






}

/* End of file sub.php */
/* Location: ./application/controllers/sub.php */
