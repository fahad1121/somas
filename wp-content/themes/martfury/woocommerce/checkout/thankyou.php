<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="container">

        <div class="heading-two-thanku">
            <h2>Bedankt voor uw bestelling!​</h2>

        </div>
        <div class="para-one-thanku">
            <p>Binnen enkele ogenblikken ontvangt u een bestelbevestiging via de mail. Hierin staan alle gegevens van uw bestelling. Zoals uw bestelnummer, bestelde artikelen, bezorg en factuuradres.  </p>
        </div>
	<?php if ( $order->has_status( 'failed' ) ) : ?>
			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce' ); ?></p>

			<p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
				<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'woocommerce' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My account', 'woocommerce' ); ?></a>
				<?php endif; ?>
			</p>

		<?php else : ?>
			<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

			<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">

				<li class="woocommerce-order-overview__order order">
					<?php esc_html_e( 'Order number:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<li class="woocommerce-order-overview__date date">
					<?php esc_html_e( 'Date:', 'woocommerce' ); ?>
					<strong><?php echo wc_format_datetime( $order->get_date_created() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email() ) : ?>
					<li class="woocommerce-order-overview__email email">
						<?php esc_html_e( 'Email:', 'woocommerce' ); ?>
						<strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
					</li>
				<?php endif; ?>

				<li class="woocommerce-order-overview__total total">
					<?php esc_html_e( 'Total:', 'woocommerce' ); ?>
					<strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
				</li>

				<?php if ( $order->get_payment_method_title() ) : ?>
					<li class="woocommerce-order-overview__payment-method method">
						<?php esc_html_e( 'Payment method:', 'woocommerce' ); ?>
						<strong><?php echo wp_kses_post( $order->get_payment_method_title() ); ?></strong>
					</li>
				<?php endif; ?>

			</ul>

		<?php endif; ?>

        <div class="para-two-thanku">
            <p>Controleer a.u.b. of alles klopt! Als dit niet het geval is, neem dan meteen contact met ons op, zodat we hopelijk de wijzigingen kunnen aanbrengen voordat uw bestelling onderweg is! Terwijl wij aan de slag gaan met uw bestelling, kunt u in deze wachtruimte lekker ontspannen. </p>
        </div>

        <div class="row three-columns">
            <div class="col-md-12 col-lg-4 column-one-thanks">
                <p>Waar heeft u zin in?</p>
                <ul>
                    <li>Iets verder vindt u 2 trending muziekvideo’s</li>
                    <li>Nieuwsgierig naar wat anderen kopen? De top 5 vindt u hieronder</li>
                    <li> Toch opzoek naar meer informatie over de bezorging?
                        Dit vindt u helemaal onderaan deze pagina.</li>
                  </ul>  
            </div>
            <div class="col-md-6 col-lg-4 column-two-thanks">
                <iframe width="350" height="200" src="https://www.youtube.com/embed/Ts_sc54Z1v0" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="col-md-6 col-lg-4 column-three-thanks">
                <iframe width="350" height="200" src="https://www.youtube.com/embed/OS8taasZl8k" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
        <div class="row selling-wrapper">
            <div class="col-md-12 selling-heading">
                <h3>De best verkochte artikelen van dit moment:</h3>
            </div>
            <div class="col-md-12 sp-thanks">
                <?php echo do_shortcode('[products limit="5" columns="5" best_selling="true"]'); ?>
            </div>
            <div class="col-md-12 selling-heading">
                <h3>Installatie</h3>
            </div>
            <div class="col-md-12 fb_installation_services">
                <?php echo do_shortcode('[product_category category=“installatie-montage” per_page="5" columns="5" orderby= “date” order=“desc”]')?>
                
            </div>


        </div>
        <div class="row para-three">
            <div class="col-md-12 heading-three-thanku">
                <h3>Extra bezorginformatie</h3>
            </div>
            <div class="col-md-12 col-lg-4 text-para">
                <h5>Kleine artikelen</h5>
                <p>Kleine producten zoals onder andere laptops, tablets en stofzuigers worden met DHL verstuurd. Voor 16:00 besteld, is morgen in huis. U ontvangt automatisch van ons systeem een Track & Trace code.</p>
            </div>
            <div class="col-md-6 col-lg-4 text-para">
                <h5>Grote artikelen</h5>
                <p>Grote producten zoals televisies van 43 inch of groter, wasmachines, koelkasten en fornuizen. Worden op zijn vroegst overmorgen geleverd. Tijdens het afrekenen kunt u ook een bezorgdatum kiezen.</p>
                <p>ij bezorgen van maandag tot en met zaterdag van 08:00 tot 17:00.</p>                    
                <p>U ontvangt de dag voor de bezorging tussen 20:00 en 22:00 een SMS met daarin het tijdsblok waarin de bezorgers langskomen.</p>            
            </div>
            <div class="col-md-6 col-lg-4 text-para">
                <h5>Schrijf je in voor onze nieuwsbrief</h5>
                <p>Meubels worden binnen 3 tot 5 werkdagen geleverd. U kunt tijdens het afrekenen een voorkeur bezorgdatum kiezen. De dag voor de bezorging informeren wij u over het tijdsblok, waarin de bezorgers langskomen.</p>           
            </div>
        </div>
        <div class="row last-column">
            <div class="col-md-12 heading-three-thanku">
                <h3>Onze nieuwsbrief & socials</h3>
            </div>
            <div class="col-md-6 col-lg-6 text-para">
                <h5>Schrijf je in voor onze nieuwsbrief</h5>
                <p>Komt eraan!</p>           
            </div>
            <div class="col-md-6 col-lg-6 text-para">
                <h5>Bekijk ons ook op Social Media</h5>
				<div>
				<img src="https://www.somashome.be/wp-content/uploads/2022/08/facebook.svg" alt="Italian Trulli" width="50" height="50">
				<img src="https://www.somashome.be/wp-content/uploads/2022/08/Instagram-1.svg" alt="Italian Trulli" width="50" height="50">
				</div>
            </div>

        </div>


</div>
<!-- moneeb end here -->

<div class="woocommerce-order">

	<?php
	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		
		<?php //do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() ); ?>
		<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

	<?php else : ?>

		<p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'woocommerce' ), null ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

	<?php endif; ?>

</div>
