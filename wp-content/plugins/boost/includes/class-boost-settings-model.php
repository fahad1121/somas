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
class Boost_Settings_Model {

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

    public function get_settings_list() {
        $settings_list = array(
	        'show-boosts-in-random-order' => '0',               // 0/1
	        'close-boosts' => '0',                              // 0/1
            'location-optimization' => '0',                     // 0/1
            'use-leads-time' => '24',                           // hours
            'give-us-credit' => '1',                            // 0/1
            'boost-visibility-time' => '10',                    // seconds
            'approximate-time-between-boosts' => '7',           // seconds
            'dont-show-notifications-after-days' => '14',       // days
            'woocommerce-transaction-gather-limit' => '100',
            'edd-transaction-gather-limit' => '100',
            'translations' => array(
	            'recently' => 'Recently',
	            'verified_by' => 'Verified by',
	            'sec' => 'sec',
	            'secs' => 'secs',
	            'min' => 'min',
	            'mins' => 'mins',
	            'hour' => 'hour',
	            'hours' => 'hours',
	            'day' => 'day',
	            'days' => 'days',
	            'week' => 'week',
	            'weeks' => 'weeks',
	            'month' => 'month',
	            'months' => 'months',
	            'year' => 'year',
	            'years' => 'years',
	            'ago' => 'ago'
	            )
        );
        return $settings_list;
    }

    public function get_setting($setting_name) {
        $settings_list = $this->get_settings_list();
        $setting = NULL;
        if (in_array($setting_name, array_keys($settings_list))) {
            $options = get_option($this->plugin_name . '_options');
	        if ('translations' === $setting_name) {
		        $setting = $this->get_translations_setting($options, $settings_list);
	        }
	        else {
		        $setting = isset( $options[ $setting_name ] ) ? $options[ $setting_name ] : $settings_list[ $setting_name ];
	        }
        }

        return $setting;
    }

    public function get_settings($settings_name_list = array()) {
        if (!is_array($settings_list = $this->get_settings_list())) {
            $settings_list = array();
        }
        $settings_name_list = empty($settings_name_list) ? array_keys($settings_list) : $settings_name_list;
        $options = get_option($this->plugin_name . '_options');

        $settings = array();
        foreach ($settings_list as $setting_name => $default_value) {
            if (in_array($setting_name, $settings_name_list)) {
	            if ('translations' === $setting_name) {
		            $settings[ $setting_name ] = $this->get_translations_setting($options, $settings_list);
	            }
	            else {
		            $settings[ $setting_name ] = isset( $options[ $setting_name ] ) ? $options[ $setting_name ] : $default_value;
	            }
            }
        }

        return $settings;
    }

    public function set_settings($new_settings) {
        $settings = $this->get_settings();

        foreach ($settings as $setting_name => $setting_value) {
	        $settings[ $setting_name ] = isset( $new_settings[ $setting_name ] ) ? $new_settings[ $setting_name ] : '';
        }

        return update_option($this->plugin_name . '_options', $settings);
    }

    public function set_default_settings(){
	    return delete_option($this->plugin_name . '_options');
    }

    public function get_translations_setting($options = null, $settings_list=null){
    	if (empty($options)) {
		    $options = get_option($this->plugin_name . '_options');
	    }
    	if (empty($settings_list)) {
		    $settings_list = $this->get_settings_list();
	    }
	    $settings_list['translations'] = isset($settings_list['translations']) ? $settings_list['translations'] : array();
	    $translations = isset($options['translations']) ? $options['translations'] : $settings_list['translations'];
    	foreach ($settings_list['translations'] as $str => $translation) {
    		if (empty($translations[$str])) {
			    $translations[$str] = $translation;
		    }
	    }
	    return $translations;
    }


}
