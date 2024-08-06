<?php
/**
 * Template for Add & Edit Location
 * @author  Flipper Code <hello@flippercode.com>
 * @package Maps
 */

global $wpdb;
$modelFactory = new WPGMP_Model();
$category_obj = $modelFactory->create_object( 'group_map' );
$categories = $category_obj->fetch();
if ( is_array( $categories ) && ! empty( $categories ) ) {
	$all_categories = array();
	foreach ( $categories as $category ) {
		$all_categories [ $category->group_map_id ] = $category;
	}
}
$location_obj = $modelFactory->create_object( 'location' );
if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_key($_GET['doaction']) && isset( $_GET['location_id'] ) ) {
	$location_obj = $location_obj->fetch( array( array( 'location_id', '=', intval( wp_unslash( $_GET['location_id'] ) ) ) ) );
	$_POST = (array) $location_obj[0];
} elseif ( ! isset( $_GET['doaction'] ) && isset( $response['success'] ) ) {
	// Reset $_POST object for antoher entry.
	unset( $_POST );
}
$form  = new WPGMP_Template();
if( isset($_GET['doaction']) && $_GET['doaction'] == 'edit') {
	$edit_mode_params = array( 'page' => 'wpgmp_form_location','doaction' => 'edit', 'location_id' => intval( $_GET['location_id'] )  );
	$form->form_action = esc_url ( add_query_arg( $edit_mode_params , esc_url( admin_url ('admin.php') ) ) );
}else{
	$form->form_action = esc_url ( add_query_arg( 'page', 'wpgmp_form_location', admin_url ('admin.php') )  );	
}

$form->set_header( esc_html__( 'Location Information', 'wp-google-map-plugin' ), $response, $enable_accordion = true, esc_html__( 'Manage Locations', 'wp-google-map-plugin' ), 'wpgmp_manage_location' );
$form->add_element('hidden', 'form_action_url',  array('value' => $form->form_action ) );


