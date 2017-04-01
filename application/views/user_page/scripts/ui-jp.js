(function ($, MODULE_CONFIG) {
  	"use strict";
  
	$.fn.uiJp = function(){
		if( !this.attr('ui-jp') ) return;
		var self = this;
		var options = eval('[' + self.attr('ui-options') + ']');
		if ($.isPlainObject(options[0])) {
			options[0] = $.extend({}, options[0]);
		}

		uiLoad.load(MODULE_CONFIG[self.attr('ui-jp')]).then( function(){          
			self[self.attr('ui-jp')].apply(self, options);
		});
	}

})(jQuery, MODULE_CONFIG);
