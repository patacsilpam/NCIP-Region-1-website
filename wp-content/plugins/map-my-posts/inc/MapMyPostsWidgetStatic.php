<?php
/**
 * Map My Posts WordPress Static Map Widget.
 *
 * @package	MapMyPosts
 * @author	Erik Fantasia <erik@aroundthisworld.com>
 * @license	GPL-2.0+
 * @link	http://www.aroundthisworld.com/map-my-posts-wordpress-plugin/
 * @copyright	2013 Erik Fantasia
 */

class MapMyPostsWidgetStatic extends WP_Widget {
	
	public function __construct() {
		$settings = array(
			'classname' => 'widget_mmp_static',
			'description' => __('Display a simple Map My Posts static map. Recommended for faster pageloads.', 'map-my-posts')
		);
		
		$title = __('Map My Posts Static Map', 'map-my-posts');
		
		$this->WP_Widget( 'MapMyPostsWidgetStatic', $title, $settings );
	}
	
	public function widget_defaults() {
		return array(
			'title'	=> 'Map of My Posts',
			'link'	=> '',
			'region'=> 'world',
			'width'	=> 300,
			'height'=> 250,
			'parent'=> '',
			'maptype' => MapMyPosts::get_setting('maptype'),
			'taxonomy'=> MapMyPosts::get_setting('taxonomy'),
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
		
		if ($instance['link']) {
			echo '<a href="' . $instance['link'] . '">';
		}
		$atts = array(
			'width'	=> $instance['width'],
			'height'=> $instance['height'],
			'parent'=> $instance['parent'],
			'maptype' => $instance['maptype'],
			'taxonomy' => $instance['taxonomy'],
		);
		$mmp = MapMyPosts::get_instance();
		echo $mmp->get_staticmap( $atts );
		if ($instance['link']) {
			echo '</a>';
		}
		
		// $after_widget defined by theme
		echo $after_widget;
	}
	
	// widget controls and settings
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->widget_defaults() );
		include( MAPMYPOSTS_PATH . '/views/widget-form-static.php' );
	}
	
	// save widget settings
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['link'] = strip_tags( $new_instance['link'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['height'] = strip_tags( $new_instance['height'] );
		$instance['parent'] = strip_tags( $new_instance['parent'] );
		$instance['maptype'] = strip_tags( $new_instance['maptype'] );
		$instance['taxonomy'] = strip_tags( $new_instance['taxonomy'] );
		
		return $instance;
	}
}
