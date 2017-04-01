<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-22
* 작성자: 김종태
* 설   명: 스크롤링 테스트
*****************************************************************
* 
*/
class scrolling extends CI_Controller {
	
	  
	 function __construct(){
		parent::__construct();
		$tpl = $this->display;
		$this->load->model('test/mdl_scrolling');
		 $this->load->database();
	}
	
	


	  function p_curl($idx='6'){
		

		$url = "http://hospi.co.kr/product_".$idx.".php";

		$res = $this->mdl_scrolling->curl_url($url);
	
		$tag_array = explode('<table width="622" border="0" cellpadding="0" cellspacing="2" bgcolor="c6e4ff">',$res);
		
		$i = 0;
		for($ii=1; $ii<count($tag_array); $ii++) {
			$row[$i][img] = $this->mdl_scrolling->_cut('<td width="30%" valign="top" bgcolor="#FFFFFF" style="padding:5 7 5 7;">','</td>',$tag_array[$ii]);

			$row[$i][title] = $this->mdl_scrolling->_cut('<td bgcolor="#FFFFFF" style="padding:3 0 3 5;">','</td>',$tag_array[$ii]);
			$row[$i][code] = $this->mdl_scrolling->_cut('<td align="right" bgcolor="#FFFFFF" style="padding:3 5 3 0;">','</td>',$tag_array[$ii]);

			$row[$i][text] = $this->mdl_scrolling->_cut('<img src="img/pixel.gif" width="10" height="10"><br>','</',$tag_array[$ii]);

			$row[$i][text2] = $this->mdl_scrolling->_cut2('<img src="img/pixel.gif" width="10" height="10"><br>','</table></td>',$tag_array[$ii]);
			

			$row[$i][title] = addslashes(strip_tags($this->mdl_scrolling->_utf8($row[$i][title])));
			$row[$i][code] = strip_tags($this->mdl_scrolling->_utf8($row[$i][code]));
			$row[$i][text] = trim($this->mdl_scrolling->_utf8($row[$i][text]));
			$row[$i][text2] = $this->mdl_scrolling->_utf8($row[$i][text2]);

			$row[$i][text2] = str_replace('<img src="img/pixel.gif" width="10" height="10">','',$row[$i][text2]);
			
			
			


			$row[$i][text] = addslashes($row[$i][text].$row[$i][text2]);
			unset($row[$i][text2]);

			if(strstr($row[$i][text],"<table")){
				$row[$i][text] = str_replace('<table','<table class="table table-bordered " ',$row[$i][text])."</table>";
				
			}

			$row[$i][img] = str_replace('"img/','"http://hospi.co.kr/img/',$row[$i][img]);
			$row[$i][img] = str_replace('<img src="','',$row[$i][img]);
			$row[$i][img] = str_replace('">','',$row[$i][img]);

			$tmp_img = $this->mdl_scrolling->curl_url($row[$i][img]);
			$filename = md5(array_pop(explode("/",$row[$i][img]))).".gif";
				
			$s = fopen('data/docs/product_img/'.$filename, "w");
			fwrite($s, $tmp_img);
			fclose($s);

			$row[$i][img] = "/data/product_img/".$filename;
			$row[$i][product_type] = "12";
			
			$this->db->insertQuery($table="hospi_product",$row[$i]);
			
			

			

			$i++;
		}
		
		print_r($row);


	  }


