<?php
/**
 * Hero Content Options
 *
 * @package Catch_Foodmania
 */

/**
 * Add hero content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_hero_content_options( $wp_customize ) {
	$wp_customize->add_section( 'catch_foodmania_hero_content_options', array(
			'title' => esc_html__( 'Hero Content', 'catch-foodmania' ),
			'panel' => 'catch_foodmania_theme_options',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_hero_content_visibility',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'choices'           => catch_foodmania_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_hero_content_options',
			'type'              => 'select',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_hero_content',
			'default'           => '0',
			'sanitize_callback' => 'catch_foodmania_sanitize_post',
			'active_callback'   => 'catch_foodmania_is_hero_content_active',
			'label'             => esc_html__( 'Page', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_hero_content_options',
			'type'              => 'dropdown-pages',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_hero_content_subtitle',
			'sanitize_callback' => 'wp_kses_post',
			'active_callback'   => 'catch_foodmania_is_hero_content_active',
			'label'             => esc_html__( 'Subtitle', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_hero_content_options',
			'type'              => 'text',
		)
	);
}
add_action( 'customize_register', 'catch_foodmania_hero_content_options' );

/** Active Callback Functions **/
if ( ! function_exists( 'catch_foodmania_is_hero_content_active' ) ) :
	/**
	* Return true if hero content is active
	*
	* @since Catch Foodmania 1.0
	*/
	function catch_foodmania_is_hero_content_active( $control ) {
		$enable = $control->manager->get_setting( 'catch_foodmania_hero_content_visibility' )->value();

		//return true only if previewed page on customizer matches the type of content option selected
		return ( catch_foodmania_check_section( $enable ) );
	}
endif;