<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo '<div class="row">';
do_action( 'woocommerce_before_checkout_form', $checkout );
echo '</div>';

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', esc_html__( 'You must be logged in to checkout.', 'martfury' ) );

	return;
}

?>
<form name="checkout" method="post" class="checkout woocommerce-checkout"
      action="<?php echo esc_url( wc_get_checkout_url() ); ?>"
      enctype="multipart/form-data">
	<div class="row">
		<div class="col-xs-12 col-sm-12 col-md-7 col-woo-checkout-details">
			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<div id="customer_details">
					<div class="checkout-billing">
						<?php do_action( 'woocommerce_checkout_billing' ); ?>
					</div>

					<div class="checkout-shipping">
						<?php do_action( 'woocommerce_checkout_shipping' ); ?>
					</div>
				</div>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>
		</div>
		<div class="col-xs-12 col-sm-12 col-md-5">
			<h3 id="order_review_heading"><?php esc_html_e( 'Uw bestelling', 'martfury' ); ?></h3>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
		</div>
	</div>

</form>

<script>
    jQuery(document).ready(function(){
        var houseNumber = houseNumber_old = '';
        if(jQuery("#billing_address_2").length)
        {
            if(jQuery("#billing_address_2").val()!=null)
            {
                houseNumber = houseNumber_old = jQuery("#billing_address_2").val();
            }
        }
        var postcode = postcode_old = '';
        if(jQuery("#billing_postcode").length)
        {
            if(jQuery("#billing_postcode").val()!=null)
            {
                postcode = postcode_old = jQuery("#billing_postcode").val();
            }
        }
        jQuery("#billing_address_2").focusout(function(){
            houseNumber = jQuery(this).val();
            if((houseNumber != '' && postcode != '') && (houseNumber != houseNumber_old || postcode != postcode_old)){
                houseNumber_old = houseNumber;
                postcode_old = postcode;
                getApiResults(postcode,houseNumber);
            }
        });
        jQuery("#billing_postcode").focusout(function(){
            postcode = jQuery(this).val();
            if((houseNumber != '' && postcode != '') && (houseNumber != houseNumber_old || postcode != postcode_old)){
                houseNumber_old = houseNumber;
                postcode_old = postcode;
                getApiResults(postcode,houseNumber);
            }
        })
    });
    function getApiResults(postcode,houseNumber){
        jQuery.ajax({
            url: 'https://postcode.tech/api/v1/postcode/full?postcode='+postcode+'&number='+houseNumber,
            type: "GET",
            beforeSend: function(xhr){
                xhr.setRequestHeader('Authorization', 'Bearer 1146de2d-8268-4a56-97e8-112764eb4feb');
            },
            success: function(res) {
                jQuery("#billing_address_1").val(res.street);
                jQuery("#billing_city").val(res.city);
            },
            complete: function(){
            },
            error: function(err) {
                //if(err.responseJSON.errors){
                    //jQuery("#billing_address_1").val('');
                    //jQuery("#billing_city").val('');
                //}
            }
        });
    }
</script>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
