<?php
/**
 * Additional css in map
 * @package Maps
 * @author Flipper Code <hello@flippercode.com>
 */

$form->add_element( 'group', 'map_additional_styling', array(
	'value' => esc_html__( 'Additional CSS', 'wp-google-map-plugin' ),
	'before' => '<div class="fc-12">',
	'after' => '</div>',
));

$form->add_element( 'textarea', 'map_all_control[additional_css]', array(
	'lable' => esc_html__( 'Additional CSS', 'wp-google-map-plugin' ),
	'value' => (isset($_POST['map_all_control']['additional_css'])) ? sanitize_text_field($_POST['map_all_control']['additional_css']) : '',
	'textarea_rows' => 10,
	'textarea_name' => 'additional_css',
	'class' => 'form-control',
));

    