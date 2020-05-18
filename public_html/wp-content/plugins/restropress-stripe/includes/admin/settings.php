<?php

/**
* Register our settings section
*
* @return array
*/
function rpresss_settings_section( $sections ) {
  $sections['rpress-stripe'] = __( 'Stripe', 'rpresss' );

  return $sections;
}
add_filter( 'rpress_settings_sections_gateways', 'rpresss_settings_section' );

/**
 * Register the gateway settings
 *
 * @access      public
 * @since       1.0
 * @return      array
 */

function rpresss_add_settings( $settings ) {

	// Build the Stripe Connect OAuth URL
	// $stripe_connect_url = add_query_arg( array(
	// 	'live_mode' => (int) ! rpress_is_test_mode(),
	// 	'state' => str_pad( wp_rand( wp_rand(), PHP_INT_MAX ), 100, wp_rand(), STR_PAD_BOTH ),
	// 	'customer_site_url' => admin_url( 'edit.php?post_type=fooditem&page=rpress-settings&tab=gateways&section=rpress-stripe' ),
	// ), 'https://restropress.com/?rpress_gateway_connect_init=stripe_connect' );

	$test_mode = rpress_is_test_mode();

  $test_key = rpress_get_option( 'test_publishable_key' );
  $live_key = rpress_get_option( 'live_publishable_key' );

  $live_text = _x( 'live', 'current value for test mode', 'rpresss' );
  $test_text = _x( 'test', 'current value for test mode', 'rpresss' );

	$mode = $live_text;
  if( $test_mode ) {
    $mode = $test_text;
  }

  $stripe_connect_desc = '';

	$stripe_settings = array(
		array(
      'id'   => 'stripe_settings',
      'name'  => '<strong>' . __( 'Stripe Settings', 'rpress-stripe' ) . '</strong>',
      'desc'  => __( 'Configure the Stripe settings', 'rpress-stripe' ),
      'type'  => 'header'
    ),
		array(
      'id'   => 'test_secret_key',
      'name'  => __( 'Test Secret Key', 'rpress-stripe' ),
      'desc'  => __( 'Enter your test secret key, found in your Stripe Account Settings', 'rpress-stripe' ),
      'type'  => 'text',
      'size'  => 'regular',
      'class' => 'rpresss-api-key-row',
    ),
    array(
      'id'   => 'test_publishable_key',
      'name'  => __( 'Test Publishable Key', 'rpress-stripe' ),
      'desc'  => __( 'Enter your test publishable key, found in your Stripe Account Settings', 'rpress-stripe' ),
      'type'  => 'text',
      'size'  => 'regular',
      'class' => 'rpresss-api-key-row',
    ),
		array(
      'id'   => 'live_secret_key',
      'name'  => __( 'Live Secret Key', 'rpress-stripe' ),
      'desc'  => __( 'Enter your live secret key, found in your Stripe Account Settings', 'rpress-stripe' ),
      'type'  => 'text',
      'size'  => 'regular',
      'class' => 'rpresss-api-key-row',
    ),
    array(
      'id'   => 'live_publishable_key',
      'name'  => __( 'Live Publishable Key', 'rpress-stripe' ),
      'desc'  => __( 'Enter your live publishable key, found in your Stripe Account Settings', 'rpress-stripe' ),
      'type'  => 'text',
      'size'  => 'regular',
      'class' => 'rpresss-api-key-row',
    ),
		array(
			'id'    => 'stripe_webhook_description',
			'type'  => 'descriptive_text',
			'name'  => __( 'Webhooks', 'rpstripe' ),
			'desc'  =>
				'<p>' . sprintf( __( 'In order for Stripe to function completely, you must configure your Stripe webhooks. Visit your <a href="%s" target="_blank">account dashboard</a> to configure them. Please add a webhook endpoint for the URL below.', 'rpstripe' ), 'https://dashboard.stripe.com/account/webhooks' ) . '</p>' .
				'<p><strong>' . sprintf( __( 'Webhook URL: %s', 'rpstripe' ), home_url( 'index.php?rpress-listener=stripe' ) ) . '</strong></p>'
		),
		array(
			'id'    => 'stripe_billing_fields',
			'name'  => __( 'Billing Address Display', 'rpstripe' ),
			'desc'  => __( 'Select how you would like to display the billing address fields on the checkout form. <p><strong>Notes</strong>:</p><p>If taxes are enabled, this option cannot be changed from "Full address".</p><p>If set to "No address fields", you <strong>must</strong> disable "zip code verification" in your Stripe account.</p>', 'rpstripe' ),
			'type'  => 'select',
			'options' => array(
				'full'        => __( 'Full address', 'rpstripe' ),
				'zip_country' => __( 'Zip / Postal Code and Country only', 'rpstripe' ),
				'none'        => __( 'No address fields', 'rpstripe' )
			),
			'std'   => 'full'
		),
 		array(
			'id'   => 'stripe_use_existing_cards',
			'name'  => __( 'Show previously used cards?', 'rpstripe' ),
			'desc'  => __( 'When enabled, provides logged in customers with a list of previously used payment methods, for faster checkout.', 'rpstripe' ),
			'type'  => 'checkbox'
		),
 		array(
 			'id'   => 'stripe_statement_descriptor',
 			'name' => __( 'Statement Descriptor', 'rpstripe' ),
 			'desc' => __( 'Choose how charges will appear on customer\'s credit card statements. <em>Max 22 characters</em>', 'rpstripe' ),
 			'type' => 'text',
 		),
		array(
			'id'   => 'stripe_preapprove_only',
			'name'  => __( 'Preapprove Only?', 'rpstripe' ),
			'desc'  => __( 'Check this if you would like to preapprove payments but not charge until a later date.', 'rpstripe' ),
			'type'  => 'checkbox',
			'tooltip_title' => __( 'What does checking preapprove do?', 'rpstripe' ),
			'tooltip_desc'  => __( 'If you choose this option, Stripe will not charge the customer right away after checkout, and the payment status will be set to preapproved in RestroPress. You (as the admin) can then manually change the status to Complete by going to Payment History and changing the status of the payment to Complete. Once you change it to Complete, the customer will be charged. Note that most typical stores will not need this option.', 'rpstripe' ),
		),
		array(
			'id' => 'stripe_restrict_assets',
			'name' => ( __( 'Restrict Stripe Assets', 'rpstripe' ) ),
			'desc' => ( __( 'Only load Stripe.com hosted assets on pages that specifically utilize Stripe functionality.', 'rpstripe' ) ),
			'type' => 'checkbox',
			'tooltip_title' => __( 'Loading Javascript from Stripe', 'rpstripe' ),
			'tooltip_desc' => __( 'Stripe advises that their Javascript library be loaded on every page to take advantage of their advanced fraud detection rules. If you are not concerned with this, enable this setting to only load the Javascript when necessary. Read more about Stripe\'s recommended setup here: https://stripe.com/docs/web/setup.', 'rpstripe' ),
		)
	);


	if ( version_compare( RP_VERSION, 1.0, '>=' ) || version_compare( RPRESS_VERSION, 1.0, '>=' )  ) {
		$stripe_settings = array( 'rpress-stripe' => $stripe_settings );

		// Set up the new setting field for the Test Mode toggle notice
		$notice = array(
			'stripe_connect_test_mode_toggle_notice' => array(
				'id' => 'stripe_connect_test_mode_toggle_notice',
				'desc' => '<p>' . __( 'You just toggled the test mode option. Save your changes using the Save Changes button below, then connect your Stripe account using the "Connect with Stripe" button when the page reloads.', 'rpstripe' ) . '</p>',
				'type' => 'stripe_connect_notice',
				'field_class' => 'rpress-hidden',
			)
		);

		// Insert the new setting after the Test Mode checkbox
		$position = array_search( 'test_mode', array_keys( $settings['main'] ), true );
    $settings = array_merge(
      array_slice( $settings['main'], $position, 1, true ),
      $notice,
      $settings
    );
	}

	return array_merge( $settings, $stripe_settings );
}
add_filter( 'rpress_settings_gateways', 'rpresss_add_settings' );

