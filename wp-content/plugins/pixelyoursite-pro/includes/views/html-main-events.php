<?php

namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$new_event_url = buildAdminUrl( 'pixelyoursite', 'events', 'edit' );
$export_url = buildAdminUrl( 'pixelyoursite', 'events', 'export' )
?>

<input type="hidden" name="pys[bulk_event_action_nonce]" value="<?php echo wp_create_nonce( 'bulk_event_action' ); ?>">

<h2 class="section-title">User Defined Events</h2>

<div class="card card-static">
    <div class="card-header">
        General
    </div>
    <div class="card-body">
	    <?php PYS()->render_switcher_input( 'custom_events_enabled' ); ?>
        <h4 class="switcher-label">Enable Events</h4>
        <div class="mt-3">
            <input type="file" id="import_events_file" name="import_events_file" accept="application/json"/>
            <label for="import_events_file" class="btn btn-sm btn-primary btn-events-import">Import Events</label>
            <a href="<?=$export_url?>" target="_blank" class="btn  ml-3 btn-sm btn-primary btn-events-export">Export Events</a>
        </div>
    </div>
</div>
<div class="card card-static">
    <div class="card-header">
        Recommeded videos
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <p><a href="https://www.youtube.com/watch?v=kEp5BDg7dP0" target="_blank">How to fire EVENTS with PixelYourSite (22:28) - watch now</a></p>
                <p><a href="https://www.youtube.com/watch?v=PcXYYGOvahc" target="_blank">Track URL tags as event parameters (8:15) - watch now</a></p>
            </div>
        </div>
    </div>
</div>

<div class="card card-static">
    <div class="card-header">
        About Parameters
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col">
                <p>All the events you configure here will automatically get the following parameters for all the tags:
                    <i>page_title, post_type, post_id, landing_page, event_URL, user_role, plugin, event_time (pro), event_day (pro), event_month (pro), traffic_source (pro), UTMs (pro).</i></p>
                <p>Exception: Google Analytics Universal will not get all these parameters. Only <i>event_time (pro), event_day (pro), event_month (pro), traffic_source (pro)</i> are sent as custom dimensions.</p>
                <p>You can add other parameters when you configure the events.</p>
            </div>
        </div>
    </div>
</div>

