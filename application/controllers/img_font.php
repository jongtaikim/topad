<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 
* 작성자: 김종태
* 설   명: 
*****************************************************************
* 
*/
class img_font extends CI_Controller {
		

	  	function __construct()
		{
			parent::__construct();
		}

	  //http://www.deesse.co.kr/img_font/make/RixMGoB/20/993399/FFFFFF/테스트

	  function make($font='',$size2='',$color='',$bg='',$text=''){
		$text = urldecode($text);
		if($_GET[text])  $text = $_GET[text];
		
		$tmp_file = "./application/tmp/".md5($text."|".$font."|".$size2."|".$bg."|".$color).".gif";

		$mk_btn = "Y";
		if(!is_file($tmp_file)) {

		if(!$font) $font_file='./trunk/fonts/ygo330.ttf'; else $font_file= "./trunk/fonts/".$font.'.ttf';

		if(!$text) $text = ' ';


		$text_array =  $this->text_array($text);

			for($ii=0; $ii<count($text_array); $ii++) {
				if(ord($text_array[$ii]) > 127) { //한글
				
				
				$ww_r[$ii] = 38;
				
				
				$han = "Y";
				if($han_count) $han_count = 0; else $han_count++;
				}else if(ord($text_array[$ii]) < 57) { //숫자
				
				$fontInfo = imagettfbbox(30, 0, $font_file, $text_array[$ii]);
				if($text_array[$ii] == "1") {
					$ww_r[$ii] = abs($fontInfo[2] - $fontInfo[0]) + 9 ;
				}else{
					$ww_r[$ii] = abs($fontInfo[2] - $fontInfo[0]) + 4 ;
				}
				$nun = "Y";
				if($nun_count) $nun_count = 0; else $nun_count++;
				}else{ //영어
				
					$fontInfo = imagettfbbox(30, 0, $font_file, $text_array[$ii]);
					$ww_r[$ii] = abs($fontInfo[2] - $fontInfo[0]) + 5 ;
					$eng = "Y";
					if($eng_count) $eng_count = 0; else $eng_count++;
				
				}
			
			}
			$ww = array_sum($ww_r)  ;
			$hh = 45;

			

		$bcolor_array = $this->content_split($bg, 2); 
		$bbcolor_array[0] = hexdec("0x{$bcolor_array[0]}");
		$bbcolor_array[1] = hexdec("0x{$bcolor_array[1]}");
		$bbcolor_array[2] = hexdec("0x{$bcolor_array[2]}");

		$fcolor_array = $this->content_split($color, 2); 
		$ffcolor_array[0] = hexdec("0x{$fcolor_array[0]}");
		$ffcolor_array[1] = hexdec("0x{$fcolor_array[1]}");
		$ffcolor_array[2] = hexdec("0x{$fcolor_array[2]}");

		$len_width = count($text_array);
		if(!$bg) $bg = "FFFFFF";
		if(!$color) $color = "E75211";
		if(!$size) $size = "30";
		if(!$x) $x = "1";
		if(!$y) $y = $size+4;
		if(!$w) $w = ($size * $len_width)  ;
		if(!$h) $h = $hh_r;


		$im=imagecreate($ww,$hh);
		$back=imagecolorallocate($im,$bbcolor_array[0],$bbcolor_array[1],$bbcolor_array[2]);



		$pen_color= imagecolorallocate($im,$ffcolor_array[0],$ffcolor_array[1],$ffcolor_array[2]);

		imagettftext($im,$size,0,$x,$y,$pen_color,$font_file, $text);





		$width = $size2 * strlen($text) - 5;
		$height = $size2*2.5;	

		if(!$han && $eng && !$nun){
		$width =  $width + 4;
		}else if($han && $eng && !$nun){
		$width =  $width + 2;
		} else if(!$han && $eng && $nun){
		$width =  $width + 8;
		}else if($han && $eng && $nun){
		$width =  $width + 3;
		}else if($han && !$eng && $nun){
		$width =  $width +3;
		}


		//$height = $height -5;


		 

			  $width = imagesx($im) * 0.1 * ($size2 /2) ;
			$height = imagesy($im) * 0.1 * ($size2 /2) + 2;

			  $width = $width +5;
			
		if($jp != "") {
			$jp = $jp - 100;
			$width = $width + $jp;
		}

			$width = $width * 0.489;
		    $height = $height * 0.489;


		   $im2 = imagecreatetruecolor($width, $height);

		/*imagetruecolortopalette($im2, true, 1024);  // 이걸 해줘야 투명배경이 씌워진다 ? 
		$back = imagecolorallocatealpha($im2, 255, 255, 255, 127);  // 투명배경을 씌운다 
		imagefilledrectangle($im2, 0, 0, $width, $height, $back); 

		$bg_color = imagecolorat($im2,1,1);
		imagecolortransparent($im2, $bg_color);
		*/
		 
		//imagecolortransparent($im, $back);

		imagecopyresampled($im2, $im, 0, 0, 0, 0, $width, $height, imagesx($im), imagesy($im));

		//imagecolortransparent($im2, $back);
		   
		  

		if($mk_btn=="Y") {
			imagegif($im2, $tmp_file); //imagejpeg($im);		
		}
			header('Content-type: image/gif');
			imagegif($im2); //imagejpeg($im);
			imagedestroy($im2);
			imagedestroy($im);
			exit;
		}else{
			header('Content-type: image/gif');
			echo file_get_contents($tmp_file);
			exit;
		}

        }

	 

		
	function content_split($str,$len = 2) {
		if(strstr($str,'#')) {
		$str = str_replace("#","",$str);
		}
		
		$ret = array();
		while ($str) {
			$i = $len - 1;
			while(ord($str{$i}) > 127) {$i--;}  // 한글이 아닐때까지 찾는다.
			while($i < ($len - 2)) { $i += 2; } // 최대길이까지 2씩 더한다
			$ret[] = substr($str,0,$i+1);
			$str = substr($str,$i+1);
		}
		return $ret;
	}


