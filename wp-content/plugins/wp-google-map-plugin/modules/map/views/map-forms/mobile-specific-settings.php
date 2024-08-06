<?php
/**
 * Map's mobile specific setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'group',
	'mobile_specific_settings',
	array(
		'value'  => esc_html__('Screen Specific Settings', 'wpgmp-google-map').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	),
);