<?php
/**
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 5.2.6
 * @package Maps
 */

	$form = new WPGMP_Template();
	$form->set_header( esc_html__( 'Premium Add-Ons / Extentions For WP Google Maps Pro', 'wpgmp-google-map' ), array() );
	$extentions = array();

	$listing = array('url' => 'https://www.wpmapspro.com/product/listing-designs-for-google-maps', 'thumb' => 'listing-designs-on-google-maps.png', 'demo_url' => 'https://www.wpmapspro.com/listing-designs-for-google-maps/');
	$search_widget = array('url' => 'https://www.wpmapspro.com/product/search-widget-for-google-maps', 'thumb' => 'search-widget-for-google-maps.png', 'demo_url' => 'https://www.wpmapspro.com/search-widget-with-google-map/');
	$filter_by_viewport = array('url' => 'https://www.wpmapspro.com/product/filter-map-listing-by-viewport', 'thumb' => 'filter-map-listing-by-viewport.png', 'demo_url' => 'https://www.wpmapspro.com/filter-map-listing-by-viewport/');
	$frontend_submissions = array('url' => 'https://www.wpmapspro.com/product/frontend-submissions-on-google-maps', 'thumb' => 'frontend-submission-thumb.png', 'demo_url' => 'https://www.wpmapspro.com/frontend-submissions-on-google-maps/');
	$user_location = array('url' => 'https://www.wpmapspro.com/product/user-location-on-google-maps', 'thumb' => 'User-Location-on-Google-Maps.png', 'demo_url' => 'https://www.wpmapspro.com/user-location-on-google-maps/');
	$skin_color = array('url' => 'https://www.wpmapspro.com/product/google-maps-skin-color', 'thumb' => 'google-maps-skin-color.png', 'demo_url' => 'https://www.wpmapspro.com/google-maps-skin-color/');
	$migration = array('url' => 'https://www.wpmapspro.com/product/wp-google-maps-migration', 'thumb' => 'wp-google-maps-migration.png', 'demo_url' => '#');
	$mysql = array('url' => 'https://www.wpmapspro.com/product/mysql-to-google-maps', 'thumb' => 'mysql-to-google-maps.png', 'demo_url' => 'https://www.wpmapspro.com/mysql-data-on-google-maps/');
	$excel = array('url' => 'https://www.wpmapspro.com/product/excel-to-google-maps', 'thumb' => 'excel-to-google-maps.png', 'demo_url' => 'https://www.wpmapspro.com/excelsheet-to-google-maps/');
	$airtable = array('url' => 'https://www.wpmapspro.com/product/airtable-data-on-google-maps', 'thumb' => 'airtable-to-googlemaps.png', 'demo_url' => 'https://www.wpmapspro.com/airtable-data-on-google-map/');
	$gravity = array('url' => 'https://www.wpmapspro.com/product/gravity-form-submissions-on-google-maps/', 'thumb' => 'gravity-form-thumb.png', 'demo_url' => 'https://www.wpmapspro.com/gravity-form-entries-googlemaps/');
	$buddypress = array('url' => 'https://www.wpmapspro.com/product/buddypress-members-on-google-maps', 'thumb' => 'buddypress-thumb.png', 'demo_url' => 'https://www.wpmapspro.com/display-buddypress-users-on-google-maps/');
	$cf7 = array('url' => 'https://www.wpmapspro.com/product/cf7-submissions-to-google-maps/', 'thumb' => 'cf7-thumb.png', 'demo_url' => 'https://www.wpmapspro.com/cf7-to-googlemaps/');
	$bookmark = array('url' => 'https://www.wpmapspro.com/product/bookmarks-locations-on-googlemaps', 'thumb' => 'Bookmark-Locations-On-Google-Maps.png', 'demo_url' => 'https://www.wpmapspro.com/locations-bookmark-for-google-maps/');
	$Itinerary = array('url' => 'https://www.wpmapspro.com/product/customer-itinerary-on-google-maps', 'thumb' => 'Customer-Itinerary-On-Google-Maps.png', 'demo_url' => 'https://www.wpmapspro.com/customer-itinerary-on-map/');
	$json = array('url' => 'https://www.wpmapspro.com/product/json-to-google-maps', 'thumb' => 'JSON-To-Google-Maps.png', 'demo_url' => 'https://www.wpmapspro.com/json-to-google-maps/');
	$wpusers = array('url' => 'https://www.wpmapspro.com/product/wordpress-users-on-google-maps', 'thumb' => 'wordpress-users-thumb.png', 'demo_url' => 'https://www.wpmapspro.com/wordpress-users/');
	$request = array('url' => 'https://www.wpmapspro.com/contact/', 'thumb' => 'request-customisation.png', 'demo_url' => ''); 

	$extentions[] =  $listing;
	$extentions[] =  $buddypress;
	$extentions[] =  $search_widget;
	$extentions[] =  $filter_by_viewport;
	$extentions[] =  $frontend_submissions;
	$extentions[] =  $user_location;
	$extentions[] =  $skin_color;
	$extentions[] =  $migration;
	$extentions[] =  $mysql;
	$extentions[] =  $excel;
	$extentions[] =  $airtable;
	$extentions[] =  $gravity;
	$extentions[] =  $bookmark;
	$extentions[] =  $cf7;
	$extentions[] =  $Itinerary;
	$extentions[] =  $json;
	$extentions[] =  $wpusers;
	$extentions[] =  $request;

	$html = '<div class="fc-row">';

	$count = count($extentions);
	foreach($extentions as $key => $addon){

		if($key != 0 && $key % 4 == 0){ $html .= '</div><div class="fc-row">';	}

		if($key == $count -1) {

			$links = '<a target="_blank" href="'.$addon['url'].'">'.esc_html__( 'Contact Now', 'wpgmp-google-map' ).'</a>';
		}else{

			$addon['url'] = add_query_arg( array(  'utm_source' => 'extensions', 'utm_medium' => 'proplugin', 'utm_campaign' => 'pro_extensions'  ),  $addon['url'] );
			
			$links = '<a target="_blank" href="'.$addon['url'].'">'.esc_html__( 'Buy Now', 'wpgmp-google-map' ).'</a>
				<a target="_blank" href="'.$addon['demo_url'].'">'.esc_html__( 'View Demo', 'wpgmp-google-map' ).'</a>';
		}

		$html .= '<div class="fc-3">
			<div class="addon_block">
			<div class="addon_block_overlay">
				'.$links.'
			</div>
			<img src="http://img.flippercode.com/new-addons-thumbnails/'.$addon['thumb'].'"/>
			</div>
   		</div>';
	}
	
	$html .= '</div>';

	$form->add_element(
		'html', 'wpgmp_extentions_listing', array(
			'id'     => 'wpgmp_extentions_listing',
			'class'  => 'wpgmp_extentions_listing',
			'html' => $html,
			'before' => '<div class="fc-12">',
			'after' => '</div>'
		)
	);

    $form->render();