$form->add_element( 'group', 'wpgmp_location_info', array(
	'value' => esc_html__( 'Location Information', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link' => 'https://www.wpmapspro.com/docs/create-a-location-using-simple-method/'
));

if( get_option( 'wpgmp_api_key' ) == '' ) {

$link = '<a href="https://www.wpmapspro.com/docs/how-to-create-an-api-key/" class="wpgmp_map_key_missing" target="_blank">'.esc_html__('create google maps api key','wp-google-map-plugin').'</a>';
	$setting_link = '<a target="_blank" href="' . admin_url( 'admin.php?page=wpgmp_manage_settings' ) . '">'.esc_html__('here','wp-google-map-plugin').'</a>';
	
$form->add_element( 'message', 'wpgmp_key_required', array(
	'value' => sprintf( esc_html__( 'Google Maps API Key is missing. Follow instructions to %1$s and then insert your key %2$s.', 'wp-google-map-plugin' ), $link, $setting_link ),
	'class' => 'fc-msg fc-danger',
	'before' => '<div class="fc-12 wpgmp_key_required">',
	'after' => '</div>',
));

}


$form->add_element( 'text', 'location_title', array(
	'lable' => esc_html__( 'Location Title', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_title'] ) && ! empty( $_POST['location_title'] )) ? sanitize_text_field($_POST['location_title']) : '',
	'required' => true,
	'placeholder' => esc_html__( 'Enter Location Title', 'wp-google-map-plugin' ),
));

$form->add_element( 'text', 'location_address', array(
	'lable' => esc_html__( 'Location Address', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_address'] ) && ! empty( $_POST['location_address'] )) ? sanitize_text_field( $_POST['location_address'] ) : '',
	'desc' => esc_html__( 'Start typing and choose an address from the google\'s auto suggest list. Choosing the place will automatically detect latitude & longitude.', 'wp-google-map-plugin' ),
	'required' => true,
	'class' => 'form-control wpgmp_auto_suggest',
	'placeholder' => esc_html__( 'Type Location Address', 'wp-google-map-plugin' ),
));
$form->set_col( 2 );
$form->add_element( 'text', 'location_latitude', array(
	'lable' => esc_html__( 'Latitude and Longitude', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_latitude'] ) && ! empty( $_POST['location_latitude'] )) ? sanitize_text_field( $_POST['location_latitude'] ) : '',
	'id' => 'googlemap_latitude',
	'required' => true,
	'class' => 'google_latitude form-control',
	'placeholder' => esc_html__( 'Latitude', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_longitude', array(
	'value' => (isset( $_POST['location_longitude'] ) && ! empty( $_POST['location_longitude'] )) ? sanitize_text_field( $_POST['location_longitude'] ) : '',
	'id' => 'googlemap_longitude',
	'required' => true,
	'class' => 'google_longitude form-control',
	'placeholder' => esc_html__( 'Longitude', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_city', array(
	'lable' => esc_html__( 'City and State', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_city'] ) && ! empty( $_POST['location_city'] )) ? sanitize_text_field( $_POST['location_city']) : '',
	'id' => 'googlemap_city',
	'class' => 'google_city form-control',
	'placeholder' => esc_html__( 'City', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_state', array(
	'value' => (isset( $_POST['location_state'] ) && ! empty( $_POST['location_state'] )) ? sanitize_text_field($_POST['location_state']) : '',
	'id' => 'googlemap_state',
	'class' => 'google_state form-control',
	'placeholder' => esc_html__( 'State', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_country', array(
	'lable' => esc_html__( 'Country and Postal Code', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_country'] ) && ! empty( $_POST['location_country'] )) ? sanitize_text_field( $_POST['location_country'] ) : '',
	'id' => 'googlemap_country',
	'class' => 'google_country form-control',
	'placeholder' => esc_html__( 'Country', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->add_element( 'text', 'location_postal_code', array(
	'value' => (isset( $_POST['location_postal_code'] ) && ! empty( $_POST['location_postal_code'] )) ? sanitize_text_field( $_POST['location_postal_code'] ) : '',
	'id' => 'googlemap_postal_code',
	'class' => 'google_postal_code form-control',
	'placeholder' => esc_html__( 'Postal Code', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-4">',
	'after' => '</div>',
));
$form->set_col( 1 );
$form->add_element( 'div', 'wpgmp_map', array(
	'lable' => esc_html__( 'Current Location', 'wp-google-map-plugin' ),
	'id' => 'wpgmp_map',
	'style' => array( 'width' => '100%' ,'height' => '300px' ),
));


$form->add_element( 'radio', 'location_settings[onclick]', array(
	'lable' => esc_html__( 'On Click', 'wp-google-map-plugin' ),
	'radio-val-label' => array( 'marker' => esc_html__( 'Display Infowindow','wp-google-map-plugin' ),'custom_link' => esc_html__( 'Redirect','wp-google-map-plugin' ) ),
	'current' => (isset($_POST['location_settings']['onclick'])) ? sanitize_text_field( $_POST['location_settings']['onclick']) : '',
	'class' => 'chkbox_class switch_onoff',
	'default_value' => 'marker',
	'data' => array( 'target' => '.wpgmp_location_onclick' ),
));


$form->add_element( 'textarea', 'location_messages', array(
	'lable' => esc_html__( 'Infowindow Message', 'wp-google-map-plugin' ),
	'value' => (isset( $_POST['location_messages'] ) && ! empty( $_POST['location_messages'] )) ?  wp_kses_post( $_POST['location_messages'] )  : '',
	'desc' => esc_html__( 'Enter here the infoWindow message.', 'wp-google-map-plugin' ),
	'textarea_rows' => 10,
	'textarea_name' => 'location_messages',
	'class' => 'form-control wpgmp_location_onclick wpgmp_location_onclick_marker',
	'id' => 'googlemap_infomessage',
	'show' => 'false',
));

$form->add_element( 'text', 'location_settings[redirect_link]', array(
	'lable' => esc_html__( 'Redirect Url','wp-google-map-plugin' ),
	'value' => isset($_POST['location_settings']['redirect_link']) ? sanitize_text_field($_POST['location_settings']['redirect_link']) : '',
	'desc' => esc_html__( 'Enter here the redirect url. e.g http://www.flippercode.com', 'wp-google-map-plugin' ),
	'class' => 'wpgmp_location_onclick_custom_link wpgmp_location_onclick form-control',
	'before' => '<div class="fc-8">',
	'after' => '</div>',
	'show' => 'false',
));

$form->add_element( 'select', 'location_settings[redirect_link_window]', array(
	'options' => array( 'yes' => esc_html__( 'YES','wp-google-map-plugin' ), 'no' => esc_html__( 'NO','wp-google-map-plugin' ) ),
	'lable' => esc_html__( 'Open new tab','wp-google-map-plugin' ),
	'current' => (isset($_POST['location_settings']['redirect_link_window'])) ? sanitize_text_field($_POST['location_settings']['redirect_link_window']) : '',
	'desc' => esc_html__( 'Open a new window tab.', 'wp-google-map-plugin' ),
	'class' => 'wpgmp_location_onclick_redirect wpgmp_location_onclick form-control',
	'before' => '<div class="fc-2">',
	'after' => '</div>',
	'show' => 'false',
));


$form->add_element(
	'image_picker', 'location_settings[featured_image]', array(
		'lable'         => esc_html__( 'Location Image', 'wpgmp-google-map' ),
		'src'           => isset( $_POST['location_settings']['featured_image'] ) ? wp_unslash( $_POST['location_settings']['featured_image'] ) : '',
		'required'      => false,
		'choose_button' => esc_html__( 'Choose', 'wpgmp-google-map' ),
		'remove_button' => esc_html__( 'Remove', 'wpgmp-google-map' ),
		'id' => 'loc_img',
	)
);


$form->add_element( 'checkbox', 'location_infowindow_default_open', array(
	'lable' => esc_html__( 'Infowindow Default Open', 'wp-google-map-plugin' ),
	'value' => 'true',
	'id' => 'location_infowindow_default_open',
	'current' => (isset($_POST['location_infowindow_default_open'])) ? sanitize_text_field($_POST['location_infowindow_default_open']) : '',
	'desc' => esc_html__( 'Check to enable infowindow default open.', 'wp-google-map-plugin' ),
	'class' => 'chkbox_class',
));
$form->add_element( 'checkbox', 'location_draggable', array(
	'lable' => esc_html__( 'Marker Draggable', 'wp-google-map-plugin' ),
	'value' => 'true',
	'id' => 'location_draggable',
	'current' => isset($_POST['location_draggable']) ? sanitize_text_field($_POST['location_draggable']) : '' ,
	'desc' => esc_html__( 'Check if you want to allow visitors to drag the marker.', 'wp-google-map-plugin' ),
	'class' => 'chkbox_class',
));
$form->add_element( 'select', 'location_animation', array(
	'lable' => esc_html__( 'Marker Animation', 'wp-google-map-plugin' ),
	'current' => (isset( $_POST['location_animation'] ) && ! empty( $_POST['location_animation'] )) ? sanitize_text_field($_POST['location_animation']) : '',
	'options' => array( 'BOUNCE' => esc_html__('Bounce','wp-google-map-plugin'), 'DROP' => esc_html__('DROP','wp-google-map-plugin') ),
	'before' => '<div class="fc-3">',
	'after' => '</div>',
));
$form->add_element(
	'group',
	'location_extra_fields',
	array(
		'value'  => esc_html__('Extra Fields Values', 'wpgmp-google-map').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);
$form->add_element( 'group', 'marker_category_listing', array(
	'value' => esc_html__( 'Marker Categories', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
	'tutorial_link' => 'https://www.wpmapspro.com/docs/how-to-assign-multiple-categories-to-a-single-locaton/'
));

if ( ! empty( $all_categories ) ) {
	$category_data = array();
	$parent_category_data = array();


	if ( ! isset($_POST['location_group_map'] ) ) {
		$data['location_group_map'] = array(); }

	foreach ( $categories as $category ) {
	
		if ( is_null( $category->group_parent ) or 0 == $category->group_parent ) {
			$parent_category_data = ' ---- ';
		} else {
			if(isset($all_categories[ $category->group_parent ]->group_map_title)){
				$parent_category_data = $all_categories[ $category->group_parent ]->group_map_title;
			}else{
				$parent_category_data = '';
			}
			
		}
		if ( '' != $category->group_marker ) {
			$icon_src = "<img src='".$category->group_marker."' />";
		} else {
			$icon_src = "<img src='".WPGMP_IMAGES."default_marker.png' />";

		}
		$select_input = $form->field_checkbox('location_group_map[]',array(
			'value' => $category->group_map_id,
			'current' => ( isset($_POST['location_group_map']) && in_array( $category->group_map_id, $_POST['location_group_map'] ) ) ? sanitize_text_field($category->group_map_id) : '',
			'class' => 'chkbox_class',
			'before' => '<div class="fc-1">',
			'after' => '</div>',
			));
		
		$category_data[] = array( $select_input,$category->group_map_title,$parent_category_data,$icon_src );
	}
	$category_data = $form->add_element( 'table', 'location_group_map', array(
		'heading' => array( 'Select','Category','Parent','Icon' ),
		'data' => $category_data,
		'id' => 'location_group_map',
		'class' => 'fc-table fc-table-layout3',
		'before' => '<div class="fc-12">',
		'after' => '</div>',
		));
		 
} else {
	$form->add_element( 'message', 'message', array(
		'value' => esc_html__( 'You don\'t have categorie(s).', 'wp-google-map-plugin' ),
		'class' => 'fc-msg fc-msg-info',
		'before' => '<div class="fc-12">',
		'after' => '</div>',
	));
}

$form->add_element('extensions','wpgmp_location_form',array(
	'value' => (isset($_POST['location_settings']['extensions_fields'])) ? sanitize_text_field($_POST['location_settings']['extensions_fields']) : array(),
	'before' => '<div class="fc-11">',
	'after' => '</div>',
	));

$form->add_element( 'submit', 'save_entity_data', array(
	'value' => esc_html__( 'Save Location','wp-google-map-plugin' ),
));
$form->add_element( 'hidden', 'operation', array(
	'value' => 'save',
));
if ( isset( $_GET['doaction'] ) && 'edit' == sanitize_key($_GET['doaction']) ) {

	$form->add_element( 'hidden', 'entityID', array(
		'value' => intval( wp_unslash( $_GET['location_id'] ) ),
	));
}
$form->render();

$infowindow_message  = (isset( $_POST['location_messages'] ) && ! empty( $_POST['location_messages'] )) ? sanitize_text_field($_POST['location_messages']) : '';
$infowindow_disable = (isset( $_POST['location_settings'] ) && ! empty( $_POST['location_settings'] )) ? sanitize_text_field($_POST['location_settings']) : '';
if(isset($_GET['group_map_id'])) {
$category_obj = $category_obj->get( array( array( 'group_map_id', '=', intval( wp_unslash( $_GET['group_map_id'] ) ) ) ) );
$category = (array) $category_obj[0];
}

if(isset($_POST['location_group_map'][0]))
$ckey =  $_POST['location_group_map'][0];
$category_group_marker = '';
if ( ! empty( $category->group_marker ) && !empty($data['location_group_map']) && isset($all_categories[$ckey]) ) {
	$category_group_marker = $all_categories[$ckey]->group_marker;
} else {
	$category_group_marker = WPGMP_IMAGES.'default_marker.png';
}
$map_data['map_options'] = array(
'center_lat'  => (isset( $_POST['location_latitude'] ) && ! empty( $_POST['location_latitude'] )) ? sanitize_text_field($_POST['location_latitude']) : '',
'center_lng'  => (isset( $_POST['location_longitude'] ) && ! empty( $_POST['location_longitude'] )) ? sanitize_text_field($_POST['location_longitude']) : '',
);
$map_data['places'][] = array(
'id'          => (isset( $_POST['location_id'] ) && ! empty( $_POST['location_id'] )) ? sanitize_text_field($_POST['location_id']) : '',
'title'       => (isset( $_POST['location_title'] ) && ! empty( $_POST['location_title'] )) ? sanitize_text_field($_POST['location_title']) : '',
'content'     => $infowindow_message,
'location'    => array(
'icon'      => $category_group_marker,
'lat'       => (isset( $_POST['location_latitude'] ) && ! empty( $_POST['location_latitude'] )) ? sanitize_text_field($_POST['location_latitude']) : '',
'lng'       => (isset( $_POST['location_longitude'] ) && ! empty( $_POST['location_longitude'] )) ? sanitize_text_field($_POST['location_longitude']) : '',
'draggable' => true,
'infowindow_default_open' => (isset( $_POST['location_infowindow_default_open'] ) && ! empty( $_POST['location_infowindow_default_open'] )) ? sanitize_text_field($_POST['location_infowindow_default_open']) : '',
'animation' => (isset( $_POST['location_animation'] ) && ! empty( $_POST['location_animation'] )) ? sanitize_text_field($_POST['location_animation']) : '',
'infowindow_disable' => ( isset($infowindow_disable['hide_infowindow']) && 'false' === @$infowindow_disable['hide_infowindow']),
'zoom'      => (isset( $_POST['location_zoom'] ) && ! empty( $_POST['location_zoom'] )) ? sanitize_text_field($_POST['location_zoom']) : '',
),
'categories'  => array( array(
'id'      => (isset($category->group_map_id)) ? $category->group_map_id : '',
'name'    => (isset($category->group_map_title)) ? $category->group_map_title : '',
'type'    => 'category',
'icon'    => $category_group_marker,
),
),
);
$map_data['page'] = 'edit_location';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
   var map = $("#wpgmp_map").maps("<?php echo esc_js( base64_encode( wp_json_encode( $map_data ) ) ); ?>").data('wpgmp_maps');
});
</script>
