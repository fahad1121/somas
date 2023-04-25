<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

define('BOOST_DB_TABLE_PREFIX',								'boost');
define('TABLE_BOOSTS',									BOOST_DB_TABLE_PREFIX . '_boosts');
define('TABLE_BOOSTS_PRODUCTS',							BOOST_DB_TABLE_PREFIX . '_boosts_products');
define('TABLE_BOOSTS_PRODUCT_CATEGORIES',				BOOST_DB_TABLE_PREFIX . '_boosts_product_categories');
define('TABLE_BOOSTS_POST_TYPES',						BOOST_DB_TABLE_PREFIX . '_boosts_post_types');
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

global $wpdb;

$tables = array(
//	$wpdb->prefix . TABLE_BOOSTS_LEADS_DATA,
//	$wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA,
//	$wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS,
//	$wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES,
//	$wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA,
//	$wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS,
//	$wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES,
//	$wpdb->prefix . TABLE_BOOSTS,
//	$wpdb->prefix . TABLE_BOOSTS_PRODUCTS,
//	$wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES,
//	$wpdb->prefix . TABLE_BOOSTS_POST_TYPES,
//	$wpdb->prefix . TABLE_BOOSTS_URLS,
//	$wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES,
//	$wpdb->prefix . TABLE_BOOSTS_TAXONOMIES,
//	$wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS,
//	$wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES,
//	$wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES,
//	$wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES,
//	$wpdb->prefix . TABLE_ACTIONS,
//	$wpdb->prefix . TABLE_BANNED_WORDS,
//	$wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES,
);

foreach ($tables as $table) {
	$wpdb->query('DROP TABLE IF EXISTS ' . $table);
}

//delete_option('boost_version');
//delete_option('boost_options');
