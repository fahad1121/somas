<?php
/**
 * Admin new order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/admin-new-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails\HTML
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

$text_align = is_rtl() ? 'right' : 'left';
$address    = $order->get_formatted_billing_address();
$shipping   = $order->get_formatted_shipping_address();
$margin_side = is_rtl() ? 'left' : 'right';
$useCustEmail = false;
/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php /* translators: %s: Customer billing full name */ ?>
<p><?php printf( esc_html__( 'You’ve received the following order from %s:', 'woocommerce' ), $order->get_formatted_billing_full_name() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );
*/

do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );
?>
<h2>
	<?php
	if ( $sent_to_admin ) {
		$before = '<a class="link" href="' . esc_url( $order->get_edit_order_url() ) . '">';
		$after  = '</a>';
	} else {
		$before = '';
		$after  = '';
	}
	/* translators: %s: Order ID. */
	echo wp_kses_post( $before . sprintf( __( '[Order #%s]', 'woocommerce' ) . $after . ' (<time datetime="%s">%s</time>)', $order->get_order_number(), $order->get_date_created()->format( 'c' ), wc_format_datetime( $order->get_date_created() ) ) );
	?>
</h2>

<div style="margin-bottom: 40px;">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Purchase Price', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$items = $order->get_items();

			$show_sku = $sent_to_admin;
			$show_purchase_note = ($order->is_paid() && !$sent_to_admin);
			$show_image = false;
			$image_size = array( 32, 32 );
			foreach ( $items as $item_id => $item ) :
//                print("<pre>".print_r($item,true)."</pre>");die;
				$product       = $item->get_product();
				$sku           = '';
				$purchase_note = '';
				$image         = '';
			
				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}
			
				if ( is_object( $product ) ) {
					$sku           = $product->get_sku();
					$purchase_note = $product->get_purchase_note();
					$image         = $product->get_image( $image_size );
					if($sku=='ITMpp2021w' || $sku=='ITMpp2021Mac')
					{
						$useCustEmail = true;
					}
				}
			
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align: middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; word-wrap:break-word;">
					<?php
			
					// Show title/image etc.
					if ( $show_image ) {
						echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
					}
			
					// Product name.
					echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );
			
					// SKU.
					if ( $show_sku && $sku ) {
						echo wp_kses_post( ' (#' . $sku . ')' );
					}
			
					// allow other plugins to add additional product information here.
					do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, $plain_text );
			
					wc_display_item_meta(
						$item,
						array(
							'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
						)
					);
			
					// allow other plugins to add additional product information here.
					do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, $plain_text );
			
					?>
					</td>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php echo "00.00" ?>
					</td>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
						<?php
						$qty          = $item->get_quantity();
						$refunded_qty = $order->get_qty_refunded_for_item( $item_id );
			
						if ( $refunded_qty ) {
							$qty_display = '<del>' . esc_html( $qty ) . '</del> <ins>' . esc_html( $qty - ( $refunded_qty * -1 ) ) . '</ins>';
						} else {
							$qty_display = esc_html( $qty );
						}
						echo wp_kses_post( apply_filters( 'woocommerce_email_order_item_quantity', $qty_display, $item ) );
						?>
					</td>
				</tr>
				<?php
			
				if ( $show_purchase_note && $purchase_note ) {
					?>
					<tr>
						<td colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
							<?php
							echo wp_kses_post( wpautop( do_shortcode( $purchase_note ) ) );
							?>
						</td>
					</tr>
					<?php
				}
				?>			
			<?php endforeach; ?>
		</tbody>
	</table>
</div>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email ); ?>

<?php
/*
 * @hooked WC_Emails::order_meta() Shows order meta data. 
 * do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
*/

 $fields = apply_filters( 'woocommerce_email_order_meta_fields', array(), $sent_to_admin, $order );
/**
 * Deprecated woocommerce_email_order_meta_keys filter.
 *
 * @since 2.3.0
 */
$_fields = apply_filters( 'woocommerce_email_order_meta_keys', array(), $sent_to_admin );

if ( $_fields ) {
	foreach ( $_fields as $key => $field ) {
		if ( is_numeric( $key ) ) {
			$key = $field;
		}

		$fields[ $key ] = array(
			'label' => wptexturize( $key ),
			'value' => wptexturize( get_post_meta( $order->get_id(), $field, true ) ),
		);
	}
}

if ( $fields ) {

	if ( $plain_text ) {

		foreach ( $fields as $field ) {
			if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
				echo $field['label'] . ': ' . $field['value'] . "\n"; // WPCS: XSS ok.
			}
		}
	} else {

		foreach ( $fields as $field ) {
			if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
				if ($field['label']!='Huisnummer'){
					echo '<p><strong>' . $field['label'] . ':</strong> ' . $field['value'] . '</p>'; // WPCS: XSS ok.
				}
			}
		}
	}
}

/** Shakir-20220928: Display Delivery Date **/
// $deivery_date_shakir = wptexturize( get_post_meta( $order->get_id(), 'delivery_date', true ));
// if (isset($deivery_date_shakir))
// {
// 	if (! empty($deivery_date_shakir))
// 	{
// 		echo '<p><strong>Voorkeur bezorgdatum:</strong> ' . $deivery_date_shakir . '</p>'; // WPCS: XSS ok.
// 	}
// }
/** Shakir-20220928: Display Delivery Date **/

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
*/
?>

<table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;" border="0">
	<tr>
		<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;" valign="top" width="50%">
			<h2><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>

			<address class="address">
				
				<?php
				// if ( $fields ) {
				// 	if ( $plain_text ) {						
				// 	} else {
				// 		foreach ( $fields as $field ) {
				// 			if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
				// 				if ($field['label']=='Huisnummer'){
				// 					echo '<strong>' . $field['label'] . ':</strong> ' . $field['value'] . '<br>'; // WPCS: XSS ok.
				// 				}
				// 			}
				// 		}
				// 	}
				// }
				// ?>

				<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'woocommerce' ) ); ?>
				<?php if ( $order->get_billing_phone() ) : ?>
					<br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
				<?php endif; ?>
				<br/>
				<?php 
				$contactEmailSomas = 'order@somashome.nl';
				if($useCustEmail == true)
				{
					if ( $order->get_billing_email() )
					{
						$contactEmailSomas = $order->get_billing_email();
					}
				}
				echo esc_html( $contactEmailSomas ); 
				?>
			</address>
		</td>
		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && $shipping ) : ?>
			<td style="text-align:<?php echo esc_attr( $text_align ); ?>; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; padding:0;" valign="top" width="50%">
				<h2><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>

				<address class="address">
					<?php echo wp_kses_post( $shipping ); ?>
					<?php if ( $order->get_shipping_phone() ) : ?>
						<br /><?php echo wc_make_phone_clickable( $order->get_shipping_phone() ); ?>
					<?php endif; ?>
				</address>
			</td>
		<?php endif; ?>
	</tr>
</table>

<?php
/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
