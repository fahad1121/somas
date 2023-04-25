<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Martfury
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
	<footer id="colophon" class="site-footer myheadertop">
        <nav class="footer-layout footer-layout-1 footer-light">
			<div class="container">
			<div class="footer-content" id="header-top-mb">
				<div class="footer-info headertoppp owl-carousel">
					<div>
							<div class="info-item"> 
								<div class="info-thumb"><a href="https://www.somashome.nl/bestellen/"><i class="icon-truck"></i></a></div> 
								<div class="info-content"> <h3>Gratis verzending</h3> </div> 
							</div> 
							<div class="info-item-sep"></div> </div>
					<div>
							<div class="info-item"> 
								<div class="info-thumb"><a href="https://www.somashome.nl/betalen/"><i class="icon-credit-card"></i></a></div> 
								<div class="info-content"> <h3>Gespreid betalen, met 0% rente mogelijk</h3> </div> 
							</div> 
							<div class="info-item-sep"></div> </div>
					<div>
							<div class="info-item"> 
								<div class="info-thumb"><a href="https://www.somashome.nl/garantie-reparatie/"><i class="icon-shield-check"></i></a></div> 
								<div class="info-content"> <h3>2 jaar garantie</h3></div>
							</div>
							<div class="info-item-sep"></div> </div>
					<div>
							<div class="info-item" id="header-border"> 
								<div class="info-thumb"><a href="https://www.somashome.nl/retourneren/"><i class="icon-undo2"></i></a></div>  
								<div class="info-content"> <h3>30 dagen bedenktijd</h3></div> 
							</div>
							<div class="info-item-sep"></div></div>
					</div>        
				</div>        
			</div>    
			<!--     </div> -->
		</nav>    
	</footer>
<?php martfury_body_open(); ?>

<div id="page" class="hfeed site">
	<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
		?>
		<?php do_action( 'martfury_before_header' ); ?>
        <header id="site-header" class="site-header <?php martfury_header_class(); ?>">
			<?php do_action( 'martfury_header' ); ?>
        </header>
	<?php } ?>
	<?php do_action( 'martfury_after_header' ); ?>

    <div id="content" class="site-content">
		<?php do_action( 'martfury_after_site_content_open' ); ?>
