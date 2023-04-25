<?php
/**
 * Cost of Goods for WooCommerce - Admin Columns Class
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  PixelYourSite.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PixelCostOfGoodsAdminColumns' ) ) :

	class PixelCostOfGoodsAdminColumns {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function __construct() {
			// Orders columns
			add_filter( 'manage_edit-shop_order_columns', array( $this, 'add_order_columns' ), PHP_INT_MAX );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_order_columns' ), PHP_INT_MAX );
			// Products columns
			add_filter( 'manage_edit-product_columns', array( $this, 'add_product_columns' ), PHP_INT_MAX );
			add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_columns' ), PHP_INT_MAX );

		}

		/**
		 * add_product_columns.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_product_columns( $columns ) {
			$_columns = array();
			foreach ( $columns as $column_key => $column_title ) {
				$_columns[ $column_key ] = $column_title;
				if ( 'price' === $column_key ) {
					$_columns['pixel_cost'] = __( 'Cost', 'pixel_cost_of_goods' );
					$_columns['pixel_profit'] = __( 'Profit', 'pixel_cost_of_goods' );
				}
			}
			return $_columns;
		}

		/**
		 * render_product_columns.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function render_product_columns( $column ) {
			if ( 'pixel_profit' === $column || 'pixel_cost' === $column ) {
				$product_id = get_the_ID();
				echo pixel_wc_cog()->core->get_product_column_data_html( $product_id, $column );
			}
		}

		/**
		 * add_order_columns.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_order_columns( $columns ) {
			$columns['pixel_cost'] = __( 'Cost', 'pixel_cost_of_goods' );
			$columns['pixel_profit'] = __( 'Profit', 'pixel_cost_of_goods' );
			$columns['pixel_recalculate'] = __( 'Recalculate', 'pixel_cost_of_goods' );
			return $columns;
		}

		/**
		 * render_order_columns.
		 *
		 * @param   string $column
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function render_order_columns( $column ) {
			$value_cost = get_post_meta( get_the_ID(), '_pixel_cost_of_goods_order_cost', true );
			$value_profit = get_post_meta( get_the_ID(), '_pixel_cost_of_goods_order_profit', true );
			if ( 'pixel_cost' === $column ) {
				$notice = get_post_meta( get_the_ID(), '_pixel_cost_of_goods_order_notice_cost', true );
				if ($value_cost != 0) {
					echo ( '' !== $value_cost ? wc_price( $value_cost ).(('' !== $notice) ? '<p>'.$notice.'</p>' : '') : '');
				} else {
					echo 'No Cost of Goods is configured for this order.';
				}
			}
			if ( 'pixel_profit' === $column ) {
				if ($value_cost != 0) {
					echo wc_price($value_profit);
				} else {
					echo '-';
				}
			}
			if ( 'pixel_recalculate' === $column ) {
				$cron = false;
				if( wp_next_scheduled( 'pixel_cog_calculate_cron' ) ) {
					$cron = 'disabled';
				}
				echo '<button type="button" data-id="'.get_the_ID().'" class="button action recalculate_button" '.$cron.'>' . __( 'Recalculate', 'woocommerce' ) . '</button>';
			}
		}


	}

endif;

return new PixelCostOfGoodsAdminColumns();

