<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/includes
 */

class Boost_License {

    public function __construct() {
    }

    public function get_license_key() {
        return get_option('boost_license_key');
    }

    public function get_license_status() {
        return get_option('boost_license_status');
    }

    public function get_license_expires() {
        return get_option('boost_license_expires');
    }
    
    public function update_license($action, $license_key = null){


	    // nothing to do...
	    if( ! in_array( $action, array('activate_license', 'deactivate_license')) ) {
		    return;
	    }

	    if (empty($license_key)) {
		    $license_key = $this->get_license_key() ? $this->get_license_key() : '';
	    }
	    $license_status = $this->get_license_status();
	    $license_expires = $this->get_license_expires();

	    // activate/deactivate license
	    if ( $action == 'activate_license' ) {
		    $license_data = $this->activate( $license_key );
	    } else {
		    $license_data = $this->deactivate($license_key);
	    }

	    $license_message = null;     // data for admin notice

	    if ( is_wp_error( $license_data ) ) {

		    $license_message = array(
			    'class' => 'boost-message-danger',
			    'msg'   => 'Something went wrong during license update request. [' . $license_data->get_error_message() . ']'
		    );

	    } else {

		    if ( empty( $license_status ) && $license_data->license == 'valid' ) {
			    $license_status = 'valid';
		    } elseif ( ! empty( $license_status ) ) {
			    $license_status = $license_data->license;
		    }

		    if ( $license_data->success ) {

			    switch ( $license_data->license ) {
				    case
				    'valid':
					    $license_message = array(
						    'class' => 'boost-message-success',
						    'msg'   => 'Your license is working fine. Good job!'
					    );
					    break;

				    case 'deactivated':
					    $license_message = array(
						    'class' => 'boost-message-success',
						    'msg'   => 'Your license was successfully deactivated for this site.'
					    );
					    break;
			    }

			    $license_expires = strtotime( $license_data->expires );

		    } else {
			    $license_data->error = !empty($license_data->error) ? $license_data->error : 'error';
			    switch ( $license_data->error ) {
				    case 'invalid':                 // key do not exist
				    case 'missing':
				    case 'key_mismatch':
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => "License keys don't match. Make sure you're using the correct license."
					    );
					    break;

				    case 'license_not_activable':   // trying to activate bundle license
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'If you have a bundle package, please use each individual license for your products.'
					    );
					    break;

				    case 'revoked':                 // license key revoked
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'This license was revoked.'
					    );
					    break;

				    case 'no_activations_left':     // no activations left
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'No activations left. Log in to your account to extent your license.'
					    );
					    break;

