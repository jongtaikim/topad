/****
2013-05-03 김시재
회원관련 자바스크립트 jquery  버전 1.7.1
****/
$(function(){
	$("#btnChkId").click(function(){
		var userid = $("input[name=userid]").val();
		
		if (userid == ""){
			alert("아이디를 입력해주세요");
			$("input[name=userid]").focus();
		}else{
			if (chkDigit(userid)){
				var chkUrl = "/user/member/ajaxCont/userid?userid=" + userid;
				$.get(chkUrl,function(data){
										
					if ($.trim(data) == "Y"){
						
						if (confirm("사용가능한 아이디 입니다\r\n아이디를 사용하시겠습니까?") ){
							$("input[name=chkid]").val("Y");
							$("input[name=userid]").attr("readonly",true);
						}
					}else{
						
						alert("사용할수 없는 아이디 입니다");
						$("input[name=chkid]").val("N");
					}
				});
			}else{
				alert("영어와 숫자만 입력하실수 있습니다");
				$("input[name=userid]").focus();
			}
		}
	});
});

function hy_add() {
	var referrer = document.referrer ;
	var f = document.frm; 

	if( !f.userid.value || f.chkid.value != "Y"  ) {
		alert("아이디를 등록하여 주시기 바랍니다.");
		f.userid.focus();
		return;
	}

	if(!f.pwd.value) {
		alert("비밀번호를 등록하여 주시기 바랍니다.")
		f.pwd.focus();
		return;
	}

	if(!f.pwd_confirm.value) {
		alert("확인 비밀번호를 등록하여 주시기 바랍니다.")
		f.pwd_confirm.focus();
		return;
	}


	if(f.pwd.value != f.pwd_confirm.value) {
		alert("입력하신 비밀번호가 맞지 않습니다.")
		return ;
	}


	if( !f.username.value ) {
		alert("이름을 입력하여 주시기 바랍니다.");
		f.username.focus();
		return;
	}
/*

	if(!f.zip1.value || !f.zip2.value || !f.addr1.value){
		alert("우편번호를 입력해주세요");
		ZipCode() ;
		return;
	}
*/
/*	if(!f.celphone1.value || !f.celphone2.value || !f.celphone3.value){
		alert("휴대전화 번호를 입력해주세요");
		f.celphone1.focus();
		return;
	}

	//상당 항목 더하기
	var couns_cont = "";

	$.each( $("input[name=couns_cont]:checkbox:checked") , function(){
		if (couns_cont != ""){
			couns_cont += "\r\n";
		}
		couns_cont += $(this).val();
	});

	$("input[name=txtCouns_cont]").val(couns_cont);

	//가입경로 더하기 *인터넷

	var search_mothod = "";
	$.each( $("input[name=search_mothod]:checkbox:checked") , function(){
		if (search_mothod != ""){
			search_mothod += "\r\n";
		}
		search_mothod += $(this).val();
	});

	$("input[name=txtSearch_mothod]").val(search_mothod);


	//가입경로 더하기 *대중매체
	var search_mothod_media = "";
	$.each( $("input[name=search_mothod_media]:checkbox:checked") , function(){
		if (search_mothod_media != ""){
			search_mothod_media += "\r\n";
		}
		search_mothod_media += $(this).val();
	});

	$("input[name=txtSearch_mothod_media]").val(search_mothod_media);


	//가입경로 더하기 *기타
	var search_mothod_etc = "";
	$.each( $("input[name=search_mothod_etc]:checkbox:checked") , function(){
		if (search_mothod_etc != ""){
			search_mothod_etc += "\r\n";
		}
		search_mothod_etc += $(this).val();
	});

	$("input[name=txtSearch_mothod_etc]").val(search_mothod_etc);
*/
	//f.action = 'member_act.php?mode=add';
	f.submit();
}

function hy_mod() {
	var referrer = document.referrer ;
	var f = document.frmEdit; 


	if(!f.pwd.value) {
		alert("비밀번호를 등록하여 주시기 바랍니다.")
		f.pwd.focus();
		return;
	}

	if(!f.pwd_confirm.value) {
		alert("확인 비밀번호를 등록하여 주시기 바랍니다.")
		f.pwd_confirm.focus();
		return;
	}

	if(f.pwd.value != f.pwd_confirm.value) {
		alert("입력하신 비밀번호가 맞지 않습니다.")
		return ;
	}


	if( !f.usernames.value ) {
		alert("이름을 입력하여 주시기 바랍니다.");
		f.usernames.focus();
		return;
	}

	/*if( !f.birth.value ) {
		alert("생년월일을 입력하여 주시기 바랍니다.");
		f.birth.focus();
		return;
	}

	if(!f.email.value) {
		alert("이메일을 입력하여 주시기 바랍니다.")
		f.email.focus()
		return ;
	}*/
/*
	if(!f.zip1.value || !f.zip2.value || !f.addr1.value){
		alert("우편번호를 입력해주세요");
		ZipCode() ;
		return;
	}

	if(!f.celphone1.value || !f.celphone2.value || !f.celphone3.value){
		alert("휴대전화 번호를 입력해주세요");
		f.celphone1.focus();
		return;
	}
*/
	//f.action = 'member_act.php?mode=mod';
	f.submit();
}

function ZipCode(frm) {
	var Page = "/member/ZipCode.php?form=" + frm;
	var Jawon = "resizable=no scrollbars=yes width=650 height=300,top=70,left=70";
	window.open(Page,"NNRG",Jawon);
	return;
}


function chkAgree(){
	if ( ! $("#terms").is(":checked") ) {
		alert("약관에 동의 해주세요");
		$("#terms").focus();
	}else if (! $("#security").is(":checked") ){
		alert("개인정보 보호정책에 동의 해주세요");
		 $("#security").focus();
	}else{
		location.href = "/user/member/member_join/2";
	}

};

function chkDate(obj) {
  var input = obj.value.replace(/-/g,"");
  var inputYear = input.substr(0,4);
  var inputMonth = input.substr(4,2) - 1;
  var inputDate = input.substr(6,2);
  var resultDate = new Date(inputYear, inputMonth, inputDate);
  if ( resultDate.getFullYear() != inputYear ||
       resultDate.getMonth() != inputMonth ||
       resultDate.getDate() != inputDate) {
    obj.value = "";
  } else {
    obj.value = inputYear + "-" + input.substr(4,2) + "-" + inputDate;
  }
}
function chkDigit(str){
	var err = 0; 

	for (var i=0; i<str.length; i++)  { 
		var chk = str.substring(i,i+1); 
		if(!chk.match(/[0-9]|[a-z]|[A-Z]/)) { 
			err = err + 1; 
		} 
	} 

	if (err > 0) { 
		return false;
	} else{
		return true;
	}
}