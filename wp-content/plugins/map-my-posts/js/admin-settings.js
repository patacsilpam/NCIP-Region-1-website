/* Farbtastic color pickin used for WP < 3.5 */
(function ($) {
	"use strict";
	$(function () {
		// check if the new WP color picker widget exists within jQuery UI
		if ( typeof $.wp === 'object' && typeof $.wp.wpColorPicker === 'function') {
			$('input#mmp_settings_min_color').wpColorPicker();
			$('input#mmp_settings_max_color').wpColorPicker();
			$('input#mmp_settings_background').wpColorPicker();
			$('input#mmp_settings_empty_color').wpColorPicker();
		}
		else {
			// use farbtastic if necessary
			$('div#mincolorpicker').farbtastic("input#mmp_settings_min_color");
			$("input#mmp_settings_min_color").click(function(){ $('div#mincolorpicker').slideToggle() });
			
			$('div#maxcolorpicker').farbtastic("input#mmp_settings_max_color");
			$("input#mmp_settings_max_color").click(function(){ $('div#maxcolorpicker').slideToggle() });
			
			$('div#bgcolorpicker').farbtastic("input#mmp_settings_background");
			$("input#mmp_settings_background").click(function(){ $('div#bgcolorpicker').slideToggle() });
			
			$('div#emptycolorpicker').farbtastic("input#mmp_settings_empty_color");
			$("input#mmp_settings_empty_color").click(function(){ $('div#emptycolorpicker').slideToggle() });
		}
	});
}(jQuery));
