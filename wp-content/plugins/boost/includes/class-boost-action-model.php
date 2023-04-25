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
class Boost_Action_Model {

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


    public function create_action_entry($boost_id, $boost_type = '', $user_name = '', $order_id = NULL, $product_id = NULL, $time = NULL, $user_location = NULL, $fake = FALSE) {
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $time = $time ? $time: time();
        $user_name = trim($user_name);
        $user_name = !empty($user_name) ? $user_name : $this->get_user_name($boost_type, $user_name, $order_id);
        if (__('Someone', 'boost') == $user_name || $this->check_user_name_for_banned_words($user_name)) {
            return false;
        }

        $user_location = is_array($user_location) && isset($user_location['country']) && isset($user_location['town']) ? $user_location : $this->get_user_location($boost_type, $order_id);

        $create_action = array(
            'boost_id' => $boost_id,
            'user_name' => $user_name,
            'order_id' => $order_id,
            'product_id' => $product_id,
            'time' => $time,
            'town' => !empty($user_location['town']) ? $user_location['town'] : '',
            'state' => !empty($user_location['state']) ? $user_location['state'] : '',
            'country' => !empty($user_location['country']) ? $user_location['country'] : '',
            'fake' => $fake ? 1 : 0
        );

        if ($wpdb->insert($table_actions_data, $create_action) == false) {
            return false;
        }

        if (!$fake) {
	        $this->delete_actions( array( 'fake' => '1', 'boost_id' => $boost_id ), array( 'time' => 'ASC' ), 1 );
        }

        $new_action_id = $wpdb->insert_id;
        return $new_action_id;
    }

    public function get_user_name($boost_type, $user_name = '', $order = NULL, $check_current_user = true)
    {
    	$user_name = trim($user_name);
        if (empty($user_name)) {
            switch ($boost_type) {
                case 'woocommerce':
                    if (!($order instanceof WC_Order) && is_int($order)) {
                        if (function_exists('wc_get_order')) {
                            $order = wc_get_order($order);
                        }
                    }

                    if ($order instanceof WC_Order) {
                        $billing_first_name = $order->get_billing_first_name();
                        $billing_last_name = $order->get_billing_last_name();
                        $user_name = !empty($billing_first_name) ? $billing_first_name . ' ' . mb_substr($billing_last_name, 0, 1) : $billing_last_name;
						$user_name = trim($user_name);

                        if (empty($user_name)) {
                            $shipping_first_name = $order->get_shipping_first_name();
                            $shipping_last_name = $order->get_shipping_last_name();
                            $user_name = !empty($shipping_first_name) ? $shipping_first_name . ' ' . mb_substr($shipping_last_name, 0, 1) : $shipping_last_name;
                        }
                    }
                    break;
                case 'easydigitaldownloads':
                    if (is_int($order) && function_exists('edd_get_payment_meta')) {
                        $payment_meta = edd_get_payment_meta($order);
                        if (!empty($payment_meta) && !empty($payment_meta['user_info']) && is_array($payment_meta['user_info'])) {
                            $user_info = $payment_meta['user_info'];
                            $user_first_name = !empty($user_info['first_name']) ? $user_info['first_name'] : '';
                            $user_last_name = !empty($user_info['last_name']) ? $user_info['last_name'] : '';
                            $user_name = !empty($user_first_name) ? $user_first_name . ' ' . mb_substr($user_last_name, 0, 1) : $user_last_name;
                        }
                    }
                    elseif ($order instanceof EDD_Payment) {
                        $user_first_name = $order->__get('first_name');
                        $user_last_name = $order->__get('last_name');
                        $user_name = !empty($user_first_name) ? $user_first_name . ' ' . mb_substr($user_last_name, 0, 1) : $user_last_name;                        
                    }
                    break;
                default:
                    break;
            }

	        $user_name = trim($user_name);
            if (empty($user_name) && $check_current_user) {
                $current_user = wp_get_current_user();
                if (0 != $current_user->ID) {
                    $first_name = $current_user->user_firstname;
                    $last_name = $current_user->user_lastname;
                    $user_name = !empty($first_name) ? $first_name . ' ' . mb_substr($last_name, 0, 1) : $last_name;
                }
            }

	        $user_name = trim($user_name);
            if (empty($user_name)) {
                $user_name = __('Someone', 'boost');
            }
        }

        return $user_name;
    }

