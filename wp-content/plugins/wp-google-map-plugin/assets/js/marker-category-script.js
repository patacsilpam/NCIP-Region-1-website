jQuery(document).ready(function($) {
	
	$('.read_icons').click(function () {

		$('.read_icons').removeClass('active');
		$(this).addClass('active');
	});

	$('input[name="wpgmp_search_icon"]').keyup(function() {
		if($(this).val() == '')
			$('.read_icons').show();
		else {
			$('.read_icons').hide();
			$('img[title^="' + $(this).val() + '"]').parent().show();
		}
	});

});

function add_icon_to_images() {
	
	var target = jQuery('.set_marker_cat_button').data('target');
	var msg = jQuery('.set_marker_cat_button').data('message');
	if(jQuery('.read_icons').hasClass('active'))
	{
		
		imgsrc = jQuery('.active').find('img').attr('src');
		var win = window.dialogArguments || opener || parent || top;
		win.send_icon_to_map(imgsrc,target);
	}
	else{
		alert(msg);
	}
}


