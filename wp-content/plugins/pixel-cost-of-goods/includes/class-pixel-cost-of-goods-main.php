<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'PixelCostOfGoods' ) ) :

	/**
	 * Main CogAddOn Class
	 *
	 * @class   PixelCostOfGoods
	 * @version 1.0.0
	 * @since   1.0.0
	 * @author  PixelYourSite.
	 */
	final class PixelCostOfGoods {

		/**
		 * Options values
		 *
		 * @var array
		 */
		private $values = array();

		/**
		 * Database option key
		 *
		 * @var string
		 */
		private $option_key = '';

		/**
		 * Default options values
		 *
		 * @var array
		 */
		private $defaults = array();

		/**
		 * List of all options
		 *
		 * @var array
		 */
		private $options = array();

		/**
		 * Plugin version.
		 *
		 * @var   string
		 * @since 1.0.0
		 */
		public $version = PIXEL_COG_VERSION;

		/**
		 * @var   PixelCostOfGoods The single instance of the class
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main PixelCostOfGoods Instance
		 *
		 * Ensures only one instance of PixelCostOfGoods is loaded or can be loaded.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @author  PixelYourSite.
		 * @static
		 * @return  PixelCostOfGoods - Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * PixelCostOfGoods Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @access  public
		 */
		function __construct() {

			// Set up localisation
			load_plugin_textdomain( 'pixel_cost_of_goods', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );

			// Include required files
			$this->includes();

			// Settings & Scripts
			if ( is_admin() ) {
				$this->admin();
			}
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @param   mixed $links
		 * @return  array
		 */
		function action_links( $links ) {
			$custom_links = array();
			$custom_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=pixel_cost_of_goods' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>';
			return array_merge( $custom_links, $links );
		}

		/**
		 * version_updated.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function version_updated() {
			update_option( 'pixel_cost_of_goods_version', $this->version );
		}

		/**
		 * admin.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function admin() {
			// Action links
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );
			// Settings
			add_filter( 'woocommerce_get_settings_pages', array( $this, 'add_woocommerce_settings_tab' ) );
			$this->settings = array();
			// Version updated
			if ( get_option( 'pixel_cost_of_goods_version', '' ) !== $this->version ) {
				add_action( 'admin_init', array( $this, 'version_updated' ) );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function includes() {
			// Core
			$this->core = require_once( 'core.php' );
		}

		/**
		 * Add Cost of Goods settings tab to WooCommerce settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function add_woocommerce_settings_tab( $settings ) {
			$settings[] = require_once( 'settings/wc-settings.php' );
			return $settings;
		}

		/**
		 * Get the plugin url.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @author  PixelYourSite.
		 * @return  string
		 */
		function plugin_url() {
			return untrailingslashit( plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 * @author  PixelYourSite.
		 * @return  string
		 */
		function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

	}

endif;

if ( ! function_exists( 'pixel_wc_cog' ) ) {
	/**
	 * Returns the main instance of PixelCostOfGoods to prevent the need to use globals.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 * @author  PixelYourSite.
	 * @return  PixelCostOfGoods
	 */
	function pixel_wc_cog() {
		return PixelCostOfGoods::instance();
	}
}

pixel_wc_cog();