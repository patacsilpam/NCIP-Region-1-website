<?php
/**
 * Map's general setting(s).
 * @package Maps
 */

$form->add_element( 'text', 'map_title', array(
	'lable' => esc_html__( 'Map Title', 'wp-google-map-plugin' ),
	'value' => ( isset($_POST['map_title']) ) ? sanitize_text_field($_POST['map_title']) : '',
	'desc' => esc_html__( 'Enter map name / title here.', 'wp-google-map-plugin' ),
	'required' => true,
	'placeholder' => '',
));
$form->add_element( 'text', 'map_width', array(
	'lable' => esc_html__( 'Map Width', 'wp-google-map-plugin' ),
	'value' => (isset($_POST['map_width'])) ? sanitize_text_field($_POST['map_width']) : '',
	'desc' => esc_html__( 'Enter the map width in pixel. Leave this field blank if you need map with 100% width.', 'wp-google-map-plugin' ),
	'placeholder' => '',
));
$form->add_element( 'text', 'map_height', array(
	'lable' => esc_html__( 'Map Height', 'wp-google-map-plugin' ),
	'value' => (isset($_POST['map_height'])) ? sanitize_text_field($_POST['map_height']) : '',
	'desc' => esc_html__( 'Enter the map height in pixel. For eg. 450', 'wp-google-map-plugin' ),
	'required' => true,
	'placeholder' => '',
));

$zoom_level = array();
for ( $i = 0; $i < 20; $i++ ) {
	$zoom_level[ $i ] = $i;
}
$form->add_element( 'select', 'map_zoom_level', array(
	'lable' => esc_html__( 'Map Zoom Level', 'wp-google-map-plugin' ),
	'current' => (isset($_POST['map_zoom_level'])) ? sanitize_text_field($_POST['map_zoom_level']) : '5',
	'desc' => esc_html__( 'Specify map zoon level. Default zoom value is 5.', 'wp-google-map-plugin' ),
	'options' => $zoom_level,
	'default_value' => 5,
));

$map_type = array( 'ROADMAP' => 'ROADMAP','SATELLITE' => 'SATELLITE','HYBRID' => 'HYBRID','TERRAIN' => 'TERRAIN' );
$form->add_element( 'select', 'map_type', array(
	'lable' => esc_html__( 'Map Type', 'wp-google-map-plugin' ),
	'current' => (isset($_POST['map_type'])) ? sanitize_text_field($_POST['map_type']) : '',
	'options' => $map_type,
));

$form->add_element( 'checkbox', 'map_scrolling_wheel', array(
	'lable' => esc_html__( 'Turn Off Scrolling Wheel', 'wp-google-map-plugin' ),
	'value' => 'false',
	'id' => 'wpgmp_map_scrolling_wheel',
	'current' => (isset($_POST['map_scrolling_wheel'])) ? sanitize_text_field($_POST['map_scrolling_wheel']) : '',
	'desc' => esc_html__( 'Please check to disable scroll wheel zoom.', 'wp-google-map-plugin' ),
	'class' => 'chkbox_class ',
));
$form->add_element( 'checkbox', 'map_all_control[map_draggable]', array(
	'lable' => esc_html__( 'Map Draggable', 'wp-google-map-plugin' ),
	'value' => 'false',
	'id' => 'wpgmp_map_draggable',
	'current' => (isset($_POST['map_all_control']['map_draggable'])) ? sanitize_text_field($_POST['map_all_control']['map_draggable']) : '',
	'desc' => esc_html__( 'Please check to disable map draggable.', 'wp-google-map-plugin' ),
	'class' => 'chkbox_class',
));

$form->add_element( 'checkbox', 'map_45imagery', array(
	'lable' => esc_html__( '45&deg; Imagery', 'wp-google-map-plugin' ),
	'value' => '45',
	'id' => 'wpgmp_map_45imagery',
	'current' => (isset($_POST['map_45imagery'])) ? sanitize_text_field($_POST['map_45imagery']) : '',
	'desc' => esc_html__( 'Apply 45&deg; Imagery ? (only available for map type SATELLITE and HYBRID).', 'wp-google-map-plugin' ),
	'class' => 'chkbox_class',
));

