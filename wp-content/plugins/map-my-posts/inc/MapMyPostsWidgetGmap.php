<?php
/**
 * Map My Posts WordPress Google Map Widget.
 *
 * @package	MapMyPosts
 * @author	Erik Fantasia <erik@aroundthisworld.com>
 * @license	GPL-2.0+
 * @link	http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright	2013 Erik Fantasia
 */

class MapMyPostsWidgetGmap extends WP_Widget {
	
	public function __construct() {
		$settings = array(
			'classname' => 'widget_mmp_gmap',
			'description' => __('Display a Google Map with categories or tags plotted as clickable markers.', 'map-my-posts')
		);
		
		$title = __('Map My Posts Google Map', 'map-my-posts');
		
		$this->WP_Widget( 'MapMyPostsWidgetGmap', $title, $settings );
	}
	
	public function widget_defaults() {
		return array(
			'title'	=> 'Map of My Posts',
			'target'=> MapMyPosts::get_setting('target'),
			'region'=> MapMyPosts::get_setting('region'),
			'width'	=> 300,
			'height'=> 250,
			'parent'=> '',
			'maptype' => MapMyPosts::get_setting('maptype'),
			'taxonomy'=> MapMyPosts::get_setting('taxonomy'),
			'infowindow' => MapMyPosts::get_setting('infowindow'),
			'maptypecontrol' => MapMyPosts::get_setting('maptypecontrol'),
		);
	}
	
	// display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		
		$instance = wp_parse_args( (array) $instance, $this->widget_defaults() );
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		// $before_widget defined by theme
		echo $before_widget;
		
		if ( $title ) {
			// $before_title and $after_title defined by theme
			echo $before_title . $title . $after_title;
		}
		$atts = array(
			'target'=> $instance['target'],
			'region'=> $instance['region'],
			'width'	=> $instance['width'] . 'px',
			'height'=> $instance['height'] . 'px',
			'parent'=> $instance['parent'],
			'maptype' => $instance['maptype'],
			'taxonomy' => $instance['taxonomy'],
			'infowindow' => $instance['infowindow'],
			'maptypecontrol' => $instance['maptypecontrol'],
			'streetviewcontrol' => 'false',
		);
		
		$mmp = MapMyPosts::get_instance();
		echo $mmp->get_map( $atts );
		
		// $after_widget defined by theme
		echo $after_widget;
	}
	
	// widget controls and settings
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->widget_defaults() );
		include( MAPMYPOSTS_PATH . '/views/widget-form-gmap.php' );
	}
	
	// save widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['target'] = strip_tags( $new_instance['target'] );
		$instance['region'] = strip_tags( $new_instance['region'] );
		$instance['width'] = intval( $new_instance['width'] );
		if ( !$instance['width'] ) {
			$instance['width'] = 300;
		}
		$instance['height'] = intval( $new_instance['height'] );
		if ( !$instance['height'] ) {
			$instance['height'] = 250;
		}
		$instance['parent'] = strip_tags( $new_instance['parent'] );
		$instance['maptype'] = strip_tags( $new_instance['maptype'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		$instance['infowindow'] = strip_tags( $new_instance['infowindow'] );
		$instance['maptypecontrol'] = strip_tags( $new_instance['maptypecontrol'] );
		
		return $instance;
	}
}
