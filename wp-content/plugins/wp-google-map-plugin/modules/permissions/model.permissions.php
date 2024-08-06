<?php
/**
 * Class: WPGMP_Model_Permissions
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Permissions' ) ) {

	/**
	 * Permission model for Plugin Access Permission.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Permissions extends FlipperCode_Model_Base {
		/**
		 * Intialize Permission object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Permission Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_manage_permissions' => esc_html__( 'Manage Permissions', 'wpgmp-google-map' ),
			);
		}
		

	}
}
