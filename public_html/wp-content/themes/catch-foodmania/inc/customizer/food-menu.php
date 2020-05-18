<?php
/**
 * Add Food Menu Settings in Customizer
 *
 * @package Catch_Foodmania
*/

/**
 * Add food_menu options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_food_menu_options( $wp_customize ) {
	// Add note to ECT Featured Content Section
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_note',
			'sanitize_callback' => 'sanitize_text_field',
			'custom_control'    => 'Catch_Foodmania_Note_Control',
			'active_callback'   => 'catch_foodmania_is_ect_food_menu_inactive',
			/* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
				'label'             => sprintf( esc_html__( 'For Food Menu, install %1$sEssential Content Types Pro%2$s Plugin with Food Menu Enabled', 'catch-foodmania' ),
				'<a target="_blank" href="https://catchplugins.com/plugins/essential-content-types-pro/">',
				'</a>'
			),
			'section'           => 'catch_foodmania_food_menu',
			'type'              => 'description',
			'priority'          => 1,
		)
	);

	$wp_customize->add_section( 'catch_foodmania_food_menu', array(
			'panel' => 'catch_foodmania_theme_options',
			'title' => esc_html__( 'Food Menus', 'catch-foodmania' ),
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'active_callback'   => 'catch_foodmania_is_ect_food_menu_active',
			'choices'           => catch_foodmania_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_food_menu',
			'type'              => 'select',
			'priority'          => 1,
		)
	);

	/* Testimonial Background */
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_bg_image',
			'default'           => trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/food-menu-bg.jpg',
			'sanitize_callback' => 'esc_url_raw',
			'active_callback'   => 'catch_foodmania_is_food_menu_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Background Image', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_food_menu',
		)
	);

	$wp_customize->add_setting( 'catch_foodmania_food_menu_bg_position_x', array(
		'sanitize_callback' => 'catch_foodmania_sanitize_food_menu_bg_position',
	) );

	$wp_customize->add_setting( 'catch_foodmania_food_menu_bg_position_y', array(
		'sanitize_callback' => 'catch_foodmania_sanitize_food_menu_bg_position',
	) );

	$wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'catch_foodmania_food_menu_bg_position', array(
		'label'           => esc_html__( 'Background Image Position', 'catch-foodmania' ),
		'active_callback' => 'catch_foodmania_is_food_menu_bg_active',
		'section'         => 'catch_foodmania_food_menu',
		'settings'        => array(
			'x' => 'catch_foodmania_food_menu_bg_position_x',
			'y' => 'catch_foodmania_food_menu_bg_position_y',
		),
	) ) );

	catch_foodmania_register_option( $wp_customize, array(
		'name'              => 'catch_foodmania_food_menu_bg_size',
		'default'           => 'cover',
		'description'       => esc_html__( 'In mobiles, Background Size is always cover', 'catch-foodmania' ),
		'sanitize_callback' => 'catch_foodmania_sanitize_select',
		'active_callback'   => 'catch_foodmania_is_food_menu_bg_active',
		'label'             => esc_html__( 'Desktop Background Image Size', 'catch-foodmania' ),
		'section'           => 'catch_foodmania_food_menu',
		'type'              => 'select',
		'choices' => array(
			'auto'    => esc_html__( 'Original', 'catch-foodmania' ),
			'contain' => esc_html__( 'Fit to Screen', 'catch-foodmania' ),
			'cover'   => esc_html__( 'Fill Screen', 'catch-foodmania' ),
		),
	) );

	catch_foodmania_register_option( $wp_customize, array(
		'name'              => 'catch_foodmania_food_menu_bg_repeat',
		'default'           => 'repeat',
		'sanitize_callback' => 'catch_foodmania_sanitize_select',
		'active_callback'   => 'catch_foodmania_is_food_menu_bg_active',
		'label'             => esc_html__( 'Repeat Background Image', 'catch-foodmania' ),
		'type'              => 'select',
		'section'           => 'catch_foodmania_food_menu',
		'choices'           => array(
			'no-repeat' =>  esc_html__( 'No Repeat', 'catch-foodmania' ),
			'repeat'    =>  esc_html__( 'Repeat both vertically and horizontally (The last image will be clipped if it does not fit)', 'catch-foodmania' ),
			'repeat-x'  =>  esc_html__( 'Repeat only horizontally', 'catch-foodmania' ),
			'repeat-y'  =>  esc_html__( 'Repeat only vertically', 'catch-foodmania' ),
		),
	) );

	catch_foodmania_register_option( $wp_customize, array(
		'name'              => 'catch_foodmania_food_menu_bg_attachment',
		'default'           => 1,
		'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
		'active_callback'   => 'catch_foodmania_is_food_menu_bg_active',
		'label'             => esc_html__( 'Scroll with Page', 'catch-foodmania' ),
		'section'           => 'catch_foodmania_food_menu',
		'type'              => 'checkbox',
	) );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_headline',
			'default'           => esc_html__( 'Our Menu', 'catch-foodmania' ),
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Headline', 'catch-foodmania' ),
			'active_callback'   => 'catch_foodmania_is_food_menu_active',
			'section'           => 'catch_foodmania_food_menu',
			'type'              => 'text',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_subheadline',
			'sanitize_callback' => 'wp_kses_post',
			'label'             => esc_html__( 'Sub headline', 'catch-foodmania' ),
			'active_callback'   => 'catch_foodmania_is_food_menu_active',
			'section'           => 'catch_foodmania_food_menu',
			'type'              => 'text',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_food_menu_number',
			'default'           => 5,
			'sanitize_callback' => 'catch_foodmania_sanitize_number_range',
			'active_callback'   => 'catch_foodmania_is_food_menu_active',
			'label'             => esc_html__( 'No of items', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_food_menu',
			'type'              => 'number',
			'input_attrs'       => array(
				'style'             => 'width: 100px;',
				'min'               => 0,
			),
		)
	);

	$number = get_theme_mod( 'catch_foodmania_food_menu_number', 5 );

	for ( $i = 1; $i <= $number ; $i++ ) {
		catch_foodmania_register_option( $wp_customize, array(
				'name'              => 'catch_foodmania_food_menu_cpt_' . $i,
				'sanitize_callback' => 'catch_foodmania_sanitize_select',
				'active_callback'   => 'catch_foodmania_is_food_menu_active',
				'label'             => esc_html__( 'Menu', 'catch-foodmania' ) . ' ' . $i ,
				'section'           => 'catch_foodmania_food_menu',
				'type'              => 'select',
				'choices'           => catch_foodmania_generate_taxonomy_array( 'ect_food_menu' ),
			)
		);
	} // End for().
}
add_action( 'customize_register', 'catch_foodmania_food_menu_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'catch_foodmania_is_food_menu_active' ) ) :
	/**
	* Return true if featured content is active
	*
	* @since Catch Foodmania 0.1
	*/
	function catch_foodmania_is_food_menu_active( $control ) {
		$enable = $control->manager->get_setting( 'catch_foodmania_food_menu_option' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( catch_foodmania_check_section( $enable ) && catch_foodmania_is_ect_food_menu_active( $control ) );
	}
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_food_menu_inactive' ) ) :
    /**
    * Return true if food_menu is active
    *
    * @since Catch Foodmania 0.1
    */
    function catch_foodmania_is_ect_food_menu_inactive( $control ) {
        return ! class_exists( 'Essential_Content_Pro_Featured_Content' );
    }
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_food_menu_active' ) ) :
    /**
    * Return true if food_menu is active
    *
    * @since Catch Foodmania 0.1
    */
    function catch_foodmania_is_ect_food_menu_active( $control ) {
        return class_exists( 'Essential_Content_Pro_Featured_Content' );
    }
endif;


if ( ! function_exists( 'catch_foodmania_is_food_menu_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Catch Foodmania 1.0
    */
    function catch_foodmania_is_food_menu_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'catch_foodmania_food_menu_bg_image' )->value();

        return ( catch_foodmania_is_food_menu_active( $control ) && '' !== $bg_image );
    }
endif;