    public function check_user_name_for_banned_words($user_name){
        global $wpdb;
        $table_banned_words = $wpdb->prefix . TABLE_BANNED_WORDS;

        $user_name_parts = explode(' ', $user_name);
        foreach ($user_name_parts as $index => $user_name_part) {
            if ($wpdb->get_var("SELECT COUNT(*) FROM $table_banned_words WHERE `word` LIKE '$user_name_part'") > 0) {
                return true;
            }
        }
        return false;
    }

    public function get_user_location_from_wc_order($order){
	    $user_location = array(
		    'town' => '',
		    'state' => '',
		    'country' => ''
	    );
    	if (is_numeric($order) && function_exists('wc_get_order')) {
    		$order = wc_get_order($order);
    	}
    	if ($order instanceof WC_Order) {
		    $countries_array = function_exists('WC') ? WC()->countries->get_countries() : array();
		    $country_states_array = function_exists('WC') ? WC()->countries->get_states() : array();
		    $user_location = array(
			    'town'    => $order->get_shipping_city(),
			    'state'   => ! empty( $country_states_array[ $order->get_shipping_country() ][ $order->get_shipping_state() ] )
				    ? $country_states_array[ $order->get_shipping_country() ][ $order->get_shipping_state() ]
				    : $order->get_shipping_state(),
			    'country' => ! empty( $countries_array[ $order->get_shipping_country() ] )
				    ? $countries_array[ $order->get_shipping_country() ]
				    : $order->get_shipping_country()
		    );
		    if ( empty( $user_location['town'] ) && empty( $user_location['state'] ) && empty( $user_location['country'] ) ) {
			    $user_location = array(
				    'town'    => $order->get_billing_city(),
				    'state'   => ! empty( $country_states_array[ $order->get_billing_country() ][ $order->get_billing_state() ] )
					    ? $country_states_array[ $order->get_billing_country() ][ $order->get_billing_state() ]
					    : $order->get_billing_state(),
				    'country' => ! empty( $countries_array[ $order->get_billing_country() ] )
					    ? $countries_array[ $order->get_billing_country() ]
					    : $order->get_billing_country()
			    );
		    }
	    }
	    return $user_location;
    }

    public function get_user_location_from_edd_payment($payment){
	    $user_location = array(
		    'town' => '',
		    'state' => '',
		    'country' => ''
	    );
    	if (is_numeric($payment) && function_exists('edd_get_payment')) {
		    $payment = edd_get_payment($payment);
    	}
    	if ($payment instanceof EDD_Payment) {
		    $payment_billing_address = $payment->__get('address');
		    $user_location = array(
			    'town' => $payment_billing_address['city'],
			    'state' => $payment_billing_address['state'],
			    'country' => $payment_billing_address['country']
		    );
	    }
	    return $user_location;
    }

