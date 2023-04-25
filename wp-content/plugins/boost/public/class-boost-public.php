<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Boost
 * @subpackage Boost/public
 * @author     cristian stoicescu <email@email.email>
 */
class Boost_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	private $notification_model;

	private $action_model;

	private $settings_model;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->notification_model = new Boost_Notification_Model( $plugin_name, $version );
		$this->action_model       = new Boost_Action_Model( $plugin_name, $version );
		$this->settings_model     = new Boost_Settings_Model( $plugin_name, $version );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Boost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/boost-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_fonts_public', plugin_dir_url( __FILE__ ) . '../includes/css/notifications-fonts.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name . '_notifications', plugin_dir_url( __FILE__ ) . '../includes/css/notifications-style.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Boost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '_public', plugin_dir_url( __FILE__ ) . 'js/boost-public.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name . '_public', 'BOOST_public', array(
				'settings'                      => $this->settings_model->get_settings( array(
					'boost-visibility-time',
					'approximate-time-between-boosts',
					'close-boosts'
				) ),
				'ajaxurl'                       => admin_url( 'admin-ajax.php' ),
				'notifications_part_load_nonce' => wp_create_nonce( 'boost_notifications_part_load_nonce' ),
			)
		);

		wp_enqueue_script( $this->plugin_name . '_public_ajax', plugin_dir_url( __FILE__ ) . 'js/boost-public-ajax.js', array( 'jquery' ), $this->version, false );

		$leads_triggers = $this->get_leads_triggers();

		wp_localize_script( $this->plugin_name . '_public_ajax', 'BOOST_public_Ajax', array(
				'ajaxurl'           => admin_url( 'admin-ajax.php' ),
				'submit_form_nonce' => wp_create_nonce( 'boost_submit_form_nonce' ),
				'leads_triggers'    => $leads_triggers
			)
		);
	}

	/**
	 * AJAX callback function.
	 */
	public function boost_submit_form_ajax_handler() {
		$nonce = empty( $_POST['nonce'] ) ? '' : stripslashes_deep($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'boost_submit_form_nonce' )
		     || ! isset( $_POST['boost_id'] ) || ! isset( $_POST['user_name'] ) ) {
			die( __( 'Error', 'boost' ) );
		}
		$boost_id      = $_POST['boost_id'];
		$user_name     = stripslashes_deep($_POST['user_name']);
		$actionModel   = new Boost_Action_Model( $this->get_plugin_name(), $this->get_version() );
		$new_action_id = $actionModel->create_action_entry( $boost_id, 'leads', $user_name );
		wp_die();
	}

	/**
	 * AJAX callback function.
	 */
	public function boost_notifications_part_load_ajax_handler() {
		$nonce = empty( $_POST['nonce'] ) ? '' : stripslashes_deep($_POST['nonce']);
		if ( ! wp_verify_nonce( $nonce, 'boost_notifications_part_load_nonce' )
		     || ! isset( $_POST['page'] ) || ! isset( $_POST['current_url'] ) ) {
			die( __( 'Error', 'boost' ) );
		}
		$page                    = $_POST['page'];
		$current_url             = stripslashes_deep($_POST['current_url']);
		$notificationModel       = new Boost_Notification_Model( $this->get_plugin_name(), $this->get_version() );
		$notifications_part_html = $notificationModel->get_notifications_html( $page, 25, $current_url );
		wp_send_json_success( array( 'notifications_part_html' => $notifications_part_html ) );
		wp_die();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_version() {
		return $this->version;
	}

	public function get_leads_triggers() {
		global $wpdb;

		$table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
		$table_boosts     = $wpdb->prefix . TABLE_BOOSTS;

		$sql = "SELECT `capture_url`, `form_selector`, `boost_id`, `form_username_field`, `form_surname_field` FROM $table_leads_data 
				LEFT JOIN $table_boosts ON ($table_boosts.`id` = $table_leads_data.`boost_id`)
				WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1';";

		if ( ( $leads_triggers = $wpdb->get_results( $sql, ARRAY_A ) ) == null ) {
			$leads_triggers = array();
		};

		return $leads_triggers;
	}

	public function action_boost_set_location_cookie() {
		$this->set_user_location_cookie();
	}

	public function set_user_location_cookie() {
		if ( empty( $_COOKIE['STYXKEY-BOOST_USER_LOCATION'] ) ) {
			$ip_address = $this->get_client_ip();
//        $ip_address = '2a02:2f01:6010:a94:e496:b8c8:9566:953c';
			$ipInfo = array();

			// *********** 1 **********
			if ( filter_var( $ip_address, FILTER_VALIDATE_IP ) ) {
				if ( is_callable( 'curl_init' ) ) {
					$curl_handle = curl_init();
					curl_setopt( $curl_handle, CURLOPT_URL, 'https://www.netip.de/search?query=' . $ip_address );
					curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
					curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
					curl_setopt( $curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' );
					$response = curl_exec( $curl_handle );
					curl_close( $curl_handle );
					if ( ! empty( $response ) ) {
						$patterns            = array();
						$patterns['domain']  = '#Domain: (.*?) #i';
						$patterns['country'] = '#Country: (.*?)&nbsp;#i';
						$patterns['state']   = '#State/Region: (.*?)<br#i';
						$patterns['town']    = '#City: (.*?)<br#i';
						foreach ( $patterns as $key => $pattern ) {
							$ipInfo[ $key ] = preg_match( $pattern, $response, $value ) && ! empty( $value[1] ) ? $value[1] : '';
						}
					}
				} else {
					$response = @file_get_contents( 'https://www.netip.de/search?query=' . $ip_address );
					if ( ! empty( $response ) ) {
						$patterns            = array();
						$patterns['domain']  = '#Domain: (.*?) #i';
						$patterns['country'] = '#Country: (.*?)&nbsp;#i';
						$patterns['state']   = '#State/Region: (.*?)<br#i';
						$patterns['town']    = '#City: (.*?)<br#i';
						foreach ( $patterns as $key => $pattern ) {
							$ipInfo[ $key ] = preg_match( $pattern, $response, $value ) && ! empty( $value[1] ) ? $value[1] : '';
						}
					}
				}


				if ( empty( $ipInfo['town'] ) || empty( $ipInfo['state'] ) || empty( $ipInfo['country'] ) ) {
					// *********** 2 **********
					if ( is_callable( 'curl_init' ) ) {
						$curl_handle = curl_init();
						curl_setopt( $curl_handle, CURLOPT_URL, 'https://freegeoip.net/json/' . $ip_address );
						curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
						curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
						curl_setopt( $curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' );
						$response = curl_exec( $curl_handle );
						curl_close( $curl_handle );
						if ( ! empty( $response ) ) {
							$location_data     = @json_decode( $response );
							$ipInfo['town']    = ! empty( $location_data->city ) ? $location_data->city : ( ! empty( $ipInfo['town'] ) ? $ipInfo['town'] : '' );
							$ipInfo['state']   = ! empty( $location_data->region_name ) ? $location_data->region_name : ( ! empty( $ipInfo['state'] ) ? $ipInfo['state'] : '' );
							$ipInfo['country'] = ! empty( $location_data->country_name ) ? $location_data->country_name : ( ! empty( $ipInfo['country'] ) ? $ipInfo['country'] : '' );
						}
					} else {
						$freegeoipAPI = 'https://freegeoip.net/json/' . $ip_address;
						$response     = @file_get_contents( $freegeoipAPI );
						if ( ! empty( $response ) ) {
							$location_data     = @json_decode( $response );
							$ipInfo['town']    = ! empty( $location_data->city ) ? $location_data->city : ( ! empty( $ipInfo['town'] ) ? $ipInfo['town'] : '' );
							$ipInfo['state']   = ! empty( $location_data->region_name ) ? $location_data->region_name : ( ! empty( $ipInfo['state'] ) ? $ipInfo['state'] : '' );
							$ipInfo['country'] = ! empty( $location_data->country_name ) ? $location_data->country_name : ( ! empty( $ipInfo['country'] ) ? $ipInfo['country'] : '' );
						}
					}
				}

				// *********** 3 **********
//        $location_data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip_address));
//        if (200 == $location_data['geoplugin_status']) {
//            $ipInfo['town'] = $location_data['geoplugin_city'];
//            $ipInfo['state'] = $location_data['geoplugin_regionName'];
//            $ipInfo['country'] = $location_data['geoplugin_countryName'];
//        }

				// *********** 4 **********
				if ( empty( $ipInfo['town'] ) || empty( $ipInfo['state'] ) || empty( $ipInfo['country'] ) ) {
					if ( is_callable( 'curl_init' ) ) {
						$curl_handle = curl_init();
						curl_setopt( $curl_handle, CURLOPT_URL, 'https://whatismyipaddress.com/ip/' . $ip_address );
						curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
						curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
						curl_setopt( $curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' );
						$response = curl_exec( $curl_handle );
						curl_close( $curl_handle );
						if ( ! empty( $response ) ) {
							$patterns            = array();
							$patterns['country'] = '#<th>Country:</th>[^<]*<td>([^<]*).*</td>#i';
							$patterns['state']   = '#<th>State/Region:</th>[^<]*<td>([^<]*)</td>#i';
							$patterns['town']    = '#<th>City:</th>[^<]*<td>([^<]*)</td>#i';
							foreach ( $patterns as $key => $pattern ) {
								$ipInfo[ $key ] = preg_match( $pattern, $response, $value ) && ! empty( $value[1] ) ? $value[1] : ( ! empty( $ipInfo[ $key ] ) ? $ipInfo[ $key ] : '' );
							}
						}
					} else {
						$response = @file_get_contents( 'https://whatismyipaddress.com/ip/' . $ip_address );
						if ( ! empty( $response ) ) {
							$patterns            = array();
							$patterns['country'] = '#<th>Country:</th>[^<]*<td>([^<]*).*</td>#i';
							$patterns['state']   = '#<th>State/Region:</th>[^<]*<td>([^<]*)</td>#i';
							$patterns['town']    = '#<th>City:</th>[^<]*<td>([^<]*)</td>#i';
							foreach ( $patterns as $key => $pattern ) {
								$ipInfo[ $key ] = preg_match( $pattern, $response, $value ) && ! empty( $value[1] ) ? $value[1] : ( ! empty( $ipInfo[ $key ] ) ? $ipInfo[ $key ] : '' );
							}
						}
					}
				}

				// *********** 5 **********
				if ( empty( $ipInfo['town'] ) || empty( $ipInfo['state'] ) || empty( $ipInfo['country'] ) ) {
					if ( is_callable( 'curl_init' ) ) {
						$curl_handle = curl_init();
						curl_setopt( $curl_handle, CURLOPT_URL, 'https://ipapi.co/' . $ip_address . '/json/' );
						curl_setopt( $curl_handle, CURLOPT_CONNECTTIMEOUT, 2 );
						curl_setopt( $curl_handle, CURLOPT_RETURNTRANSFER, 1 );
						curl_setopt( $curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' );
						$response = curl_exec( $curl_handle );
						curl_close( $curl_handle );
						if ( ! empty( $response ) ) {
							$location_data     = @json_decode( $response );
							$ipInfo['town']    = ! empty( $location_data->city ) ? $location_data->city : ( ! empty( $ipInfo['town'] ) ? $ipInfo['town'] : '' );
							$ipInfo['state']   = ! empty( $location_data->region ) ? $location_data->region : ( ! empty( $ipInfo['state'] ) ? $ipInfo['state'] : '' );
							$ipInfo['country'] = ! empty( $location_data->country_name ) ? $location_data->country_name : ( ! empty( $ipInfo['country'] ) ? $ipInfo['country'] : '' );
						}
					} else {
						$response = @file_get_contents( 'https://ipapi.co/' . $ip_address . '/json/' );
						if ( ! empty( $response ) ) {
							$location_data     = @json_decode( $response );
							$ipInfo['town']    = ! empty( $location_data->city ) ? $location_data->city : ( ! empty( $ipInfo['town'] ) ? $ipInfo['town'] : '' );
							$ipInfo['state']   = ! empty( $location_data->region ) ? $location_data->region : ( ! empty( $ipInfo['state'] ) ? $ipInfo['state'] : '' );
							$ipInfo['country'] = ! empty( $location_data->country_name ) ? $location_data->country_name : ( ! empty( $ipInfo['country'] ) ? $ipInfo['country'] : '' );
						}
					}
				}

				// *********** 6 **********
//            if (empty($ipInfo['town']) || empty($ipInfo['state']) || empty($ipInfo['country'])) {
//                if (is_callable('curl_init')) {
//                    $curl_handle = curl_init();
//                    curl_setopt($curl_handle, CURLOPT_URL, 'http://ip-api.com/json/' . $ip_address);
//                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
//                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
//                    $response = curl_exec($curl_handle);
//                    curl_close($curl_handle);
//                    if (!empty($response)) {
//	                    $location_data = json_decode($response);
//	                    $ipInfo['town'] = !empty($location_data->city) ? $location_data->city : (!empty($ipInfo['town']) ? $ipInfo['town'] : '');
//	                    $ipInfo['state'] = !empty($location_data->regionName) ? $location_data->regionName : (!empty($ipInfo['state']) ? $ipInfo['state'] : '');
//	                    $ipInfo['country'] = !empty($location_data->country) ? $location_data->country : (!empty($ipInfo['country']) ? $ipInfo['country'] : '');
//                    }
//                }
//            }
			}

			if ( empty( $ipInfo['town'] ) ) {
				$ipInfo['town'] = '';
			}
			if ( empty( $ipInfo['state'] ) ) {
				$ipInfo['state'] = '';
			}
			if ( empty( $ipInfo['country'] ) ) {
				$ipInfo['country'] = '';
			}

			setcookie( 'STYXKEY-BOOST_USER_LOCATION', serialize( $ipInfo ), 0, '/' );
			$_COOKIE['STYXKEY-BOOST_USER_LOCATION'] = serialize( $ipInfo );
		}
	}

	public function get_client_ip() {
		if ( getenv( 'HTTP_CLIENT_IP' ) ) {
			$ip_address = getenv( 'HTTP_CLIENT_IP' );
		} else if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$ip_address = getenv( 'HTTP_X_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_X_FORWARDED' ) ) {
			$ip_address = getenv( 'HTTP_X_FORWARDED' );
		} else if ( getenv( 'HTTP_FORWARDED_FOR' ) ) {
			$ip_address = getenv( 'HTTP_FORWARDED_FOR' );
		} else if ( getenv( 'HTTP_FORWARDED' ) ) {
			$ip_address = getenv( 'HTTP_FORWARDED' );
		} else if ( getenv( 'REMOTE_ADDR' ) ) {
			$ip_address = getenv( 'REMOTE_ADDR' );
		} else {
			$ip_address = 'UNKNOWN';
		}

		return $ip_address;
	}

	public function action_woocommerce_update_order( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! empty( $order ) && $order->get_status() != 'failed' ) {
			$this->add_woocommerce_transaction_leads( $order_id );
			if ( is_array( @$order_items = $order->get_items() ) ) {
				foreach ( $order_items as $item ) {
					$product_id = $item['product_id'];
					$this->add_woocommerce_specific_transaction_leads( $product_id, $order_id );
				}
			}
		}
	}

	public function action_woocommerce_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
		global $wpdb;

		$table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
		$table_boosts           = $wpdb->prefix . TABLE_BOOSTS;

		$sql = "SELECT $table_boosts.`id`, $table_boosts.`type` FROM $table_boosts
				LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'woocommerce' AND $table_woocommerce_data.`subtype` LIKE 'add_to_cart'";

		$boosts = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $boosts ) ) {
			foreach ( $boosts as $boost ) {
				$this->action_model->create_action_entry( $boost['id'], $boost['type'], '-', null, $product_id );
			}
		}
	}

	public function add_woocommerce_transaction_leads( $order_id ) {
		global $wpdb;

		$table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
		$table_boosts           = $wpdb->prefix . TABLE_BOOSTS;

		$sql = "SELECT $table_boosts.`id`, $table_boosts.`type` FROM $table_boosts
				LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'woocommerce' AND $table_woocommerce_data.`subtype` LIKE 'transaction'";

		$boosts = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $boosts ) ) {
			foreach ( $boosts as $boost ) {
				if ( ! $this->action_model->check_action_exists( array(
					'boost_id'   => array(
						'compare' => '=',
						'value'   => $boost['id']
					),
					'order_id'   => array(
						'compare' => '=',
						'value'   => $order_id
					),
					'product_id' => array(
						'compare' => '',
						'value'   => 'IS NULL'
					)
				) ) ) {
					$this->action_model->create_action_entry( $boost['id'], $boost['type'], '', $order_id, null );
				}
			}
		}
	}

	public function add_woocommerce_specific_transaction_leads( $product_id, $order_id ) {
		global $wpdb;

		$table_woocommerce_data       = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
		$table_woocommerce_products   = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
		$table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
		$table_boosts                 = $wpdb->prefix . TABLE_BOOSTS;

		if ( function_exists( 'wc_get_product' ) ) {
			$product = wc_get_product( $product_id );
			if ( ! empty( $product ) ) {
				$product_cats = $product->get_category_ids();

				$sql = "SELECT DISTINCT ($table_boosts.`id`), $table_boosts.`type` FROM $table_boosts
									LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`)
									LEFT JOIN $table_woocommerce_products ON ($table_woocommerce_products.`boost_id` = $table_boosts.`id`)
									LEFT JOIN $table_woocommerce_categories ON ($table_woocommerce_categories.`boost_id` = $table_boosts.`id`)
									WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'woocommerce' AND $table_woocommerce_data.`subtype` LIKE 'specific_transaction'
										AND ($table_woocommerce_products.`product_id` = '$product_id' OR $table_woocommerce_categories.`category_id` IN ('" . implode( "','", $product_cats ) . "'))";

				$boosts = $wpdb->get_results( $sql, ARRAY_A );
				if ( ! empty( $boosts ) ) {
					foreach ( $boosts as $boost ) {
						if ( ! $this->action_model->check_action_exists( array(
							'boost_id'   => array(
								'compare' => '=',
								'value'   => $boost['id']
							),
							'order_id'   => array(
								'compare' => '=',
								'value'   => $order_id
							),
							'product_id' => array(
								'compare' => '=',
								'value'   => $product_id
							)
						) ) ) {
							$this->action_model->create_action_entry( $boost['id'], $boost['type'], '', $order_id, $product_id );
						}
					}
				}
			}
		}
	}

	public function action_edd_complete_purchase( $payment_id ) {
		global $wpdb;

		$table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
		$table_boosts                    = $wpdb->prefix . TABLE_BOOSTS;

		$sql = "SELECT $table_boosts.`id`,$table_boosts.`type`  FROM $table_boosts
				LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'easydigitaldownloads' AND $table_easydigitaldownloads_data.`subtype` LIKE 'transaction'";

		$boosts = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $boosts ) ) {
			foreach ( $boosts as $boost ) {
				$this->action_model->create_action_entry( $boost['id'], $boost['type'], '', $payment_id, null );
			}
		}
	}

	public function action_edd_complete_download_purchase( $download_id, $payment_id, $download_type ) {
		global $wpdb;

		$table_easydigitaldownloads_data       = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
		$table_easydigitaldownloads_products   = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
		$table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
		$table_boosts                          = $wpdb->prefix . TABLE_BOOSTS;

		if ( ! is_array( $download_cats = wp_get_post_terms( $download_id, 'download_category', array( 'fields' => 'ids' ) ) ) ) {
			$download_cats = array();
		};

		$sql = "SELECT DISTINCT ($table_boosts.`id`), $table_boosts.`type` FROM $table_boosts
									LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_boosts.`id`)
									LEFT JOIN $table_easydigitaldownloads_products ON ($table_easydigitaldownloads_products.`boost_id` = $table_boosts.`id`)
									LEFT JOIN $table_easydigitaldownloads_categories ON ($table_easydigitaldownloads_categories.`boost_id` = $table_boosts.`id`)
									WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'easydigitaldownloads' AND $table_easydigitaldownloads_data.`subtype` LIKE 'specific_transaction'
										AND ($table_easydigitaldownloads_products.`product_id` = '$download_id' OR $table_easydigitaldownloads_categories.`category_id` IN ('" . implode( "','", $download_cats ) . "'))";

		$boosts = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $boosts ) ) {
			foreach ( $boosts as $boost ) {
				$this->action_model->create_action_entry( $boost['id'], $boost['type'], '', $payment_id, $download_id );
			}
		}
	}

	public function action_edd_post_add_to_cart( $download_id, $options, $items ) {
		global $wpdb;

		$table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
		$table_boosts                    = $wpdb->prefix . TABLE_BOOSTS;

		$sql = "SELECT $table_boosts.`id`, $table_boosts.`type` FROM $table_boosts
				LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND $table_boosts.`type` LIKE 'easydigitaldownloads' AND $table_easydigitaldownloads_data.`subtype` LIKE 'add_to_cart'";

		$boosts = $wpdb->get_results( $sql, ARRAY_A );
		if ( ! empty( $boosts ) ) {
			foreach ( $boosts as $boost ) {
				$this->action_model->create_action_entry( $boost['id'], $boost['type'], '-', null, $download_id );
			}
		}
	}

	public function action_boost_set_session_cookie() {

		if ( ! is_admin() && empty( $_COOKIE['STYXKEY-BOOST_SESSION'] ) ) {
			$sessionId = preg_replace( '/[^a-z]+/i', '', base_convert( (float) rand() / (float) getrandmax(), 10, 36 ) );
			setcookie( 'STYXKEY-BOOST_SESSION', $sessionId, 0, '/' );
			$_COOKIE['STYXKEY-BOOST_SESSION'] = $sessionId;
		}

	}

}