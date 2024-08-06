<?php
/**
 * Class: WPGMP_Model_Tools
 *
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Maps
 */

if ( ! class_exists( 'WPGMP_Model_Tools' ) ) {

	/**
	 * Backup model for Backup operation.
	 *
	 * @package Maps
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WPGMP_Model_Tools extends FlipperCode_Model_Base {

		/**
		 * Intialize Backup object.
		 */
		function __construct() {

		}
		/**
		 * Admin menu for Backup Operation
		 *
		 * @return array Admin menu navigation(s).
		 */
		function navigation() {
			return array(
				'wpgmp_manage_tools' => esc_html__( 'Plugin Tools', 'wpgmp-google-map' ),
			);
		}
		/**
		 * Install table associated with Location entity.
		 *
		 * @return string SQL query to install map_locations table.
		 */
		function install() {

		}
		/**
		 * Upload backup from .sql file.
		 *
		 * @return string Success or Error response.
		 */
		public function clean_database() {
			global $_POST;

			if ( isset( $_REQUEST['_wpnonce'] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

				if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

					die( 'Cheating...' );

				} else {
					$data = $_POST;
				}
			}

			if ( isset( $data['wpgmp_cleandatabase_tools'] ) ) {

				if( empty($data['wpgmp_clean_consent']) || (!empty($data['wpgmp_clean_consent']) && $data['wpgmp_clean_consent'] != 'DELETE' ) ){
					$response['error'] = esc_html__( 'Please entery "DELETE" in the provided textbox and then proceed to clear plugin\'s database.', 'wpgmp-google-map' );
					return $response;
				}  

				if ( !empty( $data['wpgmp_clean_consent'] ) && $data['wpgmp_clean_consent'] == 'DELETE' ) {

					$backup_tables = array( TBL_LOCATION, TBL_GROUPMAP, TBL_MAP );
					$connection    = FlipperCode_Database::connect();
					foreach ( $backup_tables as  $table ) {
						$this->query = $connection->prepare( "DELETE FROM $table where %d", 1 );
						FlipperCode_Database::non_query( $this->query, $connection );
					}

					$response['success'] = esc_html__( 'All the saved locations, marker categories and maps were removed.', 'wpgmp-google-map' );
				} 
			} else {

				$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wpgmp-google-map' );
			}
			return $response;

		}
		/**
		 * Take backup to .sql file.
		 *
		 * @return string Success or Error response.
		 */
		public function upload_sampledata() {

			if ( isset( $_REQUEST['_wpnonce'] ) ) {

				$nonce = sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) );

				if ( ! wp_verify_nonce( $nonce, 'wpgmp-nonce' ) ) {

					die( 'Cheating...' );

				} else {
					$data = $_POST;
				}
			}
			if ( isset( $_POST['wpgmp_sampledata_consent'] ) ) {

				if ( isset( $data['wpgmp_sampledata_consent'] ) && $data['wpgmp_sampledata_consent'] == 'YES' ) {

					global $wpdb;

					$success = true;

					$category_ids = array();

					$sample_data             = array();
					$sample_data['category'] = array(

						'category 1' => array( WPGMP_IMAGES . '/icons/1-generic.png', 1 ),
						'category 2' => array( WPGMP_IMAGES . '/icons/2-generic.png', 2 ),
					);

					foreach ( $sample_data['category'] as $title => $category ) {
						$sdata                      = array();
						$sdata['group_map_title']   = $title;
						$sdata['group_parent']      = 0;
						$sdata['group_marker']      = wp_unslash( $category[0] );
						$sdata['extensions_fields'] = serialize( wp_unslash( array( 'cat_order' => $category[1] ) ) );
						$category_ids[]             = FlipperCode_Database::insert_or_update( TBL_GROUPMAP, $sdata, $where = '' );
					}

					$sample_data['locations'] = array(

						'location 1' => array( 'San Diego State University, San Diego, CA, United States', '32.7757217', '-117.0718893', $category_ids[0], 'This is sample description about the location.', 'San Diego', 'CA', 'United States' ),
						'location 2' => array( 'The University of Texas At El Paso, West University Avenue, El Paso, TX, United States', '31.7708544', '-106.5046216', $category_ids[0], 'This is sample description about the location.', 'El Paso', 'TX', 'United States' ),
						'location 3' => array( 'University of Virginia, Charlottesville, VA, United States', '38.0335529', '-78.5079772', $category_ids[0], 'This is sample description about the location.', 'El Paso', 'TX', 'United States' ),
						'location 4' => array( 'Lincoln University, Baltimore Pike, PA, USA', '39.808079', '-75.927453', $category_ids[1], 'This is sample description about the location.', 'Baltimore Pike', 'PA', 'USA' ),
						'location 5' => array( 'Texas Woman University, Administration Drive, Denton, TX, United States', '33.2263112', '-97.1281615', $category_ids[1], 'This is sample description about the location.', 'Denton', 'TX', 'United States' ),

					);

					foreach ( $sample_data['locations'] as $title => $location ) {

						$sdata                       = array();
						$sdata['location_messages']  = wp_unslash( $location[4] );
						$sdata['location_group_map'] = serialize( wp_unslash( array( $location[3] ) ) );
						$sdata['location_title']     = $title;
						$sdata['location_address']   = $location[0];
						$sdata['location_latitude']  = $location[1];
						$sdata['location_longitude'] = $location[2];
						$sdata['location_city']      = $location[5];
						$sdata['location_state']     = $location[6];
						$sdata['location_country']   = $location[7];
						$sdata['location_author']    = get_current_user_id();
						$location_ids[]              = FlipperCode_Database::insert_or_update( TBL_LOCATION, $sdata, $where = '' );
					}


					$sample_data['maps'] = array(

						'map 1' => 'Tzo4OiJzdGRDbGFzcyI6MjI6e3M6NjoibWFwX2lkIjtzOjE6IjEiO3M6OToibWFwX3RpdGxlIjtzOjE4OiJBbGwgSW4gT25lIExpc3RpbmciO3M6OToibWFwX3dpZHRoIjtzOjA6IiI7czoxMDoibWFwX2hlaWdodCI7czozOiI0MDAiO3M6MTQ6Im1hcF96b29tX2xldmVsIjtzOjE6IjMiO3M6ODoibWFwX3R5cGUiO3M6NzoiUk9BRE1BUCI7czoxOToibWFwX3Njcm9sbGluZ193aGVlbCI7czowOiIiO3M6MTg6Im1hcF92aXN1YWxfcmVmcmVzaCI7TjtzOjEzOiJtYXBfNDVpbWFnZXJ5IjtzOjA6IiI7czoyMzoibWFwX3N0cmVldF92aWV3X3NldHRpbmciO2E6Mjp7czoxMToicG92X2hlYWRpbmciO3M6MDoiIjtzOjk6InBvdl9waXRjaCI7czowOiIiO31zOjI3OiJtYXBfcm91dGVfZGlyZWN0aW9uX3NldHRpbmciO2E6Mjp7czoxNToicm91dGVfZGlyZWN0aW9uIjtzOjQ6InRydWUiO3M6MTU6InNwZWNpZmljX3JvdXRlcyI7YToyOntpOjA7czoxOiIxIjtpOjE7czoxOiIyIjt9fXM6MTU6Im1hcF9hbGxfY29udHJvbCI7YTo5NTp7czoxNzoibWFwX21pbnpvb21fbGV2ZWwiO3M6MToiMCI7czoxNzoibWFwX21heHpvb21fbGV2ZWwiO3M6MjoiMTkiO3M6NzoiZ2VzdHVyZSI7czo0OiJhdXRvIjtzOjc6InNjcmVlbnMiO2E6Mzp7czoxMToic21hcnRwaG9uZXMiO2E6Mzp7czoxNjoibWFwX3dpZHRoX21vYmlsZSI7czowOiIiO3M6MTc6Im1hcF9oZWlnaHRfbW9iaWxlIjtzOjA6IiI7czoyMToibWFwX3pvb21fbGV2ZWxfbW9iaWxlIjtzOjE6IjUiO31zOjU6ImlwYWRzIjthOjM6e3M6MTY6Im1hcF93aWR0aF9tb2JpbGUiO3M6MDoiIjtzOjE3OiJtYXBfaGVpZ2h0X21vYmlsZSI7czowOiIiO3M6MjE6Im1hcF96b29tX2xldmVsX21vYmlsZSI7czoxOiI1Ijt9czoxMzoibGFyZ2Utc2NyZWVucyI7YTozOntzOjE2OiJtYXBfd2lkdGhfbW9iaWxlIjtzOjA6IiI7czoxNzoibWFwX2hlaWdodF9tb2JpbGUiO3M6MDoiIjtzOjIxOiJtYXBfem9vbV9sZXZlbF9tb2JpbGUiO3M6MToiNSI7fX1zOjE5OiJtYXBfY2VudGVyX2xhdGl0dWRlIjtzOjk6IjM3LjA3OTc0NCI7czoyMDoibWFwX2NlbnRlcl9sb25naXR1ZGUiO3M6MTA6Ii05MC4zMDM4NTIiO3M6MjM6ImNlbnRlcl9jaXJjbGVfZmlsbGNvbG9yIjtzOjc6IiM4Q0FFRjIiO3M6MjU6ImNlbnRlcl9jaXJjbGVfZmlsbG9wYWNpdHkiO3M6MjoiLjUiO3M6MjU6ImNlbnRlcl9jaXJjbGVfc3Ryb2tlY29sb3IiO3M6NzoiIzhDQUVGMiI7czoyNzoiY2VudGVyX2NpcmNsZV9zdHJva2VvcGFjaXR5IjtzOjI6Ii41IjtzOjI2OiJjZW50ZXJfY2lyY2xlX3N0cm9rZXdlaWdodCI7czoxOiIxIjtzOjIwOiJjZW50ZXJfY2lyY2xlX3JhZGl1cyI7czoxOiI1IjtzOjI5OiJzaG93X2NlbnRlcl9tYXJrZXJfaW5mb3dpbmRvdyI7czowOiIiO3M6MTg6Im1hcmtlcl9jZW50ZXJfaWNvbiI7czoxMDE6Imh0dHA6Ly8xMjcuMC4wLjEvZmNsYWJzL3dwZ21wL3dwLWNvbnRlbnQvcGx1Z2lucy93cC1nb29nbGUtbWFwLWdvbGQvYXNzZXRzL2ltYWdlcy8vZGVmYXVsdF9tYXJrZXIucG5nIjtzOjE5OiJnbV9yYWRpdXNfZGltZW5zaW9uIjtzOjU6Im1pbGVzIjtzOjk6ImdtX3JhZGl1cyI7czozOiIxMDAiO3M6MjE6Inpvb21fY29udHJvbF9wb3NpdGlvbiI7czo4OiJUT1BfTEVGVCI7czoxODoiem9vbV9jb250cm9sX3N0eWxlIjtzOjU6IkxBUkdFIjtzOjI1OiJtYXBfdHlwZV9jb250cm9sX3Bvc2l0aW9uIjtzOjk6IlRPUF9SSUdIVCI7czoyMjoibWFwX3R5cGVfY29udHJvbF9zdHlsZSI7czoxNDoiSE9SSVpPTlRBTF9CQVIiO3M6Mjg6ImZ1bGxfc2NyZWVuX2NvbnRyb2xfcG9zaXRpb24iO3M6OToiVE9QX1JJR0hUIjtzOjI4OiJzdHJlZXRfdmlld19jb250cm9sX3Bvc2l0aW9uIjtzOjg6IlRPUF9MRUZUIjtzOjIzOiJzZWFyY2hfY29udHJvbF9wb3NpdGlvbiI7czo4OiJUT1BfTEVGVCI7czoyNToibG9jYXRlbWVfY29udHJvbF9wb3NpdGlvbiI7czo4OiJUT1BfTEVGVCI7czoyMDoibWFwX2NvbnRyb2xfc2V0dGluZ3MiO2E6MDp7fXM6MjE6ImluZm93aW5kb3dfb3Blbm9wdGlvbiI7czo1OiJjbGljayI7czoxOToibWFya2VyX2RlZmF1bHRfaWNvbiI7czoxMDE6Imh0dHA6Ly8xMjcuMC4wLjEvZmNsYWJzL3dwZ21wL3dwLWNvbnRlbnQvcGx1Z2lucy93cC1nb29nbGUtbWFwLWdvbGQvYXNzZXRzL2ltYWdlcy8vZGVmYXVsdF9tYXJrZXIucG5nIjtzOjI3OiJpbmZvd2luZG93X2JvdW5jZV9hbmltYXRpb24iO3M6MDoiIjtzOjIwOiJpbmZvd2luZG93X3pvb21sZXZlbCI7czowOiIiO3M6MTY6ImluZm93aW5kb3dfd2lkdGgiO3M6MDoiIjtzOjIzOiJpbmZvd2luZG93X2JvcmRlcl9jb2xvciI7czoxOiIjIjtzOjI0OiJpbmZvd2luZG93X2JvcmRlcl9yYWRpdXMiO3M6MDoiIjtzOjE5OiJpbmZvd2luZG93X2JnX2NvbG9yIjtzOjE6IiMiO3M6MjQ6ImxvY2F0aW9uX2luZm93aW5kb3dfc2tpbiI7YTozOntzOjQ6Im5hbWUiO3M6NzoiZGVmYXVsdCI7czo0OiJ0eXBlIjtzOjEwOiJpbmZvd2luZG93IjtzOjEwOiJzb3VyY2Vjb2RlIjtzOjI1MzoiPGRpdiBjbGFzcz0iZmMtbWFpbiI+PGRpdiBjbGFzcz0iZmMtaXRlbS10aXRsZSI+e21hcmtlcl90aXRsZX0gPHNwYW4gY2xhc3M9ImZjLWJhZGdlIGluZm8iPnttYXJrZXJfY2F0ZWdvcnl9PC9zcGFuPjwvZGl2PiA8ZGl2IGNsYXNzPSJmYy1pdGVtLWZlYXR1cmVkX2ltYWdlIj57bWFya2VyX2ltYWdlfSA8L2Rpdj57bWFya2VyX21lc3NhZ2V9PGFkZHJlc3M+PGI+QWRkcmVzcyA6IDwvYj57bWFya2VyX2FkZHJlc3N9PC9hZGRyZXNzPjwvZGl2PiI7fXM6MjA6InBvc3RfaW5mb3dpbmRvd19za2luIjthOjM6e3M6NDoibmFtZSI7czo3OiJkZWZhdWx0IjtzOjQ6InR5cGUiO3M6NDoicG9zdCI7czoxMDoic291cmNlY29kZSI7czozNTA6IjxkaXYgY2xhc3M9ImZjLW1haW4iPjxkaXYgY2xhc3M9ImZjLWl0ZW0tdGl0bGUiPntwb3N0X3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e3Bvc3RfY2F0ZWdvcmllc308L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPntwb3N0X2ZlYXR1cmVkX2ltYWdlfSA8L2Rpdj57cG9zdF9leGNlcnB0fTxhZGRyZXNzPjxiPkFkZHJlc3MgOiA8L2I+e21hcmtlcl9hZGRyZXNzfTwvYWRkcmVzcz48YSB0YXJnZXQ9Il9ibGFuayIgIGNsYXNzPSJmYy1idG4gZmMtYnRuLXNtYWxsIGZjLWJ0bi1yZWQiIGhyZWY9Intwb3N0X2xpbmt9Ij5SZWFkIE1vcmUuLi48L2E+PC9kaXY+Ijt9czoyMDoid3BnbXBfYWNmX2ZpZWxkX25hbWUiO3M6MDoiIjtzOjEyOiJjdXN0b21fc3R5bGUiO3M6MDoiIjtzOjEzOiJmcm9tX2xhdGl0dWRlIjtzOjA6IiI7czoxNDoiZnJvbV9sb25naXR1ZGUiO3M6MDoiIjtzOjExOiJ0b19sYXRpdHVkZSI7czowOiIiO3M6MTI6InRvX2xvbmdpdHVkZSI7czowOiIiO3M6MTA6Inpvb21fbGV2ZWwiO3M6MDoiIjtzOjIzOiJkaXNwbGF5X21hcmtlcl9jYXRlZ29yeSI7czo0OiJ0cnVlIjtzOjE4OiJ3cGdtcF9jYXRlZ29yeV90YWIiO3M6NDoidHJ1ZSI7czoyNDoid3BnbXBfY2F0ZWdvcnlfdGFiX3RpdGxlIjtzOjEwOiJDYXRlZ29yaWVzIjtzOjIwOiJ3cGdtcF9jYXRlZ29yeV9vcmRlciI7czo1OiJ0aXRsZSI7czozNDoid3BnbXBfY2F0ZWdvcnlfbG9jYXRpb25fc29ydF9vcmRlciI7czozOiJhc2MiO3M6MTk6IndwZ21wX2RpcmVjdGlvbl90YWIiO3M6NDoidHJ1ZSI7czoyNToid3BnbXBfZGlyZWN0aW9uX3RhYl90aXRsZSI7czoxMDoiRGlyZWN0aW9ucyI7czoxOToid3BnbXBfdW5pdF9zZWxlY3RlZCI7czoyOiJrbSI7czoyNToid3BnbXBfZGlyZWN0aW9uX3RhYl9zdGFydCI7czo3OiJ0ZXh0Ym94IjtzOjMzOiJ3cGdtcF9kaXJlY3Rpb25fdGFiX3N0YXJ0X2RlZmF1bHQiO3M6MDoiIjtzOjIzOiJ3cGdtcF9kaXJlY3Rpb25fdGFiX2VuZCI7czo3OiJ0ZXh0Ym94IjtzOjMxOiJ3cGdtcF9kaXJlY3Rpb25fdGFiX2VuZF9kZWZhdWx0IjtzOjA6IiI7czoyMjoid3BnbXBfbmVhcmJ5X3RhYl90aXRsZSI7czoxMzoiTmVhcmJ5IFBsYWNlcyI7czoyMzoibmVhcmJ5X2NpcmNsZV9maWxsY29sb3IiO3M6NzoiIzhDQUVGMiI7czoyNToibmVhcmJ5X2NpcmNsZV9maWxsb3BhY2l0eSI7czoyOiIuNSI7czoyNToibmVhcmJ5X2NpcmNsZV9zdHJva2Vjb2xvciI7czo3OiIjOENBRUYyIjtzOjI3OiJuZWFyYnlfY2lyY2xlX3N0cm9rZW9wYWNpdHkiO3M6MjoiLjUiO3M6MjY6Im5lYXJieV9jaXJjbGVfc3Ryb2tld2VpZ2h0IjtzOjE6IjEiO3M6MTg6Im5lYXJieV9jaXJjbGVfem9vbSI7czoxOiI4IjtzOjE1OiJ3cGdtcF9yb3V0ZV90YWIiO3M6NDoidHJ1ZSI7czoyMToid3BnbXBfcm91dGVfdGFiX3RpdGxlIjtzOjY6IlJvdXRlcyI7czoxNDoiY3VzdG9tX2ZpbHRlcnMiO2E6MDp7fXM6MjE6Im1hcF9yZXNldF9idXR0b25fdGV4dCI7czo1OiJSZXNldCI7czoxNToiZGlzcGxheV9saXN0aW5nIjtzOjQ6InRydWUiO3M6MjA6IndwZ21wX3NlYXJjaF9kaXNwbGF5IjtzOjQ6InRydWUiO3M6MjQ6InNlYXJjaF9maWVsZF9hdXRvc3VnZ2VzdCI7czo0OiJ0cnVlIjtzOjI5OiJ3cGdtcF9kaXNwbGF5X2NhdGVnb3J5X2ZpbHRlciI7czo0OiJ0cnVlIjtzOjI4OiJ3cGdtcF9kaXNwbGF5X3NvcnRpbmdfZmlsdGVyIjtzOjQ6InRydWUiO3M6Mjc6IndwZ21wX2Rpc3BsYXlfcmFkaXVzX2ZpbHRlciI7czo0OiJ0cnVlIjtzOjIyOiJ3cGdtcF9yYWRpdXNfZGltZW5zaW9uIjtzOjU6Im1pbGVzIjtzOjIwOiJ3cGdtcF9yYWRpdXNfb3B0aW9ucyI7czoyODoiNSwxMCwxNSwyMCwyNSw1MCwxMDAsMjAwLDUwMCI7czozODoid3BnbXBfZGlzcGxheV9sb2NhdGlvbl9wZXJfcGFnZV9maWx0ZXIiO3M6NDoidHJ1ZSI7czoyNjoid3BnbXBfZGlzcGxheV9wcmludF9vcHRpb24iO3M6NDoidHJ1ZSI7czoyMDoid3BnbXBfbGlzdGluZ19udW1iZXIiO3M6MjoiMTAiO3M6MjA6IndwZ21wX2JlZm9yZV9saXN0aW5nIjtzOjEzOiJNYXAgTG9jYXRpb25zIjtzOjE1OiJ3cGdtcF9saXN0X2dyaWQiO3M6MTg6IndwZ21wX2xpc3RpbmdfbGlzdCI7czoyNToid3BnbXBfY2F0ZWdvcnlkaXNwbGF5c29ydCI7czo1OiJ0aXRsZSI7czoyNzoid3BnbXBfY2F0ZWdvcnlkaXNwbGF5c29ydGJ5IjtzOjM6ImFzYyI7czoyMDoid3BnbXBfZGVmYXVsdF9yYWRpdXMiO3M6MzoiMTAwIjtzOjMwOiJ3cGdtcF9kZWZhdWx0X3JhZGl1c19kaW1lbnNpb24iO3M6NToibWlsZXMiO3M6OToiaXRlbV9za2luIjthOjM6e3M6NDoibmFtZSI7czo3OiJkZWZhdWx0IjtzOjQ6InR5cGUiO3M6NDoiaXRlbSI7czoxMDoic291cmNlY29kZSI7czo0NTY6IjxkaXYgY2xhc3M9IndwZ21wX2xvY2F0aW9ucyI+DQo8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbnNfaGVhZCI+DQo8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbl90aXRsZSI+DQo8YSBocmVmPSIiIGNsYXNzPSJwbGFjZV90aXRsZSIgZGF0YS16b29tPSJ7bWFya2VyX3pvb219IiBkYXRhLW1hcmtlcj0ie21hcmtlcl9pZH0iPnttYXJrZXJfdGl0bGV9PC9hPg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbl9tZXRhIj4NCjxzcGFuIGNsYXNzPSJ3cGdtcF9sb2NhdGlvbl9jYXRlZ29yeSBmYy1iYWRnZSBpbmZvIj57bWFya2VyX2NhdGVnb3J5fTwvc3Bhbj4NCjwvZGl2Pg0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbnNfY29udGVudCI+DQp7bWFya2VyX21lc3NhZ2V9DQo8L2Rpdj4NCjxkaXYgY2xhc3M9IndwZ21wX2xvY2F0aW9uc19mb290Ij48L2Rpdj4NCjwvZGl2PiI7fXM6MTY6ImZpbHRlcnNfcG9zaXRpb24iO3M6NzoiZGVmYXVsdCI7czoxMToiZ2VvanNvbl91cmwiO3M6MDoiIjtzOjE5OiJhcHBseV9jdXN0b21fZGVzaWduIjtzOjQ6InRydWUiO3M6MTY6IndwZ21wX2N1c3RvbV9jc3MiO3M6MDoiIjtzOjIwOiJ3cGdtcF9iYXNlX2ZvbnRfc2l6ZSI7czo0OiIxNnB4IjtzOjEyOiJjb2xvcl9zY2hlbWEiO3M6MTU6IiM5RTlFOUVfIzYxNjE2MSI7czoxOToid3BnbXBfcHJpbWFyeV9jb2xvciI7czoxOiIjIjtzOjIxOiJ3cGdtcF9zZWNvbmRhcnlfY29sb3IiO3M6MToiIyI7czoxNjoiZmNfY3VzdG9tX3N0eWxlcyI7czozMDUyOiJ7IjAiOnsiaW5mb3dpbmRvdy1kZWZhdWx0Ijp7ImZjLWl0ZW0tdGl0bGUiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6XCJPcGVuIFNhbnNcIiwgc2Fucy1zZXJpZjtmb250LXdlaWdodDo3MDA7Zm9udC1zaXplOjE2cHg7Y29sb3I6cmdiYSgwLCAwLCAwLCAwLjg3KTtsaW5lLWhlaWdodDoyMS40Mjg2cHg7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDAsIDAsIDAsIDApO2ZvbnQtc3R5bGU6bm9ybWFsO3RleHQtYWxpZ246c3RhcnQ7dGV4dC1kZWNvcmF0aW9uOm5vbmUgc29saWQgcmdiYSgwLCAwLCAwLCAwLjg3KTttYXJnaW4tdG9wOjBweDttYXJnaW4tYm90dG9tOjVweDttYXJnaW4tbGVmdDowcHg7bWFyZ2luLXJpZ2h0OjBweDtwYWRkaW5nLXRvcDowcHg7cGFkZGluZy1ib3R0b206MHB4O3BhZGRpbmctbGVmdDowcHg7cGFkZGluZy1yaWdodDowcHg7In19LCIxIjp7InBvc3QtZGVmYXVsdCI6eyJmYy1pdGVtLXRpdGxlIjoiYmFja2dyb3VuZC1pbWFnZTpub25lO2ZvbnQtZmFtaWx5OlwiT3BlbiBTYW5zXCIsIHNhbnMtc2VyaWY7Zm9udC13ZWlnaHQ6NjAwO2ZvbnQtc2l6ZToxOHB4O2NvbG9yOnJnYigzMywgNDcsIDYxKTtsaW5lLWhlaWdodDoyMS40Mjg2cHg7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDAsIDAsIDAsIDApO2ZvbnQtc3R5bGU6bm9ybWFsO3RleHQtYWxpZ246c3RhcnQ7dGV4dC1kZWNvcmF0aW9uOm5vbmUgc29saWQgcmdiKDMzLCA0NywgNjEpO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206NXB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX0sIjIiOnsiaXRlbS1kZWZhdWx0Ijp7IndwZ21wX2xvY2F0aW9ucyI6ImJhY2tncm91bmQtaW1hZ2U6bm9uZTtmb250LWZhbWlseTpcIk9wZW4gU2Fuc1wiLCBzYW5zLXNlcmlmO2ZvbnQtd2VpZ2h0OjQwMDtmb250LXNpemU6MTVweDtjb2xvcjpyZ2JhKDAsIDAsIDAsIDAuODcpO2xpbmUtaGVpZ2h0OjIxLjQyODZweDtiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMCwgMCwgMCwgMCk7Zm9udC1zdHlsZTpub3JtYWw7dGV4dC1hbGlnbjpzdGFydDt0ZXh0LWRlY29yYXRpb246bm9uZSBzb2xpZCByZ2JhKDAsIDAsIDAsIDAuODcpO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206MHB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX0sIjMiOnsiaXRlbS1kZWZhdWx0Ijp7IndwZ21wX2xvY2F0aW9uc19oZWFkIjoiYmFja2dyb3VuZC1pbWFnZTpub25lO2ZvbnQtZmFtaWx5OlwiT3BlbiBTYW5zXCIsIHNhbnMtc2VyaWY7Zm9udC13ZWlnaHQ6NDAwO2ZvbnQtc2l6ZToxNXB4O2NvbG9yOnJnYmEoMCwgMCwgMCwgMC44Nyk7bGluZS1oZWlnaHQ6MjEuNDI4NnB4O2JhY2tncm91bmQtY29sb3I6cmdiYSgwLCAwLCAwLCAwKTtmb250LXN0eWxlOm5vcm1hbDt0ZXh0LWFsaWduOnN0YXJ0O3RleHQtZGVjb3JhdGlvbjpub25lIHNvbGlkIHJnYmEoMCwgMCwgMCwgMC44Nyk7bWFyZ2luLXRvcDowcHg7bWFyZ2luLWJvdHRvbTowcHg7bWFyZ2luLWxlZnQ6MHB4O21hcmdpbi1yaWdodDowcHg7cGFkZGluZy10b3A6MHB4O3BhZGRpbmctYm90dG9tOjBweDtwYWRkaW5nLWxlZnQ6MHB4O3BhZGRpbmctcmlnaHQ6MHB4OyJ9fSwiNCI6eyJpdGVtLWRlZmF1bHQiOnsicGxhY2VfdGl0bGUiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6XCJPcGVuIFNhbnNcIiwgc2Fucy1zZXJpZjtmb250LXdlaWdodDo0MDA7Zm9udC1zaXplOjE1cHg7Y29sb3I6cmdiKDAsIDExNSwgMTcwKTtsaW5lLWhlaWdodDoyMS40Mjg2cHg7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDAsIDAsIDAsIDApO2ZvbnQtc3R5bGU6bm9ybWFsO3RleHQtYWxpZ246c3RhcnQ7dGV4dC1kZWNvcmF0aW9uOnVuZGVybGluZSBzb2xpZCByZ2IoMCwgMTE1LCAxNzApO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206MHB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX0sIjUiOnsiaXRlbS1kZWZhdWx0Ijp7IndwZ21wX2xvY2F0aW9uX21ldGEiOiJiYWNrZ3JvdW5kLWltYWdlOm5vbmU7Zm9udC1mYW1pbHk6XCJPcGVuIFNhbnNcIiwgc2Fucy1zZXJpZjtmb250LXdlaWdodDo0MDA7Zm9udC1zaXplOjE1cHg7Y29sb3I6cmdiYSgwLCAwLCAwLCAwLjg3KTtsaW5lLWhlaWdodDoyMS40Mjg2cHg7YmFja2dyb3VuZC1jb2xvcjpyZ2JhKDAsIDAsIDAsIDApO2ZvbnQtc3R5bGU6bm9ybWFsO3RleHQtYWxpZ246c3RhcnQ7dGV4dC1kZWNvcmF0aW9uOm5vbmUgc29saWQgcmdiYSgwLCAwLCAwLCAwLjg3KTttYXJnaW4tdG9wOjBweDttYXJnaW4tYm90dG9tOjBweDttYXJnaW4tbGVmdDowcHg7bWFyZ2luLXJpZ2h0OjBweDtwYWRkaW5nLXRvcDowcHg7cGFkZGluZy1ib3R0b206MHB4O3BhZGRpbmctbGVmdDowcHg7cGFkZGluZy1yaWdodDowcHg7In19LCI2Ijp7Iml0ZW0tZGVmYXVsdCI6eyJ3cGdtcF9sb2NhdGlvbnNfY29udGVudCI6ImJhY2tncm91bmQtaW1hZ2U6bm9uZTtmb250LWZhbWlseTpcIk9wZW4gU2Fuc1wiLCBzYW5zLXNlcmlmO2ZvbnQtd2VpZ2h0OjQwMDtmb250LXNpemU6MTVweDtjb2xvcjpyZ2JhKDAsIDAsIDAsIDAuODcpO2xpbmUtaGVpZ2h0OjIxLjQyODZweDtiYWNrZ3JvdW5kLWNvbG9yOnJnYmEoMCwgMCwgMCwgMCk7Zm9udC1zdHlsZTpub3JtYWw7dGV4dC1hbGlnbjpzdGFydDt0ZXh0LWRlY29yYXRpb246bm9uZSBzb2xpZCByZ2JhKDAsIDAsIDAsIDAuODcpO21hcmdpbi10b3A6MHB4O21hcmdpbi1ib3R0b206MHB4O21hcmdpbi1sZWZ0OjBweDttYXJnaW4tcmlnaHQ6MHB4O3BhZGRpbmctdG9wOjBweDtwYWRkaW5nLWJvdHRvbTowcHg7cGFkZGluZy1sZWZ0OjBweDtwYWRkaW5nLXJpZ2h0OjBweDsifX19IjtzOjE4OiJpbmZvd2luZG93X3NldHRpbmciO3M6MjUzOiI8ZGl2IGNsYXNzPSJmYy1tYWluIj48ZGl2IGNsYXNzPSJmYy1pdGVtLXRpdGxlIj57bWFya2VyX3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e21hcmtlcl9jYXRlZ29yeX08L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPnttYXJrZXJfaW1hZ2V9IDwvZGl2PnttYXJrZXJfbWVzc2FnZX08YWRkcmVzcz48Yj5BZGRyZXNzIDogPC9iPnttYXJrZXJfYWRkcmVzc308L2FkZHJlc3M+PC9kaXY+IjtzOjI2OiJpbmZvd2luZG93X2dlb3RhZ3Nfc2V0dGluZyI7czozNTA6IjxkaXYgY2xhc3M9ImZjLW1haW4iPjxkaXYgY2xhc3M9ImZjLWl0ZW0tdGl0bGUiPntwb3N0X3RpdGxlfSA8c3BhbiBjbGFzcz0iZmMtYmFkZ2UgaW5mbyI+e3Bvc3RfY2F0ZWdvcmllc308L3NwYW4+PC9kaXY+IDxkaXYgY2xhc3M9ImZjLWl0ZW0tZmVhdHVyZWRfaW1hZ2UiPntwb3N0X2ZlYXR1cmVkX2ltYWdlfSA8L2Rpdj57cG9zdF9leGNlcnB0fTxhZGRyZXNzPjxiPkFkZHJlc3MgOiA8L2I+e21hcmtlcl9hZGRyZXNzfTwvYWRkcmVzcz48YSB0YXJnZXQ9Il9ibGFuayIgIGNsYXNzPSJmYy1idG4gZmMtYnRuLXNtYWxsIGZjLWJ0bi1yZWQiIGhyZWY9Intwb3N0X2xpbmt9Ij5SZWFkIE1vcmUuLi48L2E+PC9kaXY+IjtzOjI3OiJ3cGdtcF9jYXRlZ29yeWRpc3BsYXlmb3JtYXQiO3M6NDU2OiI8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbnMiPg0KPGRpdiBjbGFzcz0id3BnbXBfbG9jYXRpb25zX2hlYWQiPg0KPGRpdiBjbGFzcz0id3BnbXBfbG9jYXRpb25fdGl0bGUiPg0KPGEgaHJlZj0iIiBjbGFzcz0icGxhY2VfdGl0bGUiIGRhdGEtem9vbT0ie21hcmtlcl96b29tfSIgZGF0YS1tYXJrZXI9InttYXJrZXJfaWR9Ij57bWFya2VyX3RpdGxlfTwvYT4NCjwvZGl2Pg0KPGRpdiBjbGFzcz0id3BnbXBfbG9jYXRpb25fbWV0YSI+DQo8c3BhbiBjbGFzcz0id3BnbXBfbG9jYXRpb25fY2F0ZWdvcnkgZmMtYmFkZ2UgaW5mbyI+e21hcmtlcl9jYXRlZ29yeX08L3NwYW4+DQo8L2Rpdj4NCjwvZGl2Pg0KPGRpdiBjbGFzcz0id3BnbXBfbG9jYXRpb25zX2NvbnRlbnQiPg0Ke21hcmtlcl9tZXNzYWdlfQ0KPC9kaXY+DQo8ZGl2IGNsYXNzPSJ3cGdtcF9sb2NhdGlvbnNfZm9vdCI+PC9kaXY+DQo8L2Rpdj4iO31zOjIzOiJtYXBfaW5mb193aW5kb3dfc2V0dGluZyI7TjtzOjE2OiJzdHlsZV9nb29nbGVfbWFwIjthOjQ6e3M6MTQ6Im1hcGZlYXR1cmV0eXBlIjthOjEwOntpOjA7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6MTtzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aToyO3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjM7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6NDtzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aTo1O3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjY7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO2k6NztzOjIwOiJTZWxlY3QgRmVhdHVyZWQgVHlwZSI7aTo4O3M6MjA6IlNlbGVjdCBGZWF0dXJlZCBUeXBlIjtpOjk7czoyMDoiU2VsZWN0IEZlYXR1cmVkIFR5cGUiO31zOjE0OiJtYXBlbGVtZW50dHlwZSI7YToxMDp7aTowO3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6MTtzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjI7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTozO3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6NDtzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjU7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTo2O3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO2k6NztzOjE5OiJTZWxlY3QgRWxlbWVudCBUeXBlIjtpOjg7czoxOToiU2VsZWN0IEVsZW1lbnQgVHlwZSI7aTo5O3M6MTk6IlNlbGVjdCBFbGVtZW50IFR5cGUiO31zOjU6ImNvbG9yIjthOjEwOntpOjA7czoxOiIjIjtpOjE7czoxOiIjIjtpOjI7czoxOiIjIjtpOjM7czoxOiIjIjtpOjQ7czoxOiIjIjtpOjU7czoxOiIjIjtpOjY7czoxOiIjIjtpOjc7czoxOiIjIjtpOjg7czoxOiIjIjtpOjk7czoxOiIjIjt9czoxMDoidmlzaWJpbGl0eSI7YToxMDp7aTowO3M6Mjoib24iO2k6MTtzOjI6Im9uIjtpOjI7czoyOiJvbiI7aTozO3M6Mjoib24iO2k6NDtzOjI6Im9uIjtpOjU7czoyOiJvbiI7aTo2O3M6Mjoib24iO2k6NztzOjI6Im9uIjtpOjg7czoyOiJvbiI7aTo5O3M6Mjoib24iO319czoxMzoibWFwX2xvY2F0aW9ucyI7YTo1OntpOjA7czoyOiI3NSI7aToxO3M6MjoiNzQiO2k6MjtzOjI6IjczIjtpOjM7czoyOiI3MiI7aTo0O3M6MjoiNzEiO31zOjE3OiJtYXBfbGF5ZXJfc2V0dGluZyI7YTo0OntzOjk6Im1hcF9saW5rcyI7czowOiIiO3M6MTM6ImZ1c2lvbl9zZWxlY3QiO3M6MDoiIjtzOjExOiJmdXNpb25fZnJvbSI7czowOiIiO3M6MTY6ImZ1c2lvbl9pY29uX25hbWUiO3M6MDoiIjt9czoxOToibWFwX3BvbHlnb25fc2V0dGluZyI7TjtzOjIwOiJtYXBfcG9seWxpbmVfc2V0dGluZyI7YjowO3M6MTk6Im1hcF9jbHVzdGVyX3NldHRpbmciO2E6NTp7czo0OiJncmlkIjtzOjI6IjE1IjtzOjg6Im1heF96b29tIjtzOjE6IjEiO3M6MTM6ImxvY2F0aW9uX3pvb20iO3M6MjoiMTAiO3M6NDoiaWNvbiI7czo1OiI0LnBuZyI7czoxMDoiaG92ZXJfaWNvbiI7czo1OiI0LnBuZyI7fXM6MTk6Im1hcF9vdmVybGF5X3NldHRpbmciO2E6Njp7czoyMDoib3ZlcmxheV9ib3JkZXJfY29sb3IiO3M6MToiIyI7czoxMzoib3ZlcmxheV93aWR0aCI7czozOiIyMDAiO3M6MTQ6Im92ZXJsYXlfaGVpZ2h0IjtzOjM6IjIwMCI7czoxNjoib3ZlcmxheV9mb250c2l6ZSI7czoyOiIxNiI7czoyMDoib3ZlcmxheV9ib3JkZXJfd2lkdGgiO3M6MToiMiI7czoyMDoib3ZlcmxheV9ib3JkZXJfc3R5bGUiO3M6NjoiZG90dGVkIjt9czoxMToibWFwX2dlb3RhZ3MiO2E6MTp7czo0OiJwb3N0IjthOjQ6e3M6NzoiYWRkcmVzcyI7czowOiIiO3M6ODoibGF0aXR1ZGUiO3M6MDoiIjtzOjk6ImxvbmdpdHVkZSI7czowOiIiO3M6ODoiY2F0ZWdvcnkiO3M6MDoiIjt9fXM6MjI6Im1hcF9pbmZvd2luZG93X3NldHRpbmciO047fQ==',
					);

					foreach ( $sample_data['maps'] as $title => $export_code ) {

						$import_code = wp_unslash( $export_code );
						if ( trim( $import_code ) != '' ) {
							$map_settings = maybe_unserialize( base64_decode( $import_code ) );

							if ( is_object( $map_settings ) ) {
								$sdata                  = array();
								$data                   = (array) $map_settings;
								$sdata['map_locations'] = serialize( wp_unslash( $location_ids ) );
								if ( isset( $data['extensions_fields'] ) ) {
									$sdata['map_all_control']['extensions_fields'] = $data['extensions_fields'];
								}

								if ( isset( $data['map_all_control']['map_control_settings'] ) ) {
									$arr = array();
									$i   = 0;
									foreach ( $data['map_all_control']['map_control_settings'] as $key => $val ) {
										if ( $val['html'] != '' ) {
											$arr[ $i ]['html']     = $val['html'];
											$arr[ $i ]['position'] = $val['position'];
											$i++;
										}
									}
									$sdata['map_all_control']['map_control_settings'] = $arr;
								}

								if ( isset( $data['map_all_control']['custom_filters'] ) ) {
									$custom_filters = array();
									foreach ( $data['map_all_control']['custom_filters'] as $k => $val ) {
										if ( $val['slug'] == '' ) {
											unset( $data['map_all_control']['custom_filters'][ $k ] );
										} else {
											$custom_filters[] = $val;
										}
									}
									$sdata['map_all_control']['custom_filters'] = $custom_filters;
								}

								if ( isset( $data['map_all_control']['location_infowindow_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['infowindow_setting'] = $data['map_all_control']['location_infowindow_skin']['sourcecode'];
								}

								if ( isset( $data['map_all_control']['post_infowindow_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['infowindow_geotags_setting'] = $data['map_all_control']['post_infowindow_skin']['sourcecode'];
								}

								if ( isset( $_POST['map_all_control']['item_skin']['sourcecode'] ) ) {
									$sdata['map_all_control']['wpgmp_categorydisplayformat'] = $data['map_all_control']['item_skin']['sourcecode'];
								}

								$sdata['map_title']                   = sanitize_text_field( wp_unslash( $data['map_title'] ) );
								$sdata['map_width']                   = str_replace( 'px', '', sanitize_text_field( wp_unslash( $data['map_width'] ) ) );
								$sdata['map_height']                  = str_replace( 'px', '', sanitize_text_field( wp_unslash( $data['map_height'] ) ) );
								$sdata['map_zoom_level']              = intval( wp_unslash( $data['map_zoom_level'] ) );
								$sdata['map_type']                    = sanitize_text_field( wp_unslash( $data['map_type'] ) );
								$sdata['map_scrolling_wheel']         = sanitize_text_field( wp_unslash( $data['map_scrolling_wheel'] ) );
								$sdata['map_45imagery']               = sanitize_text_field( wp_unslash( $data['map_45imagery'] ) );
								$sdata['map_street_view_setting']     = serialize( wp_unslash( $data['map_street_view_setting'] ) );
								$sdata['map_all_control']             = serialize( wp_unslash( $data['map_all_control'] ) );
								$sdata['map_info_window_setting']     = serialize( wp_unslash( $data['map_info_window_setting'] ) );
								$sdata['style_google_map']            = serialize( wp_unslash( $data['style_google_map'] ) );
								$sdata['map_layer_setting']           = serialize( wp_unslash( $data['map_layer_setting'] ) );
								$sdata['map_polygon_setting']         = serialize( wp_unslash( $data['map_polygon_setting'] ) );
								$sdata['map_cluster_setting']         = serialize( wp_unslash( $data['map_cluster_setting'] ) );
								$sdata['map_overlay_setting']         = serialize( wp_unslash( $data['map_overlay_setting'] ) );
								$sdata['map_infowindow_setting']      = serialize( wp_unslash( $data['map_infowindow_setting'] ) );
								$sdata['map_geotags']                 = serialize( wp_unslash( $data['map_geotags'] ) );
								$map_ids[]                            = FlipperCode_Database::insert_or_update( TBL_MAP, $sdata, $where = '' );
							}
						}
					}

					if ( $success == true ) {

						$response['success'] = esc_html__( 'Sample Data has been created successfully. Go to Manage Maps and use the map shortcode.', 'wpgmp-google-map' );

					} else {
						$response['error'] = esc_html__( 'Something went wrong. Please try again.', 'wpgmp-google-map' );
					}
				} else {
					
					$response['error'] = esc_html__( 'Please enter "YES" in the provided textbox and then submit the form to install sample data.', 'wpgmp-google-map' );
				}
				
				return $response;
			}
		}

	}
}
