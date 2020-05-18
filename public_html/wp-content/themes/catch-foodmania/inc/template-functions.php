<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Catch_Foodmania
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function catch_foodmania_body_classes( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	$classes[] = 'navigation-classic';

	// Adds a class with respect to layout selected.
	$layout  = catch_foodmania_get_theme_layout();
	$sidebar = catch_foodmania_get_sidebar_id();

	if ( 'no-sidebar-full-width' === $layout ) {
		$classes[] = 'no-sidebar full-width-layout';
	} if ( 'right-sidebar' === $layout ) {
		if ( '' !== $sidebar ) {
			$classes[] = 'two-columns-layout content-left';
		}
	}

	$classes[] = 'fluid-layout';

	$header_media_title    = get_theme_mod( 'catch_foodmania_header_media_title' );
	$header_media_subtitle = get_theme_mod( 'catch_foodmania_header_media_subtitle' );
	$header_media_text     = get_theme_mod( 'catch_foodmania_header_media_text' );
	$header_media_url      = get_theme_mod( 'catch_foodmania_header_media_url', '' );
	$header_media_url_text = get_theme_mod( 'catch_foodmania_header_media_url_text' );

	$header_image = catch_foodmania_featured_overall_image();

	if ( '' == $header_image ) {
		$classes[] = 'no-header-media-image';
	}

	$header_text_enabled = catch_foodmania_has_header_media_text();

	if ( ! $header_text_enabled ) {
		$classes[] = 'no-header-media-text';
	}

	$enable_slider = catch_foodmania_check_section( get_theme_mod( 'catch_foodmania_slider_option', 'disabled' ) );

	if ( ! $enable_slider ) {
		$classes[] = 'no-featured-slider';
	}

	if ( '' == $header_image && ! $header_text_enabled && ! $enable_slider ) {
		$classes[] = 'content-has-padding-top';
	}

	if ( $enable_slider || $header_image ) {
		$classes[] = 'absolute-header';
	}

	return $classes;
}
add_filter( 'body_class', 'catch_foodmania_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function catch_foodmania_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'catch_foodmania_pingback_header' );

/**
 * Adds testimonial background CSS
 */
function catch_foodmania_testimonial_bg_css() {
	$background = get_theme_mod( 'catch_foodmania_testimonial_bg_image', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/recent-posts-section-bg.png' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'catch_foodmania_testimonial_bg_position_x' );
		$position_y = get_theme_mod( 'catch_foodmania_testimonial_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'catch_foodmania_testimonial_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'catch_foodmania_testimonial_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'catch_foodmania_testimonial_bg_size', 'auto' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '.testimonials-content-wrapper { ' . $css . '}';
	}

	wp_add_inline_style( 'catch-foodmania-style', $css );
}
add_action( 'wp_enqueue_scripts', 'catch_foodmania_testimonial_bg_css', 11 );

/**
 * Adds testimonial background CSS
 */
function catch_foodmania_food_menu_bg_css() {
	$background = get_theme_mod( 'catch_foodmania_food_menu_bg_image', trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/food-menu-bg.png' );

	$css = '';

	if ( $background ) {
		$image = ' background-image: url("' . esc_url( $background ) . '");';

		// Background Position.
		$position_x = get_theme_mod( 'catch_foodmania_food_menu_bg_position_x' );
		$position_y = get_theme_mod( 'catch_foodmania_food_menu_bg_position_y' );

		if ( ! in_array( $position_x, array( 'left', 'center', 'right' ), true ) ) {
			$position_x = 'left';
		}

		if ( ! in_array( $position_y, array( 'top', 'center', 'bottom' ), true ) ) {
			$position_y = 'top';
		}

		$position = ' background-position: ' . esc_attr( $position_x ) . ' ' . esc_attr( $position_y ) . ';';

		// Background Repeat.
		$repeat = get_theme_mod( 'catch_foodmania_food_menu_bg_repeat', 'repeat' );

		$repeat = ' background-repeat: ' . esc_attr( $repeat ) . ';';

		// Background Scroll.
		$attachment = get_theme_mod( 'catch_foodmania_food_menu_bg_attachment', 1 );

		if ( $attachment ) {
			$attachment = 'scroll';
		} else {
			$attachment = 'fixed';
		}

		$attachment = ' background-attachment: ' . esc_attr( $attachment ) . ';';

		// Background Size.
		$size = get_theme_mod( 'catch_foodmania_food_menu_bg_size', 'cover' );

		$size =  ' background-size: ' . esc_attr( $size ) . ';';

		$css = $image . $position . $repeat . $attachment . $size;
	}


	if ( '' !== $css ) {
		$css = '.menu-content-wrapper { ' . $css . '}';
	}

	wp_add_inline_style( 'catch-foodmania-style', $css );
}
add_action( 'wp_enqueue_scripts', 'catch_foodmania_food_menu_bg_css', 11 );

/**
 * Remove first post from blog as it is already show via recent post template
 */
function catch_foodmania_alter_home( $query ) {
	if ( $query->is_home() && $query->is_main_query() ) {
		$cats = get_theme_mod( 'catch_foodmania_front_page_category' );

		if ( is_array( $cats ) && ! in_array( '0', $cats ) ) {
			$query->query_vars['category__in'] = $cats;
		}
	}
}
add_action( 'pre_get_posts', 'catch_foodmania_alter_home' );

/**
 * Function to add Scroll Up icon
 */
function catch_foodmania_scrollup() {
	$disable_scrollup = get_theme_mod( 'catch_foodmania_disable_scrollup' );

	if ( $disable_scrollup ) {
		return;
	}

	echo '
	<div class="scrollup">
		<a href="#masthead" id="scrollup" class="fa fa-sort-asc" aria-hidden="true"><span class="screen-reader-text">' . esc_html__( 'Scroll Up', 'catch-foodmania' ) . '</span></a>
	</div>' ;
}
add_action( 'wp_footer', 'catch_foodmania_scrollup', 1 );

if ( ! function_exists( 'catch_foodmania_content_nav' ) ) :
	/**
	 * Display navigation/pagination when applicable
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_content_nav() {
		global $wp_query;

		// Don't print empty markup in archives if there's only one page.
		if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) ) {
			return;
		}

		$pagination_type = get_theme_mod( 'catch_foodmania_pagination_type', 'default' );

		/**
		 * Check if navigation type is Jetpack Infinite Scroll and if it is enabled, else goto default pagination
		 * if it's active then disable pagination
		 */
		if ( ( 'infinite-scroll' === $pagination_type ) && class_exists( 'Jetpack' ) && Jetpack::is_module_active( 'infinite-scroll' ) ) {
			return false;
		}

		if ( 'numeric' === $pagination_type && function_exists( 'the_posts_pagination' ) ) {
			the_posts_pagination( array(
				'prev_text'          => esc_html__( 'Previous', 'catch-foodmania' ),
				'next_text'          => esc_html__( 'Next', 'catch-foodmania' ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . esc_html__( 'Page', 'catch-foodmania' ) . ' </span>',
			) );
		} else {
			the_posts_navigation();
		}
	}
endif; // catch_foodmania_content_nav

/**
 * Check if a section is enabled or not based on the $value parameter
 * @param  string $value Value of the section that is to be checked
 * @return boolean return true if section is enabled otherwise false
 */
function catch_foodmania_check_section( $value ) {
	global $wp_query;

	// Get Page ID outside Loop
	$page_id = absint( $wp_query->get_queried_object_id() );

	// Front page displays in Reading Settings
	$page_for_posts = absint( get_option( 'page_for_posts' ) );

	return ( 'entire-site' == $value  || ( ( is_front_page() || ( is_home() && $page_for_posts !== $page_id ) ) && 'homepage' == $value ) );
}

/**
 * Return the first image in a post. Works inside a loop.
 * @param [integer] $post_id [Post or page id]
 * @param [string/array] $size Image size. Either a string keyword (thumbnail, medium, large or full) or a 2-item array representing width and height in pixels, e.g. array(32,32).
 * @param [string/array] $attr Query string or array of attributes.
 * @return [string] image html
 *
 * @since Catch Foodmania 1.0
 */

function catch_foodmania_get_first_image( $postID, $size, $attr, $src = false ) {
	ob_start();

	ob_end_clean();

	$image 	= '';

	$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', get_post_field( 'post_content', $postID ) , $matches );

	if( isset( $matches[1][0] ) ) {
		//Get first image
		$first_img = $matches[1][0];

		if ( $src ) {
			//Return url of src is true
			return $first_img;
		}

		return '<img class="pngfix wp-post-image" src="' . $first_img . '">';
	}

	return false;
}

function catch_foodmania_get_theme_layout() {
	$layout = '';

	if ( is_page_template( 'templates/full-width-page.php' ) ) {
		$layout = 'no-sidebar-full-width';
	} elseif ( is_page_template( 'templates/right-sidebar.php' ) ) {
		$layout = 'right-sidebar';
	} else {
		$layout = get_theme_mod( 'catch_foodmania_default_layout', 'right-sidebar' );

		if ( is_home() || is_archive() || is_search() ) {
			$layout = get_theme_mod( 'catch_foodmania_homepage_archive_layout', 'no-sidebar-full-width' );
		}
	}

	return $layout;
}

function catch_foodmania_get_sidebar_id() {
	$sidebar = '';

	$layout = catch_foodmania_get_theme_layout();

	if ( 'no-sidebar-full-width' === $layout ) {
		return $sidebar;
	}

	if ( is_active_sidebar( 'sidebar-1' ) ) {
		$sidebar = 'sidebar-1'; // Primary Sidebar.
	}

	return $sidebar;
}

/**
 * Featured content posts
 */
function catch_foodmania_get_featured_posts() {
	$number = get_theme_mod( 'catch_foodmania_featured_content_number', 3 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'featured-content',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = get_theme_mod( 'catch_foodmania_featured_content_cpt_' . $i );

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby']  = 'post__in';

	$featured_posts = get_posts( $args );

	return $featured_posts;
}


/**
 * Services content posts
 */
function catch_foodmania_get_services_posts() {
	$number = get_theme_mod( 'catch_foodmania_service_number', 4 );

	$post_list    = array();

	$args = array(
		'posts_per_page'      => $number,
		'post_type'           => 'ect-service',
		'ignore_sticky_posts' => 1, // ignore sticky posts.
	);

	for ( $i = 1; $i <= $number; $i++ ) {
		$post_id = get_theme_mod( 'catch_foodmania_service_cpt_' . $i );

		if ( $post_id && '' !== $post_id ) {
			$post_list = array_merge( $post_list, array( $post_id ) );
		}
	}

	$args['post__in'] = $post_list;
	$args['orderby']  = 'post__in';

	$services_posts = get_posts( $args );

	return $services_posts;
}

if ( ! function_exists( 'catch_foodmania_sections' ) ) :
	/**
	 * Display Sections on header and footer with respect to the section option set in catch_foodmania_sections_sort
	 */
	function catch_foodmania_sections( $selector = 'header' ) {
		get_template_part( 'template-parts/slider/content', 'display' );
		get_template_part( 'template-parts/header/header', 'media' );
		get_template_part( 'template-parts/featured-content/display', 'featured' );
		get_template_part( 'template-parts/services/display', 'services' );
		get_template_part( 'template-parts/hero-content/content','hero' );
		get_template_part( 'template-parts/food-menu/display', 'menu' );
		get_template_part( 'template-parts/testimonials/display', 'testimonial' );
	}
endif;
