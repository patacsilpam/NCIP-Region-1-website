<?php
/**
 * Contro Positioning over google maps.
 *
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element(
	'group', 'map_styles_settings', array(
		'value'  => esc_html__( 'Map Style Settings', 'wp-google-map-plugin' ),
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'tutorial_link' => 'https://www.wpmapspro.com/docs/how-to-give-different-style-to-your-google-map/'
	)
);


$snazzy_link = '<a href="http://snazzymaps.com" target="_blank">  '.esc_html__( 'Snazzy Maps','wp-google-map-plugin').'</a>';
$slink =  sprintf( esc_html__( 'Get free style for your google maps from %s You can copy javascript style array from there and paste here.', 'wp-google-map-plugin' ), $snazzy_link );

$form->add_element(
	'message', 'styles_message', array(
		'value'  => $slink,
		'class'  => 'alert',
		'id'     => 'styles_message',
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
	)
);


$form->add_element(
	'textarea', 'map_all_control[custom_style]', array(
		'label'         => esc_html__( 'Paste Style here', 'wp-google-map-plugin' ),
		'value'         => ( isset( $_POST['map_all_control']['custom_style'] ) && ! empty( $_POST['map_all_control']['custom_style'] ) ) ? sanitize_text_field($_POST['map_all_control']['custom_style']) : '',
		'desc'          => sprintf( esc_html__( 'Copy google map javascript style array from %s paste here.', 'wp-google-map-plugin' ), $snazzy_link ),
		'textarea_rows' => 20,
		'textarea_name' => 'location_messages',
		'class'         => 'form-control',
		'id'            => 'map_custom_style',
		'before'        => '<div class="fc-11">',
		'after'         => '</div>',
	)
);