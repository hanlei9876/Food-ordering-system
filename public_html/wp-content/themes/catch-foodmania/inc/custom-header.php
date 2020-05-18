<?php
/**
 * Sample implementation of the Custom Header feature
 *
 * You can add an optional custom header image to header.php like so ...
 *
	<?php the_header_image_tag(); ?>
 *
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 *
 * @package Catch_Foodmania
 */

/**
 * Sets up the WordPress core custom header and custom background features.
 *
 * @since Catch Foodmania 1.0
 *
 * @see catch_foodmania_header_style()
 */
function catch_foodmania_custom_header_and_background() {
	/**
	 * Filter the arguments used when adding 'custom-header' support in Foodie World.
	 *
	 * @since Catch Foodmania 1.0
	 *
	 * @param array $args {
	 *     An array of custom-header support arguments.
	 *
	 *     @type string $default-text-color Default color of the header text.
	 *     @type int      $width            Width in pixels of the custom header image. Default 1200.
	 *     @type int      $height           Height in pixels of the custom header image. Default 280.
	 *     @type bool     $flex-height      Whether to allow flexible-height header images. Default true.
	 *     @type callable $wp-head-callback Callback function used to style the header image and text
	 *                                      displayed on the blog.
	 * }
	 */
	add_theme_support( 'custom-header', apply_filters( 'catch_foodmania_custom_header_args', array(
		'default-image'      	 => get_parent_theme_file_uri( '/assets/images/header.jpg' ),
		'default-text-color'     => '#db2f39',
		'width'                  => 1920,
		'height'                 => 540,
		'flex-height'            => true,
		'flex-height'            => true,
		'wp-head-callback'       => 'catch_foodmania_header_style',
		'video'                  => true,
	) ) );

	$default_headers_args = array(
		'main' => array(
			'thumbnail_url' => get_stylesheet_directory_uri() . '/assets/images/header-thumb-275x77.jpg',
			'url'           => get_stylesheet_directory_uri() . '/assets/images/header.jpg',
		),
	);

	register_default_headers( $default_headers_args );
}
add_action( 'after_setup_theme', 'catch_foodmania_custom_header_and_background' );


