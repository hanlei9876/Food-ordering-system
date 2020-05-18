<?php
/**
 * Register Settings
 *
 * @package     RPRESS
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since 1.0
 * @global $rpress_options Array of all the RPRESS Options
 * @return mixed
 */
function rpress_get_option( $key = '', $default = false ) {
	global $rpress_options;
	$value = ! empty( $rpress_options[ $key ] ) ? $rpress_options[ $key ] : $default;
	$value = apply_filters( 'rpress_get_option', $value, $key, $default );
	return apply_filters( 'rpress_get_option_' . $key, $value, $key, $default );
}


/**
 * Update an option
 *
 * Updates an rpress setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *          the key from the rpress_options array.
 *
 * @since 1.0
 * @param string $key The Key to update
 * @param string|bool|int $value The value to set the key to
 * @global $rpress_options Array of all the RPRESS Options
 * @return boolean True if updated, false if not.
 */
function rpress_update_option( $key = '', $value = false ) {

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	if ( empty( $value ) ) {
		$remove_option = rpress_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings
	$options = get_option( 'rpress_settings' );

	// Let's let devs alter that value coming in
	$value = apply_filters( 'rpress_update_option', $value, $key );

	// Next let's try to update the value
	$options[ $key ] = $value;
	$did_update = update_option( 'rpress_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $rpress_options;
		$rpress_options[ $key ] = $value;

	}

	return $did_update;
}

/**
 * Remove an option
 *
 * Removes an rpress setting value in both the db and the global variable.
 *
 * @since 1.0
 * @param string $key The Key to delete
 * @global $rpress_options Array of all the RPRESS Options
 * @return boolean True if removed, false if not.
 */
function rpress_delete_option( $key = '' ) {
	global $rpress_options;

	// If no key, exit
	if ( empty( $key ) ){
		return false;
	}

	// First let's grab the current settings
	$options = get_option( 'rpress_settings' );

	// Next let's try to update the value
	if( isset( $options[ $key ] ) ) {

		unset( $options[ $key ] );

	}

	// Remove this option from the global RPRESS settings to the array_merge in rpress_settings_sanitize() doesn't re-add it.
	if( isset( $rpress_options[ $key ] ) ) {

		unset( $rpress_options[ $key ] );

	}

	$did_update = update_option( 'rpress_settings', $options );

	// If it updated, let's update the global variable
	if ( $did_update ){
		global $rpress_options;
		$rpress_options = $options;
	}

	return $did_update;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since 1.0
 * @return array RPRESS settings
 */
function rpress_get_settings() {

	$settings = get_option( 'rpress_settings' );

	if( empty( $settings ) ) {

		// Update old settings with new single option

		$general_settings = is_array( get_option( 'rpress_settings_general' ) )    ? get_option( 'rpress_settings_general' )    : array();
		$gateway_settings = is_array( get_option( 'rpress_settings_gateways' ) )   ? get_option( 'rpress_settings_gateways' )   : array();
		$email_settings   = is_array( get_option( 'rpress_settings_emails' ) )     ? get_option( 'rpress_settings_emails' )     : array();
		$style_settings   = is_array( get_option( 'rpress_settings_styles' ) )     ? get_option( 'rpress_settings_styles' )     : array();
		$tax_settings     = is_array( get_option( 'rpress_settings_taxes' ) )      ? get_option( 'rpress_settings_taxes' )      : array();
		$ext_settings     = is_array( get_option( 'rpress_settings_extensions' ) ) ? get_option( 'rpress_settings_extensions' ) : array();
		$license_settings = is_array( get_option( 'rpress_settings_licenses' ) )   ? get_option( 'rpress_settings_licenses' )   : array();
		$misc_settings    = is_array( get_option( 'rpress_settings_misc' ) )       ? get_option( 'rpress_settings_misc' )       : array();

		$settings = array_merge( $general_settings, $gateway_settings, $email_settings, $style_settings, $tax_settings, $ext_settings, $license_settings, $misc_settings );

		update_option( 'rpress_settings', $settings );

	}
	return apply_filters( 'rpress_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since 1.0
 * @return void
*/
function rpress_register_settings() {

	if ( false == get_option( 'rpress_settings' ) ) {
		add_option( 'rpress_settings' );
	}

  $registered_settings = rpress_get_registered_settings();

  if( is_array( $registered_settings ) && !empty( $registered_settings ) ) {

    foreach ( $registered_settings as $tab => $sections ) {
      foreach ( $sections as $section => $settings) {

        // Check for backwards compatibility
        $section_tabs = rpress_get_settings_tab_sections( $tab );
        if ( ! is_array( $section_tabs ) || ! array_key_exists( $section, $section_tabs ) ) {
          $section = 'main';
          $settings = $sections;
        }

        add_settings_section(
          'rpress_settings_' . $tab . '_' . $section,
          __return_null(),
          '__return_false',
          'rpress_settings_' . $tab . '_' . $section
        );

        foreach ( $settings as $option ) {
          // For backwards compatibility
          if ( empty( $option['id'] ) ) {
            continue;
          }

          $args = wp_parse_args( $option, array(
              'section'       => $section,
              'id'            => null,
              'desc'          => '',
              'name'          => '',
              'size'          => null,
              'options'       => '',
              'std'           => '',
              'min'           => null,
              'max'           => null,
              'step'          => null,
              'chosen'        => null,
              'multiple'      => null,
              'placeholder'   => null,
              'allow_blank'   => true,
              'readonly'      => false,
              'faux'          => false,
              'tooltip_title' => false,
              'tooltip_desc'  => false,
              'field_class'   => '',
          ) );

          add_settings_field(
            'rpress_settings[' . $args['id'] . ']',
            $args['name'],
            function_exists( 'rpress_' . $args['type'] . '_callback' ) ? 'rpress_' . $args['type'] . '_callback' : 'rpress_missing_callback',
            'rpress_settings_' . $tab . '_' . $section,
            'rpress_settings_' . $tab . '_' . $section,
            $args
          );
        }
      }
    }
  }

	// Creates our settings in the options table
	register_setting( 'rpress_settings', 'rpress_settings', 'rpress_settings_sanitize' );

}
add_action( 'admin_init', 'rpress_register_settings' );

/**
 * Retrieve the array of plugin settings
 *
 * @since 1.0
 * @return array
*/
function rpress_get_registered_settings() {

	/**
	 * 'Whitelisted' RPRESS settings, filters are provided for each settings
	 * section to allow extensions and other plugins to add their own settings
	 */

	$shop_states = rpress_get_shop_states( rpress_get_shop_country() );

	$rpress_settings = array(
		/** General Settings */
		'general' => apply_filters( 'rpress_settings_general',
			array(
				'main' => array(
					'order_settings' => array(
						'id'   => 'order_settings',
						'name' => '<h3>' . __( 'General Settings', 'restropress' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
						'tooltip_title' => __( 'Minimum Order Settings', 'restropress' ),
						'tooltip_desc'  => __( 'This would be the minimum order to be placed on the site to get checkout page' ),
					),
					'allow_minimum_order' => array(
						'id'   => 'allow_minimum_order',
						'name' => __( 'Enable minimum order', 'restropress' ),
						'desc' => sprintf(
							__( 'Enable this if you want to restrict users to order for a minimum amount.', 'restropress' )
						),
						'type' => 'checkbox',
					),
					'minimum_order_price' => array(
						'id'   => 'minimum_order_price',
						'size' => 'small',
						'name' => __( 'Minimum order amount', 'restropress' ),
						'desc' => sprintf(
							__( 'The minimum order amount in order to place the order.', 'restropress' )
						),
						'std'  => '100',
						'type' => 'number',
					),
					'minimum_order_error' => array(
						'id'   => 'minimum_order_error',
						'name' => __( 'Minimum order error message', 'restropress' ),
						'desc' => sprintf(
							__( 'This would be the error message when someone tries to place an order with less than the minimum order amount, You can use {min_order_price} variable in the message.', 'restropress' )
						),
						'std'  => 'We accept order for at least {min_order_price} ',
						'type' => 'textarea',
					),
					'use_external_bootstrap_script' => array(
						'id'   => 'use_external_bootstrap_script',
						'name' => __( 'Remove restropress bootstrap script', 'restropress' ),
						'desc' => sprintf(
							__( 'If your theme uses bootstrap and your site is experiencing issues with the popup display then enable this.', 'restropress' )
						),
						'type' => 'checkbox',
					),
					'page_settings' => array(
						'id'   => 'page_settings',
						'name' => '<h3>' . __( 'Pages', 'restropress' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
						'tooltip_title' => __( 'Page Settings', 'restropress' ),
						'tooltip_desc'  => __( 'RestroPress uses the pages below for handling the display of checkout, purchase confirmation, order history, and order failures. If pages are deleted or removed in some way, they can be recreated manually from the Pages menu. When re-creating the pages, enter the shortcode shown in the page content area.','restropress' ),
					),
					'purchase_page' => array(
						'id'          => 'purchase_page',
						'name'        => __( 'Checkout Page', 'restropress' ),
						'desc'        => __( 'This is the checkout page where buyers will complete their purchases. The [fooditem_checkout] shortcode must be on this page.', 'restropress' ),
						'type'        => 'select',
						'options'     => rpress_get_pages(),
						'chosen'      => true,
						'placeholder' => __( 'Select a page', 'restropress' ),
					),
					'success_page' => array(
						'id'          => 'success_page',
						'name'        => __( 'Success Page', 'restropress' ),
						'desc'        => __( 'This is the page buyers are sent to after completing their purchases. The [rpress_receipt] shortcode should be on this page.', 'restropress' ),
						'type'        => 'select',
						'options'     => rpress_get_pages(),
						'chosen'      => true,
						'placeholder' => __( 'Select a page', 'restropress' ),
					),
					'failure_page' => array(
						'id'          => 'failure_page',
						'name'        => __( 'Failed Transaction Page', 'restropress' ),
						'desc'        => __( 'This is the page buyers are sent to if their transaction is cancelled or fails.', 'restropress' ),
						'type'        => 'select',
						'options'     => rpress_get_pages(),
						'chosen'      => true,
						'placeholder' => __( 'Select a page', 'restropress' ),
					),
					'order_history_page' => array(
						'id'          => 'order_history_page',
						'name'        => __( 'Order History Page', 'restropress' ),
						'desc'        => __( 'This page shows a complete order history for the current user, including fooditem links. The [order_history] shortcode should be on this page.', 'restropress' ),
						'type'        => 'select',
						'options'     => rpress_get_pages(),
						'chosen'      => true,
						'placeholder' => __( 'Select a page', 'restropress' ),
					),
					'login_redirect_page' => array(
						'id'          => 'login_redirect_page',
						'name'        => __( 'Login Redirect Page', 'restropress' ),
						'desc'        => sprintf(
								__( 'If a customer logs in using the [rpress_login] shortcode, this is the page they will be redirected to. Note, this can be overridden using the redirect attribute in the shortcode like this: [rpress_login redirect="%s"].', 'restropress' ), trailingslashit( home_url() )
						),
						'type'        => 'select',
						'options'     => rpress_get_pages(),
						'chosen'      => true,
						'placeholder' => __( 'Select a page', 'restropress' ),
					),
					'locale_settings' => array(
						'id'            => 'locale_settings',
						'name'          => '<h3>' . __( 'Store Location', 'restropress' ) . '</h3>',
						'desc'          => '',
						'type'          => 'header',
						'tooltip_title' => __( 'Store Location Settings', 'restropress' ),
						'tooltip_desc'  => __( 'RestroPress will use the following Country and State to pre-fill fields at checkout. This will also pre-calculate any taxes defined if the location below has taxes enabled.','restropress' ),
					),
					'base_country' => array(
						'id'          => 'base_country',
						'name'        => __( 'Base Country', 'restropress' ),
						'desc'        => __( 'Where does your store operate from?', 'restropress' ),
						'type'        => 'select',
						'options'     => rpress_get_country_list(),
						'chosen'      => true,
						'placeholder' => __( 'Select a country', 'restropress' ),
					),
					'base_state' => array(
						'id'          => 'base_state',
						'name'        => __( 'Base State / Province', 'restropress' ),
						'desc'        => __( 'What state / province does your store operate from?', 'restropress' ),
						'type'        => 'shop_states',
						'chosen'      => true,
						'placeholder' => __( 'Select a state', 'restropress' ),
						'class'       => ( empty( $shop_states ) ) ? 'hidden' : '',
					),
				),

				//Currency Settings Here
				'currency' => array(
					'currency' => array(
						'id'      => 'currency',
						'name'    => __( 'Currency', 'restropress' ),
						'desc'    => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'restropress' ),
						'type'    => 'select',
						'options' => rpress_get_currencies(),
						'chosen'  => true,
					),
					'currency_position' => array(
						'id'      => 'currency_position',
						'name'    => __( 'Currency Position', 'restropress' ),
						'desc'    => __( 'Choose the location of the currency sign.', 'restropress' ),
						'type'    => 'select',
						'options' => array(
							'before' => __( 'Before - $10', 'restropress' ),
							'after'  => __( 'After - 10$', 'restropress' ),
						),
					),
					'thousands_separator' => array(
						'id'   => 'thousands_separator',
						'name' => __( 'Thousands Separator', 'restropress' ),
						'desc' => __( 'The symbol (usually , or .) to separate thousands.', 'restropress' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => ',',
					),
					'decimal_separator' => array(
						'id'   => 'decimal_separator',
						'name' => __( 'Decimal Separator', 'restropress' ),
						'desc' => __( 'The symbol (usually , or .) to separate decimal points.', 'restropress' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => '.',
					),
				),

				//Order Notification Settings Here
				'order_notification' => array(
					'order_notification_settings' => array(
						'id'   => 'notification_settings',
						'name' => '<h3>' . __( 'Notification Settings', 'restropress' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
						'tooltip_title' => __( 'Notification Settings', 'restropress' ),
					),
					'enable_order_notification' => array(
						'id'   => 'enable_order_notification',
						'name' => __( 'Enable Notification', 'restropress' ),
						'desc' => __( 'Enable order notification', 'restropress' ),
						'type' => 'checkbox',
					),
					'notification_title' => array(
						'id' => 'notification_title',
						'name'    => __( 'Title', 'restropress' ),
						'desc'    => __( 'Enter notification title', 'restropress' ),
						'type' => 'text',
					),
					'notification_body' => array(
						'id' => 'notification_body',
						'name'    => __( 'Description', 'restropress' ),
						'desc'    => __( 'Enter notification desc. Available place holder {order_id} - Order Id, {service_type} - Service Type, {payment_status} - Payment Status, {service_date} - Service Date', 'restropress' ),
						'type' => 'textarea',
					),
					'notification_sound' => array(
						'id' => 'notification_sound',
						'name'    => __( 'Notification sound', 'restropress' ),
						'desc'    => __( 'Select mp3 file for the notification sound.', 'restropress' ),
						'type' => 'upload',
					),
					'notification_sound_loop' => array(
						'id' => 'notification_sound_loop',
						'name'    => __( 'Play notification sound in loop', 'restropress' ),
						'desc'    => __( 'Enable this if you want the notificaiton sound to not stop until the notification duration.', 'restropress' ),
						'type' => 'checkbox',
						'std'		=> '1'
					),
					'notification_icon' => array(
						'id' => 'notification_icon',
						'name'    => __( 'Notification Icon', 'restropress' ),
						'desc'    => __( 'Select an image to use as the notification icon.', 'restropress' ),
						'type' => 'upload',
					),
					'notification_duration' => array(
						'id' => 'notification_duration',
						'name'    => __( 'Notification Length', 'restropress' ),
						'desc'    => __( 'Time in seconds, "0" = Default notification length', 'restropress' ),
						'type' => 'number',
					),
				),

				//Delivery Settings Starts Here
				'service_options' => array(
					'service_settings' => array(
						'id'   => 'service_settings',
						'name' => '<h3>' . __( 'Service Settings', 'restropress' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
						'tooltip_title' => __( 'Plugin Layout Style', 'restropress' ),
					),
					'enable_service' => array(
						'id' => 'enable_service',
						'name'    => __( 'Choose Services', 'restropress' ),
						'type' => 'radio',
						'options' => array(
							'delivery_and_pickup' => __( 'Both Delivery and Pickup' ),
							'delivery'  => __( 'Delivery Only', 'restropress' ),
							'pickup'  => __( 'Pickup Only', 'restropress' ),
						),
						'std' => 'delivery_and_pickup',
					),
					'store_time_format' => array(
						'id'            => 'store_time_format',
						'name'          => __( 'Store Time Format', 'restropress' ),
						'desc'          => __( 'Select restaurant time format', 'restropress' ),
						'type' => 'radio',
						'options' => array(
							'12hrs' 	=> __( '12 Hrs Format', 'restropress' ),
							'24hrs'  	=> __( '24 Hrs Format', 'restropress' ),
						),
						'std' => '12hrs',
					),
					'open_time' => array(
						'id'            => 'open_time',
						'name'          => __( 'Open Time', 'restropress' ),
						'desc'          => __( 'Select restaurant open time', 'restropress' ),
						'type'          => 'text',
            			'std'       => '9:00am',
						'field_class' 	=> 'rpress_timings',
						'allow_blank'	=> false,
					),
					'close_time' => array(
						'id'            => 'close_time',
						'name'          => __( 'Close Time', 'restropress' ),
						'desc'          => __( 'Select restaurant close time', 'restropress' ),
						'type'          => 'text',
            			'std'           => '10:00pm',
						'field_class' 	=> 'rpress_timings',
						'allow_blank'	=> false,
					),
					'prep_time' => array(
						'id'            => 'prep_time',
						'name'          => __( 'Cooking Time/Prep Time(minutes)', 'restropress' ),
						'desc'          => __( 'Enter the time required for food preparation, it would be used for displaying the time slots intelligibly', 'restropress' ),
						'type'          => 'number',
            			'std'           => '30',
						'allow_blank'	=> false,
					),
					'store_closed_msg' => array(
						'id'            => 'store_closed_msg',
						'name'          => __( 'Store closed message', 'restropress' ),
						'desc'          => __( 'Message that would display when ordering is not possible.', 'restropress' ),
						'type'          => 'textarea',
            			'std'           => __( 'Sorry, we are closed for ordering now.', 'restropress' ),
						'allow_blank'	=> false,
					),
				),

				//Checkout Options
				'checkout_options' => array(
					'checkout_settings' => array(
						'id'   => 'checkout_settings',
						'name' => '<h3>' . __( 'Checkout Field Options', 'restropress' ) . '</h3>',
						'desc' => '',
						'type' => 'header',
					),
					'enable_phone' => array(
						'id' => 'enable_phone',
						'name'    => __( 'Enable Phone  Field', 'restropress' ),
						'desc'    => __( 'Check this option to enable this field on checkout page', 'restropress' ),
						'type' => 'checkbox',
					),
					'enable_door_flat' => array(
						'id' => 'enable_door_flat',
						'name'    => __( 'Enable Door/Flat Field', 'restropress' ),
						'desc'    => __( 'Check this option to enable this field on checkout page', 'restropress' ),
						'type' => 'checkbox',
					),
					'enable_landmark' => array(
						'id' => 'enable_landmark',
						'name'    => __( 'Enable Landmark', 'restropress' ),
						'desc'    => __( 'Check this option to enable landmark on checkout page', 'restropress' ),
						'type' => 'checkbox',
					),
					'enable_order_note' => array(
						'id' => 'enable_order_note',
						'name'    => __( 'Enable Order Note' ),
						'desc'    => __( 'Check this option to enable order note on checkout page', 'restropress' ),
						'type' => 'checkbox',
					),
					'hide_address_on_pickup' => array(
						'id' => 'hide_address_on_pickup',
						'name'    => __( 'Hide Address On Pickup' ),
						'desc'    => __( 'Check this option to hide address from the checkout page when pickup is selected', 'restropress' ),
						'type' => 'checkbox',
					),
				),

			)
		),
		/** Payment Gateways Settings */
		'gateways' => apply_filters('rpress_settings_gateways',
			array(
				'main' => array(
					'test_mode' => array(
						'id'   => 'test_mode',
						'name' => __( 'Test Mode', 'restropress' ),
						'desc' => __( 'While in test mode no live transactions are processed. To fully use test mode, you must have a sandbox (test) account for the payment gateway you are testing.', 'restropress' ),
						'type' => 'checkbox',
					),
					'gateways' => array(
						'id'      => 'gateways',
						'name'    => __( 'Payment Gateways', 'restropress' ),
						'desc'    => __( 'Choose the payment gateways you want to enable.', 'restropress' ),
						'type'    => 'gateways',
						'options' => rpress_get_payment_gateways(),
					),
					'default_gateway' => array(
						'id'      => 'default_gateway',
						'name'    => __( 'Default Gateway', 'restropress' ),
						'desc'    => __( 'This gateway will be loaded automatically with the checkout page.', 'restropress' ),
						'type'    => 'gateway_select',
						'options' => rpress_get_payment_gateways(),
					),
					'accepted_cards' => array(
						'id'      => 'accepted_cards',
						'name'    => __( 'Accepted Payment Method Icons', 'restropress' ),
						'desc'    => __( 'Display icons for the selected payment methods.', 'restropress' ) . '<br/>' . __( 'You will also need to configure your gateway settings if you are accepting credit cards.', 'restropress' ),
						'type'    => 'payment_icons',
						'options' => apply_filters('rpress_accepted_payment_icons', array(
								'mastercard'      => 'Mastercard',
								'visa'            => 'Visa',
								'americanexpress' => 'American Express',
								'discover'        => 'Discover',
								'paypal'          => 'PayPal',
							)
						),
					),
				),
			)
		),


		/** Emails Settings */
		'emails' => apply_filters('rpress_settings_emails',
			array(
				'main' => array(
					'email_template' => array(
						'id'      => 'email_template',
						'name'    => __( 'Email Template', 'restropress' ),
						'desc'    => __( 'Choose a template. Click "Save Changes" then "Preview Purchase Receipt" to see the new template.', 'restropress' ),
						'type'    => 'select',
						'options' => rpress_get_email_templates(),
					),
					'email_logo' => array(
						'id'   => 'email_logo',
						'name' => __( 'Logo', 'restropress' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of the purchase receipt emails. Displayed on HTML emails only.', 'restropress' ),
						'type' => 'upload',
					),
					'from_name' => array(
						'id'   => 'from_name',
						'name' => __( 'From Name', 'restropress' ),
						'desc' => __( 'The name purchase receipts are said to come from. This should probably be your site or shop name.', 'restropress' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name' ),
					),
					'from_email' => array(
						'id'   => 'from_email',
						'name' => __( 'From Email', 'restropress' ),
						'desc' => __( 'Email to send purchase receipts from. This will act as the "from" and "reply-to" address.', 'restropress' ),
						'type' => 'email',
						'std'  => get_bloginfo( 'admin_email' ),
					),
					'email_settings' => array(
						'id'   => 'email_settings',
						'name' => '',
						'desc' => '',
						'type' => 'hook',
					),
				),
				'purchase_receipts' => array(
					'purchase_receipt_email_settings' => array(
						'id'   => 'purchase_receipt_email_settings',
						'name' => '',
						'desc' => '',
						'type' => 'hook',
					),
					'purchase_subject' => array(
						'id'   => 'purchase_subject',
						'name' => __( 'Purchase Email Subject', 'restropress' ),
						'desc' => __( 'Enter the subject line for the purchase receipt email.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'Purchase Receipt', 'restropress' ),
					),
					'purchase_heading' => array(
						'id'   => 'purchase_heading',
						'name' => __( 'Purchase Email Heading', 'restropress' ),
						'desc' => __( 'Enter the heading for the purchase receipt email.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'Purchase Receipt', 'restropress' ),
					),
					'purchase_receipt' => array(
						'id'   => 'purchase_receipt',
						'name' => __( 'Purchase Receipt', 'restropress' ),
						'desc' => __('Enter the text that is sent as purchase receipt email to users after completion of a successful purchase. HTML is accepted. Available template tags:','restropress' ) . '<br/>' . rpress_get_emails_tags_list(),
						'type' => 'rich_editor',
						'std'  => __( "Dear", "restro-press" ) . " {name},\n\n" . __( "Thank you for your purchase. Please click on the link(s) below to fooditem your files.", "restro-press" ) . "\n\n{fooditem_list}\n\n{sitename}",
					),
				),
				'new_order_notifications' => array(
					'order_notification_subject' => array(
						'id'   => 'order_notification_subject',
						'name' => __( 'Order Notification Subject', 'restropress' ),
						'desc' => __( 'Enter the subject line for the order notification email.', 'restropress' ),
						'type' => 'text',
						'std'  => 'New Order Received - Order #{payment_id}',
					),
					'order_notification_heading' => array(
						'id'   => 'order_notification_heading',
						'name' => __( 'Order Notification Heading', 'restropress' ),
						'desc' => __( 'Enter the heading for the order notification email.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'New Order Received!', 'restropress' ),
					),
					'order_notification' => array(
						'id'   => 'order_notification',
						'name' => __( 'Order Notification', 'restropress' ),
						'desc' => __( 'Enter the text that is sent as order notification email after completion of a purchase. HTML is accepted. Available template tags:','restropress' ) . '<br/>' . rpress_get_emails_tags_list(),
						'type' => 'rich_editor',
						'std'  => rpress_get_default_sale_notification_email(),
					),
					'admin_notice_emails' => array(
						'id'   => 'admin_notice_emails',
						'name' => __( 'Order Notification Emails', 'restropress' ),
						'desc' => __( 'Enter the email address(es) that should receive a notification anytime a order is placed, one per line.', 'restropress' ),
						'type' => 'textarea',
						'std'  => get_bloginfo( 'admin_email' ),
					),
					'disable_admin_notices' => array(
						'id'   => 'disable_admin_notices',
						'name' => __( 'Disable Admin Notifications', 'restropress' ),
						'desc' => __( 'Check this box if you do not want to receive order notification emails.', 'restropress' ),
						'type' => 'checkbox',
					),
				),
			)
		),
		/** Styles Settings */
		'styles' => apply_filters('rpress_settings_styles',
			array(
				'main' => array(
					'disable_styles' => array(
						'id'            => 'disable_styles',
						'name'          => __( 'Disable Styles', 'restropress' ),
						'desc'          => __( 'Check this to disable all included styling of buttons, checkout fields, and all other elements.', 'restropress' ),
						'type'          => 'checkbox',
						'tooltip_title' => __( 'Disabling Styles', 'restropress' ),
						'tooltip_desc'  => __( "If your theme has a complete custom CSS file for RestroPress, you may wish to disable our default styles. This is not recommended unless you're sure your theme has a complete custom CSS.", 'restropress' ),
					),
					'enable_food_image_popup' => array(
						'id'            => 'enable_food_image_popup',
						'name'          => __( 'Food Image Popup', 'restropress' ),
						'desc'          => __( 'If you want people to click on the food images to view the full food image then enable this.', 'restropress' ),
						'type'          => 'checkbox',
					),
					'button_header' => array(
						'id'   => 'button_header',
						'name' => '<strong>' . __( 'Buttons', 'restropress' ) . '</strong>',
						'desc' => __( 'Options for add to cart and purchase buttons', 'restropress' ),
						'type' => 'header',
					),
					'button_style' => array(
						'id'      => 'button_style',
						'name'    => __( 'Default Button Style', 'restropress' ),
						'desc'    => __( 'Choose the style you want to use for the buttons.', 'restropress' ),
						'type'    => 'select',
						'options' => rpress_get_button_styles(),
					),
					'checkout_color' => array(
						'id'      => 'checkout_color',
						'name'    => __( 'Theme Color', 'restropress' ),
						'desc'    => __( 'Choose the color you want to use for the buttons and links.', 'restropress' ),
						'type'    => 'color_select',
						'options' => rpress_get_button_colors(),
					),
				),
			)
		),

		/** Taxes Settings */
		'taxes' => apply_filters('rpress_settings_taxes',
			array(
				'main' => array(
					'enable_taxes' => array(
						'id'            => 'enable_taxes',
						'name'          => __( 'Enable Taxes', 'restropress' ),
						'desc'          => __( 'Check this to enable taxes on purchases.', 'restropress' ),
						'type'          => 'checkbox',
						'tooltip_title' => __( 'Enabling Taxes', 'restropress' ),
						'tooltip_desc'  => __( 'With taxes enabled, RestroPress will use the rules below to charge tax to customers. With taxes enabled, customers are required to input their address on checkout so that taxes can be properly calculated.', 'restropress' ),
					),
					'tax_rates' => array(
						'id'   => 'tax_rates',
						'name' => '<strong>' . __( 'Tax Rates', 'restropress' ) . '</strong>',
						'desc' => __( 'Add tax rates for specific regions. Enter a percentage, such as 6.5 for 6.5%.', 'restropress' ),
						'type' => 'tax_rates',
					),
					'tax_rate' => array(
						'id'   => 'tax_rate',
						'name' => __( 'Fallback Tax Rate', 'restropress' ),
						'desc' => __( 'Customers not in a specific rate will be charged this tax rate. Enter a percentage, such as 6.5 for 6.5%. ', 'restropress' ),
						'type' => 'text',
						'size' => 'small',
						'tooltip_title' => __( 'Fallback Tax Rate', 'restropress' ),
						'tooltip_desc'  => __( 'If the customer\'s address fails to meet the above tax rules, you can define a `default` tax rate to be applied to all other customers. Enter a percentage, such as 6.5 for 6.5%.', 'restropress' ),
					),
					'prices_include_tax' => array(
						'id'   => 'prices_include_tax',
						'name' => __( 'Prices entered with tax', 'restropress' ),
						'desc' => __( 'This option affects how you enter prices.', 'restropress' ),
						'type' => 'radio',
						'std'  => 'no',
						'options' => array(
							'yes' => __( 'Yes, I will enter prices inclusive of tax', 'restropress' ),
							'no'  => __( 'No, I will enter prices exclusive of tax', 'restropress' ),
						),
						'tooltip_title' => __( 'Prices Inclusive of Tax', 'restropress' ),
						'tooltip_desc'  => __( 'When using prices inclusive of tax, you will be entering your prices as the total amount you want a customer to pay for the fooditem, including tax. RestroPress will calculate the proper amount to tax the customer for the defined total price.', 'restropress' ),
					),
					'display_tax_rate' => array(
						'id'   => 'display_tax_rate',
						'name' => __( 'Display Tax Rate on Prices', 'restropress' ),
						'desc' => __( 'Some countries require a notice when product prices include tax.', 'restropress' ),
						'type' => 'checkbox',
					),
					'checkout_include_tax' => array(
						'id'   => 'checkout_include_tax',
						'name' => __( 'Display during checkout', 'restropress' ),
						'desc' => __( 'Should prices on the checkout page be shown with or without tax?', 'restropress' ),
						'type' => 'select',
						'std'  => 'no',
						'options' => array(
							'yes' => __( 'Including tax', 'restropress' ),
							'no'  => __( 'Excluding tax', 'restropress' ),
						),
						'tooltip_title' => __( 'Taxes Displayed for Products on Checkout', 'restropress' ),
						'tooltip_desc'  => __( 'This option will determine whether the product price displays with or without tax on checkout.', 'restropress' ),
					),
				),
			)
		),
		/** Extension Settings */
		'extensions' => apply_filters('rpress_settings_extensions',
			array()
		),
		'licenses' => apply_filters('rpress_settings_licenses',
			array()
		),
		/** Misc Settings */
		'misc' => apply_filters('rpress_settings_misc',
			array(
				'main' => array(
					'debug_mode' => array(
						'id'   => 'debug_mode',
						'name' => __( 'Debug Mode', 'restropress' ),
						'desc' => __( 'Check this box to enable debug mode. When enabled, debug messages will be logged and shown in RestroPress &rarr; Tools &rarr; Debug Log.', 'restropress' ),
						'type' => 'checkbox',
					),
					'uninstall_on_delete' => array(
						'id'   => 'uninstall_on_delete',
						'name' => __( 'Remove Data on Uninstall?', 'restropress' ),
						'desc' => __( 'Check this box if you would like RPRESS to completely remove all of its data when the plugin is deleted.', 'restropress' ),
						'type' => 'checkbox',
					),
				),
				'checkout' => array(
					'enforce_ssl' => array(
						'id'   => 'enforce_ssl',
						'name' => __( 'Enforce SSL on Checkout', 'restropress' ),
						'desc' => __( 'Check this to force users to be redirected to the secure checkout page. You must have an SSL certificate installed to use this option.', 'restropress' ),
						'type' => 'checkbox',
					),

					'show_register_form' => array(
						'id'      => 'show_register_form',
						'name'    => __( 'Show Register / Login Form?', 'restropress' ),
						'desc'    => __( 'Display the registration and login forms on the checkout page for non-logged-in users.', 'restropress' ),
						'type'    => 'select',
						'std'     => 'none',
						'options' => array(
							'both'         => __( 'Registration and Login Forms', 'restropress' ),
							'registration' => __( 'Registration Form Only', 'restropress' ),
							'login'        => __( 'Login Form Only', 'restropress' ),
							'none'         => __( 'None', 'restropress' ),
						),
					),

					'enable_cart_saving' => array(
						'id'   => 'enable_cart_saving',
						'name' => __( 'Enable Cart Saving', 'restropress' ),
						'desc' => __( 'Check this to enable cart saving on the checkout.', 'restropress' ),
						'type' => 'checkbox',
						'tooltip_title' => __( 'Cart Saving', 'restropress' ),
						'tooltip_desc'  => __( 'Cart saving allows shoppers to create a temporary link to their current shopping cart so they can come back to it later, or share it with someone.', 'restropress' ),
					),
				),
				'button_text' => array(
					'checkout_label' => array(
						'id'   => 'checkout_label',
						'name' => __( 'Complete Purchase Text', 'restropress' ),
						'desc' => __( 'The button label for completing a purchase.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'Purchase', 'restropress' ),
					),
					'add_to_cart_text' => array(
						'id'   => 'add_to_cart_text',
						'name' => __( 'Add to Cart Text', 'restropress' ),
						'desc' => __( 'Text shown on the Add to Cart Buttons.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'Add to Cart', 'restropress' ),
					),
					'checkout_button_text' => array(
						'id'   => 'checkout_button_text',
						'name' => __( 'Checkout Button Text', 'restropress' ),
						'desc' => __( 'Text shown on the Add to Cart Button when the product is already in the cart.', 'restropress' ),
						'type' => 'text',
						'std'  => _x( 'Checkout', 'text shown on the Add to Cart Button when the product is already in the cart', 'restropress' ),
					),
					'buy_now_text' => array(
						'id'   => 'buy_now_text',
						'name' => __( 'Buy Now Text', 'restropress' ),
						'desc' => __( 'Text shown on the Buy Now Buttons.', 'restropress' ),
						'type' => 'text',
						'std'  => __( 'Buy Now', 'restropress' ),
					),
				),
				'site_terms'     => array(
					'show_agree_to_terms' => array(
						'id'   => 'show_agree_to_terms',
						'name' => __( 'Agree to Terms', 'restropress' ),
						'desc' => __( 'Check this to show an agree to terms on checkout that users must agree to before creating orders.', 'restropress' ),
						'type' => 'checkbox',
					),
					'agree_label' => array(
						'id'   => 'agree_label',
						'name' => __( 'Agree to Terms Label', 'restropress' ),
						'desc' => __( 'Label shown next to the agree to terms checkbox.', 'restropress' ),
						'type' => 'text',
						'size' => 'regular',
					),
					'agree_text' => array(
						'id'   => 'agree_text',
						'name' => __( 'Agreement Text', 'restropress' ),
						'desc' => __( 'If Agree to Terms is checked, enter the agreement terms here.', 'restropress' ),
						'type' => 'rich_editor',
					),
				),
			)
		),
    'sms_notification' => apply_filters( 'rpress_settings_sms_notification', array() ),
	);

	$payment_statuses = rpress_get_payment_statuses();

	$rpress_settings['privacy']['export_erase'][] = array(
		'id'            => 'payment_privacy_status_action_header',
		'name'          => '<h3>' . __( 'Payment Status Actions', 'restropress' ) . '</h3>',
		'type'          => 'descriptive_text',
		'desc'          => __( 'When a user requests to be anonymized or removed from a site, these are the actions that will be taken on payments associated with their customer, by status.','restropress' ),
		'tooltip_title' => __( 'What settings should I use?', 'restropress' ),
		'tooltip_desc'  => __( 'By default, RestroPress sets suggested actions based on the Payment Status. These are purely recommendations, and you may need to change them to suit your store\'s needs. If you are unsure, you can safely leave these settings as is.','restropress' ),
	);

	$rpress_settings['privacy']['export_erase'][] = array(
		'id'   => 'payment_privacy_status_descriptive_text',
		'name' => '',
		'type' => 'descriptive_text',

	);

	$select_options = array(
		'none'      => __( 'No Action', 'restropress' ),
		'anonymize' => __( 'Anonymize', 'restropress' ),
		'delete'    => __( 'Delete', 'restropress' ),
	);

	foreach ( $payment_statuses as $status => $label ) {

		switch ( $status ) {

			case 'publish':
			case 'refunded':
			case 'revoked':
				$action = 'anonymize';
				break;

			case 'failed':
			case 'abandoned':
				$action = 'delete';
				break;

			case 'pending':
			case 'processing':
			default:
				$action = 'none';
				break;

		}

		$rpress_settings['privacy']['export_erase'][] = array(
			'id'      => 'payment_privacy_status_action_' . $status,
			'name'    => sprintf( _x( '%s Payments', 'payment status labels for the privacy export & erase settings: Pending Payments', 'restropress' ), $label ),
			'desc'    => '',
			'type'    => 'select',
			'options' => $select_options,
			'std'     => $action,
		);

	}

	if ( ! rpress_shop_supports_buy_now() ) {
		$rpress_settings['misc']['button_text']['buy_now_text']['disabled']      = true;
		$rpress_settings['misc']['button_text']['buy_now_text']['tooltip_title'] = __( 'Buy Now Disabled', 'restropress' );
		$rpress_settings['misc']['button_text']['buy_now_text']['tooltip_desc']  = __( 'Buy Now buttons are only available for stores that have a single supported gateway active and that do not use taxes.', 'restropress' );
	}
	return apply_filters( 'rpress_registered_settings', $rpress_settings );
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since 1.0
 *
 * @param array $input The value inputted in the field
 * @global array $rpress_options Array of all the RPRESS Options
 *
 * @return string $input Sanitized value
 */
function rpress_settings_sanitize( $input = array() ) {
	global $rpress_options;

	$doing_section = false;
	if ( ! empty( $_POST['_wp_http_referer'] ) ) {
		$doing_section = true;
	}

	$setting_types = rpress_get_registered_settings_types();
	$input         = $input ? $input : array();

	if ( $doing_section ) {

		parse_str( $_POST['_wp_http_referer'], $referrer ); // Pull out the tab and section
		$tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
		$section  = isset( $referrer['section'] ) ? $referrer['section'] : 'main';

		if ( ! empty( $_POST['rpress_section_override'] ) ) {
			$section = sanitize_text_field( $_POST['rpress_section_override'] );
		}

		$setting_types = rpress_get_registered_settings_types( $tab, $section );

		// Run a general sanitization for the tab for special fields (like taxes)
		$input = apply_filters( 'rpress_settings_' . $tab . '_sanitize', $input );

		// Run a general sanitization for the section so custom tabs with sub-sections can save special data
		$input = apply_filters( 'rpress_settings_' . $tab . '-' . $section . '_sanitize', $input );

	}

	// Merge our new settings with the existing
	$output = array_merge( $rpress_options, $input );

	foreach ( $setting_types as $key => $type ) {

		if ( empty( $type ) ) {
			continue;
		}

		// Some setting types are not actually settings, just keep moving along here
		$non_setting_types = apply_filters( 'rpress_non_setting_types', array(
			'header', 'descriptive_text', 'hook',
		) );

		if ( in_array( $type, $non_setting_types ) ) {
			continue;
		}

		if ( array_key_exists( $key, $output ) ) {
			$output[ $key ] = apply_filters( 'rpress_settings_sanitize_' . $type, $output[ $key ], $key );
			$output[ $key ] = apply_filters( 'rpress_settings_sanitize', $output[ $key ], $key );
		}

		if ( $doing_section ) {
			switch( $type ) {
				case 'checkbox':
				case 'gateways':
				case 'multicheck':
				case 'payment_icons':
					if ( array_key_exists( $key, $input ) && $output[ $key ] === '-1' ) {
						unset( $output[ $key ] );
					}
					break;
				case 'text':
					if ( array_key_exists( $key, $input ) && empty( $input[ $key ] ) ) {
						unset( $output[ $key ] );
					}
					break;
				default:
					if ( array_key_exists( $key, $input ) && empty( $input[ $key ] ) || ( array_key_exists( $key, $output ) && ! array_key_exists( $key, $input ) ) ) {
						unset( $output[ $key ] );
					}
					break;
			}
		} else {
			if ( empty( $input[ $key ] ) ) {
				unset( $output[ $key ] );
			}
		}

	}

	if ( $doing_section ) {
		add_settings_error( 'rpress-notices', '', __( 'Settings updated.', 'restropress' ), 'updated' );
	}

	return $output;
}

/**
 * Flattens the set of registered settings and their type so we can easily sanitize all the settings
 * in a much cleaner set of logic in rpress_settings_sanitize
 *
 * @since  1.0.0.5
 * @since 1.0.0 - Added the ability to filter setting types by tab and section
 *
 * @param $filtered_tab bool|string     A tab to filter setting types by.
 * @param $filtered_section bool|string A section to filter setting types by.
 * @return array Key is the setting ID, value is the type of setting it is registered as
 */
function rpress_get_registered_settings_types( $filtered_tab = false, $filtered_section = false ) {
	$settings      = rpress_get_registered_settings();
	$setting_types = array();
	foreach ( $settings as $tab_id => $tab ) {

		if ( false !== $filtered_tab && $filtered_tab !== $tab_id ) {
			continue;
		}

		foreach ( $tab as $section_id => $section_or_setting ) {

			// See if we have a setting registered at the tab level for backwards compatibility
			if ( false !== $filtered_section && is_array( $section_or_setting ) && array_key_exists( 'type', $section_or_setting ) ) {
				$setting_types[ $section_or_setting['id'] ] = $section_or_setting['type'];
				continue;
			}

			if ( false !== $filtered_section && $filtered_section !== $section_id ) {
				continue;
			}

			foreach ( $section_or_setting as $section => $section_settings ) {

				if ( ! empty( $section_settings['type'] ) ) {
					$setting_types[ $section_settings['id'] ] = $section_settings['type'];
				}

			}

		}

	}

	return $setting_types;
}

/**
 * Misc  Settings Sanitization
 *
 * @since  1.0.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitized value
 */
function rpress_settings_sanitize_misc_file_fooditems( $input ) {

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return $input;
	}

	if( rpress_get_file_fooditem_method() != $input['fooditem_method'] || ! rpress_htaccess_exists() ) {
		// Force the .htaccess files to be updated if the Download method was changed.
		rpress_create_protection_files( true, $input['fooditem_method'] );
	}

	return $input;
}
add_filter( 'rpress_settings_misc-file_fooditems_sanitize', 'rpress_settings_sanitize_misc_file_fooditems' );

/**
 * Misc Accounting Settings Sanitization
 *
 * @since  1.0.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitized value
 */
function rpress_settings_sanitize_misc_accounting( $input ) {

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return $input;
	}

	if( ! empty( $input['enable_sequential'] ) && ! rpress_get_option( 'enable_sequential' ) ) {

		// Shows an admin notice about upgrading previous order numbers
		RPRESS()->session->set( 'upgrade_sequential', '1' );

	}

	return $input;
}
add_filter( 'rpress_settings_misc-accounting_sanitize', 'rpress_settings_sanitize_misc_accounting' );

/**
 * Taxes Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * This also saves the tax rates table
 *
 * @since  1.0.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitized value
 */
function rpress_settings_sanitize_taxes( $input ) {

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return $input;
	}

	if( ! isset( $_POST['tax_rates'] ) ) {
		return $input;
	}

	$new_rates = ! empty( $_POST['tax_rates'] ) ? array_values( $_POST['tax_rates'] ) : array();

	update_option( 'rpress_tax_rates', $new_rates );

	return $input;
}
add_filter( 'rpress_settings_taxes_sanitize', 'rpress_settings_sanitize_taxes' );

/**
 * Payment Gateways Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 *
 * @since 1.0
 * @param array $input The value inputted in the field
 * @return string $input Sanitized value
 */
function rpress_settings_sanitize_gateways( $input ) {

	if ( ! current_user_can( 'manage_shop_settings' ) || empty( $input['default_gateway'] ) ) {
		return $input;
	}

	if ( empty( $input['gateways'] ) || '-1' == $input['gateways'] )  {

		add_settings_error( 'rpress-notices', '', __( 'Error setting default gateway. No gateways are enabled.', 'restropress' ) );
		unset( $input['default_gateway'] );

	} else if ( ! array_key_exists( $input['default_gateway'], $input['gateways'] ) ) {

		$enabled_gateways = $input['gateways'];
		$all_gateways     = rpress_get_payment_gateways();
		$selected_default = $all_gateways[ $input['default_gateway'] ];

		reset( $enabled_gateways );
		$first_gateway = key( $enabled_gateways );

		if ( $first_gateway ) {
			add_settings_error( 'rpress-notices', '', sprintf( __( '%s could not be set as the default gateway. It must first be enabled.', 'restropress' ), $selected_default['admin_label'] ), 'error' );
			$input['default_gateway'] = $first_gateway;
		}

	}

	return $input;
}
add_filter( 'rpress_settings_gateways_sanitize', 'rpress_settings_sanitize_gateways' );

/**
 * Sanitize text fields
 *
 * @since 1.0
 * @param array $input The field value
 * @return string $input Sanitized value
 */
function rpress_sanitize_text_field( $input ) {
	$tags = array(
		'p' => array(
			'class' => array(),
			'id'    => array(),
		),
		'span' => array(
			'class' => array(),
			'id'    => array(),
		),
		'a' => array(
			'href'   => array(),
			'target' => array(),
			'title'  => array(),
			'class'  => array(),
			'id'     => array(),
		),
		'strong' => array(),
		'em' => array(),
		'br' => array(),
		'img' => array(
			'src'   => array(),
			'title' => array(),
			'alt'   => array(),
			'id'    => array(),
		),
		'div' => array(
			'class' => array(),
			'id'    => array(),
		),
		'ul' => array(
			'class' => array(),
			'id'    => array(),
		),
		'li' => array(
			'class' => array(),
			'id'    => array(),
		)
	);

	$allowed_tags = apply_filters( 'rpress_allowed_html_tags', $tags );

	return trim( wp_kses( $input, $allowed_tags ) );
}
add_filter( 'rpress_settings_sanitize_text', 'rpress_sanitize_text_field' );

/**
 * Sanitize HTML Class Names
 *
 * @since 1.0.0.11
 * @param  string|array $class HTML Class Name(s)
 * @return string $class
 */
function rpress_sanitize_html_class( $class = '' ) {

	if ( is_string( $class ) ) {
		$class = sanitize_html_class( $class );
	} else if ( is_array( $class ) ) {
		$class = array_values( array_map( 'sanitize_html_class', $class ) );
		$class = implode( ' ', array_unique( $class ) );
	}

	return $class;

}

/**
 * Retrieve settings tabs
 *
 * @since 1.0
 * @return array $tabs
 */
function rpress_get_settings_tabs() {

	$settings = rpress_get_registered_settings();

	$tabs             = array();
	$tabs['general']  = __( 'General', 'restropress' );
	$tabs['gateways'] = __( 'Payment Gateways', 'restropress' );
	$tabs['emails']   = __( 'Emails', 'restropress' );
	$tabs['styles']   = __( 'Styles', 'restropress' );
	$tabs['taxes']    = __( 'Taxes', 'restropress' );
	$tabs['privacy']  = __( 'Privacy', 'restropress' );

	if( ! empty( $settings['extensions'] ) ) {
		$tabs['extensions'] = __( 'Extensions', 'restropress' );
	}

	if( ! empty( $settings['licenses'] ) ) {
		$tabs['licenses'] 	= __( 'Licenses', 'restropress' );
	}

	$tabs['misc']      		= __( 'Misc', 'restropress' );

	return apply_filters( 'rpress_settings_tabs', $tabs );
}

/**
 * Retrieve settings tabs
 *
 * @since  1.0.0
 * @return array $section
 */
function rpress_get_settings_tab_sections( $tab = false ) {

	$tabs     = array();
	$sections = rpress_get_registered_settings_sections();

	if( $tab && ! empty( $sections[ $tab ] ) ) {
		$tabs = $sections[ $tab ];
	} else if ( $tab ) {
		$tabs = array();
	}

	return $tabs;
}

/**
 * Get the settings sections for each tab
 * Uses a static to avoid running the filters on every request to this function
 *
 * @since  1.0.0
 * @return array Array of tabs and sections
 */
function rpress_get_registered_settings_sections() {

	static $sections = false;

	if ( false !== $sections ) {
		return $sections;
	}

	$sections = array(
		'general'    => apply_filters( 'rpress_settings_sections_general', array(
			'main'               => __( 'General', 'restropress' ),
			'currency'           => __( 'Currency', 'restropress' ),
			'order_notification'   => __( 'Order Notification', 'restropress' ),
			'service_options'   => __( 'Service Options', 'restropress' ),
			'checkout_options'   => __( 'Checkout Options', 'restropress' ),
		) ),
		'gateways'   => apply_filters( 'rpress_settings_sections_gateways', array(
			'main'               => __( 'General', 'restropress' ),
			'paypal'             => __( 'PayPal Standard', 'restropress' ),
		) ),
		'emails'     => apply_filters( 'rpress_settings_sections_emails', array(
			'main'               => __( 'General', 'restropress' ),
			'purchase_receipts'  => __( 'Purchase Receipts', 'restropress' ),
			'new_order_notifications' => __( 'New Order Notifications', 'restropress' ),
		) ),
		'styles'     => apply_filters( 'rpress_settings_sections_styles', array(
			'main'               => __( 'General', 'restropress' ),
		) ),
		'taxes'      => apply_filters( 'rpress_settings_sections_taxes', array(
			'main'               => __( 'General', 'restropress' ),
		) ),
		'extensions' => apply_filters( 'rpress_settings_sections_extensions', array(
			'main'               => __( 'Main', 'restropress' )
		) ),
		'licenses'   => apply_filters( 'rpress_settings_sections_licenses', array() ),
		'misc'       => apply_filters( 'rpress_settings_sections_misc', array(
			'main'               => __( 'Miscellaneous', 'restropress' ),
			'checkout'           => __( 'Checkout', 'restropress' ),
			'button_text'        => __( 'Button Text', 'restropress' ),
			'site_terms'         => __( 'Terms of Agreement', 'restropress' ),
		) ),
		'privacy'    => apply_filters( 'rpress_settings_section_privacy', array(
			'general'      => __( 'General', 'restropress' ),
			'export_erase' => __( 'Export & Erase', 'restropress' ),
		) ),
    'sms_notification' => apply_filters( 'rpress_settings_section_sms_notification', array() ),
	);

	$sections = apply_filters( 'rpress_settings_sections', $sections );

	return $sections;
}

/**
 * Retrieve a list of all published pages
 *
 * On large sites this can be expensive, so only load if on the settings page or $force is set to true
 *
 * @since  1.0.0
 * @param bool $force Force the pages to be loaded even if not on settings
 * @return array $pages_options An array of the pages
 */
function rpress_get_pages( $force = false ) {

	$pages_options = array( '' => '' ); // Blank option

	if( ( ! isset( $_GET['page'] ) || 'rpress-settings' != $_GET['page'] ) && ! $force ) {
		return $pages_options;
	}

	$pages = get_pages();
	if ( $pages ) {
		foreach ( $pages as $page ) {
			$pages_options[ $page->ID ] = $page->post_title;
		}
	}

	return $pages_options;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function rpress_header_callback( $args ) {
	echo apply_filters( 'rpress_after_setting_output', '', $args );
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_checkbox_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$name = '';
	} else {
		$name = 'name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$checked  = ! empty( $rpress_option ) ? checked( 1, $rpress_option, false ) : '';
	$html     = '<input type="hidden"' . $name . ' value="-1" />';
	$html    .= '<input type="checkbox" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"' . $name . ' value="1" ' . $checked . ' class="' . $class . '"/>';
	$html    .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_multicheck_callback( $args ) {

	$rpress_option = rpress_get_option( $args['id'] );

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '';
	if ( ! empty( $args['options'] ) ) {
		$html .= '<input type="hidden" name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" value="-1" />';
		foreach( $args['options'] as $key => $option ):
			if( isset( $rpress_option[ $key ] ) ) { $enabled = $option; } else { $enabled = NULL; }
			$html .= '<input name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" class="' . $class . '" type="checkbox" value="' . esc_attr( $option ) . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
			$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']">' . wp_kses_post( $option ) . '</label><br/>';
		endforeach;
		$html .= '<p class="description">' . $args['desc'] . '</p>';
	}

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Payment method icons callback
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_payment_icons_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<input type="hidden" name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" value="-1" />';
	if ( ! empty( $args['options'] ) ) {
		foreach( $args['options'] as $key => $option ) {

			if( isset( $rpress_option[ $key ] ) ) {
				$enabled = $option;
			} else {
				$enabled = NULL;
			}

			$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" class="rpress-settings-payment-icon-wrapper">';

				$html .= '<input name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" class="' . $class . '" type="checkbox" value="' . esc_attr( $option ) . '" ' . checked( $option, $enabled, false ) . '/>&nbsp;';

				if( rpress_string_is_image_url( $key ) ) {

					$html .= '<img class="payment-icon" src="' . esc_url( $key ) . '" style="width:32px;height:24px;position:relative;top:6px;margin-right:5px;"/>';

				} else {

					$card = strtolower( str_replace( ' ', '', $option ) );

					if( has_filter( 'rpress_accepted_payment_' . $card . '_image' ) ) {

						$image = apply_filters( 'rpress_accepted_payment_' . $card . '_image', '' );

					} else {

						$image       = rpress_locate_template( 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $card . '.png', false );
						$content_dir = WP_CONTENT_DIR;

						if( function_exists( 'wp_normalize_path' ) ) {

							// Replaces backslashes with forward slashes for Windows systems
							$image = wp_normalize_path( $image );
							$content_dir = wp_normalize_path( $content_dir );

						}

						$image = str_replace( $content_dir, content_url(), $image );

					}

					$html .= '<img class="payment-icon" src="' . esc_url( $image ) . '" style="width:32px;height:24px;position:relative;top:6px;margin-right:5px;"/>';
				}


			$html .= $option . '</label>';

		}
		$html .= '<p class="description" style="margin-top:16px;">' . wp_kses_post( $args['desc'] ) . '</p>';
	}

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_radio_callback( $args ) {
	$rpress_options = rpress_get_option( $args['id'] );

	$html = '';

	$class = rpress_sanitize_html_class( $args['field_class'] );

	foreach ( $args['options'] as $key => $option ) :
		$checked = false;

		if ( $rpress_options && $rpress_options == $key )
			$checked = true;
		elseif( isset( $args['std'] ) && $args['std'] == $key && ! $rpress_options )
			$checked = true;

		$html .= '<input name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" class="' . $class . '" type="radio" value="' . rpress_sanitize_key( $key ) . '" ' . checked(true, $checked, false) . '/>&nbsp;';
		$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']">' . esc_html( $option ) . '</label><br/>';
	endforeach;

	$html .= '<p class="description">' . apply_filters( 'rpress_after_setting_output', wp_kses_post( $args['desc'] ), $args ) . '</p>';

	echo $html;
}

/**
 * Gateways Callback
 *
 * Renders gateways fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_gateways_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<input type="hidden" name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" value="-1" />';

	foreach ( $args['options'] as $key => $option ) :
		if ( isset( $rpress_option[ $key ] ) )
			$enabled = '1';
		else
			$enabled = null;

		$html .= '<input name="rpress_settings[' . esc_attr( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']" class="' . $class . '" type="checkbox" value="1" ' . checked('1', $enabled, false) . '/>&nbsp;';
		$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . '][' . rpress_sanitize_key( $key ) . ']">' . esc_html( $option['admin_label'] ) . '</label><br/>';
	endforeach;
	$url_args  = array(
			'utm_source'   => 'settings',
			'utm_medium'   => 'gateways',
			'utm_campaign' => 'admin',
	);

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Gateways Callback (drop down)
 *
 * Renders gateways select menu
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_gateway_select_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '';

	$html .= '<select name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" class="' . $class . '">';

	foreach ( $args['options'] as $key => $option ) :
		$selected = isset( $rpress_option ) ? selected( $key, $rpress_option, false ) : '';
		$html .= '<option value="' . rpress_sanitize_key( $key ) . '"' . $selected . '>' . esc_html( $option['admin_label'] ) . '</option>';
	endforeach;

	$html .= '</select>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_text_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} elseif( ! empty( $args['allow_blank'] ) && empty( $rpress_option ) ) {
		$value = '';
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="rpress_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$disabled = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="text" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . $disabled . ' placeholder="' . esc_attr( $args['placeholder'] ) . '"/>';
	$html    .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Email Callback
 *
 * Renders email fields.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_email_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} elseif( ! empty( $args['allow_blank'] ) && empty( $rpress_option ) ) {
		$value = '';
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="rpress_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$disabled = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
	$readonly = $args['readonly'] === true ? ' readonly="readonly"' : '';
	$size     = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html     = '<input type="email" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"' . $readonly . $disabled . ' placeholder="' . esc_attr( $args['placeholder'] ) . '"/>';
	$html    .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_number_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	if ( isset( $args['faux'] ) && true === $args['faux'] ) {
		$args['readonly'] = true;
		$value = isset( $args['std'] ) ? $args['std'] : '';
		$name  = '';
	} else {
		$name = 'name="rpress_settings[' . esc_attr( $args['id'] ) . ']"';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" ' . $name . ' value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_textarea_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<textarea class="' . $class . ' large-text" cols="50" rows="5" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="rpress_settings[' . esc_attr( $args['id'] ) . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_password_callback( $args ) {
	$rpress_options = rpress_get_option( $args['id'] );

	if ( $rpress_options ) {
		$value = $rpress_options;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="password" class="' . $class . ' ' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="rpress_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function rpress_missing_callback($args) {
	printf(
		__( 'The callback function used for the %s setting is missing.', 'restropress' ),
		'<strong>' . $args['id'] . '</strong>'
	);
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_select_callback($args) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {

		// Properly set default fallback if the Select Field allows Multiple values
		if ( empty( $args['multiple'] ) ) {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		} else {
			$value = ! empty( $args['std'] ) ? $args['std'] : array();
		}

	}

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	if ( isset( $args['chosen'] ) ) {
		$class .= ' rpress-select-chosen';
	}

	// If the Select Field allows Multiple values, save as an Array
	$name_attr = 'rpress_settings[' . esc_attr( $args['id'] ) . ']';
	$name_attr = ( $args['multiple'] ) ? $name_attr . '[]' : $name_attr;

	$html = '<select id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="' . $name_attr . '" class="' . $class . '" data-placeholder="' . esc_html( $placeholder ) . '" ' . ( ( $args['multiple'] ) ? 'multiple="true"' : '' ) . '>';

	foreach ( $args['options'] as $option => $name ) {

		if ( ! $args['multiple'] ) {
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		} else {
			// Do an in_array() check to output selected attribute for Multiple
			$html .= '<option value="' . esc_attr( $option ) . '" ' . ( ( in_array( $option, $value ) ) ? 'selected="true"' : '' ) . '>' . esc_html( $name ) . '</option>';
		}

	}

	$html .= '</select>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_color_select_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<select id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" class="' . $class . '" name="rpress_settings[' . esc_attr( $args['id'] ) . ']"/>';

	foreach ( $args['options'] as $option => $color ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $color['label'] ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 */
function rpress_rich_editor_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		if( ! empty( $args['allow_blank'] ) && empty( $rpress_option ) ) {
			$value = '';
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}
	}

	$rows = isset( $args['size'] ) ? $args['size'] : 20;

	$class = rpress_sanitize_html_class( $args['field_class'] );

	ob_start();
	wp_editor( stripslashes( $value ), 'rpress_settings_' . esc_attr( $args['id'] ), array( 'textarea_name' => 'rpress_settings[' . esc_attr( $args['id'] ) . ']', 'textarea_rows' => absint( $rows ), 'editor_class' => $class ) );
	$html = ob_get_clean();

	$html .= '<br/><label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_upload_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		$value = isset($args['std']) ? $args['std'] : '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" class="' . $class . '" name="rpress_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<span>&nbsp;<input type="button" class="rpress_settings_upload_button button-secondary" value="' . __( 'Upload File', 'restropress' ) . '"/></span>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> ' . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_color_callback( $args ) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( $rpress_option ) {
		$value = $rpress_option;
	} else {
		$value = isset( $args['std'] ) ? $args['std'] : '';
	}

	$default = isset( $args['std'] ) ? $args['std'] : '';

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<input type="text" class="' . $class . ' rpress-color-picker" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="rpress_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}



/**
 * Shop States Callback
 *
 * Renders states drop down based on the currently selected country
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
function rpress_shop_states_callback($args) {
	$rpress_option = rpress_get_option( $args['id'] );

	if ( isset( $args['placeholder'] ) ) {
		$placeholder = $args['placeholder'];
	} else {
		$placeholder = '';
	}

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$states = rpress_get_shop_states();

	if ( $args['chosen'] ) {
		$class .= ' rpress-chosen';
	}

	if ( empty( $states ) ) {
		$class .= ' rpress-no-states';
	}

	$html = '<select id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="rpress_settings[' . esc_attr( $args['id'] ) . ']"' . $class . 'data-placeholder="' . esc_html( $placeholder ) . '"/>';

	foreach ( $states as $option => $name ) {
		$selected = isset( $rpress_option ) ? selected( $option, $rpress_option, false ) : '';
		$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
	}

	$html .= '</select>';
	$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Tax Rates Callback
 *
 * Renders tax rates table
 *
 * @since  1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function rpress_tax_rates_callback($args) {
	$rates = rpress_get_tax_rates();

	$class = rpress_sanitize_html_class( $args['field_class'] );

	ob_start(); ?>
	<p><?php echo $args['desc']; ?></p>
	<table id="rpress_tax_rates" class="wp-list-table widefat fixed posts <?php echo $class; ?>">
		<thead>
			<tr>
				<th scope="col" class="rpress_tax_country"><?php _e( 'Country', 'restropress' ); ?></th>
				<th scope="col" class="rpress_tax_state"><?php _e( 'State / Province', 'restropress' ); ?></th>
				<th scope="col" class="rpress_tax_global"><?php _e( 'Country Wide', 'restropress' ); ?></th>
				<th scope="col" class="rpress_tax_rate"><?php _e( 'Rate', 'restropress' ); ?><span alt="f223" class="rpress-help-tip dashicons dashicons-editor-help" title="<?php _e( '<strong>Regional tax rates: </strong>When a customer enters an address on checkout that matches the specified region for this tax rate, the cart tax will adjust automatically. Enter a percentage, such as 6.5 for 6.5%.', 'restropress' ); ?>"></span></th>
				<th scope="col"><?php _e( 'Remove', 'restropress' ); ?></th>
			</tr>
		</thead>
		<?php if( ! empty( $rates ) ) : ?>
			<?php foreach( $rates as $key => $rate ) : ?>
			<tr>
				<td class="rpress_tax_country">
					<?php
					echo RPRESS()->html->select( array(
						'options'          => rpress_get_country_list(),
						'name'             => 'tax_rates[' . rpress_sanitize_key( $key ) . '][country]',
						'selected'         => $rate['country'],
						'show_option_all'  => false,
						'show_option_none' => false,
						'class'            => 'rpress-tax-country',
						'chosen'           => false,
						'placeholder'      => __( 'Choose a country', 'restropress' )
					) );
					?>
				</td>
				<td class="rpress_tax_state">
					<?php
					$states = rpress_get_shop_states( $rate['country'] );
					if( ! empty( $states ) ) {
						echo RPRESS()->html->select( array(
							'options'          => $states,
							'name'             => 'tax_rates[' . rpress_sanitize_key( $key ) . '][state]',
							'selected'         => $rate['state'],
							'show_option_all'  => false,
							'show_option_none' => false,
							'chosen'           => false,
							'placeholder'      => __( 'Choose a state', 'restropress' )
						) );
					} else {
						echo RPRESS()->html->text( array(
							'name'  => 'tax_rates[' . rpress_sanitize_key( $key ) . '][state]', $rate['state'],
							'value' => ! empty( $rate['state'] ) ? $rate['state'] : '',
						) );
					}
					?>
				</td>
				<td class="rpress_tax_global">
					<input type="checkbox" name="tax_rates[<?php echo rpress_sanitize_key( $key ); ?>][global]" id="tax_rates[<?php echo rpress_sanitize_key( $key ); ?>][global]" value="1"<?php checked( true, ! empty( $rate['global'] ) ); ?>/>
					<label for="tax_rates[<?php echo rpress_sanitize_key( $key ); ?>][global]"><?php _e( 'Apply to whole country', 'restropress' ); ?></label>
				</td>
				<td class="rpress_tax_rate"><input type="number" class="small-text" step="0.0001" min="0.0" max="99" name="tax_rates[<?php echo rpress_sanitize_key( $key ); ?>][rate]" value="<?php echo esc_html( $rate['rate'] ); ?>"/></td>
				<td><span class="rpress_remove_tax_rate button-secondary"><?php _e( 'Remove Rate', 'restropress' ); ?></span></td>
			</tr>
			<?php endforeach; ?>
		<?php else : ?>
			<tr>
				<td class="rpress_tax_country">
					<?php
					echo RPRESS()->html->select( array(
						'options'          => rpress_get_country_list(),
						'name'             => 'tax_rates[0][country]',
						'selected'         => '',
						'show_option_all'  => false,
						'show_option_none' => false,
						'class'            => 'rpress-tax-country',
						'chosen'           => false,
						'placeholder'      => __( 'Choose a country', 'restropress' )
					) ); ?>
				</td>
				<td class="rpress_tax_state">
					<?php echo RPRESS()->html->text( array(
						'name' => 'tax_rates[0][state]'
					) ); ?>
				</td>
				<td class="rpress_tax_global">
					<input type="checkbox" name="tax_rates[0][global]" value="1"/>
					<label for="tax_rates[0][global]"><?php _e( 'Apply to whole country', 'restropress' ); ?></label>
				</td>
				<td class="rpress_tax_rate"><input type="number" class="small-text" step="0.0001" min="0.0" name="tax_rates[0][rate]" value=""/></td>
				<td><span class="rpress_remove_tax_rate button-secondary"><?php _e( 'Remove Rate', 'restropress' ); ?></span></td>
			</tr>
		<?php endif; ?>
	</table>
	<p>
		<span class="button-secondary" id="rpress_add_tax_rate"><?php _e( 'Add Tax Rate', 'restropress' ); ?></span>
	</p>
	<?php
	echo ob_get_clean();
}

/**
 * Descriptive text callback.
 *
 * Renders descriptive text onto the settings field.
 *
 * @since 1.0.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function rpress_descriptive_text_callback( $args ) {
	$html = wp_kses_post( $args['desc'] );

	echo apply_filters( 'rpress_after_setting_output', $html, $args );
}

/**
 * Registers the license field callback for Software Licensing
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 *
 * @return void
 */
if ( ! function_exists( 'rpress_license_key_callback' ) ) {
	function rpress_license_key_callback( $args ) {
		$rpress_option = rpress_get_option( $args['id'] );

		$messages = array();
		$license  = get_option( $args['options']['is_valid_license_option'] );

		if ( $rpress_option ) {
			$value = $rpress_option;
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if( ! empty( $license ) && is_object( $license ) ) {

			// activate_license 'invalid' on anything other than valid, so if there was an error capture it
			if ( false === $license->success ) {

				switch( $license->error ) {

					case 'expired' :

						$class = 'expired';
						$messages[] = sprintf(
							__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'restropress' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
							'https://restropress.com/checkout/?rpress_license_key=' . $value . '&utm_campaign=admin&utm_source=licenses&utm_medium=expired'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'revoked' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'restropress' ),
							'https://restropress.com/support?utm_campaign=admin&utm_source=licenses&utm_medium=revoked'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'missing' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'restropress' ),
							'https://restropress.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=missing'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'invalid' :
					case 'site_inactive' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'restropress' ),
							$args['name'],
							'https://restropress.com/your-account?utm_campaign=admin&utm_source=licenses&utm_medium=invalid'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'item_name_mismatch' :

						$class = 'error';
						$messages[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'restropress' ), $args['name'] );

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'no_activations_left':

						$class = 'error';
						$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'restropress' ), 'https://restropress.com/your-account/' );

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'license_not_activable':

						$class = 'error';
						$messages[] = __( 'The key you entered belongs to a bundle, please use the product specific license key.', 'restropress' );

						$license_status = 'license-' . $class . '-notice';
						break;

					default :

						$class = 'error';
						$error = ! empty(  $license->error ) ?  $license->error : __( 'unknown_error', 'restropress' );
						$messages[] = sprintf( __( 'There was an error with this license key: %s. Please <a href="%s">contact our support team</a>.', 'restropress' ), $error, 'https://magnigenie.com' );

						$license_status = 'license-' . $class . '-notice';
						break;
				}

			} else {

				switch( $license->license ) {

					case 'valid' :
					default:

						$class = 'valid';

						$now        = current_time( 'timestamp' );
						$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

						if( 'lifetime' === $license->expires ) {

							$messages[] = __( 'License key never expires.', 'restropress' );

							$license_status = 'license-lifetime-notice';

						} elseif( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

							$messages[] = sprintf(
								__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank">Renew your license key</a>.', 'restropress' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
								'https://magnigenie.com'
							);

							$license_status = 'license-expires-soon-notice';

						} else {

							$messages[] = sprintf(
								__( 'Your license key expires on %s.', 'restropress' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
							);

							$license_status = 'license-expiration-date-notice';

						}

						break;

				}

			}

		} else {
			$class = 'empty';

			$messages[] = sprintf(
				__( 'To receive updates, please enter your valid %s license key.', 'restropress' ),
				$args['name']
			);

			$license_status = null;
		}

		$class .= ' ' . rpress_sanitize_html_class( $args['field_class'] );

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
		$html = '<input type="text" class="' . sanitize_html_class( $size ) . '-text" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" name="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']" value="' . esc_attr( $value ) . '"/>';

		if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
			$html .= '<input type="submit" class="button-secondary" name="' . $args['id'] . '_deactivate" value="' . __( 'Deactivate License',  'restropress' ) . '"/>';
		}

		$html .= '<label for="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']"> '  . wp_kses_post( $args['desc'] ) . '</label>';

		if ( ! empty( $messages ) ) {
			foreach( $messages as $message ) {

				$html .= '<div class="rpress-license-data rpress-license-' . $class . ' ' . $license_status . '">';
					$html .= '<p>' . $message . '</p>';
				$html .= '</div>';

			}
		}

		wp_nonce_field( rpress_sanitize_key( $args['id'] ) . '-nonce', rpress_sanitize_key( $args['id'] ) . '-nonce' );

		echo $html;
	}
}

/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since 1.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function rpress_hook_callback( $args ) {
	do_action( 'rpress_' . $args['id'], $args );
}

/**
 * Set manage_shop_settings as the cap required to save RPRESS settings pages
 *
 * @since  1.0.0
 * @return string capability required
 */
function rpress_set_settings_cap() {
	return 'manage_shop_settings';
}
add_filter( 'option_page_capability_rpress_settings', 'rpress_set_settings_cap' );

function rpress_add_setting_tooltip( $html, $args ) {

	if ( ! empty( $args['tooltip_title'] ) && ! empty( $args['tooltip_desc'] ) ) {
		$tooltip = '<span alt="f223" class="rpress-help-tip dashicons dashicons-editor-help" title="<strong>' . $args['tooltip_title'] . '</strong><br />' . $args['tooltip_desc'] . '"></span>';
		$html .= $tooltip;
	}

	return $html;
}
add_filter( 'rpress_after_setting_output', 'rpress_add_setting_tooltip', 10, 2 );
