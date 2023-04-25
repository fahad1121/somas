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
class Boost_Boost_Model {

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

    private $default_messages;

    private $trigger_types;

    private $desktop_positions;

    private $mobile_positions;

    private $default_top_messages;

    private $display_exclude_url_types;

    private $action_model;

    private $settings_model;

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

        $this->trigger_types = array(
            'leads' => array(
                'name' => __('Leads', 'boost'),
                'subtypes' => array()
            ),
            'woocommerce' => array(
                'name' => __('WooCommerce', 'boost'),
                'subtypes' => array(
                    'transaction' => __('Transaction', 'boost'),
                    'specific_transaction' => __('Specific product or category transaction', 'boost'),
                    'stock_messages' => __('Stock messages', 'boost'),
                    'add_to_cart' => __('Add to cart clicks', 'boost')
                )
            ),
            'easydigitaldownloads' => array(
                'name' => __('EasyDigitalDownloads', 'boost'),
                'subtypes' => array(
                    'transaction' => __('Transaction', 'boost'),
                    'specific_transaction' => __('Specific product or category transaction', 'boost'),
                    'add_to_cart' => __('Add to cart clicks', 'boost')
                )
            )
        );

        $this->default_messages = array(
            'leads' => __('Just bought our product', 'boost'),
            'woocommercetransaction' => __('Bought [product_with_link]', 'boost'),
            'woocommercespecific_transaction' => __('Just bought a [product_with_link]', 'boost'),
            'woocommercestock_messages' => __('[product_name]', 'boost'),
            'woocommerceadd_to_cart' => __('Added [product_with_link] to cart', 'boost'),
            'easydigitaldownloadstransaction' => __('Bought [product_with_link]', 'boost'),
            'easydigitaldownloadsspecific_transaction' => __('Just bought a [product_with_link]', 'boost'),
            'easydigitaldownloadsadd_to_cart' => __('Added [product_with_link] to cart', 'boost')
        );

        $this->default_top_messages = array(
            'leads' => '[name] from [town], [state]',
            'woocommercetransaction' => '[name] from [town], [state]',
            'woocommercespecific_transaction' => '[name] from [town], [state]',
            'woocommercestock_messages' => 'Don\'t miss it! Only [stock] left in stock',
            'woocommerceadd_to_cart' => 'Someone from [town], [state]',
            'easydigitaldownloadstransaction' => '[name] from [town], [state]',
            'easydigitaldownloadsspecific_transaction' => '[name] from [town], [state]',
            'easydigitaldownloadsadd_to_cart' => 'Someone from [town], [state]'
        );

        $this->default_display_types = array(
            'leads' => 'capture_url',
            'woocommercetransaction' => 'fast',
            'woocommercespecific_transaction' => 'fast',
            'woocommercestock_messages' => 'all_products',
            'woocommerceadd_to_cart' => 'all_products',
            'easydigitaldownloadstransaction' => 'fast',
            'easydigitaldownloadsspecific_transaction' => 'fast',
            'easydigitaldownloadsadd_to_cart' => 'all_products'
        );

        $this->desktop_positions = array(
            'bottom_left' => __('Bottom Left', 'boost'),
            'bottom_right' => __('Bottom Right', 'boost'),
            'bottom_middle' => __('Bottom Middle', 'boost'),
            'top_left' => __('Top Left', 'boost'),
            'top_right' => __('Top Right', 'boost')
        );

        $this->mobile_positions = array(
            'bottom' => __('Bottom', 'boost'),
            'top' => __('Top', 'boost')
        );

        $this->display_exclude_url_types = array(
            'contains' => __('Contains', 'boost'),
            'equal' => __('Equal', 'boost')
        );

        $this->action_model = new Boost_Action_Model($plugin_name, $plugin_version);

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

    /**
     * @return array
     */
    public function get_default_messages()
    {
        return $this->default_messages;
    }

    /**
     * @return array
     */
    public function get_trigger_types()
    {
        return $this->trigger_types;
    }

    /**
     * @return array
     */
    public function get_desktop_positions()
    {
        return $this->desktop_positions;
    }

    /**
     * @return array
     */
    public function get_mobile_positions()
    {
        return $this->mobile_positions;
    }

    /**
     * @return array
     */
    public function get_default_top_messages()
    {
        return $this->default_top_messages;
    }

    /**
     * @return array
     */
    public function get_display_exclude_url_types()
    {
        return $this->display_exclude_url_types;
    }

