<?php
/**
 * PixelCostOfGoods - Core Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  PixelYourSite.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PixelCostOfGoodsCore' ) ) :

	class PixelCostOfGoodsCore {

		/** @var $registeredPlugins array Registered plugins (addons) */
		private $registeredPlugins = array();

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function __construct() {
			$license_status = get_option( 'pixel_cost__license_status' );

			require_once( 'reports/functions-woo-analytics.php' );

			if ( is_admin() ) {
				add_filter( 'mime_types', array( $this, 'wpse_mime_types' ) );
				add_action( 'admin_enqueue_scripts', array( $this,'admin_enqueue_scripts' ));

				add_action( 'wp_ajax_pixel_cog_toggle_license', array( $this, 'pixel_cog_toggle_license' ) );
				add_action( 'wp_ajax_nopriv_pixel_cog_toggle_license', array( $this, 'pixel_cog_toggle_license' ) );

				if ($license_status == 'valid' || $license_status == 'expired') {
					add_action( 'wp_ajax_pixel_cog_calculate_cost', array( $this, 'pixel_cog_calculate_cost' ) );
					add_action( 'wp_ajax_nopriv_pixel_cog_calculate_cost', array( $this, 'pixel_cog_calculate_cost' ) );
					add_action( 'wp_ajax_pixel_cog_cron_order_recalculate_action', array($this, 'pixel_cog_cron_order_recalculate_action' ) );
					add_action( 'wp_ajax_nopriv_pixel_cog_cron_order_recalculate_action', array($this, 'pixel_cog_cron_order_recalculate_action' ) );
					add_action( 'wp_ajax_pixel_cog_update_tax_settings', array($this, 'pixel_cog_update_tax_settings' ) );
					add_action( 'wp_ajax_nopriv_cog_update_tax_settings', array($this, 'pixel_cog_update_tax_settings' ) );
					// Items export
					add_action( 'wp_ajax_pixel_export_cost_product', array( $this, 'pixel_export_cost_product' ) );
					add_action( 'wp_ajax_nopriv_pixel_export_cost_product', array($this, 'pixel_export_cost_product') );
					add_action( 'wp_ajax_pixel_import_cost_product_plugins', array( $this, 'pixel_import_cost_product_plugins' ) );
					add_action( 'wp_ajax_nopriv_pixel_import_cost_product_plugins', array($this, 'pixel_import_cost_product_plugins') );
					add_action( 'wp_ajax_pixel_export_cat_cost_product', array($this,'pixel_export_cat_cost_product') );
					add_action( 'wp_ajax_nopriv_pixel_export_cat_cost_product', array($this,'pixel_export_cat_cost_product') );
					// Items import
					add_action( 'wp_ajax_pixel_import_cost_product', array( $this, 'pixel_import_cost_product' ) );
					add_action( 'wp_ajax_nopriv_pixel_import_cost_product', array($this,'pixel_import_cost_product') );

				}
				// Settings ajax
				require_once( 'settings/settings-ajax.php' );
				// Export report
				require_once( 'reports/class-pixel-export-cost-of-goods.php' );
				// Import report
				require_once( 'reports/class-pixel-import-cost-of-goods.php' );

				if ($license_status == 'valid' || $license_status == 'expired') {
					// Order recalculate
					add_action( 'wp_ajax_pixel_cog_order_recalculate_action', array( $this,'pixel_cog_order_recalculate_action' ));

					// Cost input on admin product page (simple product)
					add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_cost_to_product' ) );
					add_action( 'save_post_product', array( $this, 'save_cost_to_product' ), PHP_INT_MAX, 2 );

					// Cost input on admin product page (variable product)
					add_action( 'woocommerce_variation_options_pricing', array( $this, 'add_cost_to_product_variable' ), 10, 3 );
					add_action( 'woocommerce_save_product_variation', array( $this, 'save_cost_to_product_variable' ), PHP_INT_MAX, 2 );

					// Cost input on admin product category page (single product category)
					add_action( 'product_cat_edit_form_fields', array( $this, 'taxonomy_edit_custom_meta_field' ), 150, 2 );
					add_action( 'product_cat_add_form_fields', array( $this, 'taxonomy_add_custom_meta_field' ), PHP_INT_MAX, 2 );
					add_action( 'edited_product_cat', array( $this, 'taxonomy_save_custom_meta_field' ), 150, 2 );
					add_action( 'create_product_cat', array( $this, 'taxonomy_save_custom_meta_field' ), PHP_INT_MAX, 2 );

					// Order item costs on order edit page
					add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'add_cost_info_shop_order' ), PHP_INT_MAX, 3 );
					// Admin columns
                    require_once( 'classes/class-pixel-cost-of-goods-admin-columns.php' );
				}

			}
			if ($license_status == 'valid' || $license_status == 'expired') {
				// Calculate orders cost and profit for new order
				add_action( 'woocommerce_checkout_create_order', array( $this, 'add_cost_to_new_order' ), PHP_INT_MAX, 2 );
			}

		}

		function wpse_mime_types( $existing_mimes ) {
			// Add csv to the list of allowed mime types
			$existing_mimes['csv'] = 'text/csv';
			return $existing_mimes;
		}

		/**
		 * Replaces query version in registered scripts or styles with file modified time
		 *
		 * @param $src
		 *
		 * @return string
		 */
		function add_modified_time( $src ) {

			$clean_src = remove_query_arg( 'ver', $src );
			$path      = wp_parse_url( $src, PHP_URL_PATH );

			if ( $modified_time = @filemtime( untrailingslashit( ABSPATH ) . $path ) ) {
				$src = add_query_arg( 'ver', $modified_time, $clean_src );
			} else {
				$src = add_query_arg( 'ver', time(), $clean_src );
			}

			return $src;

		}

		/**
		 * admin_enqueue_scripts.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function admin_enqueue_scripts(){
			wp_enqueue_style( 'pixel_cog_admin_css', plugins_url('../assets/css/pixel-cog-main.css', __FILE__ ), false, '' );
			wp_enqueue_script( 'pixel-cog-csv-script', plugins_url( '../assets/js/PapaParse.min.js', __FILE__ ), array('jquery') );
			wp_enqueue_script( 'pixel-cog-ajax-script', plugins_url( '../assets/js/pixel_cog.js', __FILE__ ), array('jquery'));
			wp_localize_script( "pixel-cog-ajax-script",
				'PIXELCOG',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( "pixel_cog" ),
				)
			);
			// woo analytics table
            wp_register_script(
                "cog-analytics-report",
                PIXEL_COG_ASSETS . "/js/woo_report.js",
                array(
                    "wp-hooks",
                    "wp-element",
                    "wp-i18n",
                    "wc-components",
                ),
                "1.0",
                true
            );
            wp_enqueue_script( "cog-analytics-report" );

			//add_filter( 'style_loader_src', array( $this, 'add_modified_time' ), 99999999, 1 );
			//add_filter( 'script_loader_src', array( $this, 'add_modified_time' ), 99999999, 1 );
		}

		/**
		 * Toggle pixel_cost_of_goods_enable on or off via AJAX.
		 */
		function pixel_cog_toggle_enabled() {
			$settings = new PixelSettingsAjaxCostOfGoods();
			$settings->pixel_cog_toggle_enabled();
		}

		/**
		 * Toggle pixel_cog_toggle_license on or off via AJAX.
		 */
		function pixel_cog_toggle_license() {
			$settings = new PixelSettingsAjaxCostOfGoods();
			$settings->pixel_cog_toggle_license();
		}

		/**
		 * Calculate orders cost via AJAX.
		 */
		function pixel_cog_calculate_cost() {
			$settings = new PixelSettingsAjaxCostOfGoods();
			$settings->pixel_cog_calculate_cost();
		}

		/**
		 * pixel_export_cost_product.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_export_cost_product() {
			$report = new PixelExportCostOfGoods();
			$report->pixel_export_cost_product();
		}

		/**
		 * pixel_import_cost_product_plugins.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_import_cost_product_plugins() {
			$report = new PixelImportCostOfGoods();
			$report->pixel_import_cost_product_plugins();
		}

		/**
		 * pixel_import_cost_product.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_import_cost_product() {
			$report = new PixelImportCostOfGoods();
			$report->pixel_import_cost_product();
		}

		/**
		 * pixel_export_cat_cost_product.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_export_cat_cost_product() {
			$report = new PixelExportCostOfGoods();
			$report->pixel_export_cat_cost_product();
		}

		/**
		 * pixel_cog_order_recalculate_action.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_cog_order_recalculate_action(){
			if(!empty($_POST['order_id'])){
				$this->pixel_cog_order_update($_POST['order_id'], 'false');
			}
			//Don't forget to always exit in the ajax function.
			exit();
		}

		/**
		 * pixel_cog_cron_order_recalculate_action.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_cog_cron_order_recalculate_action(){
			if( !wp_next_scheduled( 'pixel_cog_calculate_cron' ) ) {
				wp_schedule_single_event( time(), 'pixel_cog_calculate_cron' );
			}
			$this->pixel_cog_cron_order_update();
			//Don't forget to always exit in the ajax function.
			exit();
		}

		/**
		 * pixel_cog_update_tax_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_cog_update_tax_settings(){
			update_option('_pixel_cog_tax_calculating', $_POST['tax']);
			//Don't forget to always exit in the ajax function.
			exit();
		}

		/**
		 * add_cost_info_shop_order.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_cost_info_shop_order($order) {
			$cron = false;
			if( wp_next_scheduled( 'pixel_cog_calculate_cron' ) ) {
				$cron = 'disabled';
			}
			$order_id = $order->save();
			$cost = get_post_meta( $order_id, '_pixel_cost_of_goods_order_cost', true );
			$html = '<h3>Order Cost</h3>
						<span>'.wc_price($cost).'</span>
						<h3>Order Profit</h3>';
			($cost > 0) ? $profit = wc_price(get_post_meta( $order_id, '_pixel_cost_of_goods_order_profit', true )) : $profit = 'No Cost of Goods is configured for this order.';

			$html .= '<span>'.$profit.'</span>
						<p><button type="button" data-id="'.$order_id.'" class="button action recalculate_button" '.$cron.'>' . __( 'Recalculate', 'woocommerce' ) . '</button></p>
					';
			echo $html;
		}

		/**
		 * add_cost_to_new_order.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_cost_to_new_order( $order, $data ) {
			$order_id = $order->save();
			$this->pixel_cog_order_update($order_id, 'true');
		}

		/**
		 * pixel_cog_cron_order_update.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_cog_cron_order_update() {
			global $wpdb;

			$offset  = isset($_POST['offset']) ? $_POST['offset'] : 0;
			$limit   = isset($_POST['limit']) ? $_POST['limit'] : 'undefined';

			$total_orders_number = $wpdb->get_var("
        SELECT count(ID)  FROM {$wpdb->prefix}posts WHERE `post_type` LIKE 'shop_order'
    ");

			if ( $limit == 'undefined' ) {
				$limit = 100;
			}

			$args = array(
				'limit' => $limit,
				'offset' => $offset,
				'return' => 'ids',
			);
			$orders = wc_get_orders( $args );
			foreach ($orders as $key => $order_id) {
				$this->pixel_cog_order_update($order_id, 'false');
			}

			$new_offset = $offset + $limit;

			if (($total_orders_number - $new_offset) < $limit){
				$limit = $total_orders_number - $new_offset;
			}

			$data_result=array(
				"limit"=> "$limit",
				"offset" => "$new_offset",
				"loop" => ($new_offset < $total_orders_number) ? "1" : "0",
				"total_orders_number" => $total_orders_number
			);

			if ($new_offset >= $total_orders_number) {
				wp_unschedule_hook( 'pixel_cog_calculate_cron' );

				$db_update_status = get_option( 'pixel_cog_db_update_status' );

				if ($db_update_status != 'complete') {
					update_option( 'pixel_cog_db_update_status', 'complete' );
				}
			}

			wp_send_json( $data_result );
		}

		/**
		 * pixel_cog_order_update.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function pixel_cog_order_update($order_id, $new_order) {
			$order = wc_get_order( $order_id );
			$orderData = $order->get_data();
			$order_shipping_total = $orderData['shipping_total'];
			$order_total = $order->get_total();
			$currency_code = $order->get_currency();
			$currency_symbol = get_woocommerce_currency_symbol( $currency_code );
			$cost = 0;
			$notice = '';
			$custom_total = 0;
			$cat_isset = 0;
			$product_tax_calculating = get_option( '_pixel_cog_tax_calculating');
			foreach ( $order->get_items() as $item_id => $item ) {
				$parent_id = false;
				if (isset( $item['variation_id'] ) && 0 != $item['variation_id']) {
					$parent_id = $item['product_id'];
				}
				$product_id = ( isset( $item['variation_id'] ) && 0 != $item['variation_id'] ? $item['variation_id'] : $item['product_id'] );
				$cost_type = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true );
				$product_cost = $this->get_product_cost( $product_id );
				if ($parent_id && $product_cost == '') {
					$product_cost = $this->get_product_cost( $parent_id );
				}
				$qlt = $item->get_quantity();
				if ($product_tax_calculating == 'no') {
					$price = $item->get_total();
				} else {
					$price = $item->get_total() + $item->get_total_tax();
				}
				if ('' !== $product_cost) {
					$cost = ($cost_type == 'percent') ? $cost + ($price * ($product_cost / 100) * $qlt) : $cost + ($product_cost * $qlt);
					$custom_total = $custom_total + $price;
				} else {
					$product_cost = $this->get_product_cost_by_cat( $product_id );
					$cost_type = $this->get_product_type_by_cat( $product_id );
					if ($product_cost) {
						$cost = ($cost_type == 'percent') ? $cost + ($price * ($product_cost / 100) * $qlt) : $cost + ($product_cost * $qlt);
						$custom_total = $custom_total + $price;
						$notice = "Category Cost of Goods was used for some products.";
						$cat_isset = 1;
					} else {
						$product_cost = get_option( '_pixel_cost_of_goods_cost_val');
						$cost_type = get_option( '_pixel_cost_of_goods_cost_type' );
						if ('' !== $product_cost) {
							$cost = ($cost_type == 'percent') ? $cost + ($price * ($product_cost / 100) * $qlt) : $cost + ($product_cost * $qlt);
							$custom_total = $custom_total + $price;
							if ($cat_isset == 1) {
								$notice = "Global and Category Cost of Goods was used for some products.";
							} else {
								$notice = "Global Cost of Goods was used for some products.";
							}
						} else {
							$notice = "Some products don't have Cost of Goods.";
						}
					}
				}
			}
			if (($order_total - $cost - $order_shipping_total) > 0) {
				//$profit = wc_price( $order_total - $cost - $order_shipping_total );
				$profit = $custom_total - $cost;
			} else {
				$profit = 0;
			}
			if ('' !== $notice) {
				update_metadata( 'post', $order_id, '_pixel_cost_of_goods_order_notice_cost', $notice );
				if (($custom_total - $cost) > 0) {
				//if (($order_total - $cost) > 0) {
					//$profit = wc_price( $custom_total - $cost );
					$profit = $custom_total - $cost;
				} else {
					$profit = 0;
				}
				update_metadata( 'post', $order_id, '_pixel_cost_of_goods_order_profit', $profit );
			} else {
				update_metadata( 'post', $order_id, '_pixel_cost_of_goods_order_notice_cost', '' );
				update_metadata( 'post', $order_id, '_pixel_cost_of_goods_order_profit', $profit );
			}
			update_metadata( 'post', $order_id, '_pixel_cost_of_goods_order_cost', $cost );
		}

		/**
		 * get_product_cost_by_cat.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_cost_by_cat( $product_id ) {
			$term_list = wp_get_post_terms($product_id,'product_cat',array('fields'=>'ids'));
			$cost = array();
			foreach ($term_list as $term_id) {
				$cost[$term_id] = get_term_meta( $term_id, '_pixel_cost_of_goods_cost_val', true );
			}
			if ( empty( $cost ) ) {
				return '';
			} else {
				asort( $cost );
				$max = end( $cost );
				return $max;
			}
		}

		/**
		 * get_product_type_by_cat.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_type_by_cat( $product_id ) {
			$term_list = wp_get_post_terms($product_id,'product_cat',array('fields'=>'ids'));
			$cost = array();
			foreach ($term_list as $term_id) {
				$cost[$term_id] = array(
					get_term_meta( $term_id, '_pixel_cost_of_goods_cost_val', true ),
					get_term_meta( $term_id, '_pixel_cost_of_goods_cost_type', true )
				);
			}
			if ( empty( $cost ) ) {
				return '';
			} else {
				asort( $cost );
				$max = end( $cost );
				return $max[1];
			}
		}

		/**
		 * get_product_cost.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_cost( $product_id ) {
			if ( '' === ( $cost = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_val', true ) ) ) {
				$product   = wc_get_product( $product_id );
				$parent_id = $product->get_parent_id();
				$cost      = get_post_meta( $parent_id, '_pixel_cost_of_goods_cost_val', true );
			}
			return str_replace( ',', '.', $cost );
		}

		/**
		 * get_product_profit.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_profit( $product_id ) {
			$product = wc_get_product( $product_id );
			$tax_enabled = wc_tax_enabled();
			$product_tax_calculating = get_option( '_pixel_cog_tax_calculating');
			if ($tax_enabled && (wc_get_price_including_tax( $product ) == wc_get_price_excluding_tax( $product )) && $product_tax_calculating == 'yes') {
				return 'depends on tax';
			} else {
				if ($tax_enabled && $product_tax_calculating == 'yes') {
					$price = wc_get_price_including_tax( $product );
				} else {
					$price = wc_get_price_excluding_tax( $product );
				}

				return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) || '' === ( $price ) ? '' : $price - $cost );

			}
		}

		/**
		 * get_product_percent_profit.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_percent_profit( $product_id ) {
			$product = wc_get_product( $product_id );
			$tax_enabled = wc_tax_enabled();
			$product_tax_calculating = get_option( '_pixel_cog_tax_calculating');
			if ($tax_enabled && (wc_get_price_including_tax( $product ) == wc_get_price_excluding_tax( $product )) && $product_tax_calculating == 'yes') {
				return 'depends on tax';
			} else {
				if ($tax_enabled && $product_tax_calculating == 'yes') {
					$price = wc_get_price_including_tax( $product );
				} else {
					$price = wc_get_price_excluding_tax( $product );
				}
				return ( '' === ( $cost = $this->get_product_cost( $product_id ) ) || '' === ( $price ) ? '' : $price - ( $price * $cost / 100 ));
			}
		}

		/**
		 * get_product_profit_html.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function get_product_profit_html( $product_id ) {
			$tax_enabled = wc_tax_enabled();
			$product = wc_get_product( $product_id );
			$product_tax_calculating = get_option( '_pixel_cog_tax_calculating');
			$cost_type = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true );
			if ($tax_enabled && $product_tax_calculating == 'yes') {
				$price = wc_get_price_including_tax( $product );
			} else {
				$price = wc_get_price_excluding_tax( $product );
			}
			if ($this->get_product_profit( $product_id ) == 'depends on tax' && $product_tax_calculating == 'yes') {
				return 'Profit will vary depending on tax settings';
			} else {
				if ( $product->is_type( 'variable' ) ) {
					if($cost_type == 'percent') {
						return ( '' === ( $profit = $this->get_product_percent_profit( $product_id ) ) ? '' :
							wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? 100 - $cost : '' ) ) );
					} else {
						return ( '' === ( $profit = $this->get_product_profit( $product_id ) ) ? '' :
							wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? (100 / $price) * $profit : '' ) ) );
					}
				} else {
					if($cost_type == 'percent') {
						return ( '' === ( $profit = $this->get_product_percent_profit( $product_id ) ) ? '' :
							wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? 100 - $cost : '' ) ) );
					} else {
						return ( '' === ( $profit = $this->get_product_profit( $product_id ) ) ? '' :
							wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost = $this->get_product_cost( $product_id ) ) ? (100 / $price) * $profit : '' ) ) );
					}
				}
			}
		}

		/**
		 * add_cost_to_product.
		 *
		 * @version 1.0.0
		 */
		function add_cost_to_product() {
			$product_id = get_the_ID();
			$product = wc_get_product( $product_id );
			$description = sprintf( __( 'Profit: %s', 'pixel_cost_of_goods' ), $this->get_product_profit_html( $product_id ) );
			if ( $product->is_type( 'variable' ) ) {
				$description = 'You can add COG to each variation if needed.';
			}
			woocommerce_wp_select(
				array(
					'id'      => '_pixel_cost_of_goods_cost_type',
					'label'   => __( 'Cost of Goods', 'pixel_cost_of_goods' ),
					'value' => get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true ),
					'options' => array(
						'fix'   => __( 'Fix', 'pixel_cost_of_goods' ),
						'percent'   => __( 'Percent', 'pixel_cost_of_goods' )
					),
				)
			);
			woocommerce_wp_text_input( array(
				'id'          => '_pixel_cost_of_goods_cost_val',
				'label'   => '',
				'value'       => get_post_meta( $product_id, '_pixel_cost_of_goods_cost_val', true ),
				'data_type'   => 'price',
				'description' => $description,
			) );
		}

		/**
		 * save_cost_to_product.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function save_cost_to_product( $post_id, $__post ) {
			if ( isset( $_POST['_pixel_cost_of_goods_cost_type'] ) ) {
				update_post_meta( $post_id, '_pixel_cost_of_goods_cost_type', $_POST['_pixel_cost_of_goods_cost_type'] );
			} else {
				update_post_meta( $post_id, '_pixel_cost_of_goods_cost_type', '' );
			}
			if ( isset( $_POST['_pixel_cost_of_goods_cost_val'] ) ) {
				update_post_meta( $post_id, '_pixel_cost_of_goods_cost_val', $_POST['_pixel_cost_of_goods_cost_val'] );
			} else {
				update_post_meta( $post_id, '_pixel_cost_of_goods_cost_type', '' );
			}
		}

		/**
		 * add_cost_to_product_variable.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_cost_to_product_variable( $loop, $variation_data, $variation ) {
			?>
			<div class="pixel_cog_wrap">
				<label><?php _e( 'Cost of Goods', 'pixel_cost_of_goods' ); ?></label>
				<div class="pixel_cog_row">
					<?php woocommerce_wp_select(
						array(
							'id'            => "_variable_pixel_cost_of_goods_cost_type_{$loop}",
							'name'          => "_variable_pixel_cost_of_goods_cost_type[{$loop}]",
							'value' => get_post_meta( $variation->ID, '_pixel_cost_of_goods_cost_type', true ),
							'wrapper_class' => 'form-field',
							'options' => array(
								'fix'   => __( 'Fix', 'pixel_cost_of_goods' ),
								'percent'   => __( 'Percent', 'pixel_cost_of_goods' )
							)
						)
					); ?>
					<?php woocommerce_wp_text_input( array(
						'id'            => "_variable_pixel_cost_of_goods_cost_val_{$loop}",
						'label'   => '',
						'name'          => "_variable_pixel_cost_of_goods_cost_val[{$loop}]",
						'value'         => get_post_meta( $variation->ID, '_pixel_cost_of_goods_cost_val', true ),
						'data_type'   => 'price',
						'wrapper_class' => 'form-field',
						'description' => sprintf( __( 'Profit: %s', 'pixel_cost_of_goods' ), $this->get_product_profit_html( $variation->ID ) ),
					) ); ?>
				</div>
			</div>
			<?php
		}

		/**
		 * save_cost_to_product_variable.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function save_cost_to_product_variable( $variation_id, $i ) {
			if ( isset( $_POST['_variable_pixel_cost_of_goods_cost_type'][ $i ] ) ) {
				update_post_meta( $variation_id, '_pixel_cost_of_goods_cost_type', $_POST['_variable_pixel_cost_of_goods_cost_type'][ $i ] );
			} else {
				update_post_meta( $variation_id, '_pixel_cost_of_goods_cost_type', '' );
			}
			if ( isset( $_POST['_variable_pixel_cost_of_goods_cost_val'][ $i ] ) ) {
				update_post_meta( $variation_id, '_pixel_cost_of_goods_cost_val', $_POST['_variable_pixel_cost_of_goods_cost_val'][ $i ] );
			} else {
				update_post_meta( $variation_id, '_pixel_cost_of_goods_cost_type', '' );
			}
		}

		function get_product_column_data_html($product_id, $column) {
			if ( 'pixel_profit' === $column || 'pixel_cost' === $column ) {
				$product = wc_get_product( $product_id );
				$tax_enabled = wc_tax_enabled();
				$product_tax_calculating = get_option( '_pixel_cog_tax_calculating');
				if ('pixel_profit' === $column && $this->get_product_profit( $product_id ) == 'depends on tax') {
					return 'depends on tax';
				} else {
					if ( $product->is_type( 'variable' ) ) {
						$data = array();
						foreach ( $product->get_children() as $variation_id ) {
							$cost_type = get_post_meta( $variation_id, '_pixel_cost_of_goods_cost_type', true );
							$cost = $this->get_product_cost( $variation_id );
							$profit = $this->get_product_profit( $variation_id );
							$variation = wc_get_product($variation_id);
							//$price = wc_get_price_excluding_tax( $variation );
							if ($tax_enabled && $product_tax_calculating == 'yes') {
								$price = wc_get_price_including_tax( $variation );
							} else {
								$price = wc_get_price_excluding_tax( $variation );
							}
							if($cost_type == 'percent') {
								//$price = $variation->get_price();
								if ($tax_enabled && $product_tax_calculating == 'yes') {
									$price = wc_get_price_including_tax( $variation );
								} else {
									$price = wc_get_price_excluding_tax( $variation );
								}
								$profit = $price - ($price * ($cost / 100));
								if ($profit == 'depends on tax') {
									$data[ $variation_id ] = array(
										'cost' => $cost ? wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() ).'%' : '',
										'profit' => 'Profit will vary depending on tax settings'
									);
								} else {
									$data[ $variation_id ] = array(
										'cost' => $cost ? wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() ).'%' : '',
										'profit' => ( '' === ( $profit ) ? '' : wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost ) ? 100 - $cost : '' ) ) )
									);
								}
							} else {
								if ($profit == 'depends on tax') {
									$data[ $variation_id ] = array(
										'cost' => $cost ? '$'.wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() ) : '',
										'profit' => 'Profit will vary depending on tax settings'
									);
								} else {
									$data[ $variation_id ] = array(
										'cost' => $cost ? '$'.wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() ) : '',
										'profit' => ( '' === ( $profit ) ? '' : wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost ) ? (100 / $price) * $profit : '' ) ) )
									);
								}
							}
						}
						if ( empty( $data ) ) {
							return '';
						} else {
							asort( $data );
							$min = current( $data );
							$max = end( $data );
							if ($column == 'pixel_cost') {
								if ( $min !== $max ) {
									$html = $this->wc_format_price_range_cost_type( $min['cost'], $max['cost'] );
								} else {
									$html = wc_format_decimal( sanitize_text_field( wp_unslash( $min['cost'] ) ), wc_get_price_decimals() );
								}
							} elseif ($column == 'pixel_profit') {
								if ( $min !== $max ) {
									$html = $this->wc_format_price_range_cost_type( $min['profit'], $max['profit'] );
								} else {
									$html = $min['profit'];
								}
							}
						}
					} else {
						$cost_type = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true );
						//$price = wc_get_price_excluding_tax( $product );
						if ($tax_enabled && $product_tax_calculating == 'yes') {
							$price = wc_get_price_including_tax( $product );
						} else {
							$price = wc_get_price_excluding_tax( $product );
						}
						if($cost_type == 'percent') {
							//$price = $product->get_regular_price();
							if ($tax_enabled && $product_tax_calculating == 'yes') {
								$price = wc_get_price_including_tax( $product );
							} else {
								$price = wc_get_price_excluding_tax( $product );
							}
							$cost = $this->get_product_cost( $product_id );
							$profit = $price - ($price * ($cost / 100));
							if ('' != $cost) {
								if ($column == 'pixel_cost') {
									$html = wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() ).'%';
								} elseif ($column == 'pixel_profit') {
									if ($profit == 'depends on tax') {
										$html = 'Profit will vary depending on tax settings';
									} else {
										$html = ( '' === ( $profit ) ? '' : wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost ) ? 100 - $cost : '' ) ) );
									}
								}
							} else {
								$html = '-';
							}
						} else {
							$cost = $this->get_product_cost( $product_id );
							$profit = $this->get_product_profit( $product_id );
							if ('' != $cost) {
								if ($column == 'pixel_cost') {
									$html = '$'.wc_format_decimal( sanitize_text_field( wp_unslash( $cost ) ), wc_get_price_decimals() );
								} elseif ($column == 'pixel_profit') {
									if ($profit == 'depends on tax') {
										$html = 'Profit will vary depending on tax settings';
									} else {
										$html = ( '' === ( $profit ) ? '' : wc_price( $profit ) . sprintf( ' (%0.2f%%)', ( 0 != ( $cost ) ? (100 / $price) * $profit : '' ) ) );
									}
								}
							} else {
								$html = '-';
							}
						}
					}

					return $html;
				}
			}
		}

		/**
		 * Format a price range for display.
		 *
		 * @param  string $from Price from.
		 * @param  string $to   Price to.
		 * @return string
		 */
		function wc_format_price_range_cost_type( $from, $to ) {
			/* translators: 1: price from 2: price to */
			$price = sprintf( _x( '%1$s &ndash; %2$s', 'Price range: from-to', 'woocommerce' ), $from, $to );

			return apply_filters( 'woocommerce_format_price_range', $price, $from, $to );
		}

		/**
		 * taxonomy_edit_custom_meta_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function taxonomy_edit_custom_meta_field($term) {
			$cat_id = $term->term_id; ?>
			<tr class="form-field">
				<th><?php _e( 'Cost of Goods', 'pixel_cost_of_goods' ); ?></th>
				<td class="pixel_cog_row">
                    <p class=" form-field _pixel_cost_of_goods_cost_type_field">
                        <label for="_pixel_cost_of_goods_cost_type"></label>
                        <select id="_pixel_cost_of_goods_cost_type" name="_pixel_cost_of_goods_cost_type" class="select short">
                            <option value="fix" <?php selected( get_term_meta( $cat_id, '_pixel_cost_of_goods_cost_type', true ),
                                esc_attr( 'fix' ) ); ?>>Fix</option>
                            <option value="percent" <?php selected( get_term_meta( $cat_id, '_pixel_cost_of_goods_cost_type', true ),
                                esc_attr( 'percent' ) ); ?>>Percent</option>
                        </select>
                    </p>
                    <p class="form-field _pixel_cost_of_goods_cost_val_field">
                        <label for="_pixel_cost_of_goods_cost_val"></label>
                        <input type="text" class="short wc_input_price" name="_pixel_cost_of_goods_cost_val" id="_pixel_cost_of_goods_cost_val" value="<?php echo get_term_meta( $cat_id, '_pixel_cost_of_goods_cost_val', true ); ?>" placeholder="">
                    </p>
				</td>
			</tr>
			<?php
		}

		/**
		 * taxonomy_add_custom_meta_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function taxonomy_add_custom_meta_field() {
			 ?>
			<div class="form-field">
				<label><?php _e( 'Cost of Goods', 'pixel_cost_of_goods' ); ?></label>
				<div class="pixel_cog_row">
                    <p class="form-field _pixel_cost_of_goods_cost_type_field">
                        <label for="_pixel_cost_of_goods_cost_type"></label>
                        <select id="_pixel_cost_of_goods_cost_type" name="_pixel_cost_of_goods_cost_type" class="select short">
                            <option value="fix" selected="selected">Fix</option>
                            <option value="percent">Percent</option>
                        </select>
                    </p>
                    <p class="form-field _pixel_cost_of_goods_cost_val_field">
                        <label for="_pixel_cost_of_goods_cost_val"></label>
                        <input type="text" class="short wc_input_price" style="" name="_pixel_cost_of_goods_cost_val" id="_pixel_cost_of_goods_cost_val" value="" placeholder="">
                    </p>
				</div>
			</div>
			<?php
		}

		/**
		 * taxonomy_save_custom_meta_field.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function taxonomy_save_custom_meta_field( $term_id ) {
			if ( isset( $_POST['_pixel_cost_of_goods_cost_type'] ) ) {
				update_term_meta( $term_id, '_pixel_cost_of_goods_cost_type', $_POST['_pixel_cost_of_goods_cost_type'] );
			} else {
				update_term_meta( $term_id, '_pixel_cost_of_goods_cost_type', '' );
			}
			if ( isset( $_POST['_pixel_cost_of_goods_cost_val'] ) ) {
				update_term_meta( $term_id, '_pixel_cost_of_goods_cost_val', $_POST['_pixel_cost_of_goods_cost_val'] );
			} else {
				update_term_meta( $term_id, '_pixel_cost_of_goods_cost_type', '' );
			}
		}
	}


endif;

return new PixelCostOfGoodsCore();