	    function p_curl2($idx='6'){
		

		$url = "http://hospi.co.kr/product_".$idx.".php";

		$res = $this->mdl_scrolling->curl_url($url);
	
		$tag_array = explode('<table width="622" border="0" cellpadding="0" cellspacing="2" bgcolor="c6e4ff">',$res);
		
		$i = 0;
		for($ii=1; $ii<count($tag_array); $ii++) {
			$row[$i][img] = $this->mdl_scrolling->_cut('<td width="30%" valign="top" bgcolor="#FFFFFF" style="padding:5 7 5 7;">','</td>',$tag_array[$ii]);

			$row[$i][title] = $this->mdl_scrolling->_cut(' <td width="427" bgcolor="#FFFFFF" style="padding:3 0 3 5;">','</td>',$tag_array[$ii]);
			$row[$i][code] = $this->mdl_scrolling->_cut('<td width="195" align="right" bgcolor="#FFFFFF" style="padding:3 5 3 0;">','</td>',$tag_array[$ii]);

			$row[$i][text] = $this->mdl_scrolling->_cut('<img src="img/pixel.gif" width="10" height="10"><br>','</td>',$tag_array[$ii]);

			$row[$i][text2] = $this->mdl_scrolling->_cut2('<img src="img/pixel.gif" width="10" height="10"><br>','</tbody></table>',$tag_array[$ii]);
			

			$row[$i][title] = addslashes(strip_tags($this->mdl_scrolling->_utf8($row[$i][title])));
			$row[$i][code] = strip_tags($this->mdl_scrolling->_utf8($row[$i][code]));
			$row[$i][text] = trim($this->mdl_scrolling->_utf8($row[$i][text]));
			$row[$i][text2] = $this->mdl_scrolling->_utf8($row[$i][text2]);

			$row[$i][text2] = str_replace('<img src="img/pixel.gif" width="10" height="10">','',$row[$i][text2]);
			
			
			


			$row[$i][text] = addslashes($row[$i][text].$row[$i][text2]);
			unset($row[$i][text2]);

			if(strstr($row[$i][text],"<table")){
				$row[$i][text] = str_replace('<table','<table class="table table-bordered " ',$row[$i][text])."</table>";
				
			}

			$row[$i][img] = str_replace('"img/','"http://hospi.co.kr/img/',$row[$i][img]);
			$row[$i][img] = str_replace('<img src="','',$row[$i][img]);
			$row[$i][img] = str_replace('">','',$row[$i][img]);

			$tmp_img = $this->mdl_scrolling->curl_url($row[$i][img]);
			$filename = md5(array_pop(explode("/",$row[$i][img]))).".gif";
				
			$s = fopen('data/docs/product_img/'.$filename, "w");
			fwrite($s, $tmp_img);
			fclose($s);

			$row[$i][img] = "/data/product_img/".$filename;
			$row[$i][product_type] = "12";
			
			$this->db->insertQuery($table="hospi_product",$row[$i]);
			
			

			

			$i++;
		}
		
		print_r($row);


	  }

