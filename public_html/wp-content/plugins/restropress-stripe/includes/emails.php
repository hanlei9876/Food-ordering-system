<?php
/**
 * Payment emails.
 *
 * @package RPRESS_Stripe
 * @since   1.1
 */

/**
 * Notify a customer that a Payment needs further action.
 *
 * @since 1.1
 *
 * @param int $payment_id RPRESS Payment ID.
 */
function rpress_stripe_preapproved_payment_needs_action_notification( $payment_id ) {
	$payment      = rpress_get_payment( $payment_id );
	$payment_data = $payment->get_meta( '_rpress_payment_meta', true );

	$from_name    = rpress_get_option( 'from_name', wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ) );
	$from_name    = apply_filters( 'rpress_purchase_from_name', $from_name, $payment_id, $payment_data );
	$from_email   = rpress_get_option( 'from_email', get_bloginfo( 'admin_email' ) );
	$from_email   = apply_filters( 'rpress_purchase_from_address', $from_email, $payment_id, $payment_data );

	if ( empty( $to_email ) ) {
		$to_email = $payment->email;
	}

	$subject = esc_html__( 'Your Preapproved Payment Requires Action', 'rpstripe' );
	$heading = rpress_do_email_tags( esc_html__( 'Payment Requires Action', 'rpstripe' ), $payment_id );

	$message  = esc_html__( 'Dear {name},', 'rpstripe' ) . "\n\n";
	$message .= esc_html__( 'Your preapproved payment requires further action before your purchase can be completed. Please click the link below to take finalize your purchase', 'rpstripe' ) . "\n\n";
	$message .= esc_url( add_query_arg( 'payment_key', $payment->key, rpress_get_success_page_uri() ) );
	$message  = rpress_do_email_tags( $message, $payment_id );

	$message = apply_filters( 'rpress_email_template_wpautop', true ) ? wpautop( $message ) : $message;

	$emails = RPRESS()->emails;

	$emails->__set( 'from_name', $from_name );
	$emails->__set( 'from_email', $from_email );
	$emails->__set( 'heading', $heading );

	$headers = $emails->get_headers();
	$emails->__set( 'headers', $headers );

	$emails->send( $to_email, $subject, $message );
}
add_action( 'rpress_stripe_preapproved_payment_needs_action', 'rpress_stripe_preapproved_payment_needs_action_notification' );
