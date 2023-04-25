<?php
namespace PixelYourSite;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class StatOrdersTable extends DataBaseTable {

    function getName()
    {
        return $this->wpdb->prefix . "pys_stat_order";
    }

    function getCreateSql()
    {
        $collate = '';
        $tableName = $this->getName();
        if ( $this->wpdb->has_cap( 'collation' ) && $this->wpdb->get_charset_collate() != false) {
            $collate = $this->wpdb->get_charset_collate();
        }
        return "CREATE TABLE $tableName (
              id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
              order_id BIGINT UNSIGNED NOT NULL,
              
              traffic_source_id BIGINT UNSIGNED NULL,
              landing_id BIGINT UNSIGNED NULL,
              utm_source_id BIGINT UNSIGNED NULL,
              utm_medium_id BIGINT UNSIGNED NULL,
              utm_campaing_id BIGINT UNSIGNED NULL,
              utm_term_id BIGINT UNSIGNED NULL,
              utm_content_id BIGINT UNSIGNED NULL,
              
              last_traffic_source_id BIGINT UNSIGNED NULL,
              last_landing_id BIGINT UNSIGNED NULL,
              last_utm_source_id BIGINT UNSIGNED NULL,
              last_utm_medium_id BIGINT UNSIGNED NULL,
              last_utm_campaing_id BIGINT UNSIGNED NULL,
              last_utm_term_id BIGINT UNSIGNED NULL,
              last_utm_content_id BIGINT UNSIGNED NULL,
              
              gross_sale FLOAT UNSIGNED NOT NULL,
              net_sale FLOAT UNSIGNED NOT NULL,
              total_sale FLOAT UNSIGNED NOT NULL,
              type TINYINT NOT NULL,
              date timestamp default current_timestamp, 
              PRIMARY KEY  (id)
            ) $collate;";
    }

    /**
     * @param $filterColName
     * @param $type
     * @return int
     */
    function getFilterCount($filterColName,$type,$dateStart,$dateEnd) {
        $sql = $this->wpdb->prepare("SELECT $filterColName FROM {$this->getName()} 
                                            WHERE type = %d AND $filterColName IS NOT NULL AND date BETWEEN %s AND %s
                                            GROUP BY $filterColName",
            $type,$dateStart,$dateEnd);

        return count($this->wpdb->get_results($sql));
    }

    /**
     * @param $filterColName
     * @param $type
     * @param $dateStart
     * @param $dateEnd
     * @param $isFirst
     * @return object
     */

    function getFilterTotal($filterColName,$type,$dateStart,$dateEnd,$isFirst) {
        $prefix = "";
        if(!$isFirst) {
            $prefix = "last_";
        }
        $sql = $this->wpdb->prepare("SELECT 
count(id) as count,
ROUND(SUM(total_sale),2) as total_sale,
ROUND(SUM(gross_sale),2) as gross_sale,
ROUND(SUM(net_sale),2) as net_sale
FROM {$this->getName()}
WHERE type = %d AND $filterColName IS NOT NULL AND date BETWEEN %s AND %s
",$type,$dateStart,$dateEnd);

        /*
          sum(case when {$prefix}traffic_source_id is null then 0 else 1 end) as traffic_source_count,
sum(case when {$prefix}landing_id is null then 0 else 1 end) as landing_count,
sum(case when {$prefix}utm_source_id is null then 0 else 1 end) as utm_source_count,
sum(case when {$prefix}utm_medium_id is null then 0 else 1 end) as utm_medium_count,
sum(case when {$prefix}utm_campaing_id is null then 0 else 1 end) as utm_campaing_count,
sum(case when {$prefix}utm_term_id is null then 0 else 1 end) as utm_term_count,
sum(case when {$prefix}utm_content_id is null then 0 else 1 end) as utm_content_count,
         */

        return $this->wpdb->get_row($sql,ARRAY_A);
    }
    public function getCOGFilterTotal($filterColName,$type,$dateStart,$dateEnd,$isFirst) {
        $prefix = "";
        if(!$isFirst) {
            $prefix = "last_";
        }
        $sql = $this->wpdb->prepare("SELECT 
count(id) as count,
ROUND(SUM(total_sale),2) as total_sale,
                    ROUND(SUM(meta1.meta_value),2) AS cost,
                    ROUND(SUM(meta2.meta_value),2) AS profit
                    FROM {$this->getName()} AS orders 
                    LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
	                LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                    
WHERE type = %d AND $filterColName IS NOT NULL AND date BETWEEN %s AND %s
",$type,$dateStart,$dateEnd);

        return $this->wpdb->get_row($sql,ARRAY_A);
    }
    /**
     * @param $typeTable
     * @param $filterColName
     * @param $filterId
     * @param $type
     * @param $dateStart
     * @param $dateEnd
     * @return array|object|\stdClass|void|null
     */
    function getFilterSingleTotal($typeTable,$filterColName,$filterId,$type,$dateStart,$dateEnd) {
        $firstNam = "";
        switch ($typeTable) {
            case "dates": {
                $firstNam = "Dates: ";
                $select = "count(DISTINCT cast(`date` as date)) as count, count(DISTINCT order_id) as orders";}break;
            case "orders": {
                $firstNam = "Orders: ";
                $select = "count(id) as count";}break;
        }
        $data = [];
        $sql = $this->wpdb->prepare("SELECT 
                $select,
                ROUND(SUM(total_sale),2) as total_sale,
                ROUND(SUM(gross_sale),2) as gross_sale,
                ROUND(SUM(net_sale),2) as net_sale
                FROM {$this->getName()}
                WHERE type = %d AND $filterColName = $filterId AND date BETWEEN %s AND %s
                ",$type,$dateStart,$dateEnd);
        $row = $this->wpdb->get_row($sql);

        switch ($typeTable) {
            case "dates": {
                $data[] = ["name"=>"Dates: ","value"=>$row->count];
                $data[] = ["name"=>"Orders: ","value"=>$row->orders];
            }break;
            case "orders": {
                $data[] = ["name"=>"Orders: ","value"=>$row->count];
            }break;
        }
        $symbols = $type == 0 ? get_woocommerce_currency_symbol() : edd_currency_symbol();
        $data[] = ["name"=>"Gross Sale: ","value"=>$symbols.$row->gross_sale];
        $data[] = ["name"=>"Net Sale: ","value"=>$symbols.$row->net_sale];
        $data[] = ["name"=>"Total Sale: ","value"=>$symbols.$row->total_sale];
        return $data;
    }

    function getCOGFilterSingleTotal($typeTable,$filterColName,$filterId,$type,$dateStart,$dateEnd) {
        $firstNam = "";
        switch ($typeTable) {
            case "dates": {
                $firstNam = "Dates: ";
                $select = "count(DISTINCT cast(`date` as date)) as count, count(DISTINCT order_id) as orders";}break;
            case "orders": {
                $firstNam = "Orders: ";
                $select = "count(id) as count";}break;
        }
        $data = [];
        $sql = $this->wpdb->prepare("SELECT 
                $select,
                order_id,
                ROUND(SUM(total_sale),2) as total_sale,
                ROUND(SUM(meta1.meta_value),2) AS cost_sale,
                ROUND(SUM(meta2.meta_value),2) AS profit_sale
                FROM {$this->getName()} AS orders 
                LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
                LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                WHERE type = %d AND $filterColName = $filterId AND date BETWEEN %s AND %s
                ",$type,$dateStart,$dateEnd);

        $row = $this->wpdb->get_row($sql);

        switch ($typeTable) {
            case "dates": {
                $data[] = ["name"=>"Dates: ","value"=>$row->count];
                $data[] = ["name"=>"Orders: ","value"=>$row->orders];
            }break;
            case "orders": {
                $data[] = ["name"=>"Orders: ","value"=>$row->count];
            }break;
        }

        $symbols = $type == 0 ? get_woocommerce_currency_symbol() : edd_currency_symbol();
        $data[] = ["name"=>"Cost: ","value"=>$symbols.$row->cost_sale];
        $data[] = ["name"=>"Profit: ","value"=>$symbols.$row->profit_sale];
        $data[] = ["name"=>"Total Sale: ","value"=>$symbols.$row->total_sale];
        return $data;
    }

    function getOrderByCol($slag) {
        switch ($slag) {
            case "order": return "count";
            case "gross_sale": return "gross";
            case "total_sale": return "total";
            default: return "net";
        }
    }

    function getSOrtType($slag) {
        if($slag == "desc") {
            return "DESC";
        }
        return "ASC";
    }

    function getSumForFilter($filterTableName,$filterColName,$startDate,$endDate,$from, $max,$type,$orderBy,$sort) {
        $orderBy = $this->getOrderByCol($orderBy);
        $sort = $this->getSOrtType($sort);
        $data = ["ids" => [],"filters" => []];
        $sql = $this->wpdb->prepare("SELECT count(order_id) as count, t2.id as item_id, t2.item_value, ROUND(SUM(gross_sale),2) as gross, ROUND(SUM(net_sale),2) as net, ROUND(SUM(total_sale),2) as total 
                                                FROM {$this->getName()} 
                                                LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
                                                WHERE type = %d AND $filterColName IS NOT NULL  AND date BETWEEN %s AND %s
                                                GROUP BY $filterColName
                                                ORDER BY $orderBy $sort
                                                LIMIT %d, %d
                                                ",$type,$startDate,$endDate,$from,$max);
        PYS()->getLog()->debug("getSumForFilter $sql");
        $rows = $this->wpdb->get_results($sql);
        foreach ($rows as $row) {
            $data["ids"][] = $row->item_id;
            $data["filters"][] = ["id" => $row->item_id,"name" => $row->item_value,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"count" => $row->count];
        }
        return $data;
    }

    function getCOGSumForFilter($filterTableName,$filterColName,$startDate,$endDate,$from, $max,$type,$orderBy,$sort) {
        $orderBy = $this->getOrderByCol($orderBy);
        $sort = $this->getSOrtType($sort);
        $data = ["ids" => [],"filters" => []];
        $sql = $this->wpdb->prepare("SELECT count(order_id) as count, 
t2.id as item_id, 
t2.item_value, 
                        ROUND(SUM(total_sale),2) as total,
                        ROUND(SUM(meta1.meta_value),2) AS gross,
                        ROUND(SUM(meta2.meta_value),2) AS net
                        FROM {$this->getName()} AS orders 
                        LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
                        LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                        LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
                        WHERE type = %d AND $filterColName IS NOT NULL  AND date BETWEEN %s AND %s
                        GROUP BY $filterColName
                        ORDER BY $orderBy $sort
                        LIMIT %d, %d
                                                ",$type,$startDate,$endDate,$from,$max);
        PYS()->getLog()->debug("getSumForFilter $sql");
        $rows = $this->wpdb->get_results($sql);
        foreach ($rows as $row) {
            $data["ids"][] = $row->item_id;
            $data["filters"][] = ["id" => $row->item_id,"name" => $row->item_value,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"count" => $row->count];
        }
        return $data;
    }

    function getDataAll($filterTableName,$filterColName,$startDate,$endDate,$type) {
        $sql = $this->wpdb->prepare("SELECT count(order_id) as count, t2.id as item_id, t2.item_value, ROUND(SUM(gross_sale),2) as gross, ROUND(SUM(net_sale),2) as net, ROUND(SUM(total_sale),2) as total 
                                                FROM {$this->getName()} 
                                                LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
                                                WHERE type = %d AND $filterColName IS NOT NULL  AND date BETWEEN %s AND %s
                                                GROUP BY $filterColName
                                                ORDER BY total DESC
                                               
                                                ",$type,$startDate,$endDate);
        return $this->wpdb->get_results($sql);
    }

    function getCOGDataAll($filterTableName,$filterColName,$startDate,$endDate,$type) {
        $sql = $this->wpdb->prepare("SELECT count(order_id) as count, t2.id as item_id, t2.item_value, ROUND(SUM(total_sale),2) as total,
                        ROUND(SUM(meta1.meta_value),2) AS gross,
                        ROUND(SUM(meta2.meta_value),2) AS net
                                                FROM {$this->getName()} AS orders 
                        LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
                        LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                                                LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
                                                WHERE type = %d AND $filterColName IS NOT NULL  AND date BETWEEN %s AND %s
                                                GROUP BY $filterColName
                                                ORDER BY total DESC
                                               
                                                ",$type,$startDate,$endDate);
        return $this->wpdb->get_results($sql);
    }

    function getData($filterTableName,$filterColName,$ids,$startDate,$endDate,$type,$orderBy,$sort) {
        $data = [];
        $orderBy = $this->getOrderByCol($orderBy);
        $sort = $this->getSOrtType($sort);
        $in = '(' . implode(',', $ids) .')';
        //data: [{x:'2016-12-25', y:20}, {x:'2016-12-26', y:10},{x:'2016-12-27', y:15}]


        $sql = $this->wpdb->prepare("SELECT count(order_id) as count,t2.id as item_id, t2.item_value,CAST(date AS DATE) date ,ROUND(SUM(gross_sale),2) as gross, ROUND(SUM(net_sale),2) as net, ROUND(SUM(total_sale),2) as total 
FROM {$this->getName()} 
LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
WHERE type = %d AND $filterColName IN $in AND date BETWEEN %s AND %s
GROUP BY cast(`date` as date), $filterColName
ORDER BY $orderBy $sort
",$type,$startDate,$endDate);

        PYS()->getLog()->debug("getData $sql");
        /**
         * @var {item_value:String,date:String,gross: float,net:float}[]$rows
         */
        $rows = $this->wpdb->get_results($sql);

        foreach ($rows as $row) {
            if(!key_exists($row->item_value,$data)) {
                $data[$row->item_value] = [
                    "item" => ["id" => $row->item_id,"name" => $row->item_value],
                    "data" => []
                ];
            }
            $data[$row->item_value]["data"][] = ["x"=>$row->date,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"count"=>$row->count];
        }

        return $data;
    }

    function getCOGData($filterTableName,$filterColName,$ids,$startDate,$endDate,$type,$orderBy,$sort) {
        $data = [];
        $orderBy = $this->getOrderByCol($orderBy);
        $sort = $this->getSOrtType($sort);
        $in = '(' . implode(',', $ids) .')';
        //data: [{x:'2016-12-25', y:20}, {x:'2016-12-26', y:10},{x:'2016-12-27', y:15}]


        $sql = $this->wpdb->prepare("SELECT order_id, count(order_id) as count,t2.id as item_id, t2.item_value,CAST(date AS DATE) date , ROUND(SUM(total_sale),2) as total, ROUND(SUM(meta1.meta_value),2) AS gross,
                        ROUND(SUM(meta2.meta_value),2) AS net
                        FROM {$this->getName()} AS orders 
                        LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
                        LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                        LEFT JOIN  $filterTableName as t2 ON  $filterColName = t2.id 
                        WHERE type = %d AND $filterColName IN $in AND date BETWEEN %s AND %s
                        GROUP BY cast(`date` as date), $filterColName
                        ORDER BY $orderBy $sort
                        ",$type,$startDate,$endDate);

        /**
         * @var {item_value:String,date:String,gross: float,net:float}[]$rows
         */
        $rows = $this->wpdb->get_results($sql);
        PYS()->getLog()->debug("getCOGData ".$sql);
        foreach ($rows as $row) {
            if(!key_exists($row->item_value,$data)) {
                $data[$row->item_value] = [
                    "item" => ["id" => $row->item_id,"name" => $row->item_value],
                    "data" => []
                ];
            }
            $data[$row->item_value]["data"][] = ["x"=>$row->date, "gross" => $row->gross,"net" => $row->net, "total" => $row->total, "count"=>$row->count];
        }

        return $data;
    }

    function isExistOrder($orderId,$type) {
        $row = $this->wpdb->get_row($this->wpdb->prepare("SELECT id FROM {$this->getName()} WHERE order_id = %d AND type = %d",$orderId,$type));
        return $row != null;
    }

    /**
     * @param $orderId
     * @return bool|int
     */
    function deleteOrder($orderId,$type) {
      return  $this->wpdb->delete($this->getName(),['order_id' => $orderId,'type' => $type],["%d","%d"]);
    }



    function getOrdersForSingle($filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("SELECT  order_id, CAST(date AS DATE) date ,ROUND(gross_sale,2) as gross, ROUND(net_sale,2) as net, ROUND(total_sale,2) as total
                    FROM {$this->getName()} 
                    WHERE type = %d AND $filterColName = %d  AND date BETWEEN %s AND %s
                    ORDER BY total DESC
                    ",$type,$filterId,$startDate,$endDate);
        $rows = $this->wpdb->get_results($sql);

        foreach ($rows as $row) {
            $data[] = ["x"=>$row->date,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"order_id" => $row->order_id,"count" => 1];
        }
        return $data;
    }
    function getCOGOrdersForSingle($filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("SELECT  order_id, CAST(date AS DATE) date , ROUND(total_sale,2) as total, ROUND(meta1.meta_value,2) AS gross,
                        ROUND(meta2.meta_value,2) AS net
                    FROM {$this->getName()} AS orders 
                        LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
                        LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                    WHERE type = %d AND $filterColName = %d  AND date BETWEEN %s AND %s
                    ORDER BY total DESC
                    ",$type,$filterId,$startDate,$endDate);
        $rows = $this->wpdb->get_results($sql);

        foreach ($rows as $row) {
            $data[] = ["x"=>$row->date,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"order_id" => $row->order_id,"count" => 1];
        }
        return $data;
    }


    function getDatesForSingle($filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("SELECT count(order_id) as count ,order_id, CAST(date AS DATE) date ,ROUND(SUM(gross_sale),2) as gross, ROUND(SUM(net_sale),2) as net , ROUND(SUM(total_sale),2) as total
                    FROM {$this->getName()} 
                    WHERE type = %d AND $filterColName = %d  AND date BETWEEN %s AND %s
                    GROUP BY cast(`date` as date)
                    ORDER BY total DESC
                    ",$type,$filterId,$startDate,$endDate);
        $rows = $this->wpdb->get_results($sql);
        PYS()->getLog()->debug("getDatesForSingle $sql");
        foreach ($rows as $row) {
            $data[] = ["x"=>$row->date,"gross" => $row->gross,"net" => $row->net,"total" => $row->total,"order_id" => $row->order_id,"count" => $row->count];
        }
        return $data;
    }

    function getCOGDatesForSingle($filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("SELECT 
                   count(order_id) as count ,
                   order_id, 
                   CAST(date AS DATE) date ,
                   ROUND(SUM(total_sale),2) as total,
                    ROUND(SUM(meta1.meta_value),2) AS cost,
                    ROUND(SUM(meta2.meta_value),2) AS profit
                    FROM {$this->getName()} AS orders 
                    LEFT JOIN wp_postmeta AS meta1 ON meta1.post_id = orders.order_id AND meta1.meta_key = '_pixel_cost_of_goods_order_cost'
	                LEFT JOIN wp_postmeta AS meta2 ON meta2.post_id = orders.order_id AND meta2.meta_key = '_pixel_cost_of_goods_order_profit'
                    WHERE type = %d AND $filterColName = %d  AND date BETWEEN %s AND %s
                    GROUP BY cast(`date` as date)
                    ORDER BY total DESC
                    ",$type,$filterId,$startDate,$endDate);
        $rows = $this->wpdb->get_results($sql);
        PYS()->getLog()->debug("getDatesForSingle $sql");
        foreach ($rows as $row) {
            $data[] = ["x"=>$row->date,"gross" => $row->cost,"net" => $row->profit,"total" => $row->total,"order_id" => $row->order_id,"count" => $row->count];
        }
        return $data;
    }

    function getProductsOrders($filterColName,$filterId,$startDate,$endDate,$type) {
        $sql = $this->wpdb->prepare("
                    SELECT order_id 
                    FROM {$this->getName()} 
                    WHERE $filterColName = %d AND type = %d AND date BETWEEN %s AND %s
                    GROUP BY order_id
              "
            ,$filterId,$type,$startDate,$endDate);

        $col =  $this->wpdb->get_col($sql);

        PYS()->getLog()->debug("getProductsOrders $sql ".print_r($col,true));
        return $col;
//        $data[] = ["name"=>"Products: ","value"=>$row->ids];
//        $data[] = ["name"=>"Orders: ","value"=>$row->orders];
//        $data[] = ["name"=>"Quantity: ","value"=>$row->qty];
//        $data[] = ["name"=>"Total Gross: ","value"=>$row->gross.get_woocommerce_currency_symbol()];

    }
    function getProductsForSingle($productTable,$filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("
 SELECT product.product_id, product.product_name, count(DISTINCT product.order_id) as count_order,  ROUND(SUM(product.gross_sale),2) as gross,SUM(product.qty) as qty 
                    FROM {$this->getName()} as orders
                    LEFT JOIN $productTable as product ON product.order_id = orders.order_id AND product.type = orders.type
                    WHERE $filterColName = %d AND orders.type = %d AND orders.date BETWEEN %s AND %s
                    GROUP BY product.product_id
                    ORDER BY gross DESC
                
                    "
        ,$filterId,$type,$startDate,$endDate);
        //error_log("getProductsForSingle ".$sql);
        $rows = $this->wpdb->get_results($sql);
        foreach ($rows as $row) {
            $data[] = ["id"=>$row->product_id,"name"=>$row->product_name,"qty"=>$row->qty,"orders"=>$row->count_order,"gross"=>$row->gross];
        }
        return $data;

    }

    function getCOGProductsForSingle($productTable,$filterColName,$filterId,$startDate,$endDate,$type) {
        $data = [];
        $sql = $this->wpdb->prepare("
 SELECT product.product_id, product.product_name, count(DISTINCT product.order_id) as count_order,  IF(meta_goods_cost_type.meta_value = 'fix',ROUND(SUM((product.gross_sale - meta_goods_cost_val.meta_value)*qty),2),ROUND(SUM((product.gross_sale -(product.gross_sale/100*meta_goods_cost_val.meta_value)*qty)),2)) as profit,SUM(product.qty) as qty 
                    FROM {$this->getName()} AS orders 
                    LEFT JOIN $productTable as product ON product.order_id = orders.order_id AND product.type = orders.type
                    LEFT JOIN wp_postmeta AS meta_goods_cost_val ON meta_goods_cost_val.post_id = product.product_id AND meta_goods_cost_val.meta_key = '_pixel_cost_of_goods_cost_val' 
                    LEFT JOIN wp_postmeta AS meta_goods_cost_type ON meta_goods_cost_type.post_id = product.product_id AND meta_goods_cost_type.meta_key = '_pixel_cost_of_goods_cost_type'
                    WHERE $filterColName = %d AND orders.type = %d AND orders.date BETWEEN %s AND %s
                    GROUP BY product.product_id
                    ORDER BY profit DESC
                
                    "
            ,$filterId,$type,$startDate,$endDate);
        error_log("getProductsForSingle ".$sql);
        $rows = $this->wpdb->get_results($sql);
        foreach ($rows as $row) {
            $data[] = ["id"=>$row->product_id,"name"=>$row->product_name,"qty"=>$row->qty,"orders"=>$row->count_order,"gross"=>$row->profit];
        }
        return $data;

    }









    function updateOrder($orderId,$gross_sale,$net_sale,$total_sale,$type) {

        if($gross_sale < 0) $gross_sale = 0;
        if($net_sale < 0) $net_sale = 0;
        if($total_sale < 0) $total_sale = 0;

        return $this->wpdb->update($this->getName(),
            ["gross_sale" => $gross_sale, "net_sale" => $net_sale,"total_sale" => $total_sale],
            ["order_id" => $orderId,'type' => $type],
            ['%f','%f'],
            ['%d','%d']
        );
    }


    function insertOrder($params) {
        $status =  $this->wpdb->insert($this->getName(),$params,
            ['%d',
                '%f','%f','%f',
                '%d','%s',
                '%d','%d','%d','%d', '%d','%d','%d',
                '%d','%d','%d','%d', '%d','%d','%d']
        );
        if(!$status) {
            error_log("pys insertOrder error: ".$this->wpdb->last_error);
        }
        return $status;
    }


    /**
     * @param int $typeId
     */
    function clear($typeId) {
        $this->wpdb->delete($this->getName(),['type'=>$typeId],['%d']);
    }
}