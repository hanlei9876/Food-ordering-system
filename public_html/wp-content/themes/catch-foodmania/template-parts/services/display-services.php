<?php
/**
 * The template for displaying services content
 *
 * @package Catch_Foodmania
 */
?>

<?php
$enable_content = get_theme_mod( 'catch_foodmania_service_option', 'disabled' );

if ( ! catch_foodmania_check_section( $enable_content ) ) {
	// Bail if services content is disabled.
	return;
}

$services_posts = catch_foodmania_get_services_posts();

if ( empty( $services_posts ) ) {
	return;
}

$title    = get_option( 'ect_service_title', esc_html__( 'Services', 'catch-foodmania' ) );
$subtitle = get_option( 'ect_service_content' );
?>

<div id="services-section" class="services-section section special">
	<div class="wrapper">
		<?php if ( '' !== $title || $subtitle ) : ?>
			<div class="section-heading-wrapper">
				<?php if ( '' !== $title ) : ?>
					<div class="section-title-wrapper">
						<h2 class="section-title"><?php echo wp_kses_post( $title ); ?></h2>
					</div><!-- .page-title-wrapper -->
				<?php endif; ?>

				<?php if ( $subtitle ) : ?>
					<div class="section-description">
						<?php
						$subtitle = apply_filters( 'the_content', $subtitle );
						echo str_replace( ']]>', ']]&gt;', $subtitle );
						?>
					</div><!-- .section-description -->
				<?php endif; ?>
			</div><!-- .section-heading-wrapper -->
		<?php endif; ?>

		<?php

		$classes[] = 'section-content-wrapper';
		?>

		<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php
				// Count number of posts for image display.
				$count = count( $services_posts );

				// Get post index to display image exactly in the middle of the number of posts.
				$image_display = absint( $count / 2 ) - 1;

				$i = 0;
				foreach ( $services_posts as $post ) {
					setup_postdata( $post );

					// Include the services content template.
					get_template_part( 'template-parts/services/content', 'services' );

					// Display Main Image.
					if ( $i === $image_display ) {
						get_template_part( 'template-parts/services/main', 'image' );
					}

					$i++;
				}

				wp_reset_postdata();
			?>

		</div><!-- .services-wrapper -->
	</div><!-- .wrapper -->
</div><!-- #services-section -->
