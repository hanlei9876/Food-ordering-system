<?php
/**
 * The template used for displaying testimonial on front page
 *
 * @package Catch_Foodmania
 */
?>
<div class="testimonial-slider-wrap">
	<div class="hentry-wrap">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<div class="testimonial-thumbnail" style="background-image: url( <?php the_post_thumbnail_url( 'catch-foodmania-testimonial' ); ?> );">
					<a class="screen-reader-text" href="<?php the_permalink(); ?>" ><?php the_title(); ?></a>
				</div>
			<?php endif; ?>

			<div class="entry-container">

				<div class="entry-content">
					<?php the_content(); ?>
				</div>
				<?php $position = get_post_meta( get_the_id(), 'ect_testimonial_position', true ); ?>

				<?php if ( get_theme_mod( 'catch_foodmania_testimonial_enable_title', 1 ) || $position ) : ?>
					<header class="entry-header">
						<?php
						if ( get_theme_mod( 'catch_foodmania_testimonial_enable_title', 1 ) ) {
							the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2>' );
						}

						if ( $position ) {
						echo '<div class="entry-meta"><span class="position">' . esc_html( $position ) . '</span></div>';
						}
						?>
					</header>
				<?php endif;?>
			</div><!-- .entry-container -->
		</article>
	</div><!-- .hentry-wrap -->
</div><!-- .testimonial-slider-wrap -->
