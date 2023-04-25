<?php

/**
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Boost
 * @subpackage Boost/includes
 * @author     cristian stoicescu <email@email.email>
 */
class Boost_Notification_Model {

	const IMAGE_TYPE_FAKE_MAP = 'map';
	const IMAGE_TYPE_ICON = 'icon';
	const IMAGE_TYPE_PRODUCT = 'product';

	const IMAGE_TYPES = [
		self::IMAGE_TYPE_FAKE_MAP => self::IMAGE_TYPE_FAKE_MAP,
		self::IMAGE_TYPE_ICON => self::IMAGE_TYPE_ICON,
		self::IMAGE_TYPE_PRODUCT => self::IMAGE_TYPE_PRODUCT
	];

//    /**
//     * The loader that's responsible for maintaining and registering all hooks that power
//     * the plugin.
//     *
//     * @since    1.0.0
//     * @access   protected
//     * @var      Boost_Loader    $loader    Maintains and registers all hooks for the plugin.
//     */
//    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    protected $settings_model;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct($plugin_name, $plugin_version) {

        $this->plugin_name = $plugin_name;
        $this->version = $plugin_version;

        $this->settings_model = new Boost_Settings_Model($plugin_name, $plugin_version);

    }
    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    public function get_all_notifications_icons() {

	    $iconsDir = plugin_dir_path(__FILE__).'imgs/notifications-icons/';
	    if (!$icons = glob($iconsDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE)) {
	    	$icons = array();
	    }
	    foreach ($icons as $key => $icon) {
		    $icons[$key] = str_replace($iconsDir, plugin_dir_url(__FILE__).'imgs/notifications-icons/', $icon);
	    }
	    return $icons;

    }

    public function get_all_notifications_fake_maps() {

	    $fakeMapsDir = plugin_dir_path(__FILE__).'imgs/sample-maps/';
	    if (!$fake_maps = glob($fakeMapsDir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE)) {
		    $fake_maps = array();
	    }
	    foreach ($fake_maps as $key => $fake_map) {
		    $fake_maps[$key] = str_replace($fakeMapsDir, plugin_dir_url(__FILE__).'imgs/sample-maps/', $fake_map);
	    }
	    return $fake_maps;

    }

    public function get_notifications_html($page = 0, $items_on_page = 25, $current_url = '') {
	    global $wp;

	    if (empty($current_url)) {
		    $current_url = rtrim(home_url(add_query_arg(array(),$wp->request)), '/');
	    }
	    $is_mobile = wp_is_mobile() ? true : false;
	    $icons = $this->get_all_notifications_icons();
	    $fake_maps = $this->get_all_notifications_fake_maps();
	    $settings = $this->settings_model->get_settings(array('use-leads-time', 'give-us-credit', 'translations', 'close-boosts'));

	    $notifications = $this->get_notifications($page, $items_on_page, $is_mobile, $current_url);
        $notifications_html = '';

        $additional_classes = array();
        if ($settings['close-boosts']) {
	        $additional_classes[] = 'allow-to-close';
        }

        foreach ($notifications as $notification) {
            $notifications_html .= $this->get_notification_as_html($notification, $additional_classes, $is_mobile, $icons, $settings, $fake_maps);
        }
        return '<div id="boost-notifications-wrapper">' . $notifications_html . '</div>';

    }

    public function get_notifications($page, $items_on_page, $is_mobile, $current_url = '') {

	    $edd_cart_checkout_page_url = function_exists('edd_get_checkout_uri') ? rtrim(edd_get_checkout_uri(array()), '/') : '';
	    $wc_cart_url = function_exists('wc_get_page_id') ? get_permalink( wc_get_page_id( 'cart' ) ) : '';
	    $wc_checkout_url = function_exists('wc_get_page_id') ? get_permalink( wc_get_page_id( 'checkout' ) ) : '';

	    if (!$post_id = get_the_ID()) {
	    	$post_id = url_to_postid($current_url);
	    }
	    $post_type = get_post_type($post_id);

	    $post_taxonomies = get_post_taxonomies($post_id);
	    if (!is_array($post_terms = wp_get_post_terms($post_id, $post_taxonomies,  array("fields" => "ids")))) {
		    $post_terms = array();
	    };
	    if (count($post_taxonomies = array_merge($post_taxonomies, $post_terms)) == 0) {
	    	$post_taxonomies = array('0');
	    };
	    $home_url = home_url('/');
	    $is_home_page = $home_url === $current_url;

	    $is_wc_cart_page = $wc_cart_url === $current_url;
	    $is_wc_checkout_page = $wc_checkout_url === $current_url;

	    $page_details = array(
	    	'current_url' => $current_url,
	    	'post_type' => $post_type,
	    	'post_id' => $post_id,
	    	'post_taxonomies' => $post_taxonomies,
	    	'is_home_page' => $is_home_page,
	    	'is_wc_cart_page' => $is_wc_cart_page,
	    	'is_wc_checkout_page' => $is_wc_checkout_page,
	    	'is_wc_product_page' => 'product' == $post_type ? true : false,
	    	'is_edd_cart_page' => !empty($edd_cart_checkout_page_url) && strpos($current_url, $edd_cart_checkout_page_url) === 0 ? true : false,
	    	'is_edd_checkout_page' => !empty($edd_cart_checkout_page_url) && strpos($current_url, $edd_cart_checkout_page_url) === 0 ? true : false,
	    	'is_edd_download_page' => 'download' == $post_type ? true : false,
	    );

	    $location_optimization_setting = $this->settings_model->get_setting('location-optimization');
	    $action_table_where_clause = '';
	    if ($location_optimization_setting) {
		    $action_model = new Boost_Action_Model($this->get_plugin_name(), $this->get_version());
		    $user_location = $action_model->get_user_location();

		    foreach ($user_location as $key => $value) {
			    if (!empty($value)) {
				    $action_table_where_clause .= (!empty($action_table_where_clause) ? ' OR ' : '') . "`$key` LIKE '$value'";
			    }
		    }
		    if (!empty($action_table_where_clause)){
			    $action_table_where_clause = " AND ($action_table_where_clause)";
		    }
	    }
	    $max_age_notifications_days = (int)$this->settings_model->get_setting('dont-show-notifications-after-days');
	    $max_age_notifications_days_time = strtotime(date('Y-m-d', strtotime("-$max_age_notifications_days days", time())));

	    $action_table_where_clause .= "AND `time` > '$max_age_notifications_days_time' ";

	    $action_table_where_clause .= ' ORDER BY `time` DESC LIMIT ' . ($page * $items_on_page) . ", $items_on_page";

	    $boosts_table_where_clause = ' AND ' . ($is_mobile ? '`mobile`' : '`desktop`') . "='1'";




	    if (!is_array($leads_notifications = $this->get_leads_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause))) {
		    $leads_notifications = array();
	    }
	    if (!is_array($wc_notifications = $this->get_woocommerce_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause, ($page > 0 ? false : true)))) {
		    $wc_notifications = array();
	    }
	    if (!is_array($edd_notifications = $this->get_easydigitaldownloads_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause))) {
		    $edd_notifications = array();
	    }

	    $notifications = array_merge($leads_notifications, $wc_notifications, $edd_notifications);

	    $random_order_setting = $this->settings_model->get_setting('show-boosts-in-random-order');
	    if ($random_order_setting) {
	    	shuffle($notifications);
	    }

	    return $notifications;
    }

    public function get_leads_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause){
        global $wpdb;

        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
	    $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
        $table_actions = $wpdb->prefix . TABLE_ACTIONS;

        $where_clause = "($table_boosts.`display_type` = 'all_pages' OR
                          ($table_boosts.`display_type` = 'capture_url' AND ($table_leads_data.`capture_url` LIKE '{$page_details['current_url']}' OR $table_leads_data.`capture_url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_leads_data.`capture_url`,'/') LIKE '{$page_details['current_url']}')) OR
                          ($table_boosts.`display_type` = 'custom' AND 
                            (   $table_boosts.`dc_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
							 OR $table_boosts.`dc_on_specific_pages` = '1' AND $table_boosts_specific_pages.`post_id` = '{$page_details['post_id']}' 
			                 OR $table_boosts.`dc_on_urls` = '1' AND ($table_boosts_urls.`url_type` LIKE 'equals' AND ($table_boosts_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_urls.`url`) <> '0')
	                         OR $table_boosts.`dc_on_post_types` = '1' AND $table_boosts_post_types.`post_type` = '{$page_details['post_type']}'
	                         OR $table_boosts.`dc_on_taxonomies` = '1' AND $table_boosts_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
		                    )
	                      )
                         )";


        $sql = "SELECT $table_boosts.`id` FROM $table_boosts
				LEFT JOIN $table_leads_data       ON ($table_leads_data.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_urls       ON ($table_boosts_urls.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_post_types ON ($table_boosts_post_types.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_specific_pages ON ($table_boosts_specific_pages.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_taxonomies ON ($table_boosts_taxonomies.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`type` = 'leads' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if(!is_array($boost_ids = $wpdb->get_col($sql))) {
            $boost_ids = array();
        }

	    $where_clause = "(    $table_boosts.`de_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
							OR $table_boosts.`de_on_specific_pages` = '1' AND $table_boosts_exclude_specific_pages.`post_id` = '{$page_details['post_id']}' 
			                OR $table_boosts.`de_on_urls` = '1' AND ($table_boosts_exclude_urls.`url_type` LIKE 'equals' AND ($table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_exclude_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_exclude_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_exclude_urls.`url`) <> '0')
	                        OR $table_boosts.`de_on_post_types` = '1' AND $table_boosts_exclude_post_types.`post_type` = '{$page_details['post_type']}'	                        
	                        OR $table_boosts.`de_on_taxonomies` = '1' AND $table_boosts_exclude_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
                         )";

        $sql = "SELECT `id` FROM $table_boosts
				LEFT JOIN $table_boosts_exclude_urls ON ($table_boosts_exclude_urls.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_exclude_post_types ON ($table_boosts_exclude_post_types.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_specific_pages ON ($table_boosts_exclude_specific_pages.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_taxonomies ON ($table_boosts_exclude_taxonomies.`boost_id` = $table_boosts.`id`)				
				WHERE $table_boosts.`type` = 'leads' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if(!is_array($exclude_boost_ids = $wpdb->get_col($sql))) {
            $exclude_boost_ids = array();
        }

        $boost_ids = array_diff($boost_ids, $exclude_boost_ids);

        if (count($boost_ids) > 0) {
	        $sql = "SELECT * FROM $table_actions
					LEFT JOIN $table_boosts ON ($table_boosts.`id` = $table_actions.`boost_id`)
					WHERE `boost_id` IN ('" . ( implode( "','", $boost_ids ) ) . "') $action_table_where_clause;";

	        if ( ! is_array( $notifications = $wpdb->get_results( $sql, ARRAY_A ) ) ) {
		        $notifications = array();
	        }

	        foreach ( $notifications as $index => $notification ) {
		        $replace_what = array(
			        '[name]',
			        '[time]',
			        '[town]',
			        '[state]',
			        '[country]',
		        );
		        $replace_for = array(
			        $notification['user_name'],
			        date( '%Y-%M-%d', $notification['time'] ),
			        $notification['town'],
			        $notification['state'],
			        $notification['country'],
		        );
		        $notifications[ $index ]['top_message'] = str_replace($replace_what, $replace_for, $notifications[ $index ]['top_message'] );
		        $notifications[ $index ]['message']     = str_replace( $replace_what, $replace_for, $notifications[ $index ]['message'] );
	        }

	        return $notifications;
        }
        return array();
    }

    public function get_woocommerce_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause, $stock_messages = true)
    {
	    if ( !class_exists( 'WooCommerce' ) ) return array();
        global $wpdb;

        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
	    $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
	    $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
        $table_actions = $wpdb->prefix . TABLE_ACTIONS;

        $product_id = 0;
        $product_cats = array();
        $product_stock = NULL;
        $is_wc_category_product_page = false;
        if ($page_details['post_type'] == 'product' && $page_details['is_wc_product_page'] && function_exists('wc_get_product')) {
            $product_id = $page_details['post_id'];
            $product = wc_get_product($product_id);
            if (!empty($product)) {
                $product_cats = $product->get_category_ids();
                if (count($product_cats) > 0) {
	                $is_wc_category_product_page = true;
                }
                if ($product->get_manage_stock()) {
                    $product_stock = $product->get_stock_quantity();
                }
            }
        }

	    $where_clause = "($table_boosts.`display_type` = 'all_pages' 
                             OR
	                         ($table_woocommerce_data.`subtype` IN ('transaction','specific_transaction') AND
	                            (
		                            ($table_boosts.`display_type` = 'custom' AND 
			                            (   $table_boosts.`dc_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
										 OR $table_boosts.`dc_on_specific_pages` = '1' AND $table_boosts_specific_pages.`post_id` = '{$page_details['post_id']}' 
		                                 OR $table_boosts.`dc_on_urls` = '1' AND ($table_boosts_urls.`url_type` LIKE 'equals' AND ($table_boosts_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_urls.`url`) <> '0')
				                         OR $table_boosts.`dc_on_post_types` = '1' AND $table_boosts_post_types.`post_type` = '{$page_details['post_type']}'
				                         OR $table_boosts.`dc_on_taxonomies` = '1' AND $table_boosts_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
					                    )
		                            ) 
		                            OR
		                            ($table_boosts.`display_type` = 'fast' AND
		                                (   $table_woocommerce_data.`df_on_home_page` = 1 AND '{$page_details['is_home_page']}' = true
		                                 OR $table_woocommerce_data.`df_on_all_products` = 1 AND '{$page_details['is_wc_product_page']}' = true AND '{$page_details['post_type']}' = 'product'
		                                 OR $table_woocommerce_data.`df_on_all_categories` = 1 AND '$is_wc_category_product_page' = true
		                                 OR $table_woocommerce_data.`df_on_cart_page` = 1 AND '{$page_details['is_wc_cart_page']}' = true
		                                 OR $table_woocommerce_data.`df_on_checkout_page` = 1 AND '{$page_details['is_wc_checkout_page']}' = true
		                                 OR $table_woocommerce_data.`df_on_all_purchased_products` = 1 AND $table_woocommerce_data.`subtype` LIKE 'specific_transaction' AND $table_woocommerce_products.`product_id` = '$product_id'
		                                 OR $table_woocommerce_data.`df_on_all_purchased_categories` = 1 AND $table_woocommerce_data.`subtype` LIKE 'specific_transaction' AND $table_woocommerce_categories.`category_id` IN ('" . implode("','", $product_cats) . "')
		                                )		                            
		                            )
	                            )
	                         ) 
	                         OR
	                         ($table_woocommerce_data.`subtype` LIKE 'add_to_cart' AND
	                            (
	                                ($table_boosts.`display_type` = 'all_products' AND '{$page_details['is_wc_product_page']}' = true AND '{$page_details['post_type']}' = 'product') 
	                                OR 
	                                ($table_boosts.`display_type` = 'all_pages') 
	                                OR 
	                                ($table_boosts.`display_type` = 'specific_products' AND $table_boosts_products.`product_id` = '$product_id')
	                            )
	                         )
                         )";

        $sql = "SELECT $table_boosts.`id` FROM $table_boosts
				LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_woocommerce_products ON ($table_woocommerce_products.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_woocommerce_categories ON ($table_woocommerce_categories.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_urls ON ($table_boosts_urls.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_post_types ON ($table_boosts_post_types.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_products ON ($table_boosts_products.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_specific_pages ON ($table_boosts_specific_pages.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_taxonomies ON ($table_boosts_taxonomies.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`type` LIKE 'woocommerce' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if (!is_array($boost_ids = $wpdb->get_col($sql))) {
            $boost_ids = array();
        }

	    $where_clause = "$table_woocommerce_data.`subtype` IN ('transaction','specific_transaction') AND
                         (     $table_boosts.`de_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
							OR $table_boosts.`de_on_specific_pages` = '1' AND $table_boosts_exclude_specific_pages.`post_id` = '{$page_details['post_id']}' 
			                OR $table_boosts.`de_on_urls` = '1' AND ($table_boosts_exclude_urls.`url_type` LIKE 'equals' AND ($table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_exclude_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_exclude_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_exclude_urls.`url`) <> '0')
	                        OR $table_boosts.`de_on_post_types` = '1' AND $table_boosts_exclude_post_types.`post_type` = '{$page_details['post_type']}'	                        
	                        OR $table_boosts.`de_on_taxonomies` = '1' AND $table_boosts_exclude_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
                         )";

        $sql = "SELECT $table_boosts.`id` FROM $table_boosts
				LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_exclude_urls ON ($table_boosts_exclude_urls.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_exclude_post_types ON ($table_boosts_exclude_post_types.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_specific_pages ON ($table_boosts_exclude_specific_pages.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_taxonomies ON ($table_boosts_exclude_taxonomies.`boost_id` = $table_boosts.`id`)				
				WHERE $table_boosts.`type` LIKE 'woocommerce' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if (!is_array($exclude_boost_ids = $wpdb->get_col($sql))) {
            $exclude_boost_ids = array();
        }

        $boost_ids = array_diff($boost_ids, $exclude_boost_ids);

	    $notifications = array();
        if (count($boost_ids) > 0) {
	        $sql = "SELECT * 
 					FROM $table_actions
					LEFT JOIN $table_boosts ON ($table_boosts.`id` = $table_actions.`boost_id`)
					LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_actions.`boost_id`)
					WHERE $table_actions.`boost_id` IN ('" . ( implode( "','", $boost_ids ) ) . "') $action_table_where_clause;";

	        if ( ! is_array( $notifications = $wpdb->get_results( $sql, ARRAY_A ) ) ) {
		        $notifications = array();
	        }
        }

        if ($stock_messages && !empty($product_id) && !empty($product_stock)) {
            $sql = "SELECT *, 
						$product_id as 'product_id', '' as 'user_name', '' as 'town', '' as 'state', '' as 'country', '0' as 'time'
					FROM $table_boosts
					LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`)
					LEFT JOIN $table_boosts_products ON ($table_boosts_products.`boost_id` = $table_boosts.`id`)
					WHERE $table_boosts.`type` LIKE 'woocommerce' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
					$table_woocommerce_data.`subtype` LIKE 'stock_messages' AND $table_woocommerce_data.`stock_number` >= '$product_stock' AND
                    (($table_boosts.`display_type` = 'all_products' AND '{$page_details['is_wc_product_page']}' = true) 
                    OR
                    ($table_boosts.`display_type` = 'specific_products' AND $table_boosts_products.`product_id` = '$product_id')) $boosts_table_where_clause;";

            if (($wc_stock_boosts = $wpdb->get_results($sql, ARRAY_A)) != NULL) {
                $notifications = array_merge($notifications, $wc_stock_boosts);
            }
        }

        if (!empty($notifications)) {
	        $replace_what = array(
		        '[name]',
		        '[time]',
		        '[town]',
		        '[state]',
		        '[country]',
		        '[product_name]',
		        '[product_with_link]',
		        '[stock]'
	        );
	        foreach ( $notifications as $index => $notification ) {
		        $product_name  = '';
		        $product_stock = '';
		        $product_link = '';
		        if ( ! empty( $notification['product_id'] ) && function_exists( 'wc_get_product' ) ) {
			        $product = wc_get_product( $notification['product_id'] );
		        }
		        elseif(!empty($notification['order_id']) && function_exists('wc_get_order') && function_exists('wc_get_product')) {
			        $order = wc_get_order($notification['order_id']);
			        if (!empty($order) && is_array(@$order_items = $order->get_items())) {
				        foreach ($order_items as $item) {
					        $product_id = $item['product_id'];
					        $product = wc_get_product($product_id);
					        if (!empty($product) && wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'single-post-thumbnail' )) {
						        break;
					        }
				        }
			        }
		        }
		        if ( ! empty( $product ) ) {
			        $product_name  = $product->get_name();
			        $product_stock = $product->get_stock_quantity();
			        $product_link = get_permalink($product->get_id());
			        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'single-post-thumbnail' );
			        $notifications[ $index ]['image'] = '<a href="'.$product_link.'" target="_blank"></a>';
			        $notifications[ $index ]['image_url'] = !empty($image[0])?$image[0]:'';
		        }

		        $replace_for =
			        array(
				        $notification['user_name'],
				        date( '%Y-%M-%d', $notification['time'] ),
				        $notification['town'],
				        $notification['state'],
				        $notification['country'],
				        '<span class="boost-notification-product-name">'.$product_name.'</span>',
				        '<a class="boost-notification-product-name" href="'.$product_link.'" target="_blank">'.$product_name.'</a>',
				        $product_stock
			        );

		        $notifications[ $index ]['top_message'] = str_replace( $replace_what, $replace_for, $notifications[ $index ]['top_message'] );
		        $notifications[ $index ]['message']     = str_replace( $replace_what, $replace_for, $notifications[ $index ]['message'] );
	        }

	        return $notifications;
        }
        return array();
    }

    public function get_easydigitaldownloads_notifications($page_details, $action_table_where_clause, $boosts_table_where_clause)
    {
	    if ( !class_exists( 'Easy_Digital_Downloads') ) return array();
        global $wpdb;

        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
	    $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
	    $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
        $table_actions = $wpdb->prefix . TABLE_ACTIONS;
        
        
        $download_id = 0;
        $download_cats = array();
	    $is_edd_category_download_page = false;
        if ($page_details['post_type'] == 'download') {
	        $download_id = $page_details['post_id'];
	        if ( ! is_array( $download_cats = wp_get_post_terms( $download_id, 'download_category', array( 'fields' => 'ids' ) ) ) ) {
		        $download_cats = array();
	        };
	        if ( count( $download_cats ) > 0 ) {
		        $is_edd_category_download_page = true;
	        }
        }

	    $where_clause = "($table_boosts.`display_type` = 'all_pages' 
                             OR
	                         ($table_easydigitaldownloads_data.`subtype` IN ('transaction','specific_transaction') AND
	                            (
		                            ($table_boosts.`display_type` = 'custom' AND 
			                            (   $table_boosts.`dc_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
										 OR $table_boosts.`dc_on_specific_pages` = '1' AND $table_boosts_specific_pages.`post_id` = '{$page_details['post_id']}' 
			                             OR $table_boosts.`dc_on_urls` = '1' AND ($table_boosts_urls.`url_type` LIKE 'equals' AND ($table_boosts_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_urls.`url`) <> '0')
				                         OR $table_boosts.`dc_on_post_types` = '1' AND $table_boosts_post_types.`post_type` = '{$page_details['post_type']}'
				                         OR $table_boosts.`dc_on_taxonomies` = '1' AND $table_boosts_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
					                    )
		                            ) 
		                            OR
		                            ($table_boosts.`display_type` = 'fast' AND
		                                (   $table_easydigitaldownloads_data.`df_on_home_page` = 1 AND '{$page_details['is_home_page']}' = true
		                                 OR $table_easydigitaldownloads_data.`df_on_all_products` = 1 AND '{$page_details['is_edd_download_page']}' = true AND '{$page_details['post_type']}' = 'download'
		                                 OR $table_easydigitaldownloads_data.`df_on_all_categories` = 1 AND '$is_edd_category_download_page' = true
		                                 OR $table_easydigitaldownloads_data.`df_on_cart_page` = 1 AND '{$page_details['is_edd_cart_page']}' = true
		                                 OR $table_easydigitaldownloads_data.`df_on_checkout_page` = 1 AND '{$page_details['is_edd_checkout_page']}' = true
		                                 OR $table_easydigitaldownloads_data.`df_on_all_purchased_products` = 1 AND $table_easydigitaldownloads_data.`subtype` LIKE 'specific_transaction' AND $table_easydigitaldownloads_products.`product_id` = '$download_id'
		                                 OR $table_easydigitaldownloads_data.`df_on_all_purchased_categories` = 1 AND $table_easydigitaldownloads_data.`subtype` LIKE 'specific_transaction' AND $table_easydigitaldownloads_categories.`category_id` IN ('" . implode("','", $download_cats) . "')
		                                )		                            
		                            )
	                            )
	                         ) 
	                         OR
	                         ($table_easydigitaldownloads_data.`subtype` LIKE 'add_to_cart' AND
	                            (
	                                ($table_boosts.`display_type` = 'all_products' AND '{$page_details['is_edd_download_page']}' = true AND '{$page_details['post_type']}' = 'download') 
	                                OR 
	                                ($table_boosts.`display_type` = 'all_pages') 
	                                OR 
	                                ($table_boosts.`display_type` = 'specific_products' AND $table_boosts_products.`product_id` = '$download_id')
	                            )
	                         )
                         )";

        $sql = "SELECT $table_boosts.`id` FROM $table_boosts
				LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_easydigitaldownloads_products ON ($table_easydigitaldownloads_products.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_easydigitaldownloads_categories ON ($table_easydigitaldownloads_categories.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_urls ON ($table_boosts_urls.`boost_id`= $table_boosts.`id`)
				LEFT JOIN $table_boosts_post_types ON ($table_boosts_post_types.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_products ON ($table_boosts_products.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_specific_pages ON ($table_boosts_specific_pages.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_taxonomies ON ($table_boosts_taxonomies.`boost_id` = $table_boosts.`id`)
				WHERE $table_boosts.`type` LIKE 'easydigitaldownloads' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if (!is_array($boost_ids = $wpdb->get_col($sql))) {
            $boost_ids = array();
        }

	    $where_clause = "$table_easydigitaldownloads_data.`subtype` IN ('transaction','specific_transaction') AND
                         (     $table_boosts.`de_on_home_page` = '1' AND '{$page_details['is_home_page']}' = true  		
							OR $table_boosts.`de_on_specific_pages` = '1' AND $table_boosts_exclude_specific_pages.`post_id` = '{$page_details['post_id']}' 
			                OR $table_boosts.`de_on_urls` = '1' AND ($table_boosts_exclude_urls.`url_type` LIKE 'equals' AND ($table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}' OR $table_boosts_exclude_urls.`url` LIKE '{$page_details['current_url']}/' OR CONCAT($table_boosts_exclude_urls.`url`,'/') LIKE '{$page_details['current_url']}') OR $table_boosts_exclude_urls.`url_type` LIKE 'contains' AND INSTR('{$page_details['current_url']}', $table_boosts_exclude_urls.`url`) <> '0')
	                        OR $table_boosts.`de_on_post_types` = '1' AND $table_boosts_exclude_post_types.`post_type` = '{$page_details['post_type']}'	                        
	                        OR $table_boosts.`de_on_taxonomies` = '1' AND $table_boosts_exclude_taxonomies.`taxonomy_id` IN ('" . implode("','", $page_details['post_taxonomies']) . "')
                         )";
        $sql = "SELECT $table_boosts.`id` FROM $table_boosts
				LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_exclude_urls ON ($table_boosts_exclude_urls.`boost_id` = $table_boosts.`id`)
				LEFT JOIN $table_boosts_exclude_post_types ON ($table_boosts_exclude_post_types.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_specific_pages ON ($table_boosts_exclude_specific_pages.`boost_id` = $table_boosts.`id`)				
				LEFT JOIN $table_boosts_exclude_taxonomies ON ($table_boosts_exclude_taxonomies.`boost_id` = $table_boosts.`id`)				
				WHERE $table_boosts.`type` LIKE 'easydigitaldownloads' AND $table_boosts.`active` = '1' AND $table_boosts.`draft` <> '1' AND 
				$where_clause $boosts_table_where_clause;";

        if (!is_array($exclude_boost_ids = $wpdb->get_col($sql))) {
            $exclude_boost_ids = array();
        }

        $boost_ids = array_diff($boost_ids, $exclude_boost_ids);

        if (count($boost_ids) > 0) {
	        $sql = "SELECT * 
 					FROM $table_actions
					LEFT JOIN $table_boosts ON ($table_boosts.`id` = $table_actions.`boost_id`)
					LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_actions.`boost_id`)
					WHERE $table_actions.`boost_id` IN ('" . ( implode( "','", $boost_ids ) ) . "') $action_table_where_clause;";

	        if ( ! is_array( $notifications = $wpdb->get_results( $sql, ARRAY_A ) ) ) {
		        $notifications = array();
	        }

	        $replace_what = array(
		        '[name]',
		        '[time]',
		        '[town]',
		        '[state]',
		        '[country]',
		        '[product_name]',
		        '[product_with_link]'
	        );
	        foreach ( $notifications as $index => $notification ) {
		        if ( ! isset( $notification['user_name'] ) ) {
			        continue;
		        }
		        $download_name = '';
		        $download_link = '';

		        if ( ! empty( $notification['product_id'] ) && function_exists( 'edd_get_download' ) ) {
			        $download = edd_get_download( $notification['product_id'] );
		        }
		        elseif(!empty($notification['order_id']) && function_exists('edd_get_payment') && function_exists('edd_get_download')) {
			        $payment = edd_get_payment($notification['order_id']);
			        if (!empty($payment) && is_array(@$payment_downloads = $payment->__get('downloads'))) {
				        foreach ($payment_downloads as $payment_download) {
				        	$download_id = $payment_download['id'];
					        $download = edd_get_download( $download_id );
					        if (!empty($download) && get_the_post_thumbnail($download_id)) {
						        break;
					        }
				        }
			        }
		        }
		        if ( ! empty( $download ) ) {
			        $download_name = ! empty( $download->post_title ) ? $download->post_title : '';
			        $download_link = get_permalink($download->ID);
			        $image = wp_get_attachment_image_src( get_post_thumbnail_id( $download->ID ), 'single-post-thumbnail' );
			        $notifications[ $index ]['image'] = '<a href="'.$download_link.'" target="_blank"></a>';
			        $notifications[ $index ]['image_url'] = !empty($image[0])?$image[0]:'';
		        }

		        $replace_for =
			        array(
				        $notification['user_name'],
				        date( '%Y-%M-%d', $notification['time'] ),
				        $notification['town'],
				        $notification['state'],
				        $notification['country'],
				        '<span class="boost-notification-product-name">'.$download_name.'</span>',
				        '<a class="boost-notification-product-name" href="'.$download_link.'" target="_blank">'.$download_name.'</a>'
			        );

		        $notifications[ $index ]['top_message'] = str_replace( $replace_what, $replace_for, $notifications[ $index ]['top_message'] );
		        $notifications[ $index ]['message']     = str_replace( $replace_what, $replace_for, $notifications[ $index ]['message'] );
	        }

	        return $notifications;
        }
        return array();
    }

    public function get_notification_as_html($notification, $additional_classes = array(), $is_mobile = false, $icons = array(), $settings = array(), $fake_maps = array()) {
        if (!isset($notification['type'])) {
            return '';
        }
        if (empty($settings)) {
	        $settings = $this->settings_model->get_settings(array('use-leads-time', 'give-us-credit', 'translations'));
        }
        $translations = $settings['translations'];

        $notification_styles = array();
        for ($i = 0; $i < 6; $i++) {
        	if (!empty($notification[($is_mobile ? 'mobile' : 'desktop').'_notification_style_' . ($i + 1)])) {
        		$notification_styles[] = $i + 1;
	        }
        }
        $notification_style = count($notification_styles) > 0 ? 'style_' . $notification_styles[rand(0, count($notification_styles) - 1)] : '';

	    $notification_classes = array(
		    'boost-notification type_'.$notification['type'],
		    $notification_style
	    );
	    if (!empty($notification['subtype'])) {
	    	$notification_classes[] = 'subtype_'.$notification['subtype'];
	    }
        if ($is_mobile) {
	        $notification_classes[] = 'boost-notification-square';
	        $notification_classes[] = 'boost-notification-mobile';
	        if (isset($notification['mobile']) && !empty($notification['mobile_position'])) {
		        $notification_classes[] = $notification['mobile'] == 0 ? 'mobile_hide' : 'mobile_' . $notification['mobile_position'];
	        }
        }
        else {
	        $notification_classes[] = 'boost-notification-desktop';
	        if (!empty($notification['notification_template'])) {
	        	$notification_classes[] = 'boost-notification-' . $notification['notification_template'];
	        }
	        if (isset($notification['desktop']) && !empty($notification['desktop_position'])) {
		        $notification_classes[] = $notification['desktop'] == 0 ? 'desktop_hide' : 'desktop_' . $notification['desktop_position'];
	        }
        }
	    $notification_classes = array_merge($notification_classes, $additional_classes);
        $notification_classes_string = implode(' ', $notification_classes);

	    $notification_time_part= '';
        if ((empty($notification['subtype']) || !in_array($notification['subtype'], array('stock_messages')))) {
	        if ( ! empty( $notification['time'] ) && ( time() - (int) $notification['time'] ) < (int) $settings['use-leads-time'] * 60 * 60) {
		        $notification_time_part = $this->elapsed_time( $notification['time'], 2, $translations );
	        } else {
		        $notification_time_part = !empty($translations['recently']) ? $translations['recently'] : __( 'Recently', 'boost' );
	        }
        }
	    $notification_credit_part = $settings['give-us-credit'] ? '<div class="boost-notification-credit-top-text">' . (!empty($translations['verified_by']) ? $translations['verified_by'] : __('Verified by', 'boost')) . ':&nbsp;</div>' . '<div class="boost-notification-credit-text-bottom"><a href="http://www.boostplugin.com" target="_blank">'.__('boost', 'boost').'<span class="boost-notification-credit-img"><img class="boost-notification-message-info-img" src="' . plugin_dir_url( __FILE__ ) . '../includes/imgs/boost-icon.png' . '"></span></a></div>' : '';

        $notification_top_message = !empty($notification['top_message']) ? $notification['top_message'] : '';
        $notification_message = !empty($notification['message']) ? $notification['message'] : '';

        $notification_attributes = '';

        if(isset($notification['id'])) {
            $notification_attributes .= ' id="boost-notification-' . $notification['id'] . '"';
        }

        if(in_array($notification['type'], array('woocommerce', 'easydigitaldownloads')) && !empty($notification['order_id'])){
            $notification_attributes .= ' data-order-id="' . $notification['order_id'] . '"';
        }

        $possible_images = array();
        $image = '';

        if (in_array($notification['type'], array('woocommerce', 'easydigitaldownloads')) && !empty($notification['use_products_images'])) {
        	if (!empty($notification['image'])) {
		        $possible_images[] = array(
		        	'html' => $notification['image'],
		        	'type' => self::IMAGE_TYPE_PRODUCT
		        );
	        }
        }
//        if (!empty($notification['town']) && $notification['town'] !== 'Town'
//             && !empty($notification['country']) && $notification['country'] !== 'Country') {
//	        $map_classes = 'google-map';
//	        $search = $notification['town'] . ',' . $notification['country'];
//	        $imageUrl = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $search . '&zoom=8&scale=1&size=200x200&maptype=roadmap&format=png&visual_refresh=true&key=';
//	        $possible_images[] = '<img class="' . $map_classes . '" src="' . $imageUrl . '" />';
//        }
	    if (!empty($fake_maps) && is_array($fake_maps) && !empty($notification['use_maps_images'])) {
		    $randomImage = $fake_maps[array_rand($fake_maps)];
		    $html = '<img src="' . $randomImage . '" />';
		    if (!empty($notification['town']) && $notification['town'] !== 'Town') {
			    $html .= '<div class="fake-map-text-box"><div class="fake-map-text">'.$notification['town'].'</div></div>';
		    }
		    $possible_images[] = array(
			    'html' => $html,
			    'type' => self::IMAGE_TYPE_FAKE_MAP
		    );
	    }
        if (!empty($icons) && is_array($icons) && !empty($notification['use_icons_images'])) {
	        $randomImage = $icons[array_rand($icons)];
	        $possible_images[] = array(
		        'html' => '<img src="' . $randomImage . '" />',
		        'type' => self::IMAGE_TYPE_ICON
	        );
        }

        if (!empty($possible_images)) {
        	$image = $possible_images[array_rand($possible_images)];
        }

        $notification_html = '
			<div class="' . $notification_classes_string . '"' . $notification_attributes . '>
				<div class="boost-notification-map" '.(!empty($notification['image_url'])?'style="background-image: url(\''.$notification['image_url'].'\');"':'').'>' . (!empty($image['html'])?$image['html']:'') . '</div>
				<div class="boost-notification-message">
					<div class="boost-notification-message-top">' . $notification_top_message . '</div>
					<div class="boost-notification-message-bottom">' . $notification_message . '</div>
					<div class="boost-notification-message-info"><div class="boost-notification-time">' . $notification_time_part . '</div><div class="boost-notification-credit">' . $notification_credit_part . '</div></div>
				</div>
				<span class="boost-close-button">X</span>
			</div>
		';

        return $notification_html;
    }

    public function elapsed_time($timestamp, $precision = 2, $translations) {
        $result = '';
        $time = time() - $timestamp;
        $a = array('decade' => 315576000, 'year' => 31557600, 'month' => 2629800, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'min' => 60, 'sec' => 1);
        $i = 0;
        foreach($a as $k => $v) {
            $$k = floor($time/$v);
            if ($$k) $i++;
            $time = $i >= $precision ? 0 : $time - $$k * $v;
            $s = $$k > 1 ? 's' : '';
            $$k = $$k ? $$k.' '.(!empty($translations[$k.$s])?$translations[$k.$s]:$k.$s).' ' : '';
            @$result .= $$k;
        }
        return $result ? $result.(!empty($translations['ago'])?$translations['ago']:'ago') : '';
    }
}
