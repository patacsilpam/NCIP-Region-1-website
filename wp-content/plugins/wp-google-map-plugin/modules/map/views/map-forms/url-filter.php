<?php
/**
 * Map's Advanced setting(s).
 *
 * @package Maps
 */

$form->add_element(
	'group',
	'url_filter_setting',
	array(
		'value'  => esc_html__('URL Filters Settings', 'wpgmp-google-map').WPGMP_PREMIUM_LINK,
		'before' => '<div class="fc-12">',
		'after'  => '</div>',
		'parent_class'		=> 'fc-locked',
	)
);