/**
 * Force full billing address display when taxes are enabled
 *
 * @access      public
 * @since       1.1
 * @return      string
 */
function rpress_stripe_sanitize_stripe_billing_fields_save( $value, $key ) {

	if( 'stripe_billing_fields' == $key && rpress_use_taxes() ) {

		$value = 'full';

	}

	return $value;

}
add_filter( 'rpress_settings_sanitize_select', 'rpress_stripe_sanitize_stripe_billing_fields_save', 10, 2 );

/**
 * Filter the output of the statement descriptor option to add a max length to the text string
 *
 * @since 1.1
 * @param $html string The full html for the setting output
 * @param $args array  The original arguments passed in to output the html
 *
 * @return string
 */
function rpress_stripe_max_length_statement_descriptor( $html, $args ) {
	if ( 'stripe_statement_descriptor' !== $args['id'] ) {
		return $html;
	}

	$html = str_replace( '<input type="text"', '<input type="text" maxlength="22"', $html );

	return $html;
}
add_filter( 'rpress_after_setting_output', 'rpress_stripe_max_length_statement_descriptor', 10, 2 );

/**
 * Callback for the stripe_connect_notice field type.
 *
 * @since 1.1
 *
 * @param array $args The setting field arguments
 */
function rpress_stripe_connect_notice_callback( $args ) {

	$value = isset( $args['desc'] ) ? $args['desc'] : '';

	$class = rpress_sanitize_html_class( $args['field_class'] );

	$html = '<div class="'.$class.'" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']">' . $value . '</div>';

	echo $html;
}

