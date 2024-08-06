<?php
/**
 * Filters Setting(s).
 * @package Maps
 */

$form->add_element( 'group', 'map_listing_setting', array(
	'value' => esc_html__( 'Map Filters', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

$form->add_element( 
	'checkbox', 'map_all_control[display_listing]', array(
	'lable' => esc_html__( 'Display Filters', 'wp-google-map-plugin' ),
	'value' => 'true',
	'id' => 'wpgmp_display_listing',
	'current' => isset($_POST['map_all_control']['display_listing'])  ? sanitize_text_field($_POST['map_all_control']['display_listing']) : '',
	'desc' => esc_html__( 'Display filters below the map', 'wp-google-map-plugin' ),	
		'class'   => 'chkbox_class switch_onoff',
		'data'    => array( 'target' => '.wpgmp_display_listing' ),
));

$form->add_element( 'textarea', 'map_all_control[wpgmp_before_listing]', array(
	'lable' => esc_html__( 'Before Filters Heading', 'wp-google-map-plugin' ),

	'value' => ( isset( $_POST['map_all_control']['wpgmp_before_listing']) && !empty($_POST['map_all_control']['wpgmp_before_listing']) ) ? sanitize_text_field($_POST['map_all_control']['wpgmp_before_listing']) : esc_html__( 'Filter Locations By Category', 'wp-google-map-plugin' ),
	 'id' => 'before_listing',
	'desc' => esc_html__( 'Display a text/html content that will be displayed before filters.', 'wp-google-map-plugin' ),
	'textarea_rows' => 10,
		'textarea_name' => 'map_all_control[wpgmp_before_listing]',
		'class'         => 'form-control wpgmp_display_listing',
		'show'          => 'false',
		'default_value' => esc_html__( 'Map Locations', 'wp-google-map-plugin' ),
));

$form->add_element( 'checkbox', 'map_all_control[wpgmp_display_category_filter]', array(
	'lable' => esc_html__( 'Display Category Filter', 'wp-google-map-plugin' ),
	'value' => 'true',
	'id' => 'wpgmp_display_category_filter',
	'current' => (isset($_POST['map_all_control']['wpgmp_display_category_filter'])) ? sanitize_text_field($_POST['map_all_control']['wpgmp_display_category_filter']) : '',
	'desc' => esc_html__( 'Check to display category filter.', 'wp-google-map-plugin' ),
		'class'   => 'chkbox_class wpgmp_display_listing',
		'show'    => 'false',
));

