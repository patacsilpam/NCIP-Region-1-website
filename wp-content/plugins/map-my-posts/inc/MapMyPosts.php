<?php
/**
 * MapMyPosts Class
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

class MapMyPosts {
	
	/**
	 * Instance of this class.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $instance = null;
	
	/**
	 * Count for GeoChart visualization objects.
	 *
	 * Used to control initial loading of Javascript/CSS. Allows multiple visualizations to be created in the same
	 * pageload.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $geochartcount = 0;
	
	/**
	 * Count for Google Map objects.
	 *
	 * Used to control initial loading of Javascript/CSS. Allows multiple Google Maps to be created in the same
	 * pageload.
	 *
	 * @since    1.0
	 *
	 * @var      object
	 */
	protected static $gmapcount = 0;
		
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     1.0
	 */
	private function __construct() {
		
		// load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		// load jsapi in the footer
		add_action( 'wp_footer', array( $this, 'enqueue_scripts' ) );
		// load inline javascript with wp_footer for google jsapi loader
		add_action( 'wp_footer', array( $this, 'load_google_apis' ), 100 );
		
		// register the widget
		add_action( 'widgets_init', array($this, 'load_widgets') );
				
		// define the shortcode
		add_shortcode( 'mmp-geochart', array( $this, 'get_geochart' ) );
		add_shortcode( 'mmp-staticmap', array( $this, 'get_staticmap' ) );
		add_shortcode( 'mmp-map', array( $this, 'get_map' ) );
		
		// register hooks to take care of activation and uninstall
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_uninstall_hook( __FILE__, array( $this, 'uninstall') );
		
		if ( is_admin() ) {
			// load admin menus, pages and settings
			$mapmyposts_admin = MapMyPostsAdmin::get_instance();
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
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}
	
	/**
	 * Clean options database when the plugin is uninstalled.
	 *
	 * @since    1.0
	 */
	public static function uninstall() {
		// keep term data in case user changes their mind
		/*
		$cats = get_terms( 'category', 'hide_empty=0' );
		foreach ($cats as $obj) {
			delete_option( MAPMYPOSTS_OPTION_PREFIX . '_' . $obj->taxonomy . '_' . $obj->term_id );
		}
		$tags = get_terms( 'post_tag', 'hide_empty=0' );
		foreach ($tags as $obj) {
			delete_option( MAPMYPOSTS_OPTION_PREFIX . '_' . $obj->taxonomy . '_' . $obj->term_id );
		}
		delete_option( MAPMYPOSTS_OPTION_PREFIX . '_settings' );
		delete_option( MAPMYPOSTS_OPTION_PREFIX . '_category_country_list' );
		delete_option( MAPMYPOSTS_OPTION_PREFIX . '_category_marker_list' );
		delete_option( MAPMYPOSTS_OPTION_PREFIX . '_post_tag_country_list' );
		delete_option( MAPMYPOSTS_OPTION_PREFIX . '_post_tag_marker_list' );
		*/
	}
	
	/**
	 * Get the Google API key and prepend for query string using argument.
	 *
	 * @param    string    $prefix    Prepend (eg, '?key=') additional text. If not defined, returns only the API key.
	 * 
	 * @since    1.0
	 */
	public static function get_api_key( $prepend = '' ) {
		$api_key = self::get_setting( 'api_key' );
		if ( $api_key ) {
			return  $prepend . urlencode($api_key);
		}
		return null;
	}
	
	/**
	 * Get default plugin setting(s).
	 *
	 * @see get_setting()
	 *
	 * @since    1.0
	 *
	 * @param    string    $key    The setting key.
	 *
	 * @return   mixed
	 */
	public static function get_defaults( $key = null) {
		$defaults = array(
			'width'		=> 'auto',
			'height'	=> '400px',
			'taxonomy'	=> 'category',
			'target'	=> '_self',
			'mode'		=> 'region',
			'maptype'	=> 'ROADMAP',
			'background'	=> '#ffffff',
			'min_color'	=> '#74a9cf',
			'max_color'	=> '#045a8d',
			'empty_color'	=> '#f5f5f5',
			'markercolor'	=> '#fe7569',
			'markersize'	=> 'normal',
			'tooltip'	=> 'true',
			'infowindow'	=> 'true',
			'pancontrol'	=> 'false',
			'linktext'	=> __('View Posts...', 'map-my-posts'),
			'maptypecontrol'=> 'false',
			'streetviewcontrol'=> 'false',
		);
		if ( $key ) {
			return $defaults[$key];
		}
		return $defaults;
	}
	
	/**
	 * Get plugin settings data saved in admin, or default option if not found.
	 *
	 * @see MapMyPostsAdmin::init_settings()
	 *
	 * @since    1.0
	 *
	 * @param    string    $key    The setting key.
	 *
	 * @return   mixed
	 */
	public static function get_setting( $key ) {
		// some defaults can be defined as null/empty string
		$null_allowed = array( 'linktext' );
		$settings = (array) get_option( MAPMYPOSTS_OPTION_PREFIX . '_settings' );
		if ( array_key_exists( $key, $settings ) ) {
			if ( $settings[$key] || in_array( $key, $null_allowed ) ) {
				return $settings[$key];
			}
		}
		return self::get_defaults( $key );
	}
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * This method recommended at: http://geertdedeckere.be/article/loading-wordpress-language-files-the-right-way
	 *
	 * @since    1.0
	 */
	public function load_plugin_textdomain() {
		
		$domain = 'map-my-posts';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( MAPMYPOSTS_BASENAME ) . '/lang/' );
	}
	
	/**
	 * Register plugin's widgets.
	 *
	 * @since     1.0
	 */
	public function load_widgets() {
		register_widget( 'MapMyPostsWidgetStatic' );
		register_widget( 'MapMyPostsWidgetGmap' );
		register_widget( 'MapMyPostsWidgetGeochart' );
	}
	
	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * Load with wp_footer hook using priority 20.
	 *
	 * @since    1.0
	 */
	public function enqueue_scripts() {
		// enqueue the google loader when needed
		if (self::$geochartcount || self::$gmapcount) {
			wp_enqueue_script( 'google-jsapi', MAPMYPOSTS_REQUEST_PROTOCOL . 'www.google.com/jsapi', array(), null );
		}		
	}
	
	
	/**
	 * Load Google JS APIs in footer.
	 *
	 * Load with wp_footer hook using priority 100.
	 *
	 * @since    1.0
	 */
	public function load_google_apis() {
		
		if (self::$geochartcount) {
?>
<script type="text/javascript">
	google.load("visualization", "1", {packages: ["geochart"], mapsApiKey: '<?= $this->get_api_key('') ?>'});
	<?php for ($i=1; $i<=self::$geochartcount; $i++): ?>
	google.setOnLoadCallback(MMPGeochart<?php echo $i; ?>);
	<?php endfor; ?>
</script>
<?php
		}
		
		if (self::$gmapcount) {
?>
<script type="text/javascript">
	google.load('maps', '3', {other_params: '<?= $this->get_api_key('key=') ?>&sensor=false'});
	<?php for ($i=1; $i<=self::$gmapcount; $i++): ?>
	google.setOnLoadCallback(MMPGmap<?php echo $i; ?>);
	<?php endfor; ?>
</script>
<?php
		}
		
	}
	
	/**
	 * Get cached lists of term data.
	 *
	 * @see MapMyPostsAdmin::build_cache_list()
	 * 
	 * @since    1.0
	 *
	 * @param    string    $taxonomy    Taxonomy of this term: "category" or "post_tag".
	 * @param    string    $mode        If in 'region' mode, only terms with a country code are returned, or in 'marker' mode only those with lat/lon.
	 *
	 * @return   array     Associative array in the format: term_id => country (if $mode == $region) or term_id => lat/lng
	 */
	public function get_cache_list( $taxonomy = 'category', $mode = 'region' ) {
		$key = MAPMYPOSTS_OPTION_PREFIX;
		
		$taxonomy = strtolower( $taxonomy );
		if ( $taxonomy == 'category' ) {
			$key .= '_category';
		} else {
			$key .= '_post_tag';
		}
		$mode = strtolower( $mode );
		if ( $mode == 'region' ) {
			$key .= '_country_list';
		} else {
			$key .= '_marker_list';
		}
		
		$cache_list = get_option( $key );
		if ( !is_array( $cache_list ) ) {
			$this->build_cache_list();
			$cache_list = get_option( $key );
		}
		
		return $cache_list;
	}
	
	/**
	 * Return data for terms with geographic information for building maps.
	 *
	 * @since    1.0
	 *
	 * @param    int       $parent      Only return terms under (and including) this parent term_ID, if defined.
	 * @param    string    $taxonomy    Taxonomy of this term: "category" or "post_tag".
	 * @param    string    $mode        If in 'region' mode, only terms with a country code are returned, or in 'marker' mode only those with lat/lon.
	 *
	 * @return   array     Returns array of terms with geographic data.
	 */
	public function get_term_list( $parent = '', $taxonomy = 'category', $mode = 'region' ) {
		$mode = strtolower( $mode );
		if ( $mode != 'region' ) {
			$mode = 'marker';
		}
		$taxonomy = strtolower( $taxonomy );
		if ( $taxonomy != 'category' ) {
			$taxonomy = 'post_tag';
		}
		if ( !is_numeric( $parent ) ) {
			$parent_obj = get_term_by('slug', $parent, $taxonomy);
			$parent = $parent_obj->term_id;
		}
		$term_list = array();
		
		$country_list = $this->get_cache_list( $taxonomy, 'region' );
		$marker_list = $this->get_cache_list( $taxonomy, 'marker' );
		
		// note if more than one term share the same country or lat/lng, only show the one with more posts
		// pad_counts=1 adds child category posts to count
		$cats = get_terms( $taxonomy, 'hide_empty=1&order_by=count&pad_counts=1&child_of=' . $parent );
		foreach ( $cats as $obj ) {
			
			$url = get_term_link( $obj->slug, $taxonomy );
			$country = $country_list[$obj->term_id];
			$latlng = $marker_list[$obj->term_id];
			
			// get adjusted latlng from MapMyPostsGeography if no existing latlng or if antarctica
			if ( ( $country && !$latlng ) || ( $country == 'AQ' ) ) {
				// use MapMyPostsGeography if we need the latlng
				$country_data = MapMyPostsGeography::get_country( $country );
				$latlng = $country_data[0] . ',' . $country_data[1];
			}
			$index = null;
			// index this list by country or lat/lng depending on the mode
			if ( $mode == 'region' && $country ) {
				$index = $country;
			}
			if ( $mode == 'marker' && $latlng ) {
				$index = $latlng;
			}
			if ($index) {
				$term_list[$index] = array(
					'term_id'	=> $obj->term_id,
					'slug'		=> $obj->slug,
					'name'		=> $obj->name,
					'count'		=> $obj->count,
					'country'	=> $country,
					'latlng'	=> $latlng,
					'url'		=> $url,
				);
			}
		}
		// include the parent terms itself, which is normally excluded using child_of with get_terms()
		if ( $parent ) {
			$parent_obj = get_term( $parent, $taxonomy );
			
			$url = get_term_link( $parent_obj->slug, $taxonomy );
			$country = $country_list[$obj->term_id];
			$latlng = $marker_list[$obj->term_id];
			$index = null;
			if ( $mode == 'region' && $country ) {
				$index = $country;
			}
			if ( $mode == 'marker' && $latlng ) {
				$index = $latlng;
			}
			// replace a subcategory if necessary
			if ( $parent_obj->count >= $term_list[$index]["count"] ) {
				$term_list[$index] = array(
					'term_id'	=> $parent_obj->term_id,
					'slug'		=> $parent_obj->slug,
					'name'		=> $parent_obj->name,
					'count'		=> $parent_obj->count,
					'country'	=> $country,
					'latlng'	=> $latlng,
					'url'		=> $url,
				);
			}
		}
		return $term_list;
	}
	
	/**
	 * Print a Google Geochart visualization.
	 *
	 * @since    1.0
	 *
	 * @param    array    $atts    Shortcode attributes - defaults can also be set with MapMyPostsAdmin.
	 *
	 * @return   void
	 */
	public function get_geochart( $atts ) {
		// counter used to display inline css/jss and prevent naming conflicts in view
		self::$geochartcount++;
		
		// extract the attributes into variables
		extract( shortcode_atts( array(
			'parent' => '',
			'taxonomy'=> self::get_setting('taxonomy'),
			'background' => self::get_setting('background'),
			'empty_color' => self::get_setting('empty_color'),
			'width' => self::get_setting('width'), // google default: 566
			'height' => self::get_setting('height'), // google default: 347
			'mode' => self::get_setting('mode'),
			'region' => self::get_setting('region'),
			'target'  => self::get_setting('target'),
			'border_color' => '#666',
			'border_width' => 0, // no border by default
			'max_color' => self::get_setting('max_color'),
			'min_color' => self::get_setting('min_color'),
			'showbubble' => self::get_setting('showbubble'),
			'legend' => 'none',
			'tooltip' => 'focus',
			'text_color' => 'black',
			'text_font' => 'Arial',
			'text_size' => 12,
		), $atts ) );
		
		if ( !$tooltip || strtolower( $tooltip ) == 'false') {
			$tooltip = 'none';
		}
		
		$term_list = $this->get_term_list( $parent, $taxonomy, $mode );
		
		foreach ( $term_list as $key => $ary ) {
			// need to fudge antarctica's latlng to make it work on a geochart
			if ( $ary['country'] == 'AQ' ) {
				$term_list[$key]['latlng'] = '-56,0';
			}
		}
		
		$div_style = '';
		$div_styles = array();
		
		// format the width and height
		if ( preg_match( '@(\d+)px\s*$@', $width, $matches ) ) {
			$width = intval( $matches[1] );
		}
		if ( preg_match( '@(\d+)px\s*$@', $height, $matches ) ) {
			$height = intval( $matches[1] );
		}		
		if ( preg_match( '@(\d+)%\s*$@', $width, $matches ) ) {
			$width = 'auto';
			$div_styles[] = 'width:' . $matches[1] . '%;';
		}
		if ( preg_match( '@(\d+)%\s*$@', $width, $matches ) ) {
			$height = 'auto';
			$div_styles[] = 'height:' . $matches[1] . '%;';
		}
		if ( count( $div_styles ) ) {
			$div_style = 'style="'.join( ',', $div_styles ).'"';
		}
		
		/*
		// use undocumented Google Geochart 'auto' region instead?
		if ( strtolower( $mode ) == 'region' && strtolower( $region ) == "auto" ) {
			$countries = array_keys( $term_list );
			if ( strtolower( $region ) == "auto" ) {
				$region = MapMyPostsGeography::find_region( $countries );
			}
		}
		*/
		
		ob_start();
		// don't use include_once or it won't show more than one per page!
		include( MAPMYPOSTS_VIEWS . 'geochart.php' );
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	/**
	 * Print a Google Map.
	 *
	 * @since    1.0
	 *
	 * @param    array    $atts    Shortcode attributes - defaults can also be set with MapMyPostsAdmin.
	 *
	 * @return   void
	 */
	public function get_map( $atts ) {
		// counter used to display inline css/jss and prevent naming conflicts in view
		self::$gmapcount++;
		
		// extract the attributes into variables
		extract( shortcode_atts( array(
			'parent'  => '',
			'width'   => self::get_setting('width'),
			'height'  => self::get_setting('height'),
			'taxonomy'=> self::get_setting('taxonomy'),
			'target'  => self::get_setting('target'),
			'region'  => self::get_setting('region'),
			'maptype' => self::get_setting('maptype'),
			'linktext'=> self::get_setting('linktext'),
			'pancontrol'  => self::get_setting('pancontrol'),
			'markercolor' => self::get_setting('markercolor'),
			'infowindow'  => self::get_setting('infowindow'),
			'maptypecontrol' => self::get_setting('maptypecontrol'),
			'streetviewcontrol' => self::get_setting('streetviewcontrol'),
		), $atts ) );
		
		$term_list = $this->get_term_list( $parent, $taxonomy, 'marker' );
		
		if ( strtolower($infowindow) == 'false' ) {
			$infowindow = 0;
		}
		$zoom = 0;
		if ( preg_match( '@(\d+)px$@', $width, $matches ) ) {
			if ( $matches[1] < 250 ) {
				$infowindow = 0;
			}
			if ( $matches[1] > 300 ) {
				$zoom = 1;
			}
		}
		if ( preg_match( '@(\d+)px$@', $height, $matches ) ) {
			if ( $matches[1] < 200 ) {
				$infowindow = 0;
			}
			if ($ $matches[1] > 300 ) {
				$zoom = 1;
			}
		}
		
		ob_start();
		// don't use include_once or it won't show more than one per page!
		include( MAPMYPOSTS_VIEWS . 'gmap.php' );
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	/**
	 * Return a Static Google Map, including HTML <img> tag.
	 *
	 * @see get_staticmap_url()
	 *
	 * @since    1.0
	 *
	 * @param    array    $atts    Shortcode attributes - defaults can also be set with MapMyPostsAdmin.
	 *
	 * @return   void
	 */
	public function get_staticmap( $atts ) {
		$url = $this->get_staticmap_url( $atts );
		$output = '<img src="' . $url . '"';
		if ( $atts['width'] ) {
			$output .= ' width="' . $atts['width'] . '"';
		}
		if ( $atts['height'] ) {
			$output .= ' height="' . $atts['height'] . '"';
		}
		$output .= '>';
		
		return $output;
	}
	
	/**
	 * Return a Static Google Map URL.
	 *
	 * There is a 2048 character URL limit set by Google.
	 * Full list of attributes: https://developers.google.com/maps/documentation/staticmaps/
	 *
	 * @see get_staticmap()
	 *
	 * @since    1.0
	 *
	 * @param    array    $atts    Shortcode attributes - defaults can also be set with MapMyPostsAdmin.
	 *
	 * @return   void
	 */
	public function get_staticmap_url( $atts ) {
		// set up the defaults
		if ( !$atts['taxonomy'] ) {
			$atts['taxonomy'] = self::get_setting( 'taxonomy' );
		}
		if ( !$atts['maptype'] ) {
			$atts['maptype'] = strtolower( self::get_setting( 'maptype' ) );
		}
		if ( !$atts['region'] ) {
			$atts['region'] = self::get_setting( 'region' );
		}
		$markeropts = array();
		if ( !$atts['markercolor'] ) {
			$markercolor = strtolower( self::get_setting( 'markercolor' ) );
		}
		if ( $markercolor && strtolower($markercolor) != '#fe7569' ) {
			$markeropts[] = 'color:' . str_replace('#', '0x', $markercolor);
		}
		$markersize = '';
		if ( !$atts['markersize'] ) {
			$markersize = strtolower( self::get_setting( 'markersize' ) );
		}
		if ( $markersize && strtolower($markersize) == 'small' ) {
			// size:tiny way too small!
			$markeropts[] = 'size:mid';
		}
		$api_key = self::get_api_key();
		if (!$atts['key'] && $api_key) {
			$atts['key'] = $api_key;
		}
		$term_list = $this->get_term_list( $atts['parent'], $atts['taxonomy'], 'marker' );
		$marker_list = array();
		foreach ( $term_list as $ary ) {
			// round off each lat/lng to a tenth of a degree precision
			if ( $ary['latlng'] ) {
				list( $lat, $lng ) = explode( ',', $ary['latlng'] );
				$lat = round($lat, 1);
				$lng = round($lng, 1);
				$marker_list[] = $lat . ',' . $lng;
			}
		}
		if ( !$atts['width'] ) {
			$atts['width'] = 300;
		}
		if ( !$atts['height'] ) {
			$atts['height'] = 250;
		}
		if ( $atts['width'] > 640 ) {
			$atts['width'] = 640;
		}
		if ( $atts['height'] > 640 ) {
			$atts['height'] = 640;
		}
		$atts['size'] = $atts['width'] . 'x' . $atts['height'];
		// unset unneeded elements from $atts
		if ( strtolower( $atts['region'] ) == 'auto' ) {
			unset($atts['center'],$atts['zoom']);
		} elseif ( strtolower( $atts['region'] == 'world' ) ) {
			$atts['center'] = '0,0';
			$atts['zoom'] = 0;
		}
		unset($atts['parent'], $atts['taxonomy'], $atts['width'], $atts['height'], $atts['region'], $atts['markercolor']);
		// if roadmap, we can save some characters by deleting this element
		if ( strtolower( $atts['maptype'] ) == 'roadmap' ) {
			unset($atts['maptype']);
		}
		$atts['markers'] = join( '|', $markeropts ) . '|' . join( '|', $marker_list ); // prepend marker color, eg: "color:|"
		$atts['sensor'] = 'false';
		$url = MAPMYPOSTS_REQUEST_PROTOCOL . 'maps.googleapis.com/maps/api/staticmap?' . http_build_query( $atts );
		// delete markers until the URL is under 2048 characters
		while ( strlen( $url ) > 2048 ) {
			array_pop( $marker_list );
			$atts['markers'] = join( '|', $markeropts ) . '|' . join( '|', $marker_list );
			$url = MAPMYPOSTS_REQUEST_PROTOCOL . 'maps.googleapis.com/maps/api/staticmap?' . http_build_query( $atts );			
		}
		return $url;
	}
	
} // end of MapMyPosts class