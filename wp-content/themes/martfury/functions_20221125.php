<?php
/**
 * DrFuri Core functions and definitions
 *
 * @package Martfury
 */


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @since  1.0
 *
 * @return void
 */
//require_once "wc_rest_api/Client.php";

function martfury_setup() {
	// Sets the content width in pixels, based on the theme's design and stylesheet.
	$GLOBALS['content_width'] = apply_filters( 'martfury_content_width', 840 );

	// Make theme available for translation.
	load_theme_textdomain( 'martfury', get_template_directory() . '/lang' );

	// Theme supports
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'post-formats', array( 'audio', 'gallery', 'video', 'quote', 'link' ) );
	add_theme_support(
		'html5', array(
			'comment-list',
			'search-form',
			'comment-form',
			'gallery',
		)
	);

	if ( martfury_fonts_url() ) {
		add_editor_style( array( 'css/editor-style.css', martfury_fonts_url() ) );
	} else {
		add_editor_style( 'css/editor-style.css' );
	}

	// Load regular editor styles into the new block-based editor.
	add_theme_support( 'editor-styles' );

	// Load default block styles.
	add_theme_support( 'wp-block-styles' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	add_theme_support( 'align-wide' );

	add_theme_support( 'align-full' );

	// Register theme nav menu
	$nav_menu = array(
		'primary'         => esc_html__( 'Primary Menu', 'martfury' ),
		'shop_department' => esc_html__( 'Shop By Department Menu', 'martfury' ),
		'mobile'          => esc_html__( 'Mobile Header Menu', 'martfury' ),
		'category_mobile' => esc_html__( 'Mobile Category Menu', 'martfury' ),
		'user_logged'     => esc_html__( 'User Logged Menu', 'martfury' ),
	);
	if ( martfury_has_vendor() ) {
		$nav_menu['vendor_logged'] = esc_html__( 'Vendor Logged Menu', 'martfury' );
	}
	register_nav_menus( $nav_menu );

	add_image_size( 'martfury-blog-grid', 380, 300, true );
	add_image_size( 'martfury-blog-list', 790, 510, true );
	add_image_size( 'martfury-blog-masonry', 370, 588, false );

	global $martfury_woocommerce;
	$martfury_woocommerce = new Martfury_WooCommerce;

	global $martfury_mobile;
	$martfury_mobile = new Martfury_Mobile;

}

add_action( 'after_setup_theme', 'martfury_setup', 100 );

/**
 * Register widgetized area and update sidebar with default widgets.
 *
 * @since 1.0
 *
 * @return void
 */
function martfury_register_sidebar() {
	// Register primary sidebar
	$sidebars = array(
		'blog-sidebar'    => esc_html__( 'Blog Sidebar', 'martfury' ),
		'topbar-left'     => esc_html__( 'Topbar Left', 'martfury' ),
		'topbar-right'    => esc_html__( 'Topbar Right', 'martfury' ),
		'topbar-mobile'   => esc_html__( 'Topbar on Mobile', 'martfury' ),
		'header-bar'      => esc_html__( 'Header Bar', 'martfury' ),
		'post-sidebar'    => esc_html__( 'Single Post Sidebar', 'martfury' ),
		'page-sidebar'    => esc_html__( 'Page Sidebar', 'martfury' ),
		'catalog-sidebar' => esc_html__( 'Catalog Sidebar', 'martfury' ),
		'product-sidebar' => esc_html__( 'Single Product Sidebar', 'martfury' ),
		'footer-links'    => esc_html__( 'Footer Links', 'martfury' ),
	);

	if ( class_exists( 'WC_Vendors' ) || class_exists( 'WCMp' ) ) {
		$sidebars['vendor_sidebar'] = esc_html( 'Vendor Sidebar', 'martfury' );
	}

	// Register footer sidebars
	for ( $i = 1; $i <= 6; $i ++ ) {
		$sidebars["footer-sidebar-$i"] = esc_html__( 'Footer', 'martfury' ) . " $i";
	}

	$custom_sidebar = martfury_get_option( 'custom_product_cat_sidebars' );
	if ( $custom_sidebar ) {
		foreach ( $custom_sidebar as $sidebar ) {
			if ( ! isset( $sidebar['title'] ) || empty( $sidebar['title'] ) ) {
				continue;
			}
			$title                                = $sidebar['title'];
			$sidebars[ sanitize_title( $title ) ] = $title;
		}
	}

	// Register sidebars
	foreach ( $sidebars as $id => $name ) {
		register_sidebar(
			array(
				'name'          => $name,
				'id'            => $id,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h4 class="widget-title">',
				'after_title'   => '</h4>',
			)
		);
	}

}

