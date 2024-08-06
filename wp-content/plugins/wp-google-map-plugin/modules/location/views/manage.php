<?php
  
$form  = new WPGMP_Template();
echo wp_kses_post( $form->show_header() );

if(!class_exists('WP_List_Table_Helper')){
	
	$tabular_file = WPGMP_CORE_CLASSES.'class.tabular.php';
	if ( file_exists( $tabular_file ) ) {
	   require_once( $tabular_file );
	}
	
}
    
if ( class_exists( 'WP_List_Table_Helper' ) && ! class_exists( 'WPGMP_Location_Table' ) ) {

	class WPGMP_Location_Table extends WP_List_Table_Helper {  public function __construct($tableinfo) {
			parent::__construct( $tableinfo ); }  }

	// Minimal Configuration :)
	global $wpdb;
	$columns   = array( 'location_title' => esc_html__( 'Location', 'wp-google-map-plugin' ),
	                    'location_address' => esc_html__( 'Location Address', 'wp-google-map-plugin' ),
	                    'location_latitude' => esc_html__( 'Latitude', 'wp-google-map-plugin' ),
	                    'location_longitude' => esc_html__( 'Longitude', 'wp-google-map-plugin' ) );
	                    
	$sortable  = array( 'location_title','location_address','location_latitude','location_longitude' );
	$tableinfo = array(
	'table' => $wpdb->prefix.'map_locations',
	'plugin' => 'wp-google-map-plugin', 
	'textdomain' => 'wp-google-map-plugin',
	'singular_label' => 'location',
	'plural_label' => 'locations',
	'admin_listing_page_name' => 'wpgmp_manage_location',
	'admin_add_page_name' => 'wpgmp_form_location',
	'primary_col' => 'location_id',
	'columns' => $columns,
	'sortable' => $sortable,
	'per_page' => 200,
	'form_id' => 'wpgmp_manage_locations',
	'form_class' => 'wpgmp_listing_form wpgmp_manage_locations',
	'actions' => array( 'edit','delete' ),
	'col_showing_links' => 'location_title',
	'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Locations', 'wp-google-map-plugin' ),
			'add_button'          => esc_html__( 'Add Location', 'wp-google-map-plugin' ),
			'delete_msg'          => esc_html__( 'Location was deleted successfully.', 'wp-google-map-plugin' ),
			'bulk_delete_msg'     => esc_html__( 'Locations were deleted successfully.', 'wp-google-map-plugin' ),
			'insert_msg'          => esc_html__( 'Location was added successfully.', 'wp-google-map-plugin' ),
			'update_msg'          => esc_html__( 'Location was updated successfully.', 'wp-google-map-plugin' ),
			'no_records_found'    => esc_html__( 'No locations were found.', 'wp-google-map-plugin' ),
		),
	);
	return new WPGMP_Location_Table( $tableinfo );

}
?>
