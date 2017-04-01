/**
* 파일명: lib.validate.js
* 설  명: 폼 체크, 값 표준화
* 작성자: jstoy project
* 날  짜: 2003-10-24
    - 2004.02.18 setValue, getValue 폼.values 기능 추가
    - 2004.02.20 그룹화 필수기능 추가 ex) required="test" requirenum="2"
    - 2004.02.25 onsubmit="return validate(this)" 기능을 다시 넣었습니다..
    - 2004.02.26 간단한 debugging 기능 추가 (try..catch..구문 사용)
***********************************************
*/

var FormCheckerObject = null;
var FormCheckerLoadAction = window.onload;
window.onload = function() {
    try {
        FormCheckerLoadAction();
    } catch (e) {
        // null
    }
    FormCheckerObject = new FormChecker;
}

// validate function for classic user
function validate(form) {
    if (FormCheckerObject == null) {
		FormCheckerObject = new FormChecker;
        //alert("validate library is not ready.");
        //return false;
    }
    try {
        return FormCheckerObject.validate(form);
    } catch (e) {
        alert(e);
        return false;
    }
}

FormChecker = function() {
    var classObj = this;

    // 미리 정의된 에러 메시지들
    this.FORM_ERROR_MSG = {
       common   : "입력하신 내용이 규칙에 어긋납니다.\n규칙에 어긋나는 내용을 바로잡아주세요.",

       required : "반드시 입력하셔야 하는 사항입니다.",
       required_group : "이 항목들 중에 {requirenum}개 이상의 항목이 입력되어야 합니다.",
       notequal : "입력된 내용이 일치하지 않습니다.",
       invalid  : "입력된 내용이 형식에 어긋납니다.",
		mincheck : "{mincheck}개의 항목이상으로 선택하세요.",
		maxcheck : "{maxcheck}개의 항목이하로 선택하세요.",
       minbyte  : "입력된 내용의 길이가 {minbyte}Byte 이상이어야 합니다.",
       maxbyte  : "입력된 내용의 길이가 {maxbyte}Byte를 초과할 수 없습니다."
    }
    // 폼 체크 함수 매핑
    this.VALIDATE_FUNCTION = {
       email   : this.func_isValidEmail,
       phone   : this.func_isValidPhone,
       userid  : this.func_isValidUserid,
       hangul  : this.func_hasHangul,
       number  : this.func_isNumeric,
       engonly : this.func_alphaOnly,
       jumin   : this.func_isValidJumin,
       bizno   : this.func_isValidBizNo
    }
    /**
    * 에러 출력 플래그
    * all : 1, one : 2, one per object : 3
    */
    this.ERROR_MODE = 2;

    for (var i=0,s=document.forms.length; i<s; i++) {
        var form = document.forms[i];
        form.getValue = function(elName) {
            var el = this.elements[elName];
            var ret = new Array();
            if (typeof el == 'undefined') {
                return null;
            } else if (typeof el.length != 'undefined') {
                if (el.type == 'select-one') {
                    if(el.options.length < 1 || el.selectedIndex < 0) {
						return null;
					} else {
						return el.options[el.selectedIndex].value;
					}
                } else {
                    for (var j=0,t=el.length; j<t; j++) {
                        if (el[j].checked) {
                            if (el[j].getAttribute('TYPE') == 'radio') return el[j].value;
                            if (el[j].getAttribute('TYPE') == 'checkbox') ret[ret.length] = el[j].value;
                        }
                    }
                    return ret;
                }
                return null;
            } else {
                return el.value;
            }
            return null;
        }
        form.setValue = function(elName, value) {
            var el = this.elements[elName];
            if (typeof el.length != 'undefined') {
                if (el.type == 'select-one') {
                    for (var j=0,t=el.length; j<t; j++) {
                        if (el.options[j].value == value)
                            el.selectedIndex = j;
                    }
                } else {
                    for (var j=0,t=el.length; j<t; j++) {
                        if (el[j].getAttribute('TYPE') == 'radio')
                            if (el[j].value == value) el[j].checked = true;
                        if (el[j].getAttribute('TYPE') == 'checkbox') {
                            if (value.length != undefined) {
                                for (var k=0,cnt=value.length; k<cnt; j++)
                                    el[j].checked = (el[j].value == value[k]);
                            } else {
                                el[j].checked = (el[j].value == value);
                            }
                        }
                    }
                }
			} else if (el.getAttribute('TYPE') == 'radio' || el.getAttribute('TYPE') == 'checkbox') {
				el.checked = (el.value == value);
            } else if (typeof el != 'undefined') {
                el.value = value;
            }
        }
        if (form.getAttribute("VALIDATE") !== null) {
            form.submitAction = form.onsubmit;
            form.onsubmit = function() {
                if(typeof this.submitAction != 'undefined') this.submitAction();
                try {
                    return classObj.validate(this);
                } catch (e) {
                    alert(e);
                    return false;
                }
            }
        }

        //==-- 기본값 세팅 --==//
        var flValues = (typeof form.values == 'object' && form.values != null) ? true : false;
        for (var e = 0; e < form.elements.length; e++) {
            var el = form.elements[e];
            if (flValues) {
                if (!this.isVaildElement(el)) continue;
                var key = el.name.replace(/\[\]$/,'');
                var value = form.values[key];
                if (value) form.setValue(el.name,value);
            }
            if (el.getAttribute("HNAME") == null || el.getAttribute("HNAME") == "")
                el.setAttribute("HNAME", el.getAttribute("NAME"));
        }
    }
}