add_action( 'widgets_init', 'martfury_register_sidebar' );

/**
 * Load theme
 */

// customizer hooks

require get_template_directory() . '/inc/mobile/theme-options.php';
require get_template_directory() . '/inc/vendors/theme-options.php';
require get_template_directory() . '/inc/backend/customizer.php';

// layout
require get_template_directory() . '/inc/functions/layout.php';

require get_template_directory() . '/inc/functions/entry.php';


// Woocommerce
require get_template_directory() . '/inc/frontend/woocommerce.php';

// Vendor
require get_template_directory() . '/inc/vendors/vendors.php';

// Mobile
require get_template_directory() . '/inc/libs/mobile_detect.php';
require get_template_directory() . '/inc/mobile/layout.php';

require get_template_directory() . '/inc/functions/media.php';

require get_template_directory() . '/inc/functions/header.php';

if ( is_admin() ) {
	require get_template_directory() . '/inc/libs/class-tgm-plugin-activation.php';
	require get_template_directory() . '/inc/backend/plugins.php';
	require get_template_directory() . '/inc/backend/meta-boxes.php';
	require get_template_directory() . '/inc/backend/product-cat.php';
	require get_template_directory() . '/inc/backend/product-meta-box-data.php';
	require get_template_directory() . '/inc/mega-menu/class-mega-menu.php';
	require get_template_directory() . '/inc/backend/editor.php';

} else {
	// Frontend functions and shortcodes
	require get_template_directory() . '/inc/functions/nav.php';
	require get_template_directory() . '/inc/functions/breadcrumbs.php';
	require get_template_directory() . '/inc/mega-menu/class-mega-menu-walker.php';
	require get_template_directory() . '/inc/mega-menu/class-mobile-walker.php';
	require get_template_directory() . '/inc/functions/comments.php';
	require get_template_directory() . '/inc/functions/footer.php';

	// Frontend hooks
	require get_template_directory() . '/inc/frontend/layout.php';
	require get_template_directory() . '/inc/frontend/nav.php';
	require get_template_directory() . '/inc/frontend/entry.php';
	require get_template_directory() . '/inc/frontend/footer.php';
}

require get_template_directory() . '/inc/frontend/header.php';

/**
 * WPML compatible
 */
if ( defined( 'ICL_SITEPRESS_VERSION' ) && ! ICL_PLUGIN_INACTIVE ) {
	require get_template_directory() . '/inc/wpml.php';
}