if ( ! function_exists( 'catch_foodmania_header_style' ) ) :
	/**
	 * Styles the header image and text displayed on the blog.
	 *
	 * @see catch_foodmania_custom_header_setup().
	 */
	function catch_foodmania_header_style() {
		$header_text_color = get_header_textcolor();

		$header_image = catch_foodmania_featured_overall_image();

	    if ( $header_image ) : ?>
	        <style type="text/css" rel="header-image">
	            .custom-header:before {
	                background-image: url( <?php echo esc_url( $header_image ); ?>);
					background-position: center;
					background-repeat: no-repeat;
					background-size: cover;
	            }
	        </style>
	    <?php
	    endif;

		/*
		 * If no custom options for text are set, let's bail.
		 * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
		 */
		if ( '#ffffff' === $header_text_color ) {
			return;
		}

		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
		// Has the text been hidden?
		if ( ! display_header_text() ) :
		?>
			.site-title,
			.site-description {
				position: absolute;
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php
			// If the user has set a custom color for the text use that.
			else :
		?>
			.site-title a {
				color: #<?php echo esc_attr( $header_text_color ); ?>;
			}
		<?php endif; ?>
		</style>
		<?php
	}
endif;

if ( ! function_exists( 'catch_foodmania_featured_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own catch_foodmania_featured_image(), and that function will be used instead.
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_featured_image() {
		$thumbnail = is_front_page() ? 'catch-foodmania-header-inner' : 'catch-foodmania-slider';

		if ( is_post_type_archive( 'jetpack-testimonial' ) ) {
			$jetpack_options = get_theme_mod( 'jetpack_testimonials' );

			if ( isset( $jetpack_options['featured-image'] ) && '' !== $jetpack_options['featured-image'] ) {
				$image = wp_get_attachment_image_src( (int) $jetpack_options['featured-image'], $thumbnail );
				return $image[0];
			} else {
				return false;
			}
		} elseif ( is_post_type_archive( 'jetpack-portfolio' ) || is_post_type_archive( 'featured-content' ) || is_post_type_archive( 'ect-service' ) ) {
			$option = '';

			if ( is_post_type_archive( 'jetpack-portfolio' ) ) {
				$option = 'jetpack_portfolio_featured_image';
			} elseif ( is_post_type_archive( 'featured-content' ) ) {
				$option = 'featured_content_featured_image';
			} elseif ( is_post_type_archive( 'ect-service' ) ) {
				$option = 'ect_service_featured_image';
			}

			$featured_image = get_option( $option );

			if ( '' !== $featured_image ) {
				$image = wp_get_attachment_image_src( (int) $featured_image, $thumbnail );
				return $image[0];
			} else {
				return get_header_image();
			}
		} elseif ( is_header_video_active() && has_header_video() ) {
			return true;
		} else {
			return get_header_image();
		}
	} // catch_foodmania_featured_image
endif;

if ( ! function_exists( 'catch_foodmania_featured_page_post_image' ) ) :
	/**
	 * Template for Featured Header Image from Post and Page
	 *
	 * To override this in a child theme
	 * simply create your own catch_foodmania_featured_imaage_pagepost(), and that function will be used instead.
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_featured_page_post_image() {
		if ( ! has_post_thumbnail() ) {
			return catch_foodmania_featured_image();
		}

		$thumbnail = is_front_page() ? 'catch-foodmania-header-inner' : 'catch-foodmania-slider';

		return get_the_post_thumbnail_url( get_the_id(), $thumbnail );
	} // catch_foodmania_featured_page_post_image
endif;

if ( ! function_exists( 'catch_foodmania_featured_overall_image' ) ) :
	/**
	 * Template for Featured Header Image from theme options
	 *
	 * To override this in a child theme
	 * simply create your own catch_foodmania_featured_pagepost_image(), and that function will be used instead.
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_featured_overall_image() {
		global $post;
		$enable = get_theme_mod( 'catch_foodmania_header_media_option', 'homepage' );

		// Check Enable/Disable header image in Page/Post Meta box
		if ( is_singular() ) {
			//Individual Page/Post Image Setting
			$individual_featured_image = get_post_meta( $post->ID, 'catch-foodmania-header-image', true );

			if ( 'disable' === $individual_featured_image || ( 'default' === $individual_featured_image && 'disable' === $enable ) ) {
				return;
			} elseif ( 'enable' == $individual_featured_image && 'disable' === $enable ) {
				return catch_foodmania_featured_page_post_image();
			}
		}

		// Check Homepage
		if ( 'homepage' === $enable ) {
			if ( is_front_page() || ( is_home() && is_front_page() ) ) {
				return catch_foodmania_featured_image();
			}
		} elseif ( 'exclude-home' === $enable ) {
			// Check Excluding Homepage
			if ( is_front_page() || ( is_home() && is_front_page() ) ) {
				return false;
			} else {
				return catch_foodmania_featured_image();
			}
		} elseif ( 'exclude-home-page-post' === $enable ) {
			if ( is_front_page() || ( is_home() && is_front_page() ) ) {
				return false;
			} elseif ( is_singular() || ( class_exists( 'WooCommerce' ) && is_shop() ) || ( is_home() && ! is_front_page() ) ) {
				return catch_foodmania_featured_page_post_image();
			} else {
				return catch_foodmania_featured_image();
			}
		} elseif ( 'entire-site' === $enable ) {
			// Check Entire Site
			return catch_foodmania_featured_image();
		} elseif ( 'entire-site-page-post' === $enable ) {
			// Check Entire Site (Post/Page)
			if ( is_singular() || ( class_exists( 'WooCommerce' ) && is_shop() ) || ( is_home() && ! is_front_page() ) ) {
				return catch_foodmania_featured_page_post_image();
			} else {
				return catch_foodmania_featured_image();
			}
		} elseif ( 'pages-posts' === $enable ) {
			// Check Page/Post
			if ( is_singular() ) {
				return catch_foodmania_featured_page_post_image();
			}
		}

		return false;
	} // catch_foodmania_featured_overall_image
endif;

if ( ! function_exists( 'catch_foodmania_header_media_text' ) ):
	/**
	 * Display Header Media Text
	 *
	 * @since Catch Foodmania 1.0
	 */
	function catch_foodmania_header_media_text() {
		?>
		<?php if ( catch_foodmania_has_header_media_text() ) : ?>
			<div class="custom-header-content content-align-left">
				<div class="entry-container">
						<div class="entry-container-wrap">
						<header class="entry-header">
							<?php 
							if ( is_singular() ) {
								echo '<h1 class="entry-title">';
								catch_foodmania_header_title(); 
								echo '</h1>';
							} else {
								echo '<h2 class="entry-title">';
								catch_foodmania_header_title(); 
								echo '</h2>';
							}?>							
						</header>

						<?php catch_foodmania_header_text(); ?>
					</div> <!-- .entry-container-wrap -->
					<?php get_template_part( 'template-parts/header/breadcrumb' ); ?>
					</div><!-- entry-container -->
				</div>
		<?php endif; 
	} // catch_foodmania_header_media_text.
endif;

if ( ! function_exists( 'catch_foodmania_has_header_media_text' ) ):
	/**
	 * Return Header Media Text fro front page
	 *
	 * @since Foodie World 0.1
	 */
	function catch_foodmania_has_header_media_text() {
		$header_media_title    = get_theme_mod( 'catch_foodmania_header_media_title' );
		$header_media_subtitle = get_theme_mod( 'catch_foodmania_header_media_subtitle' );
		$header_media_text     = get_theme_mod( 'catch_foodmania_header_media_text' );
		$header_media_url      = get_theme_mod( 'catch_foodmania_header_media_url', '' );
		$header_media_url_text = get_theme_mod( 'catch_foodmania_header_media_url_text' );

		$header_image = catch_foodmania_featured_overall_image();

		if ( ( is_front_page() && ! $header_media_title && ! $header_media_subtitle && ! $header_media_text && ! $header_media_url && ! $header_media_url_text ) || ( ( is_singular() || is_archive() || is_search() || is_404() ) && ! $header_image ) ) {
			// Header Media text Disabled
			return false;
		}

		return true;
	} // catch_foodmania_has_header_media_text.
endif;
