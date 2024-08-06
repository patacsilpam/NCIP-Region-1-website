<?php

if( !class_exists('WP_Maps_Block') ) {

	class WP_Maps_Block{

		public  function __construct(){ $this->wpmb_register_hooks(); }

		private function wpmb_register_hooks(){

			add_action( 'init', 						       [ $this, 'wpmb_register_wp_maps_block'] );
			add_action( 'rest_api_init',				       [ $this, 'wpmb_register_rest_api_endpoints']);
			add_filter( 'script_loader_tag', 			       [ $this, 'wpmb_prefix_defer_js'], 10, 2 );
			
			if(is_admin()){

				add_action( 'admin_print_styles-post.php',     [ $this, 'wpmb_blockgallery_backend_scripts'] );
				add_action( 'admin_print_styles-post-new.php', [ $this, 'wpmb_blockgallery_backend_scripts'] );
				add_action( 'add_meta_boxes', 				   [ $this, 'wpmb_call_meta_box' ] );
				add_action( 'wp_insert_post', 				   [ $this, 'wpmb_updating_map_settings'], 10, 3);
				add_action( 'admin_footer', 				   [ $this, 'wpmb_update_metabox' ]);

			}
			
		}

		function wpmb_updating_map_settings($post_id, $post, $update) {

			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
				return;
			}

			if (wp_is_post_revision($post_id)) {
				return;
			}

			if( !function_exists( 'parse_blocks' ) ){
				return;	
			}
			
			$blocks = parse_blocks($post->post_content);
		
			if ( has_block('weplugins/wp-maps-block', $post ) ) {
				
				$blocks = parse_blocks($post->post_content);
				foreach ($blocks as $block) {
					
					if ($block['blockName'] === 'weplugins/wp-maps-block') {
						
						$attributes = $block['attrs'];
						$this->wpmb_update_map_by_block_attributes($attributes);
						break;
					}
				}
			}
		}

		function wpmb_get_full_predefined_color($color){

			$color_schema = array(
				'#29B6F6' => '#212121',
				'#212F3D' => '#212121',
				'#dd3333' => '#616161',
				'#FFB74D' => '#212121',
				'#FFC107' => '#616161',
				'#9C27B0' => '#616161',
				'#673AB7' => '#616161',
				'#3F51B5' => '#616161',
				'#00BCD4' => '#616161',
				'#009688' => '#616161',
				'#4CAF50' => '#616161',
				'#FF9800' => '#616161',
				'#FF5722' => '#616161',
				'#795548' => '#616161',
				'#9E9E9E' => '#616161',
			);

			return $color.'_'.$color_schema[$color];

		}

		function wpmb_update_map_by_block_attributes( $attributes ){
			
			global $wpdb;

			$map_id = $attributes['selectedMapId'];

			$modelFactory = new WPGMP_Model();
			$map_obj      = $modelFactory->create_object( 'map' );
			$map_obj = $map_obj->fetch( array( array( 'map_id', '=', intval( wp_unslash( $map_id ) ) ) ) );
			$map     = $map_obj[0];
			if ( ! empty( $map ) ) {

				$map_all_control = maybe_unserialize( $map->map_all_control );

				$map_width = $attributes['mapWidth']; 
				$map_height = $attributes['mapHeight']; 
				$map_zoom_level = $attributes['mapZoomLevel'];
				$map_snazzy_styles = $attributes['snazzyStyle'];
				$map_all_control['custom_style'] = $map_snazzy_styles;
				
				if(isset($attributes['preDefinedToggler']) && $attributes['preDefinedToggler']){

					$map_all_control['apply_custom_design'] = 'true';
					$map_all_control['apply_own_schema'] = '';
					$map_all_control['color_schema'] = $this->wpmb_get_full_predefined_color($attributes['preDefinedColor']);
				}else{
					$map_all_control['apply_custom_design'] = '';
				}

				if(isset($attributes['customDefinedToggler']) && $attributes['customDefinedToggler']){
					
					$map_all_control['apply_custom_design'] = '';
					$map_all_control['apply_own_schema'] = 'true';
					$map_all_control['wpgmp_primary_color'] = $attributes['themePrimaryColor'];
					$map_all_control['wpgmp_secondary_color'] = $attributes['themeSecondaryColor'];
				}else{
					$map_all_control['apply_own_schema'] = '';
				}

				$map_all_control = serialize( wp_unslash( $map_all_control ) );

				$map_new_data = array(
					'map_width' => $map_width,
					'map_height' => $map_height,
					'map_zoom_level' => $map_zoom_level,
					'map_all_control' => $map_all_control
				);

				$where = array( 'map_id' => $map_id	);
				$updated = $wpdb->update(TBL_MAP, $map_new_data, $where);
			}

			
			
		}

		function wpmb_register_wp_maps_block() {
		
			$api_key = get_option( 'wpgmp_api_key' );
			$api_key_found = false;
			if(!empty($api_key)){
				$api_key_found = true;
			}

			global $wpdb;
			$row = $wpdb->get_row("SELECT map_id FROM ".$wpdb->prefix."create_map LIMIT 1", ARRAY_A);
	
			wp_register_script(
				'wp-maps-block-script',
				WPGMP_URL.'build/index.js',
				array( 'wp-blocks', 'wp-element', 'wp-editor','wpgmp-google-map-main'),
				filemtime( WPGMP_DIR . 'build/index.js' ),
				true
			);

			if(!isset($row['map_id']) ){

				wp_localize_script('wp-maps-block-script', 'wpgmp_server_data',
				array( 'siteurl' => get_option('siteurl'),
					'namespace' => 'wpgmp' ,
					'version' => 'v1',
					'default_map_id' => '0',
					'source' => 'lite',
					'api_key' => $api_key_found,
					'rest_url' => esc_url_raw( rest_url() ),
					'nonce' => wp_create_nonce( 'wp_rest' )
				));

			}else{

				$default_map = (isset($row['map_id']) && !empty($row['map_id'])) ? $row['map_id'] : '';
				$modelFactory = new WPGMP_Model();
				$map_obj      = $modelFactory->create_object( 'map' );
				$map_record   = $map_obj->fetch( array( array( 'map_id', '=', $row['map_id'] ) ) );
				$map = $map_record[0];

				wp_localize_script('wp-maps-block-script', 'wpgmp_server_data',
				array( 'siteurl' => get_option('siteurl'),
					'namespace' => 'wpgmp' ,
					'version' => 'v1',
					'default_map_id' => $default_map,
					'default_map_object' => $map,
					'api_key' => $api_key_found,
					'source' => 'lite',
					'rest_url' => esc_url_raw( rest_url() ),
					'nonce' => wp_create_nonce( 'wp_rest' )
				));
	
			}
			
			wp_register_style(
				'wp-maps-block-editor-style',
				WPGMP_URL . 'build/index.css',
				array( 'wp-edit-blocks' ),
				filemtime( WPGMP_DIR . '/build/index.css' )
			);
			
			register_block_type(  WPGMP_DIR . '/build' , array(
				'editor_script' => [ 'wpgmp-google-map-main','wpgmp-google-api','wpgmp-frontend','wp-maps-block-script'],
				'editor_style' => ['wp-maps-block-editor-style'],
				'script' => ['jquery'],
				'render_callback' => [$this,'wpmb_render_dynamic_block_output'],
			) );
		
		}
		
		function wpmb_render_dynamic_block_output($attributes)  { 
		
			$map_id = $attributes['selectedMapId'];

			if(!empty($map_id)) {

				$modelFactory = new WPGMP_Model();
				$map_obj      = $modelFactory->create_object( 'map' );
				$map_record   = $map_obj->fetch( array( array( 'map_id', '=', $map_id ) ) );
				if(isset($map_record[0]) && !empty($map_record[0]) ){

					$map = $map_record[0];
					$maps_block_markup = '';
					
					if(!empty($map->map_width)){
						$maps_block_markup .= '<style>.wpgmp-dynamic-block-container{width:'.$map->map_width.'px;}</style>';
					}
					$maps_block_markup .= do_shortcode('[put_wpgm id='.$map_id.']');
					$classes = array('wp-block-wp-maps-block', 'wpgmp-dynamic-block-container');
					$wrapper_attributes = get_block_wrapper_attributes(array('class' => implode(' ', $classes)));
			
					return sprintf(
						'<div %1$s>%2$s</div>',
						$wrapper_attributes,
						$maps_block_markup
					);	
				}
				

			}else{
				return '';
			}
			
		}
		
		function wpmb_register_rest_api_endpoints(){
		
			register_rest_route( 'wpgmp/v1', 'markup/(?P<map_id>\d+)',array(

					'methods'  => 'GET',
					'callback' => [$this,'wpmb_get_map_by_id'],
					'permission_callback' => [$this,'wpmb_check_user_logged_in']
				
			));
		
			register_rest_route( 'wpgmp/v1', 'maplists',array(

					'methods'  => 'GET',
					'callback' => [$this,'wpmb_get_all_maplists'],
					'permission_callback' => [$this,'wpmb_check_user_logged_in']
			));
		
		}

		function wpmb_check_user_logged_in($request) {
		    
		    return ( is_user_logged_in() && current_user_can('manage_options') );
		    
		}
		
		function wpmb_get_all_maplists($request){
		
			$modelFactory = new WPGMP_Model();
			$shortcodes_obj = $modelFactory->create_object( 'map' );
			$all_shortcodes = $shortcodes_obj->fetch();
			$map_list = array();
			foreach($all_shortcodes as $map){
				$map_list[] = array('map_id' => $map->map_id, 'map_title' => $map->map_title);
			}
			$response = new WP_REST_Response($map_list);
			$response->set_status(200);
		
			return $response;
		
		}
		
		function wpmb_get_map_by_id($request) {
		
			$map_markup = '';
			$map_id = $request['map_id'];
		
			if (empty($map_id)) {
				return new WP_Error( 'empty_category', 'No maps are available to fetch and display', array('status' => 404) );
			}

			// Fetch Map Information.
			$modelFactory = new WPGMP_Model();
			$map_obj      = $modelFactory->create_object( 'map' );
			$map_record   = $map_obj->fetch( array( array( 'map_id', '=', $map_id ) ) );
			$map = $map_record[0];
			$map->map_all_control = maybe_unserialize($map->map_all_control);
			

			if(!empty($map->map_width)){
				$map_markup = '<style>.wpgmp-dynamic-block-container{width:'.$map->map_width.'px;}</style>';
			}

			$plugin_url = plugins_url( 'wp-google-map-plugin');
				
			if ( !empty( $map->map_all_control['location_infowindow_skin'] ) && is_array( $map->map_all_control['location_infowindow_skin'] )  ) { 
				
				$skin_data = $map->map_all_control['location_infowindow_skin'];
				$dynamic_style = "<link rel='stylesheet' id='fc-wpgmp-infowindow-" . $skin_data['name'] . "-css' href='".$plugin_url."/templates/infowindow/" . $skin_data['name'] . "/" . $skin_data['name'] . ".css' media='all' />";
				$dynamic_styles[] = $dynamic_style;
				
			}

			$map_markup .= do_shortcode('[put_wpgm id='.$map_id.']');
			$data = array('map_id' => $map_id, 'map_markup' => $map_markup, 'map_object' => $map, 'dynamic_styles' => $dynamic_styles );
			$response = new WP_REST_Response($data);
			$response->set_status(200);
		
			return $response;
			
		}


		function wpmb_blockgallery_backend_scripts() {	
			
			global $post;

			$wpgmp_settings = get_option( 'wpgmp_settings', true );
			
			if ( isset($wpgmp_settings['wpgmp_gdpr']) && $wpgmp_settings['wpgmp_gdpr'] == true ) {

				$wpgmp_accept_cookies = apply_filters( 'wpgmp_accept_cookies', false );
				if ( $wpgmp_accept_cookies == false ) {
					return;
				}
			}

			$auto_fix = '';
			
			if( isset($wpgmp_settings['wpgmp_auto_fix']) && !empty($wpgmp_settings['wpgmp_auto_fix'])) 	{
				
				$auto_fix = $wpgmp_settings['wpgmp_auto_fix'];
				if ( $auto_fix == 'true' ) {
					wp_enqueue_script( 'jquery' );
				}
			}

			wp_enqueue_style('wpgmp-frontend_css',WPGMP_CSS.'frontend.css');
     	    wp_enqueue_script( 'jquery' );
						
			$language = get_option( 'wpgmp_language' );

			if ( $language == '' )
				$language = 'en';
			if ( get_option( 'wpgmp_api_key' ) != '' ) {
				$google_map_api = 'https://maps.google.com/maps/api/js?key='.get_option( 'wpgmp_api_key' ).'&callback=wpgmpInitMap&libraries=geometry,places&language='.$language;
			} else {
				$google_map_api = 'https://maps.google.com/maps/api/js?&callback=wpgmpInitMap&libraries=geometry,places&language='.$language;
			}
						
			$where = get_option( 'wpgmp_scripts_place' );

			if ( $where == 'header' ) {
				$where = false;
			} else {
				$where = true;
			}

			$wpgmp_local = array();
			$wpgmp_local['all_location'] = esc_html__( 'All', 'wp-google-map-plugin' );
			$wpgmp_local['show_locations'] = esc_html__( 'Show Locations', 'wp-google-map-plugin' );
			$wpgmp_local['sort_by'] = esc_html__( 'Sort by', 'wp-google-map-plugin' );
			$wpgmp_local['wpgmp_not_working'] = esc_html__( 'Not working...', 'wp-google-map-plugin' );
			$wpgmp_local['select_category'] = esc_html__( 'Select Category', 'wp-google-map-plugin' );
			$wpgmp_local['place_icon_url'] = WPGMP_ICONS;
			$wpgmp_local['wpgmp_assets'] = WPGMP_JS;

			$scripts = array(); 

			$scripts[] = array(
			'handle'  => 'wpgmp-google-map-main',
			'src'   => WPGMP_JS.'maps.js',
			'deps'    => array(),
			);

			$scripts[] = array(
			'handle'  => 'wpgmp-google-api',
			'src'   => $google_map_api,
			'deps'    => array('wpgmp-google-map-main'),
			);

			$scripts[] = array(
				'handle'  => 'wpgmp-frontend',
				'src'   => WPGMP_JS.'/minified/wpgmp_frontend.min.js',
				'deps'    => array('wpgmp-google-api'),
				);

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					if ( $auto_fix == 'true' ) {
						wp_enqueue_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
					} else {
						wp_register_script( $script['handle'], $script['src'], $script['deps'], WPGMP_VERSION, $where );
					}
				}
			}

			wp_localize_script( 'wpgmp-google-map-main', 'wpgmp_local',$wpgmp_local );
			wp_register_style('wpgmp-frontend_css',WPGMP_CSS.'frontend.css');
			if ( $auto_fix == 'true' ) {
               wp_enqueue_style('wpgmp-frontend_css'); 
			}			
			wp_enqueue_style('wp-maps-block-editor-style');
		}
		
		function wpmb_prefix_defer_js( $tag, $handle ) {
		
			$scripts_to_defer = array( 'wpgmp-google-map-main', 'wpgmp-google-api', 'wpgmp-frontend' );
			if ( is_admin() && in_array( $handle, $scripts_to_defer ) ) {
				return str_replace( '></script>', ' defer></script>', $tag );
			}
			return $tag;
		
		}

		function wpmb_call_meta_box() {
	
			$screens        = array( 'post', 'page' );
			$args = array( 'public'   => true,'_builtin' => false );
			$custom_post_types = get_post_types( $args, 'names' );
			$screens = array_merge( $screens, $custom_post_types );
			foreach ( $screens as $screen ) {
				add_meta_box(
					'wp_maps_block_metabox',
					esc_html__( 'WP Maps Block', 'wpgmp-google-map' ),
					array( $this, 'wpmb_add_meta_box' ),
					$screen
				);
			}
		}

		function wpmb_add_meta_box( $post ) {}

		function wpmb_update_metabox(){
			?><style>#wp_maps_block_metabox{display:none;}</style><?php
		}

	}

	return new WP_Maps_Block();

}