/** Shakir-20220928: Display and save Delivery Date & Delivery Time **/
/*
// Register main datetimepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_time_picker' );
function enabling_date_time_picker() {

    // Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :
    // Load the datetimepicker jQuery-ui plugin script
    wp_enqueue_style( 'datetimepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.min.css', array());
    wp_enqueue_script( 'datetimepicker', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-datetimepicker/2.5.20/jquery.datetimepicker.full.min.js', array('jquery'), '1.0', false );
    endif;
}

// Display custom checkout fields (+ datetime picker)
add_action('woocommerce_before_order_notes', 'display_custom_checkout_delivery_date_field', 10, 1 );
function display_custom_checkout_delivery_date_field( $checkout ) {
    // Define the time zone
    //date_default_timezone_set('Europe/Amsterdam'); // <== Set the time zone (http://php.net/manual/en/timezones.php)

    echo '<div id="my_custom_checkout_field">';

    // DateTimePicker
    woocommerce_form_field( 'delivery_date', array(
        'type'          => 'text',
        'class'         => 'date-picker', //array('my-field-class form-row-wide off'),
        'id'            => 'datetimepicker',
        'required'      => false,
        'label'         => __('Voorkeur bezorgdatum (voor witgoed & meubels ma-za van 08:00 tot 17:00)'), // (optioneel)
        'placeholder'   => __('dd/mm/yyyy'),
        'options'       => array('' => __('', 'woocommerce' ))
    ),'');

    echo '</div>';
}

// The jQuery script
add_action( 'wp_footer', 'display_custom_checkout_delivery_date_jquery_script');
function display_custom_checkout_delivery_date_jquery_script() {
    // Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script>
    jQuery(function($){
        var d = '#datetimepicker',
            f = d+'_field';

        // Enable the datetime picker field
        var date = new Date();
        date.setDate(date.getDate()+1);
        var minDateCal = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        date.setDate(date.getDate()+89);
        var maxDateCal = new Date(date.getFullYear(), date.getMonth(), date.getDate());
		var months = ["1January", "1February", "1March", "1April", "1May", "1June",
                  "1July", "1August", "1September", "1October", "1November", "1December"];
        $(d).datetimepicker({
            beforeShowDay: function(date) {
                var day = date.getDay();
                return [(day != 0), ''];
            },
            lang: 'de',
	  i18n: {
	    ar: { // Arabic
	      months: [
	        "كانون الثاني", "شباط", "آذار", "نيسان", "مايو", "حزيران", "تموز", "آب", "أيلول", "تشرين الأول", "تشرين الثاني", "كانون الأول"
	      ]
	    }},
			//monthNames: [ "Januar", "Februar", "Marts", "April", "Maj", "Juni", "Juli", "August", "September", "Oktober", "November", "December" ],
            minDate: minDateCal,
            maxDate: maxDateCal,
			defaultDate: minDateCal,
			format: 'd/m/Y',
            timepicker: false,
			//defaultDate: +1,
            //defaultDate: 2,
            //allowTimes:[ '00:00', '01:00', '02:00', '03:00', '04:00', '05:00', '06:00', 
            //    '07:00', '08:00', '09:00', '10:00', '11:00', '12:00', '13:00', '14:00',
            //    '15:00', '16:00', '17:00', '18:00', '19:00', '20:00', '21:00', '22:00', 
            //    '23:00']
        });
		
		//$(datetimepicker).regional['cs'] = {
    	//	monthNames: ['leden', 'únor', 'březen', 'duben', 'květen', 'červen', 'červenec', 'srpen', 'září', 'říjen', 'listopad', 'prosinec']
  		//};

  		//$(datetimepicker).setDefaults($(datetimepicker).regional['cs']);
    });
    jQuery("input[id='datetimepicker']").on('keyup',function(){
        if (keycode  != 13 && keycode  != 8 && keycode  != 9 && keycode  != 46){
            alert("Please select a date/time using calander.");
            jQuery(this).val("");
        }
    });
    </script>
    <?php
    endif;
}

// Check that the delivery date is not empty when it's selected
// add_action( 'woocommerce_checkout_process', 'check_datetimepicker_field' );
// function check_datetimepicker_field() {
//     if ( isset($_POST['delivery_option']) && $_POST['delivery_option'] === 'date'
//     && isset($_POST['delivery_date']) && empty($_POST['delivery_date']) ) {
//         wc_add_notice( __( 'Error: You must choose a delivery date and time', 'woocommerce' ), 'error' );
//     }
// }

// Check that the delivery date is not empty when it's selected
add_action( 'woocommerce_checkout_create_order', 'save_custom_checkout_delivery_date_field', 10, 2 );
function save_custom_checkout_delivery_date_field( $order, $data ) {
    if ( ! empty($_POST['delivery_date']) ) {
        $order->update_meta_data( 'delivery_datetime', sanitize_text_field( $_POST['delivery_date'] ) );
    }
}
*/
/** Shakir-20220928: Display and save Delivery Date & Delivery Time **/



