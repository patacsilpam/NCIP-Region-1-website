<?php
/**
 * WP_List_Table_Helper Class File.
 * @package Core
 * @author Flipper Code <hello@flippercode.com>
 */

if ( ! class_exists( 'WP_List_Table_Helper' ) ) {

	/**
	 * Include the main wp-list-table file.
	 */

	if ( ! class_exists( 'WP_List_Table' ) ) 
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

	/**
	 * Extend WP_LIST_TABLE to simplify table listing.
	 * @package Core
	 * @author Flipper Code <hello@flippercode.com>
	 */
	class WP_List_Table_Helper extends WP_List_Table {

	/**
	 * Plugin name.
	 * @var string
	 */
		var $plugin;
		/**
	 * Table name.
	 * @var string
	 */
		var $table;
		/**
	 * Custom SQL Query to fetch records.
	 * @var string
	 */
		var $sql;
		/**
	 * Action over records.
	 * @var array
	 */
		var $actions = array( 'edit','delete' );
	   /**
	 * Text Domain for multilingual.
	 * @var string
	 */
		var $textdomain;
		/**
	 * Singular label.
	 * @var string
	 */
		var $singular_label;
		/**
	 * Plural lable.
	 * @var string
	 */
		var $plural_label;
		/**
	 * Show add navigation at the top.
	 * @var boolean
	 */
		var $show_add_button = true;
		/**
	 * Ajax based listing
	 * @var boolean
	 */
		var $ajax = false;
		/**
	 * Columns to be displayed.
	 * @var array
	 */
		var $columns;
		/**
	 * Columns to be sortable.
	 * @var array
	 */
		var $sortable;
		/**
	 * Fields to be hide.
	 * @var  array
	 */
		var $hidden;
		/**
	 * Records per page.
	 * @var integer
	 */
		var $per_page = 10;
		/**
	 * Slug for the manage page.
	 * @var string
	 */
		var $admin_listing_page_name;
		/**
	 * Slug for the add or edit page.
	 * @var string
	 */
		var $admin_add_page_name;
		/**
	 * Response
	 * @var string
	 */
		var $response;
		/**
	 * Display string at the top of the table.
	 * @var string
	 */
		var $toptext;
		/**
	 * Display string at the bottom of the table.
	 * @var [type]
	 */
		var $bottomtext;
		/**
	 * Primary column of the table.
	 * @var string
	 */
		var $primary_col;
		/**
	 * Column where to display actions navigation.
	 * @var string
	 */
		var $col_showing_links;
		/**
	 * Call external function when actions executed.
	 * @var array
	 */
		var $extra_processing_on_actions;
		/**
	 * Current action name.
	 * @var string
	 */
		var $now_action;
		/**
	 * Table prefix.
	 * @var string
	 */
		var $prefix;
		/**
	 * Current page's records.
	 * @var array
	 */
		var $found_data;
		/**
	 * Total # of records.
	 * @var int
	 */
		var $items;
		/**
	 * All Records.
	 * @var array
	 */
		var $data;
		/**
	 * Columns to be excluded in search.
	 * @var array
	 */
		var $searchExclude;
		/**
	 * Actions executed in bulk action.
	 * @var array
	 */
		var $bulk_actions;
	/* Show header.
	 * @var bool
	 */
		var $no_header = false;
		/**
	 * Array for holding translation strings.
	 * @var array
	 */
		var $translation = array();
		
		/**
	 * For setting ID of the form component.
	 * @var array
	 */
		var $form_id;
		
		/**
	 * For setting custon class on the form component.
	 * @var array
	 */
		var $form_class;
		
		/**
		 * Constructer method
		 * @param array $tableinfo Listing configurations.
		 */
		
		public function __construct($tableinfo) {

			global $wpdb;
			$this->prefix = $wpdb->prefix;
	
			foreach ( $tableinfo as $key => $value ) {    // Initialise constuctor based provided values to class variables.
				$this->$key = $tableinfo[ $key ];
			}

			parent::__construct(
				array(
					'singular' => $this->singular_label,
					'plural'   => $this->plural_label,
					'ajax'     => $this->ajax,
				)
			);

			$this->init_listing();

		}
		
		public function authenticate_action_requests(){
			
			//Authentication
			if( !is_user_logged_in() )
			wp_die( 'You are not allowed to save changes!' );	
			
			//Authorization			
			if ( ! current_user_can('administrator') )
			wp_die( 'You are not allowed to save changes!' );
			
			//Nonce Verification
			if(!isset( $_REQUEST['_wpnonce'] ) || empty($_REQUEST['_wpnonce']) )
			wp_die('You are not allowed to save changes!');
			if( !wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-'.$this->_args['plural'] ) )
			wp_die('You are not allowed to save changes!');
			
			
		}
		
		/**
		 * Initialize table listing.
		 */
		public function init_listing() {
				
			$this->prepare_items();

			if ( isset($_GET['doaction']) && !empty( $_GET['doaction'] ) && !empty( $_GET[ $this->primary_col ] ) ) {
				
				$this->authenticate_action_requests();
				
				$this->now_action = $function_name = sanitize_text_field( wp_unslash( $_GET['doaction'] ) );
				
				if ( false != strpos( sanitize_text_field( wp_unslash( $_GET['doaction'] ) ), '-' ) ) {
					$function_name = str_replace( '-','',$function_name );
				}
				$this->$function_name();

			} else {
				$this->listing();
			}

		}
		/**
		 * Edit action.
		 */
		public function edit() {}
		/**
		 * Delete action.
		 */
				 
		public function delete() {

			global $wpdb;
			$id = intval( wp_unslash( $_GET[ $this->primary_col ] ) );
			//Prepared Query For Safe Execution
			$wpdb->query( $wpdb->prepare( 'DELETE FROM '.$this->table.' WHERE '.$this->primary_col.' = %d', $id	) );
			$this->prepare_items();
			$this->response['success'] = $this->translation['delete_msg'];
			$this->listing();

		}
		/**
		 * Display records listing.
		 */
		public function listing() {

		?>

		<div class="flippercode-ui">
			<div class="fc-main">
				<div class="fc-container">
					<div class="fc-divider">
                    <div class="fc-back">
						<div class="fc-12">
							<div class="wpgmp_menu_title">
								<h4 class="fc-title-blue"><?php echo esc_html( $this->translation['manage_heading'] ); ?>
								<span><a class="fa fc-new-link" target="_blank" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $this->admin_add_page_name ) ); ?>"><?php echo esc_html( $this->translation['add_button'] ); ?></a>
								</span></h4>
							</div>
						<div class="wpgmp-overview">
							<?php $this->show_notification( $this->response ); ?>
									<fieldset>
									<?php
									
										$form_attr = '';
										if(!empty($this->form_id)){
											$form_attr .= "id = {$this->form_id}";
										}
										if(!empty($this->form_class)){
											$this->form_class = esc_attr($this->form_class);
											$form_attr .= " class  = 'wpgmp_manage_form {$this->form_class}'";
										}
										
									?>
									
									<form method="post" action="<?php echo esc_url ( admin_url( 'admin.php?page='.$this->admin_listing_page_name ) ); ?>" <?php echo esc_html($form_attr); ?> >
									<?php
									$this->search_box( 'search', 'search_id' );
									$this->display();
									?>
									<input type="hidden" name="row_id" value="" />
									<input type="hidden" name="operation" value="" />
								</form>
								</fieldset>
							</div>
							</br>
						</div>
					</div>
			</div></div>
		</div></div>
		
		<?php	}
		/**
		 * Reset primary column ID.
		 */
		public function unset_id_field() {

			if ( array_key_exists( $this->primary_col, $this->columns ) ) { unset( $this->columns[ $this->primary_col ] );	}
		}
		/**
		 * Get sortable columns.
		 * @return array Sortable columns names.
		 */
		function get_sortable_columns() {

			if ( empty( $this->sortable ) ) {

				$sortable_columns[ $this->primary_col ] = array( $this->primary_col,false );
			} else {

				foreach ( $this->sortable as $sortable ) {
					$sortable_columns[ $sortable ] = array( $sortable,false );
				}
			}
			return $sortable_columns;
		}
		/**
		 * Get columns to be displayed.
		 * @return array Columns names.
		 */
		function get_columns() {

			$columns = array( 'cb' => '<input type="checkbox" />' );

			if ( ! empty( $this->sql ) ) {
				global $wpdb;
				//$this->sql is already prepared query if provided
				$results = $wpdb->get_results( $this->sql );
				if(is_array($results) && !empty($results)) {
					foreach ( $results[0] as $column_name => $column_value ) {    // Get all columns by provided returned by sql query(Preparing Columns Array).
					if(array_key_exists($column_name, $this->columns)) {
						$this->columns[ $column_name ] = $this->columns[$column_name];
					} else {
						$this->columns[ $column_name ] = $column_name;
					}
				}
				}
			} else {
				if ( empty( $this->columns ) ) {
					global $wpdb;
					foreach ( $wpdb->get_col( 'DESC ' . $this->table, 0 ) as $column_name ) {  // Query all column name usind DESC (Preparing Columns Array).
						$this->columns[ $column_name ] = $column_name;
					}
				}
			}

			$this->unset_id_field(); // Preventing Id field to showup in Listing.

			// This is how we initialise all columns dynamically instead of statically (normally we write each column name here) in get_columns function definition :).
			foreach ( $this->columns as $dbcolname => $collabel ) {
				$columns[ $dbcolname ] = $collabel;
			}

			return $columns;
		}
		/**
		 * Column where to display actions.
		 * @param  array  $item        Record.
		 * @param  string $column_name Column name.
		 * @return string              Column output.
		 */
		function column_default( $item, $column_name ) {
			
			if ( $column_name == $this->col_showing_links ) {

				$actions = array();
				foreach ( $this->actions as $action ) {
					$action_slug = sanitize_title( $action );
					$action_label = ucwords( $action );
					if ( 'delete' == $action_slug ) {
					
					$actions[ $action_slug ] = sprintf( '<a href="" class="delete_fc_record" data-item-id="' . $item->{$this->primary_col} . '" data-page-slug="'.$this->admin_listing_page_name.'" data-record-type="'.$this->primary_col.'" data-action="delete" data-target="#delete_fc_record">' . $action_label . '<i class="fa fa-trash"></i></a>', $item->{$this->primary_col} );
					
					} 
					else if ( 'edit' == $action_slug ) {
						$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&'.$this->primary_col.'=%s">'.$action_label.'<i class="fa fa-edit"></i></a>',$this->admin_add_page_name,$action_slug,$item->{$this->primary_col} );
					} 
					else { 
						
						$nonce = wp_create_nonce( 'bulk-' . $this->_args['plural'] );
						$actions[ $action_slug ] = sprintf( '<a href="?page=%s&doaction=%s&'.$this->primary_col.'=%s&_wpnonce=%s">'.$action_label.'</a>',$this->admin_listing_page_name,$action_slug,$item->{$this->primary_col}, $nonce );
				   }
				}
				return sprintf( '%1$s %2$s', $item->{$this->col_showing_links}, $this->row_actions( $actions ) );

			} else {
				$return = $item->$column_name;
			}
			return $return;
		}

		/**
		 * Checkbox for each record.
		 * @param  array $item Record.
		 * @return string       Checkbox Element.
		 */
		function column_cb($item) {
			return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item->{$this->primary_col} ); }
		
		/**
		 * Get bulk actions.
		 * @return array Bulk action listing.
		 */
		function get_bulk_actions() {

			$actions = array( 'delete' => 'Delete' );
			$actions = array_merge( $actions, (array) $this->bulk_actions );
			return $actions;
		}
		/**
		 * Get records from ids.
		 * @return array Records ID.
		 */
		function get_user_selected_records() {
			
			$ids = isset( $_REQUEST['id'] ) ? wp_unslash( $_REQUEST['id'] ) : array();
			if ( is_array( $ids ) ) { $ids = implode( ',', $ids ); }
			if ( ! empty( $ids ) ) {
				return $ids; }
		}
		
		function no_items(){
			
			if(!empty($this->translation['no_records_found']))
			echo esc_html($this->translation['no_records_found']);
			else
			esc_html_e( 'No items found.' );
			
		}
		
		/**
		 * Process bulk actions.
		 */
		function process_bulk_action() {

			global $wpdb;
			$this->now_action = $this->current_action();
			$ids = $this->get_user_selected_records();
			
			if(!empty($this->current_action()))
			$this->authenticate_action_requests();
			
			if ( 'delete' === $this->current_action() && !empty( $ids ) ) {
				
				//Prepared Query For Safe Execution	
				$ids = explode(',',$ids);
				$deleteCount = count($ids);
				$stringPlaceholders = array_fill(0, $deleteCount, '%s');
				$placeholdersForIds = implode(', ', $stringPlaceholders);
				$query = "DELETE FROM {$this->table} WHERE {$this->primary_col} IN ($placeholdersForIds)";
				$del = $wpdb->query( $wpdb->prepare($query, $ids) );
				if( isset($this->translation['bulk_delete_msg']) && !empty($this->translation['bulk_delete_msg']) ){
				   $this->response['success'] = $this->translation['bulk_delete_msg'];
				}else{
				   $this->response['success'] = $this->translation['delete_msg'];	
				}

			} else if ( 'export_location_csv' === $this->current_action() || 'export_csv' === $this->current_action() && ! empty( $ids ) ) {
				
				ob_clean();
				
				global $wpdb;
				$ids = $this->get_user_selected_records();
				$ids = explode(',',$ids);
				$exportCount = count($ids);
				$stringPlaceholders = array_fill(0, $exportCount, '%s');
				$placeholdersForIds = implode(', ', $stringPlaceholders);
				$prepared_query = ( ! empty( $ids )) ? " WHERE {$this->primary_col} IN ($placeholdersForIds) " : '';
				
				$columns = array_keys( $this->columns );
				$columns = (count( $columns ) == 0) ? $columns[0] : implode( ',',$columns );
				if(empty( $this->sql )){
					
					//Sanitise the order and order by clause now using sanitize_sql_orderby
					$order_by_clause = sanitize_sql_orderby( $this->primary_col.' desc' );
					$query = "SELECT $columns FROM ".$this->table.$prepared_query." Order By {$order_by_clause}";
					$data = $wpdb->get_results( $wpdb->prepare($query, $ids), ARRAY_A );
				
				}else{
					
					$query =  $this->sql;
					$data = $wpdb->get_results( $query,ARRAY_A );
					
				}
				
				$tablerecords = array();
				if ( ! empty( $this->sql ) ) {
					$col_key_value = array();
					foreach ( $data[0] as $key => $val ) {  // Make csv's first row column heading according to columns selected in custom sql.
						$col_key_value[ $key ] = $key;
					}
					$tablerecords[] = $col_key_value;
				} else {
					$tablerecords[] = $this->columns;        // Make csv's first row column heading according automatic detected columns.
				}
				foreach ( $data as $entry ) {
					if ( array_key_exists( $this->primary_col,$entry ) ) {
						unset( $entry[ $this->primary_col ] ); }
					$tablerecords[] = $entry;

				}
								
				header( 'Content-Type: application/csv' );
				header( "Content-Disposition: attachment; filename=\"{$this->plural_label}-Records.csv\";" );
				header( 'Pragma: no-cache' );
				$fp = fopen( 'php://output', 'w' );
				foreach ( $tablerecords as $record ) {
					fputcsv( $fp, $record );
				}
				fclose( $fp );
				exit;

			}
		}
		/**
		 * Show notification message based on response.
		 * @param  array $response Response.
		 */
		public function show_notification($response) {

			if ( ! empty( $response['error'] ) ) {
				$this->show_message( $response['error'],true ); } else if ( ! empty( $response['success'] ) ) {
				$this->show_message( $response['success'] ); }

		}
		/**
		 * Message html element.
		 * @param  string  $message  Message.
		 * @param  boolean $errormsg Error or not.
		 * @return string           Message element.
		 */
		public function show_message($message, $errormsg = false) {

			if ( empty( $message ) )
				return;
				
			if ( $errormsg ) {
				echo "<div class='fc-msg fc-msg-info'>".esc_html($message)."</div>";
			} else { echo "<div class='fc-msg fc-success'>".esc_html($message)."</div>"; }

		}
		
		/**
		 * Prepare records before print.
		 */
		function prepare_items() {

			global $wpdb;
			$columns  = $this->get_columns();
			$hidden   = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array( $columns, $hidden, $sortable );
			$this->process_bulk_action();
						
			$query = (empty( $this->sql )) ? 'SELECT * FROM '.$this->table : $this->sql;
			
			if ( isset($_GET['page']) && !empty($_GET['page']) && $this->admin_listing_page_name == sanitize_key($_GET['page']) && isset($_REQUEST['s']) && !empty($_REQUEST['s']) ) {

				$_GET['page'] = sanitize_key( $_GET['page'] );
				$s = sanitize_text_field( $_REQUEST['s'] );
				$first_column = '';
				$remaining_columns = array();
				$prepare_query_with_placeholders = '';
				$prepare_args_values = array();
				foreach ( $this->columns as $column_name => $columnlabel ) {

					if ( "{$this->primary_col}" == $column_name ) {
						continue;
					} 
					else {
						
						if ( empty( $first_column ) ) {
							
							  $first_column = $column_name;
							  $prepare_args_values[] = $wpdb->esc_like($s);
							  $prepare_query_with_placeholders = " WHERE {$column_name} LIKE '%%%s%%'";
							  
						} else {
							
							$remaining_columns[] = $column_name; 

							if ( is_null($this->searchExclude ) ) {
								
								$prepare_args_values[] = $wpdb->esc_like($s);
								$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";
							}

							if ( is_array($this->searchExclude) && !empty($this->searchExclude) && !@in_array( $column_name,$this->searchExclude ) ) {
								
								$prepare_args_values[] = $wpdb->esc_like($s);
								$prepare_query_with_placeholders .= " or {$column_name} LIKE '%%%s%%'";
							}
								
						}
					}
				}
				
				
				//Sanitise the order and order by clause now using sanitize_sql_orderby
				$order_by_clause = sanitize_sql_orderby( $this->primary_col.' desc' );
				
				$final_prepared_query = $wpdb->prepare( 'SELECT * FROM '.$this->table. $prepare_query_with_placeholders. ' Order By '.$order_by_clause, $prepare_args_values );
				
				//Prepared Query For Safe Execution
				$this->data = $wpdb->get_results( $final_prepared_query );
				
			} else if ( isset($_GET['orderby']) && ! empty( $_GET['orderby'] ) && isset($_GET['order']) && ! empty( $_GET['order'] ) ) {
				
				$query_to_run = $query;
				
				//First sanitising the basic url parameter string & also cross check in whitelist array
				$orderby = ( !empty( $_GET['orderby'] ) && in_array( sanitize_key( $_GET['orderby'] ), $this->sortable ) ) ? sanitize_key( $_GET['orderby'] ) : $this->primary_col;
				$order   = ( !empty( $_GET['order'] ) ) ? sanitize_key( $_GET['order'] ) : 'asc';
				
				//Then sanitise the order and order by clause now using sanitize_sql_orderby
				$order_by_clause = sanitize_sql_orderby($orderby.' '.$order);
				$query_to_run .= " Order By {$order_by_clause}";
				$this->data = $wpdb->get_results( $query_to_run );
				
			} else {
				
				$query_to_run = $query;
							
				//Sanitise the order and order by clause now using sanitize_sql_orderby
				$order_by_clause = sanitize_sql_orderby( $this->primary_col.' desc' );
				$query_to_run .= " Order By {$order_by_clause}";
				$this->data = $wpdb->get_results( $query_to_run );
			}
			
			$current_page = $this->get_pagenum();
			$total_items = count( $this->data );
			if(is_array($this->data) && !empty($this->data)) {
				$this->found_data = @array_slice( $this->data,( ( $current_page -1 ) * $this->per_page ), $this->per_page );
			} else {
				$this->found_data = array();
			}
			$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $this->per_page,
			) );
			$this->items = $this->found_data;

		}
		
		/**
		 * Extra Filter selection box at the top of table.
		 * @param  string $which Top or Bottom.
		 */
		function extra_tablenav( $which ) {
			
			$text = ( 'top' == $which ) ? $this->toptext : $this->bottomtext;
			echo esc_html($text);
			
			if ( 'top' != $which ) { return; }
		}
		/**
		 * display Pagination
		 * @param  string $which Top or Bottom.
		 */
		protected function pagination( $which ) {

			if ( empty( $this->_pagination_args ) ) {
				return;
			}
			$total_items = $this->_pagination_args['total_items'];
			$total_pages = $this->_pagination_args['total_pages'];
			$infinite_scroll = false;
			if ( isset( $this->_pagination_args['infinite_scroll'] ) ) {
				$infinite_scroll = $this->_pagination_args['infinite_scroll'];
			}

			$output = '<span class="displaying-num">' . sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) ) . '</span>';

			$current = $this->get_pagenum();

			$current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

			$current_url = remove_query_arg( array( 'hotkeys_highlight_last', 'hotkeys_highlight_first' ), $current_url );

			$page_links = array();

			$disable_first = $disable_last = '';
			if ( 1 == $current ) {
				$disable_first = ' disabled';
			}
			if ( $current == $total_pages ) {
				$disable_last = ' disabled';
			}
			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'first-page' . $disable_first,
				esc_attr__( 'Go to the first page' ),
				esc_url( remove_query_arg( 'paged', $current_url ) ),
				'&laquo;'
			);

			if ( isset( $_REQUEST['s'] ) && !empty( $_REQUEST['s'] ) ) {
				
				$_REQUEST['s'] = sanitize_text_field($_REQUEST['s']);
				$current_url = add_query_arg( 's', $_REQUEST['s'], $current_url );
			}

			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'prev-page' . $disable_first,
				esc_attr__( 'Go to the previous page' ),
				esc_url( add_query_arg( 'paged', max( 1, $current -1 ), $current_url ) ),
				'&lsaquo;'
			);

			if ( 'bottom' == $which ) {
				$html_current_page = $current;
			} else {
				$html_current_page = sprintf( "%s<input class='current-page' id='current-page-selector' title='%s' type='text' name='paged' value='%s' size='%d' />",
					'<label for="current-page-selector" class="screen-reader-text">' . 'Select Page' . '</label>',
					esc_attr__( 'Current page' ),
					$current,
					strlen( $total_pages )
				);
			}
			$html_total_pages = sprintf( "<span class='total-pages'>%s</span>", number_format_i18n( $total_pages ) );
			$page_links[] = '<span class="paging-input">' . sprintf( _x( '%1$s of %2$s', 'paging' ), $html_current_page, $html_total_pages ) . '</span>';

			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'next-page' . $disable_last,
				esc_attr__( 'Go to the next page' ),
				esc_url( add_query_arg( 'paged', min( $total_pages, $current + 1 ), $current_url ) ),
				'&rsaquo;'
			);

			$page_links[] = sprintf( "<a class='%s' title='%s' href='%s'>%s</a>",
				'last-page' . $disable_last,
				esc_attr__( 'Go to the last page' ),
				esc_url( add_query_arg( 'paged', $total_pages, $current_url ) ),
				'&raquo;'
			);

			$pagination_links_class = 'pagination-links';
			if ( ! empty( $infinite_scroll ) ) {
				$pagination_links_class = ' hide-if-js';
			}
			$output .= "\n<span class='$pagination_links_class'>" . join( "\n", $page_links ) . '</span>';

			if ( $total_pages ) {
				$page_class = $total_pages < 2 ? ' one-page' : '';
			} else {
				$page_class = ' no-pages';
			}
			$this->_pagination = "<div class='tablenav-pages{$page_class}'>$output</div>";

			echo wp_kses_post($this->_pagination);
		}

	}
}