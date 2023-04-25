<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Martfury
 */
?>
    <footer id="colophon" class="site-footer myheadertop">
        <nav class="footer-layout footer-layout-1 footer-light">
        <div class="container">
        <div class="footer-content" id="header-top-mb">
            <div class="footer-info headertoppp owl-carousel">
				<div>
						<div class="info-item"> 
							<div class="info-thumb"><a href="https://www.somashome.be/bestellen/"><i class="icon-truck"></i></a></div> 
							<div class="info-content"> <h3>Gratis verzending</h3> </div> 
						</div> 
						<div class="info-item-sep"></div> </div>
				<div>
						<div class="info-item"> 
							<div class="info-thumb"><a href="https://www.somashome.be/betalen/"><i class="icon-credit-card"></i></a></div> 
							<div class="info-content"> <h3>Gespreid betalen, met 0% rente mogelijk</h3> </div> 
						</div> 
						<div class="info-item-sep"></div> </div>
				<div>
						<div class="info-item"> 
							<div class="info-thumb"><a href="https://www.somashome.be/garantie-reparatie/"><i class="icon-shield-check"></i></a></div> 
							<div class="info-content"> <h3>2 jaar garantie</h3></div>
						</div>
						<div class="info-item-sep"></div> </div>
				<div>
						<div class="info-item" id="header-border"> 
							<div class="info-thumb"><a href="https://www.somashome.be/retourneren/"><i class="icon-undo2"></i></a></div> 
							<div class="info-content"> <h3>30 dagen bedenktijd</h3></div> 
						</div>
						<div class="info-item-sep"></div></div>
				</div>        
			</div>        
	</div>    
<!--     </div> -->
</nav>    
</footer>

<?php do_action( 'martfury_before_site_content_close' ); ?>
</div><!-- #content -->
<?php do_action( 'martfury_before_footer' ) ?>
<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'footer' ) ) {
	?>
    <footer id="colophon" class="site-footer">
		<?php do_action( 'martfury_footer' ) ?>
    </footer><!-- #colophon -->
	<?php do_action( 'martfury_after_footer' ) ?>
<?php } ?>
</div><!-- #page -->

<?php wp_footer(); ?>

<script>
	jQuery(document).ready(function(){
  jQuery(".owl-carousel").owlCarousel({
	loop:true,
    margin:10,
	autoplay: true,
	autoPlay : 2000,
    responsiveClass:true,
    responsive:{
        0:{
            items:1,
            nav:true,
			loop:true
        },
        600:{
            items:1,
            nav:false,
			loop:true
        },
        1000:{
            items:4,
            nav:true,
            loop:false
        }
	  }});
	});
	
	jQuery(document).ready(function () {
    var navListItems = jQuery('div.setup-panel div a'),
        allWells = jQuery('.setup-content'),
        allNextBtn = jQuery('.nextBtn'),
        allPrevBtn = jQuery('.prevBtn');

    allWells.hide();

    navListItems.click(function (e) {
        e.preventDefault();
        var jQuerytarget = jQuery(jQuery(this).attr('href')),
            jQueryitem = jQuery(this);

        if (!jQueryitem.hasClass('disabled')) {
            navListItems.removeClass('btn-indigo').addClass('btn-default');
            jQueryitem.addClass('btn-indigo');
            allWells.hide();
            jQuerytarget.show();
            jQuerytarget.find('input:eq(0)').focus();
        }
    });

    allPrevBtn.click(function(){
        var curStep = jQuery(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            prevStepSteps = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().prev().children("a");

            prevStepSteps.removeAttr('disabled').trigger('click');
    });

    allNextBtn.click(function(){
        var curStep = jQuery(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = jQuery('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url']"),
            isValid = true;

        jQuery(".form-group").removeClass("has-error");
        for(var i=0; i< curInputs.length; i++){
            if (!curInputs[i].validity.valid){
                isValid = false;
                jQuery(curInputs[i]).closest(".form-group").addClass("has-error");
            }
        }

        if (isValid)
            nextStepWizard.removeAttr('disabled').trigger('click');
    });

    jQuery('div.setup-panel div a.btn-indigo').trigger('click');
});
jQuery(".products-cats-menu .cats-menu-title span.text").text("Winkel Per Categorieën");
jQuery(".wc-proceed-to-checkout a.checkout-button").text("Afrekenen");
jQuery(".woocommerce-billing-fields h3").text("Factuurgegevens");
jQuery("#ship-to-different-address .woocommerce-form__label span").text("Op een ander adres bezorgen?");
jQuery(".shop_table thead tr th.product-total").text("Totale prijs");
jQuery(".shop_table tfoot tr.cart-subtotal th").text("Totale prijs");
jQuery("form.fbt-cart button.mf_add_to_cart_button").text("Voeg toe aan winkelmandje");
jQuery("a.btn-add-to-wishlist span").text("Toevoegen aan verlanglijst");
jQuery("div.price-box span.label").text("Totale prijs:");
jQuery("span.p-title strong").text("Dit item");
</script>
</body>
</html>