/** Shakir-20220928: Display and save Delivery Date **/
// Register main datepicker jQuery plugin script
add_action( 'wp_enqueue_scripts', 'enabling_date_picker' );
function enabling_date_picker() {
    // Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :
    // Load the datepicker jQuery-ui plugin script
	wp_enqueue_style( 'datepicker', 'https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.min.css', array());
    wp_enqueue_script( 'datepicker', 'https://code.jquery.com/ui/1.13.2/jquery-ui.min.js', array('jquery'), '1.0', false );
    endif;
}

// Display custom checkout fields (+ datetime picker)
add_action('woocommerce_before_order_notes', 'display_custom_checkout_delivery_date_field', 10, 1 );
function display_custom_checkout_delivery_date_field( $checkout ) {
    // Define the time zone
    date_default_timezone_set('Europe/Amsterdam'); // <== Set the time zone (http://php.net/manual/en/timezones.php)

    echo '<div id="my_custom_checkout_field">';

    // DatePicker
    woocommerce_form_field( 'delivery_date', array(
        'type'          => 'text',
        'class'         => 'date-picker', //array('my-field-class form-row-wide off'),
        'id'            => 'datepicker',
        'required'      => false,
        'label'         => __('Voorkeur bezorgdag'), // (optioneel)
        'placeholder'   => __('dd/mm/yyyy'),
        'options'       => array('Select DeliveryDate' => __('', 'woocommerce' ))
    ),'');

    echo '</div>';
}

