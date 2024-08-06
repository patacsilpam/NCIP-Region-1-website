<?php
/*
Plugin Name: WP Maps
Plugin URI: https://weplugins.com/
Description: A fully customizable WordPress Plugin for Google Maps. Create unlimited Google Maps Shortcodes, assign unlimited locations with custom infowindow messages and add to pages, posts and widgets.
Author: flippercode
Author URI: https://weplugins.com/
Version: 4.6.5
Text Domain: wp-google-map-plugin
Domain Path: /lang
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

if ( ! class_exists( 'FC_Google_Maps_Lite' ) ) {

	/**
	 * Main plugin class
	 * @author Flipper Code <hello@flippercode.com>
	 * @package Maps
	 */
	class FC_Google_Maps_Lite
	{
		/**
		 * List of Modules.
		 * @var array
		 */
		private $modules = array();
		/**
		 * Intialize variables, files and call actions.
		 * @var array
		 */
		public function __construct() {
			
			$this->wpgmp_define_constants();
			$this->wpgmp_load_files();

			register_activation_hook( __FILE__, array( $this, 'wpgmp_plugin_activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'wpgmp_plugin_deactivation' ) );
			if( is_multisite() ){
				
			  add_action( 'wpmu_new_blog',    array( $this, 'wpgmp_on_blog_new_generate'), 10, 6 );
              add_filter( 'wpmu_drop_tables', array( $this, 'wpgmp_on_blog_delete') );
              
			}
			add_action( 'plugins_loaded',     array( $this, 'wpgmp_load_plugin_languages' ) );
			add_action( 'init',               array( $this, 'wpgmp_init' ) );
			add_action( 'widgets_init',       array( $this, 'wpgmp_google_map_widget' ) );
			
		}
		
		/**
		 * Call WordPress hooks.
		 */
		function wpgmp_init() {

			add_action( 'media_upload_ell_insert_gmap_tab', array( $this, 'wpgmp_google_map_media_upload_tab' ) );
			add_action( 'wp_enqueue_scripts',               array( $this, 'wpgmp_frontend_scripts' ) );
			add_shortcode( 'put_wpgm',                      array( $this, 'wpgmp_show_location_in_map' ) );
			add_shortcode( 'display_map',                   array( $this, 'wpgmp_display_map' ) );
			add_filter( 'fc-dummy-placeholders', 		    array( $this, 'wpgmp_apply_placeholders') );
			
			if(is_admin()){
				add_action( 'admin_head', 				  	array( $this, 'wpgmp_customizer_font_family' ) );
				add_action( 'admin_menu',                   array( $this, 'wpgmp_create_menu' ) );
				add_filter( 'media_upload_tabs',            array( $this, 'wpgmp_google_map_tabs_filter' ) );
				add_action( 'admin_enqueue_scripts',        array( $this, 'wpgmp_overview_page_styles') );
				add_filter( 'plugin_action_links_'. plugin_basename(__FILE__), array($this, 'wpgmp_go_pro_link'), 10, 1 );
				add_filter( 'fc_after_plugin_header',       array( $this, 'wpgmp_show_buynow_notice') );
				add_action( 'admin_head', array( $this, 'wpgmp_custom_css_admin_head' ) );
				add_action( 'wp_ajax_wpgmp_hide_buy_notice', array( $this, 'wpgmp_hide_buy_notice' ) );
			    add_action( 'wp_ajax_nopriv_wpgmp_hide_buy_notice', array( $this, 'wpgmp_hide_buy_notice' ) );
				add_action('wp_ajax_wpgmp_hide_sample_notice', array($this, 'wpgmp_hide_sample_notice'));
				add_action('wp_ajax_nopriv_wpgmp_hide_sample_notice', array($this, 'wpgmp_hide_sample_notice'));
				add_action('wp_ajax_wpgmp_submit_uninstall_reason_action', array($this, 'wpgmp_submit_uninstall_reason_action_perform'));
				add_action('wp_ajax_nopriv_wpgmp_submit_uninstall_reason_action', array($this, 'wpgmp_submit_uninstall_reason_action'));
			}
			
		}

		function wpgmp_submit_uninstall_reason_action_perform(){

			
		    global  $wp_version, $current_user;
		    wp_verify_nonce($_REQUEST['wpgmp_ajax_nonce'], 'wpgmp_ajax_nonce');
			$reason_id = isset($_REQUEST['reason_id']) ? stripcslashes(sanitize_text_field($_REQUEST['reason_id'])) : '';
		    $basename  = isset($_REQUEST['plugin']) ? stripcslashes(sanitize_text_field($_REQUEST['plugin'])) : '';

		    if (empty($reason_id) || empty($basename)) {
		        exit;
		    }

		    $reason_info = isset($_REQUEST['reason_info']) ? stripcslashes(sanitize_textarea_field($_REQUEST['reason_info'])) : '';
		    if (!empty($reason_info)) {
		        $reason_info = substr($reason_info, 0, 255);
		    }
		    $is_anonymous = isset($_REQUEST['is_anonymous']) && 1 == $_REQUEST['is_anonymous'];


		    $options = array(
		        'product'     => 'WP Maps Plugin',
		        'reason_id'   => $reason_id,
		        'reason_info' => $reason_info,
		    );

		    if (!$is_anonymous) {
		        $options['url']                  = get_site_url();
		        $options['wp_version']           = $wp_version;
		        $options['plugin_version']       = WPGMP_VERSION;
		        $options['email'] = $current_user->data->user_email;
		    }

	        wp_remote_post(
		        "https://weplugins.com/wp-json/weplugins/v1/plugin-deactivate",
		        array(
		            'method'  => 'POST',
		            'body'    => $options,
		            'timeout' => 15,
		        )
		    );
		    exit;
	    

		}
 
		/**
		 * Register WP Google Map Widget
		 */
		function wpgmp_google_map_widget() { register_widget( 'WPGMP_Google_Map_Widget_Class' ); } 

		function wpgmp_customizer_font_family() {

			if ( isset( $_GET['doaction'] ) and 'edit' == $_GET['doaction'] and isset( $_GET['map_id'] ) ) {

			    $modelFactory = new WPGMP_Model();
				$map_obj      = $modelFactory->create_object( 'map' );
				$styles_and_scripts = $map_obj->get_map_customizer_style();
				$font_families   = $styles_and_scripts['font_families'];
				$fc_skin_styles  = $styles_and_scripts['fc_skin_styles']; 
				if ( ! empty( $fc_skin_styles ) ) {
					echo '<style>' . esc_html($fc_skin_styles) . '</style>';
				}
				if ( ! empty( $font_families ) ) {
					$font_families = array_unique($font_families);
					?>
					<script type="text/javascript">
						var google_customizer_fonts = <?php echo json_encode($font_families,JSON_FORCE_OBJECT);?>;
					</script>
				<?php }

			}

		}


		function wpgmp_go_pro_link($actions) {
			
			$actions['settings'] = '<a href="' . admin_url( 'admin.php?page=wpgmp_manage_settings' ) . '">'.esc_html__( 'Settings', 'wp-google-map-plugin' ).'</a>';
			$actions['go_pro'] = '<a style="color:#2ea100;" target = "_blank" href="https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium">'.esc_html__( 'Pro Version', 'wp-google-map-plugin' ).'</a>';
			
			
			return $actions;
		}

		function wpgmp_custom_css_admin_head() {

			$modelFactory = new WPGMP_Model();
			$location_obj = $modelFactory->create_object( 'location' );
			$locations = $location_obj->fetch();
			if(is_array($locations) && count($locations) > 0) { ?>
				<style>.wp-list-table .locations{ margin-top: 40px; } </style> <?php
			}
		}
		
		/**
		 * Eneque script at frontend.
		 */
		function wpgmp_frontend_scripts() {

			global $post;

			$wpgmp_settings = get_option( 'wpgmp_settings', true );
		
			// Hook accept cookies
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

    		// Check if the current page content has the shortcode [put_wpgm id=X]
			if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'put_wpgm' ) && preg_match( '/\[put_wpgm id=\d+\]/', $post->post_content ) ) {
     			  wp_enqueue_style('wpgmp-frontend_css',WPGMP_CSS.'frontend.css');
     			  wp_enqueue_script( 'jquery' );
			}
						
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
+               wp_enqueue_style('wpgmp-frontend_css'); 
			}

		}
		/**
		 * Display google maps at the frontend using put_wpgmp shortcode.
		 * @param  array  $atts   Map Options.
		 * @param  string $content Content.
		 */
		function wpgmp_show_location_in_map($atts, $content = null) {
			
			if(!isset($atts['id']) || empty($atts['id']))
				return '';

			$atts['id'] = sanitize_text_field($atts['id']); 

    	    if (!is_numeric($atts['id'])) {
		        return '';
		    }
		    
		    $atts['id'] = intval($atts['id']);
		    if ($atts['id'] <= 0) {
		        return '';
		    }
		    
			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( 'shortcode' );
				$output = $viewObject->display( 'put-wpgmp',$atts );
				 return $output;

			} catch (Exception $e) {
				echo wp_kses_post( WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) ) );

			}

		}
		/**
		 * Display google maps at the frontend using display_map shortcode.
		 * @param  array $atts    Map Options.
		 */
		function wpgmp_display_map($atts) {

			try {
				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( 'shortcode' );
				 $output = $viewObject->display( 'display-map',$atts );
				 return $output;

			} catch (Exception $e) {
				echo wp_kses_post( WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) ) );

			}

		}
		/**
		 * Process slug and display view in the backend.
		 */
		function wpgmp_processor() {
			
			$return = '';
			if ( isset( $_GET['page'] ) ) {
				$page = sanitize_key( wp_unslash( $_GET['page'] ) );
			} else {
				$page = 'wpgmp_view_overview';
			}

			$pageData = explode( '_', $page );
			$obj_type = $pageData[2];
			$obj_operation = $pageData[1];

			if ( count( $pageData ) < 3 ) {
				die( 'Cheating!' );
			}

			try {
				if ( count( $pageData ) > 3 ) {
					$obj_type = $pageData[2].'_'.$pageData[3];
				}

				$factoryObject = new WPGMP_Controller();
				$viewObject = $factoryObject->create_object( $obj_type );
				$viewObject->display( $obj_operation );

			} catch (Exception $e) {
				echo wp_kses_post( WPGMP_Template::show_message( array( 'error' => $e->getMessage() ) ) );

			}

		}
		/**
		 * Create backend navigation.
		 */
		function wpgmp_create_menu() {

			global $navigations;

			$pagehook1 = add_menu_page(
				esc_html__( 'WP MAPS', 'wp-google-map-plugin' ),
				esc_html__( 'WP MAPS', 'wp-google-map-plugin' ), 
				'wpgmp_admin_overview',
				WPGMP_SLUG,
				array( $this,'wpgmp_processor' ),
				esc_url( WPGMP_IMAGES.'flippercode.png' )
			);

			if ( current_user_can( 'manage_options' )  ) {
				$role = get_role( 'administrator' );
				$role->add_cap( 'wpgmp_admin_overview' );
			}

			$this->wpgmp_load_modules_menu();

			add_action( 'load-'.$pagehook1, array( $this, 'wpgmp_backend_scripts' ) );

		}
		/**
		 * Read models and create backend navigation.
		 */
		function wpgmp_load_modules_menu() {

			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {

						$object = new $module;

					if ( method_exists( $object,'navigation' ) ) {

						if ( ! is_array( $object->navigation() ) )
						continue;
						
						foreach ( $object->navigation() as $nav => $title ) {

							if ( current_user_can( 'manage_options' ) && is_admin() ) {
								$role = get_role( 'administrator' );
								$role->add_cap( $nav );

							}

							$pagehooks[] = add_submenu_page(
								WPGMP_SLUG,
								$title,
								$title,
								$nav,
								$nav,
								array( $this,'wpgmp_processor' )
							);
						}
					}
				}
			}

			if ( is_array( $pagehooks ) ) {

				foreach ( $pagehooks as $key => $pagehook ) {
					add_action( 'load-'.$pagehooks[ $key ], array( $this, 'wpgmp_backend_scripts' ) );
				}
			}

		}

		
		/**
		 * Eneque scripts in the backend.
		 */
		function wpgmp_backend_scripts() {

			$screen = get_current_screen();

			$api_key = get_option( 'wpgmp_api_key' );

			if ( $api_key != '' ) {
				$google_map_api = 'https://maps.google.com/maps/api/js?key='.$api_key.'&callback=wpgmpInitMap&libraries=geometry,places,drawing&language=en';
			} else {
				$google_map_api = 'https://maps.google.com/maps/api/js?&callback=wpgmpInitMap&libraries=geometry,places,drawing&language=en';
			}

			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'wp-color-picker' );
			$wp_scripts = array( 'jQuery','thickbox', 'wp-color-picker');

			if ( $wp_scripts ) {
				foreach ( $wp_scripts as $wp_script ) {
					wp_enqueue_script( $wp_script );
				}
			}

			$scripts = array();
			
			$scripts[] = array(
				'handle'  => 'wpgmp-map',
				'src'   => WPGMP_JS.'maps.js',
				'deps'    => array(),
			);

			if(isset($screen) && !empty($screen) && $screen->id != 'wp-maps_page_wpgmp_manage_settings'){
				$scripts[] = array(
					'handle'  => 'wpgmp-backend-google-api',
					'src'   => $google_map_api,
					'deps'    => array('wpgmp-map'),
				);	
			}
			
			
			$scripts[] = array(
				'handle'  => 'wpgmp-backend',
				'src'   => WPGMP_JS.'minified/wpgmp_backend.min.js',
				'deps'    => array('wpgmp-backend-google-api'),
			);

			if ( $scripts ) {
				foreach ( $scripts as $script ) {
					wp_enqueue_script( $script['handle'], $script['src'], $script['deps'],false );
				}
			}

			$wpgmp_local = array();
			$wpgmp_local['language'] = 'en';
			$wpgmp_local['urlforajax'] = admin_url( 'admin-ajax.php' );
			$wpgmp_local['hide'] = esc_html__( 'Hide','wp-google-map-plugin' );
			$wpgmp_local['nonce']  = wp_create_nonce( 'fc-call-nonce' );
			$wpgmp_local['ajaxnonce'] = wp_create_nonce('fc_communication');
			$wpgmp_local['text_editable']   = array( '.fc-text', '.fc-post-link', '.place_title', '.fc-item-content', '.wpgmp_locations_content' );
			$wpgmp_local['bg_editable']     = array( '.fc-bg', '.fc-item-box', '.fc-pagination', '.wpgmp_locations' );
			$wpgmp_local['margin_editable'] = array( '.fc-margin', '.fc-item-title', '.wpgmp_locations_head', '.fc-item-content', '.fc-item-meta' );
			$wpgmp_local['full_editable']   = array( '.fc-css', '.fc-item-title', '.wpgmp_locations_head', '.fc-readmore-link', '.fc-item-meta', 'a.page-numbers', '.current', '.wpgmp_location_meta' );
			$wpgmp_local['confirm_location_delete'] = esc_html__( 'Do you really want to delete this location?', 'wp-google-map-plugin' );
			$wpgmp_local['confirm_map_delete'] = esc_html__( 'Do you really want to delete this map?', 'wp-google-map-plugin' );
			$wpgmp_local['confirm_category_delete'] = esc_html__( 'Do you really want to delete this category?', 'wp-google-map-plugin' );
			$wpgmp_local['confirm_route_delete'] = esc_html__( 'Do you really want to delete this route?', 'wp-google-map-plugin' );
			$wpgmp_local['confirm_record_delete'] = esc_html__( 'Do you really want to delete this record?', 'wp-google-map-plugin' );
			$wpgmp_local['no_record_for_bulk_delete'] = esc_html__( 'Please select some records first to apply bulk action on them.','wp-google-map-plugin' );
			$wpgmp_local['confirm_bulk_delete'] = esc_html__( 'Are you sure you want to delete the selected records ?','wp-google-map-plugin' );
			$wpgmp_local['confirm_overwrite_db'] = esc_html__( 'Overwrite existing google maps database?','wp-google-map-plugin' );
			$wpgmp_local['referrer_copied'] = esc_html__( 'Referrer Was Copied','wp-google-map-plugin' );
			$wpgmp_local['do_referrer_copy'] = esc_html__( 'Copy HTTP Referrer To Clipboard','wp-google-map-plugin' );
			$wpgmp_local['wpgmp_assets'] = WPGMP_JS;
			
			wp_localize_script( 'wpgmp-map', 'wpgmp_local', $wpgmp_local );
			wp_localize_script( 'wpgmp-backend', 'settings_obj', $wpgmp_local );

			$wpgmp_js_lang = array();
			$wpgmp_js_lang['confirm'] = esc_html__( 'Are you sure to delete item?','wp-google-map-plugin' );
			wp_localize_script( 'wpgmp-backend-google-maps', 'wpgmp_js_lang', $wpgmp_js_lang );
			$admin_styles = array(
			'font_awesome_minimised' => WPGMP_CSS. 'font-awesome.min.css',
			'wpgmp-map-bootstrap' => WPGMP_CSS.'flippercode-ui.css',
			'wpgmp-backend-google-map' => WPGMP_CSS.'backend.css',	
			'wpgmp-backend-bootstrap-modal' => WPGMP_CSS . 'bootstrap-modal.css'		
			);				

			if ( $admin_styles ) {
				foreach ( $admin_styles as $admin_style_key => $admin_style_value ) {
					wp_enqueue_style( $admin_style_key, $admin_style_value );
				}
			}
		}

		public static function wpgmp_apply_placeholders( $content ){  

		 $data['marker_id']                 = 1;
		 $data['marker_title']              = 'New York, NY, United States';
		 $data['marker_address']            = 'New York, NY, United States';
		 $data['marker_message']            = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam.';
		 $data['marker_category']           = 'Real Estate';
		 $data['marker_icon']               = WPGMP_IMAGES . 'default_marker.png';
		 $data['marker_latitude']           = '40.7127837';
		 $data['marker_longitude']          = '-74.00594130000002';
		 $data['marker_city']               = 'New York';
		 $data['marker_state']              = 'NY';
		 $data['marker_country']            = 'United States';
		 $data['marker_zoom']               = '5';
		 $data['marker_postal_code']        = '10002';
		 $data['extra_field_slug']          = 'color';
		 $data['marker_featured_image_src'] = WPGMP_IMAGES . 'sample.jpg';
		 $data['marker_image']              = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['marker_featured_image']     = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['post_title']                = 'Lorem ipsum dolor sit amet, consectetur';
		 $data['post_link']                 = '#';
		 $data['post_excerpt']              = 'Lorem ipsum dolor sit amet, consectetur';
		 $data['post_content']              = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';
		 $data['post_categories']           = 'city tour';
		 $data['post_tags']                 = 'WordPress, plugins, google maps';
		 $data['post_featured_image']       = '<img class="fc-item-featured_image  fc-item-large" src="' . WPGMP_IMAGES . 'sample.jpg' . '" />';
		 $data['post_author']               = 'FlipperCode';
		 $data['post_comments']             = '<i class="fci fci-comment"></i> 10';
		 $data['view_count']                = '<i class="fci fci-heart"></i> 1';

		foreach ( $data as $key => $value ) {
			if ( strstr( $key, 'marker_featured_image_src' ) === false && strstr( $key, 'marker_icon' ) === false && strstr( $key, 'post_link' ) === false && strstr( $key, 'marker_zoom' ) === false && strstr( $key, 'marker_id' ) === false && strstr( $key, 'post_title' ) === false) {
				$content = str_replace( "{{$key}}", $value . '<span class="fc-hidden-placeholder">{' . $key . '}</span>', $content );
			} else {
				$content = str_replace( "{{$key}}", $value, $content );
			}
		}

		return $content;

	}
		
		/**
		 * Eneque scripts at backend overview page only.
		 */
		 function wpgmp_overview_page_styles( $hook ) {
		 	if($hook == 'plugins.php'){
		 		wp_enqueue_style('wpgmp-modal-css', plugin_dir_url(__FILE__) . 'assets/css/modal.css');
				wpgmp_add_feedback_form();
		 	}
		 	
		 }
		 
		/**
		 * Load plugin language file.
		 */
		function wpgmp_load_plugin_languages() {

			$this->modules = apply_filters( 'wpgmp_extensions',$this->modules);
			load_plugin_textdomain( 'wp-google-map-plugin', false, WPGMP_FOLDER.'/lang' );
		}
		/**
		 * Call hook on plugin activation for both multi-site and single-site.
		 */
		function wpgmp_plugin_activation( $network_wide ) {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_activation();
					$activated[] = $blog_id;
				}
				switch_to_blog( $currentblog );

			} else {
				$this->wpgmp_activation();
			}
		}
		/**
		 * Call hook on plugin deactivation for both multi-site and single-site.
		 */
		function wpgmp_plugin_deactivation() {

			if ( is_multisite() && $network_wide ) {
				global $wpdb;
				$currentblog = $wpdb->blogid;
				$activated = array();
				$sql = "SELECT blog_id FROM {$wpdb->blogs}";
				$blog_ids = $wpdb->get_col( $wpdb->prepare( $sql, null ) );

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					$this->wpgmp_deactivation();
					$activated[] = $blog_id;
				}

				switch_to_blog( $currentblog );
				
			} else {
				$this->wpgmp_deactivation();
			}
		}

		/**
		 * Perform tasks on new blog create and table install.
		 */
		 
		 function wpgmp_on_blog_new_generate(  $blog_id, $user_id, $domain, $path, $site_id, $meta ){
		    
			if ( is_plugin_active_for_network( plugin_basename(__FILE__) ) ) {
               switch_to_blog( $blog_id );
               $this->wpgmp_activation();
               restore_current_blog();
             }	 
		 
		 }

		/**
		 * Perform tasks on when blog deleted and remove plugin tables.
		 */
		 
		 function wpgmp_on_blog_delete( $tables ){

			global $wpdb;
            $tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_LOCATION );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_GROUPMAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_MAP );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_ROUTES );
			$tables[] = str_replace( $wpdb->base_prefix, $wpdb->prefix, TBL_BACKUPS );
            return $tables; 

		 }
		/**
		 * Create choose icon tab in media manager.
		 * @param  array $tabs Current Tabs.
		 * @return array       New Tabs.
		 */
		function wpgmp_google_map_tabs_filter($tabs) {

			$newtab = array( 'ell_insert_gmap_tab' => esc_html__( 'Choose Icons', 'wp-google-map-plugin' ) );
			return array_merge( $tabs, $newtab );
		}
		/**
		 * Intialize wp_iframe for icons tab
		 * @return [type] [description]
		 */
		function wpgmp_google_map_media_upload_tab() {

			wp_enqueue_style( 'marker-listing-styling', WPGMP_CSS.'marker-category-style.css' );
			wp_enqueue_script(  'marker-listing-script', WPGMP_JS.'marker-category-script.js');
			return wp_iframe( array( $this, 'media_wpgmp_google_map_icon' ), array() );
		}
		
		/**
		 * Read images/icons folder.
		 */
		function media_wpgmp_google_map_icon() {

			wp_enqueue_style( 'media' );
			media_upload_header();
			$form_action_url = site_url( "wp-admin/media-upload.php?type={$GLOBALS['type']}&tab=ell_insert_gmap_tab", 'admin' );
		?>

		<form enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $form_action_url ); ?>" class="media-upload-form marker-choose-form" id="library-form">
		<h3 class="media-title"><?php esc_html_e( 'Choose icon', 'wp-google-map-plugin' ) ?></h3>
		<input name="wpgmp_search_icon" id="wpgmp_search_icon" type='text' value="" placeholder="<?php esc_html_e( 'Search icons','wp-google-map-plugin' ); ?>" />
		  <div class="select_icons_container">
		  <ul id="select_icons">
			<?php
			$dir = WPGMP_ICONS_DIR;
			$file_display = array( 'jpg', 'jpeg', 'png', 'gif' );

			if ( file_exists( $dir ) == false ) {
				echo 'Directory \'', esc_html($dir), '\' not found!';

			} else {
				$dir_contents = scandir( $dir );
				foreach ( $dir_contents as $file ) {
					$image_data = explode( '.', $file );
					$file_type = strtolower( end( $image_data ) );
					if ( '.' !== $file && '..' !== $file && true == in_array( $file_type, $file_display ) ) {
					?>
					<li class="read_icons">
					<img alt="<?php echo esc_attr($image_data[0]); ?>" title="<?php echo esc_attr($image_data[0]); ?>" src="<?php echo esc_url(WPGMP_ICONS.$file); ?>"/>
				</li>
				<?php
					}
				}
			}
				if(isset($_GET['target']))
				$target = sanitize_key($_GET['target']);
			?>
			</ul>
			<button type="button" class="button set_marker_cat_button" data-message="<?php esc_html_e( 'Please choose marker icon for the category.','wp-google-map-plugin' ); ?>" data-target="<?php echo esc_attr($target); ?>" value="1" onclick="add_icon_to_images();" name="send[<?php echo esc_attr($picid) ?>]"><?php esc_html_e( 'Insert into Post', 'wp-google-map-plugin' ) ?>
			</button>
		</div>
		</form>
	<?php
		}
		
		function wpgmp_hide_buy_notice(){
			
			$nonce = $_REQUEST['ajaxnonce'];
		    if ( !isset($_REQUEST['ajaxnonce']) || 
				 ! wp_verify_nonce( $nonce, 'fc_communication' ) || 
				 !current_user_can('manage_options') ) {
				
				 $result = array('status' => '0');	 
				
			} else {
				$data = maybe_unserialize(get_option('wpgmp_ignore_buy_pro'));
				$data['wpgmp_hide_buy_notice'] = 'true';
				update_option('wpgmp_ignore_buy_pro', serialize($data));
				$result = array('status' => '1');
			}
			
			echo json_encode($result);
			exit;
		}
		
		function wpgmp_show_buynow_notice($pluginheader){
			
			if( isset($_GET['page']) && !empty($_GET['page']) && strpos( sanitize_key( $_GET['page'] ), 'wpgmp' ) !== false ){

				$ignore = maybe_unserialize(get_option('wpgmp_ignore_buy_pro'));
				
				if(isset($ignore['wpgmp_hide_buy_notice']) && !empty($ignore['wpgmp_hide_buy_notice'])){}
				else {

				$premium_plugin = '<a target="_blank" href="https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium">'.esc_html__('Pro','wp-google-map-plugin').'</a>';
             	
				$pluginheader .= '<div class="flippercode-ui">
						<div class="fc-main"><div class="buy_premium notice notice-success is-dismissible">'.sprintf( esc_html__('Upgrade hassle-free to %s version & unlock listing, routes,  directions, filtration, drawing & many more features.','wp-google-map-plugin' ), $premium_plugin).' <a class="buy_now_link" href="https://www.wpmapspro.com/?utm_source=wordpress&utm_medium=liteversion&utm_campaign=freemium&utm_id=freemium" target="_blank">'.esc_html__('Upgrade Now','wp-google-map-plugin').'</a> or <a onclick="return false;" class="fc_ignore_notice hide_buy_notice" href="#">'.esc_html('Don\'t show again','wp-google-map-plugin').'</a></div></div></div>';	
				}
			}
			
			return $pluginheader;
			
		}

		function wpgmp_hide_sample_notice()
		{

			$nonce = $_REQUEST['ajaxnonce'];
			if (
				!isset($_REQUEST['ajaxnonce']) ||
				!wp_verify_nonce($nonce, 'fc_communication') ||
				!current_user_can('manage_options')
			) {

				$result = array('status' => '0');
			} else {
				$data = maybe_unserialize(get_option('wpgmp_ignore_buy_pro'));
				$data['wpgmp_hide_sample_notice'] = 'true';
				update_option('wpgmp_ignore_buy_pro', serialize($data));
				$result = array('status' => '1');
			}

			echo json_encode($result);
			exit;
		}
		
		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_deactivation() {}

		/**
		 * Perform tasks on plugin deactivation.
		 */
		function wpgmp_activation() {

			global $wpdb;

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$modules = $this->modules;
			$pagehooks = array();
			if ( is_array( $modules ) ) {
				foreach ( $modules as $module ) {
					$object = new $module;
					if ( method_exists( $object,'install' ) ) {
							$tables[] = $object->install();
					}
				}
			}

			if ( isset($tables) && !empty($tables) && is_array( $tables ) ) {
				foreach ( $tables as $i => $sql ) {
					dbDelta( $sql );
				}
			}

			$data = maybe_unserialize(get_option('wpgmp_ignore_buy_pro'));
			if( !isset($data['wpgmp_show_sample_notice_time']) || empty($data['wpgmp_show_sample_notice_time'])){
				$data['wpgmp_show_sample_notice_time'] = date('Y-m-d', strtotime('+3days'));
				update_option('wpgmp_ignore_buy_pro', serialize($data));
			} 

		}
		
		/**
		 * Define all constants.
		 */
		private function wpgmp_define_constants() {

			global $wpdb;

			if ( ! defined( 'WPGMP_SLUG' ) )
			define( 'WPGMP_SLUG', 'wpgmp_view_overview' );
			
			if ( ! defined( 'WPGMP_VERSION' ) )
			define( 'WPGMP_VERSION', '4.6.5' );
			
			if ( ! defined( 'WPGMP_FOLDER' ) )
			define( 'WPGMP_FOLDER', basename( dirname( __FILE__ ) ) );
			
			if ( ! defined( 'WPGMP_DIR' ) )
			define( 'WPGMP_DIR', plugin_dir_path( __FILE__ ) );
			
			if ( ! defined( 'WPGMP_ICONS_DIR' ) )
			define( 'WPGMP_ICONS_DIR', WPGMP_DIR.'/assets/images/icons/' );
			
			if ( ! defined( 'WPGMP_CORE_CLASSES' ) )
			define( 'WPGMP_CORE_CLASSES', WPGMP_DIR.'core/' );
						
			if ( ! defined( 'WPGMP_PLUGIN_CLASSES' ) )
			define( 'WPGMP_PLUGIN_CLASSES', WPGMP_DIR . 'classes/' );

			if ( ! defined( 'WPGMP_TEMPLATES' ) )
			define( 'WPGMP_TEMPLATES', WPGMP_DIR . 'templates/' );
						
			if ( ! defined( 'WPGMP_MODEL' ) )
			define( 'WPGMP_MODEL', WPGMP_DIR . 'modules/' );
			
			if ( ! defined( 'WPGMP_CONTROLLER' ) )
			define( 'WPGMP_CONTROLLER', WPGMP_CORE_CLASSES );
			
			if ( ! defined( 'WPGMP_CORE_CONTROLLER_CLASS' ) )
			define( 'WPGMP_CORE_CONTROLLER_CLASS', WPGMP_CORE_CLASSES.'class.controller.php' );
			
			if ( ! defined( 'WPGMP_MODEL' ) )
			define( 'WPGMP_MODEL', WPGMP_DIR.'modules/' );
			
			if ( ! defined( 'WPGMP_URL' ) )
			define( 'WPGMP_URL', plugin_dir_url( WPGMP_FOLDER ).WPGMP_FOLDER.'/' );
			
			if ( ! defined( 'WPGMP_CSS' ) )
			define( 'WPGMP_CSS', WPGMP_URL.'assets/css/' );

			if ( ! defined( 'WPGMP_TEMPLATES_URL' ) )
			define( 'WPGMP_TEMPLATES_URL', WPGMP_URL.'templates/' );
			
			if ( ! defined( 'WPGMP_JS' ) )
			define( 'WPGMP_JS', WPGMP_URL.'assets/js/' );
			
			if ( ! defined( 'WPGMP_IMAGES' ) )
			define( 'WPGMP_IMAGES', WPGMP_URL.'assets/images/' );
			
			if ( ! defined( 'WPGMP_ICONS' ) )
			define( 'WPGMP_ICONS', WPGMP_URL.'assets/images/icons/' );
						
			if ( ! defined( 'TBL_LOCATION' ) )
			define( 'TBL_LOCATION', $wpdb->prefix.'map_locations' );
			
			if ( ! defined( 'TBL_GROUPMAP' ) )
			define( 'TBL_GROUPMAP', $wpdb->prefix.'group_map' );
			
			if ( ! defined( 'TBL_MAP' ) )
			define( 'TBL_MAP', $wpdb->prefix.'create_map' );
			
			if ( ! defined( 'TBL_ROUTES' ) )
			define( 'TBL_ROUTES', $wpdb->prefix.'map_routes' );

			if ( ! defined( 'WPGMP_PRO_IMAGES' ) )
			define( 'WPGMP_PRO_IMAGES', WPGMP_URL.'assets/images/pro/' );

			if ( ! defined( 'WPGMP_PREMIUM_LINK' ) )
			define( 'WPGMP_PREMIUM_LINK', '<a href="javascript:void(0);" class="get_pro">'.esc_html__('PRO', 'wp-google-map-plugin').'</a>' );
			
		}
		/**
		 * Load all required core classes.
		 */
		private function wpgmp_load_files() {
			
			$coreInitialisationFile = plugin_dir_path( __FILE__ ).'core/class.initiate-core.php';
			if ( file_exists( $coreInitialisationFile ) ) {
			   require_once( $coreInitialisationFile );
			}

			$blockFile = WPGMP_PLUGIN_CLASSES.'class-wp-maps-block.php';
			if ( file_exists( $blockFile ) ) {
			   require_once( $blockFile );
			}
			
			//Load Plugin Files	
			$plugin_files_to_include = array('wpgmp-check-cookies.php','wpgmp-template.php','wpgmp-controller.php',
											 'wpgmp-model.php','class.map-widget.php','wpgmp-feedback-form.php');
			foreach ( $plugin_files_to_include as $file ) {

				if(file_exists(WPGMP_PLUGIN_CLASSES . $file))
				require_once( WPGMP_PLUGIN_CLASSES . $file ); 
			}
			// Load all modules.
			$core_modules = array( 'overview','group_map','location','map','settings','route','drawing', 'permissions', 'tools','extentions' );
			if ( is_array( $core_modules ) ) {
				foreach ( $core_modules as $module ) {

					$file = WPGMP_MODEL.$module.'/model.'.$module.'.php';
					
					if ( file_exists( $file ) ) {
						include_once( $file );
						$class_name = 'WPGMP_Model_'.ucwords( $module );
						array_push( $this->modules, $class_name );
					}
				}
			}

		}
	}
}

new FC_Google_Maps_Lite();
