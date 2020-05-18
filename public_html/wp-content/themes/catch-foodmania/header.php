<?php
/**
* The header for our theme
*
* This is the template that displays all of the <head> section and everything up until <div id="content">
*
* @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
*
* @package Catch_Foodmania
*/

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php do_action( 'wp_body_open' );  ?>
	
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'catch-foodmania' ); ?></a>

		<header id="masthead" class="site-header">
			<div class="site-header-main">
				<?php get_template_part( 'template-parts/header/site', 'branding' ); ?>
				
				
				<div class="leyton-business-hours">
					<div class="monday">Mon: 5pm–9pm</div>
					<div class="tue-sun">Tue – Sun: 10am – 9pm</div>
				</div>
				
				<div class="leyton-delivery-pickup-hours">
					<div class="delivery-time">Delivery: 5pm – 9pm (7 days) Min. $25 order + $5 delivery fee</div>
					<div class="pickup-time">Pick up: 11am – 9pm (except Monday)</div>
				</div>
				
				
				<div class="nav-search-wrap">

					<?php get_template_part( 'template-parts/header/site', 'navigation' ); ?>

				</div>

			</div> <!-- .site-header-main -->
		</header><!-- #masthead -->

		<div class="below-site-header">

			<div class="site-overlay"><span class="screen-reader-text"><?php esc_html_e( 'Site Overlay', 'catch-foodmania' ); ?></span></div>

			<?php catch_foodmania_sections(); ?>

			<div id="content" class="site-content">
				<div class="wrapper">
