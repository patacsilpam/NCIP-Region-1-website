<?php
/**
 * Admin settings.
 *
 * @package	MapMyPosts
 * @author	Erik Fantasia <erik@aroundthisworld.com>
 * @license	GPL-2.0+
 * @link	http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright	2013 Erik Fantasia
 */

if ( !defined( 'MAPMYPOSTS_VERSION' ) ) {
	header( 'HTTP/1.0 403 Forbidden' );
	die;
}

class MapMyPostsAdmin {
	
	/**
	 * Slug of the plugin admin screen.
	 *
	 * @since    1.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Initialize the administration functions.
	 *
	 * @since     1.0
	 */
	private function __construct() {
		
		if ( is_admin() ) {
			// initialize admin page settings and add menu links
			add_action( 'admin_init', array( $this, 'init_settings' ) );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_filter( 'plugin_action_links_' . MAPMYPOSTS_BASENAME, array( $this, 'add_action_links' ) );
			
			// show get started message
			add_action( 'admin_notices', array( $this, 'show_admin_notices' ) );
			
			// load admin style sheet and JavaScript.
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			
			// hooks to update options table on category add/edit
			add_action( 'edited_category', array( $this, 'update_cache_term' ) );
			add_action( 'create_category', array( $this, 'update_cache_term' ) );
			// hooks to updated options table on tag add/edit
			add_action( 'edited_terms', array( $this, 'update_cache_term' ) );
			add_action( 'created_term', array( $this, 'update_cache_term' ) );
			
			// hook to rebuild cached lists on post save
			add_action( 'save_post', array( $this, 'build_cache_list' ) );
			
			// show additional category form fields
			add_action( 'edit_category_form_fields', array( $this, 'show_term_fields') );
			add_action( 'category_add_form_fields', array( $this, 'show_term_fields' ) );
			// show additional tag form fields
			add_action( 'edit_tag_form_fields', array( $this, 'show_term_fields' ) );
			add_action( 'add_tag_form_fields', array( $this, 'show_term_fields' ) );
		}
		
	}
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	/**
	 * Register the option value used for settings.
	 *
	 * @see      sanitize_settings()
	 *
	 * @since    1.0
	 */
	public function init_settings() {
		$settings_key =  MAPMYPOSTS_OPTION_PREFIX . '_settings';
		register_setting( 'mmp-settings-group', $settings_key, array( $this, 'sanitize_settings' ) );
	}
	
	/**
	 * Bug the admin after activation.
	 *
	 * @see      sanitize_settings()
	 *
	 * @since    1.0
	 */
	public function show_admin_notices() {
		
		if ( !get_option( MAPMYPOSTS_OPTION_PREFIX . '_settings' ) ) {
			$screen = get_current_screen();
			// write some initial settings and disappear after visiting settings page
			if ($screen->id == $this->plugin_screen_hook_suffix) {
				$settings = array();
				$settings['install_version'] = MAPMYPOSTS_VERSION;
				$settings['install_time'] = time();
				$settings['width'] = MapMyPosts::get_defaults( 'width' );
				$settings['height'] = MapMyPosts::get_defaults( 'height' );
				update_option( MAPMYPOSTS_OPTION_PREFIX . '_settings', $settings );
			} else {
				echo '<div class="updated"><p>';
				printf( __('Thank you for installing Map My Posts! Please visit the %1$sSettings Page%2$s to get started.'), '<a href="' . admin_url( 'options-general.php?page=map-my-posts' ) . '">', '</a>' );
				echo '</p></div>';
			}
		}
	}
	
	/**
	 * Do some basic scrubbing of the admin settings.
	 *
	 * @see      init_settings()
	 *
	 * @since    1.0
	 */
	public function sanitize_settings( $settings ) {
		$settings['width'] = strtolower( $settings['width'] );
		$settings['height'] = strtolower( $settings['height'] );
		// don't allow both auto/percentage width and height
		if ( preg_match( '@\d+%\s*|\s*auto\s*@', $settings['width'] ) && preg_match( '@\d+%\s*|\s*auto\s*@', $settings['height'] ) ) {
			$settings['height'] = '300px';
		}
		// assume they meant a width/height in px
		if ( is_numeric( $settings['width'] ) ) {
			$settings['width'] .= 'px';
		}
		if ( is_numeric( $settings['height'] ) ) {
			$settings['height'] .= 'px';
		}
		return $settings;
	}
	
