<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
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
class Boost {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Boost_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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
	public function __construct() {

		$this->plugin_name = 'boost';
		$this->version = BOOST_PLUGIN_VERSION;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->check_upgrades();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Boost_Loader. Orchestrates the hooks of the plugin.
	 * - Boost_i18n. Defines internationalization functionality.
	 * - Boost_Admin. Defines all hooks for the admin area.
	 * - Boost_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-boost-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-boost-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-action-model.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-boost-model.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-settings-model.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-notification-model.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-banned-word-model.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-boost-license-model.php';

		$this->loader = new Boost_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Boost_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Boost_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Boost_Admin( $this->get_plugin_name(), $this->get_version() );

		$action_model = new Boost_Action_Model($this->get_plugin_name(), $this->get_version());

		$license = new Boost_License();

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'boost_main_menu' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );


//		$this->loader->add_action('init', $license, 'check_license');
		$this->loader->add_action('admin_init', $license, 'update_plugin');

		if ($license->license_exist()) {
			$this->loader->add_action('wp_ajax_boost_search_items_ajax', $plugin_admin, 'boost_search_items_ajax_handler');

			$this->loader->add_action('wp_ajax_boost_edit_item_ajax', $plugin_admin, 'boost_edit_item_ajax_handler');

			$this->loader->add_action('wp_ajax_boost_search_forms_ajax', $plugin_admin, 'boost_search_forms_ajax_handler');
			$this->loader->add_action('init', $plugin_admin, 'boost_cronstarter_activation');
			$this->loader->add_action('boost_fake_boosts_job', $action_model, 'cron_job');
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	    $license = new Boost_License();

	    if ($license->license_exist()) {
            $plugin_public = new Boost_Public( $this->get_plugin_name(), $this->get_version() );

            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
            $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

            $this->loader->add_action('wp_ajax_nopriv_boost_submit_form_ajax', $plugin_public, 'boost_submit_form_ajax_handler');
            $this->loader->add_action('wp_ajax_boost_submit_form_ajax', $plugin_public, 'boost_submit_form_ajax_handler');

            $this->loader->add_action('wp_ajax_nopriv_boost_notifications_part_load_ajax', $plugin_public, 'boost_notifications_part_load_ajax_handler');
            $this->loader->add_action('wp_ajax_boost_notifications_part_load_ajax', $plugin_public, 'boost_notifications_part_load_ajax_handler');

            $this->loader->add_action('init', $plugin_public, 'action_boost_set_session_cookie');
            $this->loader->add_action('init', $plugin_public, 'action_boost_set_location_cookie');


            $this->loader->add_action( 'woocommerce_update_order', $plugin_public, 'action_woocommerce_update_order', 10, 1 );
            $this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'action_woocommerce_add_to_cart', 10, 6 );

            $this->loader->add_action( 'edd_complete_purchase', $plugin_public, 'action_edd_complete_purchase', 10, 1 );
            $this->loader->add_action( 'edd_complete_download_purchase', $plugin_public, 'action_edd_complete_download_purchase', 10, 3 );
            $this->loader->add_action( 'edd_post_add_to_cart', $plugin_public, 'action_edd_post_add_to_cart', 10, 3 );
        }
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Boost_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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

	private function check_upgrades() {

		$version = get_option( 'boost_version', false );

		// data scheme is actual
		if( $version && version_compare( $version, $this->version, '>=' ) ) {
			return;
		}

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/upgrade.php';

		if( version_compare( $version, '1.0.3', '<' ) ) {
			boost_upgrade_to_1_0_3();
		}

		update_option( 'boost_version', $this->version );

	}

}