				    case 'invalid_item_id':
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'Invalid item ID.'
					    );
					    break;

				    case 'item_name_mismatch':      // item names don't match
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => "Item names don't match."
					    );
					    break;

				    case 'expired':                 // license has expired
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'Your License has expired. <a href="'.BOOST_LICENSE_STORE_URL.'/checkout/?edd_license_key=' . esc_url( $license_key ) . '&utm_campaign=admin&utm_source=licenses&utm_medium=renew" target="_blank">Renew it now.</a>'
					    );
					    break;

				    case 'inactive':                // license is not active
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'This license is not active. Activate it now.'
					    );
					    break;

				    case 'disabled':                // license key disabled
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'License key disabled.'
					    );
					    break;

				    case 'site_inactive':
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'The license is not active for this site. Activate it now.'
					    );
					    break;
				    default:
					    $license_message = array(
						    'class' => 'boost-message-danger',
						    'msg'   => 'Error!'
					    );

			    }

			    // add error code
			    $license_message['msg'] .= " [error: $license_data->error]";

		    }

	    }

	    if ( $license_message ) {
		    set_transient( 'boost_license_message', $license_message, 30 * 1 );
	    }


	    update_option('boost_license_key', $license_key);
	    update_option('boost_license_status', $license_status);
	    update_option('boost_license_expires', $license_expires);

	    if( ! is_wp_error( $license_data ) && $license_data->license == 'valid' ) { ?>

		    <script type="text/javascript">
                window.location = '<?php echo BOOST_MAIN_PAGE ?>';
		    </script>

	    <?php }
	    elseif (! is_wp_error( $license_data ) && $license_data->license == 'deactivated') { ?>
		    <script type="text/javascript">
                window.location = '<?php echo BOOST_LICENSE_PAGE ?>';
		    </script>
		<?php }
    }

	public function activate( $license_key ) {

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
//            'item_id'    => BOOST_LICENSE_ITEM_ID,
			'item_name'  => BOOST_LICENSE_ITEM_NAME,
			'url'        => home_url(),
		);

		$response = wp_remote_post( BOOST_LICENSE_STORE_URL, array(
			'timeout'   => 20,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// $license_data->license will be either "valid" or "invalid"
		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	public function deactivate( $license_key ) {

		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
//            'item_id'  => BOOST_LICENSE_ITEM_ID,
			'item_name'  => BOOST_LICENSE_ITEM_NAME,
			'url'        => home_url(),
		);

		$response = wp_remote_post( BOOST_LICENSE_STORE_URL, array(
			'timeout'   => 20,
			'sslverify' => false,
			'body'      => $api_params
		) );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// $license_data->license will be either "deactivated" or "failed"
		return json_decode( wp_remote_retrieve_body( $response ) );

	}

//    public function activate($licenseKey) {
//        update_option('boost_license_key', $licenseKey);
//        delete_transient( 'BOOST_LICENSE_STATUS' );
//
//        $api_params = array(
//            'edd_action' => 'activate_license',
//            'license'    => $licenseKey,
////            'item_id'    => BOOST_LICENSE_ITEM_ID,
//            'item_name'  => BOOST_LICENSE_ITEM_NAME,
//            'url'        => home_url(),
//        );
//
//		$response = wp_remote_post( BOOST_LICENSE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
//		// make sure the response came back okay
//
//		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
//			update_option('boost_license_status', 'invalid');
//            $message = ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
//            return (object) array('message' => $message);
//        } else {
//            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
//			update_option('boost_license_status', $license_data->license);
//			update_option( 'boost_license_expires', $license_data->expires );
//            if ( false === $license_data->success ) {
//                switch( $license_data->error ) {
//                    case 'expired' :
//                        $message = sprintf(
//                            __( 'Your license key expired on %s.' ),
//                            date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
//                        );
//                        break;
//                    case 'revoked' :
//                        $message = __( 'Your license key has been disabled.' );
//                        break;
//                    case 'missing' :
//                        $message = __( 'Invalid license.' );
//                        break;
//                    case 'invalid' :
//                    case 'site_inactive' :
//                        $message = __( 'Your license is not active for this URL.' );
//                        break;
//                    case 'item_name_mismatch' :
//                        $message = sprintf( __( 'This appears to be an invalid license key for %s.' ), BOOST_LICENSE_ITEM_ID );
//                        break;
//                    case 'no_activations_left':
//                        $message = __( 'Your license key has reached its activation limit.' );
//                        break;
//                    case 'license_not_activable':
//                        $message = __( 'The key you entered belongs to a bundle, please use the product specific license key.', 'easy-digital-downloads' );
//                        break;
//                    default :
//                        $message = __( 'An error occurred, please try again.' );
//                        break;
//                }
//                $license_data->message = $message;
//            }
//        }
//
//        return $license_data;
//    }
//
//    public function deactivate() {
//        $licenseKey = $this->get_license_key();
//
//        $api_params = array(
//            'edd_action' => 'deactivate_license',
//            'license'    => $licenseKey,
////            'item_id'  => BOOST_LICENSE_ITEM_ID,
//            'item_name'  => BOOST_LICENSE_ITEM_NAME,
//            'url'        => home_url(),
//        );
//
//	    delete_transient( 'BOOST_LICENSE_STATUS' );
//	    delete_option('boost_license_key');
//	    delete_option( 'boost_license_status');
//	    delete_option( 'boost_license_expires' );
//
//        $response = wp_remote_post( BOOST_LICENSE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
//
//        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
////            $message =  ( is_wp_error( $response ) && ! empty( $response->get_error_message() ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.' );
//            return false;
//        } else {
//            $license_data = json_decode(wp_remote_retrieve_body($response));
//            return $license_data->success;
//        }
//    }

    public function check_license($force = false) {

        if (!$force && get_transient( 'BOOST_LICENSE_STATUS' ) !== false) {
            return;
        }

        $licenseKey = $this->get_license_key();

        if (empty($licenseKey)) {
            return;
        }

        $api_params = array(
            'edd_action' => 'check_license',
            'license'    => $licenseKey,
//            'item_id'    => BOOST_LICENSE_ITEM_ID,
            'item_name'  => BOOST_LICENSE_ITEM_NAME,
            'url'        => home_url(),
        );

        $response = wp_remote_post( BOOST_LICENSE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
	        update_option( 'boost_license_status', 'invalid' );
        } else {
            $license_data = json_decode(wp_remote_retrieve_body($response));
	        update_option( 'boost_license_status', $license_data->license );
	        update_option( 'boost_license_expires', $license_data->expires );
            set_transient( 'BOOST_LICENSE_STATUS', $response, 60*60 );
        }

    }

//    public function check_license_expiration() {
//        $licenseKey = $this->get_license_key();
//
//        $api_params = array(
//            'edd_action' => 'check_license',
//            'license'    => $licenseKey,
//            'item_id'    => BOOST_LICENSE_ITEM_ID,
//            'url'        => home_url(),
//        );
//
//        $response = wp_remote_post( BOOST_LICENSE_STORE_URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
//
//        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
//            return '';
//        }
//
//        $license_data = json_decode(wp_remote_retrieve_body($response));
//
//        if ($license_data->license === 'expired') {
//            return sprintf(
//                __('Your license key expired on %s.'),
//                date_i18n(get_option('date_format'), strtotime($license_data->expires, current_time('timestamp')))
//            );
//        }
//        return '';
//    }

    public function license_valid(){
    	if (get_option('boost_license_status') === 'valid') {
    		return true;
	    }
	    return false;
    }

    public function license_exist(){
    	if (get_option('boost_license_status') === 'valid' || get_option('boost_license_status') === 'expired') {
    		return true;
	    }
	    return false;
    }

    public function license_expired(){
    	if (get_option('boost_license_status') === 'expired') {
    		return true;
	    }
	    return false;
    }

    public function get_license_expired_date(){
	    return get_option('boost_license_expires');
    }

    public function update_plugin(){

		    if( !class_exists( 'Boost_Plugin_Updater' ) ) {
			    // load our custom updater if it doesn't already exist
			    include( BOOST_PLUGIN_PATH . 'includes/class-boost-plugin-updater.php' );
		    }

		    // retrieve our license key from the DB
		    $license_key = trim( get_option( 'boost_license_key' ) );
		    // setup the updater
		    $edd_updater = new Boost_Plugin_Updater( BOOST_LICENSE_STORE_URL, BOOST_PLUGIN_MAIN_FILE_PATH, array(
			    'version' 	=> BOOST_PLUGIN_VERSION,		// current version number
			    'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
//			    'item_id'   => BOOST_LICENSE_ITEM_ID,	// id of this plugin
			    'item_name'  => BOOST_LICENSE_ITEM_NAME,
			    'author' 	=> 'cristian stoicescu',	// author of this plugin
			    'url'       => home_url(),
			    'beta'      => false // set to true if you wish customers to receive update notifications of beta releases
		    ) );
    }

}
