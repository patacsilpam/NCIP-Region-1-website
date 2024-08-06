<?php
/**
 * Contro Positioning over google maps.
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$positions = array(
'TOP_LEFT' => esc_html__( 'Top Left', 'wp-google-map-plugin' ),
'TOP_RIGHT' => esc_html__( 'Top Right', 'wp-google-map-plugin' ),
'LEFT_TOP' => esc_html__( 'Left Top','wp-google-map-plugin' ),
'RIGHT_TOP' => esc_html__( 'Right Top', 'wp-google-map-plugin' ),
'TOP_CENTER' => esc_html__( 'Top Center', 'wp-google-map-plugin' ),
'LEFT_CENTER' => esc_html__( 'Left Center', 'wp-google-map-plugin' ),
'RIGHT_CENTER' => esc_html__( 'Right Center', 'wp-google-map-plugin' ),
'BOTTOM_RIGHT' => esc_html__( 'Bottom Right', 'wp-google-map-plugin' ),
'LEFT_BOTTOM' => esc_html__( 'Left Bottom', 'wp-google-map-plugin' ),
'RIGHT_BOTTOM' => esc_html__( 'Right Bottom', 'wp-google-map-plugin' ),
'BOTTOM_CENTER' => esc_html__( 'Bottom Center', 'wp-google-map-plugin' ),
'BOTTOM_LEFT' => esc_html__( 'Bottom Left', 'wp-google-map-plugin' ),
'BOTTOM_RIGHT' => esc_html__( 'Bottom Right', 'wp-google-map-plugin' )
);

$form->add_element( 'group', 'map_control_position_setting', array(
	'value' => esc_html__( 'Control Position(s) Settings', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link'=>'https://www.wpmapspro.com/docs/different-type-of-control-position-settings-in-map/'
));

$form->add_element( 'select', 'map_all_control[zoom_control_position]', array(
	'lable' => esc_html__( 'Zoom Control', 'wp-google-map-plugin' ),
	'current' => (isset($_POST['map_all_control']['zoom_control_position'])) ? sanitize_text_field($_POST['map_all_control']['zoom_control_position']) : '',
	'desc' => esc_html__( 'Please select position of zoom control.', 'wp-google-map-plugin' ),
	'options' => $positions,
));
$zoom_control_style = array( 'LARGE' => 'Large','SMALL' => 'Small' );
$form->add_element( 'select', 'map_all_control[zoom_control_style]', array(
	'lable' => esc_html__( 'Zoom Control Style', 'wp-google-map-plugin' ),
	'current' => (isset($_POST['map_all_control']['zoom_control_style']))? sanitize_text_field($_POST['map_all_control']['zoom_control_style']) : '',
	'desc' => esc_html__( 'Please select style of zoom control.', 'wp-google-map-plugin' ),
	'options' => $zoom_control_style,
));

$form->add_element( 'select', 'map_all_control[map_type_control_position]', array(
	'lable' => esc_html__( 'Map Type Control', 'wp-google-map-plugin' ),
	'default_value' => 'TOP_RIGHT',
	'current' => (isset($_POST['map_all_control']['map_type_control_position'])) ? sanitize_text_field($_POST['map_all_control']['map_type_control_position']) : '',
	'desc' => esc_html__( 'Please select position of map type control.', 'wp-google-map-plugin' ),
	'options' => $positions,
));


$map_type_control_style = array( 'HORIZONTAL_BAR' => 'Horizontal Bar', 'DROPDOWN_MENU' => 'Dropdown Menu' );
$form->add_element( 'select', 'map_all_control[map_type_control_style]', array(
	'lable' => esc_html__( 'Map Type Control Style', 'wp-google-map-plugin' ),
	'current' => (isset($_POST['map_all_control']['map_type_control_style'])) ? sanitize_text_field($_POST['map_all_control']['map_type_control_style']) : '',
	'desc' => esc_html__( 'Please select style of map type control.', 'wp-google-map-plugin' ),
	'options' => $map_type_control_style,
));


$form->add_element( 'select', 'map_all_control[full_screen_control_position]', array(
	'lable' => esc_html__( 'Full Screen Control', 'wp-google-map-plugin' ),
	'default_value' => 'TOP_RIGHT',
	'current' => (isset($_POST['map_all_control']['full_screen_control_position'])) ? sanitize_text_field($_POST['map_all_control']['full_screen_control_position']) : '',
	'desc' => esc_html__( 'Please select position of full screen control.', 'wp-google-map-plugin' ),
	'options' => $positions,
));

$form->add_element( 'select', 'map_all_control[street_view_control_position]', array(
	'lable' => esc_html__( 'Street View Control', 'wp-google-map-plugin' ),
	'current' => ( isset($_POST['map_all_control']['street_view_control_position']) ) ? sanitize_text_field($_POST['map_all_control']['street_view_control_position']) : '',
	'desc' => esc_html__( 'Please select position of street view control.', 'wp-google-map-plugin' ),
	'options' => $positions,
));

// Search Control Position
$form->add_element( 'select', 'map_all_control[search_control_position]', array(
	'lable' => esc_html__( 'Search Control', 'wp-google-map-plugin' ),
	
	'current' => ( isset($_POST['map_all_control']['search_control_position']) ) ? sanitize_text_field($_POST['map_all_control']['search_control_position']) : '',
	'desc' => esc_html__( 'Please select position of search box control.', 'wp-google-map-plugin' ),
	'options' => $positions,
));