    public function get_user_location($boost_type = '', $order_id = NULL)
    {
	    $ipInfo = array();

    	if (is_numeric($order_id)) {
    		if ('woocommerce' === $boost_type) {
			    $ipInfo = $this->get_user_location_from_wc_order($order_id);
		    }
		    elseif('easydigitaldownloads' === $boost_type){
			    $ipInfo = $this->get_user_location_from_edd_payment($order_id);
		    }
	    }
	    if (empty($ipInfo['town']) && empty($ipInfo['state']) && empty($ipInfo['country'])) {

//        if ($check_set_cookie && !empty($_COOKIE['BOOST_USER_LOCATION'])) {
		    if ( ! empty( $_COOKIE['STYXKEY-BOOST_USER_LOCATION'] ) ) {
			    $ipInfo = unserialize( stripslashes_deep( $_COOKIE['STYXKEY-BOOST_USER_LOCATION'] ) );
			    if ( ! empty( $ipInfo['town'] ) && ! empty( $ipInfo['state'] ) && ! empty( $ipInfo['country'] ) ) {
				    return $ipInfo;
			    }
		    }
	    }
//        $ip_address = $this->get_client_ip();
//        $ip_address = '2a02:2f01:6010:a94:e496:b8c8:9566:953c';

        // *********** 1 **********
//        if (filter_var($ip_address, FILTER_VALIDATE_IP)) {
//            $response = @file_get_contents('http://www.netip.de/search?query=' . $ip_address);
//            if (!empty($response)) {
//                $patterns = array();
//                $patterns['domain'] = '#Domain: (.*?) #i';
//                $patterns['country'] = '#Country: (.*?)&nbsp;#i';
//                $patterns['state'] = '#State/Region: (.*?)<br#i';
//                $patterns['town'] = '#City: (.*?)<br#i';
//                foreach ($patterns as $key => $pattern) {
//                    $ipInfo[$key] = preg_match($pattern, $response, $value) && !empty($value[1]) ? $value[1] : '';
//                }
//            }
//
//            if (empty($ipInfo['town']) || empty($ipInfo['state']) || empty($ipInfo['country'])) {
//                // *********** 2 **********
//                $freegeoipAPI = 'http://freegeoip.net/json/' . $ip_address;
//                $location_data = json_decode(file_get_contents($freegeoipAPI));
//                $ipInfo['town'] = !empty($location_data->city) ? $location_data->city : (!empty($ipInfo['town']) ? $ipInfo['town'] : '');
//                $ipInfo['state'] = !empty($location_data->region_name) ? $location_data->region_name : (!empty($ipInfo['state']) ? $ipInfo['state'] : '');
//                $ipInfo['country'] = !empty($location_data->country_name) ? $location_data->country_name : (!empty($ipInfo['country']) ? $ipInfo['country'] : '');
//            }
//
//            // *********** 3 **********
////        $location_data = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=' . $ip_address));
////        if (200 == $location_data['geoplugin_status']) {
////            $ipInfo['town'] = $location_data['geoplugin_city'];
////            $ipInfo['state'] = $location_data['geoplugin_regionName'];
////            $ipInfo['country'] = $location_data['geoplugin_countryName'];
////        }
//
//            // *********** 4 **********
//            if (empty($ipInfo['town']) || empty($ipInfo['state']) || empty($ipInfo['country'])) {
//                if (is_callable('curl_init')) {
//                    $curl_handle = curl_init();
//                    curl_setopt($curl_handle, CURLOPT_URL, 'https://whatismyipaddress.com/ip/' . $ip_address);
//                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
//                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
//                    $response = curl_exec($curl_handle);
//                    curl_close($curl_handle);
//                    if (!empty($response)) {
//                        $patterns = array();
//                        $patterns['country'] = '#<th>Country:</th>[^<]*<td>([^<]*).*</td>#i';
//                        $patterns['state'] = '#<th>State/Region:</th>[^<]*<td>([^<]*)</td>#i';
//                        $patterns['town'] = '#<th>City:</th>[^<]*<td>([^<]*)</td>#i';
//                        foreach ($patterns as $key => $pattern) {
//                            $ipInfo[$key] = preg_match($pattern, $response, $value) && !empty($value[1]) ? $value[1] : (!empty($ipInfo[$key]) ? $ipInfo[$key] : '');
//                        }
//                    }
//                }
//            }
//
//            // *********** 5 **********
//            if (empty($ipInfo['town']) || empty($ipInfo['state']) || empty($ipInfo['country'])) {
//                if (is_callable('curl_init')) {
//                    $curl_handle = curl_init();
//                    curl_setopt($curl_handle, CURLOPT_URL, 'https://ipapi.co/' . $ip_address . '/json/');
//                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
//                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
//                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
//                    $response = curl_exec($curl_handle);
//                    curl_close($curl_handle);
//                    if (!empty($response)) {
//	                    $location_data = json_decode($response);
//	                    $ipInfo['town'] = !empty($location_data->city) ? $location_data->city : (!empty($ipInfo['town']) ? $ipInfo['town'] : '');
//	                    $ipInfo['state'] = !empty($location_data->region) ? $location_data->region : (!empty($ipInfo['state']) ? $ipInfo['state'] : '');
//	                    $ipInfo['country'] = !empty($location_data->country_name) ? $location_data->country_name : (!empty($ipInfo['country']) ? $ipInfo['country'] : '');
//                    }
//                }
//            }
//
//            // *********** 6 **********
////            if (empty($ipInfo['town']) || empty($ipInfo['state']) || empty($ipInfo['country'])) {
////                if (is_callable('curl_init')) {
////                    $curl_handle = curl_init();
////                    curl_setopt($curl_handle, CURLOPT_URL, 'http://ip-api.com/json/' . $ip_address);
////                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
////                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
////                    curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36');
////                    $response = curl_exec($curl_handle);
////                    curl_close($curl_handle);
////                    if (!empty($response)) {
////	                    $location_data = json_decode($response);
////	                    $ipInfo['town'] = !empty($location_data->city) ? $location_data->city : (!empty($ipInfo['town']) ? $ipInfo['town'] : '');
////	                    $ipInfo['state'] = !empty($location_data->regionName) ? $location_data->regionName : (!empty($ipInfo['state']) ? $ipInfo['state'] : '');
////	                    $ipInfo['country'] = !empty($location_data->country) ? $location_data->country : (!empty($ipInfo['country']) ? $ipInfo['country'] : '');
////                    }
////                }
////            }
//        }

        if (empty($ipInfo['town'])) {
            $ipInfo['town'] = '';
        }
        if (empty($ipInfo['state'])) {
            $ipInfo['state'] = '';
        }
        if (empty($ipInfo['country'])) {
            $ipInfo['country'] = '';
        }

        return $ipInfo;
    }
    