<div class="card card-static">
    <div class="card-header">
        Events List
    </div>
    <div class="card-body">
        <div class="row mb-3 bulk-events-block">
            <div class="col">
                <a href="<?php echo esc_url( $new_event_url ); ?>" class="btn btn-sm btn-primary mr-3">Add</a>
                <button class="btn btn-sm btn-light" name="pys[bulk_event_action]" value="enable" type="submit">Enable</button>
                <button class="btn btn-sm btn-light" name="pys[bulk_event_action]" value="disable" type="submit">Disable</button>
                <button class="btn btn-sm btn-light" name="pys[bulk_event_action]" value="clone" type="submit">Duplicate</button>
                <button class="btn btn-sm btn-danger ml-3 bulk-events-delete" name="pys[bulk_event_action]" value="delete" type="submit">Delete</button>

            </div>
        </div>
        <div class="row">
            <div class="col">
                <table class="table mb-0" id="table-custom-events">
                    <thead>
                    <tr>
                        <th style="width: 45px;">
                            <label class="custom-control custom-checkbox">
                                <input type="checkbox" id="pys_select_all_events" value="1" class="custom-control-input">
                                <span class="custom-control-indicator"></span>
                            </label>
                        </th>
                        <th>Name</th>
                        <th>Trigger</th>
                        <th>Networks</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php foreach ( CustomEventFactory::get() as $event ) : ?>

                        <?php
                        $errorMessage = "";
                        /** @var CustomEvent $event */

                        $event_edit_url = buildAdminUrl( 'pixelyoursite', 'events', 'edit', array(
                            'id' => $event->getPostId()
                        ) );

                        $event_enable_url = buildAdminUrl( 'pixelyoursite', 'events', 'enable', array(
                            'pys'      => array(
                                'event' => array(
                                    'post_id' => $event->getPostId(),
                                )
                            ),
                            '_wpnonce' => wp_create_nonce( 'pys_enable_event' ),
                        ) );

                        $event_disable_url = buildAdminUrl( 'pixelyoursite', 'events', 'disable', array(
                            'pys'      => array(
                                'event' => array(
                                    'post_id' => $event->getPostId(),
                                )
                            ),
                            '_wpnonce' => wp_create_nonce( 'pys_disable_event' ),
                        ) );

                        $event_remove_url = buildAdminUrl( 'pixelyoursite', 'events', 'remove', array(
                            'pys'      => array(
                                'event' => array(
                                    'post_id' => $event->getPostId(),
                                )
                            ),
                            '_wpnonce' => wp_create_nonce( 'pys_remove_event' ),
                        ) );

                        switch ( $event->getTriggerType() ) {

                            case 'post_type': {
                                $event_type = 'Post Type';
                                $selectedPostType = $event->getPostTypeValue();
                                $errorMessage = "Post type not found";
                                $types = get_post_types(null,"objects ");
                                foreach ($types as $type) {
                                    if($type->name == $selectedPostType) {
                                        $errorMessage = "";
                                        break;
                                    }
                                }

                            }break;

                            case 'url_click':
                                $event_type = 'Link Click';
                                break;

                            case 'css_click':
                                $event_type = 'Element Click';
                                break;

                            case 'css_mouseover':
                                $event_type = 'Element Mouseover';
                                break;

                            case 'scroll_pos':
                                $event_type = 'Scroll Position';
                                break;

                            default:
                                $event_type = 'Page Visit';
                        }

                        ?>

                        <tr data-post_id="<?php esc_attr_e( $event->getPostId() ); ?>"
                            class="<?php echo $event->isEnabled() ? '' : 'disabled'; ?>">
                            <td>
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="pys[selected_events][]"
                                           value="<?php esc_attr_e( $event->getPostId() ); ?>"
                                           class="custom-control-input pys-select-event">
                                    <span class="custom-control-indicator"></span>
                                </label>
                            </td>
                            <td>
                                <a href="<?php echo esc_url( $event_edit_url ); ?>"><?php esc_html_e( $event->getTitle() ); ?></a>
                                <span class="event-actions">
                                    <?php if ( $event->isEnabled() ) : ?>
                                        <a href="<?php echo esc_url( $event_disable_url ); ?>">Disable</a>
                                    <?php else : ?>
                                        <a href="<?php echo esc_url( $event_enable_url ); ?>">Enable</a>
                                    <?php endif; ?>
                                    &nbsp;|&nbsp;
                                    <a href="<?php echo esc_url( $event_remove_url ); ?>" class="
                                    text-danger remove-custom-event">Remove</a>
                                </span>
                            </td>
                            <td>
                                <?php esc_html_e( $event_type ); ?>
                                <?php if($errorMessage != "") : ?>
                                    <div class="event_error">
                                        <?=$errorMessage?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="networks">
                                <?php if ( Facebook()->enabled() && $event->isFacebookEnabled() ) : ?>
                                    <i class="fa fa-facebook-square"></i>
                                <?php else : ?>
                                    <i class="fa fa-facebook-square" style="opacity: .25;"></i>
                                <?php endif; ?>

                                <?php if ( GA()->enabled() && $event->isGoogleAnalyticsEnabled() ) : ?>
                                    <i class="fa fa-area-chart"></i>
                                <?php else : ?>
                                    <i class="fa fa-area-chart" style="opacity: .25;"></i>
                                <?php endif; ?>

	                            <?php if ( Ads()->enabled() && $event->isGoogleAdsEnabled() ) : ?>
                                    <i class="fa fa-google"></i>
	                            <?php else : ?>
                                    <i class="fa fa-google" style="opacity: .25;"></i>
	                            <?php endif; ?>

                                <?php if ( Pinterest()->enabled() && $event->isPinterestEnabled() ) : ?>
                                    <i class="fa fa-pinterest-square"></i>
                                <?php else : ?>
                                    <i class="fa fa-pinterest-square" style="opacity: .25;"></i>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<hr>
<div class="row justify-content-center">
    <div class="col-4">
        <button class="btn btn-block btn-sm btn-save">Save Settings</button>
    </div>
</div>