FormChecker.prototype.isVaildElement = function(el) {
    return (el.tagName.toLowerCase() == "fieldset" || el.tagName.toLowerCase() == "object" ||
        el.name == null || el.name == "")
        ? false : true;
}

FormChecker.prototype.getElType = function(el) {
    var elType = null;
    if (el.type.toLowerCase() == "radio" || el.type.toLowerCase() == "checkbox") {
        elType = "check";
    } else if (el.type.toLowerCase() == "file") {
        elType = "file";
    } else if (el.tagName.toLowerCase() == "select") {
        elType = "select";
    } else if (el.tagName.toLowerCase() == "input" || el.tagName.toLowerCase() == "textarea") {
        elType = "text";
    }
    return elType;
}

FormChecker.prototype.validate = function(form) {
    this.isErr  = false;
    this.errMsg = this.FORM_ERROR_MSG["common"] ? this.FORM_ERROR_MSG["common"] + "\n\n" : "";
    this.errObj = null;
    this.curObj = null;

    var oldRequired = new Array;
    for (var i=0, s=form.elements.length; i<s; i++) {
        var el = form.elements[i];
        if (!this.isVaildElement(el)) continue;

        var required   = el.getAttribute("REQUIRED");
        var requirenum = el.getAttribute("REQUIRENUM");
        var trim    = el.getAttribute("TRIM");
        var mincheck = el.getAttribute("MINCHECK");
        var maxcheck = el.getAttribute("MAXCHECK");
        var minbyte = el.getAttribute("MINBYTE");
        var maxbyte = el.getAttribute("MAXBYTE");
        var option  = el.getAttribute("OPTION");
        var match   = el.getAttribute("MATCH");
        var span    = el.getAttribute("SPAN");
        var glue    = el.getAttribute("GLUE");
        var pattern = el.getAttribute("PATTERN");
        var elValue = form.getValue(el.name);
        var elType  = this.getElType(el);

        if (elType == "text") {
           try {
				switch (trim) {
					case "ltrim": el.value = elValue.ltrim(); break;
					case "rtrim": el.value = elValue.rtrim(); break;
					case "notrim": break;
					default: el.value = elValue.trim(); break;
				}
			} catch(e) {
				continue;
			}
        }
        if (required !== null) {
			if (required == "") {
				if (elValue == null || elValue == "")
                    this.addError(el,"required");
            } else {
                var flOldRequired = false;
                for (var j=0; j<oldRequired.length; j++)
                    if (oldRequired[j] == required) flOldRequired = true;

                if (!flOldRequired) {
                    oldRequired[oldRequired.length] = required;
                    var reqNum = 0;
                    var reqHname = new Array;
                    for (var j=0; j<s; j++) {
                        var reqEl = form.elements[j];
                        if (!this.isVaildElement(reqEl)) continue;
                        if (reqEl.getAttribute("REQUIRED") == required) {
                            var reqElName = form.getValue(reqEl.name);
                            if (reqElName != "" && reqElName != null) reqNum++;
                            reqHname[reqHname.length] = reqEl.getAttribute("HNAME");
                        }
                    }
                    if (reqNum < requirenum)
                        this.addError(el,"required_group",reqHname.join(", "));
                }
            }
        }
        if (elType == "text") {
            if (minbyte != null) {
                if (elValue.bytes() < parseInt(minbyte,10)) this.addError(el,"minbyte");
            }
            if (maxbyte != null) {
                if (elValue.bytes() > parseInt(maxbyte,10)) this.addError(el,"maxbyte");
            }
            if (match != null) {
                if (typeof form.elements[match] == 'undefined')
                    throw "Element '"+ match +"' is not found.";
                else if (elValue != form.elements[match].value)
                    this.addError(el,"notequal");
            }
            if (elValue != "" && option !== null) {
                if (typeof this.VALIDATE_FUNCTION[option] == 'undefined') {
                    throw "Function map '"+ option +"' is not found.";
                } else if (span !== null) {
                    var _value = new Array();
                    for (var j = 0; j < span; j++) {
                        if (typeof form.elements[i+j] == 'undefined')
                            throw (i+j) +"th Element is not found.";
                        _value[j] = form.elements[i+j].value;
                    }
                    var value = _value.join(glue === null ? "" : glue);
                    var tmpMsg = this.VALIDATE_FUNCTION[option](el, value);
                    if (tmpMsg !== true) this.addError(el,tmpMsg);
                } else {
                    var tmpMsg = this.VALIDATE_FUNCTION[option](el);
                    if (tmpMsg !== true) this.addError(el,tmpMsg);
                }
            }
            if (elValue != "" && pattern !== null) {
                try {
                    pattern = new RegExp(pattern);
                } catch (e) {
                    throw "Invaild Regular Expression '"+ pattern +"'";
                }
                if (!pattern.test(elValue)) {
					this.addError(el,'invalid');
				}
            }
        }
        if ((mincheck > 0 || maxcheck > 0) && elType == "check") {
            var _checks = form.elements[el.name];
            var _num = 0;

            if (typeof _checks.length != "undefined") {
                for (var j=0; j<_checks.length; j++) {
                    if (_checks[j].checked) _num++;
                }
            } else {
                if (_checks.checked) _num++;
            }
            if (mincheck > 0 && _num < mincheck) this.addError(el, "mincheck");
            if (maxcheck > 0 && _num > maxcheck) this.addError(el, "maxcheck");
        }
    }
    if (this.isErr == true) {
        alert(this.errMsg);
        if (this.errObj.getAttribute("delete") !== null)
            this.errObj.value = "";
        if (this.errObj.getAttribute("select") !== null)
            this.errObj.select();
        if (this.errObj.getAttribute("nofocus") === null)
            this.errObj.focus();
    }
    return !this.isErr;
}