    public function get_action($id){
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $sql = "SELECT * FROM $table_actions_data
                WHERE `id` = '$id';";

        $action = $wpdb->get_row($sql, ARRAY_A);

        return $action;
    }

    public function check_action_exists($params = array()){
	    global $wpdb;
	    $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

	    $sql = "SELECT * FROM $table_actions_data";

	    $where_clause = '';
	    foreach ($params as $param_key => $param_value) {
	    	if (isset($param_value['compare']) && isset($param_value['value'])) {
			    $where_clause .= (!empty($where_clause)?' AND ':'')."`$param_key` {$param_value['compare']} {$param_value['value']}";
		    }
	    }

	    $sql .= ' WHERE '.(!empty($where_clause)?$where_clause:'1=1').' limit 1;';

	    $actions = $wpdb->get_results($sql);

	    return !empty($actions);
    }

    public function update_action($id, $data)
    {
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $action_data = $this->get_action($id);

        $update_action = array();
        foreach ($action_data as $key => $value) {
            if ('id' == $key) {
                continue;
            }
            if (isset($data[$key]) && $data[$key] != $action_data[$key]) {
                $update_action[$key] = $data[$key];
            }
        }

        if (!empty($update_action) && $wpdb->update($table_actions_data, $update_action, array('id' => $id)) === false) {
            return $action_data;
        }

        $action_data = $this->get_action($id);
        return $action_data;
    }