    public function create_boost($data = array()){
        $boost_type = isset($data['type']) && in_array($data['type'], array_keys($this->trigger_types)) ? $data['type'] : false;
        $boost_sub_type = isset($data['subtype']) ? $data['subtype'] : '';
        if (!$boost_type) {
            return false;
        }

        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
        $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
        $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;
        
        $action_model = new Boost_Action_Model($this->get_plugin_name(), $this->get_version());
        $time = time();

        $create_boost = array(
            'name' => isset($data['name']) ? $data['name'] : '',
            'type' => $boost_type,
            'desktop' => isset($data['desktop']) ? $data['desktop'] : 1,
            'desktop_position' => isset($data['desktop_position']) ? $data['desktop_position'] : 'bottom_left',
            'mobile' => isset($data['mobile']) ? $data['mobile'] : 1,
            'mobile_position' => isset($data['mobile_position']) ? $data['mobile_position'] : 'bottom',
            'active' => isset($data['active']) ? $data['active'] : 0,
            'top_message' => isset($data['top_message']) ? $data['top_message'] : (isset($this->default_top_messages["$boost_type$boost_sub_type"]) ? $this->default_top_messages["$boost_type$boost_sub_type"] : ''),
            'message' => isset($data['message']) ? $data['message'] : (isset($this->default_messages["$boost_type$boost_sub_type"]) ? $this->default_messages["$boost_type$boost_sub_type"] : ''),
            'display_type' => isset($data['display_type']) ? $data['display_type'] : (isset($this->default_display_types["$boost_type$boost_sub_type"]) ? $this->default_display_types["$boost_type$boost_sub_type"] : ''),
            'dc_on_home_page' => isset($data['dc_on_home_page']) ? $data['dc_on_home_page'] : 0,
            'dc_on_urls' => isset($data['dc_on_urls']) ? $data['dc_on_urls'] : 0,
            'dc_on_specific_pages' => isset($data['dc_on_specific_pages']) ? $data['dc_on_specific_pages'] : 0,
            'dc_on_post_types' => isset($data['dc_on_post_types']) ? $data['dc_on_post_types'] : 0,
            'dc_on_taxonomies' => isset($data['dc_on_taxonomies']) ? $data['dc_on_taxonomies'] : 0,
            'de_on_home_page' => isset($data['de_on_home_page']) ? $data['de_on_home_page'] : 0,
            'de_on_urls' => isset($data['de_on_urls']) ? $data['de_on_urls'] : 0,
            'de_on_specific_pages' => isset($data['de_on_specific_pages']) ? $data['de_on_specific_pages'] : 0,
            'de_on_post_types' => isset($data['de_on_post_types']) ? $data['de_on_post_types'] : 0,
            'de_on_taxonomies' => isset($data['de_on_taxonomies']) ? $data['de_on_taxonomies'] : 0,
            'draft' => isset($data['draft']) ? $data['draft'] : 1,
            'notification_template' => isset($data['notification_template']) ? $data['notification_template'] : 'round',
            'enable_fake' => isset($data['enable_fake']) ? $data['enable_fake'] : 0,
            'min_actions_limit' => isset($data['min_actions_limit']) ? $data['min_actions_limit'] : 0,
            'desktop_notification_style_1' => isset($data['desktop_notification_style_1']) ? $data['desktop_notification_style_1'] : 1,
            'desktop_notification_style_2' => isset($data['desktop_notification_style_2']) ? $data['desktop_notification_style_2'] : 0,
            'desktop_notification_style_3' => isset($data['desktop_notification_style_3']) ? $data['desktop_notification_style_3'] : 0,
            'desktop_notification_style_4' => isset($data['desktop_notification_style_4']) ? $data['desktop_notification_style_4'] : 0,
            'desktop_notification_style_5' => isset($data['desktop_notification_style_5']) ? $data['desktop_notification_style_5'] : 0,
            'desktop_notification_style_6' => isset($data['desktop_notification_style_6']) ? $data['desktop_notification_style_6'] : 0,
            'mobile_notification_style_1' => isset($data['mobile_notification_style_1']) ? $data['mobile_notification_style_1'] : 1,
            'mobile_notification_style_2' => isset($data['mobile_notification_style_2']) ? $data['mobile_notification_style_2'] : 0,
            'mobile_notification_style_3' => isset($data['mobile_notification_style_3']) ? $data['mobile_notification_style_3'] : 0,
            'mobile_notification_style_4' => isset($data['mobile_notification_style_4']) ? $data['mobile_notification_style_4'] : 0,
            'mobile_notification_style_5' => isset($data['mobile_notification_style_5']) ? $data['mobile_notification_style_5'] : 0,
            'mobile_notification_style_6' => isset($data['mobile_notification_style_6']) ? $data['mobile_notification_style_6'] : 0,
            'use_products_images' => isset($data['use_products_images']) ? $data['use_products_images'] : 1,
            'use_maps_images' => isset($data['use_maps_images']) ? $data['use_maps_images'] : 1,
            'use_icons_images' => isset($data['use_icons_images']) ? $data['use_icons_images'] : 1,
        );

        if ($wpdb->insert($table_boosts, $create_boost) == false) {
            return false;
        }

        $boost_id = $wpdb->insert_id;

	    $this->update_boost_countries_for_fake((isset($data['countries_for_fakes']) ? $data['countries_for_fakes'] : null), $boost_id, $table_boosts_fake_countries);

	    $this->update_boost_post_types((isset($data['dc_post_types']) ? $data['dc_post_types'] : null), $boost_id, $table_boosts_post_types);
	    $this->update_boost_post_types((isset($data['de_post_types']) ? $data['de_post_types'] : null), $boost_id, $table_boosts_exclude_post_types);

	    $this->update_boost_urls((isset($data['dc_urls']) ? $data['dc_urls'] : null), $boost_id, $table_boosts_urls);
	    $this->update_boost_urls((isset($data['de_urls']) ? $data['de_urls'] : null), $boost_id, $table_boosts_exclude_urls);

	    $this->update_boost_specific_pages((isset($data['dc_specific_pages']) ? $data['dc_specific_pages'] : null), $boost_id, $table_boosts_specific_pages);
	    $this->update_boost_specific_pages((isset($data['de_specific_pages']) ? $data['de_specific_pages'] : null), $boost_id, $table_boosts_exclude_specific_pages);

	    $this->update_boost_taxonomies((isset($data['dc_taxonomies']) ? $data['dc_taxonomies'] : null), $boost_id, $table_boosts_taxonomies);
	    $this->update_boost_taxonomies((isset($data['de_taxonomies']) ? $data['de_taxonomies'] : null), $boost_id, $table_boosts_exclude_taxonomies);

	    if (isset($data['display_products']) && is_array($data['display_products'])) {
		    $boost_display_products = $data['display_products'];
		    if (!empty($boost_display_products)) {
			    $create_boost_display_product = array(
				    'boost_id' => $boost_id
			    );
			    foreach ($boost_display_products as $boost_display_product) {
				    $create_boost_display_product['product_id'] = $boost_display_product;
				    $wpdb->insert($table_boosts_products, $create_boost_display_product);
			    }
		    }
	    }
	    if (isset($data['display_product_categories']) && is_array($data['display_product_categories'])) {
		    $boost_display_product_categories = $data['display_product_categories'];
		    if (!empty($boost_display_product_categories)) {
			    $create_boost_display_product_category = array(
				    'boost_id' => $boost_id
			    );
			    foreach ($boost_display_product_categories as $boost_display_product_category) {
				    $create_boost_display_product_category['category_id'] = $boost_display_product_category;
				    $wpdb->insert($table_boosts_product_categories, $create_boost_display_product_category);
			    }
		    }
	    }
        switch ($boost_type) {
            case 'leads':
                if(isset($data['capture_url']) || isset($data['form_selector']) || isset($data['form_username_field']) || isset($data['form_surname_field'])) {
                    $create_lead_data = array(
                        'boost_id' => $boost_id,
                        'capture_url' => isset($data['capture_url']) ? $data['capture_url'] : '',
                        'form_selector' => isset($data['form_selector']) ? $data['form_selector'] : '',
                        'form_username_field' => isset($data['form_username_field']) ? $data['form_username_field'] : '',
                        'form_surname_field' => isset($data['form_surname_field']) ? $data['form_surname_field'] : '',
                    );
                    $wpdb->insert($table_leads_data, $create_lead_data);
                }
                break;
            case 'woocommerce':
                if (isset($data['subtype']) || isset($data['stock_number']) || isset($data['df_on_all_purchased_products'])
                    || isset($data['df_on_all_purchased_categories']) || isset($data['df_on_all_products'])
                    || isset($data['df_on_all_categories']) || isset($data['df_on_cart_page']) || isset($data['df_on_checkout_page'])
                    || isset($data['df_on_home_page'])) {
                    $create_woocommerce_data = array(
                        'boost_id' => $boost_id,
                        'subtype' => isset($data['subtype']) ? $data['subtype'] : '',
                        'stock_number' => isset($data['stock_number']) ? $data['stock_number'] : '10',
                        'df_on_all_purchased_products' => isset($data['df_on_all_purchased_products']) ? $data['df_on_all_purchased_products'] : '0',
                        'df_on_all_purchased_categories' => isset($data['df_on_all_purchased_categories']) ? $data['df_on_all_purchased_categories'] : '0',
                        'df_on_all_products' => isset($data['df_on_all_products']) ? $data['df_on_all_products'] : '0',
                        'df_on_all_categories' => isset($data['df_on_all_categories']) ? $data['df_on_all_categories'] : '0',
                        'df_on_cart_page' => isset($data['df_on_cart_page']) ? $data['df_on_cart_page'] : '0',
                        'df_on_checkout_page' => isset($data['df_on_checkout_page']) ? $data['df_on_checkout_page'] : '0',
                        'df_on_home_page' => isset($data['df_on_home_page']) ? $data['df_on_home_page'] : '0'
                    );
                    $wpdb->insert($table_woocommerce_data, $create_woocommerce_data);
                }
                if (isset($data['products']) && is_array($data['products'])) {
                    $boost_products = $data['products'];
                    if (!empty($boost_products)) {
                        $products_ids = $boost_products;
                        $create_boost_product = array(
                            'boost_id' => $boost_id
                        );
                        foreach ($boost_products as $boost_product) {
                            $create_boost_product['product_id'] = $boost_product;
                            $wpdb->insert($table_woocommerce_products, $create_boost_product);
                        }
                    }
                }
                if (isset($data['categories']) && is_array($data['categories'])) {
                    $boost_categories = $data['categories'];
                    if (!empty($boost_categories)) {
                        $categories_ids = $boost_categories;
                        $create_boost_category = array(
                            'boost_id' => $boost_id
                        );
                        foreach ($boost_categories as $boost_category) {
                            $create_boost_category['category_id'] = $boost_category;
                            $wpdb->insert($table_woocommerce_categories, $create_boost_category);
                        }
                    }
                }
                if (isset($data['subtype']) && in_array($data['subtype'], array('transaction', 'specific_transaction')) && function_exists('wc_get_orders')) {
                    $action_model->create_actions_for_exist_orders($boost_id, $data['type'], $data['subtype'], $time, (isset($products_ids) ? $products_ids : array()), (isset($categories_ids) ? $categories_ids : array()));
                }
                break;
            case 'easydigitaldownloads':
                if (isset($data['subtype']) || isset($data['df_on_all_purchased_products'])
                    || isset($data['df_on_all_purchased_categories']) || isset($data['df_on_all_products'])
                    || isset($data['df_on_all_categories']) || isset($data['df_on_cart_page']) || isset($data['df_on_checkout_page'])
                    || isset($data['df_on_home_page'])) {
                    $create_easydigitaldownloads_data = array(
                        'boost_id' => $boost_id,
                        'subtype' => isset($data['subtype']) ? $data['subtype'] : '',
                        'df_on_all_purchased_products' => isset($data['df_on_all_purchased_products']) ? $data['df_on_all_purchased_products'] : '0',
                        'df_on_all_purchased_categories' => isset($data['df_on_all_purchased_categories']) ? $data['df_on_all_purchased_categories'] : '0',
                        'df_on_all_products' => isset($data['df_on_all_products']) ? $data['df_on_all_products'] : '0',
                        'df_on_all_categories' => isset($data['df_on_all_categories']) ? $data['df_on_all_categories'] : '0',
                        'df_on_cart_page' => isset($data['df_on_cart_page']) ? $data['df_on_cart_page'] : '0',
                        'df_on_checkout_page' => isset($data['df_on_checkout_page']) ? $data['df_on_checkout_page'] : '0',
                        'df_on_home_page' => isset($data['df_on_home_page']) ? $data['df_on_home_page'] : '0'
                    );
                    $wpdb->insert($table_easydigitaldownloads_data, $create_easydigitaldownloads_data);
                }
                if (isset($data['products']) && is_array($data['products'])) {
                    $boost_products = $data['products'];
                    if (!empty($boost_products)) {
                        $products_ids = $boost_products;
                        $create_boost_product = array(
                            'boost_id' => $boost_id
                        );
                        foreach ($boost_products as $boost_product) {
                            $create_boost_product['product_id'] = $boost_product;
                            $wpdb->insert($table_easydigitaldownloads_products, $create_boost_product);
                        }
                    }
                }
                if (isset($data['categories']) && is_array($data['categories'])) {
                    $boost_categories = $data['categories'];
                    if (!empty($boost_categories)) {
                        $categories_ids = $boost_categories;
                        $create_boost_category = array(
                            'boost_id' => $boost_id
                        );
                        foreach ($boost_categories as $boost_category) {
                            $create_boost_category['category_id'] = $boost_category;
                            $wpdb->insert($table_easydigitaldownloads_categories, $create_boost_category);
                        }
                    }
                }
                if (isset($data['subtype']) && in_array($data['subtype'], array('transaction', 'specific_transaction')) && function_exists('edd_get_payments')) {
                    $action_model->create_actions_for_exist_orders($boost_id, $data['type'], $data['subtype'], $time, (isset($products_ids) ? $products_ids : array()), (isset($categories_ids) ? $categories_ids : array()));
                }
                break;
            default:
                break;
        }
        
        if (in_array($create_boost['type'], array('leads', 'woocommerce', 'easydigitaldownloads'))
            && (empty($create_boost['subtype']) || !empty($create_boost['subtype']) && 'stock_messages' != $create_boost['subtype'])
                && $create_boost['enable_fake'] && $create_boost['active'] == '1' && $create_boost['draft'] == '0') {
	        $max_age_notifications_days = (int)$this->settings_model->get_setting('dont-show-notifications-after-days');
	        $max_age_notifications_days_time = strtotime(date('Y-m-d', strtotime("-$max_age_notifications_days days", $time)));
            $action_model->generate_fake_actions($boost_id, NULL, $time, $max_age_notifications_days_time);
        }

        return $boost_id;
    }