// The jQuery script
add_action( 'wp_footer', 'display_custom_checkout_delivery_date_jquery_script');
function display_custom_checkout_delivery_date_jquery_script() {
    $foundLargeItem = false;
	$largeItemcategories   = array('computers-technologies', 'afvoerdroger', 'afzuigunit', 'airco', 'amerikaanse-koelkast', 'centrifuge', 'condensdroger', 'eiland-afzuigkap', 'gasfornuis', 'inbouw-combimagnetron', 'inbouw-conventionele-oven', 
	'inbouw-elektrische-kookplaat', 'inbouw-gaskookplaat', 'inbouw-inductie-kookplaat', 'inbouw-keramische-kookplaat', 'inbouw-koelkast', 'inbouw-koel-vriescombinatie', 'inbouw-koffiemachine', 
	'inbouw-magnetron', 'inbouw-multifunctionele-oven', 'inbouw-stoomoven', 'inbouw-vaatwasser', 'inbouw-vriezer', 'inbouw-warmhoudlade', 'inductie-fornuis', 'integreerbare-afzuigkap', 
	'keramisch-fornuis', 'klimaatkast', 'koel-vriescombinatie', 'mini-koelkast', 'mini-vriezer', 'onderbouw-afzuigkap', 'schouwmodel-afzuigkap', 'tafelmodel-koelkast', 'tafelmodel-vriezer', 
	'vlakscherm-afzuigkap', 'vrieskast', 'vrieskist', 'vrijstaande-koelkast', 'vrijstaande-kookplaat', 'vrijstaande-vaatwasser', 'warmtepompdroger', 'was-droogcombinatie', 
	'wasmachine-bovenlader', 'wasmachine-voorlader', '4k-led-tv', '4k-oled-tv', '4k-qled-tv', '8k-qled-tv', 'blu-ray-speler', 'curved-monitor', 'digitale-tv', 'full-hd-qled-tv', 'hd-led-tv');
	// Only on front-end and checkout page
    if( is_checkout() && ! is_wc_endpoint_url() ) :
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( has_term( $largeItemcategories, 'product_cat', $cart_item['product_id'] ) ) {
				$foundLargeItem = true;
				break;
			}
			// $product = $cart_item['data'];
			// if(!empty($product)){
			// 	echo $product->get_image();
		}
		
		$phpdate = new DateTime();
		if(mktime(12, 0, 0) <= time()) {
			if($foundLargeItem==true){
				if(date('D') == 'Fri'){
					//Please block mondays after friday 1200 as delivery date (Large items only) Leon message on 23/10/2022
					$phpdate->modify('+4 day');
				}else{
					$phpdate->modify('+3 day');
				}				
			}else{
				$phpdate->modify('+2 day');
			}					
		} else {
			if($foundLargeItem==true){
				$phpdate->modify('+2 day');
			}else{
				$phpdate->modify('+1 day');
			}			
		}
		$phpmonth=$phpdate->format('m');
		$phpmonth=$phpmonth-1;
    ?>
    <script>
    jQuery(function($){		
		$.datepicker.regional['nl'] = {clearText: 'Effacer', clearStatus: '',
		closeText: 'sluiten', closeStatus: 'Onveranderd sluiten ',
		prevText: '<vorige', prevStatus: 'Zie de vorige maand',
		nextText: 'volgende>', nextStatus: 'Zie de volgende maand',
		currentText: 'Huidige', currentStatus: 'Bekijk de huidige maand',
		monthNames: ['januari','februari','maart','april','mei','juni',
		'juli','augustus','september','oktober','november','december'],
		monthNamesShort: ['jan','feb','mrt','apr','mei','jun',
		'jul','aug','sep','okt','nov','dec'],
		monthStatus: 'Bekijk een andere maand', yearStatus: 'Bekijk nog een jaar',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['zondag','maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag'],
		dayNamesShort: ['zo', 'ma','di','wo','do','vr','za'],
		dayNamesMin: ['zo', 'ma','di','wo','do','vr','za'],
		dayStatus: 'Gebruik DD als de eerste dag van de week', dateStatus: 'Kies DD, MM d',
		dateFormat: 'dd/mm/yy', firstDay: 1, 
		initStatus: 'Kies een datum', isRTL: false};
		$.datepicker.setDefaults($.datepicker.regional['nl']);

        var d = '#datepicker',
            f = d+'_field';
        // Enable the date picker field
        //var date = new Date();
		var date = new Date(<?php echo "{$phpdate->format('Y')}, {$phpmonth}, {$phpdate->format('d')}"; ?>);
		//alert(date.toLocaleDateString());
		var minDateCal = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        date.setDate(date.getDate()+29);
        var maxDateCal = new Date(date.getFullYear(), date.getMonth(), date.getDate());
        $(d).datepicker({
            beforeShowDay: function(date) {
				var day = date.getDay();
				return [(day != 0), ''];
			},
            minDate: minDateCal,
            maxDate: maxDateCal,
			defaultDate: minDateCal,
            // format: 'd/m/Y'
        });
    });
    jQuery("input[id='datepicker']").on('keyup',function(e){
		var keycode = e.keyCode || e.which;
        if (keycode != 8 && keycode != 9 && keycode != 13 && keycode != 16 && keycode != 17 && keycode != 46 //backspace, tab, enter, shift, ctrl, delete
			&& keycode != 18 && keycode != 20 && keycode != 27 //alt,caps,escape
			&& keycode != 35 && keycode != 36 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 10){ //end, home, left,up,right.down arrow keys
            alert("Selecteer de bezorgdatum met behulp van de kalender.");
            jQuery(this).val("");
        }
    });
    </script>
    <?php
    endif;
}

// Check that the delivery date is not empty when it's selected
add_action( 'woocommerce_checkout_create_order', 'save_custom_checkout_delivery_date_field', 10, 2 );
function save_custom_checkout_delivery_date_field( $order, $data ) {
    if ( ! empty($_POST['delivery_date']) ) {
        $order->update_meta_data( 'delivery_date', sanitize_text_field( $_POST['delivery_date'] ) );
		//$order->update_meta_data( 'delivery_date', sanitize_text_field(DateTime::createFromFormat('d/m/Y', $_POST['delivery_date'])->format('Y-m-d')) );		
    }
}
/** Shakir-20220928: Display and save Delivery Date **/

/** Shakir-20221014: Make housenumber field mandatory **/
// add_filter( 'woocommerce_billing_fields', 'make_wc_housenumber_field_required');
// function make_wc_housenumber_field_required( $fields ) {
// 	$fields['billing_address_2']['required'] = true;
// 	return $fields;
// }
/** Shakir-20221014: Make housenumber field mandatory **/

