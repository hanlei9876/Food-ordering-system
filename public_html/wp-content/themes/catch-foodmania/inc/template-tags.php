<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Catch_Foodmania
 */

if ( ! function_exists( 'catch_foodmania_posted_on' ) ) :
	/**
	 * Prints HTML with meta information for the current post-date/time and author.
	 */
	function catch_foodmania_posted_on() {
		$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
		if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
			$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
		}

		$time_string = sprintf( $time_string,
			esc_attr( get_the_date( 'c' ) ),
			esc_html( get_the_date() ),
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		$posted_on = sprintf(
			/* translators: %s: post date */
			__( '<span class="date-label"> on </span>%s', 'catch-foodmania' ),
			'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
		);

		// Get the author name; wrap it in a link.
		$byline = sprintf(
			/* translators: %s: post author */
			__( '<span class="author-label screen-reader-text">By </span>%s', 'catch-foodmania' ),
			'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
		);

		echo '<span class="byline"> ' . $byline . '</span><span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

	}
endif;

if ( ! function_exists( 'catch_foodmania_entry_category' ) ) :
	/**
	 * Prints HTML with meta information for the category.
	 */
	function catch_foodmania_entry_category( $echo = true ) {
		$output = '';

		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( ' ' );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				$output = sprintf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'catch-foodmania' ) ),
					$categories_list
				); // WPCS: XSS OK.
			}
		}

		if ( 'ect-service' === get_post_type() || 'featured-content' === get_post_type() || 'jetpack-portfolio' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$term_list = get_the_term_list( get_the_ID(), get_post_type() . '-type' );
			if ( $term_list ) {
				/* translators: 1: list of categories. */
				$output = sprintf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'catch-foodmania' ) ),
					$term_list
				); // WPCS: XSS OK.
			}
		}

		if ( $echo ) {
			echo $output;
		} else {
			return $output;
		}
	}
endif;

if ( ! function_exists( 'catch_foodmania_entry_footer' ) ) :
	/**
	 * Prints HTML with meta information for the categories, tags and comments.
	 */
	function catch_foodmania_entry_footer() {
		// Hide category and tag text for pages.
		if ( 'post' === get_post_type() ) {
			/* translators: used between list items, there is a space after the comma */
			$categories_list = get_the_category_list( ' ' );
			if ( $categories_list ) {
				/* translators: 1: list of categories. */
				printf( '<span class="cat-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="cat-text screen-reader-text">Categories</span>', 'Used before category names.', 'catch-foodmania' ) ),
					$categories_list
				); // WPCS: XSS OK.
			}

			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list();
			if ( $tags_list ) {
				/* translators: 1: list of tags. */
				printf( '<span class="tags-links">%1$s%2$s</span>',
					sprintf( _x( '<span class="tags-text screen-reader-text">Tags</span>', 'Used before tag names.', 'catch-foodmania' ) ),
					$tags_list
				); // WPCS: XSS OK.
			}
		}

		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			echo '<span class="comments-link">';
			comments_popup_link(
				sprintf(
					wp_kses(
						/* translators: %s: post title */
						__( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'catch-foodmania' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				)
			);
			echo '</span>';
		}

		edit_post_link(
			sprintf(
				wp_kses(
					/* translators: %s: Name of current post. Only visible to screen readers */
					__( 'Edit <span class="screen-reader-text">%s</span>', 'catch-foodmania' ),
					array(
						'span' => array(
							'class' => array(),
						),
					)
				),
				get_the_title()
			),
			'<span class="edit-link">',
			'</span>'
		);
	}
endif;

if ( ! function_exists( 'catch_foodmania_author_bio' ) ) :
	/**
	 * Prints HTML with meta information for the author bio.
	 */
	function catch_foodmania_author_bio() {
		if ( '' !== get_the_author_meta( 'description' ) ) {
			get_template_part( 'template-parts/biography' );
		}
	}
endif;

if ( ! function_exists( 'catch_foodmania_header_title' ) ) :
	/**
	 * Display Header Media Title
	 */
	function catch_foodmania_header_title() {
		if ( is_front_page() ) {
			$subtitle = get_theme_mod( 'catch_foodmania_header_media_subtitle' ) ? '<span class="sub-title">' . wp_kses_post( get_theme_mod( 'catch_foodmania_header_media_subtitle' ) ) . '</span><!-- .sub-title -->' : '';

			echo $subtitle . wp_kses_post( get_theme_mod( 'catch_foodmania_header_media_title' ) );
		} elseif ( is_singular() ) {
			the_title();
		} elseif ( is_404() ) {
			esc_html_e( 'Oops! That page can&rsquo;t be found.', 'catch-foodmania' );
		} elseif ( is_search() ) {
			/* translators: %s: search query. */
			printf( esc_html__( 'Search Results for: %s', 'catch-foodmania' ), '<span>' . get_search_query() . '</span>' );
		} else {
			the_archive_title();
		}
	}
endif;

if ( ! function_exists( 'catch_foodmania_header_text' ) ) :
	/**
	 * Display Header Media Text
	 */
	function catch_foodmania_header_text() {
		if ( is_front_page() ) {
			$content = get_theme_mod( 'catch_foodmania_header_media_text' );

			if ( $header_media_url = get_theme_mod( 'catch_foodmania_header_media_url', '' ) ) {
				$target = get_theme_mod( 'catch_foodmania_header_url_target' ) ? '_blank' : '_self';

				$content .= '<span class="more-button"><a href="'. esc_url( $header_media_url ) . '" target="' . $target . '" class="more-link">' .esc_html( get_theme_mod( 'catch_foodmania_header_media_url_text' ) ) . '<span class="screen-reader-text">' .wp_kses_post( get_theme_mod( 'catch_foodmania_header_media_title' ) ) . '</span></a></span>';
			}

			$content = apply_filters( 'the_content', $content );
			$content = str_replace( ']]>', ']]&gt;', $content );

			echo '<div class="entry-summary">' . wp_kses_post( $content ) . '</div>';
		} elseif ( is_404() ) {
			esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'catch-foodmania' );
		} else {
			the_archive_description();
		}
	}
endif;


if ( ! function_exists( 'catch_foodmania_single_image' ) ) :
	/**
	 * Display Single Page/Post Image
	 */
	function catch_foodmania_single_image() {
		$featured_image = get_theme_mod( 'catch_foodmania_single_layout', 'disabled' );

		if ( ! has_post_thumbnail() || 'disabled' == $featured_image ) {
			echo '<!-- Page/Post Single Image Disabled or No Image set in Post Thumbnail -->';
			return false;
		}
		else {
			?>
			<div class="post-thumbnail <?php echo esc_attr( $featured_image ); ?>">
                <?php the_post_thumbnail( $featured_image ); ?>
	        </div>
	   	<?php
		}
	}
endif;