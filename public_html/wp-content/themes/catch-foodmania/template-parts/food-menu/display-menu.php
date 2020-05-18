<?php
/**
 * The template for displaying food_menu items
 *
 * @package Catch_Foodmania
 */
?>

<?php
$enable = get_theme_mod( 'catch_foodmania_food_menu_option', 'disabled' );

if ( ! catch_foodmania_check_section( $enable ) ) {
	// Bail if featured content is disabled
	return;
}

$type        = get_theme_mod( 'catch_foodmania_food_menu_type', 'demo' );
$headline    = get_theme_mod( 'catch_foodmania_food_menu_headline', esc_html__( 'Our Menu', 'catch-foodmania' ) );
$subheadline = get_theme_mod( 'catch_foodmania_food_menu_subheadline' );
$background  = get_theme_mod( 'catch_foodmania_food_menu_bg_image', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/food-menu-bg.jpg' );

$classes[] = 'menu-content-wrapper';
$classes[] = 'section';
$classes[] = 'cpt';

if ( $background ) {
	$classes[] = 'has-background-image';
}


?>

<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div class="wrapper">
		<?php if ( $headline || $subheadline ) : ?>
			<div class="section-heading-wrapper">
			<?php if ( $headline ) : ?>
				<div class="section-title-wrapper">
					<h2 class="section-title"><?php echo wp_kses_post( $headline ); ?></h2>
				</div>
			<?php endif; ?>

			<?php if ( $subheadline ) : ?>
				<div class="section-description">
					<?php
					$subheadline = apply_filters( 'the_content', $subheadline );
					echo str_replace( ']]>', ']]&gt;', $subheadline );
					?>
				</div><!-- .section-description -->
			<?php endif; ?>
			</div><!-- .section-heading-wrap -->
		<?php endif; ?>

		<div class="section-content-wrapper">
			<?php get_template_part( 'template-parts/food-menu/cat-cpt', 'menu' ); ?>
		</div><!-- .section-content-wrap -->
	</div><!-- .wrapper -->
</div><!-- .menu-content-wrapper -->
