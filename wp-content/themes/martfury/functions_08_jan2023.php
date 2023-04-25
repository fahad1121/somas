<?php

function theme_styles()  
{ 

	wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
	wp_enqueue_style( 'owl-style', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css');
	wp_enqueue_script( 'owl-js', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js');

}
add_action('wp_enqueue_scripts', 'theme_styles');
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

// Register Feed Custom Taxonomy
function create_feeds_custom_taxonomy()  {
	$labels = array(
		'name'                       => 'Feeds',
		'singular_name'              => 'Feed',
		'menu_name'                  => 'Feeds',
		'all_items'                  => 'All Feeds',
		'parent_item'                => 'Parent Feed',
		'parent_item_colon'          => 'Parent Feed:',
		'new_item_name'              => 'New Feed Name',
		'add_new_item'               => 'Add New Feed',
		'edit_item'                  => 'Edit Feed',
		'update_item'                => 'Update Feed',
		'separate_items_with_commas' => 'Separate Feed with commas',
		'search_items'               => 'Search Feeds',
		'add_or_remove_items'        => 'Add or remove Feeds',
		'choose_from_most_used'      => 'Choose from the most used Feeds',
	);
	$capabilities = array(
		'manage_terms'               => 'manage_woocommerce',
		'edit_terms'                 => 'manage_woocommerce',
		'delete_terms'               => 'manage_woocommerce',
		'assign_terms'               => 'manage_woocommerce',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => false,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
		'show_in_rest'          	 => true,
		'query_var' 		 		 => true,
		'rewrite'               	 => array( 'slug' => 'feed' ),
		'rest_base'             	 => 'feed',
		'rest_controller_class' 	 => 'WP_REST_Terms_Controller',
		'capabilities'               => $capabilities,
	);
	register_taxonomy( 'feed', 'product', $args );
	register_taxonomy_for_object_type( 'feeds', 'product' );
	}
	add_action( 'init', 'create_feeds_custom_taxonomy', 0 );
	add_action( 'woocommerce_after_shop_loop_item', 'action_function_name_9559' );
	function action_function_name_9559(){
		global $product;
		$value = $product->get_price();
		$isSmartPhoneCat = false;
		$category_ids = array( 575 );
		$terms = get_the_terms( $product->get_id(), 'product_cat' );
		foreach ( $terms as $term ) {
			if ( in_array( $term->term_id, $category_ids ) ) {
				$isSmartPhoneCat = true;
				break;
			}
		}
		echo '<div class="keer-wrapper">';	
		if($value>35){
			echo '<div class="three-keer">';
			echo '<p>'. get_woocommerce_currency_symbol(); 
			echo str_replace(".",",",number_format(round(($value)/3,2),2)).'</p>';
			echo 'in 3 keer';
			echo '</div>';
		}
		if($value>250 && $isSmartPhoneCat == false){
			echo '<div class="thirtysix-keer">';
			echo '<p>'. get_woocommerce_currency_symbol(); 
			echo str_replace(".",",",number_format(round(($value)*0.03206,2),2)).'</p>';
			echo 'in 36 keer';
			echo '</div>';
		}
			echo '</div>';
	}
	//Register taxonomy API for WC
	add_action( 'rest_api_init', 'register_rest_field_for_custom_taxonomy_feeds' );
	function register_rest_field_for_custom_taxonomy_feeds() {
		register_rest_field('product', "feeds", array(
			'get_callback'    => 'product_get_callback_feed',
			'update_callback'    => 'product_update_callback_feed',
			'schema' => null,
		));
	}
	
	//Get Taxonomy record in wc REST API
	function product_get_callback_feed($post, $attr, $request, $object_type)
	{
		$terms = array();
		// Get terms
		foreach (wp_get_post_terms( $post[ 'id' ], 'feed') as $term) {
			$terms[] = array(
				'id'        => $term->term_id,
				'name'      => $term->name,
				'slug'      => $term->slug
			);
		}
		return $terms;
	}
			
	//Update Taxonomy record in wc REST API
	function product_update_callback_feed($values, $post, $attr, $request, $object_type)
	{   
		if ( empty( $values ) || ! is_array( $values ) ) {
			return;
		}
		
		// Post ID
		$postId = $post->get_id();
		
		$feeds = array();
		foreach ($values as $value) {
			$feed = json_decode(json_encode($value));
			array_push($feeds, $feed->name);
		}
		
	   // Set terms
	   wp_set_object_terms( $postId, $feeds, 'feed');
	}
	
	//Register Brand taxonomy API for WC
	add_action( 'rest_api_init', 'register_rest_field_for_custom_taxonomy_brands' );
	function register_rest_field_for_custom_taxonomy_brands() {
		register_rest_field('product', "brands", array(
			'get_callback'    => 'product_get_callback_brand',
			'update_callback'    => 'product_update_callback_brand',
			'schema' => null,
		));
	}
	
	//Get Brand Taxonomy record in wc REST API
	function product_get_callback_brand($post, $attr, $request, $object_type)
	{
		$terms = array();
		// Get terms
		foreach (wp_get_post_terms( $post[ 'id' ],'product_brand') as $term) {
			$terms[] = array(
				'id'        => $term->term_id,
				'name'      => $term->name,
				'slug'      => $term->slug
			);
		}
		return $terms;
	}
			
	//Update Brand Taxonomy record in wc REST API
	function product_update_callback_brand($values, $post, $attr, $request, $object_type)
	{   
		if ( empty( $values ) || ! is_array( $values ) ) {
			return;
		}
		
		// Post ID
		$postId = $post->get_id();
		
		$brands = array();
		foreach ($values as $value) {
			$brand = json_decode(json_encode($value));
			array_push($brands, $brand->name);
		}
		
		//$values = array( 'Apple', 'Asus' );
		// Set terms
	   wp_set_object_terms( $postId, $brands, 'product_brand');
	}
	function hide_core_update_notifications_from_users() {
		remove_action( 'admin_notices', 'update_nag', 3 );
	}
	add_action( 'admin_head', 'hide_core_update_notifications_from_users', 1 );
	add_filter( 'auto_update_plugin', '__return_false' );
	add_filter( 'auto_update_theme', '__return_false' );
	
	/** Remove categories from shop and other pages * in Woocommerce */ 
	function wc_hide_selected_terms( $terms, $taxonomies, $args ) { 
		$new_terms = array(); 
		if ( in_array( 'product_cat', $taxonomies ) && !is_admin() && is_shop() ) { 
			foreach ( $terms as $key => $term ) { 
				if ( ! in_array( $term->slug, array( 'uncategorized' ) ) ) { 
					$new_terms[] = $term; 
				} 
			} 
			$terms = $new_terms; 
		} 
		return $terms; 
	} 
	add_filter( 'get_terms', 'wc_hide_selected_terms', 10, 3 );
	
	/** Shakir-20220316: Remove spraypay payment for smartphone category **/ 
	function somas_unset_gateway_by_category( $available_gateways ) {
		if ( is_admin() ) return $available_gateways;
		if ( ! is_checkout() ) return $available_gateways;
		$unset = false;
		//$unset = 'false';
	
		/* $category_ids = array( 8, 37 ); */
		$category_ids = array( 575 );
	
		foreach ( WC()->cart->get_cart_contents() as $key => $values ) {
			$terms = get_the_terms( $values['product_id'], 'product_cat' );
			foreach ( $terms as $term ) {
				/* echo '<script>console.log("Shakir PHP error: ' . $term->term_id . '")</script>'; */			
				if ( in_array( $term->term_id, $category_ids ) ) {
					$unset = true;
					//$unset = 'true';
					break;
				}
			}
		}
		if ( $unset == true ) unset( $available_gateways['pay_gateway_spraypay'] );
		return $available_gateways;
	}
	add_filter( 'woocommerce_available_payment_gateways', 'somas_unset_gateway_by_category' );
	
	/** Shakir-20220406: Add prefix to order number **/
	function add_prefix_woocommerce_order_number( $order_id ) {
		$prefix       = 'SHB';
		$new_order_id = $prefix . $order_id;
		return $new_order_id;
	}
	add_filter( 'woocommerce_order_number', 'add_prefix_woocommerce_order_number' );
	
	/** Shakir-20220617: Disable theme edit in Woo Admin **/
	function disable_martfurytheme_edit() {
		define('DISALLOW_FILE_EDIT', TRUE);
	}
	add_action('init','disable_martfurytheme_edit');
	
	/** Shakir-20220617: Add Category Second Desc **/
	// 1. Display field on "Add new product category" admin page
	function somashome_wp_cat_editor_add() {
		?>
		<div class="form-field">
			<label for="seconddesc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label>
		<?php
		  $settings = array(
			 'textarea_name' => 'seconddesc',
			 'quicktags' => array( 'buttons' => 'em,strong,link' ),
			 'tinymce' => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			 ),
			 'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
		  );
		  wp_editor( '', 'seconddesc', $settings );
		?>
			<p class="description"><?php echo __( 'This description will go BELOW products on the category page', 'woocommerce' ); ?></p>
		</div>
		<?php
	}
	add_action( 'product_cat_add_form_fields', 'somashome_wp_cat_editor_add', 10, 2 );
	 
	// ---------------
	// 2. Display field on "Edit product category" admin page  
	function somashome_wp_cat_editor_edit( $term ) {
		$second_desc = htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) );
		?>
		<tr class="form-field">
			<th scope="row" valign="top"><label for="second-desc"><?php echo __( 'Second Description', 'woocommerce' ); ?></label></th>
			<td>
			<?php
			 $settings = array(
				'textarea_name' => 'seconddesc',
				'quicktags' => array( 'buttons' => 'em,strong,link' ),
				'tinymce' => array(
				   'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				   'theme_advanced_buttons2' => '',
				),
				'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
			 );
			 wp_editor( $second_desc, 'seconddesc', $settings );
			 ?>
				<p class="description"><?php echo __( 'This is the description that goes BELOW products on the category page', 'woocommerce' ); ?></p>
			</td>
		</tr>
		<?php
	}
	add_action( 'product_cat_edit_form_fields', 'somashome_wp_cat_editor_edit', 10, 2 );
	 
	// ---------------
	// 3. Save field @ admin page 
	function somashome_save_wp_editor( $term_id, $tt_id = '', $taxonomy = '' ) {
	   if ( isset( $_POST['seconddesc'] ) && 'product_cat' === $taxonomy ) {
		  update_woocommerce_term_meta( $term_id, 'seconddesc', esc_attr( $_POST['seconddesc'] ) );
	   }
	}
	add_action( 'edit_term', 'somashome_save_wp_editor', 10, 3 );
	add_action( 'created_term', 'somashome_save_wp_editor', 10, 3 );
	
	// ---------------
	// 4. Display field under products @ Product Category pages
	function somashome_display_wp_cat_editor_content() {
	   if ( is_product_taxonomy() ) {
		  $term = get_queried_object();
		  if ( $term && ! empty( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) {
			 echo '<p class="term-description">' . wc_format_content( htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) . '</p>';
		  }
	   }
	}
	add_action( 'woocommerce_after_main_content', 'somashome_display_wp_cat_editor_content', 5 );
	//add_action( 'woocommerce_after_shop_loop', 'somashome_display_wp_cat_editor_content', 5 );
	/** Shakir-20220617: Add Category Second Desc **/
	
	/** Shakir-20220623: Add trust pilot 5.0 on product page **/
	function somashome_add_custom_section_below_product() {
		echo '<center><img width="218" height="22" src="https://www.somashome.be/wp-content/uploads/2022/11/trustpilot4.5Latest.svg" class="attachment-full size-full" alt="" loading="lazy" style="margin-top:10px;"></center>';
	}
	add_action( 'woocommerce_product_meta_end', 'somashome_add_custom_section_below_product', 10 );
	/** Shakir-20220623: Add trust pilot 5.0 on product page **/
	
	/** Shakir-20220830: Change reply to email from customer email to admin in New Order **/
	add_filter( 'woocommerce_email_headers', 'new_order_reply_to_admin_header', 20, 3 );
	function new_order_reply_to_admin_header( $header, $email_id, $order ) {
		if ( $email_id === 'new_order' ){
			$email = new WC_Email($email_id);
			$header = "Content-Type: " . $email->get_content_type() . "\r\n";
			$header .= 'Reply-to: ' . __('SomasHome') . ' <' . 'order@somashome.be' . ">\r\n";
		}
		return $header;
	}
	/** Shakir-20220830: Change reply to email from customer email to admin in New Order **/
	
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
			'class'         => array('date-picker'), //array('my-field-class form-row-wide off'),
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
		$largeItemcategories   = array('afvoerdroger', 'afzuigunit', 'airco', 'amerikaanse-koelkast', 'centrifuge', 'condensdroger', 'eiland-afzuigkap', 'gasfornuis', 'inbouw-combimagnetron', 'inbouw-conventionele-oven', 
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
					//$phpdate->modify('+3 day');
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
	// add_filter( 'woocommerce_billing_fields', 'ts_require_wc_housenumber_field');
	// 	function ts_require_wc_housenumber_field( $fields ) {
	// 	$fields['billing_address_2']['required'] = true;
	// 	return $fields;
	// }
	/** Shakir-20221014: Make housenumber field mandatory **/
	
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

/**
 * Fahad code starts here
 *
 */

add_filter('woocommerce_get_catalog_ordering_args', function ($args) {
    $orderby_value = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : apply_filters('woocommerce_default_catalog_orderby', get_option('woocommerce_default_catalog_orderby'));

    if ('menu_order' == $orderby_value) {
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        $args['meta_key'] = 'product_custom_text_field_margin';
    }

    return $args;
});