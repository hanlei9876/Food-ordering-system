<?php
/**
 * Add Testimonial Settings in Customizer
 *
 * @package Catch_Foodmania
*/

/**
 * Add testimonial options to theme options
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function catch_foodmania_testimonial_options( $wp_customize ) {
    // Add note to Jetpack Testimonial Section
    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_jetpack_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'label'             => sprintf( esc_html__( 'For Testimonial Options, go %1$shere%2$s', 'catch-foodmania' ),
                '<a href="javascript:wp.customize.section( \'catch_foodmania_testimonials\' ).focus();">',
                '</a>'
            ),
            'section'           => 'jetpack_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    $wp_customize->add_section( 'catch_foodmania_testimonials', array(
            'panel'    => 'catch_foodmania_theme_options',
            'title'    => esc_html__( 'Testimonials', 'catch-foodmania' ),
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
            'name'              => 'catch_foodmania_testimonial_note_1',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'active_callback'   => 'catch_foodmania_is_ect_testimonial_inactive',
            'label'             => sprintf( esc_html__( 'For Testimonial, install %1$sEssential Content Types%2$s Plugin with Testimonial Content Type Enabled', 'catch-foodmania' ),
                '<a target="_blank" href="' . esc_url( $install_url ) . '">',
                '</a>'
            ),
            'section'           => 'catch_foodmania_testimonials',
            'type'              => 'description',
            'priority'          => 1,
        )
    );

    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_testimonial_option',
            'default'           => 'disabled',
            'sanitize_callback' => 'catch_foodmania_sanitize_select',
            'choices'           => catch_foodmania_section_visibility_options(),
            'label'             => esc_html__( 'Enable on', 'catch-foodmania' ),
            'section'           => 'catch_foodmania_testimonials',
            'type'              => 'select',
            'priority'          => 1,
        )
    );

    /* Custom Recent Posts Background */
    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_testimonial_bg_image',
            'default'           => trailingslashit( esc_url( get_template_directory_uri() ) ) . 'assets/images/recent-posts-section-bg.png',
            'sanitize_callback' => 'esc_url_raw',
            'active_callback'   => 'catch_foodmania_is_testimonial_active',
            'custom_control'    => 'WP_Customize_Image_Control',
            'label'             => esc_html__( 'Background Image', 'catch-foodmania' ),
            'section'           => 'catch_foodmania_testimonials',
        )
    );

    $wp_customize->add_setting( 'catch_foodmania_testimonial_bg_position_x', array(
        'sanitize_callback' => 'catch_foodmania_sanitize_testimonial_bg_position',
    ) );

    $wp_customize->add_setting( 'catch_foodmania_testimonial_bg_position_y', array(
        'sanitize_callback' => 'catch_foodmania_sanitize_testimonial_bg_position',
    ) );

    $wp_customize->add_control( new WP_Customize_Background_Position_Control( $wp_customize, 'catch_foodmania_testimonial_bg_position', array(
        'label'           => esc_html__( 'Background Image Position', 'catch-foodmania' ),
        'active_callback' => 'catch_foodmania_is_testimonial_bg_active',
        'section'         => 'catch_foodmania_testimonials',
        'settings'        => array(
            'x' => 'catch_foodmania_testimonial_bg_position_x',
            'y' => 'catch_foodmania_testimonial_bg_position_y',
        ),
    ) ) );

    catch_foodmania_register_option( $wp_customize, array(
        'name'              => 'catch_foodmania_testimonial_bg_size',
        'default'           => 'auto',
        'description'       => esc_html__( 'In mobiles, Background Size is always cover', 'catch-foodmania' ),
        'sanitize_callback' => 'catch_foodmania_sanitize_select',
        'active_callback'   => 'catch_foodmania_is_testimonial_bg_active',
        'label'             => esc_html__( 'Desktop Background Image Size', 'catch-foodmania' ),
        'section'           => 'catch_foodmania_testimonials',
        'type'              => 'select',
        'choices' => array(
            'auto'    => esc_html__( 'Original', 'catch-foodmania' ),
            'contain' => esc_html__( 'Fit to Screen', 'catch-foodmania' ),
            'cover'   => esc_html__( 'Fill Screen', 'catch-foodmania' ),
        ),
    ) );

    catch_foodmania_register_option( $wp_customize, array(
        'name'              => 'catch_foodmania_testimonial_bg_repeat',
        'default'           => 'repeat',
        'sanitize_callback' => 'catch_foodmania_sanitize_select',
        'active_callback'   => 'catch_foodmania_is_testimonial_bg_active',
        'label'             => esc_html__( 'Repeat Background Image', 'catch-foodmania' ),
        'type'              => 'select',
        'section'           => 'catch_foodmania_testimonials',
        'choices'           => array(
            'no-repeat' =>  esc_html__( 'No Repeat', 'catch-foodmania' ),
            'repeat'    =>  esc_html__( 'Repeat both vertically and horizontally (The last image will be clipped if it does not fit)', 'catch-foodmania' ),
            'repeat-x'  =>  esc_html__( 'Repeat only horizontally', 'catch-foodmania' ),
            'repeat-y'  =>  esc_html__( 'Repeat only vertically', 'catch-foodmania' ),
        ),
    ) );

    catch_foodmania_register_option( $wp_customize, array(
        'name'              => 'catch_foodmania_testimonial_bg_attachment',
        'default'           => 1,
        'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
        'active_callback'   => 'catch_foodmania_is_testimonial_bg_active',
        'label'             => esc_html__( 'Scroll with Page', 'catch-foodmania' ),
        'section'           => 'catch_foodmania_testimonials',
        'type'              => 'checkbox',
    ) );

    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_testimonial_cpt_note',
            'sanitize_callback' => 'sanitize_text_field',
            'custom_control'    => 'Catch_Foodmania_Note_Control',
            'active_callback'   => 'catch_foodmania_is_testimonial_active',
            /* translators: 1: <a>/link tag start, 2: </a>/link tag close. */
			'label'             => sprintf( esc_html__( 'For CPT heading and sub-heading, go %1$shere%2$s', 'catch-foodmania' ),
                '<a href="javascript:wp.customize.section( \'jetpack_testimonials\' ).focus();">',
                '</a>'
            ),
            'section'           => 'catch_foodmania_testimonials',
            'type'              => 'description',
        )
    );

    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_testimonial_number',
            'default'           => 4,
            'sanitize_callback' => 'catch_foodmania_sanitize_number_range',
            'active_callback'   => 'catch_foodmania_is_testimonial_active',
            'label'             => esc_html__( 'No of items', 'catch-foodmania' ),
            'section'           => 'catch_foodmania_testimonials',
            'type'              => 'number',
            'input_attrs'       => array(
                'style'             => 'width: 100px;',
                'min'               => 1,
                'max'               => 7,
            ),
        )
    );

    catch_foodmania_register_option( $wp_customize, array(
            'name'              => 'catch_foodmania_testimonial_enable_title',
            'default'           => 1,
            'sanitize_callback' => 'catch_foodmania_sanitize_checkbox',
            'active_callback'   => 'catch_foodmania_is_testimonial_active',
            'label'             => esc_html__( 'Check to Enable Title', 'catch-foodmania' ),
            'section'           => 'catch_foodmania_testimonials',
            'type'              => 'checkbox',
        )
    );

    $number = get_theme_mod( 'catch_foodmania_testimonial_number', 4 );

    for ( $i = 1; $i <= $number ; $i++ ) {
        catch_foodmania_register_option( $wp_customize, array(
                'name'              => 'catch_foodmania_testimonial_cpt_' . $i,
                'sanitize_callback' => 'catch_foodmania_sanitize_post',
                'active_callback'   => 'catch_foodmania_is_testimonial_active',
                'label'             => esc_html__( 'Testimonial', 'catch-foodmania' ) . ' ' . $i ,
                'section'           => 'catch_foodmania_testimonials',
                'type'              => 'select',
                'choices'           => catch_foodmania_generate_post_array( 'jetpack-testimonial' ),
            )
        );
    } // End for().
}
add_action( 'customize_register', 'catch_foodmania_testimonial_options' );

