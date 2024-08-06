<?php
/**
 * Class: WPGMP_Model_Settings
 * @author Flipper Code <hello@flippercode.com>
 * @version 4.1.6
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Settings' ) ) {

	/**
	 * Setting model for Plugin Options.
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Settings extends FlipperCode_Model_Base {
		/**
		 * Intialize Backup object.
		 */
		function __construct() {
		}
		/**
		 * Admin menu for Settings Operation
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_manage_settings' => esc_html__( 'Plugin Settings', 'wp-google-map-plugin' ),
			);
		}
		/**
		 * Add or Edit Operation.
		 */
		function save() {
			
			global $_POST;
			
			//Permission Verification
			if ( ! current_user_can('administrator') )
			die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if( !isset( $_REQUEST['_wpnonce'] ) || ( isset( $_REQUEST['_wpnonce'] ) && empty($_REQUEST['_wpnonce']) ) )
			die( 'You are not allowed to save changes!' );
			if ( isset( $_REQUEST['_wpnonce'] ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpgmp-nonce' ) )
			die( 'You are not allowed to save changes!' );

			//Check Validations
			$this->verify( $_POST );

			if ( is_array( $this->errors ) and ! empty( $this->errors ) ) {
				$this->throw_errors();
			}
			$extra_fields = array();
			if ( isset( $_POST['location_extrafields'] ) ) {
				foreach ( $_POST['location_extrafields'] as $index => $label ) {
					if ( $label != '' ) {
						$extra_fields[$index] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}

			$meta_hide = array();
			if ( isset( $_POST['wpgmp_allow_meta'] ) ) {
				foreach ( $_POST['wpgmp_allow_meta'] as $index => $label ) {
					if ( $label != '' ) {
						$meta_hide[$index] = sanitize_text_field( wp_unslash( $label ) );
					}
				}
			}
			update_option( 'wpgmp_language',sanitize_text_field( wp_unslash( $_POST['wpgmp_language'] ) ) );
			update_option( 'wpgmp_api_key',sanitize_text_field( wp_unslash( $_POST['wpgmp_api_key'] ) ) );
			update_option( 'wpgmp_scripts_place',sanitize_text_field( wp_unslash( $_POST['wpgmp_scripts_place'] ) ) );
			update_option( 'wpgmp_location_extrafields', serialize(  $extra_fields  ) );
			update_option( 'wpgmp_allow_meta', serialize(  $meta_hide  ));
			
			$wpgmp_settings = get_option( 'wpgmp_settings', true );

			if ( ! is_array( $wpgmp_settings ) ) {
				$wpgmp_settings = array();
			}

			if ( isset( $_POST['wpgmp_auto_fix'] ) ) {

				$wpgmp_settings['wpgmp_auto_fix']         = sanitize_text_field( wp_unslash( $_POST['wpgmp_auto_fix'] ) );
			} else {
				$wpgmp_settings['wpgmp_auto_fix']         = '';
			}

			if ( isset( $_POST['wpgmp_debug_mode'] ) ) {
				$wpgmp_settings['wpgmp_debug_mode']             = sanitize_text_field( wp_unslash( $_POST['wpgmp_debug_mode'] ) );
			} else {
				$wpgmp_settings['wpgmp_debug_mode']             = '';
			}

			if ( isset( $_POST['wpgmp_gdpr'] ) ) {
				$wpgmp_settings['wpgmp_gdpr']             = sanitize_text_field( wp_unslash( $_POST['wpgmp_gdpr'] ) );
			} else {
				$wpgmp_settings['wpgmp_gdpr']             = '';
			}

			if ( isset( $_POST['wpgmp_gdpr_show_placeholder'] ) ) {
				$wpgmp_settings['wpgmp_gdpr_show_placeholder']             = sanitize_text_field( wp_unslash( $_POST['wpgmp_gdpr_show_placeholder'] ) );
			} else {
				$wpgmp_settings['wpgmp_gdpr_show_placeholder']             = '';
			}

			$wpgmp_settings['wpgmp_gdpr_msg']         = wp_unslash( $_POST['wpgmp_gdpr_msg'] );

			$wpgmp_settings = apply_filters('wpgmp_plugin_settings',$wpgmp_settings);
			update_option( 'wpgmp_settings', $wpgmp_settings );

			$response['success'] = esc_html__( 'Setting(s) were saved successfully.','wp-google-map-plugin' );
			return $response;

		}
	}
}