// Fahad code starts here
// add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// function woocommerce_product_custom_fields()
// {
//     global $woocommerce, $post;
//     echo '<div class="product_custom_field">';
   
//      woocommerce_wp_text_input(
//         array(
//             'id' => '_product_purchase_price',
//             'placeholder' => 'Purchase Price',
//             'label' => __('Purchase Price', 'woocommerce'),
//             'desc_tip' => 'true'
//         )
//     );
//      woocommerce_wp_text_input(
//         array(
//             'id' => '_product_margin',
//             'placeholder' => 'Margin',
//             'label' => __('Margin', 'woocommerce'),
//             'desc_tip' => 'true'
//         )
//     );
//     echo '</div>';
// }
// add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
// function woocommerce_product_custom_fields_save($post_id)
// {
//     $woocommerce_custom_product_margin = $_POST['_product_margin'];
//     $woocommerce_custom_product_purchase_price = $_POST['_product_purchase_price'];
//     if (!empty($woocommerce_custom_product_margin && $woocommerce_custom_product_purchase_price))
//         update_post_meta($post_id, '_product_purchase_price', esc_attr($woocommerce_custom_product_purchase_price));
//         update_post_meta($post_id, '_product_margin', esc_attr($woocommerce_custom_product_margin));
// }

// //API Routes

// add_action('rest_api_init',function () {
//     register_rest_route('v1','product-list',array('methods'  => 'GET','callback' => 'getProducts',));
//     register_rest_route('v1','add-product',array('methods'  => 'POST','callback' => 'addProduct',));
//     register_rest_route('v1','update-product/(?P<id>[\d]+)',array('methods'  => 'POST','callback' => 'updateProduct',));
// }
// );

// //API Call Backs
// /**
//  * @throws Exception
//  */
// function getProducts()
// {
//     $wc_api = new WC_API_Client( CONSUMER_KEY, SECRET_KEY, STORE_URL );

//     if(count($wc_api->get_products()) > 0){
//         return [
//             'status' => 200,
//             'success' => true,
//             'message' => "Product found successfully",
//             'data' => $wc_api->get_products(),
//         ];
//     }
//     return [
//         'status' => 203,
//         'success' => false,
//         'message' => "No Product found"
//     ];
// }
// function addProduct($data){
//     $wc_api = new WC_API_Client( CONSUMER_KEY, SECRET_KEY, STORE_URL );

//     $args = array(
//         'name' => $data['name'],
//         'type' => $data['type'],
//         'regular_price' => $data['regular_price'],
//         'description' => $data['description'],
//         'short_description' => $data['short_description'],
//         'meta_data' => [
//                 [
//                         'key' => '_product_purchase_price',
//                         'value' => $data['purchase_price']
//                 ],
//                 [
//                         'key' => '_product_margin',
//                         'value' => $data['margin']
//                 ]
//         ]
//     );
//     if($wc_api->get_products()->id > 0){
//         return [
//             'status' => 200,
//             'success' => true,
//             'message' => "Product has been added successfully"
//         ];
//     }
//     return [
//         'status' => 203,
//         'success' => false,
//         'data' => "Product couldn't added"
//     ];
// }

// function updateProduct(){
//     $wc_api = new WC_API_Client( CONSUMER_KEY, SECRET_KEY, STORE_URL );
//     $productId = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'))[3];
//     $data = [];

//     foreach($_POST as $key=>$value)
//     {
//         $data[] = [
//                 $key => $value
//         ];
//     }
// //   $wc_api->;
//     return [
//         'status' => 203,
//         'success' => false,
//         'data' => $data
//     ];
// }


/** Shakir-20221115: Add purchase_price field in woo product **/
function create_product_custom_text_field_purchase_price() {
	$args = array(
	'id' => 'product_custom_text_field_purchase_price',
	'label' => __( 'Aankoop prijs (€)', 'woocommerce' ),
	'class' => 'product-custom-field',
	'desc_tip' => true,
	'description' => __( 'Enter the Aankoop prijs.', 'woocommerce' ),
	);
	woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'create_product_custom_text_field_purchase_price' );

