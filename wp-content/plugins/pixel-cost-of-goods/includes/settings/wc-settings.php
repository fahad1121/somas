<?php
/**
 * PixelCostOfGoods - Settings
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  PixelYourSite.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PixelCostOfGoodsSettings' ) ) :

	class PixelCostOfGoodsSettings extends WC_Settings_Page {

		/**
		 * Constructor.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function __construct() {
			$this->id    = 'pixel_cost_of_goods';
			$this->label = __( 'Cost of Goods by PixelYourSite', 'pixel_cost_of_goods' );
			add_action( 'woocommerce_admin_field_pixel_cost_of_goods', array( $this, 'pixel_cog_setting' ) );
			add_action( 'woocommerce_admin_field_pixel_cost_of_goods_buttons', array( $this, 'pixel_cog_buttons' ) );
			parent::__construct();
		}

		/**
		 * Get sections.
		 *
		 * @return array
		 */
		public function get_sections() {
			$sections = array(
				'' => __( 'Cost of Goods by PixelYourSite', 'pixel_cost_of_goods' ),
			);
			return apply_filters( 'woocommerce_get_sections_' . $this->id, $sections );
		}

		/**
		 * get_settings.
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		public function get_settings( $current_section = '' ) {
			$settings = array();
			$license_status = get_option( 'pixel_cost__license_status' );
			if ( '' === $current_section ) {
				$license = apply_filters(
					'woocommerce_pixel_cost_of_goods_settings',
					array(
						array(
							'title' => __( 'Cost of Goods by PixelYourSite', 'pixel_cost_of_goods' ),
							'type'  => 'title',
							'id'    => 'pixel_cost_of_goods_options',
						),
						array(
							'type' => 'pixel_cost_of_goods',
						),
						array(
							'type' => 'sectionend',
							'id'   => 'pixel_cost_of_goods_options',
						),
					)
				);
				if ($license_status == 'valid' || $license_status == 'expired') {
					$tax_rule = array(
						array(
							'title'    => __( 'TAX rule', 'pixel_cost_of_goods' ),
							'type'     => 'title',
							'id'       => 'pixel_cog_tax_options',
							'desc'	   => __('Price might change based on the country in which your customer is located, so TAX is calculated only counting Profit in Orders!')
						),
						array(
							'title'     => __( 'TAX when calculating the profit', 'pixel_cost_of_goods' ),
							'id'       => '_pixel_cog_tax_calculating',
							'std'     => 'yes', // WooCommerce < 2.0
							'default' => 'yes', // WooCommerce >= 2.0
							'type'     => 'radio',
							'class'    => 'chosen_radio pixel_cog_tax_radio',
							'options' => array(
								'yes'   => __( 'Price WITH tax - COG = Profit', 'pixel_cost_of_goods' ),
								'no'   => __( 'Price WITHOUT tax - COG = Profit', 'pixel_cost_of_goods' )
							),
						),
						array(
							'type'     => 'sectionend',
							'id'       => 'pixel_cog_tax_options',
						),
					);
					$global_rule = array(
						array(
							'title'    => __( 'Global rule', 'pixel_cost_of_goods' ),
							'type'     => 'title',
							'desc'     => __( 'Apply this rule to all your products when no category or individual product rules exist.', 'pixel_cost_of_goods' ),
							'id'       => 'pixel_cost_of_goods_global_options',
						),
						array(
							'title'     => __( 'Cost of Goods type', 'cost_of_goods' ),
							'desc_tip' => __( 'Select Cost of Goods type.', 'pixel_cost_of_goods' ),
							'id'       => '_pixel_cost_of_goods_cost_type',
							'default'  => array(),
							'type'     => 'select',
							'class'    => 'chosen_select',
							'options' => array(
								'fix'   => __( 'Fix', 'pixel_cost_of_goods' ),
								'percent'   => __( 'Percent', 'pixel_cost_of_goods' )
							),
						),
						array(
							'title'    => __( 'Cost of Goods value', 'pixel_cost_of_goods' ),
							'type'     => 'text',
							'id'       => '_pixel_cost_of_goods_cost_val',
							'default'  => '',
						),
						array(
							'type'     => 'sectionend',
							'id'       => 'pixel_cost_of_goods_global_options',
						),
					);
					$buttons_group = apply_filters(
						'woocommerce_pixel_cost_of_goods_settings_buttons',
						array(
							array(
								'type' => 'pixel_cost_of_goods_buttons',
							)
						)
					);
				} else {
					$tax_rule = array();
					$global_rule  = array();
					$buttons_group = array();
				}

				$settings = array_merge( $license, $tax_rule, $global_rule, $buttons_group);
			}

			return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings, $current_section );
		}

		/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section;

			$settings = $this->get_settings( $current_section );
			WC_Admin_Settings::output_fields( $settings );

		}

		/**
		 * Output pixel_cost_of_goods settings.
		 */
		public function pixel_cog_setting() {
			$license = get_option('pixel_cost_of_goods_license');
			?>
			<div class="pixel_cog_popup popup"></div>
			<tr valign="top">
				<td class="pixel_cog_widefat" colspan="2">
					<div class="pixel_cog_setting_wrapper">
						<div class="setting_wrapper-row wc-item-license-save">
							<?php if ($license) {
								?>
								<div>
									<label for="pixel_cog_license" class="pixel_cog_license"><?php _e( 'Your license key:', 'pixel_cost_of_goods' ); ?></label>
									<input name="pixel_cog_license" id="pixel_cog_license" type="text" value="*****************************************" />
								</div>
								<div class="wc-license-btn">
									<button type="button" class="button-primary button-block wc-license-deactivate" tabindex="0" aria-hidden="false"><?php esc_html_e( 'Deactivate your license', 'pixel_cost_of_goods' ); ?></button>
								</div>
								<?php
							} else {
								?>
								<div>
									<label for="pixel_cog_license" class="pixel_cog_license"><?php _e( 'Add your license key:', 'pixel_cost_of_goods' ); ?></label>
									<input name="pixel_cog_license" id="pixel_cog_license" type="text" value="" />
								</div>
								<div class="wc-license-btn">
									<button type="button" class="button-primary button-block wc-license-save" tabindex="0" aria-hidden="false" aria-label="<?php echo __( 'Save Cost of Goods by PixelYourSite License', 'pixel_cost_of_goods' ); ?>"><?php esc_html_e( 'Activate your license', 'pixel_cost_of_goods' ); ?></button>
								</div>
								<?php
							} ?>
						</div>
					</div>
				</td>
			</tr>
			<?php
		}

		/**
		 * Output pixel_cog_buttons settings.
		 */
		public function pixel_cog_buttons() {
			$cron = false;
			if( wp_next_scheduled( 'pixel_cog_calculate_cron' ) ) {
				$cron = true;
			}
			?>
			<tr valign="top">
				<td class="pixel_cog_widefat" colspan="2">
					<div class="pixel_cog_setting_wrapper">
						<div class="setting_wrapper-row">
							<hr/>
							<p><?php _e( 'Calculate cost of goods and profit for your existing orders:', 'pixel_cost_of_goods' ); ?></p>
							<a href="#" class="button-primary <?php echo $cron ? 'processing' : ''; ?>" id="calculate_cost_btn" target="_blank"><?php echo $cron ? 'Calculate in process...' : 'Calculate'; ?></a>
							<span><?php _e( 'Depending on the numbers of order you have, this job can take a while.', 'pixel_cost_of_goods' ); ?></span>
							<p class="updated"></p>
						</div>
						<?php
							if (get_plugins( '/ni-woocommerce-cost-of-goods' ) && is_plugin_active( 'ni-woocommerce-cost-of-goods/ni-woocommerce-cost-of-goods.php' )) {
								?>
								<div class="setting_wrapper-row">
									<hr/>
									<p><?php _e( 'We detected the Ni WooCommerce cost of goods plugin. Do you want to import the cost from this plugin?', 'pixel_cost_of_goods' ); ?></p>
									<a href="#" class="button-primary product_cog_import_plugins" data-product-metakey="_ni_cost_goods"><?php _e( 'import from Ni WooCommerce cost of goods', 'pixel_cost_of_goods' ); ?></a>
								</div>
								<?php
							}
						?>
						<?php
						if (get_plugins( '/cost-of-goods-for-woocommerce' ) && is_plugin_active( 'cost-of-goods-for-woocommerce/cost-of-goods-for-woocommerce.php' )) {
							?>
							<div class="setting_wrapper-row">
								<hr/>
								<p><?php _e( 'We detected the Cost of Goods for WooCommerce plugin. Do you want to import the cost from this plugin?', 'pixel_cost_of_goods' ); ?></p>
								<a href="#" class="button-primary product_cog_import_plugins" data-product-metakey="_alg_wc_cog_cost"><?php _e( 'import from Cost of Goods for WooCommerce', 'pixel_cost_of_goods' ); ?></a>
							</div>
							<?php
						}
						?>
						<?php
						if (get_plugins( '/atum-stock-manager-for-woocommerce' ) && is_plugin_active( 'atum-stock-manager-for-woocommerce/atum-stock-manager-for-woocommerce.php' )) {
							?>
							<div class="setting_wrapper-row">
								<hr/>
								<p><?php _e( 'We detected the ATUM Inventory Management for WooCommerce plugin. Do you want to import the cost from this plugin?', 'pixel_cost_of_goods' ); ?></p>
								<a href="#" class="button-primary product_cog_import_plugins" data-product-metakey="purchase_price"><?php _e( 'import from ATUM Inventory Management for WooCommerce', 'pixel_cost_of_goods' ); ?></a>
							</div>
							<?php
						}
						?>
						<div class="setting_wrapper-row">
							<hr/>
							<p><?php _e( 'Export Products “cost of goods” as CSV', 'pixel_cost_of_goods' ); ?></p>
							<a href="#" class="button-primary" id="product_csv_export"><?php _e( 'Export Products CSV', 'pixel_cost_of_goods' ); ?></a>
						</div>
						<div class="setting_wrapper-row">
							<hr/>
							<p><?php _e( 'Export Product Categories “cost of goods” as CSV', 'pixel_cost_of_goods' ); ?></p>
							<a href="#" class="button-primary" id="product_csv_export_cat"><?php _e( 'Export Product Categories CSV', 'pixel_cost_of_goods' ); ?></a>
						</div>
						<div class="setting_wrapper-row">
							<hr/>
							<p><?php _e( sprintf( __( 'Import Products "cost of goods" from CSV. %s', 'pixel_cost_of_goods' ),
									'<a href="' . PIXEL_COG_URL . '/pixel_products_cog_import_example.csv" target="_blank" >' . __( 'Download sample', 'pixel_cost_of_goods' ) . '</a>' ), 'pixel_cost_of_goods' ); ?></p>
							<a href="#" class="button-primary" id="product_csv_import"><?php _e( 'Import Products CSV', 'pixel_cost_of_goods' ); ?></a>
						</div>
						<div class="setting_wrapper-row">
							<hr/>
							<p><?php _e( sprintf( __( 'Import Product Categories "cost of goods" from CSV. %s', 'pixel_cost_of_goods' ),
									'<a href="' . PIXEL_COG_URL . '/pixel_products_cat_cog_import_example.csv" target="_blank" >' . __( 'Download sample', 'pixel_cost_of_goods' ) . '</a>' ), 'pixel_cost_of_goods' ); ?></p>
							<a href="#" class="button-primary" id="product_csv_import_cat"><?php _e( 'Import Product Categories CSV', 'pixel_cost_of_goods' ); ?></a>
						</div>
					</div>
					<?php
					if ( (get_plugins( '/pixelyoursite-pro' ) && is_plugin_active( 'pixelyoursite-pro/pixelyoursite-pro.php' )) || ( get_plugins( '/pixelyoursite' ) && is_plugin_active('pixelyoursite/facebook-pixel-master.php'))) {
						?>
						<div class="pixel-cog-notice-wrapper">
							<img src="<?php echo PIXEL_COG_ASSETS.'/images/pys-square-logo-small.png'; ?>" class="pys-notice-logo" alt=" pys-square-logo" />
							<div class="notice-content">
								<h4>Facebook Pixel: You can configure PixelYourSite to deduct the cost of goods from WooCommerce CompleteRegistration, Purchase, InitiateCheckout, AddToCart, and ViewContent events value. <a href="<?php echo get_site_url().'/wp-admin/admin.php?page=pixelyoursite&tab=woo'; ?>" target="_blank">Click to configure</a>.</h4>
							</div>
						</div>
						<?php
					} else if ( (get_plugins( '/pixelyoursite-pro' ) && !is_plugin_active( 'pixelyoursite-pro/pixelyoursite-pro.php' )) || (  get_plugins( '/pixelyoursite' ) && !is_plugin_active('pixelyoursite/facebook-pixel-master.php'))) {
						$activation_link = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'activate-plugin',
									'plugin' => 'pixelyoursite'
								),
								admin_url( 'plugins.php' )
							),
							'activate-plugin_pixelyoursite'
						);
						if (get_plugins( '/pixelyoursite-pro' )) {
							?>
							<div class="pixel-cog-notice-wrapper">
								<img src="<?php echo PIXEL_COG_ASSETS.'/images/pys-square-logo-small.png'; ?>" class="pys-notice-logo" alt=" pys-square-logo" />
								<div class="notice-content">
									<h4>Facebook Pixel: with PixelYourSite you can deduct the cost of goods from WooCommerce events value. <a href="<?php echo $activation_link; ?>" target="_blank">Activate PixelYourSite Pro</a>.</h4>
								</div>
							</div>
							<?php
						} else if (get_plugins( '/pixelyoursite' )) {
							?>
							<div class="pixel-cog-notice-wrapper">
								<img src="<?php echo PIXEL_COG_ASSETS.'/images/pys-square-logo-small.png'; ?>" class="pys-notice-logo" alt=" pys-square-logo" />
								<div class="notice-content">
									<h4>Facebook Pixel: with PixelYourSite you can deduct the cost of goods from WooCommerce events value. <a href="<?php echo $activation_link; ?>" target="_blank">Activate your PixelYourSite plugin</a>, or <a href="//www.pixelyoursite.com" target="_blank">get the pro</a>.</h4>
								</div>
							</div>
							<?php
						}
					} else {
						$install_link = wp_nonce_url(
							add_query_arg(
								array(
									'action' => 'install-plugin',
									'plugin' => 'pixelyoursite'
								),
								admin_url( 'update.php' )
							),
							'install-plugin_pixelyoursite'
						);
						?>
						<div class="pixel-cog-notice-wrapper">
							<img src="<?php echo PIXEL_COG_ASSETS.'/images/pys-square-logo-small.png'; ?>" class="pys-notice-logo" alt=" pys-square-logo" />
							<div class="notice-content">
								<h4>Facebook Pixel: with PixelYourSite, you can deduct the cost of goods from the Facebook WooCommerce events value. Install the <a href="<?php echo $install_link; ?>" target="_blank">free version</a> or <a href="https://www.pixelyoursite.com/" target="_blank">get the pro</a>.</h4>
							</div>
						</div>
						<?php
					}
					?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Generate an activation URL for a plugin like the ones found in WordPress plugin administration screen.
		 *
		 * @param  string $plugin A plugin-folder/plugin-main-file.php path (e.g. "my-plugin/my-plugin.php")
		 *
		 * @return string         The plugin activation url
		 */
		function generatePluginActivationLinkUrl($plugin)
		{
			// the plugin might be located in the plugin folder directly

			if (strpos($plugin, '/')) {
				$plugin = str_replace('/', '%2F', $plugin);
			}

			$activateUrl = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin);

			// change the plugin request to the plugin to pass the nonce check
			$_REQUEST['plugin'] = $plugin;
			$activateUrl = wp_nonce_url($activateUrl, 'activate-plugin_' . $plugin);

			return $activateUrl;
		}

		/**
		 * Save settings.
		 */
		public function save() {
			global $current_section;
			// Save settings fields based on section.
			WC_Admin_Settings::save_fields( $this->get_settings( $current_section ) );

			if ( $current_section ) {
				do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
			}
		}

	}

endif;

return new PixelCostOfGoodsSettings();
