<?php
/**
 * PixelImportCostOfGoods
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * PixelImportCostOfGoods
 */
class PixelImportCostOfGoods {

	/**
	 * Single instance of the class
	 *
	 * @var \PixelImportCostOfGoods
	 * @since 1.0.0
	 */
	protected static $instance;

	public $limit;
	public $offset;
	public $product_key;

	function pixel_import_cost_product(){

		$type  = $_POST['type'];
		$csvdata   = $_POST['csvdata'];

		$data=array(
			"type" => $type
		);

		if ($type == 'products') {
			foreach ($csvdata as $products){
				update_post_meta( $products['item_ID'], '_pixel_cost_of_goods_cost_type', mb_strtolower($products['cost_of_goods_type']) );
				update_post_meta( $products['item_ID'], '_pixel_cost_of_goods_cost_val', $products['cost_of_goods_value'] );
			}
		}

		if ($type == 'categories') {
			foreach ($csvdata as $categories) {
				update_term_meta( $categories['category_ID'], '_pixel_cost_of_goods_cost_type', mb_strtolower($categories['cost_of_goods_type']) );
				update_term_meta( $categories['category_ID'], '_pixel_cost_of_goods_cost_val', $categories['cost_of_goods_value'] );
			}
		}

		wp_send_json( $data );
	}

	function pixel_import_cost_product_plugins() {
		global $wpdb;

        $this->offset  = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $this->limit   = isset($_POST['limit']) ? $_POST['limit'] : 'undefined';
		$this->product_key  = $_POST['product_key'];

		if ( $this->limit == 'undefined' ) {
			$this->limit = 100;
		}

		$query_from = "FROM {$wpdb->prefix}posts
			WHERE post_type IN ( 'product', 'product_variation' )
			AND post_status = 'publish' 
			 ";

		$products_array = $wpdb->get_results("SELECT ID as id {$query_from} LIMIT ".$this->limit." OFFSET ".$this->offset."");

		$total_products_number = $wpdb->get_var("SELECT COUNT( ID ) {$query_from};");

		if ($this->product_key == 'purchase_price') {
			foreach ($products_array as $products){
				$product = wc_get_product($products->id);
				$product_id = $product->get_id();
				$query_from_atum = "FROM {$wpdb->prefix}atum_product_data
				WHERE product_id = ".$product_id." 
				 ";
				$atum_result = $wpdb->get_results("SELECT purchase_price as purchase_price {$query_from_atum} ", ARRAY_N);
				$atum_result = $atum_result[0][0];
				if ($atum_result !== '') {
					update_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', 'fix' );
					update_post_meta( $product_id, '_pixel_cost_of_goods_cost_val', $atum_result );
				}
			}
		} else {
			foreach ($products_array as $products){
				$product = wc_get_product($products->id);
				$product_id = $product->get_id();
				if (get_post_meta( $product_id, $this->product_key, true )  !== '') {
					update_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', 'fix' );
					update_post_meta( $product_id, '_pixel_cost_of_goods_cost_val', get_post_meta( $product_id, $this->product_key, true ) );
				}
			}
		}

		$new_offset = $this->offset + $this->limit;

		if (($total_products_number - $new_offset) < $this->limit){
			$this->limit = $total_products_number - $new_offset;
		}

		$data_result=array(
			"limit"=> "$this->limit",
			"offset" => "$new_offset",
			"loop" => ($new_offset < $total_products_number) ? "1" : "0"
		);
		wp_send_json( $data_result );
	}

}