	function text_array($text){
	$text_array =  $this->content_split($text,2);
	$len = 0;
	$text_a = array();
	for($ii=0; $ii<count($text_array); $ii++) {
		if(strlen($text_array[$ii]) == 2 && ord($text_array[$ii]) < 127) {
		$a = $this->content_split($text_array[$ii],1);

		$text_a[$len] = $a[0];
		$len = $len + 1;	
		
		$text_a[$len] = $a[1];
		$len = $len + 1;

		}else{
		$text_a[$len] = $text_array[$ii];
		$len = $len + 1;
		}
	}
	return $text_a;
	}
	

	function GDImageLoad($filename){
		 global $IsTrueColor, $Extension;

		$image_type = getimagesize($filename);

		 switch( $image_type[2] ) {
			  case 2: // JPEG일경우
				   $im = imagecreatefromjpeg($filename);
				   $Extension = "jpg";
				   break;
			  case 1: // GIF일 경우
				   $im = imagecreatefromgif($filename);
				   $Extension = "gif";
				   break;
			  case 3: // png일 경우
				   $im = imagecreatefrompng($filename);
				   $Extension = "png";
				   break;
			  default:
				   break;
		 }

		 $IsTrueColor = imageistruecolor($im);

		 return $im;
	}

	//원본 이미지 좌우양쪽을 잘라 버턴으로 만듭니다. 
    function CreateButton_2W($Img, $Im, $Im_,$width ,$height ,$px,$plus) { 
		$Width =$width + $plus; 
        $Height = $Img[Height]; 
        $SaveIm =imagecreatetruecolor($Width,$Img[Height]);
		$tmp=imagecreatefromgif($Img[Name]); 
	
        $PosWidth = $Img['Width']-$px; 
        $PosHeight = $Img['Height']-$px; 
        $PosWidth2 = $Img['Width']-$px*2; 
        $PosHeight2 = $Img['Height']-$px*2; 

        $RePosWidth = $Width-$px; 
        $RePosHeight = $Height-$px; 
        $RePosWidth2 = $Width-$px*2; 
        $RePosHeight2 = $Height-$px*2; 
		

		//2009-06-26 종태 가운데 포지션을 잡아보자
		$padding = ($Width - $width) / 1.9+1;
		$padding_top = (($Height - $height) / 2) / 2.1 - 0.5;
		$padding_top_ = $Img[Height] / 1.61 +5 ;

        //왼쪽 
        imagecopy($SaveIm, $tmp,0, 0, 0, 0, $px, $Img['Height']); 
        //오른쪽 
        imagecopy($SaveIm, $tmp, $RePosWidth, 0, $PosWidth, 0, $px, $Img['Height']); 
        //가운데 
        imagecopyresampled($SaveIm, $tmp, $px, 0, $px, 0, $RePosWidth2, $Img['Height'], $px, $Img['Height']); 
        imagecopyresampled($SaveIm,$Im,  $padding, $padding_top, 0, 0, $width ,$height, $width ,$height); 
		imagecopyresampled($SaveIm,$Im_,  $padding, $padding_top_, 0, 0, $width ,$height, $width ,$height); 
		
		//imagecopymerge($SaveIm,$Im_,$padding, $padding_top_, 0, 0, $Width,$Height,0); // 원본과 워터마크를 찍은 이미지를 적당한 투명도로 겹치기 
		
		imagedestroy($Im); 
        return $SaveIm ; 
    } 
   

