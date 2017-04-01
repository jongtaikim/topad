<?
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명:  
*****************************************************************
* 
*/

class mdl_scrolling extends CI_Model {

    var $curlopt_referer = '';
    var $curlopt_header = '';

    function __construct()
    {
        parent::__construct();
	 
    }

    function _cut($s,$e,$text){
		$a = explode($s,$text);
		$b = explode($e,$a[1]);
		return $b[0];
			
	}

    function _cut2($s,$e,$text){
		$a = explode($s,$text);
		$b = explode($e,$a[2]);
		return $b[0];
			
	}
	


	function curl_url($url,$REQUEST_METHOD="GET",$proxy=''){
	   //한개만처리
	    $res = $this->fetch_multi_url(array($url),$REQUEST_METHOD,$proxy);
	    return $res[0];
	   
	}

	function curl_urls($url_list,$REQUEST_METHOD="GET",$proxy=''){
	   //동시 처리시킬 URL
	    $time = time();
	    $res = $this->fetch_multi_url($url_list,$REQUEST_METHOD);
	    if(count($res) <2){
		    return $res[0];
	    }else{
		    return $res;
	    }
	   
	}

	function fetch_multi_url( array $url_list, $REQUEST_METHOD='GET', $proxy='',$timeout=0 ) {
	   $mh = curl_multi_init();
		
	   $cookiefile = $this->makeCookieFile();

	    foreach ($url_list as $i => $url) {

		
		  $conn[$i] = curl_init($url);
		
		if($REQUEST_METHOD !="GET"){
			$post = "";
			list($url,$post) = explode("?",$url);
			
			curl_setopt($conn[$i], CURLOPT_POST	, true);
			curl_setopt($conn[$i], CURLOPT_POSTFIELDS,$post);
		}
		   
		   if($this->curlopt_referer){
			 curl_setopt($conn[$i],CURLOPT_REFERER,$this->curlopt_referer);
		   }
		   if($this->curlopt_header){
			 curl_setopt($conn[$i],CURLOPT_HTTPHEADER,$this->curlopt_header);
		   }

		      $headers = array();
//			$headers[] = 'X-Apple-Tz: 0';
//			$headers[] = 'X-Apple-Store-Front: 143444,12';

			$headers[] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
//			$headers[] = 'Accept-encoding:gzip, deflate, sdch';
			$headers[] = 'Accept-Language: ko-KR,ko;q=0.8,en-US;q=0.6,en;q=0.4';
			$headers[] = 'cache-control:max-age=0';
			$headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=utf-8';
//			$headers[] = 'Host: www.example.com';
//			$headers[] = 'Referer: http://www.example.com/index.php'; //Your referrer address
//			$headers[] = 'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0';
//			$headers[] = 'X-MicrosoftAjax: Delta=true';

			curl_setopt($conn[$i], CURLOPT_HTTPHEADER, $headers);


		  curl_setopt($conn[$i],CURLOPT_RETURNTRANSFER,1);
		  curl_setopt($conn[$i],CURLOPT_FAILONERROR,1);
		  curl_setopt($conn[$i],CURLOPT_FOLLOWLOCATION,1);
		  curl_setopt($conn[$i],CURLOPT_MAXREDIRS,3);
		  curl_setopt($conn[$i], CURLOPT_COOKIEJAR, $cookiefile);
		  curl_setopt($conn[$i], CURLOPT_COOKIEFILE, $cookiefile); 
		  curl_setopt($conn[$i], CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.93 Safari/537.36");
		 
		  if($proxy){
			curl_setopt($conn[$i], CURLOPT_PROXY, $proxy);
			curl_setopt($conn[$i], CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
		  }else{
		 
			  //SSL증명서 무시
			  curl_setopt($conn[$i],CURLOPT_SSL_VERIFYPEER,false);
			  curl_setopt($conn[$i],CURLOPT_SSL_VERIFYHOST,false);
		  }
		  
		  //타임아웃
		  if ($timeout){
			curl_setopt($conn[$i],CURLOPT_TIMEOUT,$timeout);
		  }
		 
		  curl_multi_add_handle($mh,$conn[$i]);
	    }
	   
	    $active = null;
	    do {
		  $mrc = curl_multi_exec($mh,$active);
	    } while ($mrc == CURLM_CALL_MULTI_PERFORM);
	   
	    while ($active and $mrc == CURLM_OK) {
		  if (curl_multi_select($mh) != -1) {
			do {
			    $mrc = curl_multi_exec($mh,$active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		  }
	    }
	   
	    if ($mrc != CURLM_OK) {
		  echo '읽기 에러가 발생:'.$mrc;
	    }
	   
	    //결과 취득
	    $res = array();
	    foreach ($url_list as $i => $url) {
		  if (($err = curl_error($conn[$i])) == '') {
			$res[$i] = curl_multi_getcontent($conn[$i]);
		  } else {
			echo '취득실패:'.$url_list[$i].'<br />';
		  }
		  curl_multi_remove_handle($mh,$conn[$i]);
		  curl_close($conn[$i]);
	    }
	    curl_multi_close($mh);
	   
	    return $res;
	}

	//디비저장
	function db_save($data){
		
		$datas[keyword] = $data[keyword];
		$datas[run_date] = date("Y-m-d");
		$datas[run_h] = date("H");
		$datas[html_text] = $data[res];
		
		
		$sql = "delete from keyword_run where keyword = '".$data[keyword]."' and run_date ='".$datas[run_date]."' and run_h ='".$datas[run_h]."'  ";
		$this->db->query($sql);
		$this->db->insertQuery($table="keyword_run",$datas);
		
		
	}

	//랭크값 디비저장
	function db_rank_save($rank='',$keyword='',$keyword_text='',$target_url=''){
		
		if($rank <11 && $rank >0){
			$datas[rank] = $rank;
			$datas[keyword] = $keyword;
			$datas[keyword_text] = $keyword_text;
			$datas[target_url] = $target_url;
			$datas[reg_date] = date("Y-m-d");
			$datas[reg_h] = date("H");
			
			
			$sql = "delete from keyword_rank where keyword = '".$datas[keyword]."' and reg_date ='".$datas[reg_date]."' and reg_h ='".$datas[reg_h]."' and target_url = '".$datas[target_url]."'  ";

			$this->db->query($sql);
			$this->db->insertQuery($table="keyword_rank",$datas);
		}
		
		
	}


	function makeCookieFile( $cookie_file_name = "test.cookie") {
		
		$cookiePath = "";
		/*********************
		 * cookie file
		*/
		// for window
		$cookiePath	= "./application/tmp/".$cookie_file_name.".cookie";
		
		if(!file_exists($cookiePath)) {
			$s = fopen($cookiePath, "w");
			fwrite($s, "");
			fclose($s);
		}
		return $cookiePath;
	}
	
	function _utf8($text,$charset='euc-kr'){
		return iconv($charset,"utf-8",$text);
	}
	
	function _euc($text,$charset='utf-8'){
		return iconv($charset,"euc-kr",$text);
	}
	
	function __RemoveEnterTab($in) {
	
		$out = str_replace("\r\n", "", $in);
		$out = str_replace("\r\t", "", $out);
	
		return $out;
	}
	
	function __FullEscape($in) {
	
		$out = str_replace("\r\n", "", $in);
		$out = str_replace("\r\t", "", $out);
	
		$out = urlencode($out);
	
		$out = str_replace('+','%20',$out);
		$out = str_replace('_','%5F',$out);
		$out = str_replace('.','%2E',$out);
		$out = str_replace('-','%2D',$out);
	
		return $out;
	}
	
    

	
}

?>