<?php
/**
 * Model base class
 * @author Flipper Code <hello@flippercode.com>
 * @version 3.0.0
 * @package Core
 */

if ( ! class_exists( 'FlipperCode_Model_Base' ) ) {

	/**
	 * Model base class
	 * @author Flipper Code <hello@flippercode.com>
	 * @version 3.0.0
	 * @package Core
	 */
	class FlipperCode_Model_Base {
		
		/**
		 * Errors container.
		 * @var array
		 */
		protected $errors = array();
		/**
		 * Success message container.
		 * @var array
		 */
		protected $success = array();
		/**
		 * Hold query to be executed.
		 * @var string
		 */
		private $query = '';
		/**
		 * Table name assoicated to the model class.
		 * @var string
		 */
		public $table = '';
		/**
		 * Unique field name of the model table.
		 * @var [type]
		 */
		public $unique;
		/**
		 * Navigations releated to the model.
		 * @var array
		 */
		public  $navigation = array( '' );
		/**
		 * Model class constructer.
		 */
		private function __construct() {

		}
		/**
		 * Assign value to property.
		 * @param string $property Property Name.
		 * @param string $value    Property Value.
		 */
		function set_val($property, $value) {

			if ( is_array( $value ) ) {
				$this->{$property} = $value; } elseif ($this->valid( $property,$value ))
			$this->{$property} = $value;
		}
		/**
		 * Validate property value before assign.
		 * @param  string $property Property Name.
		 * @param  string $value    Property Value.
		 * @return boolean          True or False.
		 */
		function valid($property, $value) {

			if ( property_exists( $this, $property ) ) {

				$validator = new FlipperCode_Validator();

				if ( isset( $this->validations[ $property ] ) ) {

					foreach ( $this->validations[ $property ] as $type => $message ) {

						$validator->add( $property,$value,$type,$message );
					}

					$errors = $validator->validate();

					if ( $errors ) {
						$this->errors[ $property ] = $errors[ $property ];
						return false;
					} else {
						return true;
					}
				} else {
					return true;
				}
			}
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
		 * Validate all property together.
		 * @param  array $data Property name and value pair.
		 * @return boolean       True or False.
		 */
		function verify($data = array()) {

			$errors = '';
			//Call extension validation.
			if( isset($data['fc_entity_type']) and $data['fc_entity_type']!='' ) {
				$this->validations = apply_filters($data['fc_entity_type'].'_validation',$this->validations,$data);
			}
			
			if ( isset( $this->validations ) ) {

				foreach ( $this->validations as $field => $checkup ) {
					$validator = new FlipperCode_Validator();
					$dimension = explode('::',$field);
					foreach ( $checkup as $property => $message ) {
						if( count($dimension) == 1 ) {
							$validator->add( $field,$data[ $dimension[0] ],$property,$message );
						} else if(  count($dimension) == 2  ) {
							$validator->add( $field,$data[ $dimension[0] ][ $dimension[1] ],$property,$message );
						}
					}
					$errors = $validator->validate();
					if ( $errors ) {
						$this->errors[ $field ] = $errors[ $field ];
					}
				}

			}

			if( isset($data['fc_entity_type']) and $data['fc_entity_type']!='' ) {
				$this->errors = apply_filters($data['fc_entity_type'].'_custom_validation',$this->errors,$data);
			}
			
			if( is_array( $this->errors ) and !empty( $this->errors ) ) {
				return false;
			}
			return true;

		}
		/**
		 * Retrive records from database based on conditional array.
		 * @param string  $table     Table name.
		 * @param array   $fcv_array Conditional Array.
		 * @param string  $sortBy    Sort by.
		 * @param boolean $ascending Order by.
		 * @param string  $limit     Limit.
		 */

		function get($table = '', $fcv_array = array(), $sortBy = '', $ascending = true, $limit = '') {

		    global $wpdb;
		    $connection = FlipperCode_Database::connect();

		    $table = esc_sql($table);

		    $sqlLimit = ('' != $limit ? "LIMIT %d" : '');
		    $this->query = "SELECT * FROM {$this->table} ";
		    $params = array();

		    if (count($fcv_array) > 0) {
		        $this->query .= ' WHERE ';

		        for ($i = 0, $c = count($fcv_array); $i < $c; $i++) {
		            if (count($fcv_array[$i]) == 1) {
		                $this->query .= ' ' . $fcv_array[$i][0] . ' ';
		                continue;
		            } else {
		                if ($i > 0 && count($fcv_array[$i - 1]) != 1) {
		                    $this->query .= ' AND ';
		                }
		                if (isset($this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes']) && 'NUMERIC' != $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0] && 'SET' != $this->pog_attribute_type[$fcv_array[$i][0]]['db_attributes'][0]) {
		                    if (1 == $GLOBALS['configuration']['db_encoding']) {
		                        $value = $this->is_column($fcv_array[$i][2]) ? 'BASE64_DECODE(' . $fcv_array[$i][2] . ')' : '%s';
		                        $this->query .= 'BASE64_DECODE(`' . $fcv_array[$i][0] . '`) ' . $fcv_array[$i][1] . ' ' . $value;
		                        if (!$this->is_column($fcv_array[$i][2])) $params[] = $fcv_array[$i][2];
		                    } else {
		                        $value = $this->is_column($fcv_array[$i][2]) ? $fcv_array[$i][2] : '%s';
		                        $this->query .= '`' . $fcv_array[$i][0] . '` ' . $fcv_array[$i][1] . ' ' . $value;
		                        if (!$this->is_column($fcv_array[$i][2])) $params[] = $this->escape($fcv_array[$i][2]);
		                    }
		                } else {
		                    $value = $this->is_column($fcv_array[$i][2]) ? $fcv_array[$i][2] : '%s';
		                    if ('in' == strtolower($fcv_array[$i][1])) {
		                        $values = explode(',', $fcv_array[$i][2]);
		                        $placeholders = implode(',', array_fill(0, count($values), '%s'));
		                        $params = array_merge($params, $values); // Assuming IN clause values are handled properly
		                        $value = "($placeholders)";
		                    } else {
		                        $params[] = $fcv_array[$i][2];
		                    }
		                    $this->query .= '`' . $fcv_array[$i][0] . '` ' . $fcv_array[$i][1] . ' ' . $value;
		                }
		            }
		        }
		    }

		    if (!empty($sortBy)) {
		        if (isset($this->pog_attribute_type[$sortBy]['db_attributes']) && 'NUMERIC' != $this->pog_attribute_type[$sortBy]['db_attributes'][0] && 'SET' != $this->pog_attribute_type[$sortBy]['db_attributes'][0]) {
		            if (1 == $GLOBALS['configuration']['db_encoding']) {
		                $sortBy = "BASE64_DECODE($sortBy) ";
		            } else {
		                $sortBy = "$sortBy ";
		            }
		        } else {
		            $sortBy = "$sortBy ";
		        }
		    } else {
		        $sortBy = $this->unique;
		    }

		    $sort_order = ($ascending) ? 'ASC' : 'DESC';
		    $order_by_clause = sanitize_sql_orderby($sortBy . ' ' . $sort_order);
		    $this->query .= ' ORDER BY ' . $order_by_clause . " $sqlLimit";

		    if ('' != $limit) {
		        $params[] = (int) $limit;
		    }

		    // Use $wpdb->prepare to safely prepare the query only if there are placeholders
		    if (!empty($params)) {
		        $prepared_query = $wpdb->prepare($this->query, $params);
		    } else {
		        $prepared_query = $this->query;
		    }
			
			$results = FlipperCode_Database::reader( $prepared_query, $connection );
			return $results;
		}

		/**
		 * Query to be executed.
		 * @param  string $query SQL Query.
		 * @return array        Records.
		 */
		function query($query) {

			$this->query = $query;
			$connection = FlipperCode_Database::connect();
			$thisObjectName = get_class( $this );
			$cursors = FlipperCode_Database::reader( $this->query, $connection );

			if ( ! empty( $cursors ) ) {

				foreach ( $cursors as $row ) {

					$obj = new $thisObjectName();
					$obj->fill( $row );
					$objects[] = $obj;
				}

				return $objects;
			}
		}
		/**
		 * Validate file extension.
		 * @param  string $file_name File Name.
		 * @return boolean      True or False.
		 */
		public function wpp_validate_extension($file_name) {

			$ext_array = array( '.csv', '.xml', '.json', '.xls' );
			$extension = strtolower( strrchr( $file_name,'.' ) );
			$ext_count = count( $ext_array );

			if ( ! $file_name ) {
				return false;
			} else {
				if ( ! $ext_array ) {
					return true;
				} else {
					foreach ( $ext_array as $value ) {
						$first_char = substr( $value,0,1 );
						if ( '.' <> $first_char ) {
							$extensions[] = '.'.strtolower( $value );
						} else {
							$extensions[] = strtolower( $value );
						}
					}

					foreach ( $extensions as $value ) {
						if ( $value == $extension ) {
							$valid_extension = 'TRUE';
						}
					}

					if ( $valid_extension ) {
						return true;
					} else {
						return false;
					}
				}
			}
		}
		/**
		 * Throw errors in try block.
		 * @throws Exception User custom Errors.
		 */
		protected function throw_errors() {

			if ( isset( $this->errors ) && is_array( $this->errors ) ) {

				$escaped_errors = array_map( 'esc_html', $this->errors );
				$final_error_message = implode( '<br>', $escaped_errors );
				throw new Exception( wp_kses_post($final_error_message) );

			}
		}

		/**
		 * This function will try to encode $text to base64, except when $text is a number. This allows us to Escape all data before they're inserted in the database, regardless of attribute type.
		 * @param string $text String.
		 * @return string encoded to base64.
		 */
		public function escape($text) {

			return @mysql_real_escape_string( $text );
		}
		/**
		 * Check if column name.
		 * @param  string $value Column name.
		 * @return boolean        True or False.
		 */
		public static function is_column($value) {

			if ( strlen( $value ) > 2 ) {
				if ( substr( $value, 0, 1 ) == '`' && substr( $value, strlen( $value ) - 1, 1 ) == '`' ) {
					return true;
				}
				return false;
			}

			return false;
		}
		/**
		 * Convert XML to array.
		 * @param  string $xml XML nodes.
		 * @return array      Array nodes.
		 */
		public function wpp_xml_2array($xml) {

			$arr = array();

			foreach ( $xml->children() as $r ) {
				$t = array();
				if ( count( $r->children() ) == 0 ) {
					$arr[ $r->getName() ] = strval( $r );
				} else {
					$arr[ $r->getName() ][] = $this->wpp_xml_2array( $r );
				}
			}

			return $arr;
		}

		/**
		 * Validate file extension.
		 * @param  string $file_name File Name.
		 * @return boolean      True or False.
		 */
		public function validate_extension($file_name) {

			$ext_array = array( '.csv' );
			$extension = strtolower( strrchr( $file_name,'.' ) );
			$ext_count = count( $ext_array );

			if ( ! $file_name ) {
				return false;
			} else {
				if ( ! $ext_array ) {
					return true;
				} else {
					foreach ( $ext_array as $value ) {
						$first_char = substr( $value,0,1 );
						if ( '.' <> $first_char ) {
							$extensions[] = '.'.strtolower( $value );
						} else {
							$extensions[] = strtolower( $value );
						}
					}

					foreach ( $extensions as $value ) {
						if ( $value == $extension ) {
							$valid_extension = 'TRUE';
						}
					}

					if ( $valid_extension ) {
						return true;
					} else {
						return false;
					}
				}
			}
		}

	}
}
