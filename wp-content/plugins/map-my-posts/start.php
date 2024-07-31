<?php
/**
 * Map My Posts WordPress Plugin
 *
 * Associate categories with countries and display posts on a Google Geo Chart Visualization.
 * Plugin boilerplate from: http://tommcfarlin.com/wordpress-plugin-boilerplate/
 *
 * @package	MapMyPosts
 * @author	Erik Fantasia <erik@aroundthisworld.com>
 * @license	GPL-2.0+
 * @link	http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright	2013 Erik Fantasia
 *
 * @wordpress-plugin
 * Plugin Name: Map My Posts
 * Plugin URI: http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * Description: Display a Google Map or Geochart visualization, using map locations associated with categories or tags.
 * Version: 1.0.6
 * Author: Erik Fantasia
 * Author URI: http://www.aroundthisworld.com
 * Text Domain: map-my-posts
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// make sure we have proper PHP support
if ( !function_exists( 'spl_autoload_register' ) && is_admin() ) {
	require_once ABSPATH . '/wp-admin/includes/plugin.php';
	deactivate_plugins( __FILE__ );
	wp_die( __('Map My Posts requires PHP 5.1.2 or higher. The plugin has disabled itself.', 'map-my-posts') );
}

if ( !defined( 'MAPMYPOSTS_VERSION' ) ) {
	define( 'MAPMYPOSTS_VERSION', '1.0.6' );
}
if ( !defined( 'MAPMYPOSTS_URL' ) ) {
	define( 'MAPMYPOSTS_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'MAPMYPOSTS_PATH' ) ) {
	define( 'MAPMYPOSTS_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'MAPMYPOSTS_BASENAME' ) ) {
	define( 'MAPMYPOSTS_BASENAME', plugin_basename( __FILE__ ) );
}
if ( !defined( 'MAPMYPOSTS_INC') ) {
	define( 'MAPMYPOSTS_INC', MAPMYPOSTS_PATH . '/inc/' );
}
if ( !defined( 'MAPMYPOSTS_VIEWS') ) {
	define( 'MAPMYPOSTS_VIEWS', MAPMYPOSTS_PATH . '/views/' );
}
if ( !defined( 'MAPMYPOSTS_REQUEST_PROTOCOL' ) ) {
	define( 'MAPMYPOSTS_REQUEST_PROTOCOL', isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://' );
}
if ( !defined( 'MAPMYPOSTS_OPTION_PREFIX' ) ) {
	define( 'MAPMYPOSTS_OPTION_PREFIX', 'mmp' );
}
if ( !defined( 'MAPMYPOSTS_PLUGIN_NAME' ) ) {
	define( 'MAPMYPOSTS_PLUGIN_NAME', 'Map My Posts' );
}
if ( !defined( 'MAPMYPOSTS_PLUGIN_SLUG' ) ) {
	define( 'MAPMYPOSTS_PLUGIN_SLUG', 'map-my-posts' );
}
if ( !defined( 'MAPMYPOSTS_PLUGIN_URI' ) ) {
	define( 'MAPMYPOSTS_PLUGIN_URI', 'http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/' );
}

spl_autoload_register( 'mapmyposts_autoloader' );

function mapmyposts_autoloader( $class_name ) {
	$path = MAPMYPOSTS_INC . str_replace( '_', '/', $class_name ) . '.php';
	if ( class_exists( $class_name) ) {
		return true;
	} elseif ( file_exists( $path ) ) {
		return @include( $path );
	}
	return false;
}

// get this show on the road!
$mapmyposts = MapMyPosts::get_instance();