FormChecker.prototype.addError = function(el, type, elName) {
    var pattern = /\{([a-zA-Z0-9_]+)\}/i;
    var msg = (this.FORM_ERROR_MSG[type]) ? this.FORM_ERROR_MSG[type] : type;
    var elName = elName ? elName : el.getAttribute("hname");

    if (el.getAttribute("errmsg") != null) msg = el.getAttribute("errmsg");
    if (pattern.test(msg) == true) {
        while (pattern.exec(msg)) msg = msg.replace(pattern, el.getAttribute(RegExp.$1));
    }
    if (!this.errObj || this.ERROR_MODE != 2) {
        if (this.curObj == el && el.getAttribute("errmsg") == null) {
            if (this.ERROR_MODE == 1)
                this.errMsg += "   - "+ msg +"\n";
            } else if (this.curObj != el) {
                if (this.curObj)
                    this.errMsg += "\n";
            this.errMsg += "["+ elName +"]\n   - "+ msg +"\n";
        }
    }
    if (!this.errObj) this.errObj = el;
    this.curObj = el;
    this.isErr  = true;
    return;
}

/// 패턴 검사 함수들 ///
FormChecker.prototype.func_isValidEmail = function(el,value) {
   var value = value ? value : el.value;
   var pattern = /^[_a-zA-Z0-9-\.]+@[\.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
   return (pattern.test(value)) ? true : "invalid";
}

FormChecker.prototype.func_isValidUserid = function(el) {
   var pattern = /^[a-zA-Z]{1}[a-zA-Z0-9_]{4,11}$/;
   return (pattern.test(el.value)) ? true : "5자이상 12자 미만,\n 영문,숫자, _ 문자만 사용할 수 있습니다";
}

FormChecker.prototype.func_hasHangul = function(el) {
   var pattern = /[가-힝]/;
   return (pattern.test(el.value)) ? true : "반드시 한글을 포함해야 합니다";
}

FormChecker.prototype.func_alphaOnly = function(el) {
   var pattern = /^[a-zA-Z]+$/;
   return (pattern.test(el.value)) ? true : "invalid";
}

FormChecker.prototype.func_isNumeric = function(el) {
   var pattern = /^[0-9]+$/;
   return (pattern.test(el.value)) ? true : "반드시 숫자로만 입력해야 합니다";
}

FormChecker.prototype.func_isValidJumin = function(el,value) {
    var pattern = /^([0-9]{6})-?([0-9]{7})$/;
    var num = value ? value : el.value;
    if (!pattern.test(num)) return "invalid";
    num = RegExp.$1 + RegExp.$2;

    var sum = 0;
    var last = num.charCodeAt(12) - 0x30;
    var bases = "234567892345";
    for (var i=0; i<12; i++) {
        if (isNaN(num.substring(i,i+1))) return "invalid";
        sum += (num.charCodeAt(i) - 0x30) * (bases.charCodeAt(i) - 0x30);
    }
    var mod = sum % 11;
    return ((11 - mod) % 10 == last) ? true : "invalid";
}

FormChecker.prototype.func_isValidBizNo = function(el,value) {
    var pattern = /([0-9]{3})-?([0-9]{2})-?([0-9]{5})/;
    var num = value ? value : el.value;
    if (!pattern.test(num)) return "invalid";
    num = RegExp.$1 + RegExp.$2 + RegExp.$3;
    var cVal = 0;
    for (var i=0; i<8; i++) {
        var cKeyNum = parseInt(((_tmp = i % 3) == 0) ? 1 : ( _tmp  == 1 ) ? 3 : 7);
        cVal += (parseFloat(num.substring(i,i+1)) * cKeyNum) % 10;
    }
    var li_temp = parseFloat(num.substring(i,i+1)) * 5 + "0";
    cVal += parseFloat(li_temp.substring(0,1)) + parseFloat(li_temp.substring(1,2));
    return (parseInt(num.substring(9,10)) == 10-(cVal % 10)%10) ? true : "invalid";
}

FormChecker.prototype.func_isValidPhone = function(el,value) {
    var pattern = /^([0]{1}[0-9]{1,2})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/;
    var num = value ? value : el.value;
    if (pattern.exec(num)) {
        if(RegExp.$1 == "011" || RegExp.$1 == "016" || RegExp.$1 == "017" || RegExp.$1 == "018" || RegExp.$1 == "019") {
            if(!el.getAttribute("span"))
                el.value = RegExp.$1 + "-" + RegExp.$2 + "-" + RegExp.$3;
        }
        return true;
    } else {
        return "invalid";
    }
}

/**
* common prototype functions
*/
String.prototype.trim = function(str) {
    str = this != window ? this : str;
    return str.ltrim().rtrim();
}

String.prototype.ltrim = function(str) {
    str = this != window ? this : str;
    return str.replace(/^\s+/g,"");
}

String.prototype.rtrim = function(str) {
    str = this != window ? this : str;
    return str.replace(/\s+$/g,"");
}

String.prototype.bytes = function(str) {
    var len = 0;
    str = this != window ? this : str;
    for (j=0; j<str.length; j++) {
        var chr = str.charAt(j);
        len += (chr.charCodeAt() > 128) ? 2 : 1;
    }
    return len;
}