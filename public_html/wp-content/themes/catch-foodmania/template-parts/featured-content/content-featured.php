<?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package Catch_Foodmania
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="hentry-inner">
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'catch-foodmania-featured-content' );
				}
				else {
					$image = '<img src="' . trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x444.jpg"/>';

					// Get the first image in page, returns false if there is no image.
					$first_image = catch_foodmania_get_first_image( $post->ID, 'catch-foodmania-featured-content', '' );

					// Set value of image as first image if there is an image present in the page.
					if ( $first_image ) {
						$image = $first_image;
					}

					echo $image;
				}
				?>
			</a>
		</div>

		<div class="entry-container">
			<header class="entry-header">
				<div class="entry-category">
					<?php catch_foodmania_entry_category(); ?>
				</div>

				<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h2>' ); ?>

				<div class="entry-meta">
					<?php catch_foodmania_posted_on(); ?>
				</div><!-- .entry-meta -->

			</header>

			<?php echo '<div class="entry-summary"><p>' . get_the_excerpt() . '</p></div><!-- .entry-summary -->'; ?>
		</div><!-- .entry-container -->
	</div> <!-- .hentry-inner -->
</article>
