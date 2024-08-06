<?php
add_filter('wpgmp_accept_cookies','wpgmp_accept_cookies');

function check_cookieyes_consent() {
    if (isset($_COOKIE['cookieyes-consent'])) {
        $cookie_value = $_COOKIE['cookieyes-consent'];
        $cookie_data = urldecode($cookie_value);
        $consent_data = preg_match('/consent:([^,]+)/', $cookie_data, $matches);
        if (isset($matches[1]) && $matches[1] === 'yes') {
            // Consent is set to "yes"
            return true;
        } else {
            // Consent is not set to "yes"
            return false;
        }
    } else {
        // Cookie does not exist
        return false;
    }
}

function wpgmp_accept_cookies($is_allowed) {

	if (function_exists('cmplz_has_consent') && cmplz_has_consent('marketing')) {
		$is_allowed = true;
	} elseif (function_exists('gdpr_cookie_is_accepted') && gdpr_cookie_is_accepted('thirdparty')) {
		$is_allowed = true;
	} elseif (function_exists('cky_get_consent_db_version') && check_cookieyes_consent()) {
		$is_allowed = true;
	} elseif (class_exists('iubendaParser') && iubendaParser::consent_given()) {
		$is_allowed = true;
	} elseif (function_exists('cn_cookies_accepted') && (bool) Cookie_Notice::cookies_accepted()) {
		$is_allowed = true;
	}
	
	return $is_allowed;
}

function wpgmp_check_cookies_accepted(){
	$accepted = true;

	// Check if corresponding plugins are active and if cookies are accepted
	if (function_exists('cmplz_has_consent') && !cmplz_has_consent('marketing')) {
		$accepted = false;
	} elseif (function_exists('gdpr_cookie_is_accepted') && !gdpr_cookie_is_accepted('thirdparty')) {
		$accepted = false;
	} elseif (function_exists('cky_get_consent_db_version') && !check_cookieyes_consent()) {
		$accepted = false;
	} elseif (class_exists('iubendaParser') && !iubendaParser::consent_given()) {
		$accepted = false;
	} elseif (function_exists('cn_cookies_accepted') && !(bool) Cookie_Notice::cookies_accepted()) {
		$accepted = false;
	}

	$accepted = apply_filters('wpgmp_is_cookies_accepted',$accepted);

	return $accepted;

}

function wpgmp_show_placeholder($output, $map_id) {

	$wpgmp_settings = get_option( 'wpgmp_settings', true );
	if( !isset($wpgmp_settings['wpgmp_gdpr_show_placeholder']) or $wpgmp_settings['wpgmp_gdpr_show_placeholder'] != 'true'){
		return;
	}
	
	$show_placeholder = !wpgmp_check_cookies_accepted();
	if($show_placeholder) {
		$output ="<div class='wpgmp_map_container_placeholder'><div class='no-cookie-accepted'>".$wpgmp_settings['wpgmp_gdpr_msg']."</div></div>";
	}
	
	return $output;
}

add_filter('wpgmp_before_container', 'wpgmp_show_placeholder', 10, 2);

function wpgmp_hide_map_container($map_class, $map_id) {

	$wpgmp_settings = get_option( 'wpgmp_settings', true );
	if( !isset($wpgmp_settings['wpgmp_gdpr_show_placeholder']) or $wpgmp_settings['wpgmp_gdpr_show_placeholder'] != 'true'){
		return;
	}
	$show_placeholder = !wpgmp_check_cookies_accepted();

	if($show_placeholder) {
		$map_class = $map_class.' wpgmp_hide_map_container';
	}
	
	return $map_class;
}

add_filter('wpgmp_container_class', 'wpgmp_hide_map_container', 10, 2);