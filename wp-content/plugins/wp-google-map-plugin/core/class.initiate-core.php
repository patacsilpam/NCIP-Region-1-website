<?php
/**
 *  Load All Core Initialisation class
 *  @package Core
 *  @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'FlipperCode_Initialise_Core' ) ) {


	 class FlipperCode_Initialise_Core {

		public function __construct() {	

			$this->_load_core_files(); 
			$this->_register_flippercode_globals();
		}

		public function _load_core_files() {

			$corePath  = plugin_dir_path( __FILE__ );
			$coreFiles = array(
				'class.tabular.php',
				'class.template.php',
				'abstract.factory.php',
				'class.controller-factory.php',
				'class.model-factory.php',
				'class.controller.php',
				'class.model.php',
				'class.validation.php',
				'class.database.php',
				'class.importer.php',
				'class.plugin-overview.php',
			);

			/**
			 *  Load All Core Initialisation class from core folder
			 */
			foreach ( $coreFiles as $file ) {

				if ( file_exists( $corePath.$file ) ) {
					require_once( $corePath.$file );
				}
			}


		}

		public function _register_flippercode_globals() {

			add_action( 'wp_ajax_core_templates', array( $this, 'fc_load_template' ) );

		}

		function fc_load_template() {

			check_ajax_referer( 'fc-call-nonce', 'nonce' );
			$response      = array();
			$data          = $_POST;
			$core_dir_path = plugin_dir_path( dirname( __FILE__ ) );
			$core_dir_url  = plugin_dir_url( dirname( __FILE__ ) );
			$template      = $data['template_name'];
			$template_type = $data['template_type'];
			

			if ( isset( $data['template_name'] ) ) {
				$layout_file = $core_dir_path . 'templates/' . $template_type . '/' . $template . '/' . $template . '.html';
				$layout_url  = $core_dir_url . 'templates/' . $template_type . '/' . $template . '/' . $template . '.html';
				ob_start();
				include_once $layout_file;
				$content = ob_get_contents();
				ob_clean();
			}

			if ( isset( $data['template_source'] ) ) {
				$content = stripcslashes( $data['template_source'] );
			}

			if ( $content == '' ) {
				$response['html'] = '<div id="messages" class="error">Sorry layout ' . $layout_id . ' not found.</div>';
			} else {
				$temp_content = $content;
				$content      = "<div class='fc-" . $template_type . '-' . $template . "'>" . apply_filters( 'fc-dummy-placeholders', $content ) . '</div>';
				$columns      = isset($data['columns']) ? $data['columns'] : '';
				if ( $columns == '' ) {
					$columns = 1;}
				$parent_div = '<div class="fc-component-block fc-columns-' . $columns . '">';
				for ( $i = 0;$i < $columns;$i++ ) {
					$parent_div .= '<div class="fc-component-content">' . $content . '</div>';
				}
				$parent_div            .= '</div>';
				$response['html']       = $parent_div;
				$response['sourcecode'] = $temp_content;
			}
			echo json_encode( $response );
			exit;

		}

	 }

}
return new FlipperCode_Initialise_Core();