// save data from custom field
function save_product_custom_text_field_purchase_price( $post_id ) {
    $product = wc_get_product( $post_id );
    $purchase_price = isset( $_POST['product_custom_text_field_purchase_price'] ) ? $_POST['product_custom_text_field_purchase_price'] : '';
    $product->update_meta_data( 'product_custom_text_field_purchase_price', sanitize_text_field( $purchase_price ) );
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_product_custom_text_field_purchase_price' );

//rest Api logic for purchase price
function rest_get_product_custom_text_field_purchase_price($post, $field_name, $request) {
  return get_post_meta($post->id, $field_name);
}

function rest_update_product_custom_text_field_purchase_price($value, $post, $field_name) {
  if (!$value || !is_string($value)) {
    return;
  }
  return update_post_meta($post->ID, $field_name, $value);
}
function process_product_custom_text_field_purchase_price() {
    //register_api_field
	register_rest_field('post',
        'product_custom_text_field_purchase_price',
        array(
            'get_callback' => 'rest_get_product_custom_text_field_purchase_price',
            'update_callback' => 'rest_update_product_custom_text_field_purchase_price',
            'schema' => array(
                        'description' => 'The purchase price of the product.',
                        'type' => 'string',
                        'context' => array('view', 'edit')
                    )
        )
    );
}
//add_action('rest_api_init', 'process_product_custom_text_field_purchase_price');
/** Shakir-20221115: Add purchase_price field in woo product **/

/** Shakir-20221115: Add margin field in woo product **/
function create_product_custom_text_field_margin() {
	$args = array(
	'id' => 'product_custom_text_field_margin',
	'label' => __( 'marge', 'woocommerce' ),
	'class' => 'product-custom-field',
	'desc_tip' => true,
	'description' => __( 'Enter the marge.', 'woocommerce' ),
	);
	woocommerce_wp_text_input( $args );
}
add_action( 'woocommerce_product_options_general_product_data', 'create_product_custom_text_field_margin' );

// save data from custom field
function save_product_custom_text_field_margin( $post_id ) {
    $product = wc_get_product( $post_id );
    $margin = isset( $_POST['product_custom_text_field_margin'] ) ? $_POST['product_custom_text_field_margin'] : '';
    $product->update_meta_data( 'product_custom_text_field_margin', sanitize_text_field( $margin ) );
    $product->save();
}
add_action( 'woocommerce_process_product_meta', 'save_product_custom_text_field_margin' );

//rest Api logic for margin
function rest_get_product_custom_text_field_margin($post, $field_name, $request) {
  return get_post_meta($post->id, $field_name);
}

function rest_update_product_custom_text_field_margin($value, $post, $field_name) {
  if (!$value || !is_string($value)) {
    return;
  }
  return update_post_meta($post->ID, $field_name, $value);
}
function process_product_custom_text_field_margin() {
    //register_api_field
	register_rest_field('post',
        'product_custom_text_field_margin',
        array(
            'get_callback' => 'rest_get_product_custom_text_field_margin',
            'update_callback' => 'rest_update_product_custom_text_field_margin',
            'schema' => array(
                        'description' => 'The margin of the product.',
                        'type' => 'string',
                        'context' => array('view', 'edit')
                    )
        )
    );
}
//add_action('rest_api_init', 'process_product_custom_text_field_margin');
/** Shakir-20221115: Add margin field in woo product **/

add_filter( 'woocommerce_get_catalog_ordering_args', 'custom_catalog_ordering_args', 20, 1 );

function custom_catalog_ordering_args( $args) {

    $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $product_categories = array($uriSegments[3], $uriSegments[2]);
    if( ! is_product_category($product_categories) )
        return $args;

    $args['orderby'] = 'ID';

    if( $args['orderby'] == 'ID' )
        $args['order'] = 'DESC';

    return $args;
}
