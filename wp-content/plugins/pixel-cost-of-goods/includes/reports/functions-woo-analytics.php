<?php

/**
 * Add new report to woo Analytics menu
 * @see https://github.com/woocommerce/woocommerce-admin/blob/master/docs/examples/extensions/add-report/woocommerce-admin-add-report-example.php
 * @param $report_pages
 * @return array
 */

function add_report_add_report_menu_item( $report_pages ) {

    $report_pages []= array(
        'id'     => 'cog-analytics-report',
        'title'  => __( 'Cost of Goods', 'woocommerce-admin' ),
        'parent' => 'woocommerce-analytics',
        'path'   => '/analytics/cog_orders',
    );
   // array_splice( $report_pages, 2, 0, array($report) );
    return $report_pages;
}
//add_filter( 'woocommerce_analytics_report_menu_items', 'add_report_add_report_menu_item' );

function add_product_extended_attributes_schema( $properties ) {
    $properties['extended_info']['customer']['email'] = array(
        'type'        => 'string',
        'readonly'    => true,
        'context'     => array( 'view', 'edit' ),
        'description' => 'Customer Email.',
    );
    return $properties;
}
add_filter( 'woocommerce_rest_report_products_schema', 'add_product_extended_attributes_schema' );

/**
 * Modify select query
 * @see https://github.com/woocommerce/woocommerce-admin/tree/master/docs/examples/extensions/sql-modification
 * */

add_filter("woocommerce_rest_reports_column_types","cog_rest_reports_column_types",20,2);
function cog_rest_reports_column_types($column_types, $array) {
    $column_types['cog_cost'] =  'floatval';
    $column_types['cog_profit'] =  'floatval';
    $column_types['shipping'] =  'floatval';
    $column_types['gross_sales'] =  'floatval';
    return $column_types;
}


function add_join_subquery( $clauses ) {
    global $wpdb;

    $clauses[] = "LEFT  JOIN {$wpdb->postmeta} cog_cost_meta ON {$wpdb->prefix}wc_order_stats.order_id = cog_cost_meta.post_id";
    $clauses[] = "LEFT  JOIN {$wpdb->postmeta} cog_profit_meta ON {$wpdb->prefix}wc_order_stats.order_id = cog_profit_meta.post_id";

    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_join_orders_subquery', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_total', 'add_join_subquery' );
add_filter( 'woocommerce_analytics_clauses_join_orders_stats_interval', 'add_join_subquery' );


function add_where_subquery( $clauses ) {

    $clauses[] = "AND cog_cost_meta.meta_key = '_pixel_cost_of_goods_order_cost' ";
    $clauses[] = "AND cog_profit_meta.meta_key = '_pixel_cost_of_goods_order_profit' ";

    return $clauses;
}
add_filter( 'woocommerce_analytics_clauses_where_orders_subquery', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_total', 'add_where_subquery' );
add_filter( 'woocommerce_analytics_clauses_where_orders_stats_interval', 'add_where_subquery' );


function add_select_subquery( $clauses ) {
    $clauses[] = ', IFNULL(cog_cost_meta.meta_value,0) AS cog_cost';
    $clauses[] = ', IFNULL(cog_profit_meta.meta_value,0) AS cog_profit';
    return $clauses;
}

add_filter( 'woocommerce_analytics_clauses_select_orders_subquery', 'add_select_subquery' );



function add_select_subquery_total($clauses) {
    $clauses[] = ', SUM(cog_cost_meta.meta_value) AS cog_cost';
    $clauses[] = ', SUM(cog_profit_meta.meta_value) AS cog_profit';
    return $clauses;
}
add_filter( 'woocommerce_analytics_clauses_select_orders_stats_interval', 'add_select_subquery_total' );
add_filter( 'woocommerce_analytics_clauses_select_orders_stats_total', 'add_select_subquery_total' );
add_filter( 'woocommerce_analytics_report_should_use_cache', '__return_false' );
