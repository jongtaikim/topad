(function() {
    /**
     * WYSIWYG 편집 영역에 해당하는 <iframe> 을 초기화한다.
     * document.write 방식을 이용하여 iframe을 초기화 하며, 용
     * IE + document.domain 이 지정된 경우 document.domain 이 지정된 iframe(catalyst)을 먼저 로딩하여
     * iframe 에 접근가능하도록 처리 한 후, document.write 를 실행한다.
     * @private
     * @class
     */
    Trex.WysiwygIframeLoader = Trex.Class.create({
        initialize: function(iframe, iframeUrl, doctype) {
            this.iframe = iframe;
	        this.iframeUrl = iframeUrl;
			this.doctype = '';
			switch (doctype) {
			case "edge":
			case "loose":
			case "strict":
				this.doctype = doctype;
//			case "quirks":
			}
        },

        load: function(callback) {
            try {
                this.loadLocalIframe(callback, this.doctype);
            } catch (e) {
                this.reloadUsingCatalyst(callback);
            }
        },

        loadLocalIframe: function(callback, doctype) {
            var doc = this.iframe.contentWindow.document;
            doc.open();
			switch (doctype) {
			case "edge":
				doc.write(DOCTYPE_edge);
				break;
			case "loose":
				doc.write(DOCTYPE_loose);
				break;
			case "strict":
				doc.write(DOCTYPE_strict);
				break;
			}
            doc.write(wysiwygHTML);
            doc.close();
            // 하위 호환을 위하여 delay 처리한다. 기존 iframe observer 들이 loading 이 비동기라 가정하고 작성되어있다.
            setTimeout(function() {
                callback(doc);
            }, 0);
        },

        reloadUsingCatalyst: function(callback) {
            //console.log("retry with xss iframe catalyst");
            var self = this;
            _WIN.__tx_wysiwyg_iframe_load_complete = function() {
                self.loadLocalIframe(callback, ''); //이 시점에선 어차피 doctype 을 설정할 수 없음.
            };
	        if (!this.iframeUrl) {
		        var basePath = this.getIframePagePath();
				var doctype = this.doctype;
				switch (doctype) {
				case "edge":
				case "loose":
				case "strict":
					this.iframeUrl = basePath + "trex/iframe_loader_catalyst_" + doctype + ".html";
					break;
				default:
					this.iframeUrl = basePath + "trex/iframe_loader_catalyst.html";
				}
	        }

            var explicitDocumentDomain = (document.location.hostname != document.domain);
            if (explicitDocumentDomain) {
                this.iframeUrl = this.iframeUrl + ((this.iframeUrl.indexOf("?") > -1) ? "&" : "?") + "xssDomain=" + document.domain;
            }
            this.iframe.src = this.iframeUrl;
        },

        getIframePagePath: function() {
            return EditorJSLoader.getPageBasePath('editor.js');
        },

        // 옛날 스타일
        loadRemoteIframe: function() {
            var iframe = this.el;
            iframe.setAttribute("src", this.canvasConfig.wysiwygUrl);
        }
    });


    function absolutizeURL(url) {
        var location = _DOC.location;
        if (/^(https?:|file:|)\/\//.test(url)) {
        } else if (url.indexOf("/") === 0) {
            url = "//" + location.host + ":" + (location.port || "80") + url;
        } else {
            var href = location.href;
            var cutPos = href.lastIndexOf("/");
            url = href.substring(0, cutPos + 1) + url;
        }
        return url;
    }

    var cssBasePath = absolutizeURL(EditorJSLoader.getCSSBasePath());
	
	var DOCTYPE_edge = '<!DOCTYPE html>';
	var DOCTYPE_loose = '<!DOCTYPE html>';
	var DOCTYPE_strict = '<!DOCTYPE html>';
	
    var wysiwygHTML =
            '<html lang="ko"><head>' +
            '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' +
            '<title>DaumEditor Wygiwyg Panel</title>' +
        
			'<link rel="stylesheet" href="/uiset/assets/animate.css/animate.min.css" type="text/css" />' +
			'<link rel="stylesheet" href="/uiset/assets/font-awesome/css/font-awesome.min.css" type="text/css" />' +
			'<link rel="stylesheet" href="/uiset/assets/material-design-icons/material-design-icons.css" type="text/css" />' +
			'<link rel="stylesheet" href="/uiset/assets/bootstrap/dist/css/bootstrap.min.css" type="text/css" />' +
			'<link rel="stylesheet" href="/uiset/assets/styles/app.css" type="text/css" />' +
			'<!--[if lt IE 9]> <script src="/trunk/scripts//ie/html5shiv.js"></script> <script src="/trunk/scripts/ie/respond.min.js"></script> <script src="/trunk/scripts/ie/excanvas.js"></script> <![endif]-->' +
			'</head>' +
            '<body class="container white"><div class="col-md-12" id="">' +
            $tom.EMPTY_PARAGRAPH_HTML +
            '</div></body>'+
			'</html>';

})();