    public function get_actions($filters = array(), $sort = array(), $limit = NULL, $offset = NULL)
    {
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $where = '1=1';
        foreach ($filters as $key => $value) {
            if (is_array($value)) {
                switch ($value['comparison']) {
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
                }
            } else {
                if ('search_value' == $key) {
                    $where .= " AND (`id` LIKE '%" . $value . "%' OR `user_name` LIKE '%" . $value . "%' OR `country` LIKE '%" . $value . "%' OR `state` LIKE '%" . $value . "%' OR `town` LIKE '%" . $value . "%')";
                } else {
                    $where .= " AND `$key` = '$value'";
                }
            }
        }

        $order = '';
        foreach ($sort as $order_field => $order_type) {
            $order .= " $order_field $order_type,";
        }
        $order = !empty($order) ? substr($order, 0, -1) : 'id ASC';

        $sql = "SELECT $table_actions_data.* FROM $table_actions_data WHERE $where ORDER BY $order";
        $sql .= $limit !== NULL ? ' LIMIT ' . ($offset !== NULL ? "$offset, " : '') . "$limit" : '';

        $actions = $wpdb->get_results($sql, ARRAY_A);
        return $actions;
    }

    public function get_actions_count($filters = array()){
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $where = '1=1';
        foreach ($filters as $key => $value) {
            if (is_array($value) && !empty($value['comparison'])) {
                switch($value['comparison']){
                    case 'lt':
                        $where .= " AND `$key` < '" . $value['value'] . "'";
                        break;
                    case 'mt':
                        $where .= " AND `$key` > '" . $value['value'] . "'";
                        break;
                    case 'eq':
                        $where .= " AND `$key` = '" . $value['value'] . "'";
                        break;
                    case 'lte':
                        $where .= " AND `$key` <= '" . $value['value'] . "'";
                        break;
                    case 'mte':
                        $where .= " AND `$key` >= '" . $value['value'] . "'";
                        break;
                }
            }
            else {
                if ('search_value' == $key) {
                    $where .= " AND (`id` LIKE '%" . $value . "%' OR `user_name` LIKE '%" . $value . "%' OR `country` LIKE '%" . $value . "%' OR `state` LIKE '%" . $value . "%' OR `town` LIKE '%" . $value . "%')";
                } else {
                    $where .= " AND `$key` = '$value'";
                }
            }
        }

        if (!is_numeric($actions_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_actions_data WHERE $where;"))){
            $actions_count = 0;
        }
        return $actions_count;
    }

    public function delete_actions($filters = array(), $sort = array(), $limit = NULL)
    {
        global $wpdb;
        $table_actions_data = $wpdb->prefix . TABLE_ACTIONS;

        $where = '1=1';
        foreach ($filters as $key => $value) {
            if (is_array($value) && !empty($value['comparison'])) {
                switch($value['comparison']){
                    case 'lt':
                        $where .= " AND `$key` < '" . $value['value'] . "'";
                        break;
                    case 'mt':
                        $where .= " AND `$key` > '" . $value['value'] . "'";
                        break;
                    case 'eq':
                        $where .= " AND `$key` = '" . $value['value'] . "'";
                        break;
                    case 'lte':
                        $where .= " AND `$key` <= '" . $value['value'] . "'";
                        break;
                    case 'mte':
                        $where .= " AND `$key` >= '" . $value['value'] . "'";
                        break;
                }
            }
            else {
                $where .= " AND `$key` = '$value'";
            }
        }

        $order = '';
        foreach ($sort as $order_field => $order_type) {
            $order .= " $order_field $order_type,";
        }
        $order = !empty($order) ? substr($order, 0, -1) : 'id ASC';

        $sql = "DELETE FROM $table_actions_data WHERE $where ORDER BY $order";
        $sql .= $limit !== NULL ? " LIMIT $limit" : '';
        $wpdb->query($sql);
    }

    public function generate_all_fake_actions($time, $max_age_notifications_days_time) {
        $boost_model = new Boost_Boost_Model($this->get_plugin_name(), $this->get_version());

        $fake_boosts = $boost_model->get_boosts(array('draft' => '0', 'active' => '1', 'enable_fake' => '1'), array(),true);

        foreach ($fake_boosts as $fake_boost) {
            $this->generate_fake_actions($fake_boost, NULL, $time, $max_age_notifications_days_time);
        }
    }

    public function generate_fake_actions($fake_boost, $count, $time, $max_age_notifications_days_time)
    {
        if (is_numeric($fake_boost)){
            $boost_model = new Boost_Boost_Model($this->get_plugin_name(), $this->get_version());
            $fake_boost = $boost_model->get_boost($fake_boost);
        }
        if (!is_numeric($count)) {
            $actions_count = $this->get_actions_count(
                array('boost_id' => $fake_boost['id'], 'time' => array('comparison' => 'mt', 'value' => $max_age_notifications_days_time)));

            $count = $fake_boost['min_actions_limit'] - $actions_count;
        }
        if ($count > 0) {
            include_once 'lib/fakedata/fake-data.php';
	        $fake_boost['countries_for_fakes'] = !empty($fake_boost['countries_for_fakes'])
		        ? $fake_boost['countries_for_fakes']
		        : array();

			$fake_data_countries = array();
	        foreach($fake_data as $fake_data_item){
		        if (!empty($fake_data_item['country'])){
			        $fake_data_countries[] = $fake_data_item['country'];
		        }
	        }

	        $fake_boost['countries_for_fakes'] = array_intersect($fake_boost['countries_for_fakes'], $fake_data_countries);

            for ($i = 0; $i < $count; $i++) {
	            $rand_fake_data = NULL;
	            if ( ! empty( $fake_boost['countries_for_fakes'] ) ) {
		            $rand_country = $fake_boost['countries_for_fakes'][ array_rand($fake_boost['countries_for_fakes']) ];
		            foreach ( $fake_data as $fake_data_item ) {
			            if ( ! empty( $fake_data_item['country'] ) && $fake_data_item['country'] == $rand_country ) {
				            $rand_fake_data = $fake_data_item;
				            break;
			            }
		            }
	            } else {
		            $rand_fake_data = $fake_data[ array_rand($fake_data) ];
	            }

	            if ( ! empty( $rand_fake_data ) ) {
		            $fake_product_id = null;
		            if (in_array($fake_boost['type'], array('woocommerce', 'easydigitaldownloads'))) {
			            $random_posts = array();
			            $args = array(
				            'post_type'     => 'woocommerce'==$fake_boost['type']?'product':'download',
				            'posts_per_page' => 1,
				            'orderby'        => 'rand',
				            'post_status'    => 'publish'
			            );
			            if (!empty($fake_boost['subtype']) && 'specific_transaction' == $fake_boost['subtype']) {
				            if (!empty($fake_boost['products']) && is_array($fake_boost['products'])) {
					            $products_ids = array_unique(array_map(function($product) {
						            return $product['product_id'];
					            }, $fake_boost['products']));
					            $args['post__in'] = $products_ids;
					            $posts = get_posts( $args );
					            $random_posts = array_merge($random_posts, $posts);
					            unset($args['post__in']);
				            }
				            if (!empty($fake_boost['categories']) && is_array($fake_boost['categories'])) {
					            $categories_ids = array_unique(array_map(function($category) {
						            return $category['category_id'];
					            }, $fake_boost['categories']));
					            $args['tax_query'] = array(
						            array(
							            'taxonomy' => 'woocommerce'==$fake_boost['type']?'product_cat':'download_category',
							            'terms'    => $categories_ids,
						            ),
					            );
					            $posts = get_posts( $args );
					            $random_posts = array_merge($random_posts, $posts);
					            unset($args['tax_query']);
				            }
			            } else {
				            $random_posts = get_posts( $args );
			            }
			            if (!empty($random_posts)) {
				            $fake_product_id = $random_posts[array_rand($random_posts)]->ID;
			            } else {
			            	continue;
			            }
		            }
		            $fake_user_location = array(
			            'country' => $rand_fake_data['country'],
			            'state'   => '',
			            'town'    => ! empty( $rand_fake_data['towns'] ) ? $rand_fake_data['towns'][ array_rand($rand_fake_data['towns']) ] : ''
		            );
		            $fake_user_name     = ! empty( $rand_fake_data['names'] ) ? $rand_fake_data['names'][ array_rand($rand_fake_data['names']) ] : '';
		            $fake_user_surname  = ! empty( $rand_fake_data['surnames'] ) ? $rand_fake_data['surnames'][ array_rand($rand_fake_data['surnames']) ] : '';
		            $fake_user_username = ! empty( $fake_user_name ) ? $fake_user_name . ' ' . mb_substr( $fake_user_surname, 0, 1 ) : $fake_user_surname;
		            $fake_time          = rand( $max_age_notifications_days_time, $time );
		            $this->create_action_entry( $fake_boost['id'], $fake_boost['type'], $fake_user_username, null, $fake_product_id, $fake_time, $fake_user_location, true );
	            }
            }
        }
        elseif($count < 0) {
            $this->delete_actions(array('fake' => '1', 'boost_id' => $fake_boost['id']), array('time' => 'ASC'), abs($count));
        }
    }

    public function cron_job()
    {
        $time = time();
        $ttl = 60 * 60 * 24 * 365; // 1 year

        $this->delete_actions(array('time' => array('comparison' => 'lte', 'value' => $time - $ttl)));

        $setting_model = new Boost_Settings_Model($this->get_plugin_name(), $this->get_version());
	    $max_age_notifications_days = (int)$setting_model->get_setting('dont-show-notifications-after-days');
	    $max_age_notifications_days_time = strtotime(date('Y-m-d', strtotime("-$max_age_notifications_days days", $time)));

        $this->generate_all_fake_actions($time, $max_age_notifications_days_time);
    }

    public function create_actions_for_exist_orders($boost_id, $boost_type, $boost_subtype, $time, $products_ids = array(), $categories_ids = array())
    {
	    $setting_model = new Boost_Settings_Model($this->get_plugin_name(), $this->get_version());
	    $max_age_notifications_days = (int)$setting_model->get_setting('dont-show-notifications-after-days');
	    $max_age_notifications_days_time = strtotime(date('Y-m-d', strtotime("-$max_age_notifications_days days", $time)));

        switch ($boost_type) {
            case 'woocommerce':
                $this->create_wc_actions_for_exist_orders($boost_id, $boost_type, $boost_subtype, $time, $max_age_notifications_days_time, $products_ids, $categories_ids);
                break;
            case 'easydigitaldownloads':
                $this->create_edd_actions_for_exist_orders($boost_id, $boost_type, $boost_subtype, $time, $max_age_notifications_days_time, $products_ids, $categories_ids);
                break;
            default:
                break;
        }
    }
    
    public function create_wc_actions_for_exist_orders($boost_id, $boost_type, $boost_subtype, $time, $max_age_notifications_days_time, $products_ids = array(), $categories_ids = array()){
        $offset = 0;
        $created_count = 0;
        $settings_model = new Boost_Settings_Model($this->get_plugin_name(), $this->get_version());
        $woocommerce_transaction_gather_limit = $settings_model->get_setting('woocommerce-transaction-gather-limit');
        $order_statuses = wc_get_order_statuses();
        unset($order_statuses['wc-failed']);
        while (
            ($existing_orders = wc_get_orders(array(
                'limit' => $woocommerce_transaction_gather_limit,
                'type' => 'shop_order',
                'post_status' => array_keys($order_statuses),
                'date_created' => '>'.$max_age_notifications_days_time,
                'orderby' => 'date',
                'order' => 'DESC',
                'offset' => $offset * $woocommerce_transaction_gather_limit
            ))) && $created_count < $woocommerce_transaction_gather_limit) {
            foreach ($existing_orders as $order) {
                if ($created_count >= $woocommerce_transaction_gather_limit) {
                    break;
                }
                if (!empty($order)) {
                    $order_id = $order->get_id();
                    $order_date_created = $order->get_date_created();
                    $order_date_created = $order_date_created instanceof DateTime ? $order_date_created->getTimestamp() : $time;
                    $user_name = $this->get_user_name($boost_type, '', $order, false);
                    $user_location = $this->get_user_location_from_wc_order($order);
                    if ('transaction' == $boost_subtype) {
                        $this->create_action_entry($boost_id, $boost_type, $user_name, $order_id, NULL, $order_date_created, $user_location);
                        if (++$created_count >= $woocommerce_transaction_gather_limit) {
                            break;
                        }
                    } elseif ('specific_transaction' == $boost_subtype) {
                        if (!empty($products_ids) || !empty($categories_ids)) {
                            $order_items = $order->get_items();
                            foreach ($order_items as $item) {
                                $product_id = $item['product_id'];
                                $product_cats = array();
                                if (function_exists('wc_get_product')) {
                                    $product = wc_get_product($product_id);
                                    if (!empty($product)) {
                                        $product_cats = $product->get_category_ids();
                                    }
                                }
	                            $intersect_cats = array_intersect($categories_ids, $product_cats);
                                if (!empty($products_ids) && in_array($product_id, $products_ids)
                                    || !empty($categories_ids) && !empty($product_cats) && !empty($intersect_cats)
                                ) {
                                    $this->create_action_entry($boost_id, $boost_type, $user_name, $order_id, $product_id, $order_date_created, $user_location);
                                    if (++$created_count >= $woocommerce_transaction_gather_limit) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            $offset++;
        }
    }

    public function create_edd_actions_for_exist_orders($boost_id, $boost_type, $boost_subtype, $time, $max_age_notifications_days_time, $products_ids = array(), $categories_ids = array())
    {
        $created_count = 0;
        $settings_model = new Boost_Settings_Model($this->get_plugin_name(), $this->get_version());
        $edd_transaction_gather_limit = $settings_model->get_setting('edd-transaction-gather-limit');
        $payments = edd_get_payments(array(
                'output' => 'payments',
                'number' => $edd_transaction_gather_limit,
                'orderby' => 'ID',
                'order' => 'DESC',
                'download' => $boost_subtype == 'specific_transaction' ? $products_ids : null,
                'date_query' => array(
                    array(
                        'after' => date('Y-n-d H:i:s', $max_age_notifications_days_time),
                        'before' => date('Y-n-d H:i:s', $time),
                        'inclusive' => true
                    ),
                )
            )
        );
        if (!empty($payments) && is_array($payments)) {
            foreach ($payments as $payment) {
                if ($created_count >= $edd_transaction_gather_limit) {
                    break;
                }
                $payment_id = $payment->__get('ID');
                $payment_date_created = $payment->__get('date');
                $payment_date_created = is_string($payment_date_created) && strtotime($payment_date_created) ? strtotime($payment_date_created) : $time;
                $user_name = $this->get_user_name($boost_type, '', $payment, false);
                $user_location = $this->get_user_location_from_edd_payment($payment);
                if ('transaction' == $boost_subtype) {
                    $this->create_action_entry($boost_id, $boost_type, $user_name, $payment_id, NULL, $payment_date_created, $user_location);
                    if (++$created_count >= $edd_transaction_gather_limit) {
                        break;
                    }
                } elseif ('specific_transaction' == $boost_subtype) {
                    if (!empty($products_ids) || !empty($categories_ids)) {
                        $payment_downloads = $payment->__get('downloads');
                        foreach ($payment_downloads as $payment_download) {
                            $download_id = $payment_download['id'];
                            if (!is_array($download_cats = wp_get_post_terms($download_id, 'download_category', array('fields' => 'ids')))) {
                                $download_cats = array();
                            };
                            $intersect_cats = array_intersect($categories_ids, $download_cats);
                            if (!empty($products_ids) && in_array($download_id, $products_ids)
                                || !empty($categories_ids) && !empty($download_cats) && !empty($intersect_cats)
                            ) {
                                $this->create_action_entry($boost_id, $boost_type, $user_name, $payment_id, $download_id, $payment_date_created, $user_location);
                                if (++$created_count >= $edd_transaction_gather_limit) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
