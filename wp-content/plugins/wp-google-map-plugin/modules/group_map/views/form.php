<?php
/**
 * Template for Add & Edit Category
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$category = $modelFactory->create_object( 'group_map' );
$categories = (array) $category->fetch();
if ( isset( $_GET['doaction'] ) &&  'edit' == sanitize_key($_GET['doaction']) && isset( $_GET['group_map_id'] ) ) {
	$category_obj   = $category->fetch( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
	$_POST = (array) $category_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) && isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}
$current_cat_id = '';
$form  = new WPGMP_Template();
if( isset($_GET['doaction']) && $_GET['doaction'] == 'edit') {
	$edit_mode_params = array( 'page' => 'wpgmp_form_group_map','doaction' => 'edit', 'group_map_id' => intval( $_GET['group_map_id'] )  );
	$form->form_action = esc_url ( add_query_arg( $edit_mode_params , esc_url( admin_url ('admin.php') ) ) );
	$current_cat_id = $_GET['group_map_id'];
}else{
	$form->form_action = esc_url ( add_query_arg( 'page', 'wpgmp_form_group_map', admin_url ('admin.php') )  );	
}

$form->set_header( esc_html__( 'Marker Category', 'wp-google-map-plugin' ), $response, $enable_accordion = false, esc_html__( 'Manage Marker Categories', 'wp-google-map-plugin' ), 'wpgmp_manage_group_map' );

$form->add_element( 'group', 'wpgmp_marker_cat', array(
	'value' => esc_html__( 'Marker Category', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link' => 'https://www.wpmapspro.com/docs/how-to-create-a-marker-categories/'
));

if ( is_array( $categories ) ) {
	$markers = array( ' ' => 'Please Select' );
	foreach ( $categories as $i => $single_category ) {
		
		if($single_category->group_map_id == $current_cat_id)
			continue;

		$markers[ $single_category->group_map_id ] = $single_category->group_map_title;
	}

	$form->add_element('select', 'group_parent', array(
		'lable' => esc_html__( 'Parent Category', 'wp-google-map-plugin' ),
		'current' => (isset( $_POST['group_parent'] ) && ! empty( $_POST['group_parent'] )) ? intval( wp_unslash( $_POST['group_parent'] ) ) : '',
		'desc' => esc_html__( 'You can optionally assign a parent marker category to the new marker category you are creating. Assign parent category if you want.', 'wp-google-map-plugin' ),
		'options' => $markers,
	));

}

$form->add_element('text', 'group_map_title', array(
	'lable' => esc_html__( 'Marker Category Title', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['group_map_title'] ) && ! empty( $_POST['group_map_title'] )) ? sanitize_text_field( wp_unslash( $_POST['group_map_title'] ) ) : '',
	'id' => 'group_map_title',
	'desc' => esc_html__( 'Enter marker category name / title here.', 'wp-google-map-plugin' ),
	'class' => 'create_map form-control',
	'placeholder' => esc_html__( 'Marker Category Title', 'wp-google-map-plugin' ),
	'required' => true,
));


$form->add_element('image_picker', 'group_marker', array(
	'lable' => esc_html__( 'Select Marker Category Icon', 'wp-google-map-plugin' ),
	'src' => (isset( $_POST['group_marker'] ) ) ? wp_unslash( $_POST['group_marker'] ) : WPGMP_IMAGES.'/default_marker.png',
	'required' => true,
	'choose_button' => esc_html__( 'Choose', 'wp-google-map-plugin' ),
	'remove_button' => esc_html__( 'Remove','wp-google-map-plugin' ),
	'id' => 'marker_category_icon',
));

$form->set_col( 1 );


$form->add_element('extensions','wpgmp_category_form',array(
	'value' => (isset($_POST['extensions_fields'])) ? $_POST['extensions_fields'] : array(),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	));


$form->add_element('submit', 'create_group_map_location', array(
	'value' => 'Save Marker Category',
	'before' => '<div class="fc-12">',
	'after' => '</div>'

));

$form->add_element('hidden', 'operation', array(
	'value' => 'save',
));

if ( isset( $_GET['doaction'] ) and  'edit' == $_GET['doaction'] ) {
	$form->add_element('hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['group_map_id'] ) ),
	));
}

$form->render();
