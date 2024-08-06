<?php
/**
 * This class used to manage settings page in backend.
 * @author Flipper Code <hello@flippercode.com>
 * @version 4.1.6
 * @package Maps
 */

$wpgmp_settings = get_option( 'wpgmp_settings', true );
$api_key = get_option( 'wpgmp_api_key' );

if ( $api_key != '' ) {
	$google_map_api = 'https://maps.google.com/maps/api/js?key=' . $api_key . '&callback=wpgmpInitMap&libraries=geometry,places,drawing&language=en';
} else {
	$google_map_api = 'https://maps.google.com/maps/api/js?libraries=geometry,places,drawing&callback=wpgmpInitMap&language=en';
}


wp_enqueue_script('wpgmp-backend-google-api', $google_map_api, array('wpgmp-map'), WPGMP_VERSION);

$form  = new WPGMP_Template();
$form->form_action = esc_url ( add_query_arg( 'page', 'wpgmp_manage_settings', admin_url ('admin.php') )  );
$form->set_header( esc_html__( 'General Plugin Settings', 'wp-google-map-plugin' ), $response, $enable_accordion = false );

$form->add_element( 'group', 'wpgmp_general_settings', array(
	'value' => esc_html__( 'General Plugin Settings', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link' => 'https://www.wpmapspro.com/docs/how-to-create-an-api-key/'
));

$referrer = home_url();
$referrer_two = '*'.$_SERVER['HTTP_HOST'].'/*';

$form->add_element(
	'message',
	'wpgmp_api_key_instructions',
	array(
		'value' => esc_html__('The very first step to get started with Google Maps is to create the right API key for your website. While creating the Google Maps API keys from the Google Cloud Platform, in the key restriction section, you need to choose HTTP referrer and then you will need to enter HTTP referrer according to your website domain name.', 'wp-google-map-plugin') . '<br><br>' . esc_html__('You will need to enter any one of the below HTTP referrer during the key creation process :   ', 'wp-google-map-plugin') . '<br><br><b><span class="wpgmp_referrer">'.$referrer. '</span></b>&nbsp;&nbsp;&nbsp;&nbsp; <input type="hidden" id="wpgmp_referrer_with_dot" value="'.$referrer.'" class="referrer_to_create"> <span class="tooltip"><span class="copy_to_clipboard"><img src="'. WPGMP_IMAGES. '/copy-to-clipboard.png">  <span class="tooltiptext" id="with_dot_tooltip">'.esc_html__('Copy HTTP Referrer To Clipboard','wp-google-map-plugin').'</span></span></span>&nbsp;&nbsp;&nbsp;&nbsp;'.esc_html__('( Works for most websites )','wp-google-map-plugin').'<br><br><b><span class="wpgmp_referrer">'.$referrer_two. ' </span></b>&nbsp;&nbsp;&nbsp;&nbsp; <input type="hidden" class="referrer_to_create" id="wpgmp_referrer_without_dot" value="'.$referrer_two.'"> <span class="tooltip"><span class="copy_to_clipboard"><img src="'. WPGMP_IMAGES. '/copy-to-clipboard.png">  <span class="tooltiptext" id="without_dot_tooltip">'.esc_html__('Copy HTTP Referrer To Clipboard','wp-google-map-plugin').'</span></span></span>&nbsp;&nbsp;&nbsp;&nbsp;'.esc_html__('( Try this if above doesn\'t work )','wp-google-map-plugin').'<br><br><a href="https://www.wpmapspro.com/docs/how-to-create-an-api-key/" target="_blank">'.esc_html__('View Tutorial','wp-google-map-plugin').'</a>',
		'class' => 'fc-msg fc-msg-info wpgmp_api_key_instructions',
		'show'  => 'true',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);

$link = '<a href="https://www.wpmapspro.com/docs/how-to-create-an-api-key/" target="_blank">'.esc_html__("View Instructions","wp-google-map-plugin").'</a>'; 

$form->set_col( 2 );

$form->add_element('text','wpgmp_api_key',array(
	'lable' => esc_html__( 'Google Maps API Key','wp-google-map-plugin' ),
	'value' => get_option( 'wpgmp_api_key' ),
	'before' => '<div class="fc-6"> <div class="wpgmp_apitest"></div>',
	'after'  => '</div>',
	'desc'  => sprintf( esc_html__( 'Enter the created Google Maps API Key here. %1$s', 'wp-google-map-plugin' ), $link ),
));

if ( get_option( 'wpgmp_api_key' ) == '' ) {

	$generate_link = '<a href="https://www.wpmapspro.com/docs/how-to-create-an-api-key/" class="wpgmp_map_key_missing wpgmp_key_btn fc-btn fc-btn-default btn-lg" target="_blank" >' . esc_html__( 'Generate API Key', 'wpgmp-google-map' ) . '</a>';

	$form->add_element(
		'html', 'wpgmp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-2">',
			'after'  => '</div>',
		)
	);

} else {

	$generate_link = '<a href="javascript:void(0);" class="wpgmp_check_key fc-btn fc-btn-default btn-lg" >' . esc_html__( 'Test API Key', 'wpgmp-google-map' ) . '</a>';

	$form->add_element(
		'html', 'wpgmp_key_btn', array(
			'html'   => $generate_link,
			'before' => '<div class="fc-2">',
			'after'  => '</div>',
		)
	);
}

$form->set_col( 1 );

$guide_link = '<a href="https://www.wpmapspro.com/category/maps-error-codes/" target="_blank">'.esc_html__("guides.",'wp-google-map-plugin').'</a>'; 

$form->add_element(
	'html', 'wpgmp_map_preview', array(
		'lable' => 'Map Preview',
		'html'   => "<div id='wpgmp_map_preview' style='width:100%;height:300px'></div>",
		'before' => '<div class="fc-6">',
		'after'  => '</div>',
		'desc'   => sprintf(esc_html__( 'If map is not visible then please check the javascript console for the error and fix using our %1$s', 'wp-google-map-plugin' ), $guide_link)
	)
);

$language = array(
'en' => esc_html__( 'ENGLISH', 'wp-google-map-plugin' ),
'ar' => esc_html__( 'ARABIC', 'wp-google-map-plugin' ),
'eu' => esc_html__( 'BASQUE', 'wp-google-map-plugin' ),
'bg' => esc_html__( 'BULGARIAN', 'wp-google-map-plugin' ),
'bn' => esc_html__( 'BENGALI', 'wp-google-map-plugin' ),
'ca' => esc_html__( 'CATALAN', 'wp-google-map-plugin' ),
'cs' => esc_html__( 'CZECH', 'wp-google-map-plugin' ),
'da' => esc_html__( 'DANISH', 'wp-google-map-plugin' ),
'de' => esc_html__( 'GERMAN', 'wp-google-map-plugin' ),
'el' => esc_html__( 'GREEK', 'wp-google-map-plugin' ),
'en-AU' => esc_html__( 'ENGLISH (AUSTRALIAN)', 'wp-google-map-plugin' ),
'en-GB' => esc_html__( 'ENGLISH (GREAT BRITAIN)', 'wp-google-map-plugin' ),
'es' => esc_html__( 'SPANISH', 'wp-google-map-plugin' ),
'fa' => esc_html__( 'FARSI', 'wp-google-map-plugin' ),
'fi' => esc_html__( 'FINNISH', 'wp-google-map-plugin' ),
'fil' => esc_html__( 'FILIPINO', 'wp-google-map-plugin' ),
'fr' => esc_html__( 'FRENCH', 'wp-google-map-plugin' ),
'gl' => esc_html__( 'GALICIAN', 'wp-google-map-plugin' ),
'gu' => esc_html__( 'GUJARATI', 'wp-google-map-plugin' ),
'hi' => esc_html__( 'HINDI', 'wp-google-map-plugin' ),
'hr' => esc_html__( 'CROATIAN', 'wp-google-map-plugin' ),
'hu' => esc_html__( 'HUNGARIAN', 'wp-google-map-plugin' ),
'id' => esc_html__( 'INDONESIAN', 'wp-google-map-plugin' ),
'it' => esc_html__( 'ITALIAN', 'wp-google-map-plugin' ),
'iw' => esc_html__( 'HEBREW', 'wp-google-map-plugin' ),
'ja' => esc_html__( 'JAPANESE', 'wp-google-map-plugin' ),
'kn' => esc_html__( 'KANNADA', 'wp-google-map-plugin' ),
'ko' => esc_html__( 'KOREAN', 'wp-google-map-plugin' ),
'lt' => esc_html__( 'LITHUANIAN', 'wp-google-map-plugin' ),
'lv' => esc_html__( 'LATVIAN', 'wp-google-map-plugin' ),
'ml' => esc_html__( 'MALAYALAM', 'wp-google-map-plugin' ),
'it' => esc_html__( 'ITALIAN', 'wp-google-map-plugin' ),
'mr' => esc_html__( 'MARATHI', 'wp-google-map-plugin' ),
'nl' => esc_html__( 'DUTCH', 'wp-google-map-plugin' ),
'no' => esc_html__( 'NORWEGIAN', 'wp-google-map-plugin' ),
'pl' => esc_html__( 'POLISH', 'wp-google-map-plugin' ),
'pt' => esc_html__( 'PORTUGUESE', 'wp-google-map-plugin' ),
'pt-BR' => esc_html__( 'PORTUGUESE (BRAZIL)', 'wp-google-map-plugin' ),
'pt-PT' => esc_html__( 'PORTUGUESE (PORTUGAL)', 'wp-google-map-plugin' ),
'ro' => esc_html__( 'ROMANIAN', 'wp-google-map-plugin' ),
'ru' => esc_html__( 'RUSSIAN', 'wp-google-map-plugin' ),
'sk' => esc_html__( 'SLOVAK', 'wp-google-map-plugin' ),
'sl' => esc_html__( 'SLOVENIAN', 'wp-google-map-plugin' ),
'sr' => esc_html__( 'SERBIAN', 'wp-google-map-plugin' ),
'sv' => esc_html__( 'SWEDISH', 'wp-google-map-plugin' ),
'tl' => esc_html__( 'TAGALOG', 'wp-google-map-plugin' ),
'ta' => esc_html__( 'TAMIL', 'wp-google-map-plugin' ),
'te' => esc_html__( 'TELUGU', 'wp-google-map-plugin' ),
'th' => esc_html__( 'THAI', 'wp-google-map-plugin' ),
'tr' => esc_html__( 'TURKISH', 'wp-google-map-plugin' ),
'uk' => esc_html__( 'UKRAINIAN', 'wp-google-map-plugin' ),
'vi' => esc_html__( 'VIETNAMESE', 'wp-google-map-plugin' ),
'zh-CN' => esc_html__( 'CHINESE (SIMPLIFIED)', 'wp-google-map-plugin' ),
'zh-TW' => esc_html__( 'CHINESE (TRADITIONAL)', 'wp-google-map-plugin' ),
);

$form->add_element( 'select', 'wpgmp_language', array(
	'lable' => esc_html__( 'Map Language', 'wp-google-map-plugin' ),
	'current' => get_option( 'wpgmp_language' ),
	'desc' => esc_html__( 'Choose the language for google map. The default language is English.', 'wp-google-map-plugin' ),
	'options' => $language,
	'before' => '<div class="fc-6">',
	'after' => '</div>',
));

$form->add_element( 'radio', 'wpgmp_scripts_place', array(
	'lable' => esc_html__( 'Include Scripts in ', 'wp-google-map-plugin' ),
	'radio-val-label' => array( 'header' => esc_html__( 'Header','wp-google-map-plugin' ),'footer' => esc_html__( 'Footer (Recommended)','wp-google-map-plugin' ) ),
	'current' => get_option( 'wpgmp_scripts_place' ),
	'class' => 'chkbox_class',
	'default_value' => 'footer',
));

$form->add_element(
	'group',
	'meta_box_setting',
	array(
		'value'  => esc_html__('Meta Box Settings', 'wpgmp-google-map').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group',
	'create_extra_fields',
	array(
		'value'  => esc_html__('Create Extra Field(s)', 'wpgmp-google-map').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);

$form->add_element(
	'group', 'map_troubleshooting', array(
		'value'  => esc_html__( 'Troubleshooting', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'tutorial_link' => 'https://www.wpmapspro.com/docs/what-to-do-when-google-maps-is-not-visible/'
	)
);



$form->add_element(
	'checkbox', 'wpgmp_auto_fix', array(
		'lable'   => esc_html__( 'Auto Fix', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset($wpgmp_settings['wpgmp_auto_fix']) ? $wpgmp_settings['wpgmp_auto_fix'] : '',
		'desc'    => esc_html__( 'If map is not visible somehow, turn on auto fix and check the map.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'checkbox', 'wpgmp_debug_mode', array(
		'lable'   => esc_html__( 'Turn On Debug Mode', 'wpgmp-google-map' ),
		'value'   => 'true',
		'current' => isset($wpgmp_settings['wpgmp_debug_mode']) ? $wpgmp_settings['wpgmp_debug_mode'] : '',
		'desc'    => esc_html__( 'If map is not visible somehow even auto fix in turned on, please turn on debug mode and contact support team to analysis javascript console output.', 'wpgmp-google-map' ),
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'group', 'map_gdpr', array(
		'value'  => esc_html__( 'Cookies Acceptance', 'wpgmp-google-map' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'tutorial_link' => 'https://www.wpmapspro.com/topic/cookies/'
	)
);

$form->add_element(
	'checkbox', 'wpgmp_gdpr', array(
		'lable'   => esc_html__( 'Enable Cookies Acceptance', 'wpgmp-google-map' ),
		'value'   => 'true',
		'desc'    => esc_html__( 'Maps will remain hidden until the visitor accepts the cookie policy. This feature is intended for developers who wish to monitor cookies access and limit map access.', 'wpgmp-google-map' ),
		'current' => isset($wpgmp_settings['wpgmp_gdpr']) ? $wpgmp_settings['wpgmp_gdpr'] : "",
		'class'   => 'chkbox_class',
	)
);

$form->add_element(
	'textarea', 'wpgmp_gdpr_msg', array(
		'lable'                => esc_html__( '"No Map" Notice', 'wpgmp-google-map' ),
		'desc'                 => esc_html__( 'Show message instead of map until visitor accept the cookies policy. HTML Tags are allowed. Leave it blank for no message.', 'wpgmp-google-map' ),
		'value'                => isset($wpgmp_settings['wpgmp_gdpr_msg']) ? $wpgmp_settings['wpgmp_gdpr_msg'] : esc_html__( 'Please accept cookies to show google maps.', 'wpgmp-google-map' ),
		'textarea_fc-dividers' => 10,
		'textarea_name'        => 'wpgmp_gdpr_msg',
		'class'                => 'form-control wpgmp_gdpr_setting',
	)
);

$form->add_element(
	'checkbox', 'wpgmp_gdpr_show_placeholder', array(
		'lable'   => esc_html__( 'Show Placeholder', 'wpgmp-google-map' ),
		'value'   => 'true',
		'desc'    => esc_html__( 'Until you accept cookies, show a map image along with a consent message in place of Google Maps', 'wpgmp-google-map' ),
		'current' => isset($wpgmp_settings['wpgmp_gdpr_show_placeholder']) ? $wpgmp_settings['wpgmp_gdpr_show_placeholder'] : "",
		'class'   => 'chkbox_class',
	)
);

$form->add_element('submit','wpgmp_save_settings',array(
	'value' => esc_html__( 'Save Settings','wp-google-map-plugin' ),
	));
$form->add_element('hidden','operation',array(
	'value' => 'save',
	));
$form->add_element('hidden','page_options',array(
	'value' => 'wpgmp_api_key,wpgmp_scripts_place',
	));
$form->render();
$map_data['map_options'] = array(
	'center_lat' =>  '',
	'center_lng' =>  '',
);
$map_data['map_property'] = array('map_id' => 1);
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var map = $("#wpgmp_map_preview").maps("<?php echo esc_js( base64_encode( wp_json_encode( $map_data ) ) ); ?>").data('wpgmp_maps');
});
</script>

