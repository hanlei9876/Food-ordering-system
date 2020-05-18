<?php

/**
 * Removes Stripe from active gateways if Recurring version is < 2.9.
 *
 * @since 1.1
 *
 * @param array $enabled_gateways Enabled gateways that allow purchasing.
 * @return array
 */
function rpress_stripe_require_recurring_290( $enabled_gateways ) {
	if ( 
		isset( $enabled_gateways['stripe'] ) &&
		defined( 'RPRESS_RECURRING_VERSION' ) &&
		! version_compare( RPRESS_RECURRING_VERSION, '2.8.8', '>' )
	) {
		unset( $enabled_gateways['stripe'] );
	}

	return $enabled_gateways;
}
add_filter( 'rpress_enabled_payment_gateways', 'rpress_stripe_require_recurring_290', 20 );

/**
 * Register our new payment status labels for RPRESS
 *
 * @since 1.1
 * @return array
 */
function rpress_stripe_payment_status_labels( $statuses ) {
	$statuses['preapproval']         = __( 'Preapproved', 'rpstripe' );
	$statuses['preapproval_pending'] = __( 'Preapproval Pending', 'rpstripe' );
	$statuses['cancelled']           = __( 'Cancelled', 'rpstripe' );
	return $statuses;
}
add_filter( 'rpress_payment_statuses', 'rpress_stripe_payment_status_labels' );

/**
 * Sets the stripe-checkout parameter if the direct parameter is present in the [purchase_link] short code
 *
 * @since  1.1
 * @return array
 */
function rpress_stripe_purchase_link_shortcode_atts( $out, $pairs, $atts ) {

	if( ! empty( $out['direct'] ) ) {

		$out['stripe-checkout'] = true;
		$out['direct'] = true;

	} else {

		foreach( $atts as $key => $value ) {
			if( false !== strpos( $value, 'stripe-checkout' ) ) {
				$out['stripe-checkout'] = true;
				$out['direct'] = true;
			}
		}

	}

	return $out;
}
add_filter( 'shortcode_atts_purchase_link', 'rpress_stripe_purchase_link_shortcode_atts', 10, 3 );

/**
 * Sets the stripe-checkout parameter if the direct parameter is present in rpress_get_purchase_link()
 *
 * @since  1.1
 * @return array
 */
function rpress_stripe_purchase_link_atts( $args ) {

	if( ! empty( $args['direct'] ) && rpress_is_gateway_active( 'stripe' ) ) {

		$args['stripe-checkout'] = true;
		$args['direct'] = true;
	}

	return $args;
}
add_filter( 'rpress_purchase_link_args', 'rpress_stripe_purchase_link_atts', 10 );

/**
 * Injects the Stripe token and customer email into the pre-gateway data
 *
 * @since  1.1
 * @return array
 */
function rpress_stripe_straight_to_gateway_data( $purchase_data ) {
	$purchase_data['gateway'] = 'stripe';
	$_REQUEST['rpress-gateway']  = 'stripe';

	return $purchase_data;
}
add_filter( 'rpress_straight_to_gateway_purchase_data', 'rpress_stripe_straight_to_gateway_data' );

/**
 * Process the POST Data for the Credit Card Form, if a token wasn't supplied
 *
 * @since  1.1
 * @return array The credit card data from the $_POST
 */
function rpress_stripe_process_post_data( $purchase_data ) {
	if ( ! isset( $purchase_data['gateway'] ) || 'stripe' !== $purchase_data['gateway'] ) {
		return;
	}

	if ( isset( $_POST['rpress_stripe_existing_card'] ) && 'new' !== $_POST['rpress_stripe_existing_card'] ) {
		return;
	}

	// Require a name for new cards.
	if ( ! isset( $_POST['card_name'] ) || strlen( trim( $_POST['card_name'] ) ) === 0 ) {
		rpress_set_error( 'no_card_name', __( 'Please enter a name for the credit card.', 'rpstripe' ) );
	}
}
add_action( 'rpress_checkout_error_checks', 'rpress_stripe_process_post_data' );

/**
 * Retrieves the locale used for Checkout modal window
 *
 * @since  1.1
 * @return string The locale to use
 */
function rpress_stripe_get_stripe_checkout_locale() {
	return apply_filters( 'rpress_stripe_checkout_locale', 'auto' );
}