	   function p_curl3($idx='9'){
		

		$url = "http://hospi.co.kr/product_".$idx.".php";

		$res = $this->mdl_scrolling->curl_url($url);
	
		$tag_array = explode('<table width="622" border="0" cellpadding="0" cellspacing="2" bgcolor="c6e4ff">',$res);
		
		$i = 0;
		for($ii=1; $ii<count($tag_array); $ii++) {
			$row[$i][img] = $this->mdl_scrolling->_cut('<td width="30%" valign="top" bgcolor="#FFFFFF" style="padding:5 7 5 7;">','</td>',$tag_array[$ii]);

			$row[$i][title] = $this->mdl_scrolling->_cut('<td bgcolor="#FFFFFF" style="padding:3 0 3 5;">','</td>',$tag_array[$ii]);
			$row[$i][code] = $this->mdl_scrolling->_cut('<td align="right" bgcolor="#FFFFFF" style="padding:3 5 3 0;">','</td>',$tag_array[$ii]);

			$row[$i][text] = $this->mdl_scrolling->_cut('<img src="img/pixel.gif" width="10" height="10"><br>','</td>',$tag_array[$ii]);

			$row[$i][text2] = $this->mdl_scrolling->_cut2('<img src="img/pixel.gif" width="10" height="10"><br>','</tbody></table>',$tag_array[$ii]);
			

			$row[$i][title] = addslashes(strip_tags($this->mdl_scrolling->_utf8($row[$i][title])));
			$row[$i][code] = strip_tags($this->mdl_scrolling->_utf8($row[$i][code]));
			$row[$i][text] = trim($this->mdl_scrolling->_utf8($row[$i][text]));
			$row[$i][text2] = $this->mdl_scrolling->_utf8($row[$i][text2]);

			$row[$i][text2] = str_replace('<img src="img/pixel.gif" width="10" height="10">','',$row[$i][text2]);
			
			
			


			$row[$i][text] = addslashes($row[$i][text].$row[$i][text2]);
			unset($row[$i][text2]);

			if(strstr($row[$i][text],"<table")){
				$row[$i][text] = str_replace('<table','<table class="table table-bordered " ',$row[$i][text])."</table>";
				
			}

			$row[$i][img] = str_replace('"img/','"http://hospi.co.kr/img/',$row[$i][img]);
			$row[$i][img] = str_replace('<img src="','',$row[$i][img]);
			$row[$i][img] = str_replace('">','',$row[$i][img]);

			$tmp_img = $this->mdl_scrolling->curl_url($row[$i][img]);
			$filename = md5(array_pop(explode("/",$row[$i][img]))).".gif";
				
			$s = fopen('data/docs/product_img/'.$filename, "w");
			fwrite($s, $tmp_img);
			fclose($s);

			$row[$i][img] = "/data/product_img/".$filename;
			$row[$i][product_type] = "9";
			
			$this->db->insertQuery($table="hospi_product",$row[$i]);
			
			

			

			$i++;
		}
		
		print_r($row);


	  }
	  

