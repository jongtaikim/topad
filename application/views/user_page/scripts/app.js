(function ($) {
	'use strict';

	window.app = {
      name: 'Flatkit',
      version: '1.0.0',
      // for chart colors
      color: {
        'primary':      '#0cc2aa',
        'accent':       '#a88add',
        'warn':         '#fcc100',
        'info':         '#6887ff',
        'success':      '#6cc788',
        'warning':      '#f77a99',
        'danger':       '#f44455',
        'white':        '#ffffff',
        'light':        '#f1f2f3',
        'dark':         '#2e3e4e',
        'black':        '#2a2b3c'
      },
      setting: {
        theme: {
    			primary: 'primary',
    			accent: 'accent',
    			warn: 'warn'
        },
        color: {
	        primary:      '#0cc2aa',
	        accent:       '#a88add',
	        warn:         '#fcc100'
    	},
        folded: false,
        boxed: false,
        container: false,
        themeID: 1,
        bg: ''
      }
    };
    var setting = 'jqStorage-'+app.name+'-Setting',
        storage = $.localStorage;
    
    if( storage.isEmpty(setting) ){
        storage.set(setting, app.setting);
    }else{
        app.setting = storage.get(setting);
    }

    if(getParams('bg')){
      app.setting.bg = getParams('bg');
      storage.set(setting, app.setting);
    }

    // init
    $('body').addClass(app.setting.bg);
    app.setting.boxed && $('body').addClass('container');
    setTimeout(function() {
        app.setting.folded && $('#aside').addClass('folded');
        $('[ng-model="app.setting.folded"]').prop('checked', app.setting.folded);
        $('[ng-model="app.setting.boxed"]').prop('checked', app.setting.boxed);
    }, 100);
    
    // folded, boxed, container
    $(document).on('change', '#settingLayout input', function(e){
         eval( $(this).attr('ng-model') +"="+ $(this).prop('checked') );
         storage.set(setting, app.setting);
         location.reload();
    });
    // color and theme
    $(document).on('click', '[ng-click]', function (e) {
        eval($(this).attr('ng-click'));
        storage.set(setting, app.setting);
        location.reload();
    });

    function setTheme(theme){
      app.setting.theme = theme.theme;
      setColor();
      if(theme.url){
        setTimeout(function() {
          window.location.href = theme.url;
        }, 1);
      }
    };

    function setColor(){
      app.setting.color = {
        primary: getColor( app.setting.theme.primary ),
        accent: getColor( app.setting.theme.accent ),
        warn: getColor( app.setting.theme.warn )
      };
    };

    function getColor(name){
      return app.color[ name ] ? app.color[ name ] : palette.find(name);
    };

    // Checks for ie
    var isIE = !!navigator.userAgent.match(/MSIE/i) || !!navigator.userAgent.match(/Trident.*rv:11\./);
    isIE && $('body').addClass('ie');

    // Checks for iOs, Android, Blackberry, Opera Mini, and Windows mobile devices
    var ua = window['navigator']['userAgent'] || window['navigator']['vendor'] || window['opera'];
    (/iPhone|iPod|iPad|Silk|Android|BlackBerry|Opera Mini|IEMobile/).test(ua) && $('body').addClass('smart');

    function getParams(name) {
      name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
      var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
          results = regex.exec(location.search);
      return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

})(jQuery);
