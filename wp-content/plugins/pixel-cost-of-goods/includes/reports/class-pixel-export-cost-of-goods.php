<?php
/**
 * PixelExportCostOfGoods
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * PixelExportCostOfGoods
 */
class PixelExportCostOfGoods {

	/**
	 * Single instance of the class
	 *
	 * @var \PixelExportCostOfGoods
	 * @since 1.0.0
	 */
	protected static $instance;

	public $limit;
	public $offset;

	function pixel_export_cost_product() {
		global $wpdb;

        $this->offset  = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $this->limit   = isset($_POST['limit']) ? $_POST['limit'] : 'undefined';

		$step_data = array();

		$export_keys = array(
			'post_title' => 'item_name',
			'id' => 'item_ID',
			'price' => 'price',
			'sale_price' => 'sale_price',
			'cog_type' => 'cost_of_goods_type',
			'cog_val' => 'cost_of_goods_value'
		);

		if ( $this->limit == 'undefined' ) {
			$this->limit = 100;
			$this->clear_export_costs_output_csv($export_keys, 'pixel_products_cog_export.csv');
		}

		$query_from = "FROM {$wpdb->prefix}posts
			WHERE post_type IN ( 'product', 'product_variation' )
			AND post_status = 'publish' 
			 ";

		$products_array = $wpdb->get_results("SELECT ID as id {$query_from} LIMIT ".$this->limit." OFFSET ".$this->offset."");

		$total_products_number = $wpdb->get_var("SELECT COUNT( ID ) {$query_from};");

		$array = [];
		foreach ($products_array as $products){
			$product = wc_get_product($products->id);
			$product_id = $product->get_id();
			$array['id'] = $product_id;
			$array['cog_type'] = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true ) ? get_post_meta( $product_id, '_pixel_cost_of_goods_cost_type', true ) : 'fix';
			$array['cog_val'] = get_post_meta( $product_id, '_pixel_cost_of_goods_cost_val', true );
			$array['post_title'] = $product->get_title();
			$array['price'] = $product->get_regular_price();
			$array['sale_price'] = $product->get_sale_price();
			array_push($step_data , $array);
		}

		$new_offset = $this->offset + $this->limit;

		if (($total_products_number - $new_offset) < $this->limit){
			$this->limit = $total_products_number - $new_offset;
		}

		$data_result=array(
			"limit"=> "$this->limit",
			"offset" => "$new_offset",
			"total_products_number" => $total_products_number,
			"loop" => ($new_offset < $total_products_number) ? "1" : "0",
			"step_data" => $step_data,
			"file_url" => $this->export_costs_output_csv($step_data, $export_keys, 'pixel_products_cog_export.csv')
		);
		wp_send_json( $data_result );
	}

	function pixel_export_cat_cost_product() {
		global $wpdb;

        $this->offset  = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $this->limit   = isset($_POST['limit']) ? $_POST['limit'] : 'undefined';

		$step_data = array();

		$export_keys = array(
			'cat_title' => 'category_name',
			'id' => 'category_ID',
			'cog_type' => 'cost_of_goods_type',
			'cog_val' => 'cost_of_goods_value'
		);

		if ( $this->limit == 'undefined' ) {
			$this->limit = 100;
			$this->clear_export_costs_output_csv($export_keys, 'pixel_products_cat_cog_export.csv');
		}

		$query_from = "FROM {$wpdb->prefix}terms as terms
			INNER JOIN {$wpdb->prefix}term_taxonomy AS tm_taxonomy ON terms.term_id = tm_taxonomy.term_id
			WHERE 1=1 
			AND tm_taxonomy.taxonomy = 'product_cat' 
			 ";

		$terms_array = $wpdb->get_results("SELECT terms.term_id as term_id {$query_from} LIMIT ".$this->limit." OFFSET ".$this->offset."");

		$total_terms_number = $wpdb->get_var("SELECT COUNT( terms.term_id ) {$query_from};");

		$array = [];
		foreach ($terms_array as $terms){
			$term = get_term($terms->term_id);
			$array['id'] = $term->term_id;
			$array['cog_type'] = get_term_meta( $term->term_id, '_pixel_cost_of_goods_cost_type', true ) ? get_term_meta( $term->term_id, '_pixel_cost_of_goods_cost_type', true ) : 'fix';
			$array['cog_val'] = get_term_meta( $term->term_id, '_pixel_cost_of_goods_cost_val', true );
			$array['cat_title'] = $term->name;
			array_push($step_data , $array);
		}

		$new_offset = $this->offset + $this->limit;

		if (($total_terms_number - $new_offset) < $this->limit){
			$this->limit = $total_terms_number - $new_offset;
		}

		$data_result=array(
			"limit"=> "$this->limit",
			"offset" => "$new_offset",
			"loop" => ($new_offset < $total_terms_number) ? "1" : "0",
			"file_url" => $this->export_costs_output_csv($step_data, $export_keys, 'pixel_products_cat_cog_export.csv')
		);
		wp_send_json( $data_result );
	}

	function clear_export_costs_output_csv($export_keys, $file_name) {

		$file = PIXEL_COG_PATH . '/' . $file_name;
		$file_url = PIXEL_COG_URL . '/' . $file_name;
		# Generate CSV data from array
		$fh = fopen($file, 'w');
		# to use memory instead
		# write out the headers
		fputcsv($fh, $export_keys, ";");
		fclose($fh);

		return $file_url;
	}

	function export_costs_output_csv($items, $export_keys, $file_name) {

		$file = PIXEL_COG_PATH . '/' . $file_name;
		$file_url = PIXEL_COG_URL . '/' . $file_name;
		# Generate CSV data from array
		$fh = fopen($file, 'a+');
		# to use memory instead

		# write out the headers
		foreach ($items as $item) {
			unset($csv_line);
			foreach ($export_keys as $key => $value) {
				if (isset($item[$key])) {
					$csv_line[] = $item[$key];
				}
			}
			if(isset($csv_line)){
				fputcsv($fh, $csv_line, ";");
			}
		}
		fclose($fh);

		return $file_url;
	}

}