    public function update_boost($data = array(), $boost_id)
    {
        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
        $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
        $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;

        $boost_data = $wpdb->get_row("SELECT * FROM $table_boosts WHERE `id` = '$boost_id';", ARRAY_A);
        $action_model = new Boost_Action_Model($this->get_plugin_name(), $this->get_version());
        $time = time();

        if (empty($boost_data)) {
            return false;
        }

        $enable_fake_changed = false;
        $min_actions_limit_changed = false;
        $active_changed = false;
        $notification_styles_off_count = 0;

        $update_boost = array();
        foreach ($boost_data as $key => $value) {
            if ('id' == $key) {
                continue;
            }
            if ('enable_fake' == $key && isset($data[$key]) && $data[$key] != $boost_data[$key]) {
                $enable_fake_changed = true;
            }
            if ('active' == $key && isset($data[$key]) && $data[$key] != $boost_data[$key]) {
                $active_changed = true;
            }
            if ('min_actions_limit' == $key && isset($data[$key]) && $data[$key] != $boost_data[$key]) {
	            $min_actions_limit_changed = true;
            }
            if (stripos($key, 'notification_style_') !== false && isset($data[$key]) && $data[$key] == 0) {
	            $notification_styles_off_count++;
            }
            if (isset($data[$key]) && $data[$key] != $boost_data[$key]) {
                $update_boost[$key] = $data[$key];
            }
        }

        if ($notification_styles_off_count == 6) {
	        $update_boost['notification_style_1'] = 1;
        }

        if (!empty($update_boost) && $wpdb->update($table_boosts, $update_boost, array('id' => $boost_id)) === false) {
            return false;
        }

        $this->update_boost_countries_for_fake((isset($data['countries_for_fakes']) ? $data['countries_for_fakes'] : null), $boost_id, $table_boosts_fake_countries);

        $this->update_boost_post_types((isset($data['dc_post_types']) ? $data['dc_post_types'] : null), $boost_id, $table_boosts_post_types);
        $this->update_boost_post_types((isset($data['de_post_types']) ? $data['de_post_types'] : null), $boost_id, $table_boosts_exclude_post_types);

        $this->update_boost_urls((isset($data['dc_urls']) ? $data['dc_urls'] : null), $boost_id, $table_boosts_urls);
        $this->update_boost_urls((isset($data['de_urls']) ? $data['de_urls'] : null), $boost_id, $table_boosts_exclude_urls);

        $this->update_boost_specific_pages((isset($data['dc_specific_pages']) ? $data['dc_specific_pages'] : null), $boost_id, $table_boosts_specific_pages);
        $this->update_boost_specific_pages((isset($data['de_specific_pages']) ? $data['de_specific_pages'] : null), $boost_id, $table_boosts_exclude_specific_pages);

        $this->update_boost_taxonomies((isset($data['dc_taxonomies']) ? $data['dc_taxonomies'] : null), $boost_id, $table_boosts_taxonomies);
        $this->update_boost_taxonomies((isset($data['de_taxonomies']) ? $data['de_taxonomies'] : null), $boost_id, $table_boosts_exclude_taxonomies);

        if (isset($data['display_products']) && is_array($data['display_products'])) {
            $boost_display_products_new = $data['display_products'];
            if (($boost_display_products = $wpdb->get_col("SELECT `product_id` FROM $table_boosts_products WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                $boost_display_products = array();
            }
            $new_old_diff = array_diff($boost_display_products_new, $boost_display_products);
            $old_new_diff = array_diff($boost_display_products, $boost_display_products_new);
            if (!empty($new_old_diff)
                || !empty($old_new_diff)
            ) {
                $wpdb->delete($table_boosts_products, array('boost_id' => $boost_id));
                if (!empty($boost_display_products_new)) {
                    $create_boost_display_product = array(
                        'boost_id' => $boost_id
                    );
                    foreach ($boost_display_products_new as $boost_display_product) {
                        $create_boost_display_product['product_id'] = $boost_display_product;
                        $wpdb->insert($table_boosts_products, $create_boost_display_product);
                    }
                }
            }
        }

        if (isset($data['display_product_categories']) && is_array($data['display_product_categories'])) {
            $boost_display_product_categories_new = $data['display_product_categories'];
            if (($boost_display_product_categories = $wpdb->get_col("SELECT `category_id` FROM $table_boosts_product_categories WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                $boost_display_product_categories = array();
            }
	        $new_old_diff = array_diff($boost_display_product_categories_new, $boost_display_product_categories);
	        $old_new_diff = array_diff($boost_display_product_categories, $boost_display_product_categories_new);
	        if (!empty($new_old_diff)
	            || !empty($old_new_diff)
	        ) {
                $wpdb->delete($table_boosts_product_categories, array('boost_id' => $boost_id));
                if (!empty($boost_display_product_categories_new)) {
                    $create_boost_display_product_category = array(
                        'boost_id' => $boost_id
                    );
                    foreach ($boost_display_product_categories_new as $boost_display_product_category) {
                        $create_boost_display_product_category['category_id'] = $boost_display_product_category;
                        $wpdb->insert($table_boosts_product_categories, $create_boost_display_product_category);
                    }
                }
            }
        }

        switch ($data['type']) {
            case 'leads':
                if (isset($data['capture_url']) || isset($data['form_selector']) || isset($data['form_username_field']) || isset($data['form_surname_field'])) {
                    $boost_lead_data = array(
                        'boost_id' => $boost_id,
                        'capture_url' => '',
                        'form_selector' => '',
                        'form_username_field' => '',
                        'form_surname_field' => ''
                    );

                    $need_create = false;
                    $boost_lead_data_old = $wpdb->get_row("SELECT * FROM $table_leads_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                    if (empty($boost_lead_data_old)) {
                        $need_create = true;
                    } else {
                        $boost_lead_data = $boost_lead_data_old;
                    }
                    foreach ($boost_lead_data as $key => $value) {
                        if ('id' == $key) {
                            continue;
                        }
                        $boost_lead_data[$key] = isset($data[$key]) ? $data[$key] : $value;
                    }

                    if ($need_create) {
                        if ($wpdb->insert($table_leads_data, $boost_lead_data) == false) {
                            return false;
                        }
                    } else {
                        $wpdb->update($table_leads_data, $boost_lead_data, array('boost_id' => $boost_id));
                    }
                }
                break;
            case 'woocommerce':
                $subtype_changed = false;
	            if (isset($data['subtype']) || isset($data['stock_number']) || isset($data['df_on_all_purchased_products'])
	                || isset($data['df_on_all_purchased_categories']) || isset($data['df_on_all_products'])
	                || isset($data['df_on_all_categories']) || isset($data['df_on_cart_page']) || isset($data['df_on_checkout_page'])
	                || isset($data['df_on_home_page'])) {
                    $boost_woocommerce_data = array(
                        'boost_id' => $boost_id,
                        'subtype' => '',
                        'stock_number' => '',
                        'df_on_all_purchased_products' => '',
                        'df_on_all_purchased_categories' => '',
                        'df_on_all_products' => '',
                        'df_on_all_categories' => '',
                        'df_on_cart_page' => '',
                        'df_on_checkout_page' => '',
                        'df_on_home_page' => ''
                    );

                    $need_create = false;
                    $boost_woocommerce_data_old = $wpdb->get_row("SELECT * FROM $table_woocommerce_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                    if (empty($boost_woocommerce_data_old)) {
                        $need_create = true;
                    } else {
                        $boost_woocommerce_data = $boost_woocommerce_data_old;
                    }

                    foreach ($boost_woocommerce_data as $key => $value) {
                        if ('id' == $key) {
                            continue;
                        }
                        if ('subtype' == $key && $value != $data[$key]) {
                            $subtype_changed = true;
                            $update_boost = array(
                                'message' => isset($this->default_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : '',
                                'top_message' => isset($this->default_top_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_top_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : '',
                                'display_type' => isset($this->default_display_types[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_display_types[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : ''
                            );
                            $wpdb->update($table_boosts, $update_boost, array('id' => $boost_id));
                        }
                        $boost_woocommerce_data[$key] = isset($data[$key]) ? $data[$key] : $value;
                    }

                    if ($need_create) {
                        if ($wpdb->insert($table_woocommerce_data, $boost_woocommerce_data) == false) {
                            return false;
                        }
                    } else {
                        $wpdb->update($table_woocommerce_data, $boost_woocommerce_data, array('boost_id' => $boost_id));
                    }
                }
                if (isset($data['products']) && is_array($data['products'])) {
                    $boost_products_new = $data['products'];
                    if (($boost_products = $wpdb->get_col("SELECT `product_id` FROM $table_woocommerce_products WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                        $boost_products = array();
                    }
	                $new_old_diff = array_diff($boost_products_new, $boost_products);
	                $old_new_diff = array_diff($boost_products, $boost_products_new);
	                if (!empty($new_old_diff)
	                    || !empty($old_new_diff)
	                ) {
                        $wpdb->delete($table_woocommerce_products, array('boost_id' => $boost_id));
                        if (!empty($boost_products_new)) {
                            $create_boost_product = array(
                                'boost_id' => $boost_id
                            );
                            foreach ($boost_products_new as $boost_product) {
                                $create_boost_product['product_id'] = $boost_product;
                                $wpdb->insert($table_woocommerce_products, $create_boost_product);
                            }
                        }
                    }
                }
                if (isset($data['categories']) && is_array($data['categories'])) {
                    $boost_categories_new = $data['categories'];
                    if (($boost_categories = $wpdb->get_col("SELECT `category_id` FROM $table_woocommerce_categories WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                        $boost_categories = array();
                    }
	                $new_old_diff = array_diff($boost_categories_new, $boost_categories);
	                $old_new_diff = array_diff($boost_categories, $boost_categories_new);
	                if (!empty($new_old_diff)
	                    || !empty($old_new_diff)
	                ) {
                        $wpdb->delete($table_woocommerce_categories, array('boost_id' => $boost_id));
                        if (!empty($boost_categories_new)) {
                            $create_boost_category = array(
                                'boost_id' => $boost_id
                            );
                            foreach ($boost_categories_new as $boost_category) {
                                $create_boost_category['category_id'] = $boost_category;
                                $wpdb->insert($table_woocommerce_categories, $create_boost_category);
                            }
                        }
                    }
                }
                if ($subtype_changed && isset($data['subtype']) && in_array($data['subtype'], array('transaction', 'specific_transaction')) && function_exists('wc_get_orders')) {
                    $action_model->create_actions_for_exist_orders($boost_id, $data['type'], $data['subtype'], $time, (isset($products_ids) ? $products_ids : array()), (isset($categories_ids) ? $categories_ids : array()));
                }
                break;
            case 'easydigitaldownloads':
                $subtype_changed = false;
                if (isset($data['subtype']) || isset($data['df_on_all_purchased_products'])
                    || isset($data['df_on_all_purchased_categories']) || isset($data['df_on_all_products'])
                    || isset($data['df_on_all_categories']) || isset($data['df_on_cart_page']) || isset($data['df_on_checkout_page'])
                    || isset($data['df_on_home_page'])) {
                    $boost_easydigitaldownloads_data = array(
                        'boost_id' => $boost_id,
                        'subtype' => '',
                        'df_on_all_purchased_products' => '',
                        'df_on_all_purchased_categories' => '',
                        'df_on_all_products' => '',
                        'df_on_all_categories' => '',
                        'df_on_cart_page' => '',
                        'df_on_checkout_page' => '',
                        'df_on_home_page' => ''
                    );

                    $need_create = false;
                    $boost_easydigitaldownloads_data_old = $wpdb->get_row("SELECT * FROM $table_easydigitaldownloads_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                    if (empty($boost_easydigitaldownloads_data_old)) {
                        $need_create = true;
                    } else {
                        $boost_easydigitaldownloads_data = $boost_easydigitaldownloads_data_old;
                    }

                    foreach ($boost_easydigitaldownloads_data as $key => $value) {
                        if ('id' == $key) {
                            continue;
                        }
                        if ('subtype' == $key && $value != $data[$key]) {
                            $subtype_changed = true;
                            $update_boost = array(
                                'message' => isset($this->default_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : '',
                                'top_message' => isset($this->default_top_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_top_messages[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : '',
                                'display_type' => isset($this->default_display_types[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']]) ? $this->default_display_types[(isset($data['type']) ? $data['type'] : $boost_data['type']) . $data['subtype']] : ''
                            );
                            $wpdb->update($table_boosts, $update_boost, array('id' => $boost_id));
                        }
                        $boost_easydigitaldownloads_data[$key] = isset($data[$key]) ? $data[$key] : $value;
                    }

                    if ($need_create) {
                        if ($wpdb->insert($table_easydigitaldownloads_data, $boost_easydigitaldownloads_data) == false) {
                            return false;
                        }
                    } else {
                        $wpdb->update($table_easydigitaldownloads_data, $boost_easydigitaldownloads_data, array('boost_id' => $boost_id));
                    }
                }
                if (isset($data['products']) && is_array($data['products'])) {
                    $boost_products_new = $data['products'];
                    if (($boost_products = $wpdb->get_col("SELECT `product_id` FROM $table_easydigitaldownloads_products WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                        $boost_products = array();
                    }
	                $new_old_diff = array_diff($boost_products_new, $boost_products);
	                $old_new_diff = array_diff($boost_products, $boost_products_new);
	                if (!empty($new_old_diff)
	                    || !empty($old_new_diff)
	                ) {
                        $wpdb->delete($table_easydigitaldownloads_products, array('boost_id' => $boost_id));
                        if (!empty($boost_products_new)) {
                            $create_boost_product = array(
                                'boost_id' => $boost_id
                            );
                            foreach ($boost_products_new as $boost_product) {
                                $create_boost_product['product_id'] = $boost_product;
                                $wpdb->insert($table_easydigitaldownloads_products, $create_boost_product);
                            }
                        }
                    }
                }
                if (isset($data['categories']) && is_array($data['categories'])) {
                    $boost_categories_new = $data['categories'];
                    if (($boost_categories = $wpdb->get_col("SELECT `category_id` FROM $table_easydigitaldownloads_categories WHERE `boost_id` = '$boost_id';", ARRAY_A)) == null) {
                        $boost_categories = array();
                    }
	                $new_old_diff = array_diff($boost_categories_new, $boost_categories);
	                $old_new_diff = array_diff($boost_categories, $boost_categories_new);
	                if (!empty($new_old_diff)
	                    || !empty($old_new_diff)
	                ) {
                        $wpdb->delete($table_easydigitaldownloads_categories, array('boost_id' => $boost_id));
                        if (!empty($boost_categories_new)) {
                            $create_boost_category = array(
                                'boost_id' => $boost_id
                            );
                            foreach ($boost_categories_new as $boost_category) {
                                $create_boost_category['category_id'] = $boost_category;
                                $wpdb->insert($table_easydigitaldownloads_categories, $create_boost_category);
                            }
                        }
                    }
                }
                if ($subtype_changed && isset($data['subtype']) && in_array($data['subtype'], array('transaction', 'specific_transaction')) && function_exists('edd_get_payments')) {
                    $action_model->create_actions_for_exist_orders($boost_id, $data['type'], $data['subtype'], $time, (isset($products_ids) ? $products_ids : array()), (isset($categories_ids) ? $categories_ids : array()));
                }
                break;
            default:
                break;
        }

        $enable_fake = isset($data['enable_fake']) ? $data['enable_fake'] : $boost_data['enable_fake'];
        $active = isset($data['active']) ? $data['active'] : $boost_data['active'];
        if ($enable_fake_changed && '1' == $enable_fake && '1' == $active
            || $active_changed && '1' == $active && '1' == $enable_fake
	        || $min_actions_limit_changed && '1' == $active && '1' == $enable_fake) {
            if (in_array($data['type'], array('leads', 'woocommerce', 'easydigitaldownloads'))
                && (empty($data['subtype']) || !empty($data['subtype']) && 'stock_messages' != $data['subtype'])) {
	            $max_age_notifications_days = (int)$this->settings_model->get_setting('dont-show-notifications-after-days');
	            $max_age_notifications_days_time = strtotime(date('Y-m-d', strtotime("-$max_age_notifications_days days", $time)));
                $action_model->generate_fake_actions($boost_id, NULL, $time, $max_age_notifications_days_time);
            }
        } 
        elseif ($enable_fake_changed && '0' == $enable_fake && '1' == $active
            || $active_changed && '0' == $active) {
            $action_model->delete_actions(array('boost_id' => $boost_id, 'fake' => '1'));
        }

        return $boost_id;
    }

    public function delete_boost($boost_id){
        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
        $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
        $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
        $table_actions = $wpdb->prefix . TABLE_ACTIONS;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;

        $boost_data = $wpdb->get_row("SELECT * FROM $table_boosts WHERE `id` = '$boost_id';", ARRAY_A);

        if (empty($boost_data)) {
            return false;
        }

        $delete_tables = array(
	        $table_boosts_fake_countries,
	        $table_boosts_post_types,
	        $table_boosts_exclude_post_types,
	        $table_boosts_urls,
	        $table_boosts_exclude_urls,
	        $table_boosts_products,
	        $table_boosts_product_categories,
	        $table_boosts_specific_pages,
	        $table_boosts_exclude_specific_pages,
	        $table_boosts_taxonomies,
	        $table_boosts_exclude_taxonomies
        );
	    foreach ($delete_tables as $delete_table) {
		    $wpdb->delete( $delete_table, array('boost_id' => $boost_data['id']));
	    }

        switch ($boost_data['type']) {
            case 'leads':
                $wpdb->delete( $table_leads_data, array('boost_id' => $boost_data['id']));
                break;
            case 'woocommerce':
                $wpdb->delete( $table_woocommerce_data, array('boost_id' => $boost_data['id']));
                $wpdb->delete( $table_woocommerce_products, array('boost_id' => $boost_data['id']));
                $wpdb->delete( $table_woocommerce_categories, array('boost_id' => $boost_data['id']));
                break;
            case 'easydigitaldownloads':
                $wpdb->delete( $table_easydigitaldownloads_data, array('boost_id' => $boost_data['id']));
                $wpdb->delete( $table_easydigitaldownloads_products, array('boost_id' => $boost_data['id']));
                $wpdb->delete( $table_easydigitaldownloads_categories, array('boost_id' => $boost_data['id']));
                break;
            default:
                break;
        }

        $wpdb->delete( $table_boosts, array('id' => $boost_data['id']));

        return true;
    }

    public function copy_boost($boost_id){
        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
        $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
        $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;

        $boost_data = $wpdb->get_row("SELECT * FROM $table_boosts WHERE `id` = '$boost_id';", ARRAY_A);

        if (empty($boost_data)) {
            return false;
        }

        unset($boost_data['id']);
        $boost_data['active'] = 0;


        if ($wpdb->insert($table_boosts, $boost_data) == false) {
            return false;
        }

        $new_boost_id = $wpdb->insert_id;

        $copy_tables = array(
	        $table_boosts_fake_countries,
	        $table_boosts_post_types,
	        $table_boosts_exclude_post_types,
	        $table_boosts_urls,
	        $table_boosts_exclude_urls,
	        $table_boosts_products,
	        $table_boosts_product_categories,
	        $table_boosts_specific_pages,
	        $table_boosts_exclude_specific_pages,
	        $table_boosts_taxonomies,
	        $table_boosts_exclude_taxonomies
        );

        foreach ($copy_tables as $copy_table) {
        	$this->copy_table_data($boost_id, $new_boost_id, $copy_table);
        }

        switch ($boost_data['type']) {
            case 'leads':
	            $this->copy_table_data($boost_id, $new_boost_id, $table_leads_data);
                break;
            case 'woocommerce':
	            $this->copy_table_data($boost_id, $new_boost_id, $table_woocommerce_data);
	            $this->copy_table_data($boost_id, $new_boost_id, $table_woocommerce_products);
	            $this->copy_table_data($boost_id, $new_boost_id, $table_woocommerce_categories);
                break;
            case 'easydigitaldownloads':
	            $this->copy_table_data($boost_id, $new_boost_id, $table_easydigitaldownloads_data);
	            $this->copy_table_data($boost_id, $new_boost_id, $table_easydigitaldownloads_products);
	            $this->copy_table_data($boost_id, $new_boost_id, $table_easydigitaldownloads_categories);
                break;
            default:
                break;
        }

        return $new_boost_id;
    }

    public function get_boost($boost_id){
        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_woocommerce_products = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_PRODUCTS;
        $table_woocommerce_categories = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_CATEGORIES;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_easydigitaldownloads_products = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_PRODUCTS;
        $table_easydigitaldownloads_categories = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_CATEGORIES;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
        $table_boosts_products = $wpdb->prefix . TABLE_BOOSTS_PRODUCTS;
        $table_boosts_product_categories = $wpdb->prefix . TABLE_BOOSTS_PRODUCT_CATEGORIES;
        $table_boosts_post_types = $wpdb->prefix . TABLE_BOOSTS_POST_TYPES;
        $table_boosts_urls = $wpdb->prefix . TABLE_BOOSTS_URLS;
        $table_boosts_exclude_urls = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_URLS;
        $table_boosts_exclude_post_types = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_POST_TYPES;
	    $table_boosts_specific_pages = $wpdb->prefix . TABLE_BOOSTS_SPECIFIC_PAGES;
	    $table_boosts_exclude_specific_pages = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_SPECIFIC_PAGES;
	    $table_boosts_taxonomies = $wpdb->prefix . TABLE_BOOSTS_TAXONOMIES;
	    $table_boosts_exclude_taxonomies = $wpdb->prefix . TABLE_BOOSTS_EXCLUDE_TAXONOMIES;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;

        $boost = $wpdb->get_row("SELECT * FROM $table_boosts WHERE `id` = '$boost_id';", ARRAY_A);

        if (empty($boost)) {
            return false;
        }


	    $boost['countries_for_fakes'] = $wpdb->get_col("SELECT `country` FROM $table_boosts_fake_countries WHERE `boost_id` = '$boost_id';");

        $boost_post_types_data = $wpdb->get_results("SELECT * FROM $table_boosts_post_types WHERE `boost_id` = '$boost_id';", ARRAY_A);
        $boost_post_types = array();
        if (!empty($boost_post_types_data)) {
            foreach ($boost_post_types_data as $boost_post_type_data) {
                $post_type = get_post_type_object($boost_post_type_data['post_type']);
                if (!empty($post_type)) {
                    $boost_post_types[] = array(
                        'post_type_name' => $post_type->name,
                        'post_type_label' => $post_type->label
                    );
                }
            }
        }
        $boost['dc_post_types'] = $boost_post_types;

        $boost_exclude_post_types_data = $wpdb->get_results("SELECT * FROM $table_boosts_exclude_post_types WHERE `boost_id` = '$boost_id';", ARRAY_A);
        $boost_exclude_post_types = array();
        if (!empty($boost_exclude_post_types_data)) {
            foreach ($boost_exclude_post_types_data as $boost_exclude_post_type_data) {
                $post_type = get_post_type_object($boost_exclude_post_type_data['post_type']);
                if (!empty($post_type)) {
                    $boost_exclude_post_types[] = array(
                        'post_type_name' => $post_type->name,
                        'post_type_label' => $post_type->label
                    );
                }
            }
        }
        $boost['de_post_types'] = $boost_exclude_post_types;

        $boost_urls = $wpdb->get_results("SELECT * FROM $table_boosts_urls WHERE `boost_id` = '$boost_id';", ARRAY_A);
        $boost['dc_urls'] = $boost_urls;

        $boost_exclude_urls = $wpdb->get_results("SELECT * FROM $table_boosts_exclude_urls WHERE `boost_id` = '$boost_id';", ARRAY_A);
        $boost['de_urls'] = $boost_exclude_urls;

	    $boost_specific_pages_data = $wpdb->get_col("SELECT `post_id` FROM $table_boosts_specific_pages WHERE `boost_id` = '$boost_id';");
	    $boost_specific_pages = array();
	    if (!empty($boost_specific_pages_data)) {
		    foreach ($boost_specific_pages_data as $boost_specific_page_id) {
			    $specific_page = get_post($boost_specific_page_id);
			    if (!empty($specific_page)) {
				    $boost_specific_pages[] = array(
					    'id' => $specific_page->ID,
					    'title' => $specific_page->post_title . ' (' . ucfirst(str_replace('_', ' ',$specific_page->post_type)) . ')'
				    );
			    }
		    }
	    }
	    $boost['dc_specific_pages'] = $boost_specific_pages;

	    $boost_exclude_specific_pages_data = $wpdb->get_col("SELECT `post_id` FROM $table_boosts_exclude_specific_pages WHERE `boost_id` = '$boost_id';");
	    $boost_exclude_specific_pages = array();
	    if (!empty($boost_exclude_specific_pages_data)) {
		    foreach ($boost_exclude_specific_pages_data as $boost_exclude_specific_page_id) {
			    $exclude_specific_page = get_post($boost_exclude_specific_page_id);
			    if (!empty($exclude_specific_page)) {
				    $boost_exclude_specific_pages[] = array(
					    'id' => $exclude_specific_page->ID,
					    'title' => $exclude_specific_page->post_title . ' (' . ucfirst(str_replace('_', ' ',$exclude_specific_page->post_type)) . ')'
				    );
			    }
		    }
	    }
	    $boost['de_specific_pages'] = $boost_exclude_specific_pages;

	    $boost_taxonomies_data = $wpdb->get_col("SELECT `taxonomy_id` FROM $table_boosts_taxonomies WHERE `boost_id` = '$boost_id';");
	    $boost_taxonomies = array();
	    if (!empty($boost_taxonomies_data)) {
		    foreach ($boost_taxonomies_data as $boost_taxonomy_id) {
			    if (is_numeric($boost_taxonomy_id)) {
				    $term = get_term($boost_taxonomy_id);
				    if (!empty($term)) {
					    $boost_taxonomies[] = array(
						    'id' => $term->term_id,
						    'name' => $term->name . ' (' . ucfirst(str_replace('_', ' ',$term->taxonomy)) . ')'
					    );
				    }
			    }
			    else {
				    $taxonomy = get_taxonomy($boost_taxonomy_id);
				    if (!empty($taxonomy)) {
					    $boost_taxonomies[] = array(
						    'id' => $taxonomy->name,
						    'name' => $taxonomy->label . ' (All)'
					    );
				    }

			    }
		    }
	    }
	    $boost['dc_taxonomies'] = $boost_taxonomies;

	    $boost_exclude_taxonomies_data = $wpdb->get_col("SELECT `taxonomy_id` FROM $table_boosts_exclude_taxonomies WHERE `boost_id` = '$boost_id';");
	    $boost_exclude_taxonomies = array();
	    if (!empty($boost_exclude_taxonomies_data)) {
		    foreach ($boost_exclude_taxonomies_data as $boost_exclude_taxonomy_id) {
		    	if (is_numeric($boost_exclude_taxonomy_id)) {
				    $exclude_term = get_term($boost_exclude_taxonomy_id);
				    if (!empty($exclude_term)) {
					    $boost_exclude_taxonomies[] = array(
						    'id' => $exclude_term->term_id,
						    'name' => $exclude_term->name . ' (' . ucfirst(str_replace('_', ' ',$exclude_term->taxonomy)) . ')'
					    );
				    }
			    }
			    else {
				    $exclude_taxonomy = get_taxonomy($boost_exclude_taxonomy_id);
				    if (!empty($exclude_taxonomy)) {
					    $boost_exclude_taxonomies[] = array(
						    'id' => $exclude_taxonomy->name,
						    'name' => $exclude_taxonomy->label . ' (All)'
					    );
				    }

			    }
		    }
	    }
	    $boost['de_taxonomies'] = $boost_exclude_taxonomies;


        switch ($boost['type']) {
            case 'leads':
                $boost_lead_data = $wpdb->get_row("SELECT * FROM $table_leads_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if( !empty($boost_lead_data)) {
                    unset($boost_lead_data['id'], $boost_lead_data['boost_id']);
                    $boost = array_merge($boost, $boost_lead_data);
                }
                break;
            case 'woocommerce':
                $boost_woocommerce_data = $wpdb->get_row("SELECT * FROM $table_woocommerce_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if( !empty($boost_woocommerce_data)) {
                    unset($boost_woocommerce_data['id'], $boost_woocommerce_data['boost_id']);
                    $boost = array_merge($boost, $boost_woocommerce_data);
                }

                $boost_woocommerce_products_data = $wpdb->get_results("SELECT * FROM $table_woocommerce_products WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_woocommerce_products_data)) {
                    foreach ($boost_woocommerce_products_data as $index => $boost_woocommerce_product_data) {
                        $product = get_post($boost_woocommerce_product_data['product_id'], ARRAY_A);
                        $boost_woocommerce_products_data[$index]['product_name'] = !empty($product['post_title']) ? $product['post_title'] : '';
                    }
                }
                $boost['products'] = $boost_woocommerce_products_data;

                $boost_woocommerce_categories_data = $wpdb->get_results("SELECT * FROM $table_woocommerce_categories WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_woocommerce_categories_data)) {
                    foreach ($boost_woocommerce_categories_data as $index => $boost_woocommerce_category_data) {
                        $category = get_term_by('id', $boost_woocommerce_category_data['category_id'], 'product_cat', ARRAY_A);
                            $boost_woocommerce_categories_data[$index]['category_name'] = !empty($category['name']) ? $category['name'] : '';
                    }
                }
                $boost['categories'] = $boost_woocommerce_categories_data;

                $boost_display_products_data = $wpdb->get_results("SELECT * FROM $table_boosts_products WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_display_products_data)) {
                    foreach ($boost_display_products_data as $index => $boost_display_product_data) {
                        $product = get_post($boost_display_product_data['product_id'], ARRAY_A);
                        $boost_display_products_data[$index]['product_name'] = !empty($product['post_title']) ? $product['post_title'] : '';
                    }
                }
                $boost['display_products'] = $boost_display_products_data;

                $boost_display_product_categories_data = $wpdb->get_results("SELECT * FROM $table_boosts_product_categories WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_display_product_categories_data)) {
                    foreach ($boost_display_product_categories_data as $index => $boost_display_product_category_data) {
                        $category = get_term_by('id', $boost_display_product_category_data['category_id'], 'product_cat', ARRAY_A);
                        $boost_display_product_categories_data[$index]['category_name'] = !empty($category['name']) ? $category['name'] : '';
                    }
                }
                $boost['display_product_categories'] = $boost_display_product_categories_data;
                break;
            case 'easydigitaldownloads':
                $boost_easydigitaldownloads_data = $wpdb->get_row("SELECT * FROM $table_easydigitaldownloads_data WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if( !empty($boost_easydigitaldownloads_data)) {
                    unset($boost_easydigitaldownloads_data['id'], $boost_easydigitaldownloads_data['boost_id']);
                    $boost = array_merge($boost, $boost_easydigitaldownloads_data);
                }

                $boost_easydigitaldownloads_products_data = $wpdb->get_results("SELECT * FROM $table_easydigitaldownloads_products WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_easydigitaldownloads_products_data)) {
                    foreach ($boost_easydigitaldownloads_products_data as $index => $boost_easydigitaldownloads_product_data) {
                        $product = get_post($boost_easydigitaldownloads_product_data['product_id'], ARRAY_A);
                        $boost_easydigitaldownloads_products_data[$index]['product_name'] = !empty($product['post_title']) ? $product['post_title'] : '';
                    }
                }
                $boost['products'] = $boost_easydigitaldownloads_products_data;

                $boost_easydigitaldownloads_categories_data = $wpdb->get_results("SELECT * FROM $table_easydigitaldownloads_categories WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_easydigitaldownloads_categories_data)) {
                    foreach ($boost_easydigitaldownloads_categories_data as $index => $boost_easydigitaldownloads_category_data) {
                        $category = get_term_by('id', $boost_easydigitaldownloads_category_data['category_id'], 'download_category', ARRAY_A);
                        $boost_easydigitaldownloads_categories_data[$index]['category_name'] = !empty($category['name']) ? $category['name'] : '';
                    }
                }
                $boost['categories'] = $boost_easydigitaldownloads_categories_data;

                $boost_display_products_data = $wpdb->get_results("SELECT * FROM $table_boosts_products WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_display_products_data)) {
                    foreach ($boost_display_products_data as $index => $boost_display_product_data) {
                        $product = get_post($boost_display_product_data['product_id'], ARRAY_A);
                        $boost_display_products_data[$index]['product_name'] = !empty($product['post_title']) ? $product['post_title'] : '';
                    }
                }
                $boost['display_products'] = $boost_display_products_data;

                $boost_display_product_categories_data = $wpdb->get_results("SELECT * FROM $table_boosts_product_categories WHERE `boost_id` = '$boost_id';", ARRAY_A);
                if (!empty($boost_display_product_categories_data)) {
                    foreach ($boost_display_product_categories_data as $index => $boost_display_product_category_data) {
                        $category = get_term_by('id', $boost_display_product_category_data['category_id'], 'download_category', ARRAY_A);
                        $boost_display_product_categories_data[$index]['category_name'] = !empty($category['name']) ? $category['name'] : '';
                    }
                }
                $boost['display_product_categories'] = $boost_display_product_categories_data;
                break;
            default:
                break;
        }

        return $boost;
    }

    public function get_boosts($filters = array('draft' => '0'), $sort = array(), $fake_countries = false){
        global $wpdb;

        $table_leads_data = $wpdb->prefix . TABLE_BOOSTS_LEADS_DATA;
        $table_woocommerce_data = $wpdb->prefix . TABLE_BOOSTS_WOOCOMMERCE_DATA;
        $table_easydigitaldownloads_data = $wpdb->prefix . TABLE_BOOSTS_EASYDIGITALDOWNLOADS_DATA;
        $table_boosts = $wpdb->prefix . TABLE_BOOSTS;
	    $table_boosts_fake_countries = $wpdb->prefix . TABLE_BOOSTS_FAKE_COUNTRIES;

	    $where = '1=1';
	    foreach ($filters as $key => $value) {
		    if ( is_array( $value ) ) {
			    switch ( $value['comparison'] ) {
				    case 'lt':
					    $where .= " AND `$key` < '" . $value['value'] . "'";
					    break;
				    case 'mt':
					    $where .= " AND `$key` > '" . $value['value'] . "'";
					    break;
				    case 'equal':
					    $where .= " AND `$key` = '" . $value['value'] . "'";
					    break;
				    case 'lte':
					    $where .= " AND `$key` <= '" . $value['value'] . "'";
					    break;
				    case 'mte':
					    $where .= " AND `$key` >= '" . $value['value'] . "'";
					    break;
				    case 'in':
				    	if (!empty($value['value']) && is_array($value['value'])) {
						    $where .= " AND `$key` IN ('" . implode('\',\'', $value['value']) . "')";
					    }
					    break;
			    }
		    } else {
			    $where .= " AND `$key` = '$value'";
		    }
	    }

	    $order = '';
	    foreach ($sort as $order_field => $order_type) {
		    $order .= " $order_field $order_type,";
	    }
	    $order = !empty($order) ? substr($order, 0, -1) : 'id ASC';

        $boosts = array();

        $leads_boosts = $wpdb->get_results("SELECT $table_boosts.*, $table_leads_data.`capture_url`, $table_leads_data.`form_selector` FROM $table_boosts LEFT JOIN $table_leads_data ON ($table_leads_data.`boost_id` = $table_boosts.`id`) WHERE $table_boosts.`type` = 'leads' AND $where ORDER BY $order;", ARRAY_A);
        if (!empty($leads_boosts)) {
            $boosts = array_merge($boosts, $leads_boosts);
        }

        $woocommerce_boosts = $wpdb->get_results("SELECT $table_boosts.*, $table_woocommerce_data.`subtype`, $table_woocommerce_data.`stock_number` FROM $table_boosts LEFT JOIN $table_woocommerce_data ON ($table_woocommerce_data.`boost_id` = $table_boosts.`id`) WHERE $table_boosts.`type` = 'woocommerce' AND $where ORDER BY $order;", ARRAY_A);
        if (!empty($woocommerce_boosts)) {
            $boosts = array_merge($boosts, $woocommerce_boosts);
        }

        $easydigitaldownloads_boosts = $wpdb->get_results("SELECT $table_boosts.*, $table_easydigitaldownloads_data.`subtype` FROM $table_boosts LEFT JOIN $table_easydigitaldownloads_data ON ($table_easydigitaldownloads_data.`boost_id` = $table_boosts.`id`) WHERE $table_boosts.`type` = 'easydigitaldownloads' AND $where ORDER BY $order;", ARRAY_A);
        if (!empty($easydigitaldownloads_boosts)) {
            $boosts = array_merge($boosts, $easydigitaldownloads_boosts);
        }

        if ($fake_countries) {
	        foreach ($boosts as $key => $boost) {
		        $boosts[$key]['countries_for_fakes'] = $wpdb->get_col("SELECT `country` FROM $table_boosts_fake_countries WHERE `boost_id` = '{$boost['id']}';");
	        }
        }

	    return $boosts;
    }

    public function get_step_fields($step, $boost_type, $boost_subtype = NULL){
        $fields = array();
        switch ($step) {
            case '1':
                switch ($boost_type) {
                    case 'leads':
                        $fields = array(
                            'type' => '',
                            'capture_url' => '',
                            'form_selector' => '',
                            'form_username_field' => '',
                            'form_surname_field' => '',
                            'enable_fake' => 0,
                            'min_actions_limit' => 20
                        );
                        break;
                    case 'woocommerce':
                        $fields = array(
                            'type' => '',
                            'subtype' => '',
                            'products' => array(),
                            'categories' => array(),
                            'stock_number' => 1,
                            'enable_fake' => 0,
                            'min_actions_limit' => 20
                        );
                        break;
                    case 'easydigitaldownloads':
                        $fields = array(
                            'type' => '',
                            'subtype' => '',
                            'products' => array(),
                            'categories' => array(),
                            'enable_fake' => 0,
                            'min_actions_limit' => 20
                        );
                        break;
                    default:
                        break;
                }
                break;
            case '2':
                $fields = array(
                    'top_message' => '',
                    'message' => 0,
                    'notification_template' => '',
	                'desktop_notification_style_1' => 0,
	                'desktop_notification_style_2' => 0,
	                'desktop_notification_style_3' => 0,
	                'desktop_notification_style_4' => 0,
	                'desktop_notification_style_5' => 0,
	                'desktop_notification_style_6' => 0,
	                'mobile_notification_style_1' => 0,
	                'mobile_notification_style_2' => 0,
	                'mobile_notification_style_3' => 0,
	                'mobile_notification_style_4' => 0,
	                'mobile_notification_style_5' => 0,
	                'mobile_notification_style_6' => 0,
                    'use_products_images' => 0,
                    'use_maps_images' => 0,
                    'use_icons_images' => 0
                );
                break;
            case '3':

                $fields = array(
                    'dc_on_home_page' => 0,
                    'dc_on_urls' => 0,
                    'dc_urls' => array(),
                    'dc_on_specific_pages' => 0,
                    'dc_specific_pages' => array(),
                    'dc_on_post_types' => 0,
                    'dc_post_types' => array(),
                    'dc_on_taxonomies' => 0,
                    'dc_taxonomies' => array(),
                    'de_on_home_page' => 0,
                    'de_on_urls' => 0,
                    'de_urls' => array(),
                    'de_on_specific_pages' => 0,
                    'de_specific_pages' => array(),
                    'de_on_post_types' => 0,
                    'de_post_types' => array(),
                    'de_on_taxonomies' => 0,
                    'de_taxonomies' => array()
                );
                switch ($boost_type) {
                    case 'leads':
                        $fields['display_type'] = 'capture_url';
                        break;
                    case 'woocommerce':
                    case 'easydigitaldownloads':
                        $fields['display_type'] = 'fast';
                        $fields['df_on_all_products'] = 0;
                        $fields['df_on_all_categories'] = 0;
                        $fields['df_on_cart_page'] = 0;
                        $fields['df_on_checkout_page'] = 0;
                        $fields['df_on_home_page'] = 0;
                        switch ($boost_subtype) {
                            case 'transaction':
                                break;
                            case 'specific_transaction':
                                $fields['df_on_all_purchased_products'] = 0;
                                $fields['df_on_all_purchased_categories'] = 0;
                                break;
                            case 'add_to_cart':
                            case 'stock_messages':
                                $fields = array(
                                    'display_type' => 'all_products',
                                    'display_products' => array()
                                );
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
                $fields['desktop'] = 0;
                $fields['desktop_position'] = '';
                $fields['mobile'] = 0;
                $fields['mobile_position'] = '';
                break;
            case '4':
	            $fields['desktop'] = 0;
	            $fields['desktop_position'] = '';
	            $fields['mobile'] = 0;
	            $fields['mobile_position'] = '';
                break;
            default:
                break;
        }
        $fields['name'] = '';
        return $fields;
    }

    public function update_boost_urls($data, $boost_id, $table){
	    if (is_array($data)) {
		    global $wpdb;
		    $wpdb->delete( $table, array( 'boost_id' => $boost_id ) );
		    if ( ! empty( $data['url'] ) && is_array( $data['url'] )
		         && ! empty( $data['url_type'] ) && is_array( $data['url_type'] ) ) {

			    $create_boost_url = array(
				    'boost_id' => $boost_id
			    );
			    foreach ( $data['url'] as $index => $url ) {
				    if ( '' != $url ) {
					    $create_boost_url['url']      = $url;
					    $create_boost_url['url_type'] = ! empty( $data['url_type'][ $index ] ) ? $data['url_type'][ $index ] : 'equals';
					    $wpdb->insert( $table, $create_boost_url );
				    }
			    }
		    }
	    }
    }

    public function update_boost_post_types($data, $boost_id, $table) {
	    if ( is_array( $data ) ) {
		    global $wpdb;
		    $wpdb->delete( $table, array( 'boost_id' => $boost_id ) );
		    if ( ! empty( $data ) ) {
			    $create_boost_post_type = array(
				    'boost_id' => $boost_id
			    );
			    foreach ( $data as $post_type ) {
				    $create_boost_post_type['post_type'] = $post_type;
				    $wpdb->insert( $table, $create_boost_post_type );
			    }
		    }
	    }
    }

    public function update_boost_countries_for_fake($data, $boost_id, $table) {
	    if ( is_array( $data ) ) {
		    global $wpdb;
		    $wpdb->delete( $table, array( 'boost_id' => $boost_id ) );
		    if ( ! empty( $data ) ) {
			    $create_boost_countries_for_fakes = array(
				    'boost_id' => $boost_id
			    );
			    foreach ( $data as $boost_country_for_fakes ) {
				    $create_boost_countries_for_fakes['country'] = $boost_country_for_fakes;
				    $wpdb->insert( $table, $create_boost_countries_for_fakes );
			    }
		    }
	    }
    }

    public function update_boost_specific_pages($data, $boost_id, $table){
	    if ( is_array( $data ) ) {
		    global $wpdb;
		    $wpdb->delete( $table, array( 'boost_id' => $boost_id ) );
		    if ( ! empty( $data ) ) {
			    $create_boost_specific_page = array(
				    'boost_id' => $boost_id
			    );
			    foreach ( $data as $boost_specific_page ) {
				    $create_boost_specific_page['post_id'] = $boost_specific_page;
				    $wpdb->insert( $table, $create_boost_specific_page );
			    }
		    }
	    }
    }

    public function update_boost_taxonomies($data, $boost_id, $table) {
	    if ( is_array( $data ) ) {
		    global $wpdb;
		    $wpdb->delete( $table, array( 'boost_id' => $boost_id ) );
		    if ( ! empty( $data ) ) {
			    $create_boost_taxonomy = array(
				    'boost_id' => $boost_id
			    );
			    foreach ( $data as $taxonomy_id ) {
				    $create_boost_taxonomy['taxonomy_id'] = $taxonomy_id;

				    $wpdb->insert( $table, $create_boost_taxonomy );
			    }
		    }
	    }
    }

    public function copy_table_data($boost_id, $new_boost_id, $table){
    	global $wpdb;
	    $data = $wpdb->get_results("SELECT * FROM $table WHERE `boost_id` = '$boost_id';", ARRAY_A);
	    if (!empty($data)) {
		    foreach ($data as $data_row) {
		    	unset($data_row['id']);
			    $data_row['boost_id'] = $new_boost_id;
			    $wpdb->insert($table, $data_row);
		    }
	    }
    }
}
