<?php
/**
 * Services options
 *
 * @package Catch_Foodmania
 */

/**
 * Add services content options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_service_options( $wp_customize ) {
	// Add note to Jetpack Portfolio Section
    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_service_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'label'             => sprintf( esc_html__( 'For Service Options, go %1$shere%2$s', 'catch-foodmania' ),
                 '<a href="javascript:wp.customize.section( \'catch_foodmania_service\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'ect_service',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	$wp_customize->add_section( 'catch_foodmania_service', array(
			'panel' => 'catch_foodmania_theme_options',
			'title' => esc_html__( 'Service', 'catch-foodmania' ),
		)
	);

	$action = 'install-plugin';
	$slug   = 'essential-content-types';

	$install_url = wp_nonce_url(
	    add_query_arg(
	        array(
	            'action' => $action,
	            'plugin' => $slug
	        ),
	        admin_url( 'update.php' )
	    ),
	    $action . '_' . $slug
	);

	catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_service_note_1',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'active_callback'   => 'catch_foodmania_is_ect_services_inactive',
            'label'             => sprintf( esc_html__( 'For Services, install %1$sEssential Content Types%2$s Plugin with Services Content Type Enabled', 'catch-foodmania' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'
            ),
            'section'           => 'catch_foodmania_service',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

	// Add color scheme setting and control.
	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_service_option',
			'default'           => 'disabled',
			'sanitize_callback' => 'catch_foodmania_sanitize_select',
			'active_callback'	=> 'catch_foodmania_is_ect_services_active',
			'choices'           => catch_foodmania_section_visibility_options(),
			'label'             => esc_html__( 'Enable on', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_service',
			'type'              => 'select',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_service_main_image',
			'sanitize_callback' => 'catch_foodmania_sanitize_image',
			'active_callback'   => 'catch_foodmania_is_services_active',
			'custom_control'    => 'WP_Customize_Image_Control',
			'label'             => esc_html__( 'Section Main Image', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_service',
			'mime_type'         => 'image',
		)
	);

	catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_service_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'active_callback'   => 'catch_foodmania_is_services_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'catch-foodmania' ),
                 '<a href="javascript:wp.customize.control( \'ect_service_title\' ).focus();">',
                 '</a>'
            ),
            'section'           => 'catch_foodmania_service',
            'type'              => 'description',
        )
    );

	catch_foodmania_register_option( $wp_customize, array(
			'name'              => 'catch_foodmania_service_number',
			'default'           => 4,
			'sanitize_callback' => 'catch_foodmania_sanitize_number_range',
			'active_callback'   => 'catch_foodmania_is_services_active',
			'description'       => esc_html__( 'Save and refresh the page if No. of Services is changed (Max no of Services is 20)', 'catch-foodmania' ),
			'input_attrs'       => array(
				'style' => 'width: 100px;',
				'min'   => 0,
			),
			'label'             => esc_html__( 'No of items', 'catch-foodmania' ),
			'section'           => 'catch_foodmania_service',
			'type'              => 'number',
			'transport'         => 'postMessage',
		)
	);

	$number = get_theme_mod( 'catch_foodmania_service_number', 4 );

	//loop for services post content
	for ( $i = 1; $i <= $number ; $i++ ) {
		catch_foodmania_register_option( $wp_customize, array(
				'name'              => 'catch_foodmania_service_cpt_' . $i,
				'sanitize_callback' => 'catch_foodmania_sanitize_post',
				'active_callback'   => 'catch_foodmania_is_services_active',
				'label'             => esc_html__( 'Services', 'catch-foodmania' ) . ' ' . $i ,
				'section'           => 'catch_foodmania_service',
				'type'              => 'select',
                'choices'           => catch_foodmania_generate_post_array( 'ect-service' ),
			)
		);
	} // End for().
}
add_action( 'customize_register', 'catch_foodmania_service_options', 10 );

/** Active Callback Functions **/
if ( ! function_exists( 'catch_foodmania_is_services_active' ) ) :
	/**
	* Return true if featured content is active
	*
	* @since Adonis 0.1
	*/
	function catch_foodmania_is_services_active( $control ) {
		$enable = $control->manager->get_setting( 'catch_foodmania_service_option' )->value();

		return ( catch_foodmania_is_ect_services_active( $control ) &&  catch_foodmania_check_section( $enable ) );
	}
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_services_inactive' ) ) :
    /**
    * Return true if service is active
    *
    * @since Adonis 0.1
    */
    function catch_foodmania_is_ect_services_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Service' ) || class_exists( 'Essential_Content_Pro_Service' ) );
    }
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_services_active' ) ) :
    /**
    * Return true if service is active
    *
    * @since Adonis 0.1
    */
    function catch_foodmania_is_ect_services_active( $control ) {
        return ( class_exists( 'Essential_Content_Service' ) || class_exists( 'Essential_Content_Pro_Service' ) );
    }
endif;