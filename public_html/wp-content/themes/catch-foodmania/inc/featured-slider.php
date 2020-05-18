<?php
/**
 * The template for displaying the Slider
 *
 * @package Catch_Foodmania
 */

if ( ! function_exists( 'catch_foodmania_featured_slider' ) ) :
	/**
	 * Add slider.
	 *
	 * @uses action hook catch_foodmania_before_content.
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_featured_slider() {
		if ( catch_foodmania_is_slider_displayed() ) {
			$type = get_theme_mod( 'catch_foodmania_slider_type', 'category' );

			$output = '
				<div class="slider-content-wrapper section">
					<div class="wrapper">
						<div class="section-content-wrap">
							<div class="cycle-slideshow"
							    data-cycle-log="false"
							    data-cycle-pause-on-hover="true"
							    data-cycle-swipe="true"
							    data-cycle-auto-height=container
								data-cycle-pager="#featured-slider-pager"
								data-cycle-prev="#featured-slider-prev"
        						data-cycle-next="#featured-slider-next"
								data-cycle-slides="> .post-slide"
								>

								<div class="controllers">
									<!-- prev/next links -->
									<div id="featured-slider-prev" class="cycle-prev fa fa-angle-left" aria-label="Previous" aria-hidden="true"><span class="screen-reader-text">' . esc_html__( 'Previous Slide', 'catch-foodmania' ) . '</span></div>

									<!-- empty element for pager links -->
									<div id="featured-slider-pager" class="cycle-pager"></div>

									<div id="featured-slider-next" class="cycle-next fa fa-angle-right" aria-label="Next" aria-hidden="true"><span class="screen-reader-text">' . esc_html__( 'Next Slide', 'catch-foodmania' ) . '</span></div>

								</div><!-- .controllers -->';
				$output .= catch_foodmania_post_page_category_slider();

				$output .= '
							</div><!-- .cycle-slideshow -->
						</div><!-- .section-content-wrap -->
					</div><!-- .wrapper -->';

			$output .= '
				</div><!-- .slider-content-wrapper -->';

			echo $output;
		} // End if().
	}
	endif;
add_action( 'catch_foodmania_slider', 'catch_foodmania_featured_slider', 10 );

if ( ! function_exists( 'catch_foodmania_post_page_category_slider' ) ) :
	/**
	 * This function to display featured posts/page/category slider
	 *
	 * @param $options: catch_foodmania_theme_options from customizer
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_post_page_category_slider() {
		$quantity     = get_theme_mod( 'catch_foodmania_slider_number', 4 );
		$no_of_post   = 0; // for number of posts
		$post_list    = array();// list of valid post/page ids
		$output       = '';

		$args = array(
			'post_type'           => 'any',
			'orderby'             => 'post__in',
			'ignore_sticky_posts' => 1, // ignore sticky posts
		);

		for ( $i = 1; $i <= $quantity; $i++ ) {
			$post_id = get_theme_mod( 'catch_foodmania_slider_page_' . $i );

			if ( $post_id && '' !== $post_id ) {
				$post_list = array_merge( $post_list, array( $post_id ) );

				$no_of_post++;
			}
		}

		$args['post__in'] = $post_list;

		if ( ! $no_of_post ) {
			return;
		}

		$args['posts_per_page'] = $no_of_post;

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) :
			$loop->the_post();

			$title_attribute = the_title_attribute( 'echo=0' );

			$content_alignment = get_theme_mod( 'catch_foodmania_content_align_' . ( $loop->current_post + 1 ), 'content-aligned-left' );


			if ( 0 === $loop->current_post ) {
				$classes = 'post post-' . esc_attr( get_the_ID() ) . ' hentry slides displayblock ' . $content_alignment;

			} else {
				$classes = 'post post-' . esc_attr( get_the_ID() ) . ' hentry slides displaynone ' . $content_alignment;
			}

			// Default value if there is no featurd image or first image.
			$image_url = trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-1920x822.jpg';

			if ( has_post_thumbnail() ) {
				$image_url = get_the_post_thumbnail_url( get_the_ID(), 'catch-foodmania-slider' );
			} else {
				// Get the first image in page, returns false if there is no image.
				$first_image_url = catch_foodmania_get_first_image( get_the_ID(), 'catch-foodmania-slider', '', true );

				// Set value of image as first image if there is an image present in the page.
				if ( $first_image_url ) {
					$image_url = $first_image_url;
				}
			}

			$more_tag_text = get_theme_mod( 'catch_foodmania_excerpt_more_text',  esc_html__( 'Continue reading', 'catch-foodmania' ) );

			$output .= '
			<div class="post-slide">
				<article class="' . $classes . '">';

					$output .= '
					<div class="slider-image">
						<a href="' . esc_url( get_permalink() ) . '" title="' . $title_attribute . '">
								<img src="' . esc_url( $image_url ) . '" class="wp-post-image" alt="' . $title_attribute . '">
							</a>
					</div><!-- .slider-image -->
					<div class="entry-container"><div class="entry-container-wrap">';

				if ( 'post' === get_post_type() ) {
					$output .= '<div class="entry-meta">' . catch_foodmania_entry_category( false ) . '</div>';
				}

				$output .= the_title( '<header class="entry-header"><h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '">', '</a></h2></header>', false );

				$output .= '<div class="entry-summary"><p>' . get_the_excerpt() . '</p></div><!-- .entry-summary -->';

						$output .= '
					</div></div><!-- .entry-container -->
				</article><!-- .slides -->
			</div><!-- .post-slide -->';
		endwhile;

		wp_reset_postdata();

		return $output;
	}
endif; // catch_foodmania_post_page_category_slider.

if ( ! function_exists( 'catch_foodmania_image_slider' ) ) :
	/**
	 * This function to display featured posts slider
	 *
	 * @get the data value from theme options
	 * @displays on the index
	 *
	 * @usage Featured Image, Title and Excerpt of Post
	 *
	 */
	function catch_foodmania_image_slider() {
		$quantity = get_theme_mod( 'catch_foodmania_slider_number', 4 );

		$output = '';

		for ( $i = 1; $i <= $quantity; $i++ ) {
			$image = get_theme_mod( 'catch_foodmania_slider_image_' . $i );
			$content_alignment = get_theme_mod( 'catch_foodmania_content_align_' . $i, 'content-aligned-left' );

			// Check Image Not Empty to add in the slides.
			if ( $image ) {
				$imagetitle = get_theme_mod( 'catch_foodmania_featured_title_' . $i ) ? get_theme_mod( 'catch_foodmania_featured_title_' . $i ) : '';

				$title     = '';
				$content   ='';
				$link      = get_theme_mod( 'catch_foodmania_featured_link_' . $i );
				$target    = '_self';
				$more_link = '';

				if ( $link ) {
					// Checking Link Target.
					$target = get_theme_mod( 'catch_foodmania_featured_target_' . $i ) ? '_blank' : '_self';

					$more_tag_text = get_theme_mod( 'catch_foodmania_excerpt_more_text',  esc_html__( 'Continue reading', 'catch-foodmania' ) );

					$more_link = '<span class="more-button"><a href="' . esc_url( $link ) . '" class="more-link">' . wp_kses_data( $more_tag_text ) . '</a></span>';
				}

				$subtitle = get_theme_mod( 'catch_foodmania_featured_sub_title_' . $i ) ? '<span class="sub-title">' . wp_kses_post( get_theme_mod( 'catch_foodmania_featured_sub_title_' . $i ) ) . '</span><!-- .sub-title -->' : '';

				$title = '<header class="entry-header"><h2 class="entry-title">' . $subtitle . '<a title="' . esc_attr( $imagetitle ) . '" href="' . esc_url( $link ) . '" target="' . $target . '">' . esc_html( $imagetitle ) . '</a></h2></header>';

				$subtitle = get_theme_mod( 'catch_foodmania_featured_sub_title_' . $i ) ? '<span class="sub-title">' . wp_kses_post( get_theme_mod( 'catch_foodmania_featured_sub_title_' . $i ) ) . '</span><!-- .sub-title -->' : '';

				$content = get_theme_mod( 'catch_foodmania_featured_content_' . $i ) ? '<div class="entry-summary"><p>' . wp_kses_post( get_theme_mod( 'catch_foodmania_featured_content_' . $i ) ) . $more_link . '</p></div><!-- .entry-summary -->' : '';

				$contentopening = '';
				$contentclosing = '';

				// Content Opening and Closing.
				if ( $title || $subtitle || $content ) {
					$contentopening = '<div class="entry-container"><div class="entry-container-wrap">';
					$contentclosing = '</div></div><!-- .entry-container -->';
				}

				// Adding in Classes for Display block and none.
				$classes = ( 1 === $i ) ? 'displayblock' : 'displaynone';

				$classes .=  'image-slides hentry slider-image images-' . $i . ' slides ' . $content_alignment;

				$output .= '
				<div class="post-slide">
					<article class="image-slides hentry images-' . esc_attr( $i ) . ' slides  ' . $classes . '">
						<div class="slider-image">
							<a href="' . esc_url( $link ) . '" title="' . esc_attr( $imagetitle ) . '" target="' . $target . '">
								<img src="' . esc_url( $image ) . '" class="wp-post-image" alt="' . esc_attr( $imagetitle ) . '">
							</a>
						</div>

						' . $contentopening .  $title .$content . $contentclosing . '
					</article><!-- .slides -->
				</div><!-- .post-slide -->';
			} // End if().
		} // End for().
		return $output;
	}
endif; // catch_foodmania_image_slider.

if ( ! function_exists( 'catch_foodmania_is_slider_displayed' ) ) :
	/**
	 * Return true if slider image is displayed
	 *
	 */
	function catch_foodmania_is_slider_displayed() {
		$enable_slider = get_theme_mod( 'catch_foodmania_slider_option', 'disabled' );

		return catch_foodmania_check_section( $enable_slider );
	}
endif; // catch_foodmania_is_slider_displayed.
