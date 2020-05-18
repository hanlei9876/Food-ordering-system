<?php
/**
 * Theme Options
 *
 * @package Catch_Foodmania
 */

/**
 * Add theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_theme_options( $wp_customize ) {
	$wp_customize->add_panel( 'catch_foodmania_theme_options', array(
		'title'    => esc_html__( 'Theme Options', 'catch-foodmania' ),
		'priority' => 130,
	) );

	// Breadcrumb Option.
	$wp_customize->add_section( 'catch_foodmania_breadcrumb_options', array(
		'description'   => esc_html__( 'Breadcrumbs are a great way of letting your visitors find out where they are on your site with just a glance.', 'catch-foodmania' ),
		'panel'         => 'catch_foodmania_theme_options',
		'title'         => esc_html__( 'Breadcrumb', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              =>'catch_foodmania_breadcrumb_option',
			'default'           => 1,
			'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
			'label'             => esc_html__( 'Check to enable Breadcrumb', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_breadcrumb_options',
			'type'              => 'checkbox',
	    )
	);
    // Breadcrumb Option End

	// Layout Options
	$wp_customize->add_section( 'catch_foodmania_layout_options', array(
		'title' => esc_html__( 'Layout Options', 'catch-foodmania' ),
		'panel' => 'catch_foodmania_theme_options',
		)
	);

	/* Default Layout */
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_default_layout',
			'default'           => 'right-sidebar',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'label'             => esc_html__( 'Default Layout', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'catch-foodmania' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'catch-foodmania' ),
			),
		)
	);

	/* Homepage/Archive Layout */
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_homepage_archive_layout',
			'default'           => 'no-sidebar-full-width',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'label'             => esc_html__( 'Homepage/Archive Layout', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'right-sidebar'         => esc_html__( 'Right Sidebar ( Content, Primary Sidebar )', 'catch-foodmania' ),
				'no-sidebar-full-width' => esc_html__( 'No Sidebar: Full Width', 'catch-foodmania' ),
			),
		)
	);

	// Single Page/Post Image
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_single_layout',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'label'             => esc_html__( 'Single Page/Post Image', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_layout_options',
			'type'              => 'radio',
			'choices'           => array(
				'disabled'       => esc_html__( 'Disabled', 'catch-foodmania' ),
				'post-thumbnail' => esc_html__( 'Post Thumbnail (1060x596)', 'catch-foodmania' ),
			),
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'catch_foodmania_excerpt_options', array(
		'panel' => 'catch_foodmania_theme_options',
		'title' => esc_html__( 'Excerpt Options', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_excerpt_length',
			'default'           => '10',
			'sanitize_callback' => 'absint',
			'description' => esc_html__( 'Excerpt length. Default is 55 words', 'catch-foodmania' ),
			'input_attrs' => array(
				'min'   => 10,
				'max'   => 200,
				'step'  => 5,
				'style' => 'width: 60px;',
			),
			'label'    => esc_html__( 'Excerpt Length (words)', 'catch-foodmania' ),
			'section'  => 'catch_foodmania_excerpt_options',
			'type'     => 'number',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_excerpt_more_text',
			'default'           => esc_html__( 'Continue reading', 'catch-foodmania' ),
			'sanitize_callback' => 'sanitize_text_field',
			'label'             => esc_html__( 'Read More Text', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_excerpt_options',
			'type'              => 'text',
		)
	);

	// Excerpt Options.
	$wp_customize->add_section( 'catch_foodmania_search_options', array(
		'panel'     => 'catch_foodmania_theme_options',
		'title'     => esc_html__( 'Search Options', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_search_text',
			'default'           => esc_html__( 'Search ...', 'catch-foodmania' ),
			'sanitize_callback' => 'wp_kses_data',
			'label'             => esc_html__( 'Search Text', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_search_options',
			'type'              => 'text',
		)
	);

	// Homepage / Frontpage Options.
	$wp_customize->add_section( 'catch_foodmania_homepage_options', array(
		'description' => esc_html__( 'Only posts that belong to the categories selected here will be displayed on the front page', 'catch-foodmania' ),
		'panel'       => 'catch_foodmania_theme_options',
		'title'       => esc_html__( 'Homepage / Frontpage Options', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_front_page_category',
			'sanitize_callback' => 'catch_foodmania_sanitize_category_list',
			'custom_control'    => 'Catch_Foodmania_Multi_Cat',
			'label'             => esc_html__( 'Categories', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_homepage_options',
			'type'              => 'dropdown-categories',
		)
	);

	$wp_customize->add_section( 'catch_foodmania_menu_options', array(
		'panel'       => 'catch_foodmania_theme_options',
		'title'       => esc_html__( 'Menu Options', 'catch-foodmania' ),
	) );

	// Pagination Options.
	$wp_customize->add_section( 'catch_foodmania_pagination_options', array(
		'panel'       => 'catch_foodmania_theme_options',
		'title'       => esc_html__( 'Pagination Options', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_pagination_type',
			'default'           => 'default',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'choices'           => catch_foodmania_get_pagination_types(),
			'label'             => esc_html__( 'Pagination type', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_pagination_options',
			'type'              => 'select',
		)
	);

	/* Scrollup Options */
	$wp_customize->add_section( 'catch_foodmania_scrollup', array(
		'panel'    => 'catch_foodmania_theme_options',
		'title'    => esc_html__( 'Scrollup Options', 'catch-foodmania' ),
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_disable_scrollup',
			'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
			'label'             => esc_html__( 'Disable Scroll Up', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_scrollup',
			'type'              => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'catch_foodmania_theme_options' );