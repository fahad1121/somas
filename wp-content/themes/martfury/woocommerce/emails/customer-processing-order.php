<?php
/**
 * Customer invoice email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-invoice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$text_align = is_rtl() ? 'right' : 'left';
$address    = $order->get_formatted_billing_address();
$shipping   = $order->get_formatted_shipping_address();
$margin_side = is_rtl() ? 'left' : 'right';

/**
 * Executes the e-mail header.
 *
 * @hooked WC_Emails::email_header() Output the email header
 * do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
 */

?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
		<style>
		/* .site-footer {
			color: #000000;
			font-weight: 400;
		}
		.site-footer .footer-info {
			display: flex;
			align-items: center;
			flex-wrap: wrap;
			justify-content: space-between;
			margin: 0 -15px;
			border-bottom: 1px solid #e1e1e1;
			padding-bottom: 55px;
			padding-top: 25px;
		} */

		.container {
			padding-left: 0;
			padding-right: 0;
		}
		.container .container {
			width: 100%;
		}
		img.template-logo {
			height: 52px;
			width: 330px;
			margin-top: 1.5em;
			margin-bottom: 0.5em; 
		}
		div.footer-h2-emaiil {
			color: #000000;
			font-family: "Work Sans", Arial, sans-serif;
			font-size: 15px;
			font-weight: 400 !important;
			line-height: 1.6;
			color: black;
			margin-top: 0.9em;
		}	
		h3.h3-email {
			font-size: 16px !important;
			font-weight: 500 !important;
			margin: 0 !important;
		}
		button.btn-email {
			margin: 0 20px !important;
			padding: 11px 16px !important;
			font-size: 16px !important;
			color: white !important;
			background: #21357e !important;
		}
		a.anchor-email {
			background-color: #2467B0;
			font-family: Roboto, Sans-serif;
			font-weight: 500;
			font-size: 14px;
			padding: 15px 10px;
			margin: 5px 5px;
			border-radius: 4px;
			width: auto;
			display: inline-block;
			line-height: 1;
			color: #fff;
			fill: #fff;
			text-align: center;
			text-decoration: none;			
		}
		a.custom-anchor {
			text-decoration: none !important;			
		}		
		div.email-color-bc {
			background-color: #eaf5ff !important;
			/* padding: 42px !important; */
			text-align: left !important;
		}
		hr.email-color-bc-hr {
			margin-top:-1px !important;
		}
		div.email-color-bc-2 {
			background-color: #eaf5ff !important;
			/* padding: 42px !important; */
			/* text-align: -webkit-center !important; */
			text-align: left;
		}
		td.btn-sectionemail {
			padding: 26px 0 !important;
		}
		div.ups-1
		{
			/* width: 24%; */
			width: 49%;
			/* border-right:1px solid #d5d5d5; */
			padding-right: 2px;
			padding-bottom:10px;
			display: inline-block;
			float: left;
		}
		div.ups-2
		{
			/* width: 24%; */
			width: 49%;
			/* border-right:1px solid #d5d5d5; */
			padding-right: 2px;
			padding-bottom:10px;
			display: inline-block;
			float: left;
		}
		div.ups-4
		{
			/* width: 24%; */
			width: 49%;
			padding-bottom:10px;
			display: inline-block;
			float: right;
		}
		
		@media (max-width: 991px) {
			.container {
				padding-left: 15px;
				padding-right: 15px;
			}
			.container .container {
				padding-left: 0;
				padding-right: 0;
			}
			
			div.ups-1
			{
				width: 49%;				
			}
			div.ups-2
			{
				width: 49%;
			}
			div.ups-4
			{
				width: 49%;
			}
		}		
		</style>
	</head>

	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0" style="background-color:transparent;margin:0;padding:0px 0;width:100%">
		<div id="wrapper" dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>" style="background-color:transparent;margin:0;padding:0px 0;width:100%;">
			<div class="container">
				<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%" style="max-width:500px;" align="center">
					<tr>
						<td align="center" valign="top">
							<div id="template_header_image">
								<img class="template-logo" src="https://www.somashome.be/wp-content/uploads/2022/08/Somas-Home-Logo-png.png" alt="Somas">								
							</div>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<div id="header-ups" style="">
								<div style="width: 100%; margin:10px 0px 0px 0px; text-align: center; font-size: 10px;">
									<div class="ups-1">
										<div style="border-right:1px solid #d5d5d5;">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-truck.jpg" style="height: 15px;" alt="">
											Gratis verzending
										</div>
									</div>
									<div class="ups-2">
										<div style="">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-credit-card.jpg" style="height: 15px;" alt="">
											Gespreid betalen, met 0% rente mogelijk
										</div>
									</div>
									<div class="ups-1">
										<div style="border-right:1px solid #d5d5d5;">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-shield-check.jpg" style="height: 17px;" alt="">
											2 jaar garantie
										</div>
									</div>
									<div class="ups-4">
										<div style="">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-undo2.jpg" style="height: 17px;" alt="">
											30 dagen bedenktijd
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px !important;"> -->
							<div class="email-color-bc" style="padding:0px 0px 0px 0px; border-bottom:3px solid #5e92b5;"></div>

							<div id="header-top-mb">
								<h1 style="text-align: center;color: black;display: block;font-size: 2em;margin-block-start: 0.67em;margin-block-end: 0.67em;margin-inline-start: 0px;margin-inline-end: 0px;font-weight: bold; margin:10px 0px 10px 0px;">Bedankt voor uw bestelling bij Somas Home!</h1>
							</div>

							<div class="email-color-bc" style="padding:40px 10px 40px 10px; border-bottom:3px solid #5e92b5;">
								<h2 class="h2-email" style="text-align: center;color: black;">
									<?php printf( esc_html__( 'Beste %s,', 'woocommerce' ), esc_html( $order->get_billing_first_name() ) ); ?><br>
									<?php printf( esc_html__( 'Bedankt voor uw bestelling met de ordernummer %s!', 'woocommerce' ), $order->get_order_number() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><br><br>
									U ontvangt een e-mail met een Track & Trace code zodra uw bestelling onderweg is.
								</h2>
							</div>

							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px !important;"> -->

							<div class="btn-sectionemail" style="text-align: center; margin:20px 0px 40px 0px;">
								<!-- <button class="btn-email" type="button">Bekijk uw bestelling</button> -->
								<a href="https://www.somashome.be/mijn-account/" class="anchor-email" role="button">
									<span class="">
										<span class="">Bekijk uw bestelling</span>
									</span>
								</a>
								<a href="https://www.somashome.be/bezorg-informatie-tv-witgoed-meubels/" class="anchor-email" role="button">
									<span class="">
										<span class="">Bezorginfo grote producten</span>
									</span>
								</a>
							</div>

							<div>
								<h2 class="heading-h2-emaiil" style="text-align: center;color: black;">Overzicht van uw bestelling</h2>
							</div>

							<div class="" style="background-color:transparent;padding:0px 0px 0px 0px;">
								<table border="0" cellpadding="0" cellspacing="0" id="template_container" width="100%" style="background-color:transparent;border:0px;">								
									<tr>
										<td align="center" valign="top">
											<!-- Body -->
											<table border="0" cellpadding="0" cellspacing="0" width="100%" id="template_body">
												<tr>
													<td valign="top" id="body_content" style="background-color:transparent;padding:0px 0px 0px 0px;">
														<!-- Content -->
														<table border="0" cellpadding="20" cellspacing="0" width="100%">
															<tr>
																<td valign="top" style="padding:0px 0px 0px 0px;">
																	<div id="body_content_inner">
																	                                                             
<?php if ( $order->needs_payment() ) { ?>

<?php } else { ?>

	<?php
}

/**
 * Hook for the woocommerce_email_order_details.
 *
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text, $email );
?>

<div style="">
	<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border-color: #5e92b5 !important;" border="1">
		<thead>
			<tr>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="td" scope="col" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$items = $order->get_items();
			$show_sku = $sent_to_admin;
			$show_image = true;
			$image_size = array( 90, 90 );
			$plain_text = $plain_text;
			$sent_to_admin = $sent_to_admin;
			foreach ( $items as $item_id => $item ) :
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
						<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
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
		<tfoot>
			<?php
			$item_totals = $order->get_order_item_totals();

			if ( $item_totals ) {
				$i = 0;
				foreach ( $item_totals as $total ) {
					$i++;
					?>
					<tr>
						<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
						<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
					</tr>
					<?php
				}
			}
			if ( $order->get_customer_note() ) {
				?>
				<tr>
					<th class="td" scope="row" colspan="1" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>;"><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
				<?php
			}
			?>
		</tfoot>
	</table>
</div>

<?php
do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text, $email );

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

/**
 * Executes the email footer.
 *
 * @hooked WC_Emails::email_footer() Output the email footer
 * do_action( 'woocommerce_email_footer', $email );
 */
?>
																	</div>
																</td>
															</tr>
														</table>
														<!-- End Content -->
													</td>
												</tr>
											</table>
											<!-- End Body -->
										</td>
									</tr>
								</table>
							</div>

							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px;"> -->

							<div>
								<h2 class="heading-h2-emaiil" style="text-align: center;color: black; margin:30px 0px 20px 0px;">Wij bezorgen uw bestelling op het onderstaand adres:</h2>
							</div>

							<?php
							// $HuisnummerVal='';

							
							// if ( $fields ) {
							// 	if ( $plain_text ) {						
							// 	} else {
							// 		foreach ( $fields as $field ) {
							// 			if ( isset( $field['label'] ) && isset( $field['value'] ) && $field['value'] ) {
							// 				if ($field['label']=='Huisnummer'){
							// 					$HuisnummerVal=$field['value'];
							// 				}
							// 			}
							// 		}
							// 	}
							// }
							// if($address){
							// 	$address=str_replace( '<strong></strong>',  ' ' . $HuisnummerVal, $address );
							// }
							?>

							<div class="senior-section" style="border-bottom:3px solid #5e92b5;">
								<div class="email-color-bc-2" style="padding:10px 10px 20px 10px;">
									<div style="display: inline-block; vertical-align: middle; width: 100px; text-align:center;">
										<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-home.png" alt="Shipping" style="max-width:60% !important;">
									</div>
									<div style="display: inline-block; vertical-align: middle; width: calc(98% - 100px);" >
										<h3 style="color:black !important; display:block; line-height:130%; text-align:center; font-size:16px; font-weight:500; margin:0; text-align: center !important; padding:20px 0px 20px 0px;">
											<?php echo wp_kses_post( $address ? $address : esc_html__( 'N/A', 'woocommerce' ) ); ?>
											<?php if ( $order->get_billing_phone() ) : ?>
												<br/><?php echo wc_make_phone_clickable( $order->get_billing_phone() ); ?>
											<?php endif; ?>
											<?php if ( $order->get_billing_email() ) : ?>
												<br/><?php echo esc_html( $order->get_billing_email() ); ?>
											<?php endif; ?>
										</h3>
									</div>
								</div>
							</div>

							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px;"> -->

							<div>
								<h2 class="heading-h2-emaiil" style="text-align: center;color: black; margin:40px 0px 20px 0px;">Belangrijk om te weten</h2>
							</div>

							<div style="border-bottom:3px solid #5e92b5;">
								<div class="email-color-bc" style="padding:10px 10px 20px 25px;">
									<h2 class="h2-email" style="color: black; margin-bottom: 10px !important;">
										Bestelling annuleren
									</h2>
									<h3 class="h3-email" style="color: black;">
										Dat kan als uw bestelling nog niet verzonden is via de <a href="https://www.somashome.be/klantenservice/" class="custom-anchor">Klantenservice</a>.
									</h3>
									<br/>
									<h2 class="h2-email" style="color: black; margin-bottom: 10px !important;">
										Bestelling wijzigen
									</h2>
									<h3 class="h3-email" style="color: black;">
										Dat kan alleen via de <a href="https://wa.me/31622586631" class="custom-anchor">WhatsApp chat</a> of <a href="tel:0880115999" class="custom-anchor">telefoon</a>.
									</h3>
									<br/>
									<h2 class="h2-email" style="color: black; margin-bottom: 10px !important;">
										lets aan de bestelling toevoegen
									</h2>
									<h3 class="h3-email" style="color: black;">
										Wilt u een van onze <a href="https://www.somashome.be/product-category/installatie-montage/" class="custom-anchor">installatie & montage</a> diensten toevoegen aan uw bestelling of wilt u dat wij uw oude
										apparaat meenemen voor recycling? Laat het ons zo snel mogelijk weten!
									</h3>
									<br/>
									<h2 class="h2-email" style="color: black; margin-bottom: 10px !important;">
										Bedenktijd
									</h2>
									<h3 class="h3-email" style="color: black;">
										U heeft 30 dagen bedenktijd voor alle producten. <a href="https://www.somashome.be/retourneren/" class="custom-anchor">Retourneren</a> is gratis*
									</h3>
									<br/>
									<h2 class="h2-email" style="color: black; margin-bottom: 10px !important;">
										Bezorging
									</h2>
									<h3 class="h3-email" style="color: black;">
									Voor kleine producten ontvangt u een T&T van DHL of PostNL. Voor grote producten ontvangt u een SMS van Runners, BCC of een andere bezorgdienst. Meer lezen klik <a href="https://www.somashome.be/bezorgen-installeren/" class="custom-anchor">hier</a>.
									</h3>
								</div>
							</div>

							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px;"> -->

							<div>
								<div>
									<h2 class="heading-h2-emaiil" style="text-align: center;color: black; margin:35px 0px 20px 0px;">
										Heeft u nog vragen?
									</h2>
								</div>
							</div>

							<div>
								<div class="btn-sectionemail" style="text-align: center;">
									<a href="https://www.somashome.be/klantenservice/" class="anchor-email" role="button">
										<span class="">
											<span class="">Naar de klantenservice</span>
										</span>
									</a>
									<a href="mailto:contact@somashome.be" class="anchor-email" role="button">
										<span class="">
											<span class="">contact@somashome.be</span>
										</span>
									</a>
								</div>
							</div>

							<div style="margin:20px 0px 0px 0px;">
								<div class="" style="background-color: #eaf5ff !important; text-align: center;">
									<div class="" style="padding:10px 0px 10px 0px;">
										<table style="width:100%;" cellspacing="0" cellpadding="0" border="0" width="100%">
											<tbody>
												<tr>
													<td style="text-align:middle;vertical-align:middle;max-width: 25%;">
														<a href="https://wa.me/31622586631"><img src="https://www.iconsdb.com/icons/preview/tropical-blue/whatsapp-xxl.png" alt="Whatsapp" style="max-width: 25px;"></a>
													</td>
													<td style="text-align:middle;vertical-align:middle;max-width: 25%;">
														<a href="tel:0880115999"><img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-telephone.png" alt="call" style="max-width: 25px;"></a>
													</td>
													<td style="text-align:middle;vertical-align:middle;max-width: 25%;">
														<a href="https://business.facebook.com/somashomenl/?business_id=200531854583896"><img src="https://cdn.iconscout.com/icon/free/png-256/facebook-logo-2019-1597680-1350125.png" alt="fb" style="max-width: 25px;"></a>
													</td>
													<td style="text-align:middle;vertical-align:middle;max-width: 25%;">
														<a href="https://www.instagram.com/somashome_nl/"><img src="https://www.clipartmax.com/png/full/110-1104111_follow-us-png-blue-ig-logo.png" alt="insta" style="max-width: 25px;"></a>
													</td>
												</tr>	
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="email-color-bc" style="padding:0px 0px 0px 0px; border-bottom:3px solid #5e92b5;"></div>
							<!-- <hr style="border-top: 1px solid #d4e7f4; margin-top:-1px;"> -->

							<!-- <div>
								<div>
								<h2 class="heading-h2-emaiil" style="text-align: center;color: black; margin:30px 0px 15px 0px;">
										Andere klanten kopen ook:
									</h2>
								</div>
							</div>							 -->
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<div id="footer-ups" style="">
								<div style="width: 100%; margin:50px 0px 20px 0px; text-align: center; font-size: 10px;">
									<div class="ups-1">
										<div style="border-right:1px solid #d5d5d5;">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-truck.jpg" style="height: 15px;" alt="">
											Gratis verzending
										</div>
									</div>
									<div class="ups-2">
										<div style="">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-credit-card.jpg" style="height: 15px;" alt="">
											Gespreid betalen, met 0% rente mogelijk
										</div>
									</div>
									<div class="ups-1">
										<div style="border-right:1px solid #d5d5d5;">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-shield-check.jpg" style="height: 17px;" alt="">
											2 jaar garantie
										</div>
									</div>
									<div class="ups-4">
										<div style="">
											<img src="https://www.somashome.be/wp-content/uploads/2022/08/icon-undo2.jpg" style="height: 17px;" alt="">
											30 dagen bedenktijd
										</div>
									</div>
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<div>
								<div style="width: 100%; margin:30px 0px 15px 0px; text-align: center;">
									<!-- Bekijk onze 281 reviews &nbsp;  -->
									<a href="https://nl.trustpilot.com/review/somashome.be"><img src="https://www.somashome.be/wp-content/uploads/2022/08/Trustpilot_ratings_4halfstar-RGB-512x96-1.png" alt="" style="height: 41px;width: 222px;"></a>
								</div>
							</div>

							<div class="email-color-bc" style="padding:0px 0px 0px 0px; border-bottom:3px solid #5e92b5;">								
							</div>
							<!-- <hr style="border-top: 1px solid #7b90a9;"> -->

							<div>
								<div class="footer-h2-emaiil" >
								<!-- <h2 class="footer-h2-emaiil" style="text-align: center;color: black;"> -->
									<div class="footer-copyright">© Copyright 2022 Somas Retail | <a href="https://www.somashome.be/algemene-voorwaarden//" class="custom-anchor">Algemene voorwaarden</a> | <a href="https://www.somashome.be/disclaimer/" class="custom-anchor">Disclaimer</a> | <a href="https://www.somashome.be/privacy-policy/" class="custom-anchor">Privacy</a></div>
									<!-- </h2> -->
								</div>
							</div>
						</td>
					</tr>					
				</table>
			</div>
		</div>
	</body>
</html>