	    function p_curl4($idx='10'){
		

		$url = "http://hospi.co.kr/product_".$idx.".php";

		$res = $this->mdl_scrolling->curl_url($url);
	
		$tag_array = explode('<table width="622" border="0" cellpadding="0" cellspacing="2" bgcolor="c6e4ff">',$res);
		
		$i = 0;
		for($ii=1; $ii<count($tag_array); $ii++) {
			$row[$i][img] = $this->mdl_scrolling->_cut('<td width="30%" valign="top" bgcolor="#FFFFFF" style="padding:5 7 5 7;">','</td>',$tag_array[$ii]);

			$row[$i][title] = $this->mdl_scrolling->_cut('<td bgcolor="#FFFFFF" style="padding:3 0 3 5;">','</td>',$tag_array[$ii]);
			$row[$i][code] = $this->mdl_scrolling->_cut('<td align="right" bgcolor="#FFFFFF" style="padding:3 5 3 0;">','</td>',$tag_array[$ii]);

			$row[$i][text] = $this->mdl_scrolling->_cut('<img src="img/pixel.gif" width="10" height="10"><br>','</td>',$tag_array[$ii]);

			$row[$i][text2] = $this->mdl_scrolling->_cut2('<img src="img/pixel.gif" width="10" height="10"><br>','</tbody></table>',$tag_array[$ii]);
			

			$row[$i][title] = addslashes(strip_tags($this->mdl_scrolling->_utf8($row[$i][title])));
			$row[$i][code] = strip_tags($this->mdl_scrolling->_utf8($row[$i][code]));
			$row[$i][text] = trim($this->mdl_scrolling->_utf8($row[$i][text]));
			$row[$i][text2] = $this->mdl_scrolling->_utf8($row[$i][text2]);

			$row[$i][text2] = str_replace('<img src="img/pixel.gif" width="10" height="10">','',$row[$i][text2]);
			
			
			


			$row[$i][text] = addslashes($row[$i][text].$row[$i][text2]);
			unset($row[$i][text2]);

			if(strstr($row[$i][text],"<table")){
				$row[$i][text] = str_replace('<table','<table class="table table-bordered " ',$row[$i][text])."</table>";
				
			}

			$row[$i][img] = str_replace('"img/','"http://hospi.co.kr/img/',$row[$i][img]);
			$row[$i][img] = str_replace('<img src="','',$row[$i][img]);
			$row[$i][img] = str_replace('">','',$row[$i][img]);

			$tmp_img = $this->mdl_scrolling->curl_url($row[$i][img]);
			$filename = md5(array_pop(explode("/",$row[$i][img]))).".gif";
				
			$s = fopen('data/docs/product_img/'.$filename, "w");
			fwrite($s, $tmp_img);
			fclose($s);

			$row[$i][img] = "/data/product_img/".$filename;
			$row[$i][product_type] = "10";
			
			$this->db->insertQuery($table="hospi_product",$row[$i]);
			
			

			

			$i++;
		}
		
		print_r($row);


	  }


	  
	    function p_curl5($idx='10'){
		

		$url = "http://hospi.co.kr/product_".$idx.".php";

		$res = $this->mdl_scrolling->curl_url($url);
	
		$tag_array = explode('<table width="622" border="0" cellpadding="0" cellspacing="2" bgcolor="c6e4ff">',$res);
		
		$i = 0;
		for($ii=1; $ii<count($tag_array); $ii++) {
			$row[$i][img] = $this->mdl_scrolling->_cut('<td width="30%" valign="top" bgcolor="#FFFFFF" style="padding:5 7 5 7;">','</td>',$tag_array[$ii]);

			$row[$i][title] = $this->mdl_scrolling->_cut('<td bgcolor="#FFFFFF" style="padding:3 0 3 5;">','</td>',$tag_array[$ii]);
			$row[$i][code] = $this->mdl_scrolling->_cut('<td align="right" bgcolor="#FFFFFF" style="padding:3 5 3 0;">','</td>',$tag_array[$ii]);

			$row[$i][text] = $this->mdl_scrolling->_cut('<img src="img/pixel.gif" width="10" height="10"><br>','</td>',$tag_array[$ii]);

			$row[$i][text2] = $this->mdl_scrolling->_cut2('<img src="img/pixel.gif" width="10" height="10"><br>','</tbody></table>',$tag_array[$ii]);
			

			$row[$i][title] = addslashes(strip_tags($this->mdl_scrolling->_utf8($row[$i][title])));
			$row[$i][code] = strip_tags($this->mdl_scrolling->_utf8($row[$i][code]));
			$row[$i][text] = trim($this->mdl_scrolling->_utf8($row[$i][text]));
			$row[$i][text2] = $this->mdl_scrolling->_utf8($row[$i][text2]);

			$row[$i][text2] = str_replace('<img src="img/pixel.gif" width="10" height="10">','',$row[$i][text2]);
			
			
			


			$row[$i][text] = addslashes($row[$i][text].$row[$i][text2]);
			unset($row[$i][text2]);

			if(strstr($row[$i][text],"<table")){
				$row[$i][text] = str_replace('<table','<table class="table table-bordered " ',$row[$i][text])."</table>";
				
			}

			$row[$i][img] = str_replace('"img/','"http://hospi.co.kr/img/',$row[$i][img]);
			$row[$i][img] = str_replace('<img src="','',$row[$i][img]);
			$row[$i][img] = str_replace('">','',$row[$i][img]);

			$tmp_img = $this->mdl_scrolling->curl_url($row[$i][img]);
			$filename = md5(array_pop(explode("/",$row[$i][img]))).".gif";
				
			$s = fopen('data/docs/product_img/'.$filename, "w");
			fwrite($s, $tmp_img);
			fclose($s);

			$row[$i][img] = "/data/product_img/".$filename;
			$row[$i][product_type] = "11";
			
			$this->db->insertQuery($table="hospi_product",$row[$i]);
			
			

			

			$i++;
		}
		
		print_r($row);


	  }
	  




}

/* End of file sub.php */
/* Location: ./application/controllers/test/page.php */