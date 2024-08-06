<?php
/**
 * Map's Center Location setting(s).
 * @package Maps
 */

$form->add_element( 'group', 'map_center_setting', array(
	'value' => esc_html__( 'Map\'s Center', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link' => 'https://www.wpmapspro.com/topic/center-location/'
));

$form->add_element( 'text', 'map_all_control[map_center_latitude]', array(
	'lable' => esc_html__( 'Center Latitude', 'wp-google-map-plugin' ),
	'value' => (isset($_POST['map_all_control']['map_center_latitude'])) ? sanitize_text_field($_POST['map_all_control']['map_center_latitude']) : '',
	'desc' => esc_html__( 'Enter the center latitude for map.', 'wp-google-map-plugin' ),
	'placeholder' => '',
));
$form->add_element( 'text', 'map_all_control[map_center_longitude]', array(
	'lable' => esc_html__( 'Center Longitude', 'wp-google-map-plugin' ),
	'value' => (isset($_POST['map_all_control']['map_center_longitude'])) ? sanitize_text_field($_POST['map_all_control']['map_center_longitude']) : '',
	'desc' => esc_html__( 'Enter the center longitude for map.', 'wp-google-map-plugin' ),
	'placeholder' => '',
));

$form->add_element(
	'checkbox', 'map_all_control[fit_bounds]', array(
		'lable'   => esc_html__( 'Center by Assigned Locations', 'wpgmp-google-map' ),
		'value'   => 'true',
		'class'   => 'chkbox_class',
		'id'      => 'wpgmp_fit_bounds_location',
		'current' => isset( $data['map_all_control']['fit_bounds'] ) ? $data['map_all_control']['fit_bounds'] : '',
		'desc'    => esc_html__( 'Center the map based on locations assigned to the map to show all locations at once.', 'wpgmp-google-map' ).'<br><b>'.esc_html__('( Most recommended way for centering the map according to assigned locations. )', 'wpgmp-google-map').'</b>',
	)
);
