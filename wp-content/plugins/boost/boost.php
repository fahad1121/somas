<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://url.url
 * @since             1.0.0
 * @package           Boost
 *
 * @wordpress-plugin
 * Plugin Name:       Boost
 * Plugin URI:        https://www.boostplugin.com
 * Description:       Automatically capture and show recent visitors’ activity as nice, auto-vanishing pop-ups. We call them BOOSTS.
 * Version:           1.1.0
 * Author:            The Boost Plugin
 * Author URI:        https://www.boostplugin.com
 * License:           GPL-2.0+
 * License URI:       https://www.boostplugin.com/terms-return-policy
 * Text Domain:       boost
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('BOOST_PLUGIN_NAME',							    'boost');

define('BOOST_PLUGIN_VERSION',							'1.1.0');

define('BOOST_PLUGIN_PATH',					            plugin_dir_path(__FILE__));

define('BOOST_PLUGIN_MAIN_FILE_PATH',					__FILE__);

define( 'BOOST_LICENSE_ITEM_NAME', 'The Boost Plugin' );

define( 'BOOST_LICENSE_STORE_URL', 'https://www.boostplugin.com' );

define( 'BOOST_LICENSE_PAGE', admin_url('admin.php?page=boost_license'));

define( 'BOOST_MAIN_PAGE', admin_url('admin.php?page=boost_main'));

define('BOOST_DB_TABLE_PREFIX',								'boost');

define('TABLE_BOOSTS',									BOOST_DB_TABLE_PREFIX . '_boosts');

define('TABLE_BOOSTS_POST_TYPES',						BOOST_DB_TABLE_PREFIX . '_boosts_post_types');

define('TABLE_BOOSTS_PRODUCTS',							BOOST_DB_TABLE_PREFIX . '_boosts_products');

define('TABLE_BOOSTS_PRODUCT_CATEGORIES',				BOOST_DB_TABLE_PREFIX . '_boosts_product_categories');

define('TABLE_BOOSTS_URLS',								BOOST_DB_TABLE_PREFIX . '_boosts_urls');

define('TABLE_BOOSTS_EXCLUDE_URLS',						BOOST_DB_TABLE_PREFIX . '_boosts_exclude_urls');

define('TABLE_BOOSTS_EXCLUDE_POST_TYPES',				BOOST_DB_TABLE_PREFIX . '_boosts_exclude_post_types');

define('TABLE_BOOSTS_SPECIFIC_PAGES',				    BOOST_DB_TABLE_PREFIX . '_boosts_specific_pages');

define('TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES',			BOOST_DB_TABLE_PREFIX . '_boosts_exclude_specific_pages');

define('TABLE_BOOSTS_TAXONOMIES',				        BOOST_DB_TABLE_PREFIX . '_boosts_taxonomies');

define('TABLE_BOOSTS_EXCLUDE_TAXONOMIES',			    BOOST_DB_TABLE_PREFIX . '_boosts_exclude_taxonomies');

define('TABLE_BOOSTS_LEADS_DATA',						BOOST_DB_TABLE_PREFIX . '_boosts_leads_data');

define('TABLE_BOOSTS_WOOCOMMERCE_DATA',					BOOST_DB_TABLE_PREFIX . '_boosts_woocommerce_data');

define('TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS',				BOOST_DB_TABLE_PREFIX . '_boosts_woocommerce_products');

define('TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES',			BOOST_DB_TABLE_PREFIX . '_boosts_woocommerce_categories');

define('TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA',		BOOST_DB_TABLE_PREFIX . '_boosts_easydigitaldownloads_data');

define('TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS',	BOOST_DB_TABLE_PREFIX . '_boosts_easydigitaldownloads_products');

define('TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES',	BOOST_DB_TABLE_PREFIX . '_boosts_easydigitaldownloads_categories');

define('TABLE_ACTIONS',									BOOST_DB_TABLE_PREFIX . '_actions');

define('TABLE_BANNED_WORDS',							BOOST_DB_TABLE_PREFIX . '_banned_words');

define('TABLE_BOOSTS_FAKE_COUNTRIES',					BOOST_DB_TABLE_PREFIX . '_boosts_fake_countries');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-boost-activator.php
 */
function activate_boost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boost-activator.php';
	Boost_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-boost-deactivator.php
 */
function deactivate_boost() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-boost-deactivator.php';
	Boost_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_boost' );
register_deactivation_hook( __FILE__, 'deactivate_boost' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-boost.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_boost() {

	$plugin = new Boost();
	$plugin->run();

}
run_boost();
