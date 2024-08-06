<?php
/**
 * Class: WPGMP_Model_Drawing
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Drawing' ) ) {

	/**
	 * Drawing model for Shapes operation.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Drawing extends FlipperCode_Model_Base {
		/**
		 * Intialize drawing object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Drawing Operation
		 *
		 * @return array Admin meny navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_manage_drawing' => esc_html__( 'Map Drawing', 'wpgmp-google-map' ),
			);
		}

		
	}
}
