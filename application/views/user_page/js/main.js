/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-10-06
* 작성자: 김종태
* 설   명: 메인용 스크립트
*****************************************************************
* 
*/

      var main_slide_int = 0;
	  var main_slide_max_int = 8;
	  var up_main_sld_size = 0;
	  var m_silde = "";
	
	
	function win_set(){
		//$('.slide_item').width($(window).width());
		//win_w = $(window).width();
	}

	$(window).resize(function(){
		win_set();
	})

	/**
	 *  메인 셀프 카메라
	 *
	 */	
	
	function main_self_img(){
		$('.self_main_img').each(function(){
			$(this).mouseenter(function(){
				$('.self_main_img_on').fadeOut(400);
				$(this).next().fadeIn(400);

			});
		});

		$('.self_main_img_on, .selfcamera').each(function(){
			$('.self_main_img_on').mouseleave(function(){
				$(this).fadeOut(400);
			});

			$('.self_main_img_on').click(function(){
				//셀카후기 바로가기
				location.href='/user/board/list_view/1911?cate=1911';
			});
		});


		$('.bnb_img').each(function(){
			$(this).mouseenter(function(){
				$('.bnb_img_on').fadeOut(400);
				$(this).next().fadeIn(400);

			});
		});

		$('.bnb_img_on, .banner_bottom').each(function(){
			$('.bnb_img_on').mouseleave(function(){
				$(this).fadeOut(400);
			});
		});

	}



   function carousel_play(id,time){
	 if(!time) time = 4000;
	 $(id).carousel({
			  interval: time
	});
  }

	$(window).resize(function(){
		if($(window).width() >=1999){
			$('#main_slide_to').css('margin-left','0');
		}

		if($(window).width() <=2000){
			var tmp_left = (2000 - $(window).width()) /2 ;
			if(tmp_left >0){
				$('#main_slide_to').css('margin-left','-'+tmp_left+'px');
			}
		}
	});
 

	$(document).ready(function(){

      var main_slide_int = 0;
	  var main_slide_max_int = 8;
	 
		win_set();
		$('#slide_item_body').fadeIn(600);

		
		carousel_play('#main_slide_to',6100);

		$('#main_slide_to').on('slide.bs.carousel', function () {
			console.log(main_slide_int);
			var main_slide_ints = main_slide_int+2;
			$('.main_v_img'+main_slide_ints+', .main_v_img'+main_slide_ints+'_text').hide();
		});
		
		$('#main_slide_to').on('slid.bs.carousel', function () {
		  
  		      setTimeout(function(){
				 $('.main_v_img').show();
			  },110);
			  setTimeout(function(){
				 $('.main_v_text').show();
			  },600);
			  
			  if(main_slide_max_int <= main_slide_int){
				 main_slide_int=0;
			  }else{
				   main_slide_int++;
			  }
		});
     

		main_self_img(); //셀프 이미지
		carousel_play('#banner-L',4000);
		carousel_play('#banner-B',6000);
	});



			   