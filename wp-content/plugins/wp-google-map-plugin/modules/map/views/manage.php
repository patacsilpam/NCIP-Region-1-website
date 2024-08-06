<?php
/**
 * Manage Maps
 * @package Maps
 */
  $form  = new WPGMP_Template();
  echo wp_kses_post( $form->show_header() );
  
  if(!class_exists('WP_List_Table_Helper')){
	
	$tabular_file = WPGMP_CORE_CLASSES.'class.tabular.php';
	if ( file_exists( $tabular_file ) ) {
	   require_once( $tabular_file );
	}
	
  }

if ( class_exists( 'WP_List_Table_Helper' ) and ! class_exists( 'WPGMP_Maps_Table' ) ) {

	/**
	 * Display maps manager.
	 */
	class WPGMP_Maps_Table extends WP_List_Table_Helper {
		/**
	  	 * Intialize manage category table.
	  	 * @param array $tableinfo Table's properties.
	  	 */
		public function __construct($tableinfo) {
			parent::__construct( $tableinfo ); }
		/**
	  	 * Output for Shortcode column.
	  	 * @param array $item Map Row.
	  	 */
		public function column_shortcodes($item) {
			echo esc_html('[put_wpgm id='.$item->map_id.']');	}
		/**
		 * Clone of the map.
		 * @param  integer $item Map ID.
		 */
		public function copy() {
			
			$this->authenticate_action_requests();
			$map_id = intval( $_GET['map_id'] );
			$modelFactory = new WPGMP_Model();
			$map_obj = $modelFactory->create_object( 'map' );
			$map = $map_obj->copy( $map_id );
			$this->prepare_items();
			$this->listing();
		}

	}

	global $wpdb;
	$columns = array(
	'map_title'  => esc_html__('Map Title','wp-google-map-plugin'),
	'map_width' => esc_html__('Map Width','wp-google-map-plugin'),
	'map_height' => esc_html__('Map Height','wp-google-map-plugin'),
	'map_zoom_level' => esc_html__('Map Zoom Level','wp-google-map-plugin'),
	'map_type' => esc_html__('Map Type','wp-google-map-plugin'),
	'shortcodes' => esc_html__('Map Shortcode','wp-google-map-plugin')
	);
	$sortable  = array( 'map_title','map_width','map_height','map_zoom_level','map_type' );
	$tableinfo = array(
	'table' => $wpdb->prefix.'create_map',
	'plugin' => 'wp-google-map-plugin',
	'textdomain' => 'wp-google-map-plugin',
	'singular_label' => 'map',
	'plural_label' => 'maps',
	'admin_listing_page_name' => 'wpgmp_manage_map',
	'admin_add_page_name' => 'wpgmp_form_map',
	'primary_col' => 'map_id',
	'columns' => $columns,
	'sortable' => $sortable,
	'per_page' => 20,
	'form_id' => 'wpgmp_manage_maps',
	'form_class' => 'wpgmp_listing_form wpgmp_manage_maps',
	'actions' => array( 'edit','delete','copy' ),
	'bulk_actions' => array( 'delete' => esc_html__( 'Delete', 'wp-google-map-plugin' ) ),
	'col_showing_links' => 'map_title',
	'searchExclude' => array( 'shortcodes' ),
	'translation' => array(
			'manage_heading'      => esc_html__( 'Manage Maps', 'wp-google-map-plugin' ),
			'add_button'          => esc_html__( 'Add Map', 'wp-google-map-plugin' ),
			'delete_msg'          => esc_html__( 'Maps was deleted successfully.', 'wp-google-map-plugin' ),
			'bulk_delete_msg'     => esc_html__( 'Maps were deleted successfully.', 'wp-google-map-plugin' ),
			'insert_msg'          => esc_html__( 'Map was created successfully.', 'wp-google-map-plugin' ),
			'update_msg'          => esc_html__( 'Map was updated successfully.', 'wp-google-map-plugin' ),
			'no_records_found'    => esc_html__( 'No maps were found.', 'wp-google-map-plugin' ),
	)
	);
	$obj = new WPGMP_Maps_Table( $tableinfo );
}