/**
 * Active Callback Functions
 */
if ( ! function_exists( 'catch_foodmania_is_testimonial_active' ) ) :
    /**
    * Return true if portfolio is active
    *
    * @since Adonis 0.1
    */
    function catch_foodmania_is_testimonial_active( $control ) {
        $enable = $control->manager->get_setting( 'catch_foodmania_testimonial_option' )->value();

        //return true only if previwed page on customizer matches the type of content option selected
        return ( catch_foodmania_is_ect_testimonial_active( $control ) && catch_foodmania_check_section( $enable ) );
    }
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_testimonial_inactive' ) ) :
    /**
    *
    * @since Adonis 0.1
    */
    function catch_foodmania_is_ect_testimonial_inactive( $control ) {
        return ! ( class_exists( 'Essential_Content_Jetpack_Testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_Testimonial' ) );
    }
endif;

if ( ! function_exists( 'catch_foodmania_is_ect_testimonial_active' ) ) :
    /**
    *
    * @since Adonis 0.1
    */
    function catch_foodmania_is_ect_testimonial_active( $control ) {
        return ( class_exists( 'Essential_Content_Jetpack_Testimonial' ) || class_exists( 'Essential_Content_Pro_Jetpack_Testimonial' ) );
    }
endif;

if ( ! function_exists( 'catch_foodmania_is_testimonial_bg_active' ) ) :
    /**
    * Return true if background is set
    *
    * @since Catch Foodmania 1.0
    */
    function catch_foodmania_is_testimonial_bg_active( $control ) {
        $bg_image = $control->manager->get_setting( 'catch_foodmania_testimonial_bg_image' )->value();

        return ( catch_foodmania_is_testimonial_active( $control ) && '' !== $bg_image );
    }
endif;