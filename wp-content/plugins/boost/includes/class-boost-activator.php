<?php

/**
 * Fired during plugin activation
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Boost
 * @subpackage Boost/includes
 * @author     cristian stoicescu <email@email.email>
 */
class Boost_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
		$table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
		$table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
		$table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
		$table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
		$table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
		$table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
		$table_boosts = $wpdb->prefix . TABLE_BOOSTS;
		$table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;
		$table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
		$table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
		$table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
		$table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
		$table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
		$table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
		$table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
		$table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
		$table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
		$table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
		$table_actions = $wpdb->prefix . TABLE_ACTIONS;
		$table_banned_words = $wpdb->prefix . TABLE_BANNED_WORDS;

		$sql = "CREATE TABLE $table_woocommerce_data (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 				  	`boost_id` INT NOT NULL , 
 				  	`subtype` VARCHAR(30) NOT NULL , 
 				  	`stock_number` INT(6) NOT NULL , 
 					`df_on_all_purchased_products` TINYINT(1) NOT NULL ,
 					`df_on_all_purchased_categories` TINYINT(1) NOT NULL ,
 					`df_on_all_products` TINYINT(1) NOT NULL ,
 					`df_on_all_categories` TINYINT(1) NOT NULL ,
 					`df_on_cart_page` TINYINT(1) NOT NULL ,
 					`df_on_checkout_page` TINYINT(1) NOT NULL ,
 					`df_on_home_page` TINYINT(1) NOT NULL ,
 				  	PRIMARY KEY (`id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_woocommerce_products (
 				  	`boost_id` INT NOT NULL , 
 				  	`product_id` INT , 
 				  	`product_name` VARCHAR(200) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_woocommerce_categories (
 				  	`boost_id` INT NOT NULL , 
 				  	`category_id` INT , 
 				  	`category_name` VARCHAR(200) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_easydigitaldownloads_data (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 				  	`boost_id` INT NOT NULL , 
 				  	`subtype` VARCHAR(30) NOT NULL , 
 					`df_on_all_purchased_products` TINYINT(1) NOT NULL ,
 					`df_on_all_purchased_categories` TINYINT(1) NOT NULL ,
 					`df_on_all_products` TINYINT(1) NOT NULL ,
 					`df_on_all_categories` TINYINT(1) NOT NULL ,
 					`df_on_cart_page` TINYINT(1) NOT NULL ,
 					`df_on_checkout_page` TINYINT(1) NOT NULL ,
 					`df_on_home_page` TINYINT(1) NOT NULL ,
 				  	PRIMARY KEY (`id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_easydigitaldownloads_products (
 				  	`boost_id` INT NOT NULL , 
 				  	`product_id` INT , 
 				  	`product_name` VARCHAR(200) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_easydigitaldownloads_categories (
 				  	`boost_id` INT NOT NULL , 
 				  	`category_id` INT , 
 				  	`category_name` VARCHAR(200) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_leads_data (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 				  	`boost_id` INT NOT NULL , 
 				  	`capture_url` VARCHAR(200) NOT NULL , 
 				  	`form_selector` VARCHAR(200) NOT NULL , 
 				  	`form_username_field` VARCHAR(50) NOT NULL , 
 				  	`form_surname_field` VARCHAR(50) NOT NULL , 
 				  	PRIMARY KEY (`id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_actions (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 					`boost_id` INT NOT NULL , 
 					`user_name` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`order_id` INT , 
 					`product_id` INT , 
 					`time` INT NOT NULL , 
 					`town` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`state` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`country` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`fake` TINYINT(1) NOT NULL , 
 					 PRIMARY KEY (`id`),
 					 INDEX `BOOSTID` (`boost_id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_urls (
 					`boost_id` INT NOT NULL ,
 					`url` VARCHAR(200) NOT NULL ,
 					`url_type` VARCHAR(10) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_exclude_urls (
 					`boost_id` INT NOT NULL , 
 					`url` VARCHAR(200) NOT NULL ,
 					`url_type` VARCHAR(10) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_post_types (
 					`boost_id` INT NOT NULL , 
 					`post_type` VARCHAR(100) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_exclude_post_types (
 					`boost_id` INT NOT NULL , 
 					`post_type` VARCHAR(100) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_specific_pages (
 					`boost_id` INT NOT NULL ,
 					`post_id` INT NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_exclude_specific_pages (
 					`boost_id` INT NOT NULL ,
 					`post_id` INT NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_taxonomies (
 					`boost_id` INT NOT NULL ,
 					`taxonomy_id` VARCHAR(100) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_exclude_taxonomies (
 					`boost_id` INT NOT NULL ,
 					`taxonomy_id` VARCHAR(100) NOT NULL );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 					`name` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`type` VARCHAR(100) NOT NULL , 
 					`desktop` TINYINT(1) NOT NULL , 
 					`mobile` TINYINT(1) NOT NULL , 
 					`notification_template` VARCHAR (10) NOT NULL , 
 					`active` TINYINT(1) NOT NULL , 
 					`top_message` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 					`message` VARCHAR(200) CHARACTER SET utf8 NOT NULL , 
 					`display_type` VARCHAR(20) NOT NULL ,
 					`dc_on_home_page` TINYINT(1) NOT NULL ,
 					`dc_on_urls` TINYINT(1) NOT NULL ,
 					`dc_on_specific_pages` TINYINT(1) NOT NULL ,
 					`dc_on_post_types` TINYINT(1) NOT NULL ,
 					`dc_on_taxonomies` TINYINT(1) NOT NULL ,
 					`de_on_home_page` TINYINT(1) NOT NULL ,
 					`de_on_urls` TINYINT(1) NOT NULL ,
 					`de_on_specific_pages` TINYINT(1) NOT NULL ,
 					`de_on_post_types` TINYINT(1) NOT NULL ,
 					`de_on_taxonomies` TINYINT(1) NOT NULL ,
 					`desktop_position` VARCHAR(50) NOT NULL , 
 					`mobile_position` VARCHAR(50) NOT NULL , 
 					`draft` TINYINT(1) NOT NULL , 
 					`enable_fake` TINYINT(1) NOT NULL , 
 					`min_actions_limit` INT NOT NULL ,
 					`desktop_notification_style_1` TINYINT(1) NOT NULL DEFAULT '1', 
 					`desktop_notification_style_2` TINYINT(1) NOT NULL , 
 					`desktop_notification_style_3` TINYINT(1) NOT NULL , 
 					`desktop_notification_style_4` TINYINT(1) NOT NULL , 
 					`desktop_notification_style_5` TINYINT(1) NOT NULL , 
 					`desktop_notification_style_6` TINYINT(1) NOT NULL ,
 					`mobile_notification_style_1` TINYINT(1) NOT NULL DEFAULT '1', 
 					`mobile_notification_style_2` TINYINT(1) NOT NULL , 
 					`mobile_notification_style_3` TINYINT(1) NOT NULL , 
 					`mobile_notification_style_4` TINYINT(1) NOT NULL , 
 					`mobile_notification_style_5` TINYINT(1) NOT NULL , 
 					`mobile_notification_style_6` TINYINT(1) NOT NULL ,
 					`use_products_images` TINYINT(1) NOT NULL ,
 					`use_maps_images` TINYINT(1) NOT NULL ,
 					`use_icons_images` TINYINT(1) NOT NULL ,
 					PRIMARY KEY (`id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_products (
 				  	`boost_id` INT NOT NULL , 
 				  	`product_id` INT );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_product_categories (
 				  	`boost_id` INT NOT NULL , 
 				  	`category_id` INT );";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_banned_words (
				  `id` INT NOT NULL AUTO_INCREMENT,
				  `word` VARCHAR(50) CHARACTER SET utf8 NOT NULL,
				  PRIMARY KEY (`id`));";

		dbDelta( $sql );

		$sql = "CREATE TABLE $table_boosts_fake_countries (
 					`id` INT NOT NULL AUTO_INCREMENT , 
 				  	`boost_id` INT NOT NULL , 
 				  	`country` VARCHAR(100) CHARACTER SET utf8 NOT NULL , 
 				  	PRIMARY KEY (`id`));";

		dbDelta( $sql );


		$boost_current_plugin_version = get_option('boost_version');
		$boost_plugin_version = BOOST_PLUGIN_VERSION;

		if (!$boost_current_plugin_version) {
			self::init_banned_words();
			self::init_default_boosts();

		}
		update_option( "boost_version", $boost_plugin_version );
	}

	public static function init_banned_words() {
		global $wpdb;
		$table_banned_words = $wpdb->prefix . TABLE_BANNED_WORDS;

		if ($wpdb->get_var("SELECT COUNT(*) FROM $table_banned_words") == 0) {
			include_once 'lib/bannedwords/banned-words.php';

			$query = "INSERT INTO `$table_banned_words` (`id`,`word`) VALUES ";
			foreach ($banned_words as $banned_word) {
				$query .= "(NULL,'" . str_replace("'","\'",$banned_word) . "'),";
			}
			$query = substr_replace($query, ';', -1);
			$wpdb->query($query);
		}
	}

	public static function init_default_boosts() {
		$WooCommerce_activated = class_exists( 'WooCommerce' ) ? true : false;
		$EDD_activated = class_exists( 'Easy_Digital_Downloads' ) ? true : false;

		$boost_model = new Boost_Boost_Model(BOOST_PLUGIN_NAME, BOOST_PLUGIN_VERSION);

		if ($WooCommerce_activated) {
			$create_boost = array(
				'type' => 'woocommerce',
				'desktop' => 1,
				'desktop_position' => 'bottom_left',
				'mobile' => 1,
				'mobile_position' => 'bottom',
				'active' => 0,
				'draft' => 0,
				'notification_template' => 'round',
				'display_type' => 'all_pages',
			);

			$create_boost['name'] = 'WooCommerce General Transaction';
			$create_boost['subtype'] = 'transaction';
			$create_boost['top_message'] = '[name] from [town], [state]';
			$create_boost['message'] = 'Bought [product_with_link]';
			$boost_model->create_boost($create_boost);

			$create_boost['name'] = 'WooCommerce Add to cart';
			$create_boost['subtype'] = 'add_to_cart';
			$create_boost['top_message'] = 'Someone from [town], [state]';
			$create_boost['message'] = 'Added [product_with_link] to cart';
			$boost_model->create_boost($create_boost);

			$create_boost['name'] = 'WooCommerce Stock messages';
			$create_boost['subtype'] = 'stock_messages';
			$create_boost['top_message'] = 'Don\'t miss it! Only [stock] left in stock';
			$create_boost['message'] = '[product_name]';
			$create_boost['display_type'] = 'all_products';
			$create_boost['stock_number'] = '5';
			$boost_model->create_boost($create_boost);

		}

		if ($EDD_activated) {
			$create_boost = array(
				'type' => 'easydigitaldownloads',
				'desktop' => 1,
				'desktop_position' => 'bottom_left',
				'mobile' => 1,
				'mobile_position' => 'bottom',
				'active' => 0,
				'draft' => 0,
				'notification_template' => 'round',
				'display_type' => 'all_pages',
			);

			$create_boost['name'] = 'EDD General Transaction';
			$create_boost['subtype'] = 'transaction';
			$create_boost['top_message'] = '[name] from [town], [state]';
			$create_boost['message'] = 'Bought [product_with_link]';
			$boost_model->create_boost($create_boost);

			$create_boost['name'] = 'EDD Add to cart';
			$create_boost['subtype'] = 'add_to_cart';
			$create_boost['top_message'] = 'Someone from [town], [state]';
			$create_boost['message'] = 'Added [product_with_link] to cart';
			$boost_model->create_boost($create_boost);
		}

	}

}