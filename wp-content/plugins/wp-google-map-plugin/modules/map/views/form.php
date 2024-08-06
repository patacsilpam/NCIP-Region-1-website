<?php
/**
 * Template for Add & Edit Map
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

if(function_exists('wp_enqueue_code_editor'))
      wp_enqueue_code_editor(array('type' => 'text/html'));

global $wpdb;
$modelFactory = new WPGMP_Model();
$map_obj = $modelFactory->create_object( 'map' );
if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_key($_GET['doaction']) && isset( $_GET['map_id'] ) ) {
	
	$map_obj = $map_obj->fetch( array( array( 'map_id', '=', intval( wp_unslash( $_GET['map_id'] ) ) ) ) );
	$map = $map_obj[0];
	if(!empty($map)) {
		$map->map_street_view_setting = maybe_unserialize( $map->map_street_view_setting );
		$map->map_route_direction_setting = maybe_unserialize( $map->map_route_direction_setting );
		$map->map_all_control = maybe_unserialize( $map->map_all_control );
		$map->map_info_window_setting = maybe_unserialize( $map->map_info_window_setting );
		$map->style_google_map = maybe_unserialize( $map->style_google_map );
		$map->map_locations = maybe_unserialize( $map->map_locations );
		$map->map_layer_setting = maybe_unserialize( $map->map_layer_setting );
		$map->map_polygon_setting = maybe_unserialize( $map->map_polygon_setting );
		$map->map_polyline_setting = maybe_unserialize( $map->map_polyline_setting );
		$map->map_cluster_setting = maybe_unserialize( $map->map_cluster_setting );
		$map->map_overlay_setting = maybe_unserialize( $map->map_overlay_setting );
		$map->map_infowindow_setting = maybe_unserialize( $map->map_infowindow_setting );
		$map->map_geotags = maybe_unserialize( $map->map_geotags );
		$data = (array) $map;
	}
	
	$_POST = (array) $map;
} elseif ( ! isset( $_GET['doaction'] ) && isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}

$form  = new WPGMP_Template();
if( isset($_GET['doaction']) && sanitize_key($_GET['doaction']) == 'edit' ) {
	$edit_mode_params = array( 'page' => 'wpgmp_form_map','doaction' => 'edit', 'map_id' => intval( $_GET['map_id'] )  );
	$form->form_action = esc_url ( add_query_arg( $edit_mode_params , esc_url( admin_url ('admin.php') ) ) );
}else{
	$form->form_action = esc_url ( add_query_arg( 'page', 'wpgmp_form_map', admin_url ('admin.php') )  );	
}
$form->set_header( esc_html__( 'Map Information', 'wp-google-map-plugin' ), $response, $enable_accordion = true, esc_html__( 'Manage Maps', 'wp-google-map-plugin' ), 'wpgmp_manage_map' );

$form->add_element( 'group', 'wpgmp_map_info', array(
	'value' => esc_html__( 'Map Information', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link'=> 'https://www.wpmapspro.com/docs/how-to-create-a-map/'
));

$form->add_element('hidden', 'form_action_url', array('value' => $form->form_action ) );

if( get_option( 'wpgmp_api_key' ) == '' ) {

$link = '<a href="https://www.wpmapspro.com/docs/how-to-create-an-api-key/" class="wpgmp_map_key_missing" target="_blank">'.esc_html__("create google maps api key","wp-google-map-plugin").'</a>';
$setting_link = '<a target="_blank" href="' . admin_url( 'admin.php?page=wpgmp_manage_settings' ) . '">'.esc_html__("here","wp-google-map-plugin").'</a>';
	
$form->add_element( 'message', 'wpgmp_key_required', array(
	'value'  => sprintf( esc_html__( 'Google Maps API Key is missing. Follow instructions to %1$s and then insert your key %2$s.', 'wp-google-map-plugin' ), $link, $setting_link ),
	'class' => 'fc-msg fc-danger',
	'before' => '<div class="fc-12 wpgmp_key_required">',
	'after' => '</div>',
));

}

include( 'map-forms/general-setting-form.php' );
require 'map-forms/mobile-specific-settings.php';
include( 'map-forms/map-center-settings.php' );
include( 'map-forms/locations-form.php' );
include('map-forms/google-maps-amenities.php');
include( 'map-forms/control-setting-form.php' );
include( 'map-forms/control-position-style-form.php' );
include('map-forms/custom-control-form.php');
include('map-forms/filter-settings.php');

include('map-forms/layers-form.php');
include('map-forms/geotag-form.php');
include('map-forms/map-style-setting-form.php');
include('map-forms/street-view-setting-form.php' );
include('map-forms/route-direction-form.php');
include('map-forms/marker-cluster-setting-form.php');
include('map-forms/overlay-setting-form.php');
include('map-forms/limit-panning-setting-form.php');
include('map-forms/tab-setting-form.php');
include('map-forms/listing-setting-form.php');
include('map-forms/map-ui.php');
include('map-forms/url-filter.php');
include('map-forms/import-maps.php');

include( 'map-forms/extensible-settings.php' );
include('map-forms/additional-styling-form.php');
$form->add_element('extensions','wpgmp_map_form',array(
	'value' => (isset($data)) ? $data : array(),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	));
$form->add_element( 'submit', 'save_entity_data', array(
	'value' => esc_html__( 'Save Map','wp-google-map-plugin' ),
));
$form->add_element( 'hidden', 'operation', array(
	'value' => 'save',
));
$form->add_element( 'hidden', 'map_locations', array(
	'value' => '',
));
$form->add_element(
	'hidden', 'map_all_control[fc_custom_styles]', array(
		'value' => '',
		'id'    => 'fc_custom_styles',
	)
);
if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

	$form->add_element( 'hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['map_id'] ) ),
	));
}
$form->render();