       function CreateButton_3W($Img, $Im, $Im_,$width ,$height ,$px) { 
		$Width =$width + 38; 
        $Height = $Img[Height]; 
        $SaveIm =imagecreatetruecolor($Width,$Img[Height]);
		$tmp=imagecreatefromgif($Img[Name]); 
	
        $PosWidth = $Img['Width']-$px; 
        $PosHeight = $Img['Height']-$px; 
        $PosWidth2 = $Img['Width']-$px*2; 
        $PosHeight2 = $Img['Height']-$px*2; 

        $RePosWidth = $Width-$px; 
        $RePosHeight = $Height-$px; 
        $RePosWidth2 = $Width-$px*2; 
        $RePosHeight2 = $Height-$px*2; 
		

		//2009-06-26 종태 가운데 포지션을 잡아보자
		$padding = ($Width - $width) / 1.9 +2;
		$padding_top = (($Height - $height) / 2) / 2.1 -1;
		$padding_top_ = $Img[Height] / 1.61 ;

        //왼쪽 
        imagecopy($SaveIm, $tmp,0, 0, 0, 0, $px, $Img['Height']); 
        //오른쪽 
        imagecopy($SaveIm, $tmp, $RePosWidth, 0, $PosWidth, 0, $px, $Img['Height']); 
        //가운데 
        imagecopyresampled($SaveIm, $tmp, $px, 0,$px, 0, $RePosWidth2, $Img['Height'], $px, $Img['Height']); 
        imagecopyresampled($SaveIm,$Im,  $padding, $padding_top, 0, 0, $width ,$height, $width ,$height); 
		imagecopyresampled($SaveIm,$Im_,  $padding, $padding_top_, 0, 0, $width ,$height, $width ,$height); 
		
		//imagecopymerge($SaveIm,$Im_,$padding, $padding_top_, 0, 0, $Width,$Height,0); // 원본과 워터마크를 찍은 이미지를 적당한 투명도로 겹치기 
		
		imagedestroy($Im); 
        return $SaveIm ; 
    } 


