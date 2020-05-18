<?php
/**
 * The template for displaying services posts on the front page
 *
 * @package Catch_Foodmania
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="hentry-inner">
		<div class="post-thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php

				// Default value if there is no first image
				$image = '<img class="wp-post-image" src="' . trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-70x70.jpg" >';

				if ( $media_id = get_post_meta( $post->ID, 'ect-alt-featured-image', true ) ) {
					$title_attribute = the_title_attribute( 'echo=0' );
					// Get alternate thumbnail from CPT meta.
					echo wp_get_attachment_image( $media_id, 'catch-foodmania-service', false,  array( 'title' => $title_attribute, 'alt' => $title_attribute ) );
				} elseif ( has_post_thumbnail() ) {
					the_post_thumbnail( 'catch-foodmania-service' );
				} else {
					// Get the first image in page, returns false if there is no image.
					$first_image = catch_foodmania_get_first_image( get_the_ID(), 'catch-foodmania-service', '' );

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

			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-content -->
		</div><!-- .entry-container -->
	</div> <!-- .hentry-inner -->
</article> <!-- .article -->