	/**
	 * Register the administration menu for this plugin under the WordPress settings menu.
	 *
	 * @since    1.0
	 */
	public function add_admin_menu() {
		
		// add settings submenu for admin
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'Map My Posts', 'map-my-posts' ), // heading
			__( 'Map My Posts', 'map-my-posts' ), // menu name
			'manage_options',
			MAPMYPOSTS_PLUGIN_SLUG,
			array( $this, 'display_admin_page' )
		);
		
	}
	
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0
	 */
	public function display_admin_page() {
		// settings are stored as an associative array in one option value
		$settings_key = MAPMYPOSTS_OPTION_PREFIX . '_settings';
		$settings = (array) get_option( $settings_key );
		
		// if settings have not been save on this page, fill in with defaults from MapMyPosts::get_setting()
		if (! $settings['maptype'] ) {
			$settings = MapMyPosts::get_defaults();
		}
		
		include_once( MAPMYPOSTS_VIEWS . 'admin-settings.php' );
	}
	
	/**
	 * Register the action links shown next to activate.
	 *
	 * @since    1.0
	 */
	public function add_action_links( $actions ) {
		return array_merge(
			array(
			      'settings' => '<a href="' . admin_url( 'options-general.php?page=map-my-posts' ) . '">' . __('Settings', 'map-my-posts') . '</a>'
			),
			$actions
		);
	}
	
	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {
		
		$screen = get_current_screen();
		// load css for views/edit-terms.php
		if ( $screen->id == 'edit-category' || $screen->id == 'edit-post_tag' ) {
			wp_enqueue_style( MAPMYPOSTS_PLUGIN_SLUG .'-edit-terms', MAPMYPOSTS_URL . 'css/edit-terms.css', MAPMYPOSTS_VERSION );
		}
		// load css for settings page
		if ( $screen->id == $this->plugin_screen_hook_suffix && isset( $this->plugin_screen_hook_suffix ) ) {
			// use farbtastic if WP < 3.5
			if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
				wp_enqueue_style( 'farbtastic' );
			} else {
				wp_enqueue_style( 'wp-color-picker' );
			}
			wp_enqueue_style( MAPMYPOSTS_PLUGIN_SLUG .'-admin-styles',  MAPMYPOSTS_URL . 'css/admin-settings.css', MAPMYPOSTS_VERSION );
		}
		
	}
	
	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * Google JS APIs are NOT lazy loaded in footer like they are for the public scripts in MapMyPosts class.
	 * 
	 * @since     1.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {
		
		$screen = get_current_screen();
		// load jsapi in header and load gmaps/geodecoder for use with views/edit-terms.php
		if ( $screen->id == 'edit-category' || $screen->id == 'edit-post_tag' ) {
			wp_enqueue_script( 'google-jsapi', MAPMYPOSTS_REQUEST_PROTOCOL . 'www.google.com/jsapi', array(), null );
			wp_enqueue_script( MAPMYPOSTS_PLUGIN_SLUG . '-admin-geocoder-script', MAPMYPOSTS_URL . 'js/admin-geocoder.js', array( 'jquery' ), MAPMYPOSTS_VERSION . '111' );
			function load_gmap_admin() {
				?>
				<script type="text/javascript">				
				google.load('maps', '3', {other_params: '<?= MapMyPosts::get_api_key('key=') ?>&sensor=false'});
				</script>
				<?php
			}
			add_action( 'admin_footer', 'load_gmap_admin', 100 );
			$translate_array = array(
				'not_found'	=> __('Location not found! Please enter a different search term.', 'map-my-posts'),
				'general_error'	=> __('There was an error with the Geocoding service.', 'map-my-posts'),
			);
			wp_localize_script( MAPMYPOSTS_PLUGIN_SLUG . '-admin-geocoder-script', 'mmp_text', $translate_array );
		}
		// load js for settings page
		if ( $screen->id == $this->plugin_screen_hook_suffix && isset( $this->plugin_screen_hook_suffix ) ) {
			// use farbtastic if WP < 3.5
			if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
				wp_enqueue_script( 'farbtastic' );
			} else {
				wp_enqueue_script( 'wp-color-picker' );
			}
			wp_enqueue_script( MAPMYPOSTS_PLUGIN_SLUG . '-admin-settings-script', MAPMYPOSTS_URL . 'js/admin-settings.js', array( 'jquery' ), MAPMYPOSTS_VERSION );
		}
		
	}
	
	/**
	 * Update the option database with data for or a specific term.
	 *
	 * @see get_cache_term()
	 * 
	 * @since    1.0
	 *
	 * @param    int       $term_id     The numeric term_ID.
	 * @param    string    $taxonomy    The term's taxonomy either 'category' or 'post_tag'.
	 */
	public function update_cache_term( $term_id = null, $taxonomy = null ) {
		// $tag_id is set on create_category, otherwise read from the form
		if ( !isset( $term_id ) ) {
			$term_id = $_POST['tag_id'];
		}
		if ( !isset( $taxonomy ) ) {
			$taxonomy = $_POST['taxonomy'];
		}
		$data['country'] = $_POST['mmp_country'];
		$data['lat'] = $_POST['mmp_lat'];
		$data['lng'] = $_POST['mmp_lng'];
		$data['zoom'] = $_POST['mmp_zoom'];
		$data['city'] = $_POST['mmp_geo_city'];
		$data['state'] = $_POST['mmp_geo_state'];
		$data['address'] = $_POST['mmp_geo_address']; // the full formatted address when available
		$data['ver'] = MAPMYPOSTS_VERSION;
		if ( !empty( $term_id ) && !empty( $taxonomy ) ) {
			update_option( MAPMYPOSTS_OPTION_PREFIX . '_' . strtolower( $taxonomy ) . '_' . $term_id, $data );
		}
		$this->build_cache_list();
	}
	
	/**
	 * Get data from option database for the specified term.
	 *
	 * @see update_cache_term()
	 *
	 * @since    1.0
	 *
	 * @param    int       $term_id      The numeric term_ID.
	 * @param    string    $taxonomy     Taxonomy of this term: "category" or "post_tag".
	 *
	 * @return   Array     Cached term data including country and latlng
	 */
	public function get_cache_term( $term_id, $taxonomy ) {
		return get_option( MAPMYPOSTS_OPTION_PREFIX . '_' . strtolower( $taxonomy ) . '_' . $term_id );
	}
	
	/**
	 * Display additional form fields for adding and editing categories/tags.
	 *
	 * @since    1.0
	 *
	 * @param    object    $tag    Object containing term data.
	 */
	public function show_term_fields( $tag ) {
		$country_guess = null;
		// if we are editing and not adding
		if ( $tag->term_id ) {
			// get the category option data ($data used in view), if it doesn't exist take a guess at the country
			if ( ( $data = $this->get_cache_term( $tag->term_id, $tag->taxonomy ) ) === false) {
				$country_guess = MapMyPostsGeography::guess_country( $tag->name );
			}
		}
		$countries = MapMyPostsGeography::get_countries();
		include_once( MAPMYPOSTS_VIEWS . 'edit-terms.php' );
	}
	
	/**
	 * Build cached lists of term data for categories and tags.
	 *
	 * @see MapMyPosts::get_cache_list()
	 * 
	 * @since    1.0
	 */
	public function build_cache_list() {
		// build the category country and marker lists
		$country_list = array();
		$marker_list = array();
		$terms = get_terms( 'category', 'hide_empty=0' );
		foreach ( $terms as $obj ) {
			$data = $this->get_cache_term( $obj->term_id, $obj->taxonomy );
			if ( $data['country'] ) {
				// should we assign to parent category country if that exists?
				$country_list[$obj->term_id] = $data['country'];
			}
			if ( is_numeric( $data['lat'] ) && is_numeric( $data['lng'] ) ) {
				$marker_list[$obj->term_id] = $data['lat'] . ',' . $data['lng'];
			}
		}
		update_option( MAPMYPOSTS_OPTION_PREFIX . '_category_country_list', $country_list );
		update_option( MAPMYPOSTS_OPTION_PREFIX . '_category_marker_list', $marker_list );
		
		// repeat same process for the tags
		$country_list = array();
		$marker_list = array();
		$terms = get_terms( 'post_tag', 'hide_empty=0' );
		foreach ( $terms as $obj ) {
			$data = $this->get_cache_term( $obj->term_id, $obj->taxonomy );
			if ( $data['country'] ) {
				// should we assign to parent category country if that exists?
				$country_list[$obj->term_id] = $data['country'];
			}
			if ( is_numeric( $data['lat'] ) && is_numeric( $data['lng'] ) ) {
				$marker_list[$obj->term_id] = $data['lat'] . ',' . $data['lng'];
			}
		}
		update_option( MAPMYPOSTS_OPTION_PREFIX . '_post_tag_country_list', $country_list );
		update_option( MAPMYPOSTS_OPTION_PREFIX . '_post_tag_marker_list', $marker_list );
	}
	
} // end of MapMyPostsAdmin class