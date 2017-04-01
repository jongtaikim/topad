<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class yellowid extends CI_Controller {

	public function __construct() {
		parent::__construct();

	}

	public function keyboard()
	{
		$json['keyboard']['type'] = "buttons"; 
		$json['keyboard']['buttons'] = array("오늘의 특가","베스트 상품","앱으로 구매하면 적립금이 2배!","주문하기","배송조회/문의","교환/환불 문의"); 
		
		echo json_encode($json['keyboard']);
		exit;
		
	}
	
	/*
	curl -XPOST 'http://m.ez777.firstmall.kr/yellowid/message' -d '{"user_key": "encryptedUserKey", "type": "text", "content": "선택2"}'
	*/
	
	public function message()
	{
		
		//메세지
		$keyword = $_REQUEST['content'];
		
		switch ($_REQUEST['type']) {
		case "text":

			switch ($keyword) {
			 case "오늘의 특가": 
				$json['message']['text'] = "태그안됨!! <img src='https://mud-kage.kakao.com/dn//yu19s/btqcMe7eYPI/45i1zOQfCQL20tkDnGw730/img.jpg'>"; 
				
				$json['message']['photo']['url'] = "https://mud-kage.kakao.com/dn//yu19s/btqcMe7eYPI/45i1zOQfCQL20tkDnGw730/img.jpg"; 
				$json['message']['photo']['width'] = 300; 
				$json['message']['photo']['height'] = 184; 

				$json['message']['photo']['url'] = "https://ezshopping.co.kr:40002/upfiles/banner/1831554659.jpg"; 
				$json['message']['photo']['width'] = 300; 
				$json['message']['photo']['height'] = 184; 

				$json['message']['message_button']['label'] = "자세히보기"; 
				$json['message']['message_button']['url'] = "https://plus-talk.kakao.com/ plus/mini/page/preview/121895.html"; 
				
				$json['keyboard']['type'] = "buttons"; 
				$json['keyboard']['buttons'] = array("뒤로가기","상품1","상품2","뒤로가기","상품3","상품4"); 
			break;
			case "뒤로가기": 

				$json['message']['text'] = "안녕하세요? 이지홈쇼핑입니다.\n원하시는 메뉴를 선택하세요."; 
				$json['keyboard']['type'] = "buttons"; 
				$json['keyboard']['buttons'] =  array("오늘의 특가","베스트 상품","앱으로 구매하면 적립금이 2배!","주문하기","배송조회/문의","교환/환불 문의"); 


			break;
			case "처음으로": 
				$json['type'] = "buttons"; 
				$json['buttons'] = array("오늘의 특가","베스트 상품","앱으로 구매하면 적립금이 2배!","주문하기","배송조회/문의","교환/환불 문의"); 
			break;
			case "상품1": 
				$json['message']['text'] = "거시기"; 
				$json['message']['photo']['url'] = "https://mud-kage.kakao.com/dn//yu19s/btqcMe7eYPI/45i1zOQfCQL20tkDnGw730/img.jpg"; 
				$json['message']['photo']['width'] = 300; 
				$json['message']['photo']['height'] = 184; 

				$json['message']['message_button']['label'] = "자세히보기"; 
				$json['message']['message_button']['url'] = "https://plus-talk.kakao.com/ plus/mini/page/preview/121895.html"; 
				
				$json['keyboard']['type'] = "buttons"; 
				$json['keyboard']['buttons'] = array("뒤로가기","상품1","상품2","뒤로가기","상품3","상품4"); 
			break;
			}
		 
		break;
		case "photo":break;
		case "video":break;
		case "audio":break;

		}
		
		echo json_encode($json);
		exit;
		
	}


}

/* End of file brand.php */
/* Location: ./app/controllers/admin/android_push.php */