 //원본 이미지 좌우양쪽을 잘라 버턴으로 만듭니다. 
    function CreateButton_4W($Img, $Im, $Im_,$width ,$height ,$px,$plus) { 
		$Width =$width + $plus; 
        $Height = $Img[Height]; 
        $SaveIm =imagecreatetruecolor($Width,$Img[Height]);
		$tmp=imagecreatefromgif($Img[Name]); 
	
        $PosWidth = $Img['Width']-$px; 
        $PosHeight = $Img['Height']-$px; 
        $PosWidth2 = $Img['Width']-$px*2; 
        $PosHeight2 = $Img['Height']-$px*2; 

        $RePosWidth = $Width-$px; 
        $RePosHeight = $Height-$px; 
        $RePosWidth2 = $Width-$px*2; 
        $RePosHeight2 = $Height-$px*2; 
		

		//2009-06-26 종태 가운데 포지션을 잡아보자
		$padding = ($Width - $width) / 1.9+1;
		$padding_top = (($Height - $height) / 2) / 2.1 - 4;
		$padding_top_ = $Img[Height] / 1.61 - 3 ;

        //왼쪽 
        imagecopy($SaveIm, $tmp,0, 0, 0, 0, $px, $Img['Height']); 
        //오른쪽 
        imagecopy($SaveIm, $tmp, $RePosWidth, 0, $PosWidth, 0, $px, $Img['Height']); 
        //가운데 
        imagecopyresampled($SaveIm, $tmp, $px, 0, $px, 0, $RePosWidth2, $Img['Height'], $px, $Img['Height']); 
        imagecopyresampled($SaveIm,$Im,  $padding, $padding_top, 0, 0, $width ,$height, $width ,$height); 
		imagecopyresampled($SaveIm,$Im_,  $padding, $padding_top_, 0, 0, $width ,$height, $width ,$height); 
		
		//imagecopymerge($SaveIm,$Im_,$padding, $padding_top_, 0, 0, $Width,$Height,0); // 원본과 워터마크를 찍은 이미지를 적당한 투명도로 겹치기 
		
		imagedestroy($Im); 
        return $SaveIm ; 
    } 
   


    //원본 이미지 좌우양쪽을 잘라 버턴으로 만듭니다. 
    function CreateButtonBg($Img, $Im, $Im_,$width ,$height ,$px) { 
		$Width =$width + 30; 
        $Height = $height; 
        $SaveIm =imagecreatetruecolor($Width,$Img[Height]);
		$tmp=imagecreatefromgif($Img[Name]); 
	
        $PosWidth = $Img['Width']-$px; 
        $PosHeight = $Img['Height']-$px; 
        $PosWidth2 = $Img['Width']-$px*2; 
        $PosHeight2 = $Img['Height']-$px*2; 

        $RePosWidth = $Width-$px; 
        $RePosHeight = $Height-$px; 
        $RePosWidth2 = $Width-$px*2; 
        $RePosHeight2 = $Height-$px*2; 
		

		//2009-06-26 종태 가운데 포지션을 잡아보자
		$padding = ($Width - $width) / 1.9;
		$padding_top = (($Height - $height) / 2) / 2.1 -1;
		$padding_top_ = $Img[Height] / 1.61 ;

        
        //가운데 
        imagecopyresampled($SaveIm, $tmp, 0, 0, 1, 0, $RePosWidth2, $Img['Height'], 10, $Img['Height']); 
        
		//imagecopymerge($SaveIm,$Im_,$padding, $padding_top_, 0, 0, $Width,$Height,0); // 원본과 워터마크를 찍은 이미지를 적당한 투명도로 겹치기 
		
		//imagedestroy($Im); 
        return $SaveIm ; 
    } 
   


	
	//이미지 정보를 구합니다. 
    function GetImgInfo($FileName) { 
        $ImgInfo['Name'] = $FileName; 
        $ImgInfo['File'] = $this->GetNameExt($FileName); 
      
	
	  	list($ImgInfo['Width'], $ImgInfo['Height'], $ImgInfo['Type'], $ImgInfo['Attr']) = getimagesize($FileName); 
       
		return $ImgInfo; 
    } 
    //파일명와 확장자를 분리합니다. 
    function GetNameExt($FileName) { 
        preg_match('/(.*)\.([^\.]+)$/', $FileName, $Match); 
        $File['name'] = $Match[1]; 
        $File['ext'] = strtolower($Match[2]); 
        return $File; 
    } 
	
}

/* End of file img_font.php */
/* Location: ./application/controllers/img_font.php */
?>