/**
 * Callback for the stripe_checkout_notice field type.
 *
 * @since 1.1
 *
 * @param array $args The setting field arguments
 */
function rpress_stripe_checkout_notice_callback( $args ) {
	$value = isset( $args['desc'] ) ? $args['desc'] : '';

	$html = '<div class="notice notice-warning inline' . rpress_sanitize_html_class( $args['field_class'] ) . '" id="rpress_settings[' . rpress_sanitize_key( $args['id'] ) . ']">' . wpautop( $value ) . '</div>';

	echo $html;
}

/**
 * Listens for Stripe Connect completion requests and saves the Stripe API keys.
 *
 * @since 1.1
 */
function rpresss_process_gateway_connect_completion() {

	if( ! isset( $_GET['rpress_gateway_connect_completion'] ) || 'stripe_connect' !== $_GET['rpress_gateway_connect_completion'] || ! isset( $_GET['state'] ) ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return;
	}

	if( headers_sent() ) {
		return;
	}

	$rpress_credentials_url = add_query_arg( array(
		'live_mode' => (int) ! rpress_is_test_mode(),
		'state' => sanitize_text_field( $_GET['state'] ),
		'customer_site_url' => admin_url( 'edit.php?post_type=fooditem' ),
	), 'https://restropress.com/?rpress_gateway_connect_credentials=stripe_connect' );

	$response = wp_remote_get( esc_url_raw( $rpress_credentials_url ) );

	if( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
		$message = '<p>' . sprintf( __( 'There was an error getting your Stripe credentials. Please <a href="%s">try again</a>. If you continue to have this problem, please contact support.', 'rpstripe' ), esc_url( admin_url( 'edit.php?post_type=fooditem&page=rpress-settings&tab=gateways&section=rpress-stripe' ) ) ) . '</p>';
		wp_die( $message );
	}

	$data = json_decode( $response['body'], true );
	$data = $data['data'];

	if( rpress_is_test_mode() ) {
		rpress_update_option( 'test_publishable_key', sanitize_text_field( $data['publishable_key'] ) );
		rpress_update_option( 'test_secret_key', sanitize_text_field( $data['secret_key'] ) );
	} else {
		rpress_update_option( 'live_publishable_key', sanitize_text_field( $data['publishable_key'] ) );
		rpress_update_option( 'live_secret_key', sanitize_text_field( $data['secret_key'] ) );
	}

	rpress_update_option( 'stripe_connect_account_id', sanitize_text_field( $data['stripe_user_id'] ) );
	wp_redirect( esc_url_raw( admin_url( 'edit.php?post_type=fooditem&page=rpress-settings&tab=gateways&section=rpress-stripe' ) ) );
	exit;

}
add_action( 'admin_init', 'rpresss_process_gateway_connect_completion' );