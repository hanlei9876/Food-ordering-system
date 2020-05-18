<?php
/**
 * Header Media Options
 *
 * @package Catch_Foodmania
 */

/**
 * Add Header Media options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_header_media_options( $wp_customize ) {
	$wp_customize->get_section( 'header_image' )->description = esc_html__( 'If you add video, it will only show up on Homepage/FrontPage. Other Pages will use Header/Post/Page Image depending on your selection of option. Header Image will be used as a fallback while the video loads ', 'catch-foodmania' );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_option',
			'default'           => 'homepage',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'choices'           => array(
				'homepage'               => esc_html__( 'Homepage / Frontpage', 'catch-foodmania' ),
				'exclude-home'           => esc_html__( 'Excluding Homepage', 'catch-foodmania' ),
				'exclude-home-page-post' => esc_html__( 'Excluding Homepage, Page/Post Featured Image', 'catch-foodmania' ),
				'entire-site'            => esc_html__( 'Entire Site', 'catch-foodmania' ),
				'entire-site-page-post'  => esc_html__( 'Entire Site, Page/Post Featured Image', 'catch-foodmania' ),
				'pages-posts'            => esc_html__( 'Pages and Posts', 'catch-foodmania' ),
				'disable'                => esc_html__( 'Disabled', 'catch-foodmania' ),
			),
			'label'             => esc_html__( 'Enable on', 'catch-foodmania' ),
			'section'           => 'header_image',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_subtitle',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Sub Title', 'catch-foodmania' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_title',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Title', 'catch-foodmania' ),
			'section'           => 'header_image',
			'type'              => 'text',
		)
	);

    catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_text',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Header Media Text', 'catch-foodmania' ),
			'section'           => 'header_image',
			'type'              => 'textarea',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_url',
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'label'             => esc_html__( 'Header Media Url', 'catch-foodmania' ),
			'section'           => 'header_image',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_media_url_text',
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Header Media Url Text', 'catch-foodmania' ),
			'section'           => 'header_image',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_header_url_target',
			'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
			'label'             => esc_html__( 'Check to Open Link in New Window/Tab', 'catch-foodmania' ),
			'section'           => 'header_image',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'catch_foodmania_header_media_options' );
