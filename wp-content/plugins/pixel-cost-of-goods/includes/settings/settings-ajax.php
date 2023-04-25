<?php
/**
 * PixelSettingsAjaxCostOfGoods
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * PixelSettingsAjaxCostOfGoods
 */
class PixelSettingsAjaxCostOfGoods {

	/**
	 * Toggle pixel_cost_of_goods_enable on or off via AJAX.
	 */
	function pixel_cog_toggle_enabled() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$enabled = get_option( 'pixel_cost_of_goods_enable', 'no' );

			if ( ! wc_string_to_bool( $enabled ) ) {
				update_option( 'pixel_cost_of_goods_enable', 'yes' );
			} else {
				// Disable the gateway.
				update_option( 'pixel_cost_of_goods_enable', 'no' );
			}

			wp_send_json_success( ! wc_string_to_bool( $enabled ) );
			wp_die();
		}

		wp_send_json_error( 'invalid_data' );
		wp_die();
	}

	/**
	 * Toggle pixel_cog_toggle_license on or off via AJAX.
	 */
	function pixel_cog_toggle_license() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			$license_key = $_POST['key'];
			$license_action = $_POST['status'];
			$license_status = get_option( 'pixel_cost__license_status' );
			$license_expires = get_option( 'pixel_cost__license_expires' );
			$admin_notice = array();
			if ( $license_action == 'activate' ) {
				$license_data = $this->licenseActivate( $license_key );
			} else {
				$license_data = $this->licenseDeactivate( get_option( 'pixel_cost_of_goods_license' ) );
				update_option( 'pixel_cost_of_goods_license', '' );
				update_option( 'pixel_cost__license_status', '' );
				update_option( 'pixel_cost__license_expires', '' );
				wp_send_json_success( $license_data );
				wp_die();
			}
			if ( $license_action == 'activate' ) {
			if ( is_wp_error( $license_data ) ) {

				$admin_notice = array(
					'class' => 'danger',
					'msg'   => 'Something went wrong during license update request. [' . $license_data->get_error_message() . ']'
				);

				wp_send_json_error( $license_data->get_error_message());
				wp_die();

			} else {

				/**
				 * Overwrite empty license status only on successful activation.
				 * For existing status overwrite with any value except error.
				 */
				if ( empty( $license_status ) && $license_data->license == 'valid' ) {
					$license_status = 'valid';
				} elseif ( ! empty( $license_status ) ) {
					$license_status = $license_data->license;
				}

				if ( $license_data->success ) {

					switch ( $license_data->license ) {
						case
						'valid':
							$admin_notice = array(
								'class' => 'success',
								'msg'   => 'Your license is working fine. Good job!'
							);
							break;

						case 'deactivated':
							$admin_notice = array(
								'class' => 'success',
								'msg'   => 'Your license was successfully deactivated for this site.'
							);
							break;
					}

					$license_expires = strtotime( $license_data->expires );

				} else {

					switch ( $license_data->error ) {
						case 'invalid':                 // key do not exist
						case 'missing':
						case 'key_mismatch':
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => "License keys don't match. Make sure you're using the correct license."
							);
							break;

						case 'license_not_activable':   // trying to activate bundle license
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'If you have a bundle package, please use each individual license for your products.'
							);
							break;

						case 'revoked':                 // license key revoked
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'This license was revoked.'
							);
							break;

						case 'no_activations_left':     // no activations left
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'No activations left. Log in to your account to extent your license.'
							);
							break;

						case 'invalid_item_id':
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'Invalid item ID.'
							);
							break;

						case 'item_name_mismatch':      // item names don't match
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => "Item names don't match."
							);
							break;

						case 'expired':                 // license has expired
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'Your License has expired. <a href="http://www.pixelyoursite.com/checkout/?edd_license_key=' . urlencode( $license_key ) . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew" target="_blank">Renew it now.</a>'
							);
							break;

						case 'inactive':                // license is not active
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'This license is not active. Activate it now.'
							);
							break;

						case 'disabled':                // license key disabled
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'License key disabled.'
							);
							break;

						case 'site_inactive':
							$admin_notice = array(
								'class' => 'danger',
								'msg'   => 'The license is not active for this site. Activate it now.'
							);
							break;

					}

					// add error code
					$admin_notice['msg'] .= " [error: $license_data->error]";

					wp_send_json_error( $license_data->error);
					wp_die();

				}

			}

			if ( ! empty( $admin_notice ) ) {
				set_transient( "pixel_cog__license_notice", $admin_notice, 60 * 5 );
			}

			update_option( 'pixel_cost_of_goods_license', $license_key );
			update_option( 'pixel_cost__license_status', $license_status );
			update_option( 'pixel_cost__license_expires', $license_expires );

			wp_send_json_success( $license_data );
			wp_die();
			}
		}
	}

	/**
	 * @param string          $license_key
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	function licenseActivate( $license_key ) {

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_name'  => PIXEL_COG_ITEM_NAME,
			'url'        => home_url()
		);

		$response = wp_remote_post( 'https://www.pixelyoursite.com', array(
			'timeout'   => 120,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * @param string          $license_key
	 *
	 * @return array|mixed|object|\WP_Error
	 */
	function licenseDeactivate( $license_key ) {

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_name'  => PIXEL_COG_ITEM_NAME,
			'url'        => home_url()
		);

		$response = wp_remote_post( 'https://www.pixelyoursite.com', array(
			'timeout'   => 120,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * Calculate orders cost via AJAX.
	 */

	function pixel_cog_calculate_cost() {
		if ( current_user_can( 'manage_woocommerce' ) ) {
			if( !wp_next_scheduled( 'pixel_cog_calculate_cron' ) ) {
				wp_schedule_single_event( time(), 'pixel_cog_calculate_cron' );
			} else {
				wp_send_json_error( ' cron job is already scheduled' );
			}
		}
	}

}
