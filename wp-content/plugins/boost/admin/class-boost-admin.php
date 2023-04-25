<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://url.url
 * @since      1.0.0
 *
 * @package    Boost
 * @subpackage Boost/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Boost
 * @subpackage Boost/admin
 * @author     cristian stoicescu <email@email.email>
 */
class Boost_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $boost_model;

	private $settings_model;

	private $notification_model;

	private $action_model;

	private $banned_word_model;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->boost_model = new Boost_Boost_Model($plugin_name, $version);
		$this->settings_model = new Boost_Settings_Model($plugin_name, $version);
		$this->notification_model = new Boost_Notification_Model($plugin_name, $version);
		$this->action_model = new Boost_Action_Model($this->plugin_name, $this->version);
		$this->banned_word_model = new Boost_BannedWord_Model($this->plugin_name, $this->version);

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Boost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name . '_select2', plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '_fonts', plugin_dir_url( __FILE__ ) . '../includes/css/fonts.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '_notifications', plugin_dir_url( __FILE__ ) . '../includes/css/notifications-style.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_fonts_public', plugin_dir_url( __FILE__ ) . '../includes/css/notifications-fonts.css', array(), $this->version, 'all' );

//		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/boost-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_dashboard', plugin_dir_url( __FILE__ ) . 'css/boost-dashboard.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_settings', plugin_dir_url( __FILE__ ) . 'css/boost-settings.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_form_steps', plugin_dir_url( __FILE__ ) . 'css/boost-form-steps.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_select2_boost_theme', plugin_dir_url( __FILE__ ) . 'css/select2-boost-theme.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_captured_and_banned', plugin_dir_url( __FILE__ ) . 'css/boost-captured-and-banned.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_license', plugin_dir_url( __FILE__ ) . 'css/boost-license.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '_boost_classes', plugin_dir_url( __FILE__ ) . 'css/boost-classes.css', array(), $this->version, 'all' );



	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Boost_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Boost_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name . '_admin', plugin_dir_url( __FILE__ ) . 'js/boost-admin.js', array( 'jquery', 'jquery-ui-droppable' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '_select2', plugin_dir_url( __FILE__ ) . 'js/select2.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '_admin_ajax', plugin_dir_url( __FILE__ ) . 'js/boost-admin-ajax.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name . '_admin_ajax', 'BOOST_Ajax', array(
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'search_items_nonce' => wp_create_nonce( 'boost_search_items_nonce' ),
				'edit_item_nonce' => wp_create_nonce( 'boost_edit_item_nonce' ),
				'search_forms_nonce' => wp_create_nonce( 'boost_search_forms_nonce' )
			)
		);

	}

	/**
	 * Register the Settings page.
	 *
	 * @since    1.0.0
	 */
	public function boost_main_menu() {

        $license = new Boost_License();

        if ($license->license_exist()) {
            add_menu_page( __('Boost', 'boost'), __('Boost', 'boost'), 'manage_options', 'boost_main',
                array($this, 'display_plugin_main_page'));
            add_submenu_page('boost_main', __('Your Boosts', 'boost'), __('Your Boosts', 'boost'), 'manage_options',
                'boost_main', array($this, 'display_plugin_main_page'));
            add_submenu_page('boost_main', __('Make Boost', 'boost'), __('Make Boost', 'boost'), 'manage_options',
                'boost_make_boost', array($this, 'display_plugin_make_boost_page'));
            add_submenu_page('boost_main', __('Captured Data', 'boost'), __('Captured Data', 'boost'), 'manage_options',
                'boost_action_list', array($this, 'display_plugin_action_list_page'));
            add_submenu_page('boost_main', __('Banned Words', 'boost'), __('Banned Words', 'boost'), 'manage_options',
                'boost_banned_word_list', array($this, 'display_plugin_banned_word_list_page'));
            add_submenu_page('boost_main', __('Settings', 'boost'), __('Settings', 'boost'), 'manage_options',
                'boost_settings', array($this, 'display_plugin_settings_page'));
        } else {
            add_menu_page( __('Boost', 'boost'), __('Boost', 'boost'), 'manage_options', 'boost_main',
                array($this, 'display_plugin_license_page'));
        }

		add_submenu_page('boost_main', __('License', 'boost'), __('License', 'boost'), 'manage_options', 'boost_license',  array($this, 'display_plugin_license_page'));

	}
	
	/**
	 * Callback function for the main plugin page.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_main_page() {
		if (!empty($_POST['submit'])) {
			$action = !empty($_POST['submit']) ? $_POST['submit'] : '';
			switch ($action) {
				case 'copy_boost':
					$boost_id = !empty($_POST['boost_id']) ? $_POST['boost_id'] : 0;
					$this->boost_model->copy_boost($boost_id);
					break;
				case 'delete_boost':
					$boost_id = !empty($_POST['boost_id']) ? $_POST['boost_id'] : 0;
					$this->boost_model->delete_boost($boost_id);
					break;
                case 'bulk_delete':
                    $boost_ids = array_merge(
                            array(),
                        (!empty($_POST['boosts']) && is_array($_POST['boosts']) ? $_POST['boosts'] : array()),
                        (!empty($_POST['draft_boosts']) && is_array($_POST['draft_boosts']) ? $_POST['draft_boosts'] : array())
                    );
                    foreach($boost_ids as $boost_id){
	                    $this->boost_model->delete_boost($boost_id);
                    }
                    break;
				case 'enable_disable_boost':
					$boost_id = !empty($_POST['boost_id']) ? $_POST['boost_id'] : 0;
					if (!empty($boost_id)) {
						$boost = $this->boost_model->get_boost($boost_id);
						if (!empty($boost)){
							$update_boost = array(
								'type' => $boost['type'],
								'draft' => 0,
								'active' => $boost['active'] ? 0 : 1
							);
							$this->boost_model->update_boost($update_boost, $boost_id);
						}
					}
					break;
				case 'save_boost':
		            $boost_id = !empty($_POST['boost']['id']) ? $_POST['boost']['id'] : (!empty($_GET['boost_id']) ? $_GET['boost_id'] : 0);
					if (!empty($boost_id)) {
			            $boost_data = !empty($_POST['boost']) ? stripslashes_deep($_POST['boost']) : array();
						$boost_data['draft'] = 0;
						$boost_data['active'] = 1;
						$this->boost_model->update_boost($boost_data, $boost_id);
					}
					break;
				default:
					break;
			}
		}
		$boosts = $this->boost_model->get_boosts(array('draft' => '0'), array('id' => 'DESC'));
		$draft_boosts = $this->boost_model->get_boosts(array('draft' => '1'), array('id' => 'DESC'));
		$trigger_types = $this->boost_model->get_trigger_types();
		?>
		<div class="wrap">
			<div class="boost-page boost-main-page">
				<div class="boost-loading"></div>

                <form action="<?= admin_url('/admin.php?page=boost_main')?>" method="POST">
                    <div class="boost-page-header boost-main-page-header">
                        <div class="boost-logo">
                            <div class="boost-logo-img">
                                <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/logo.png' ?>" />
                            </div>
                            <div class="boost-logo-text"><?= __('Catch leads with various Social Boost messages', 'boost') ?></div>
                        </div>
                        <div class="boost-button-block boost-new-boost-button-block">
                            <button type="submit"
                                    name="submit"
                                    value="update_settings"
                                    class="boost-button boost-button-primary boost-new-boost-button"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost')?>">
                                <span><?= __('New Boost', 'boost')?></span>
                            </button>
                        </div>
                    </div>

                    <div class="boost-page-tab-list-container boost-main-page-tab-list-container">
                        <div class="boost-batch-actions boost-hidden">
                            <button type="submit"
                                    name="submit"
                                    value="bulk_delete"
                                    class="boost-button boost-button-danger boost-bulk-delete-boosts-button"
                                    data-need-confirm="true">
                                <span><?= __('Bulk Delete', 'boost')?></span>
                            </button>
                        </div>
                        <div class="boost-page-tab-list boost-main-page-tab-list">
                            <div>
                                <input type="radio" id="boosts" name="active_tab" value="boosts" <?php checked( true); ?>>
                                <label for="boosts"><?= __('Boosts', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="draft-boosts" name="active_tab" value="draft-boosts">
                                <label for="draft-boosts"><?= __('Drafts', 'boost') ?></label>
                            </div>
                        </div>
                    </div>

                    <div class="boost-tabs-container">
                        <div class="boost-tab boost-tab-boosts boost-active">
                            <table class="boost-table">
                                <thead>
                                    <tr>
                                        <th class="boost-select-boost-all boost-align-left">
                                            <div class="boost-checkbox-container">
                                                <input id="select_all_boost" type="checkbox" name="select_all_boost" value="1" class="boost-checkbox">
                                                <label for="select_all_boost"></label>
                                            </div>
                                        </th>
                                        <th class="boost-name boost-align-left"><?=__('NAME', 'boost')?></th>
                                        <th class="boost-type boost-align-left">
                                            <span class="boost-type-circle"></span>
                                            <?=__('TYPE', 'boost')?></th>
                                        <th class="boost-subtype boost-align-left"><?=__('CATEGORY', 'boost')?></th>
                                        <th class="boost-display boost-align-left"><?=__('DISPLAY ON', 'boost')?></th>
                                        <th class="boost-devices"><?=__('DEVICES', 'boost')?></th>
                                        <th class="boost-enable"><?=__('OFF/ON', 'boost')?></th>
                                        <th class="boost-actions"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (count($boosts) == 0){
                                    ?>
                                    <tr><td colspan="8" align="center"><?=__('There are no boosts!', 'boost')?></td></tr>
                                    <?php
                                }
                                else{
                                foreach ($boosts as $boost){
                                    ?>
                                    <tr>
                                        <td class="boost-select-boost">
                                            <div class="boost-checkbox-container">
                                                <input id="boost_<?=$boost['id']?>" type="checkbox" name="boosts[]" value="<?=$boost['id']?>" class="boost-checkbox">
                                                <label for="boost_<?=$boost['id']?>"></label>
                                            </div>
                                        </td>
                                        <td class="boost-align-left">
                                                <?= $boost['name'] != '' ? $boost['name'] : __('N/A', 'boost')?>
                                        </td>
                                        <td class="boost-align-left">
                                            <span class="boost-type-circle boost-<?= $boost['type'] ?>-background"></span>
                                            <span><?= $trigger_types[$boost['type']]['name']?></span>
                                        </td>
                                        <td class="boost-align-left">
                                            <span><?= (!empty($boost['subtype']) ? $trigger_types[$boost['type']]['subtypes'][$boost['subtype']] : $trigger_types[$boost['type']]['name']) ?></span>
                                        </td>
                                        <td class="boost-list-boost-display-container">
                                            <span><?= ucfirst(str_replace('_', ' ', $boost['display_type'])) ?></span>
                                        </td>
                                        <td class="boost-list-boost-desktop-container">
                                            <div class="boost-display-desktop">
                                                <?php
                                                if ($boost['desktop'] == '1') {
                                                ?>
                                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/display-icons/display-desktop-' . $boost['desktop_position'] . '.png' ?>">
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="boost-display-mobile">
                                                <?php
                                                if ($boost['mobile'] == '1') {
                                                ?>
                                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/display-icons/display-mobile-' . $boost['mobile_position'] . '.png' ?>">
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="boost-list-boost-active-container">
                                            <form action="<?= admin_url('/admin.php?page=boost_main'); ?>" method="POST">
                                                <input type="hidden" name="boost_id" value="<?= $boost['id'] ?>">
                                                <button type="submit"
                                                        name="submit"
                                                        value="enable_disable_boost"
                                                        class="boost-on-off-switcher boost-list-boost-active-button boost-list-boost-active-button-<?=$boost['active'] ? 'active' : 'inactive'?>">
                                                </button>
                                            </form>
                                        </td>
                                        <td class="boost-list-boost-actions-container">
                                            <div class="boost-dropdown boost-dropdown-menu">
                                                <div class="boost-dropdown-toggle">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                                <div class="boost-list-boost-actions boost-dropdown-content">
                                                    <form action="<?= admin_url('/admin.php?page=boost_main'); ?>" method="POST">
                                                        <input type="hidden" name="boost_id" value="<?= $boost['id'] ?>">
                                                        <button type="submit"
                                                                name="submit"
                                                                value="delete_boost"
                                                                class="boost-action-button action-delete"
                                                                data-need-confirm="true">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('DELETE', 'boost') ?></span>
                                                        </button>
                                                        <button type="submit"
                                                                name="submit"
                                                                value="copy_boost"
                                                                class="boost-action-button action-duplicate">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('DUPLICATE', 'boost') ?></span>
                                                        </button>
                                                        <button type="submit"
                                                                formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_id=' . $boost['id']); ?>"
                                                                class="boost-action-button action-edit">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('EDIT', 'boost') ?></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="boost-tab boost-tab-draft-boosts boost-hidden">
                            <table class="boost-table">
                                <thead>
                                    <tr>
                                        <th class="boost-select-boost-all boost-align-left">
                                            <div class="boost-checkbox-container">
                                                <input id="select_all_draft_boost" type="checkbox" name="select_all_draft_boost" value="1" class="boost-checkbox">
                                                <label for="select_all_draft_boost"></label>
                                            </div>
                                        </th>
                                        <th class="boost-name boost-align-left"><?=__('NAME', 'boost')?></th>
                                        <th class="boost-type boost-align-left"><?=__('TYPE', 'boost')?></th>
                                        <th class="boost-subtype boost-align-left"><?=__('CATEGORY', 'boost')?></th>
                                        <th class="boost-display boost-align-left"><?=__('DISPLAY ON', 'boost')?></th>
                                        <th class="boost-devices"><?=__('DEVICES', 'boost')?></th>
                                        <th class="boost-enable"><?=__('OFF/ON', 'boost')?></th>
                                        <th class="boost-actions"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                if (count($draft_boosts) == 0){
                                    ?>
                                    <tr><td colspan="8" align="center"><?=__('There are no boosts!', 'boost')?></td></tr>
                                    <?php
                                }
                                else{
                                foreach ($draft_boosts as $draft_boost){
                                    ?>
                                    <tr>
                                        <td class="boost-select-boost">
                                            <div class="boost-checkbox-container">
                                                <input id="boost_<?=$draft_boost['id']?>" type="checkbox" name="draft_boosts[]" value="<?=$draft_boost['id']?>" class="boost-checkbox">
                                                <label for="boost_<?=$draft_boost['id']?>"></label>
                                            </div>
                                        </td>
                                        <td class="boost-align-left">
                                                <?= $draft_boost['name'] != '' ? $draft_boost['name'] : __('N/A', 'boost')?>
                                        </td>
                                        <td class="boost-align-left">
                                            <span class="boost-type-circle boost-<?= $draft_boost['type'] ?>-background"></span>
                                            <span><?= $trigger_types[$draft_boost['type']]['name']?></span>
                                        </td>
                                        <td class="boost-align-left">
                                            <span><?= (!empty($draft_boost['subtype']) ? $trigger_types[$draft_boost['type']]['subtypes'][$draft_boost['subtype']] : $trigger_types[$draft_boost['type']]['name']) ?></span>
                                        </td>
                                        <td class="boost-list-boost-display-container">
                                            <span><?= ucfirst(str_replace('_', ' ', $draft_boost['display_type'])) ?></span>
                                        </td>
                                        <td class="boost-list-boost-desktop-container">
                                            <div class="boost-display-desktop">
                                                <?php
                                                if ($draft_boost['desktop'] == '1') {
                                                ?>
                                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/display-icons/display-desktop-' . $draft_boost['desktop_position'] . '.png' ?>">
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <div class="boost-display-mobile">
                                                <?php
                                                if ($draft_boost['mobile'] == '1') {
                                                ?>
                                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/display-icons/display-mobile-' . $draft_boost['mobile_position'] . '.png' ?>">
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </td>
                                        <td class="boost-list-boost-active-container">
                                            <form action="<?= admin_url('/admin.php?page=boost_make_boost'); ?>" method="POST">
                                                <input type="hidden" name="boost_id" value="<?= $draft_boost['id'] ?>">
                                                <button type="submit"
                                                        name="submit"
                                                        value="enable_disable_boost"
                                                        class="boost-on-off-switcher boost-list-boost-active-button boost-list-boost-active-button-<?=$draft_boost['active'] ? 'active' : 'inactive'?>">
                                                </button>
                                            </form>
                                        </td>
                                        <td class="boost-list-boost-actions-container">
                                            <div class="boost-dropdown boost-dropdown-menu">
                                                <div class="boost-dropdown-toggle">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </div>
                                                <div class="boost-list-boost-actions boost-dropdown-content">
                                                    <form action="<?= admin_url('/admin.php?page=boost_main'); ?>" method="POST">
                                                        <input type="hidden" name="boost_id" value="<?= $draft_boost['id'] ?>">
                                                        <button type="submit"
                                                                name="submit"
                                                                value="delete_boost"
                                                                class="boost-action-button action-delete"
                                                                data-need-confirm="true">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('DELETE', 'boost') ?></span>
                                                        </button>
                                                        <button type="submit"
                                                                name="submit"
                                                                value="copy_boost"
                                                                class="boost-action-button action-duplicate">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('DUPLICATE', 'boost') ?></span>
                                                        </button>
                                                        <button type="submit"
                                                                formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_id=' . $draft_boost['id']); ?>"
                                                                class="boost-action-button action-edit">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('EDIT', 'boost') ?></span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
			</div>
		</div>
		<?php
	}

	public function display_plugin_settings_page(){
		if (!empty($_POST['submit'])) {
			$action = !empty($_POST['submit']) ? $_POST['submit'] : '';
			switch ($action) {
				case 'update_settings':
					$new_settings = isset($_POST['boost_options']) ? stripslashes_deep($_POST['boost_options']) : array();
					$this->settings_model->set_settings($new_settings);
                    break;
                case 'restore_defaults_settings':
                    $this->settings_model->set_default_settings();
                    break;
                case 'regenerate_boosts_from_orders':
                    $boosts = $this->boost_model->get_boosts(
                            array(
                                    'draft' => '0',
                                    'type' => array(
                                            'value' => array('woocommerce', 'easydigitaldownloads'),
                                            'comparison' => 'in',
                                    )
                            )
                    );
	                $time = time();
                    foreach ($boosts as $boost) {
                        if (!empty($boost['subtype']) && in_array($boost['subtype'], array('transaction', 'specific_transaction'))) {
	                        $full_boost_data = $this->boost_model->get_boost($boost['id']);
	                        $products_ids = array_map(function($product){
	                            return $product['product_id'];
                            }, $full_boost_data['products']);
	                        $categories_ids = array_map(function($category){
	                            return $category['category_id'];
                            }, $full_boost_data['categories']);
	                        $this->action_model->create_actions_for_exist_orders(
	                                $full_boost_data['id'],
                                    $full_boost_data['type'],
                                    !empty($full_boost_data['subtype'])?$full_boost_data['subtype']:'',
                                    $time,
                                    $products_ids,
                                    $categories_ids
                            );
                        }
                    }
                    break;
				default:
					break;
			}
		}
		$settings = $this->settings_model->get_settings();
	    ?>

		<div class="wrap">
			<div class="boost-page boost-settings-page">
				<div class="boost-loading"></div>
                    <form method="post" action="<?= admin_url('/admin.php?page=boost_settings')?>">
                        <div class="boost-page-header boost-main-page-header">
                            <div class="boost-logo">
                                <div class="boost-logo-img">
                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/logo.png' ?>" />
                                </div>
                                <div class="boost-logo-text"><?= __('Catch leads with various Social Boost messages', 'boost') ?></div>
                            </div>
                            <div class="boost-button-block boost-save-settings-button-block">
                                    <button type="submit" name="submit" value="update_settings"
                                        class="boost-button boost-button-primary boost-save-settings-button">
                                        <span><?= __('Save Settings', 'boost')?></span>
                                    </button>
                            </div>
                        </div>

                        <div class="boost-page-tab-list-container boost-settings-page-tab-list-container">
                            <div class="boost-page-tab-list boost-settings-page-tab-list">
                                <div>
                                    <input type="radio" id="settings" name="active_tab" value="settings" <?php checked( true); ?>>
                                    <label for="settings"><?= __('Settings', 'boost') ?></label>
                                </div>
                                <div>
                                    <input type="radio" id="default-settings" name="active_tab" value="default-settings">
                                    <label for="default-settings"><?= __('Restore Defaults', 'boost') ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="boost-tabs-container">
                            <div class="boost-tab boost-tab-settings boost-active">
                                <div class="boost-tab-left-side boost-tab-side">
                                    <div class="boost-setting-field-container boost-setting-use-leads-time-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'USE LEADS TIME FOR', 'boost' );?></div>
                                            <div class="boost-setting-field boost-spinner">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="0" max="1000" id="boost_options[use-leads-time]" name="boost_options[use-leads-time]" value="<?= $settings['use-leads-time']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                            </div>
                                            <div class="boost-setting-field-name"><?= __( 'HOURS', 'boost' );?></div>
                                        </div>
                                        <div class="boost-setting-field-description"><?= __('For older leads exact time will be replace with "recentely"', 'boost')?></div>
                                    </div>
                                    <div class="boost-setting-field-container boost-setting-dont-show-notifications-after-days-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'DON\'T SHOW NOTIFICATIONS AFTER', 'boost' );?></div>
                                            <div class="boost-setting-field boost-spinner">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="0" max="1000" id="boost_options[dont-show-notifications-after-days]" name="boost_options[dont-show-notifications-after-days]" value="<?= $settings['dont-show-notifications-after-days']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                            </div>
                                            <div class="boost-setting-field-name"><?= __( 'DAYS', 'boost' );?></div>
                                        </div>
                                        <div class="boost-setting-field-description"><?= __('Notifications older than the specified period will not be displayed', 'boost')?></div>
                                    </div>
                                    <div class="boost-setting-field-container boost-setting-boost-visibility-time-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'VISIBILITY', 'boost' );?></div>
                                            <div class="boost-setting-field boost-spinner">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="1" id="boost_options[boost-visibility-time]" name="boost_options[boost-visibility-time]" value="<?= $settings['boost-visibility-time']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                            </div>
                                            <div class="boost-setting-field-name"><?= __( 'SECONDS', 'boost' );?></div>
                                        </div>
                                        <div class="boost-setting-field-description"><?= __('Duration of each Boost visibility', 'boost')?></div>
                                    </div>
                                    <div class="boost-setting-field-container boost-setting-approximate-time-between-boosts-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'DELAY', 'boost' );?></div>
                                            <div class="boost-setting-field boost-spinner">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="1" id="boost_options[approximate-time-between-boosts]" name="boost_options[approximate-time-between-boosts]" value="<?= $settings['approximate-time-between-boosts']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                            </div>
                                            <div class="boost-setting-field-name"><?= __( 'SECONDS', 'boost' );?></div>
                                        </div>
                                        <div class="boost-setting-field-description"><?= __('Approximate time between Boosts', 'boost')?></div>
                                    </div>
                                    <div class="boost-setting-field-container boost-setting-gather-limit-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'TRANSACTION GATHER LIMIT', 'boost' );?></div>
                                            <div class="boost-setting-field boost-spinner spinner-pink">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="0" max="1000" id="boost_options[woocommerce-transaction-gather-limit]" name="boost_options[woocommerce-transaction-gather-limit]" value="<?= $settings['woocommerce-transaction-gather-limit']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                                <div class="spinner-description"><?=__('WooCommerce', 'boost')?></div>
                                            </div>
                                            <div class="boost-setting-field boost-spinner spinner-blue">
                                                <div class="spinner-data">
                                                    <a class="spinner-control spinner-down"></a>
                                                    <input type="number" min="0" max="1000" id="boost_options[edd-transaction-gather-limit]" name="boost_options[edd-transaction-gather-limit]" value="<?= $settings['edd-transaction-gather-limit']?>">
                                                    <a class="spinner-control spinner-up"></a>
                                                </div>
                                                <div class="spinner-description"><?=__('EasyDigitalDownloads', 'boost')?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    if (!empty($settings['translations']) && is_array($settings['translations'])) {
                                        foreach ($settings['translations'] as $key => $translation) {
                                    ?>
                                            <div class="boost-setting-field-container boost-setting-translation-field">
                                                <div class="boost-setting-field-data">
                                                    <div class="boost-setting-field-name boost-setting-need-translation-text"><?= ucfirst(str_replace('_', ' ', $key)) . " " . __( 'TRANSLATION', 'boost' );?></div>
                                                    <div class="boost-setting-field">
                                                        <input type="text" id="boost_options[translations][<?=$key?>]" name="boost_options[translations][<?=$key?>]" value="<?= htmlspecialchars($translation) ?>" class="boost-input-style">
                                                    </div>
                                                    <div class="boost-setting-field-name"></div>
                                                </div>
                                                <div class="boost-setting-field-description"></div>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>

                                <div class="boost-tab-right-side boost-tab-side">
                                    <div class="boost-setting-field-container switch-field">
                                        <div class="switch-data">
                                            <div class="boost-setting-field boost-on-off-switcher-checkbox boost-on-off-switcher-checkbox-eyes">
                                                <input type="checkbox" id="boost_options[give-us-credit]" name="boost_options[give-us-credit]" value="1" <?php checked( $settings['give-us-credit'], 1 , true ); ?>>
                                                <label for="boost_options[give-us-credit]"></label>
                                            </div>
                                        </div>
                                        <div class="switch-label">
                                            <div class="boost-setting-field-name field-name"><?= __( 'GIVE US CREDIT', 'boost' );?></div>
                                            <div class="boost-setting-field-description"><?= __('Thank you :)', 'boost');?></div>
                                        </div>
                                    </div>
	                                <?php
                                    if (false) {
	                                ?>
                                        <div class="boost-setting-field-container switch-field disabled">
                                            <div class="switch-data">
                                                <div class="boost-setting-field boost-on-off-switcher-checkbox boost-on-off-switcher-checkbox-eyes">
                                                    <input disabled="disabled" type="checkbox" id="boost_options[location-optimization]" name="boost_options[location-optimization]" value="1" <?php checked( $settings['location-optimization'], 1 , true ); ?>>
                                                    <label for="boost_options[location-optimization]"></label>
                                                </div>
                                            </div>
                                            <div class="switch-label">
                                                <div class="boost-setting-field-name field-name"><?= __( 'LOCATION OPTIMIZATION', 'boost' ); ?></div>
                                                <div class="boost-setting-field-description">
                                                    <?= __('Try to show leads from a location closer to the user. This can increase conversion.', 'boost');?>
                                                </div>
                                                <div class="boost-extra-addon-required">
                                                    <div class="boost-extra-addon-required-text"><?=__('EXTRA ADD-ON REQUIRED', 'boost')?></div>
                                                    <div class="boost-icon-help"></div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <div class="boost-setting-field-container switch-field">
                                        <div class="switch-data">
                                            <div class="boost-setting-field boost-on-off-switcher-checkbox boost-on-off-switcher-checkbox-eyes">
                                                <input type="checkbox" id="boost_options[show-boosts-in-random-order]" name="boost_options[show-boosts-in-random-order]" value="1" <?php checked( $settings['show-boosts-in-random-order'], 1 , true ); ?>>
                                                <label for="boost_options[show-boosts-in-random-order]"></label>
                                            </div>
                                        </div>
                                        <div class="switch-label">
                                            <div class="boost-setting-field-name field-name"><?= __( 'RANDOM ORDER', 'boost' ); ?></div>
                                            <div class="boost-setting-field-description">
                                                <?= __('Show boosts in random order. By default, boosts are shown in chronological order, starting with the most recent ones.', 'boost');?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="boost-setting-field-container switch-field">
                                        <div class="switch-data">
                                            <div class="boost-setting-field boost-on-off-switcher-checkbox boost-on-off-switcher-checkbox-eyes">
                                                <input type="checkbox" id="boost_options[close-boosts]" name="boost_options[close-boosts]" value="1" <?php checked( $settings['close-boosts'], 1 , true ); ?>>
                                                <label for="boost_options[close-boosts]"></label>
                                            </div>
                                        </div>
                                        <div class="switch-label">
                                            <div class="boost-setting-field-name field-name"><?= __( 'CLOSE BOOSTS', 'boost' ); ?></div>
                                            <div class="boost-setting-field-description">
                                                <?= __('If you enable this, the boosts will have a closing icon that will turn them OFF for 24 hours.', 'boost');?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="boost-tab-side">
                                    <div class="boost-setting-field-container boost-setting-regenerate-boosts-from-orders-field">
                                        <div class="boost-setting-field-data">
                                            <div class="boost-setting-field-name"><?= __( 'REGENERATE BOOSTS FROM ORDERS?', 'boost' );?></div>
                                            <div class="boost-setting-field">
                                                <button type="submit" name="submit" value="regenerate_boosts_from_orders"
                                                        class="boost-button boost-button-small boost-button-primary boost-regenerate-boosts-button"
                                                        data-need-confirm="true">
                                                    <span><?= __('Regenerate boosts', 'boost')?></span>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="boost-setting-field-description"><?= __('An option to recreate again boosts from existing WooCommerce and EDD orders.', 'boost')?></div>
                                    </div>
                                </div>
							</div>
							<div class="boost-tab boost-tab-default-settings boost-hidden">
							    <div class="boost-confirmation-message boost-setting-field-name"><?= __('Are you sure you want to set the default settings?', 'boost') ?></div>

                                <div class="boost-confirmation-buttons-block">
                                        <button type="submit" name="submit" value="restore_defaults_settings"
                                            class="boost-button boost-button-primary boost-restore-defaults-settings-button">
                                            <span><?= __('Yes', 'boost')?></span>
                                        </button>
                                        <button type="submit" name="submit" value="show_settings"
                                            class="boost-button boost-button-cancel boost-show-settings-button">
                                            <span><?= __('No', 'boost')?></span>
                                        </button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
	    <?php
	}

	public function display_plugin_license_page() {

	    $license = new Boost_License();

		$license_expires = $license->get_license_expires();
		$license_key = $license->get_license_key();
		$license_expires_soon = false;
		$license_expired = false;
		$license_exist = $license->license_exist();

		if( $license_expires && $license_exist) {

			$now = time();

			if( $now >= $license_expires ) {
				$license_expired = true;
			} elseif ( $now >= ( $license_expires - 30 * DAY_IN_SECONDS ) ) {
				$license_expires_soon = true;
			}

		}

		if (!empty($_POST['submit'])) {
			$action = !empty($_POST['submit']) ? $_POST['submit'] : '';
			if (!empty($action)) {
				$license->update_license($action, trim($_POST['license_key']));
            }
		}
	    ?>

		<div class="wrap">
			<div class="boost-page boost-license-page">
				<div class="boost-loading"></div>
                    <form method="post" action="<?= admin_url('/admin.php?page=boost_license')?>">
                        <div class="boost-page-header boost-main-page-header">
                            <div class="boost-logo">
                                <div class="boost-logo-img">
                                    <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/logo.png' ?>" />
                                </div>
                                <div class="boost-logo-text"><?= __('Catch leads with various Social Boost messages', 'boost') ?></div>
                            </div>
                        </div>

                        <div class="boost-license-container">
                            <div class="boost-license-field">
                                <div class="boost-license-field-input">
                                    <input spellcheck="false" type="text" id="license_key" value="<?= htmlspecialchars($license_key) ?>" <?= $license_exist ? 'readonly' : ''; ?> name="license_key" placeholder="<?=__('Please enter license key', 'boost')?>" class="boost-input-style">
                                </div>
                                <div class="boost-license-field-button">
                                    <button type="submit" name="submit" value="<?= $license_exist ? 'deactivate_license' : 'activate_license' ?>"
                                        class="boost-button boost-button-primary boost-button-medium">
                                        <span><?= $license_exist ? __('Deactivate license', 'boost') : __('Activate license', 'boost') ?></span>
                                    </button>
                                </div>
                                <div class="boost-license-message">
	                                <?php if ( $notice = get_transient( 'boost_license_message' ) ) : ?>

                                        <div class="<?php esc_attr_e( $notice['class'] ); ?>">
                                            <?php echo $notice['msg']; ?>
                                        </div>

	                                <?php endif; ?>

	                                <?php if ( $license_expires_soon ) : ?>

                                        <div class="">
                                            <p>Your license key <strong>expires on <?php echo date( get_option( 'date_format' ), $license_expires ); ?></strong>. Make sure you keep everything updated and in order.</p>
                                            <p><a href="<?=BOOST_LICENSE_STORE_URL?>/checkout/?edd_license_key=<?php esc_attr_e( $license_key ); ?>&utm_campaign=admin&utm_source=licenses&utm_medium=renew" target="_blank"><strong>Click here to renew your license now</strong></a></p>
                                        </div>

	                                <?php endif; ?>

	                                <?php if ( $license_expired ) : ?>

                                        <div class="boost-message-red">
                                            <p><strong>Your license key is expired</strong>, so you no longer get any updates. Don't miss our last improvements and
                                                make sure that everything works smoothly.</p>
                                            <p><a href="<?=BOOST_LICENSE_STORE_URL?>/checkout/?edd_license_key=<?php esc_attr_e( $license_key ); ?>&utm_campaign=admin&utm_source=licenses&utm_medium=renew" target="_blank"><strong>Click here to renew your license now</strong></a></p>
                                        </div>

	                                <?php endif; ?>
                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
	    <?php
	}

	public function display_plugin_make_boost_page() {
		$step = !empty($_GET['step']) && in_array($_GET['step'], array('1','2','3','4')) ? $_GET['step'] : '1';
		$boost_type = !empty($_POST['boost']['type']) ? $_POST['boost']['type'] : (!empty($_GET['boost_type']) ? $_GET['boost_type'] : null);
		$boost_subtype = !empty($_POST['boost']['subtype']) ? $_POST['boost']['subtype'] : (!empty($_GET['subtype']) ? $_GET['subtype'] : null);
		$boost_id = !empty($_POST['boost']['id']) ? $_POST['boost']['id'] : (!empty($_GET['boost_id']) ? $_GET['boost_id'] : null);
		$previous_step = !empty($_POST['current_step']) ? $_POST['current_step'] : '';

		if(isset($_POST['submit'])) {
			$boost_data = !empty($_POST['boost']) ? stripslashes_deep($_POST['boost']) : array();
			$step_fields = $this->boost_model->get_step_fields($previous_step, $boost_type, $boost_subtype);
			$boost_data = array_merge($step_fields, $boost_data);
			if (!empty($boost_data['active'])) {
				$boost_data['draft'] = 0;
			}
			switch ($_POST['submit']) {
				case 'select_type':
					if (!empty($boost_type)) {
						$boost_data['type'] = $boost_type;
						$boost_id = $this->boost_model->create_boost($boost_data);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					break;
				case 'next':
					if (!empty($boost_id)) {
						$this->boost_model->update_boost($boost_data, $boost_id);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					break;
				case 'previous':
					if (!empty($boost_id)) {
						$boost = $this->boost_model->get_boost($boost_id);
					}
					break;
				case 'step_1':
					if (!empty($boost_id)) {
						$this->boost_model->update_boost($boost_data, $boost_id);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					break;
				case 'step_2':
					if (!empty($boost_id)) {
						$this->boost_model->update_boost($boost_data, $boost_id);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					else {
						$step = 1;
					}
					break;
				case 'step_3':
					if (!empty($boost_id)) {
						$this->boost_model->update_boost($boost_data, $boost_id);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					else {
						$step = 1;
					}
					break;
				case 'step_4':
					if (!empty($boost_id)) {
						$this->boost_model->update_boost($boost_data, $boost_id);
						$boost = $this->boost_model->get_boost($boost_id);
					}
					else {
						$step = 1;
					}
					break;
				default:
					break;
			}
		}
		else {
			if (empty($boost_id) || ($boost = $this->boost_model->get_boost($boost_id)) == false) {
				$step = 1;
				$boost_type = null;
				$boost = array();
			}
			else {
				$boost_type = $boost['type'];
				$boost_subtype = isset($boost['subtype']) ? $boost['subtype'] : '';
			}
		}
		$trigger_types = $this->boost_model->get_trigger_types();

		?>
		<div class="wrap">
			<div class="boost-page boost-make-boost">
				<div class="boost-loading"></div>
                <form action="<?= admin_url('/admin.php?page=boost_make_boost'); ?>" method="POST" autocomplete="off">
                    <input type="hidden" name="boost[id]" value="<?= isset($boost['id']) ? $boost['id'] : '' ?>">
                    <input type="hidden" name="boost[type]"
                           value="<?= isset($boost['type']) ? $boost['type'] : '' ?>">
                    <input type="hidden" name="boost[subtype]"
                           value="<?= isset($boost['subtype']) ? $boost['subtype'] : '' ?>">
                    <input type="hidden" name="current_step" value="<?= $step ?>">

                    <div class="boost-make-boost-header">
                        <div class="boost-make-boost-header-left">
                            <div class="boost-name-field">
                                <input type="text" id="boost[name]" name="boost[name]" placeholder="Name your Boost"
                                       value="<?= isset($boost['name']) ? htmlspecialchars($boost['name']) : '' ?>">
                            </div>
                        </div>
                        <div class="boost-make-boost-header-right boost-notification-preview">

	                        <?php
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
	                        $replace_for = array(
		                        'Name',
		                        'Time',
		                        'Town',
		                        'State',
		                        'Country',
		                        'Product name',
		                        'Product with link',
		                        'Stock'
	                        );

	                        $top_message = str_replace($replace_what, $replace_for, (isset($boost['top_message']) ? $boost['top_message'] : ''));
	                        $message = str_replace($replace_what, $replace_for, (isset($boost['message']) ? $boost['message'] : ''));
	                        ?>
                            <div class="boost-make-boost-field-preview-data">
                                <?= $this->notification_model->get_notification_as_html(array('type' => $boost_type, 'subtype' => '', 'notification_template' => !empty($boost['notification_template']) ? $boost['notification_template'] : 'round', 'time' => time() - 2 * 60 * 60, 'top_message' => $top_message, 'message' => $message), array('preview', 'boost-notification-desktop', 'preview-step'.$step));?>
                            </div>
                        </div>
                    </div>

                    <div class="boost-make-boost-steps-menu">
                            <button type="submit" name="submit" value="step_1"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_id=' . $boost_id); ?>"
                                    class="boost-make-boost-step boost-make-boost-step-1 <?= $step == 1 && !$boost_type ? 'active' : ''?>">
                                <div class="boost-make-boost-step-text">
                                    <?= __('Boost Type', 'boost') ?>
                                    <div class="step-number">1</div>
                                </div>
                            </button>
                            <button type="submit" name="submit" value="step_1"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_id=' . $boost_id); ?>"
                                    class="boost-make-boost-step boost-make-boost-step-1 <?= $step == 1 && $boost_type ? 'active' : ''?>">
                                <div class="boost-make-boost-step-text">
                                    <?= __('Configure', 'boost') ?>
                                    <div class="step-number">2</div>
                                </div>
                            </button>
                            <button type="submit" name="submit" value="step_2"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=2&boost_id=' . $boost_id); ?>"
                                    class="boost-make-boost-step boost-make-boost-step-2 <?= $step == 2 ? 'active' : ''?>">
                                <div class="boost-make-boost-step-text">
                                    <?= __('Message', 'boost') ?>
                                    <div class="step-number">3</div>
                                </div>
                            </button>
                            <button type="submit" name="submit" value="step_3"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=3&boost_id=' . $boost_id); ?>"
                                    class="boost-make-boost-step boost-make-boost-step-3 <?= $step == 3 ? 'active' : ''?>">
                                <div class="boost-make-boost-step-text">
                                    <?= __('Display', 'boost') ?>
                                    <div class="step-number">4</div>
                                </div>
                            </button>
                            <button type="submit" name="submit" value="step_4"
                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=4&boost_id=' . $boost_id); ?>"
                                    class="boost-make-boost-step boost-make-boost-step-4 <?= $step == 4 ? 'active' : ''?>">
                                <div class="boost-make-boost-step-text">
                                    <?= __('Review & Publish', 'boost') ?>
                                    <div class="step-number">5</div>
                                </div>
                            </button>
                    </div>
                    <div class="boost-make-boost-step-container">
                        <?php
                        switch ($step) {
                            case '2':
                                switch ($boost['type']) {
                                    case 'leads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-leads">
                                                <div class="boost-make-boost-type-head"><?= __('Any Form', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Show boosts when a form is completed. Works with most forms and popup.', 'boost') ?></div>
                                            </div>
                                        </div>
                                        <?php
                                        break;
                                    case 'woocommerce':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-woocommerce">
                                                <div class="boost-make-boost-type-head"><?= __('Woo Commerce Boost', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place, or show stock messages.', 'boost') ?></div>
                                            </div>
                                        </div>
                                        <?php
                                        break;
                                    case 'easydigitaldownloads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-easydigitaldownloads">
                                                <div class="boost-make-boost-type-head"><?= __('Easy Digital Downloads', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place.', 'boost') ?></div>
                                            </div>
                                        </div>
                                        <?php
                                        break;
                                    default:
                                        break;
                                }
                                ?>


                                <div class="boost-make-boost-configure-message-block boost-make-boost-type-<?=$boost['type']?>">
                                    <div class="boost-make-boost-left-side">

                                        <div class="boost-make-boost-field boost-make-boost-top-message boost-make-boost-message-editor-container">
                                            <div class="boost-message-editor-data">
                                                <input type="text" id="boost[top_message]" name="boost[top_message]" value="<?= isset($boost['top_message']) ? htmlspecialchars($boost['top_message']) : '' ?>">
                                                <div  spellcheck="false" contenteditable="true" class="boost-message-editor boost-input-style"><?php
                                                    $top_message = $this->replace_tag_to_html_tag((isset($boost['top_message']) ? $boost['top_message'] : ''));
                                                    echo $top_message;
                                                    ?></div>
                                                <div class="boost-make-boost-field-label"><?=__('Boost header:', 'boost')?></div>
                                            </div>
                                            <div class="boost-make-boost-field-description"><?= __('You can use the tags on the right by dragging them into the fields', 'boost') ?></div>
                                        </div>

                                        <div class="boost-make-boost-field boost-make-boost-message boost-make-boost-message-editor-container">
                                            <div class="boost-message-editor-data">
                                                <input type="text" id="boost[message]" name="boost[message]" value="<?= isset($boost['message']) ? htmlspecialchars($boost['message']) : '' ?>">
                                                <div data-max-len="100" spellcheck="false" contenteditable="true" class="boost-message-editor boost-input-style"><?php
                                                    $message = $this->replace_tag_to_html_tag((isset($boost['message']) ? $boost['message'] : ''));
                                                    echo $message;
                                                    ?></div>
                                                <div class="boost-make-boost-field-label"><?=__('Boost message:', 'boost')?></div>
                                            </div>
                                            <div class="boost-make-boost-field-description"><span class="boost-make-boost-message-length"><?=strlen($boost['message'])?></span><?= ' / 100 ' . __('characters', 'boost') ?></div>
                                        </div>

                                    </div>
                                    <div class="boost-make-boost-right-side">
                                        <?php $this->tag_cloud_html($boost_type, $boost_subtype) ?>
                                    </div>
                                </div>

                                <div class="boost-make-boost-select-notification-template-container">
                                    <div class="boost-make-boost-field">
                                        <div class="boost-select2-container">
                                            <select id="boost[notification_template]" name="boost[notification_template]" class="select2-standard boost-input-style boost-select-notification-template-input-style">
                                                <option
                                                    value="round" <?php selected(isset($boost['notification_template']) ? $boost['notification_template'] : '', 'round'); ?>><?= __('Round', 'boost') ?></option>
                                                <option
                                                    value="square" <?php selected(isset($boost['notification_template']) ? $boost['notification_template'] : '', 'square'); ?>><?= __('Square', 'boost') ?></option>
                                            </select>
                                        </div>
                                        <div class="boost-make-boost-field-description">
                                            <?= __('Select a prefered Boost shape and as many templates as you wish on the right', 'boost'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="boost-make-boost-select-notification-style-template-container">
                                    <div class="boost-make-boost-field">
                                        <div class="boost-make-boost-field-label"><?=__('Select your Boost Desktop Template', 'boost') . ':'?></div>
                                        <div class="boost-make-boost-notification-style-templates">
                                                <?php

                                                $top_message = str_replace($replace_what, $replace_for, (isset($boost['top_message']) ? $boost['top_message'] : ''));
                                                $message = str_replace($replace_what, $replace_for, (isset($boost['message']) ? $boost['message'] : ''));
                                                for($i = 0; $i < 6; $i++){
                                                ?>
                                                    <div class="boost-notification-style-template">
                                                        <?= $this->notification_model->get_notification_as_html(
                                                            array(
                                                                'type' => $boost_type,
                                                                'subtype' => '',
                                                                'notification_template' => !empty($boost['notification_template']) ? $boost['notification_template'] : 'round',
                                                                'time' => time() - 2 * 60 * 60,
                                                                'top_message' => $top_message,
                                                                'message' => $message),
                                                            array(
                                                                'preview',
                                                                'boost-notification-desktop',
                                                                'style_'.($i+1)
                                                            )
                                                        );
                                                        ?>

                                                        <div class="boost-checkbox-container">
                                                            <input id="boost[desktop_notification_style_<?= ($i+1) ?>]" type="checkbox" name="boost[desktop_notification_style_<?= ($i+1) ?>]" value="1" <?php checked((isset($boost['desktop_notification_style_' . ($i + 1)]) ? $boost['desktop_notification_style_' . ($i + 1)] : 0), 1, true) ?> class="boost-checkbox">
                                                            <label for="boost[desktop_notification_style_<?= ($i+1) ?>]"></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                        </div>
                                    </div>
                                    <div class="boost-make-boost-field">
                                        <div class="boost-make-boost-field-label"><?=__('Select your Boost Mobile Template', 'boost') . ':'?></div>
                                        <div class="boost-make-boost-notification-style-templates">
                                                <?php

                                                $top_message = str_replace($replace_what, $replace_for, (isset($boost['top_message']) ? $boost['top_message'] : ''));
                                                $message = str_replace($replace_what, $replace_for, (isset($boost['message']) ? $boost['message'] : ''));
                                                for($i = 0; $i < 6; $i++){
                                                ?>
                                                    <div class="boost-notification-style-template">
                                                        <?= $this->notification_model->get_notification_as_html(
                                                            array(
                                                                'type' => $boost_type,
                                                                'subtype' => '',
                                                                'notification_template' => !empty($boost['notification_template']) ? $boost['notification_template'] : 'round',
                                                                'time' => time() - 2 * 60 * 60,
                                                                'top_message' => $top_message,
                                                                'message' => $message
                                                            ),
                                                            array(
                                                                'preview',
                                                                'style_'.($i+1)
                                                            ),
                                                            true);
                                                        ?>

                                                        <div class="boost-checkbox-container">
                                                            <input id="boost[mobile_notification_style_<?= ($i+1) ?>]" type="checkbox" name="boost[mobile_notification_style_<?= ($i+1) ?>]" value="1" <?php checked((isset($boost['mobile_notification_style_' . ($i + 1)]) ? $boost['mobile_notification_style_' . ($i + 1)] : 0), 1, true) ?> class="boost-checkbox">
                                                            <label for="boost[mobile_notification_style_<?= ($i+1) ?>]"></label>
                                                        </div>
                                                    </div>
                                                <?php
                                                }
                                                ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="boost-make-boost-select-notification-image-type-container">
                                    <div class="boost-make-boost-field">
                                        <div class="boost-make-boost-field-label"><?=__('Select your Boost Images Types', 'boost') . ':'?></div>
	                                    <?php
	                                    if (in_array($boost_type, array('woocommerce', 'easydigitaldownloads'))) {
		                                    ?>
                                            <div class="boost-make-boost-field boost-make-use-products-images switch-field">
                                                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                                    <input type="checkbox" id="boost[use_products_images]" name="boost[use_products_images]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( @$boost['use_products_images'], '1' , true ); ?>>
                                                    <label for="boost[use_products_images]"></label>
                                                </div>
                                                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
				                                    <?= __('Use Product Images', 'boost')?>
                                                </div>
                                            </div>
		                                    <?php
	                                    }
	                                    ?>
                                        <div class="boost-make-boost-field boost-make-use-maps-images switch-field">
                                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                                <input type="checkbox" id="boost[use_maps_images]" name="boost[use_maps_images]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( @$boost['use_maps_images'], '1' , true ); ?>>
                                                <label for="boost[use_maps_images]"></label>
                                            </div>
                                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
			                                    <?= __('Use Maps', 'boost')?>
                                            </div>
                                        </div>
                                        <div class="boost-make-boost-field boost-make-use-icons-images switch-field">
                                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                                <input type="checkbox" id="boost[use_icons_images]" name="boost[use_icons_images]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( @$boost['use_icons_images'], '1' , true ); ?>>
                                                <label for="boost[use_icons_images]"></label>
                                            </div>
                                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
			                                    <?= __('Use Icons', 'boost')?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                break;
                            case '3':
                                switch ($boost_type) {
                                    case 'leads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-leads">
                                                <div class="boost-make-boost-type-head"><?= __('Any Form', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Show boosts when a form is completed. Works with most forms and popup.', 'boost') ?></div>
                                            </div>
                                            <?php $this->display_tabs_list($boost); ?>
                                        </div>
                                        <div class="boost-make-boost-right-side">
                                            <?php $this->boost_display_html($boost); ?>
                                        </div>
                                        <?php $this->display_tabs($boost); ?>
                                        <?php
                                        break;
                                    case 'woocommerce':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-woocommerce">
                                                <div class="boost-make-boost-type-head"><?= __('Woo Commerce Boost', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place, or show stock messages.', 'boost') ?></div>
                                            </div>
                                            <?php $this->display_tabs_list($boost) ?>
                                        </div>
                                        <div class="boost-make-boost-right-side">
                                            <?php $this->boost_display_html($boost); ?>
                                        </div>
                                        <?php $this->display_tabs($boost);?>
                                        <?php
                                        break;
                                    case 'easydigitaldownloads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-easydigitaldownloads">
                                                <div class="boost-make-boost-type-head"><?= __('Easy Digital Downloads', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place.', 'boost') ?></div>
                                            </div>
                                            <?php $this->display_tabs_list($boost) ?>
                                        </div>
                                        <div class="boost-make-boost-right-side">
                                            <?php $this->boost_display_html($boost); ?>
                                        </div>
                                        <?php $this->display_tabs($boost);?>
                                        <?php
                                        break;
                                    default:
                                        break;
                                }
                                $this->display_exclude_options($boost);
                                ?>
                                <?php
                                break;
                            case '4':
                                ?>
                                <div class="boost-make-boost-left-side">
                                <?php
                                switch ($boost_type) {
	                                case 'leads':
		                                ?>
                                        <div class="boost-make-boost-type boost-make-boost-type-leads">
                                            <div class="boost-make-boost-type-head"><?= __( 'Any Form', 'boost' ) ?></div>
                                            <div class="boost-make-boost-type-description"><?= __( 'Show boosts when a form is completed. Works with most forms and popup.', 'boost' ) ?></div>
                                        </div>
		                                <?php
		                                break;
	                                case 'woocommerce':
		                                ?>
                                        <div class="boost-make-boost-type boost-make-boost-type-woocommerce">
                                            <div class="boost-make-boost-type-head"><?= __( 'Woo Commerce Boost', 'boost' ) ?></div>
                                            <div class="boost-make-boost-type-description"><?= __( 'Capture leads when a transaction takes place, or show stock messages.', 'boost' ) ?></div>
                                        </div>
		                                <?php
		                                break;
	                                case 'easydigitaldownloads':
		                                ?>
                                        <div class="boost-make-boost-type boost-make-boost-type-easydigitaldownloads">
                                            <div class="boost-make-boost-type-head"><?= __( 'Easy Digital Downloads', 'boost' ) ?></div>
                                            <div class="boost-make-boost-type-description"><?= __( 'Capture leads when a transaction takes place.', 'boost' ) ?></div>
                                        </div>
		                                <?php
		                                break;
	                                default:
		                                break;
                                }
                                ?>
                                    <div class="boost-make-boost-review-fields boost-make-boost-type-<?=$boost['type']?>">
                                        <div class="boost-make-boost-field boost-make-boost-review-field">
                                            <div class="boost-review-row">
                                                <?= (!empty($boost['subtype']) ? $trigger_types[$boost['type']]['subtypes'][$boost['subtype']] : $trigger_types[$boost['type']]['name']) ?>
                                            </div>
                                        </div>
                                        <?php
                                        $top_message = $this->replace_tag_to_html_tag((isset($boost['top_message']) ? $boost['top_message'] : ''));
                                        $message = $this->replace_tag_to_html_tag((isset($boost['message']) ? $boost['message'] : ''));
                                        ?>
                                        <div class="boost-make-boost-field boost-make-boost-review-field">
                                            <div class="boost-review-row">
                                                <?= $top_message ?>
                                            </div>
                                        </div>

                                        <div class="boost-make-boost-field boost-make-boost-review-field">
                                            <div class="boost-review-row">
                                                <?= $message ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="boost-make-boost-right-side">
                                    <?php $this->boost_display_html($boost); ?>
                                </div>
                                <?php
                                break;
                            default:
                                switch ($boost_type) {
                                    case 'leads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-leads">
                                                <div class="boost-make-boost-type-head"><?= __('Any Form', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Show boosts when a form is completed. Works with most forms and popup.', 'boost') ?></div>
                                            </div>
                                        </div>
                                        <div class="boost-make-boost-right-side">
                                        </div>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-field boost-make-boost-capture-url">
                                                <div class="boost-input-button-box">
                                                    <input spellcheck="false" type="text" id="boost[capture_url]" name="boost[capture_url]"
                                                           placeholder="<?= __('Capture URL', 'boost'); ?>"
                                                           value="<?= isset($boost['capture_url']) ? htmlspecialchars($boost['capture_url']) : '' ?>"
                                                           class="boost-input-style">
                                                    <button type="button" name="search_forms" value="search_forms"
                                                            class="boost-submit-control-button boost-submit-control-button">
                                                        <div><?= __('GO!', 'boost') ?></div>
                                                    </button>
                                                    <div class="boost-make-boost-field-error-message boost-hidden"></div>
                                                </div>
                                                <div class="boost-make-boost-field-description"><?= __('A lead will be capture when the form found on this URL is completed', 'boost'); ?></div>
                                            </div>
                                        </div>
                                        <div class="boost-make-boost-right-side">
                                            <?php
                                            $forms_data = !empty($boost['capture_url']) ? $this->get_forms_data($boost['capture_url']) : array();
                                            $forms_data = is_array($forms_data) ? $forms_data : array();
                                            ?>
                                            <div class="boost-make-boost-field boost-make-boost-select-field boost-make-boost-form-selector <?= empty($boost['capture_url']) || empty($forms_data) ? 'boost-hidden' : ''?>">
                                                <div class="boost-select2-container">
                                                    <select id="boost[form_selector]" name="boost[form_selector]" class="select2-simple boost-input-style boost-select-form-input-style">
                                                        <?php
                                                        foreach ($forms_data as $form) {

                                                            $form_name = isset($form['id']) && $form['id'] != '' ? 'form#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
                                                            $form_id = isset($form['id']) && $form['id'] != '' ? '#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
                                                            if ($form_name != '') {
    //															?>
                                                            <option class="class!" value="<?= $form_id ?>" <?php selected((isset($boost['form_selector']) ? $boost['form_selector'] : ''), $form_id); ?>><?= $form_name ?></option>
                                                            <?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div id="capture_form_fields_container" class="boost-hidden">
                                                    <select id="capture_form_fields">
                                                        <?php
                                                        foreach ($forms_data as $form) {
                                                            $form_name = isset($form['id']) && $form['id'] != '' ? 'form#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        $form_id = isset($form['id']) && $form['id'] != '' ? '#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        if (!empty($form_name)) {
                                                            if (isset($form['fields']) && is_array($form['fields'])){
                                                            foreach ($form['fields'] as $field){
    //															?>
                                                            <option value="<?= $field['value'] ?>" data-form-id="<?= $form_id ?>" ><?= $field['name'] ?></option>
                                                            <?php
                                                            }
                                                            }
                                                        }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="boost-make-boost-field boost-make-boost-select-field boost-make-boost-form-username-field <?= empty($boost['capture_url']) || empty($forms_data) ? 'boost-hidden' : ''?>">
                                                <div class="boost-make-boost-field-label"><?= __('Choose the name field:', 'boost'); ?></div>
                                                <div class="boost-select2-container">
                                                    <select id="boost[form_username_field]" name="boost[form_username_field]" class="select2-simple boost-input-style boost-select-form-input-style">
                                                        <?php
                                                        foreach ($forms_data as $index => $form) {
                                                            $form_name = isset($form['id']) && $form['id'] != '' ? 'form#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        $form_id = isset($form['id']) && $form['id'] != '' ? '#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        if(!empty($form_name) && !empty($boost['form_selector']) && $form_id == $boost['form_selector'] || empty($boost['form_selector']) && $index == 0){
                                                            if (isset($form['fields']) && is_array($form['fields'])){
                                                            foreach ($form['fields'] as $field){
    //															?>
                                                            <option value="<?= $field['value'] ?>" data-form-id="<?= $form_id ?>" <?php selected((isset($boost['form_username_field']) ? $boost['form_username_field'] : ''), $field['value']); ?>><?= $field['name'] ?></option>
                                                            <?php
                                                            }
                                                            }
                                                        }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="boost-make-boost-field boost-make-boost-select-field boost-make-boost-form-surname-field <?= empty($boost['capture_url']) || empty($forms_data) ? 'boost-hidden' : ''?>">
                                                <div class="boost-make-boost-field-label"><?= __('Choose the surname field:', 'boost'); ?></div>
                                                <div class="boost-select2-container">
                                                    <select id="boost[form_surname_field]" name="boost[form_surname_field]" class="select2-simple boost-input-style boost-select-form-input-style">
                                                        <?php
                                                        foreach ($forms_data as $index => $form) {
                                                            $form_name = isset($form['id']) && $form['id'] != '' ? 'form#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        $form_id = isset($form['id']) && $form['id'] != '' ? '#' . $form['id'] : (isset($form['selector']) ? $form['selector'] : '');
	                                                        if(!empty($form_name) && !empty($boost['form_selector']) && $form_id == $boost['form_selector'] || empty($boost['form_selector']) && $index == 0){
                                                            if (isset($form['fields']) && is_array($form['fields'])){
                                                            foreach ($form['fields'] as $field){
    //															?>
                                                            <option value="<?= $field['value'] ?>" data-form-id="<?= $form_id ?>" <?php selected((isset($boost['form_surname_field']) ? $boost['form_surname_field'] : ''), $field['value']); ?>><?= $field['name'] ?></option>
                                                            <?php
                                                            }
                                                        }
                                                        }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <?php $this->fake_block_html($boost)?>
                                        <?php

                                        break;
                                    case 'woocommerce':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-woocommerce">
                                                <div class="boost-make-boost-type-head"><?= __('Woo Commerce Boost', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place, or show stock messages.', 'boost') ?></div>
                                            </div>
                                        </div>

                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-field boost-make-boost-subtype">
                                                <div class="boost-select2-container">
                                                    <select data-containers-box-id="boost-make-boost-subtype-containers" class="select2-standard boost-input-style" id="boost[subtype]" name="boost[subtype]">
                                                        <?php
                                                        foreach ($trigger_types[$boost_type]['subtypes'] as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?php selected((isset($boost['subtype']) ? $boost['subtype'] : ''), $key); ?>><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="boost-make-boost-field-description">
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-transaction <?= isset($boost['subtype']) && 'transaction' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('A lead will be created anytime a transaction takes place', 'boost') ?>
                                                    </span>
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-specific_transaction <?= isset($boost['subtype']) && 'specific_transaction' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('A lead will be created anytime a specific transaction takes place', 'boost') ?>
                                                    </span>
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-stock_messages <?= isset($boost['subtype']) && 'stock_messages' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <span class="boost-make-boost-subtype-description-text">
                                                            <?= __('A stock warning will show on each product page. WooCommerce stock management must be turned on.', 'boost') ?>
                                                        </span>
                                                        <span class="boost-icon-help">
                                                            <div class="boost-help-data">
                                                                <div class="boost-help-header"><?=__('WooCommerce', 'boost')?></div>
                                                                <div><?='1) '.__('Settings', 'boost')?></div>
                                                                <div><?='2) '.__('Products', 'boost')?></div>
                                                                <div><?='3) '.__('Inventory', 'boost')?></div>
                                                                <div><?='4) '.__('Manage Stock', 'boost')?></div>
                                                            </div>
                                                        </span>
                                                    </span>
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-add_to_cart <?= isset($boost['subtype']) && 'add_to_cart' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('When another visitors clicks on a product add to cart we show a message to all the other visitors on that specific product.', 'boost') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="boost-make-boost-subtype-containers">
                                            <div id="boost-make-boost-subtype-container-specific_transaction" class="boost-make-boost-subtype-container boost-make-boost-specific_transaction <?= isset($boost['subtype']) && $boost['subtype'] == 'specific_transaction' ? 'boost-active' : ''?>">
                                                <div class="boost-make-boost-left-side">
                                                    <div class="boost-make-boost-field boost-make-boost-products boost-select2-container">
                                                        <div>
                                                            <select multiple="multiple" class="boost-search-items boost-input-style boost-search-wc-products" id=""
                                                                    name="boost[products][]" data-placeholder="<?= __('Search for a WooCommerce product...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                                                <?php
                                                                if (isset($boost['products']) && is_array($boost['products'])) {
                                                                    foreach ($boost['products'] as $product) {
                                                                        ?>
                                                                        <option value="<?= $product['product_id'] ?>"
                                                                                selected="selected"><?= $product['product_name'] ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="boost-make-boost-field-description">
                                                            <?= __('A lead will be created anytime one of the products is bought.', 'boost') ?>
                                                        </div>
                                                    </div>
                                                    <div class="boost-make-boost-field boost-make-boost-categories boost-select2-container">
                                                        <div>
                                                            <select multiple="multiple" class="boost-search-items boost-input-style boost-search-wc-product-categories" id=""
                                                                    name="boost[categories][]" data-placeholder="<?= __('Search for a WooCommerce product category...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                                                <?php
                                                                if (isset($boost['categories']) && is_array($boost['categories'])) {
                                                                    foreach ($boost['categories'] as $category) {
                                                                        ?>
                                                                        <option value="<?= $category['category_id'] ?>"
                                                                                selected="selected"><?= $category['category_name'] ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="boost-make-boost-field-description">
                                                            <?= __('A lead will be created anytime one of the products from the categories is bought.', 'boost') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="boost-make-boost-subtype-container-stock_messages" class="boost-make-boost-subtype-container boost-make-boost-stock_messages <?= isset($boost['subtype']) && $boost['subtype'] == 'stock_messages' ? 'boost-active' : ''?>">
                                                <div class="boost-make-boost-left-side">
                                                    <div class="boost-make-boost-field boost-make-boost-stock-number">
                                                        <div class="boost-make-boost-field-label">
                                                            <?= __('Show boost when', 'boost') . '<br>' . __('stock is equal or less then:', 'boost')?>
                                                        </div>
                                                        <div class="boost-spinner">
                                                            <div class="spinner-data">
                                                                <a class="spinner-control spinner-down"></a>
                                                                <input type="number" min="1" max="1000" id="boost[stock_number]" name="boost[stock_number]" value="<?= (isset($boost['stock_number']) ? $boost['stock_number'] : '10') ?>">
                                                                <a class="spinner-control spinner-up"></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="boost-make-boost-subtype-container-add_to_cart" class="boost-make-boost-subtype-container boost-make-boost-add_to_cart <?= isset($boost['subtype']) && $boost['subtype'] == 'add_to_cart' ? 'boost-active' : ''?>">
                                            </div>
                                        </div>
                                        <?php $this->fake_block_html($boost)?>
                                        <?php
                                        break;
                                    case 'easydigitaldownloads':
                                        ?>
                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-type boost-make-boost-type-easydigitaldownloads">
                                                <div class="boost-make-boost-type-head"><?= __('Easy Digital Downloads', 'boost') ?></div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place.', 'boost') ?></div>
                                            </div>
                                        </div>

                                        <div class="boost-make-boost-left-side">
                                            <div class="boost-make-boost-field boost-make-boost-subtype">
                                                <div class="boost-select2-container">
                                                    <select data-containers-box-id="boost-make-boost-subtype-containers" class="select2-standard boost-input-style" id="boost[subtype]" name="boost[subtype]">
                                                        <?php
                                                        foreach ($trigger_types[$boost_type]['subtypes'] as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?php selected((isset($boost['subtype']) ? $boost['subtype'] : ''), $key); ?>><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="boost-make-boost-field-description">
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-transaction <?= isset($boost['subtype']) && 'transaction' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('A lead will be created anytime a transaction takes place', 'boost') ?>
                                                    </span>
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-specific_transaction <?= isset($boost['subtype']) && 'specific_transaction' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('A lead will be created anytime a specific transaction takes place', 'boost') ?>
                                                    </span>
                                                    <span class="boost-make-boost-subtype-description boost-make-boost-subtype-add_to_cart <?= isset($boost['subtype']) && 'add_to_cart' == $boost['subtype'] ? 'boost-active' : ''?>">
                                                        <?= __('When another visitors clicks on a product add to cart we show a message to all the other visitors on that specific product.', 'boost') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="boost-make-boost-subtype-containers">
                                            <div id="boost-make-boost-subtype-container-specific_transaction" class="boost-make-boost-subtype-container boost-make-boost-specific_transaction <?= isset($boost['subtype']) && $boost['subtype'] == 'specific_transaction' ? 'boost-active' : ''?>">
                                                <div class="boost-make-boost-left-side">
                                                    <div class="boost-make-boost-field boost-make-boost-products boost-select2-container">
                                                        <div>
                                                            <select multiple="multiple" id="boost[products]" name="boost[products][]"
                                                                    class="boost-search-items boost-input-style boost-search-edd-downloads"
                                                                    data-placeholder="<?= __('Search for a EDD download...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                                                <?php
                                                                if(isset($boost['products']) && is_array($boost['products'])) {
                                                                    foreach($boost['products'] as $product) {
                                                                        ?>
                                                                        <option value="<?=$product['product_id']?>" selected="selected"><?=$product['product_name']?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="boost-make-boost-field-description">
                                                            <?= __('A lead will be created anytime one of the products is bought.', 'boost') ?>
                                                        </div>
                                                    </div>
                                                    <div class="boost-make-boost-field boost-make-boost-categories boost-select2-container">
                                                        <div>
                                                            <select multiple="multiple" id="boost[categories]" name="boost[categories][]"
                                                                    class="boost-search-items boost-input-style boost-input-style boost-search-edd-download-categories"
                                                                    data-placeholder="<?= __('Search for a EDD download category...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                                                <?php
                                                                if (isset($boost['categories']) && is_array($boost['categories'])) {
                                                                    foreach ($boost['categories'] as $category) {
                                                                        ?>
                                                                        <option value="<?= $category['category_id'] ?>"
                                                                                selected="selected"><?= $category['category_name'] ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <div class="boost-make-boost-field-description">
                                                            <?= __('A lead will be created anytime one of the products from the categories is bought.', 'boost') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php $this->fake_block_html($boost)?>

                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <div class="boost-make-boost-types">
                                            <button type="submit" name="submit" value="select_type"
                                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_type=leads'); ?>"
                                                    class="boost-make-boost-type">

                                                <div class="boost-make-boost-type-data-container boost-leads-background">
                                                    <div class="boost-make-boost-type-data">
                                                        <div class="boost-make-boost-type-img">
                                                            <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/leads_icon.png' ?>">
                                                        </div>
                                                        <div class="boost-make-boost-type-name"><?= __('Any Form', 'boost') ?></div>
                                                    </div>
                                                </div>
                                                <div class="boost-make-boost-type-description"><?= __('Show boosts when a form is completed. Works with most forms and popup.', 'boost') ?></div>

                                            </button>
                                            <button type="submit" name="submit" value="select_type"
                                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_type=woocommerce'); ?>"
                                                    class="boost-make-boost-type">

                                                <div class="boost-make-boost-type-data-container boost-woocommerce-background">
                                                    <div class="boost-make-boost-type-data">
                                                        <div class="boost-make-boost-type-img">
                                                            <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/woocommerce_icon.png' ?>">
                                                        </div>
                                                        <div class="boost-make-boost-type-name"><?= __('Woo Commerce', 'boost') ?></div>
                                                    </div>
                                                </div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place, or show stock messages.', 'boost') ?></div>

                                            </button>
                                            <button type="submit" name="submit" value="select_type"
                                                    formaction="<?= admin_url('/admin.php?page=boost_make_boost&step=1&boost_type=easydigitaldownloads'); ?>"
                                                    class="boost-make-boost-type">

                                                <div class="boost-make-boost-type-data-container boost-easydigitaldownloads-background">
                                                    <div class="boost-make-boost-type-data">
                                                        <div class="boost-make-boost-type-img">
                                                            <img src="<?= plugin_dir_url( __FILE__ ) . 'imgs/easydigitaldownloads_icon.png' ?>">
                                                        </div>
                                                        <div class="boost-make-boost-type-name"><?= __('Easy Digital Downloads', 'boost') ?></div>
                                                    </div>
                                                </div>
                                                <div class="boost-make-boost-type-description"><?= __('Capture leads when a transaction takes place.', 'boost') ?></div>

                                            </button>
                                        </div>
                                        <?php
                                        break;
                                }
                                break;
                        }
                        ?>
                                <div class="boost-make-boost-cancel-buttons <?= !empty($boost_type) ? 'boost-align-right' : 'boost-align-center' ?>">
                                <?php
                                if (!empty($boost_type) && in_array($boost_type, array('leads' ,'woocommerce', 'easydigitaldownloads'))){
                                    ?>
                                        <button type="submit" name="submit" value="save_boost"
                                                formaction="<?= admin_url('/admin.php?page=boost_main') ?>"
                                                class="<?= $step < 4 ? 'boost-button-cancel' : 'boost-button-primary' ?> boost-button">

                                                <?= __('Save & Close', 'boost') ?>

                                        </button>
                                    <?php
                                    if ($step < 4) {
	                                    ?>
                                        <button type="submit" name="submit" value="next"
                                                formaction="<?= admin_url( '/admin.php?page=boost_make_boost&step=' . ( $step + 1 ) . '&boost_id=' . $boost_id ); ?>"
                                                class="boost-button-primary boost-button">

		                                    <?= __( 'Continue', 'boost' ) ?>

                                        </button>

	                                    <?php
                                    }
	                                ?>
                                <?php
                                }
                                else {
                                    ?>
                                        <button type="submit" name="submit" value="cancel"
                                                formaction="<?= admin_url('/admin.php?page=boost_main') ?>"
                                                class="boost-button-cancel boost-button">

                                                <?= __('Cancel', 'boost') ?>

                                        </button>
                                    <?php
                                }
                                ?>
                                </div>
                    </div>
                </form>
			</div>
		</div>
	<?php
	}

	public function display_plugin_action_list_page()
	{
		if(isset($_POST['submit'])) {
			$action_id = !empty($_POST['action_id']) ? $_POST['action_id'] : 0;
			switch ($_POST['submit']) {
				case 'delete':
					if (!empty($action_id)) {
						$this->action_model->delete_actions(array('id' => $action_id), array(), 1);
					}
					break;
				default:
					break;
			}
		}

		$actions_on_page = 20;
		$search = !empty($_GET['s']) ? stripslashes_deep($_GET['s']) : '';
		$actions_count = $this->action_model->get_actions_count(array('search_value' => $search));
		$number_of_pages = ceil($actions_count / $actions_on_page);
		$page = !empty($_GET['pn']) && $_GET['pn'] > 0 && $_GET['pn'] <= $number_of_pages ? $_GET['pn'] : '1';

		$actions = $this->action_model->get_actions(array('search_value' => $search), array('time' => 'DESC'), $actions_on_page, ($page - 1) * $actions_on_page);
		?>
		<div class="wrap">
			<div class="boost-page boost-action-list">
				<div class="boost-loading"></div>
				<div class="boost-action-list-container">
						<div id="actions-filter" >
							<form method="get" action="<?= admin_url('/admin.php'); ?>">
								<p class="search-box">
									<label class="screen-reader-text"
										   for="action-search-input"><?= __('Search actions', 'boost') ?>:</label>
									<input type="search" id="action-search-input" name="s" value="<?=$search?>" class="boost-items-list-pages-input-style">
									<input type="submit" id="action-search-submit" class="boost-button boost-button-small boost-button-primary" value="Search actions">
								</p>
								<input type="hidden" id="page" name="page" value="boost_action_list">
							</form>
							<div class="tablenav top">
								<?php $this->get_pagination_html('boost_action_list', $page, $number_of_pages, $actions_count, $search)?>
							</div>
							<table id="edit_action" class="boost-items-list-table boost-action-list-table boost-table /wp-list-table /widefat /fixed /striped">
								<thead>
								<tr>

									<th class="manage-column column-action-id num"
										scope="col"><?= __('#ID', 'boost') ?></th>
									<th class="manage-column column-action-name"
										scope="col"><?= __('User name', 'boost') ?></th>
									<th class="manage-column column-action-country"
										scope="col"><?= __('Country', 'boost') ?></th>
									<th class="manage-column column-action-state"
										scope="col"><?= __('State', 'boost') ?></th>
									<th class="manage-column column-action-town"
										scope="col"><?= __('Town', 'boost') ?></th>
									<th class="manage-column column-action-actions"
										scope="col"><?= __('Actions', 'boost') ?></th>

								</tr>
								</thead>

								<tbody id="the-list">
								<?php
								if (count($actions) == 0) {
									?>
									<tr><th colspan="6"><?= __('There are no actions!', 'boost') ?></th></tr>
									<?php
								}
								else {
									foreach ($actions as $action) {
										?>
										<tr id="<?= $action['id'] ?>"
											class="boost-items-list-row boost-action-list-row"
											valign="top">
											<th class="column-items-id"><?= $action['id'] ?></th>
											<th id="user_name"
												class="boost-items-list-changeable-cell column-item-user_name"><?= $action['user_name'] ?></th>
											<th id="country"
												class="boost-items-list-changeable-cell column-item-country"><?= $action['country'] ?></th>
											<th id="state"
												class="boost-items-list-changeable-cell column-item-state"><?= $action['state'] ?></th>
											<th id="town"
												class="boost-items-list-changeable-cell column-item-town"><?= $action['town'] ?></th>
											<th class="column-item-actions column-action-actions">
												<div
													class="boost-items-list-table-actions boost-action-list-table-actions">
												<span>
													<form method="POST" action="<?=admin_url('/admin.php?page=boost_action_list')?>">
														<input type="hidden" name="action_id" value="<?=$action['id']?>">
													<button
														type="button"
														name="edit"
														value="edit"
														class="boost-button boost-button-small boost-button-action action-edit">
                                                        <span class="boost-action-icon"></span>
														<span><?= __('Edit', 'boost') ?></span>
													</button>
													<button type="submit"
															name="submit"
															value="delete"
															formaction="<?= admin_url("/admin.php?page=boost_action_list&s=$search&pn=$page"); ?>"
															class="boost-button boost-button-small boost-button-action action-delete">
                                                        <span class="boost-action-icon"></span>
														<span><?= __('Delete', 'boost') ?></span>
													</button>
													</form>
												</span>
												</div>
											</th>
										</tr>
										<?php
									}
								}
								?>
								</tbody>

								<tfoot>
								<tr>

									<th class="manage-column column-action-id num"
										scope="col"><?= __('#ID', 'boost') ?></th>
									<th class="manage-column column-action-name"
										scope="col"><?= __('User name', 'boost') ?></th>
									<th class="manage-column column-action-country"
										scope="col"><?= __('Country', 'boost') ?></th>
									<th class="manage-column column-action-state"
										scope="col"><?= __('State', 'boost') ?></th>
									<th class="manage-column column-action-town"
										scope="col"><?= __('Town', 'boost') ?></th>
									<th class="manage-column column-action-actions"
										scope="col"><?= __('Actions', 'boost') ?></th>

								</tr>
								</tfoot>

							</table>
							<div class="tablenav bottom">
								<?php $this->get_pagination_html('boost_action_list', $page, $number_of_pages, $actions_count, $search)?>
								<br class="clear">
							</div>
						</div>
				</div>
			</div>
		</div>
		<?php
	}
	
	public function display_plugin_banned_word_list_page()
	{
		if (isset($_POST['submit'])) {
			$banned_word_id = !empty($_POST['banned_word_id']) ? $_POST['banned_word_id'] : 0;
			switch ($_POST['submit']) {
				case 'delete':
					if (!empty($banned_word_id)) {
						$this->banned_word_model->delete_banned_words(array('id' => $banned_word_id), array(), 1);
					}
					break;
				case 'add_banned_word':
					$new_banned_word = stripslashes_deep($_POST['new_banned_word']);
					if (!empty($new_banned_word)) {
						$this->banned_word_model->create_banned_word(array('word' => $new_banned_word));
					}
					break;
				default:
					break;
			}
		}

		$banned_words_on_page = 20;
		$search = !empty($_GET['s']) ? stripslashes_deep($_GET['s']) : '';
		$banned_words_count = $this->banned_word_model->get_banned_words_count(array('search_value' => $search));
		$number_of_pages = ceil($banned_words_count / $banned_words_on_page);
		$page = !empty($_GET['pn']) && $_GET['pn'] > 0 && $_GET['pn'] <= $number_of_pages ? $_GET['pn'] : '1';

		$banned_words = $this->banned_word_model->get_banned_words(array('search_value' => $search), array(), $banned_words_on_page, ($page - 1) * $banned_words_on_page);
		?>
		<div class="wrap">
			<div class="boost-page boost-banned-word-list">
				<div class="boost-loading"></div>
				<div class="boost-banned-word-list-container">
					<div id="banned-words-filter" class="">
						<form method="get" action="<?= admin_url('/admin.php'); ?>">
							<p class="search-box">
								<label class="screen-reader-text"
									   for="banned-word-search-input"><?= __('Search banned words', 'boost') ?>
									:</label>
								<input type="search" id="banned-word-search-input" name="s" value="<?= $search ?>" class="boost-items-list-pages-input-style">
								<input type="submit" id="banned-word-search-submit" class="boost-button boost-button-small boost-button-primary"
									   value="Search banned words">
							</p>
							<input type="hidden" id="page" name="page" value="boost_banned_word_list">
						</form>
						<div class="tablenav top">
							<div class="alignleft /actions /bulkactions">
								<form action="<?= admin_url("/admin.php?page=boost_banned_word_list&pn=$page&s=$search"); ?>" method="POST">
									<label for="new_banned_word" class="screen-reader-text"><?=__('Add new banned word', 'boost')?></label>
									<input type="text" name="new_banned_word" id="new_banned_word" class="boost-items-list-pages-input-style"/>
									<button type="submit" name="submit" class="boost-button boost-button-small boost-button-primary action" value="add_banned_word">
										<span><?=__('Add new banned word', 'boost')?></span>
									</button>
								</form>
							</div>
							<?php $this->get_pagination_html('boost_banned_word_list', $page, $number_of_pages, $banned_words_count, $search)?>
						</div>
						<table id="edit_banned_word"
							   class="boost-items-list-table boost-banned-word-list-table boost-table /wp-list-table /widefat /fixed /striped">
							<thead>
							<tr>

								<th class="manage-column column-action-id num"
									scope="col"><?= __('#ID', 'boost') ?></th>
								<th class="manage-column column-action-name"
									scope="col"><?= __('Banned word', 'boost') ?></th>
								<th class="manage-column column-action-actions"
									scope="col"><?= __('Actions', 'boost') ?></th>

							</tr>
							</thead>

							<tbody id="the-list">
							<?php
							if (count($banned_words) == 0) {
								?>
								<tr>
									<th colspan="3"><?= __('There are no banned words!', 'boost') ?></th>
								</tr>
								<?php
							} else {
								foreach ($banned_words as $banned_word) {
									?>
									<tr id="<?= $banned_word['id'] ?>"
										class="boost-items-list-row boost-banned-word-list-row"
										valign="top">
										<th class="column-items-id"><?= $banned_word['id'] ?></th>
										<th id="word"
											class="boost-items-list-changeable-cell column-item-user_name"><?= $banned_word['word'] ?></th>
										<th class="column-item-actions column-banned-word-actions">
											<div
												class="boost-items-list-table-actions boost-banned-word-list-table-actions">
												<span>
													<form action="<?= admin_url('/admin.php?page=boost_banned_word_list'); ?>" method="POST">
														<input type="hidden" name="banned_word_id" value="<?=$banned_word['id']?>">
														<button
															type="button"
															name="edit"
															value="edit"
															class="boost-button boost-button-small boost-button-action action-edit">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('Edit', 'boost') ?></span>
														</button>
														<button type="submit"
																name="submit"
																value="delete"
																formaction="<?= admin_url("/admin.php?page=boost_banned_word_list&s=$search&pn=$page"); ?>"
																class="boost-button boost-button-small boost-button-action action-delete">
                                                            <span class="boost-action-icon"></span>
                                                            <span><?= __('Delete', 'boost') ?></span>
														</button>
													</form>
												</span>
											</div>
										</th>
									</tr>
									<?php
								}
							}
							?>
							</tbody>

							<tfoot>
							<tr>

								<th class="manage-column column-action-id num"
									scope="col"><?= __('#ID', 'boost') ?></th>
								<th class="manage-column column-action-name"
									scope="col"><?= __('Banned word', 'boost') ?></th>
								<th class="manage-column column-action-actions"
									scope="col"><?= __('Actions', 'boost') ?></th>

							</tr>
							</tfoot>

						</table>
						<div class="tablenav bottom">
							<?php $this->get_pagination_html('boost_banned_word_list', $page, $number_of_pages, $banned_words_count, $search)?>
							<br class="clear">
						</div>
					</div>
			    </div>
		    </div>
        </div>
		<?php
	}

	/**
	 * AJAX callback function.
	 */
	public function boost_search_items_ajax_handler()
	{
		$nonce = empty($_POST['nonce']) ? '' : stripslashes_deep($_POST['nonce']);
		if (!wp_verify_nonce($nonce, 'boost_search_items_nonce')
			|| !isset($_POST['search_type']) || !isset($_POST['search_string'])
		) die(__('Error', 'boost'));
		$search_type = stripslashes_deep($_POST['search_type']);
		$search_string = stripslashes_deep($_POST['search_string']);
		$items = array();
		switch ($search_type) {
			case 'post_types':
				$items = $this->get_post_types($search_string);
				break;
			case 'categories':
				$items = $this->get_categories_by_name('category', $search_string);
				break;
			case 'wc_products':
				$items = $this->get_posts_by_title('product', $search_string);
				break;
			case 'wc_product_categories':
				$items = $this->get_categories_by_name('product_cat', $search_string);
				break;
			case 'edd_downloads':
				$items = $this->get_posts_by_title('download', $search_string);
				break;
			case 'edd_download_categories':
				$items = $this->get_categories_by_name('download_category', $search_string);
				break;
			case 'taxonomies':
				$items = $this->get_taxonomies_and_terms_by_name($search_string);
				break;
			case 'specific_pages':
				$items = $this->get_specific_pages_by_title($search_string);
				break;
			default:
				break;
		}
		wp_send_json_success(array('items' => $items));
		wp_die();
	}
	/**
	 * AJAX callback function.
	 */
	public function boost_edit_item_ajax_handler()
	{
		$nonce = empty($_POST['nonce']) ? '' : stripslashes_deep($_POST['nonce']);
		if (!wp_verify_nonce($nonce, 'boost_edit_item_nonce')
			|| !isset($_POST['item_id']) || !isset($_POST['item_data']) || !isset($_POST['action_type'])
		) die(__('Error', 'boost'));

		switch ($_POST['action_type']) {
			case 'edit_action':
				$action_id = $_POST['item_id'];
				$action_data = stripslashes_deep($_POST['item_data']);
				$updated_action = $this->action_model->update_action($action_id, $action_data);
				wp_send_json_success(array('item_id' => $action_id, 'item_data' => $updated_action));
				break;
			case 'edit_banned_word':
				$banned_word_id = $_POST['item_id'];
				$banned_word_data = stripslashes_deep($_POST['item_data']);
				$updated_banned_word = $this->banned_word_model->update_banned_word($banned_word_id, $banned_word_data);
				wp_send_json_success(array('item_id' => $banned_word_id, 'item_data' => $updated_banned_word));
				break;
			default:
				break;
		}
		wp_die();
	}
	/**
	 * AJAX callback function.
	 */
	public function boost_search_forms_ajax_handler()
	{
		$nonce = empty($_POST['nonce']) ? '' : stripslashes_deep($_POST['nonce']);
		if (!wp_verify_nonce($nonce, 'boost_search_forms_nonce')
			|| !isset($_POST['capture_url'])
		) die(__('Error', 'boost'));

		$forms_data = $this->get_forms_data(urldecode($_POST['capture_url']));
		$data = array(
			'forms_data' => $forms_data
		);
		if ($forms_data === false) {
			$data['error'] = __('Error! Couldn\'t get the page HTML.', 'boost');
		}
		elseif (!is_array($forms_data) || is_array($forms_data) && count($forms_data) == 0) {
			$data['error'] = __('Error! Forms not found on the page.', 'boost');
		}
		wp_send_json_success($data);

		wp_die();
	}

	public function get_posts_by_title($post_type, $search_string) {
		global $wpdb;
		$posts = $wpdb->get_results( $wpdb->prepare("SELECT `id`, `post_title` FROM $wpdb->posts WHERE `post_type` = '%s' AND `post_title` LIKE '%s'", $post_type, '%'. $wpdb->esc_like( $search_string ) .'%' ));
		return $posts;
	}

	public function get_categories_by_name($taxonomy, $search_string) {
		$term_query = new WP_Term_Query( array(
			'taxonomy' => $taxonomy,
			'name__like' => "$search_string",
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false
		) );
		$categories = $term_query->get_terms();
		return $categories;
	}

	public function get_terms_by_name($search_string) {
		$term_query = new WP_Term_Query( array(
			'name__like' => "$search_string",
			'orderby' => 'name',
			'order' => 'ASC',
			'hide_empty' => false
		) );
		$terms = $term_query->get_terms();
		return $terms;
	}

	public function get_taxonomies_by_name($search_string) {
		$taxonomies = get_taxonomies(array('public' => true), 'objects');

		foreach ($taxonomies as $index => $taxonomy) {
			if (stripos($taxonomy->name, $search_string) === false
                && stripos($taxonomy->label, $search_string) === false) {
				unset($taxonomies[$index]);
			}
		}
		return $taxonomies;
	}

	public function get_taxonomies_and_terms_by_name($search_string) {
		$term_query = new WP_Term_Query( array(
			'taxonomy'   => get_taxonomies( array( 'public' => true ), 'names' ),
			'name__like' => "$search_string",
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => false
		) );
		$terms      = $term_query->get_terms();

		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );
		foreach ( $taxonomies as $index => $taxonomy ) {
			if ( stripos( $taxonomy->name, $search_string ) === false
			     && stripos( $taxonomy->label, $search_string ) === false ) {
				unset( $taxonomies[ $index ] );
			}
		}

		return array('terms' => $terms, 'taxonomies' => $taxonomies);
	}

	public function get_specific_pages_by_title($search_string) {
		global $wpdb;
		$specific_pages = $wpdb->get_results( $wpdb->prepare("SELECT `id`, `post_title`, `post_type` FROM $wpdb->posts WHERE `post_title` LIKE '%s' AND `post_parent` = '0' AND `post_status` = 'publish'", '%'. $wpdb->esc_like( $search_string ) .'%' ));
		return $specific_pages;
	}

	public function get_post_types($search_string) {

		$all_post_types = get_post_types(array('public' => true), 'objects');
		$post_types = array();
		foreach ($all_post_types as $post_type) {
			if (stripos($post_type->label, $search_string) !== false) {
				$post_types[] = array(
					'id' => $post_type->name,
					'name' => $post_type->label
				);
			}
		}
		return $post_types;
	}

	public function boost_cronstarter_activation() {
		if( !wp_next_scheduled( 'boost_fake_boosts_job' ) ) {
			wp_schedule_event( time(), 'daily', 'boost_fake_boosts_job' );
		}
	} 

	public function get_pagination_html($page, $page_number, $number_of_pages, $items_count, $search){
		?>
		<div class="tablenav-pages">
            <span class="displaying-num">
                <?= $items_count . ' ' . __('items', 'boost') ?>
            </span>
            <span class="pagination-links">
                <a class="first-page"
                   href="<?= admin_url("/admin.php?page=$page&s=$search&pn=1") ?>">
                    <span
                        class="screen-reader-text"><?= __('First page', 'boost') ?></span>
                    <span aria-hidden="true">«</span>
                </a>
                <a class="prev-page"
                   href="<?= admin_url("/admin.php?page=$page&s=$search") ?>&pn=<?= $page_number > 1 ? ($page_number - 1) : 1 ?>">
                    <span class="screen-reader-text"><?= __('Previous page', 'boost') ?></span>
                    <span aria-hidden="true">‹</span>
                </a>
                <span class="paging-input">
                    <label for="current-page-selector"
                           class="screen-reader-text"><?= __('Current Page', 'boost') ?></label>
                    <input
                        class="current-page" id="current-page-selector" type="text" name="pn"
                        value="<?= $page_number ?>" size="1"
                        aria-describedby="table-paging">
                    <span class="tablenav-paging-text"> <?= __('of', 'boost') ?> <span
                            class="total-pages"><?= $number_of_pages ?></span>
                    </span>
                </span>
                <a class="next-page"
                   href="<?= admin_url("/admin.php?page=$page&s=$search") ?>&pn=<?= $page_number < $number_of_pages ? ($page_number + 1) : $number_of_pages ?>">
                    <span
                        class="screen-reader-text"><?= __('Next page', 'boost') ?></span>
                    <span
                        aria-hidden="true">›</span>
                </a>
                <a class="last-page"
                   href="<?= admin_url("/admin.php?page=$page&s=$search&pn=$number_of_pages"); ?>">
                    <span class="screen-reader-text"><?= __('Last page', 'boost') ?></span>
                    <span aria-hidden="true">»</span>
                </a>
                <br class="clear">
            </span>
		</div>
        <?php
	}

    public function get_forms_data($capture_url){
        $forms_data = array();
        $page_html = @file_get_contents($capture_url);
		if ($page_html === false) {
			$curl = curl_init($capture_url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 2 );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/56.0.2924.87 Safari/537.36' );

			$page_html = curl_exec($curl);
			if (!$page_html) return false;
		}

	    $dom = new DomDocument();
        @$dom->loadHTML( $page_html );

        $xpath = new DomXPath( $dom );

        $forms = $xpath->query('.//form');
        foreach ($forms as $form) {
            $form_fields_data = array(
                    array(
	            'id' => '',
	            'name' => 'There is no field',
	            'value' => '',
	            'type' => '',
	            'class' => '',
	            'selector' => ''
            )
            );
            $form_fields = $xpath->query('.//input[@type="text"]', $form);
            foreach ($form_fields as $form_field) {
                $form_fields_data[] = array(
                'id' => $form_field->getAttribute('id'),
                'name' => $form_field->getAttribute('name'),
                'value' => $form_field->getAttribute('name'),
                'type' => $form_field->getAttribute('type'),
                'class' => $form_field->getAttribute('class'),
                'selector' => $this->getFullCSSSelector($form_field, $form)
                );
            }
             $forms_data[] = array(
                'id' => $form->getAttribute('id'),
                'class' => $form->getAttribute('class'),
                'action' => $form->getAttribute('action'),
                'method' => $form->getAttribute('method'),
                'selector' => $this->getFullCSSSelector($form, $xpath->query('.//body')->item(1)),
                'fields' => $form_fields_data
            );
        }

        // search Ninja forms
        if (function_exists('Ninja_Forms')){
            $exclude_field_types = array(
                    'starrating',
                    'recaptcha',
                    'spam',
                    'hidden',
                    'confirm',
                    'html',
                    'total',
                    'liststate',
                    'submit',
                    'listselect',
                    'listradio',
                    'listmultiselect',
                    'listcheckbox',
                    'date',
                    'checkbox',
                    'listcountry',
                    'number',
                    'hr',
                    'total',
                    'product',
                    'shipping',
                    'quantity'
            );

	        $forms = $xpath->query('.//div[@role="form"]');
	        foreach ($forms as $form) {
		        $form_id = preg_replace('/[^0-9]/','', $form->getAttribute('id'));
		        if (!$form_id) continue;
                $form_fields_data = array(
                    array(
                        'id' => '',
                        'name' => 'There is no field',
                        'value' => '',
                        'type' => '',
                        'class' => '',
                        'selector' => ''
                    )
                );
                $form_fields = Ninja_Forms()->form( $form_id )->get_fields();
                foreach ($form_fields as $form_field) {
                    $field_type = $form_field->get_setting('type');
                    if (in_array($field_type, $exclude_field_types)) continue;
                    $field_id = $form_field->get_id();
                    $form_fields_data[] = array(
                        'id' => $field_id,
                        'name' => $form_field->get_setting('label'),
                        'value' => $field_id,
                        'type' => '',
                        'class' => '',
                    );
                }
             $forms_data[] = array(
                'id' => $form->getAttribute('id'),
                'class' => $form->getAttribute('class'),
                'action' => $form->getAttribute('action'),
                'method' => $form->getAttribute('method'),
                'selector' => $this->getFullCSSSelector($form, $xpath->query('.//body')->item(1)),
                'fields' => $form_fields_data
            );
	        }
        }

		return $forms_data;
    }

    private function getNodePos($pNode, $nodeName)
    {
        if($pNode == null)
            {
                    return 0;
        }
        else
        {
            $var = 0;
                if ($pNode->previousSibling != null)
                {
                if ($pNode->previousSibling->nodeName == $nodeName)
                {
                    $var = 1;
                }
                }
                return $this->getNodePos($pNode->previousSibling, $nodeName) + $var;
        }
    }

    private function getFullXpath($pNode)
    {
        if($pNode == null)
            {
                    return "";
        }
        else
        {
            return $this->getFullXpath($pNode->parentNode) . "/" . $pNode->nodeName . "[" .strval($this->getNodePos($pNode, $pNode->nodeName)+1) . "]";//+1 to get the real xPath index
        }
    }

    private function getFullCSSSelector($node, $pNode)
    {
        $nodeID = $this->getNodeID($node);
        if ($nodeID != '') {
            return $node->nodeName . '#' . $nodeID;
        }
        elseif ($node->nodeName == 'body' || $node == $pNode) {
            return $node->nodeName;
        }
        else {
            return $this->getFullCSSSelector($node->parentNode, $pNode) . '>' . $node->nodeName;
        }
    }

    private function getNodeID($node){
        $nodeID = '';
        $nodeAttrs = $node->attributes;
        foreach ($nodeAttrs as $nodeAttr){
            if($nodeAttr->nodeName == 'id'){
                $nodeID = $nodeAttr->nodeValue;
            }
        }
        return $nodeID;
    }

    public function fake_block_html($boost){
    ?>
    <div class="boost-make-boost-fake-block <?= empty($boost['subtype']) || (!empty($boost['subtype']) && 'stock_messages' != $boost['subtype']) ? 'boost-active' : ''?>">
        <div class="boost-make-boost-field boost-make-boost-fake switch-field">
            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                <input type="checkbox" id="boost[enable_fake]" name="boost[enable_fake]"
                       value="1" <?php checked((isset($boost['enable_fake']) ? $boost['enable_fake'] : 0), 1, true) ?>
                class="boost-show-hide-switcher" data-relative-box-id="boost-make-boost-fake-block">
               <label for="boost[enable_fake]"></label>
            </div>
            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                <?= __('Enable fake boosts untill sufficient real leads are captured', 'boost'); ?>
            </div>
        </div>
        <div id="boost-make-boost-fake-block" class="boost-make-boost-fake-block-right-side <?= !$boost['enable_fake'] ? 'boost-hidden' : '' ?>">
            <div class="boost-make-boost-field boost-make-boost-fake-mix">
                <div class="boost-make-boost-field-label"><?= __('Mix fake boosts untill at least', 'boost'); ?></div>
                <div class="boost-spinner">
                    <div class="spinner-data">
                        <a class="spinner-control spinner-down"></a>
                        <input type="number" min="0" max="1000" id="boost[min_actions_limit]" name="boost[min_actions_limit]" value="<?= isset($boost['min_actions_limit']) ? $boost['min_actions_limit'] : '20' ?>">
                        <a class="spinner-control spinner-up"></a>
                    </div>
                </div>
                <div class="boost-make-boost-field-label"><?= __('real leads are captured', 'boost'); ?></div>
            </div>
            <div class="boost-make-boost-field boost-countries-for-fakes-container boost-select2-container">
                <div class="boost-select2-additional-buttons">
                    <button type="button" class="select2-select-all boost-submit-control-button">
                        <span><?= __( 'ALL', 'boost' ) ?></span>
                    </button>
                    <button type="button" class="select2-clear boost-submit-control-button">
                        <span><?= __( 'CLEAR', 'boost' ) ?></span>
                    </button>
                </div>
                <div class="">
                    <select multiple="multiple" id="boost[countries_for_fakes]"
                            name="boost[countries_for_fakes][]"
                            class="select2-multiple boost-input-style boost-countries-for-fakes"
                            data-placeholder="<?= __( 'Select countries...', 'boost' ) ?>" tabindex="-1"
                            aria-hidden="true">
                        <?php
                        include_once __DIR__ . '/../includes/lib/fakedata/fake-data.php';
                        if ( isset( $fake_data ) && is_array( $fake_data ) ) {
                            foreach ( $fake_data as $fake_data_item ) {
                                $selected = in_array( $fake_data_item['country'], ( ! empty( $boost['countries_for_fakes'] ) ? $boost['countries_for_fakes'] : array() ) ) ? ' selected="selected"' : '';
                                ?>
                                <option value="<?= $fake_data_item['country'] ?>" <?= $selected ?>><?= $fake_data_item['country'] ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
        <?php
    }

    public function boost_display_html($boost){
    ?>
    <div class="boost-make-boost-positions-fields boost-inline-block">
            <div class="boost-make-boost-field-container boost-make-boost-desktop-position-field boost-make-boost-position-field <?= $boost['desktop'] ? 'position-active' : '' ?>">
                <div class="boost-make-boost-field-data">
                    <div class="boost-make-boost-field boost-position desktop-position">
                            <input class="boost-position" type="text" id="boost[desktop_position]" name="boost[desktop_position]" value="<?= $boost['desktop_position']?>">
                            <div class="<?= !$boost['desktop'] ? 'disabled' : '' ?> position-place desktop-position-place position-top-place desktop-position-top-place position-left-place desktop-position-left-place position-top-left desktop-position-top-left <?= $boost['desktop_position'] == 'top_left' ? 'active' : ''?>" data-position="top_left"></div>
                            <div class="<?= !$boost['desktop'] ? 'disabled' : '' ?> position-place desktop-position-place position-top-place desktop-position-top-place position-right-place desktop-position-right-place position-top-right desktop-position-top-right <?= $boost['desktop_position'] == 'top_right' ? 'active' : ''?>" data-position="top_right"></div>
                            <div class="<?= !$boost['desktop'] ? 'disabled' : '' ?> position-place desktop-position-place position-bottom-place desktop-position-bottom-place position-right-place desktop-position-right-place position-bottom-right desktop-position-bottom-right <?= $boost['desktop_position'] == 'bottom_right' ? 'active' : ''?>" data-position="bottom_right"></div>
                            <div class="<?= !$boost['desktop'] ? 'disabled' : '' ?> position-place desktop-position-place position-bottom-place desktop-position-bottom-place position-middle-place desktop-position-middle-place position-bottom-middle desktop-position-bottom-middle <?= $boost['desktop_position'] == 'bottom_middle' ? 'active' : ''?>" data-position="bottom_middle"></div>
                            <div class="<?= !$boost['desktop'] ? 'disabled' : '' ?> position-place desktop-position-place position-bottom-place desktop-position-bottom-place position-left-place desktop-position-left-place position-bottom-left desktop-position-bottom-left <?= $boost['desktop_position'] == 'bottom_left' ? 'active' : ''?>" data-position="bottom_left"></div>

                            <div class="boost-checkbox-container boost-display-checkbox">
                                <input type="checkbox" id="boost[desktop]" name="boost[desktop]"
                                       value="1" <?php checked((isset($boost['desktop']) ? $boost['desktop'] : 0), 1, true); ?>
                                       class="boost-checkbox"
                                       data-checked-label="<?= __('DISPLAY ON DESKTOP', 'boost') ?>"
                                       data-unchecked-label="<?= __('HIDE ON DESKTOP', 'boost') ?>">
                                <label for="boost[desktop]" class="desktop-position-placeholder"></label>
                            </div>
                    </div>
                </div>
                <div class="boost-make-boost-field-description"><?= $boost['desktop'] ? __('DISPLAY ON DESKTOP', 'boost') : __('HIDE ON DESKTOP', 'boost') ?></div>
            </div>
            <div class="boost-make-boost-field-container boost-make-boost-mobile-position-field boost-make-boost-position-field <?= $boost['mobile'] ? 'position-active' : '' ?>">
                <div class="boost-make-boost-field-data">
                    <div class="boost-make-boost-field boost-position mobile-position">
                            <input class="boost-position" type="text" id="boost[mobile_position]" name="boost[mobile_position]" value="<?= $boost['mobile_position']?>">
                            <div class="<?= !$boost['mobile'] ? 'disabled' : '' ?> position-place mobile-position-place position-top-place mobile-position-top-place position-middle-place position-top-middle mobile-position-top-middle <?= $boost['mobile_position'] == 'top' ? 'active' : ''?>" data-position="top"></div>
                            <div class="<?= !$boost['mobile'] ? 'disabled' : '' ?> position-place mobile-position-place position-bottom-place mobile-position-bottom-place position-middle-place position-bottom-middle mobile-position-bottom-middle <?= $boost['mobile_position'] == 'bottom' ? 'active' : ''?>" data-position="bottom"></div>

                            <div class="boost-checkbox-container boost-display-checkbox">
                                <input type="checkbox" id="boost[mobile]" name="boost[mobile]"
                                       value="1" <?php checked((isset($boost['mobile']) ? $boost['mobile'] : 0), 1, true); ?>
                                       class="boost-checkbox"
                                       data-checked-label="<?= __('DISPLAY ON MOBILE', 'boost') ?>"
                                       data-unchecked-label="<?= __('HIDE ON MOBILE', 'boost') ?>">
                                <label for="boost[mobile]" class="mobile-position-placeholder"></label>
                            </div>
                    </div>
                </div>
                <div class="boost-make-boost-field-description"><?= $boost['mobile'] ? __('DISPLAY ON MOBILE', 'boost') : __('HIDE ON MOBILE', 'boost') ?></div>
            </div>
        </div>
        <?php
    }

    public function replace_tag_to_html_tag($message){
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
        $replace_for = array(
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>name<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>time<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>town<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>state<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>country<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>product<span class="boost-message-tag-hidden-char">_</span>name<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>product<span class="boost-message-tag-hidden-char">_</span>with<span class="boost-message-tag-hidden-char">_</span>link<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>',
            '<span contenteditable="false" class="boost-message-tag"><span class="boost-message-tag-hidden-char">[</span>stock<span class="boost-message-tag-hidden-char">]</span><span class="boost-message-tag-delete"></span></span>'
        );

        $message = str_replace($replace_what, $replace_for, $message);
        return $message;
    }

    public function tag_cloud_html($boost_type, $boost_subtype){
        $tags = array();
        switch($boost_type){
            case 'leads':
	            $tags = array(
		            'name',
		            'town',
		            'state',
		            'country',
		            'time'
	            );
                break;
            case 'woocommerce':
	        case 'easydigitaldownloads':
                switch($boost_subtype) {
	                case 'transaction':
		                $tags = array(
			                'name',
			                'town',
			                'state',
			                'country',
			                'time',
			                'product name',
			                'product with link'
		                );
		                break;
	                case 'specific_transaction':
		                $tags = array(
			                'name',
			                'town',
			                'state',
			                'country',
			                'time',
			                'product name',
			                'product with link'
		                );
		                break;
	                case 'stock_messages':
		                $tags = array(
			                'product name',
			                'product with link',
			                'stock'
		                );
		                break;
	                case 'add_to_cart':
		                $tags = array(
			                'town',
			                'state',
			                'country',
			                'time',
			                'product name',
			                'product with link'
		                );
		                break;
	                default:
		                break;
                }
                break;
            default:
                break;
        }
    ?>
        <div class="boost-make-boost-tag-cloud">
            <?php
            foreach ($tags as $tag){
            ?>
                <span contenteditable="false" class="boost-message-tag draggable">
                    <?= $tag ?>
                </span>
            <?php
            }
             ?>
        </div>
    <?php
    }

    public function display_tabs_list($boost) {
    ?>
        <div class="boost-page-tab-list-container boost-make-boost-configure-display-tab-list-container">
            <div class="boost-page-tab-list boost-make-boost-configure-display-tab-list">
            <?php
            switch ($boost['type']) {
                case 'leads':
            ?>
                    <div>
                        <input type="radio" id="display_capture_url" name="boost[display_type]" value="capture_url" <?php checked( $boost['display_type'], 'capture_url' , true ); ?>>
                        <label for="display_capture_url"><?= __('On the Captured URL', 'boost') ?></label>
                    </div>
                    <div>
                        <input type="radio" id="display_all_pages" name="boost[display_type]" value="all_pages" <?php checked( $boost['display_type'], 'all_pages' , true ); ?>>
                        <label for="display_all_pages"><?= __('Show on All Pages', 'boost') ?></label>
                    </div>
                    <div>
                        <input type="radio" id="display_custom" name="boost[display_type]" value="custom" <?php checked( $boost['display_type'], 'custom' , true ); ?>>
                        <label for="display_custom"><?= __('Custom Settings', 'boost') ?></label>
                    </div>
            <?php
                    break;
                case 'woocommerce':
                case 'easydigitaldownloads':
                    switch ($boost['subtype']) {
                        case 'transaction':
                        case 'specific_transaction':
                            ?>
                            <div>
                                <input type="radio" id="display_fast" name="boost[display_type]" value="fast" <?php checked( $boost['display_type'], 'fast' , true ); ?>>
                                <label for="display_fast"><?= __('Fast Settings', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="display_all_pages" name="boost[display_type]" value="all_pages" <?php checked( $boost['display_type'], 'all_pages' , true ); ?>>
                                <label for="display_all_pages"><?= __('Show on All Pages', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="display_custom" name="boost[display_type]" value="custom" <?php checked( $boost['display_type'], 'custom' , true ); ?>>
                                <label for="display_custom"><?= __('Custom Settings', 'boost') ?></label>
                            </div>
                            <?php
                            break;
                        case 'stock_messages':
                            ?>
                            <div>
                                <input type="radio" id="display_all_products" name="boost[display_type]" value="all_products" <?php checked( $boost['display_type'], 'all_products' , true ); ?>>
                                <label for="display_all_products"><?= __('On All Products', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="display_specific_products" name="boost[display_type]" value="specific_products" <?php checked( $boost['display_type'], 'specific_products' , true ); ?>>
                                <label for="display_specific_products"><?= __('On Specific Products', 'boost') ?></label>
                            </div>
                            <?php
                            break;
                        case 'add_to_cart':
                            ?>
                            <div>
                                <input type="radio" id="display_all_products" name="boost[display_type]" value="all_products" <?php checked( $boost['display_type'], 'all_products' , true ); ?>>
                                <label for="display_all_products"><?= __('On All Products', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="display_all_pages" name="boost[display_type]" value="all_pages" <?php checked( $boost['display_type'], 'all_pages' , true ); ?>>
                                <label for="display_all_pages"><?= __('Show on All Pages', 'boost') ?></label>
                            </div>
                            <div>
                                <input type="radio" id="display_specific_products" name="boost[display_type]" value="specific_products" <?php checked( $boost['display_type'], 'specific_products' , true ); ?>>
                                <label for="display_specific_products"><?= __('On Specific Products', 'boost') ?></label>
                            </div>
                            <?php
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
            ?>
            </div>
        </div>
    <?php
    }

    public function display_tabs($boost) {
    ?>
    <div class="boost-tabs-container">
        <?php
        switch($boost['type']) {
            case 'leads':
                ?>
                <div class="boost-tab boost-tab-capture_url <?= 'capture_url' == $boost['display_type'] ? 'boost-active' : '' ?>">
                </div>
                <div class="boost-tab boost-tab-all_pages <?= 'all_pages' == $boost['display_type'] ? 'boost-active' : '' ?>">
                </div>
                <?php $this->display_custom_tab($boost); ?>
                <?php
                break;
            case 'woocommerce':
            case 'easydigitaldownloads':
                switch($boost['subtype']) {
                    case 'transaction':
                    case 'specific_transaction':
                        ?>
                        <?php $this->display_fast_tab($boost); ?>
                        <div class="boost-tab boost-tab-all_pages <?= 'all_pages' == $boost['display_type'] ? 'boost-active' : '' ?>">
                        </div>
                        <?php $this->display_custom_tab($boost); ?>
                        <?php
                        break;
                    case 'stock_messages':
                        ?>
                        <div class="boost-tab boost-tab-all_products <?= 'all_products' == $boost['display_type'] ? 'boost-active' : '' ?>">
                        </div>
	                    <?php
	                    $this->display_specific_products_tab($boost);
                        break;
                    case 'add_to_cart':
                        ?>
                        <div class="boost-tab boost-tab-all_products <?= 'all_products' == $boost['display_type'] ? 'boost-active' : '' ?>">
                        </div>
                        <div class="boost-tab boost-tab-all_pages <?= 'all_pages' == $boost['display_type'] ? 'boost-active' : '' ?>">
                        </div>
                        <?php
                        $this->display_specific_products_tab($boost);
                        break;
                    default:
                        break;
                }
                break;
            default:
                break;
        }
        ?>
    </div>
    <?php
    }

    public function display_custom_tab($boost){
    ?>
        <div class="boost-tab boost-tab-custom <?= 'custom' == $boost['display_type'] ? 'boost-active' : '' ?>">

            <div class="boost-make-boost-field boost-make-show-home-page switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[dc_on_home_page]" name="boost[dc_on_home_page]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['dc_on_home_page'], '1' , true ); ?>>
                   <label for="boost[dc_on_home_page]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show on the Home Page', 'boost')?>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-urls-block switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[dc_on_urls]" name="boost[dc_on_urls]" value="1" class="boost-show-hide-switcher" data-relative-box-id="display-on-urls-block" <?php checked( $boost['dc_on_urls'], '1' , true ); ?>>
                   <label for="boost[dc_on_urls]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show When URL Contains/Equals', 'boost')?>
                </div>
                <div id="display-on-urls-block" class="boost-make-prof-show-relative-block boost-make-show-urls-block-urls <?= '1' == $boost['dc_on_urls'] ? 'boost-active' : '' ?>">
                    <div class="boost-make-boost-show-urls boost-make-boost-urls">
                    <?php
                    if (!empty($boost['dc_urls']) && is_array($boost['dc_urls'])) {
                        foreach ($boost['dc_urls'] as $dc_url) {
                        ?>
                        <div class="boost-make-boost-show-url boost-make-boost-url">
                            <select name="boost[dc_urls][url_type][]" class="select2-standard-simple boost-input-style">
                                <option value="contains" <?php selected($dc_url['url_type'], 'contains', true); ?>><?=__('Contains', 'boost'); ?></option>
                                <option value="equals" <?php selected($dc_url['url_type'], 'equals', true); ?>><?=__('Equals', 'boost'); ?></option>
                            </select>
                            <input name="boost[dc_urls][url][]" type="text" value="<?=htmlspecialchars($dc_url['url'])?>" class="boost-input-style" placeholder="<?=__('Type here…', 'boost'); ?>">
                            <span class="boost-url-delete"></span>
                        </div>
                        <?php
                        }
                    }
                    else {
                    ?>
                        <div class="boost-make-boost-show-url boost-make-boost-url">
                            <select name="boost[dc_urls][url_type][]" class="select2-standard-simple boost-input-style"></select>
                            <input name="boost[dc_urls][url][]" type="text" class="boost-input-style" placeholder="<?=__('Type here…', 'boost'); ?>">
                            <span class="boost-url-delete"></span>
                        </div>
                    <?php
                    }
                    ?>
                    </div>
                    <div class="boost-make-boost-show-add-url"><?= '+ ' . __('Add another URL', 'boost'); ?></div>
                    <div class="boost-make-boost-field-description boost-make-boost-show-urls-description">
                        <?= __('Boost will display only when an URL contains or equals values above.', 'boost'); ?>
                    </div>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-specific-pages-block switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[dc_on_specific_pages]" name="boost[dc_on_specific_pages]" value="1" class="boost-show-hide-switcher" data-relative-box-id="display-on-specific-pages-block" <?php checked( $boost['dc_on_specific_pages'], '1' , true ); ?>>
                   <label for="boost[dc_on_specific_pages]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show on Specific Pages', 'boost')?>
                </div>
                <div id="display-on-specific-pages-block" class="boost-make-prof-show-relative-block boost-make-show-specific-pages-block-items <?= '1' == $boost['dc_on_specific_pages'] ? 'boost-active' : '' ?>">
                    <select multiple="multiple" id="boost[dc_specific_pages]" name="boost[dc_specific_pages][]"
                            class="boost-search-items boost-input-style boost-search-specific-pages"
                            data-placeholder="<?= __('Search for a specific page...', 'boost')?>" tabindex="-1" aria-hidden="true">
                        <?php
                                if(!empty($boost['dc_specific_pages']) && is_array($boost['dc_specific_pages'])) {
                                    foreach($boost['dc_specific_pages'] as $dc_specific_page) {
                                        ?>
                                        <option value="<?=$dc_specific_page['id']?>" selected="selected"><?=$dc_specific_page['title']?></option>
                                        <?php
                                    }
                                }
                        ?>
                    </select>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-post-types-block switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[dc_on_post_types]" name="boost[dc_on_post_types]" value="1" class="boost-show-hide-switcher" data-relative-box-id="display-on-post-types-block" <?php checked( $boost['dc_on_post_types'], '1' , true ); ?>>
                   <label for="boost[dc_on_post_types]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show on Post Type', 'boost')?>
                </div>
                <div id="display-on-post-types-block" class="boost-make-prof-show-relative-block boost-make-show-post-types-block-items <?= '1' == $boost['dc_on_post_types'] ? 'boost-active' : '' ?>">
                    <select multiple="multiple" id="boost[dc_post_types]" name="boost[dc_post_types][]"
                            class="boost-search-items boost-input-style boost-search-post-types"
                            data-placeholder="<?= __('Search for a post type...', 'boost')?>" tabindex="-1" aria-hidden="true">
                        <?php
                                if(!empty($boost['dc_post_types']) && is_array($boost['dc_post_types'])) {
                                    foreach($boost['dc_post_types'] as $dc_post_type) {
                                        ?>
                                        <option value="<?=$dc_post_type['post_type_name']?>" selected="selected"><?=$dc_post_type['post_type_label']?></option>
                                        <?php
                                    }
                                }
                        ?>
                    </select>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-taxonomies-block switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[dc_on_taxonomies]" name="boost[dc_on_taxonomies]" value="1" class="boost-show-hide-switcher" data-relative-box-id="display-on-taxonomies-block" <?php checked( $boost['dc_on_taxonomies'], '1' , true ); ?>>
                   <label for="boost[dc_on_taxonomies]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show on Taxonomies', 'boost')?>
                </div>
                <div id="display-on-taxonomies-block" class="boost-make-prof-show-relative-block boost-make-show-taxonomies-block-items <?= '1' == $boost['dc_on_taxonomies'] ? 'boost-active' : '' ?>">
                    <select multiple="multiple" id="boost[dc_taxonomies]" name="boost[dc_taxonomies][]"
                            class="boost-search-items boost-input-style boost-search-taxonomies"
                            data-placeholder="<?= __('Search for a taxonomy...', 'boost')?>" tabindex="-1" aria-hidden="true">
                        <?php
                            if(!empty($boost['dc_taxonomies']) && is_array($boost['dc_taxonomies'])) {
                                foreach($boost['dc_taxonomies'] as $dc_taxonomy) {
                                    ?>
                                    <option value="<?=$dc_taxonomy['id']?>" selected="selected"><?=$dc_taxonomy['name']?></option>
                                    <?php
                                }
                            }
                        ?>
                    </select>
                </div>
            </div>

        </div>
    <?php
    }

    public function display_fast_tab($boost){
	    $trigger_types = $this->boost_model->get_trigger_types();
    ?>
        <div class="boost-tab boost-tab-fast <?= 'fast' == $boost['display_type'] ? 'boost-active' : '' ?>">

            <?php
            if ('specific_transaction' == $boost['subtype']){
            ?>

                <div class="boost-make-boost-field boost-make-show-all-purchased-products switch-field">
                    <div class="boost-inline-block boost-on-off-switcher-checkbox">
                        <input type="checkbox" id="boost[df_on_all_purchased_products]" name="boost[df_on_all_purchased_products]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_all_purchased_products'], '1' , true ); ?>>
                       <label for="boost[df_on_all_purchased_products]"></label>
                    </div>
                    <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                        <?= __('On Every Purchased Product', 'boost')?>
                    </div>
                </div>

                <div class="boost-make-boost-field boost-make-show-all-purchased-product-categories switch-field">
                    <div class="boost-inline-block boost-on-off-switcher-checkbox">
                        <input type="checkbox" id="boost[df_on_all_purchased_categories]" name="boost[df_on_all_purchased_categories]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_all_purchased_categories'], '1' , true ); ?>>
                       <label for="boost[df_on_all_purchased_categories]"></label>
                    </div>
                    <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                        <?= __('On Every Purchased Product Category', 'boost')?>
                    </div>
                </div>

            <?php
            }
            ?>

            <div class="boost-make-boost-field boost-make-show-all-products switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[df_on_all_products]" name="boost[df_on_all_products]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_all_products'], '1' , true ); ?>>
                   <label for="boost[df_on_all_products]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('On All Products', 'boost')?>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-all-<?=$boost['type']?>-categories switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[df_on_all_categories]" name="boost[df_on_all_categories]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_all_categories'], '1' , true ); ?>>
                   <label for="boost[df_on_all_categories]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= sprintf(__('On All %s Categories', 'boost'), $trigger_types[$boost['type']]['name'])?>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-cart-page switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[df_on_cart_page]" name="boost[df_on_cart_page]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_cart_page'], '1' , true ); ?>>
                   <label for="boost[df_on_cart_page]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('On the Cart Page', 'boost')?>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-checkout-page switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[df_on_checkout_page]" name="boost[df_on_checkout_page]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_checkout_page'], '1' , true ); ?>>
                   <label for="boost[df_on_checkout_page]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('On the Checkout Page', 'boost')?>
                </div>
            </div>

            <div class="boost-make-boost-field boost-make-show-home-page switch-field">
                <div class="boost-inline-block boost-on-off-switcher-checkbox">
                    <input type="checkbox" id="boost[df_on_home_page]" name="boost[df_on_home_page]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['df_on_home_page'], '1' , true ); ?>>
                   <label for="boost[df_on_home_page]"></label>
                </div>
                <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                    <?= __('Show on the Home Page', 'boost')?>
                </div>
            </div>

        </div>

    <?php
    }
    
    public function display_specific_products_tab($boost) {
    ?>
        <div class="boost-tab boost-tab-specific_products <?= 'specific_products' == $boost['display_type'] ? 'boost-active' : '' ?>">

            <div class="boost-make-prof-show-relative-block boost-make-show-specific-products-block-items">
                <select multiple="multiple" class="boost-search-items boost-input-style boost-search-<?='woocommerce' == $boost['type'] ? 'wc-products' : 'edd-downloads'; ?>" id=""
                        name="boost[display_products][]" data-placeholder="<?= __('Search for a WooCommerce product...', 'boost')?>" tabindex="-1" aria-hidden="true">
                    <?php
                    if (!empty($boost['display_products']) && is_array($boost['display_products']) && count($boost['display_products']) > 0) {
                        foreach ($boost['display_products'] as $product) {
                            ?>
                            <option value="<?= $product['product_id'] ?>"
                                    selected="selected"><?= $product['product_name'] ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
            </div>

        </div>
    <?php
    }

    public function display_exclude_options($boost){
        if (empty($boost['subtype']) || !empty($boost['subtype']) && !in_array($boost['subtype'], array('add_to_cart', 'stock_messages'))) {
        ?>
        
            <div class="boost-exclude-options-block-container">
    
                <div class="boost-make-boost-field switch-field">
                    <div class="boost-inline-block boost-on-off-switcher-checkbox">
                        <input type="checkbox" id="boost[exclude_options]" name="boost[exclude_options]" value="1" class="boost-show-hide-switcher" data-relative-box-id="exclude-options-block"
	                        <?php checked( $boost['de_on_home_page'] || $boost['de_on_urls'] || $boost['de_on_specific_pages'] || $boost['de_on_post_types'] || $boost['de_on_taxonomies'], '1' , true ); ?>>
                        <label for="boost[exclude_options]"></label>
                    </div>
                    <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                            <?= __('Exclude', 'boost')?>
                    </div>
                    <div id="exclude-options-block" class="boost-make-prof-show-relative-block <?=$boost['de_on_home_page'] || $boost['de_on_urls'] || $boost['de_on_specific_pages'] || $boost['de_on_post_types'] || $boost['de_on_taxonomies'] ? 'boost-active' : ''?>">
    
                        <div class="boost-make-boost-field boost-make-hide-home-page switch-field">
                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                <input type="checkbox" id="boost[de_on_home_page]" name="boost[de_on_home_page]" value="1" class="boost-show-hide-switcher" data-relative-box-id="" <?php checked( $boost['de_on_home_page'], '1' , true ); ?>>
                                <label for="boost[de_on_home_page]"></label>
                            </div>
                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                                <?= __('Hide on the Home Page', 'boost')?>
                            </div>
                        </div>
    
                        <div class="boost-make-boost-field boost-make-hide-urls-block switch-field">
                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                <input type="checkbox" id="boost[de_on_urls]" name="boost[de_on_urls]" value="1" class="boost-show-hide-switcher" data-relative-box-id="hide-on-urls-block" <?php checked( $boost['de_on_urls'], '1' , true ); ?>>
                                <label for="boost[de_on_urls]"></label>
                            </div>
                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                                <?= __('Hide When URL Contains/Equals', 'boost')?>
                            </div>
                            <div id="hide-on-urls-block" class="boost-make-prof-hide-relative-block boost-make-hide-urls-block-urls <?= '1' == $boost['de_on_urls'] ? 'boost-active' : '' ?>">
                                <div class="boost-make-boost-hide-urls boost-make-boost-urls">
                                    <?php
                                    if (!empty($boost['de_urls']) && is_array($boost['de_urls'])) {
                                        foreach ($boost['de_urls'] as $de_urls) {
                                            ?>
                                            <div class="boost-make-boost-hide-url boost-make-boost-url">
                                                <select name="boost[de_urls][url_type][]" class="select2-standard-simple boost-input-style">
                                                    <option value="contains" <?php selected($de_urls['url_type'], 'contains', true); ?>><?=__('Contains', 'boost'); ?></option>
                                                    <option value="equals" <?php selected($de_urls['url_type'], 'equals', true); ?>><?=__('Equals', 'boost'); ?></option>
                                                </select>
                                                <input name="boost[de_urls][url][]" type="text" value="<?=htmlspecialchars($de_urls['url'])?>" class="boost-input-style" placeholder="<?=__('Type here…', 'boost'); ?>">
                                                <span class="boost-url-delete"></span>
                                            </div>
                                            <?php
                                        }
                                    }
                                    else {
                                        ?>
                                        <div class="boost-make-boost-hide-url boost-make-boost-url">
                                            <select name="boost[de_urls][url_type][]" class="select2-standard-simple boost-input-style"></select>
                                            <input name="boost[de_urls][url][]" type="text" class="boost-input-style" placeholder="<?=__('Type here…', 'boost'); ?>">
                                            <span class="boost-url-delete"></span>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="boost-make-boost-hide-add-url"><?= '+ ' . __('Add another URL', 'boost'); ?></div>
                                <div class="boost-make-boost-field-description boost-make-boost-hide-urls-description">
                                    <?= __('Boost will hide only when an URL contains or equals values above.', 'boost'); ?>
                                </div>
                            </div>
                        </div>
    
                        <div class="boost-make-boost-field boost-make-hide-specific-pages-block switch-field">
                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                <input type="checkbox" id="boost[de_on_specific_pages]" name="boost[de_on_specific_pages]" value="1" class="boost-show-hide-switcher" data-relative-box-id="hide-on-specific-pages-block" <?php checked( $boost['de_on_specific_pages'], '1' , true ); ?>>
                                <label for="boost[de_on_specific_pages]"></label>
                            </div>
                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                                <?= __('Hide on Specific Pages', 'boost')?>
                            </div>
                            <div id="hide-on-specific-pages-block" class="boost-make-prof-hide-relative-block boost-make-hide-specific-pages-block-items <?= '1' == $boost['de_on_specific_pages'] ? 'boost-active' : '' ?>">
                                <select multiple="multiple" id="boost[de_specific_pages]" name="boost[de_specific_pages][]"
                                        class="boost-search-items boost-input-style boost-search-specific-pages"
                                        data-placeholder="<?= __('Search for a specific page...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                    <?php
                                    if(!empty($boost['de_specific_pages']) && is_array($boost['de_specific_pages'])) {
                                        foreach($boost['de_specific_pages'] as $de_specific_page) {
                                            ?>
                                            <option value="<?=$de_specific_page['id']?>" selected="selected"><?=$de_specific_page['title']?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
    
                        <div class="boost-make-boost-field boost-make-hide-post-types-block switch-field">
                            <div class="boost-inline-block boost-on-off-switcher-checkbox">
                                <input type="checkbox" id="boost[de_on_post_types]" name="boost[de_on_post_types]" value="1" class="boost-show-hide-switcher" data-relative-box-id="hide-on-post-types-block" <?php checked( $boost['de_on_post_types'], '1' , true ); ?>>
                                <label for="boost[de_on_post_types]"></label>
                            </div>
                            <div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
                                <?= __('Hide on Post Type', 'boost')?>
                            </div>
                            <div id="hide-on-post-types-block" class="boost-make-prof-hide-relative-block boost-make-hide-post-types-block-items <?= '1' == $boost['de_on_post_types'] ? 'boost-active' : '' ?>">
                                <select multiple="multiple" id="boost[de_post_types]" name="boost[de_post_types][]"
                                        class="boost-search-items boost-input-style boost-search-post-types"
                                        data-placeholder="<?= __('Search for a post type...', 'boost')?>" tabindex="-1" aria-hidden="true">
                                    <?php
                                    if(!empty($boost['de_post_types']) && is_array($boost['de_post_types'])) {
                                        foreach($boost['de_post_types'] as $de_post_type) {
                                            ?>
                                            <option value="<?=$de_post_type['post_type_name']?>" selected="selected"><?=$de_post_type['post_type_label']?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
    
                        <div class="boost-make-boost-field boost-make-hide-taxonomies-block switch-field">
							<div class="boost-inline-block boost-on-off-switcher-checkbox">
								<input type="checkbox" id="boost[de_on_taxonomies]" name="boost[de_on_taxonomies]" value="1" class="boost-show-hide-switcher" data-relative-box-id="hide-on-taxonomies-block" <?php checked( $boost['de_on_taxonomies'], '1' , true ); ?>>
								<label for="boost[de_on_taxonomies]"></label>
							</div>
							<div class="boost-make-boost-field-label boost-make-boost-field-checkbox-label">
								<?= __('Hide on Taxonomies', 'boost')?>
							</div>
							<div id="hide-on-taxonomies-block" class="boost-make-prof-hide-relative-block boost-make-hide-taxonomies-block-items <?= '1' == $boost['de_on_taxonomies'] ? 'boost-active' : '' ?>">
								<select multiple="multiple" id="boost[de_taxonomies]" name="boost[de_taxonomies][]"
										class="boost-search-items boost-input-style boost-search-taxonomies"
										data-placeholder="<?= __('Search for a taxonomy...', 'boost')?>" tabindex="-1" aria-hidden="true">
									<?php
									if(!empty($boost['de_taxonomies']) && is_array($boost['de_taxonomies'])) {
										foreach($boost['de_taxonomies'] as $de_taxonomy) {
											?>
											<option value="<?=$de_taxonomy['id']?>" selected="selected"><?=$de_taxonomy['name']?></option>
											<?php
										}
									}
									?>
								</select>
							</div>
                		</div>
    
                    </div>
    
                </div>
            </div>
        <?php
        }
    }
}