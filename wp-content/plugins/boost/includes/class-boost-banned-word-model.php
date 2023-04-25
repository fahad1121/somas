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
class Boost_BannedWord_Model {

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


    public function create_banned_word($data) {
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

        $create_banned_word = array(
            'word' => !empty($data['word']) ? $data['word'] : ''
        );

        if ($wpdb->insert($table_banned_words_data, $create_banned_word) == false) {
            return false;
        }

        $new_banned_word_id = $wpdb->insert_id;
        return $new_banned_word_id;
    }

    public function get_banned_word($id){
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

        $sql = "SELECT * FROM $table_banned_words_data
                WHERE `id` = '$id';";

        $banned_word = $wpdb->get_row($sql, ARRAY_A);

        return $banned_word;
    }

    public function update_banned_word($id, $data)
    {
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

        $banned_word_data = $this->get_banned_word($id);

        $update_banned_word = array();
        foreach ($banned_word_data as $key => $value) {
            if ('id' == $key) {
                continue;
            }
            if (isset($data[$key]) && $data[$key] != $banned_word_data[$key]) {
                $update_banned_word[$key] = $data[$key];
            }
        }

        if (!empty($update_banned_word) && $wpdb->update($table_banned_words_data, $update_banned_word, array('id' => $id)) === false) {
            return $banned_word_data;
        }

        $banned_word_data = $this->get_banned_word($id);
        return $banned_word_data;
    }

    public function get_banned_words($filters = array(), $sort = array(), $limit = NULL, $offset = NULL)
    {
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

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
                    $where .= " AND (`id` LIKE '%" . $value . "%' OR `word` LIKE '%" . $value . "%')";
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

        $sql = "SELECT $table_banned_words_data.* FROM $table_banned_words_data WHERE $where ORDER BY $order";
        $sql .= $limit !== NULL ? ' LIMIT ' . ($offset !== NULL ? "$offset, " : '') . "$limit" : '';

        $banned_words = $wpdb->get_results($sql, ARRAY_A);
        return $banned_words;
    }

    public function get_banned_words_count($filters = array()){
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

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
                    $where .= " AND (`id` LIKE '%" . $value . "%' OR `word` LIKE '%" . $value . "%')";
                } else {
                    $where .= " AND `$key` = '$value'";
                }
            }
        }

        if (!is_numeric($banned_words_count = $wpdb->get_var("SELECT COUNT(*) FROM $table_banned_words_data WHERE $where;"))){
            $banned_words_count = 0;
        }
        return $banned_words_count;
    }

    public function delete_banned_words($filters = array(), $sort = array(), $limit = NULL)
    {
        global $wpdb;
        $table_banned_words_data = $wpdb->prefix . TABLE_BANNED_WORDS;

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

        $sql = "DELETE FROM $table_banned_words_data WHERE $where ORDER BY $order";
        $sql .= $limit !== NULL ? " LIMIT $limit" : '';
        $wpdb->query($sql);
    }

}
