<?php

/**
 * @wordpress-plugin
 * Plugin Name: Cost of Goods by PixelYourSite
 * Plugin URI: http://www.pixelyoursite.com/
 * Description: Add the Cost of Goods for WooCommerce products and calculate the profit for each order. Use "price minus cost" as Facebook Pixel events value with the PixelYourSite plugin..
 * Version: 1.0.11
 * Author: PixelYourSite
 * Author URI: http://www.pixelyoursite.com
 * License: GPLv3
 *
 * Requires at least: 4.9
 * Tested up to: 5.6
 *
 * WC requires at least: 3.5.4
 * WC tested up to: 6.7
 *
 * Text Domain: pixel_cost_of_goods
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'PIXEL_COG_VERSION', '1.0.11' );
define( 'PIXEL_COG_ITEM_NAME', 'WooCommerce Cost of Goods' );
define( 'PIXEL_COG_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'PIXEL_COG_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'PIXEL_COG_ASSETS', untrailingslashit( plugin_dir_url( __FILE__ ).'assets' ) );
define( 'PIXEL_COG_PLUGIN_NAME', 'WooCommerce Cost of Goods' );

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once ABSPATH . 'wp-admin/includes/plugin.php';
// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) {
	return;
}

$db_update_status = get_option( 'pixel_cog_db_update_status' );

if ($db_update_status != 'complete') {
	add_action( 'admin_notices', 'pixel_cog_need_db_update_notice' );
}

function pixel_cog_need_db_update_notice() {
	?>
	<div class='notice notice-warning'>
		<p>WooCommerce Cost of Goods needs a database update: <a href="#" id="calculate_cost_btn_notice">Update Now</a></p>
	</div>
	<?php
}

$license_status = get_option( 'pixel_cost__license_status' );

if ($license_status != 'valid' && $license_status != 'expired' && $license_status != 'disabled') {
	add_action( 'admin_notices', 'pixel_cog_license_error_notice' );
}

if ($license_status == 'expired') {
	add_action( 'admin_notices', 'pixel_cog_license_expired_notice' );
}

if ($license_status == 'disabled') {
	add_action( 'admin_notices', 'pixel_cog_license_disabled_notice' );
}

include "rapid-addon.php";
$pixel_cog_addon = new RapidAddon('Cost of Goods by PixelYourSite', 'pixel_cog_addon');

if ( is_plugin_active('wp-all-import-pro/wp-all-import-pro.php')){
	if($license_status == 'valid') {
		$pixel_cog_addon->add_field('_pixel_cost_of_goods_cost_val', 'Cost of Goods Price', 'text');
		$pixel_cog_addon->set_import_function('pixel_cog_addon_import_function');
		$pixel_cog_addon->run(array( "post_types" => array( "product" ) ));
	}
}

function pixel_cog_addon_import_function( $post_id, $data, $import_options, $article ) {

	global $pixel_cog_addon;

	if ( !empty( $article['ID'] ) or $pixel_cog_addon->can_update_meta( '_pixel_cost_of_goods_cost_type', $import_options ) ) {
		update_post_meta( $post_id, '_pixel_cost_of_goods_cost_type', 'fix' );
	}

	if ( !empty( $article['ID'] ) or $pixel_cog_addon->can_update_meta( '_pixel_cost_of_goods_cost_val', $import_options ) ) {
		update_post_meta( $post_id, '_pixel_cost_of_goods_cost_val', $data['_pixel_cost_of_goods_cost_val'] );
	}
}

function pixel_cog_license_error_notice() {
	?>
	<div class='notice notice-warning'>
		<p>Please activate your license key for the "Cost of Goods for Woocommerce by PixelYourSite" plugin. <a href="<?php echo get_site_url().'/wp-admin/admin.php?page=wc-settings&tab=pixel_cost_of_goods'; ?>" target="_blank">Click here</a></p>
	</div>
	<?php
}

function pixel_cog_license_expired_notice() {
	?>
	<div class='notice notice-error'>
		<p>Your <a href="<?php echo get_site_url().'/wp-admin/admin.php?page=wc-settings&tab=pixel_cost_of_goods'; ?>" target="_blank">license</a> for the Cost of Goods for Woocommerce by PixelYourSite is expired. Please renew it.</p>
	</div>
	<?php
}

function pixel_cog_license_disabled_notice() {
	?>
	<div class='notice notice-error'>
		<p>Your license for the Cost of Goods for Woocommerce by PixelYourSite is disabled. Please replace it with a valid license.</p>
	</div>
	<?php
}

$license = get_option( 'pixel_cost_of_goods_license');
if ($license) {
	if ( false === ( $value = get_transient( 'pixel_cog_check_license' ) ) ) {
		$store_url = 'https://www.pixelyoursite.com';
		$item_name = 'WooCommerce Cost of Goods';
		$api_params = array(
			'edd_action' => 'check_license',
			'license' => $license,
			'item_name' => urlencode( $item_name ),
			'url' => home_url()
		);
		$response = wp_remote_post( $store_url, array( 'body' => $api_params, 'timeout' => 120, 'sslverify' => false ) );
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( 'pixel_cost__license_status', $license_data->license );

		set_transient( 'pixel_cog_check_license', $license_data->license, 24 * HOUR_IN_SECONDS );
	}
}
require_once 'includes/class-pixel-cost-of-goods-main.php';

add_action( 'admin_init', 'updatePlugin', 0 );

function updatePlugin() {

	require_once 'includes/class-plugin-updater.php';

	$license_key = trim( get_option( 'pixel_cost_of_goods_license' ) );

	new Plugin_Updater( 'https://www.pixelyoursite.com', __FILE__, array(
			'version'   => PIXEL_COG_VERSION,
			'license'   => $license_key,
			'item_name' => PIXEL_COG_ITEM_NAME,
			'author'    => 'PixelYourSite',
			'url'           => home_url(),
			'beta'          => false
		)
	);

}
