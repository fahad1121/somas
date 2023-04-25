<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

?>

<div class="wrap" id="pys">

    <div class="pys_stat">


        <div class="row">
            <div class="col">
                <h2 class="section-title">Plugin Filters</h2>
            </div>
        </div>

        <div class="row">
            <div class="col">

                <div class="card">
                    <div class="card-header">
                        pys_disable_by_gdpr - Disable send all pixels events<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Disable send all pixels events, can by used for custom gdpr</p>
                        <p>Param: <i>bool $status</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_disable_by_gdpr',function ($status) {
    if(get_current_user_id() == 0 ) {
        return true;
    }
    return $status;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_disable_{pixel}_by_gdpr - Disable send pixel events<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>{pixel} - facebook, google_ads, ga, tiktok, pinterest, bing</p>
                        <p>Disable some pixel events, can by used for custom gdpr</p>
                        <p>Param: <i>bool $status</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_disable_facebook_by_gdpr',function ($status) {
    if(get_current_user_id() == 0 ) {
        return true;
    }
    return $status;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_gdpr_ajax_enabled - Update gdpr pixel status <?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">

                        <p>Load latest gdpr pixel status before load web pixel. Can by used when server use page caching</p>
                        <p>Param: <i>bool $status</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_gdpr_ajax_enabled',function ($status) {
    if(get_current_user_id() == 0 ) {
        return true;
    }
    return $status;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_event_data - Edit or add custom data to event<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Param: <i>array $data, string $slug ,any $context</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_event_data',function ($data,$slug,$context) {
    if(get_current_user_id() == 0 ) {
        $data['params']['total'] = 0;
    }
    return $data;
},10,3);</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_currencies_list - Add new currency in list, for custom events<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Param: <i>array $currencies</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_currencies_list',function ($currencies) {
    $currencies['PTH'] = 'Test';
    return $currencies;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_{edd or woo}_checkout_order_id - Use custom order id for purchase event<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>pys_edd_checkout_order_id - Edd plugin<br>pys_woo_checkout_order_id - WooCommerce plugin</p>
                        <p>Can by user for custom checkout page</p>
                        <p>Param: <i>int $order_id</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_woo_checkout_order_id',function ($order_id) {
    if(isset($_GET['custom_order_param_with_id'])) {
        return $_GET['custom_order_param_with_id'];
    }
    return $order_id;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_validate_pixel_event - Disable some events<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>You can disable some events depend on your logic</p>
                        <p>Param: <i>bool $isActive, \PixelYourSite\PYSEvent $event, \PixelYourSite\Settings $pixel</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_validate_pixel_event',function ($isActive,$event,$pixel) {
    if($pixel->getSlug() == "facebook"
       && $event->getId() == "woo_purchase"
       && get_current_user_id() == 0
    ) {
        return false;
    }
    return $isActive;
},10,3);</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_disable_server_event_filter - Disable Facebook server events<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Param: <i>bool $status</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_disable_server_event_filter',function ($status) {
    if(get_current_user_id() == 0 ) {
        return true;
    }
    return $status;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_before_send_fb_server_event - Add custom data to  Facebook server event<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Param: <i>FacebookAds\Object\ServerSide\Event $event,string $pixel_Id, string $eventId</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_before_send_fb_server_event',function ($event,$pixel_Id,$eventId) {
    if(get_current_user_id() == 0 ) {
        $event->setActionSource("not_registered");
    }
    return $event;
},10,3);</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_pixel_disabled - Disable Pixel<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p>Param: <i>bool $isActive,string $pixelSlug</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_pixel_disabled',function ($isActive,$pixelSlug) {
    if(get_current_user_id() == 0 && $pixelSlug == 'facebook') {
        return false
    }
    return $isActive;
},10,2);</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_{pixel}_ids - Add custom Pixel id<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">
                        <p> {pixel} - facebook, google_ads, ga, tiktok, pinterest, bing
                        </p>
                        <p>Param: <i>array $ids</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_facebook_ids',function ($ids) {
    if(get_current_user_id() == 0) {
        $ids[]='CUSTOM_PIXEL_ID';
    }
    return $ids;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_fb_advanced_matching - Add or edit facebook advanced matching params<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">

                        <p>Param: <i>array $params</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_fb_advanced_matching',function ($params) {
    if(get_current_user_id() == 0) {
        $params['fn'] = "not_registered";
        $params['ln'] = "not_registered";
    }
    return $params;
});</pre>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        pys_fb_server_user_data - Add or edit facebook server user data<?php cardCollapseBtn(); ?>
                    </div>
                    <div class="card-body">

                        <p>Param: <i>\PYS_PRO_GLOBAL\FacebookAds\Object\ServerSide\UserData $userData</i></p>
                        <label>Example:</label>
                        <pre class="copy_text">add_filter('pys_fb_server_user_data',function ($userData) {
    if(get_current_user_id() == 0) {
        $userData->setFirstName("undefined");
        $userData->setLastName("undefined");
        $userData->setEmail("undefined");
    }
    return $userData;
});</pre>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

