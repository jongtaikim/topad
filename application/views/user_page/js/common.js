/* vim: set expandtab tabstop=4 shiftwidth=4 foldmethod=marker: */
/**
* 작성일: 2015-09-21
* 작성자: 김종태
*****************************************************************
* 
*/

function ie_chk(){
	var trident = navigator.userAgent.match(/Trident\/(\d)/i);
	if(trident != null){
		 return 1;
	} else{
		 return 0;
	}
}

//var main_slide_mouserover = '';
//슬라이더 실행

//1184 / 900

function animatedStart(){
//	 if( !$('html').hasClass('ie') ) {
		$('[ani]').css({"opacity":0, "filter": "alpha(opacity = 0)"});
		$('[ani]').each(function(){
			

			
			if(($(this).offset().top / 3.5) <  $(window).scrollTop() || (($(this).offset().top / 5) < 220) && $(window).scrollTop() == 0){
				var delay = $(this).attr('delay');
				var ani = $(this).attr('ani');
				var obj = $(this);
				if(!delay) delay = 100;
				if(!ani) ani = 'fadeIn';
				
				setTimeout(function(){
					obj.addClass('animated '+ani);
				},delay);
			}
		});
//	}
}



function animatedStart2(){
	// if( !$('html').hasClass('ie') ) {
		$('[ani_s]').css({"opacity":0, "filter": "alpha(opacity = 0)"});
		$('[ani_s]').each(function(){
			
				var delay = $(this).attr('delay');
				var ani = $(this).attr('ani_s');
				var obj = $(this);
				if(!delay) delay = 100;
				if(!ani) ani = 'fadeIn';
				
				setTimeout(function(){
					obj.addClass('animated '+ani);
				},delay);
			
		});
	//}
}

$(document).scroll(function(){

animatedStart();

});



function reload_tree(){
	location.reload();
}

$(document).ready(function(){ 
	animatedStart();
	animatedStart2();
	$('.over_plus').hover(function(){ $(this).addClass('opa70'); },function(){ $(this).removeClass('opa70'); })

});





/****** facebook *********************/
 //콜백
  function statusChangeCallback(response) {
	if(!userid){
		console.log('statusChangeCallback');
		console.log(response);
	  
		if (response.status === 'connected') {
		  
		//userid = response.authResponse.userID;
		accessToken = response.authResponse.accessToken;
		login_api();
		fblogins = true;

		} else if (response.status === 'not_authorized') {
		fblogins = false;
		} else {
		fblogins = false;
		}
	}else{
		console.log('로그인중');
	}
  }


 function facebooklogin() {  
     //페이스북 로그인 버튼을 눌렀을 때의 루틴.  
         FB.login(function(response) {  
             var fbname;  
             var accessToken = response.authResponse.accessToken;  
			checkLoginState();
         }, {scope: 'public_profile,email'});  
 }  
 





  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
  FB.init({
    appId      : fb_app_id,
    cookie     : true,  // enable cookies to allow the server to access 
    xfbml      : true,  // parse social plugins on this page
    version    : 'v2.5' 
  });

   FB.getLoginStatus(function(response) {
    statusChangeCallback(response);
  });

  };


 function fblogout(){
   FB.logout(function(response) {
		logout_end();
   });

 }



  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));



  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function login_api() {
    console.log('로그인 성공 ');
    FB.api('/me', {  fields: 'name, email' },function(response) {
	
     var user_photo =  'http://graph.facebook.com/' + response.id + '/picture';
   
		$.ajax({
		type: 'POST',
		url: '/user/fb/fb_user_save',
		data : 'fb_userid='+response.id+'&fb_username='+response.name+'&fb_email='+response.email+'&fb_userphoto='+user_photo+'&fb_accesstoken='+accessToken,
		dataType: 'html',
		success: function(data, status) {
			//alert(data);
			//location.reload();
			login_end();
		},
		error: function(request,status,error) {
			alert(request.responseText);
		}
		});
		
    });
  }

/****** facebook *********************/
/****** kakao *********************/

  // 사용할 앱의 JavaScript 키를 설정해 주세요.
    Kakao.init(kakao_app_id);
    function loginWithKakao() {
	// 로그인 창을 띄웁니다.
	Kakao.Auth.login({
		 success: function(authObj) {
	// 로그인 성공시, API를 호출합니다.
	Kakao.API.request({
		  url: '/v1/user/me',
		  success: function(res) {


			 $.ajax({
				type: 'POST',
				url: '/user/fb/kakao_user_save',
				data : 'userid='+res.id+'&username='+res.properties.nickname+'&userphoto='+res.properties.profile_image,
				dataType: 'html',
				success: function(data, status) {
					login_end();
				},
				error: function(request,status,error) {
					alert(request.responseText);
				}
			});

		  },
		  fail: function(error) {
		    alert(JSON.stringify(error))
		  }
	});
    },
        fail: function(err) {
          console.log(JSON.stringify(err))
        }
      });
    }

/****** kakao *********************/
/****** naver *********************/



/****** naver *********************/



function login_end(){
	//alert('로그인완료');
}

function logout_end(){
	//encodeURIComponent()
	$.ajax({
	type: 'GET',
	url: '/user/member/member_logout',
	dataType: 'html',
		success: function(html, status) {
		//	alert('로그아웃 완료');	
			location.reload();
		}
	});
	
}