(function ($) {
	"use strict";
  	
	uiLoad.load('/uiset/libs/jquery/screenfull/dist/screenfull.min.js');
	$(document).on('click', '[ui-fullscreen]', function (e) {
		e.preventDefault();
		if (screenfull.enabled) {
		  screenfull.toggle();
		}
	});
})(jQuery);
