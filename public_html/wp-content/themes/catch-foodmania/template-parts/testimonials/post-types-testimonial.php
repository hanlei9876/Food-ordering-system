<?php
/**
 * The template for displaying testimonial items
 *
 * @package Catch_Foodmania
 */
?>

<?php
$number = get_theme_mod( 'catch_foodmania_testimonial_number', 4 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$args = array(
	'orderby'             => 'post__in',
	'ignore_sticky_posts' => 1, // ignore sticky posts
	'post_type'           => 'jetpack-testimonial',
);

$post_list  = array();// list of valid post/page ids

$no_of_post = 0; // for number of posts

for ( $i = 1; $i <= $number; $i++ ) {
	$post_id =  get_theme_mod( 'catch_foodmania_testimonial_cpt_' . $i );

	if ( $post_id && '' !== $post_id ) {
		// Polylang Support.
		if ( class_exists( 'Polylang' ) ) {
			$post_id = pll_get_post( $post_id, pll_current_language() );
		}

		$post_list = array_merge( $post_list, array( $post_id ) );

		$no_of_post++;
	}
}

$args['post__in'] = $post_list;

if ( 0 === $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;
$loop = new WP_Query( $args );

if ( $loop -> have_posts() ) :
	?>
	<div class="section-content-wrap">
		<div class="cycle-slideshow"
		    data-cycle-log="false"
		    data-cycle-pause-on-hover="true"
		    data-cycle-swipe="true"
		    data-cycle-auto-height=container
			data-cycle-speed="1000"
			data-cycle-timeout="4000"
			data-cycle-loader=false
			data-cycle-prev=".cycle-prev"
			data-cycle-next=".cycle-next"
			data-cycle-slides=".testimonial-slider-wrap"
			data-cycle-pager="#testimonial-pager-template"
    		data-cycle-pager-template=""
			>
			<?php

			$thumbnail = array();

			while ( $loop -> have_posts() ) :
				$loop -> the_post();

				if ( $media_id = get_post_meta( get_the_ID(), 'ect-alt-featured-image', true ) ) {
					// Get alternate thumbnail from CPT meta.
					$thumbnail[] = wp_get_attachment_image_url( $media_id, 'catch-foodmania-testimonial-thumb' );
				} elseif ( has_post_thumbnail() ) {
					$thumbnail[] = get_the_post_thumbnail_url( get_the_ID(), 'catch-foodmania-testimonial-thumb' );
				}

				get_template_part( 'template-parts/testimonials/content', 'testimonial' );
			endwhile;
			?>
		</div><!-- .cycle-slideshow -->

		<!-- prev/next links -->
		<div class="cycle-prev fa fa-angle-left" aria-label="<?php esc_attr_e( 'Previous', 'catch-foodmania' ); ?>" aria-hidden="true"><span class="screen-reader-text"><?php esc_html_e( 'Previous Slide', 'catch-foodmania' ); ?></span></div>
		<div class="cycle-next fa fa-angle-right" aria-label="<?php esc_attr_e( 'Next', 'catch-foodmania' ); ?>" aria-hidden="true"><span class="screen-reader-text"><?php esc_html_e( 'Next Slide', 'catch-foodmania' ); ?></span></div>

		<div id="testimonial-pager-template" class="cycle-pager external">
			<?php
			foreach ( $thumbnail as $thumb ) {
				echo '<a href="#"><img src="' . esc_url( $thumb ) . '"></a>';
			}
		    ?>
		</div>
	</div><!-- .section-content-wrap -->
	<?php
	wp_reset_postdata();
endif;
