<?php
/**
 * Class: WPGMP_Model_Route
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Route' ) ) {

	/**
	 * Route model for CRUD operation.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Route extends FlipperCode_Model_Base {
		/**
		 * Validations on route properies.
		 *
		 * @var array
		 */
		protected $validations;
		/**
		 * Intialize route object.
		 */
		function __construct() {

			$this->validations = array(
				'route_title' => array( 'req' => esc_html__( 'Please enter route title.', 'wpgmp-google-map' ) ),
			);

			$this->table  = TBL_ROUTES;
			$this->unique = 'route_id';

		}
		/**
		 * Admin menu for CRUD Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_form_route'   => esc_html__( 'Add Route', 'wpgmp-google-map' ),
				'wpgmp_manage_route' => esc_html__( 'Manage Routes', 'wpgmp-google-map' ),
			);
		}
		

	}
}
