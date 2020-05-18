<?php
/**
 * Payment receipt.
 *
 * @package RPRESS_Stripe
 * @since   2.7.0
 */

/**
 * Output a Payment authorization form in the Payment Receipt.
 *
 * @param WP_Post $payment Payment.
 */
function rpress_stripe_payment_receipt_authorize_payment_form( $payment ) {
	// RPRESS 3.0 compat.
	if ( is_a( $payment, 'WP_Post' ) ) {
		$payment = rpress_get_payment( $payment->ID );
	}

	$customer_id = $payment->get_meta( '_rpress_stripe_customer_id' );
	$payment_intent_id = $payment->get_meta( '_rpress_stripe_payment_intent_id' );

	if ( empty( $customer_id ) || empty( $payment_intent_id ) ) {
		return false;
	}

	if ( 'preapproval_pending' !== $payment->status ) {
		return false;
	}

	$payment_intent = rpress_stripe_api_request( 'PaymentIntent', 'retrieve', $payment_intent_id );

	rpress_stripe_js( true );
?>

<form
	id="rpress-stripe-update-payment-method"
	data-payment-intent="<?php echo esc_attr( $payment_intent->id ); ?>"
	<?php if ( isset( $payment_intent->last_payment_error ) && isset( $payment_intent->last_payment_error->payment_method ) ) : ?>
	data-payment-method="<?php echo esc_attr( $payment_intent->last_payment_error->payment_method->id ); ?>"
	<?php endif; ?>
>
	<h3>Authorize Payment</h3>
	<p><?php esc_html_e( 'To finalize your preapproved purchase, please confirm your payment method.', 'rpstripe' ); ?></p>

	<div id="rpress_checkout_form_wrap">
		<?php
		do_action( 'rpress_stripe_cc_form' );
		?>

		<p>
			<input
				id="rpress-stripe-update-payment-method-submit"
				type="submit"
				data-loading="<?php echo esc_attr( 'Please Wait…', 'rpstripe' ); ?>"
				data-submit="<?php echo esc_attr( 'Authorize Payment', 'rpstripe' ); ?>"
				value="<?php echo esc_attr( 'Authorize Payment', 'rpstripe' ); ?>"
				class="button rpress-button"
			/>
		</p>

		<div id="rpress-stripe-update-payment-method-errors"></div>

	</div>
</form>

<?php
}
add_action( 'rpress_payment_receipt_after_table', 'rpress_stripe_payment_receipt_authorize_payment_form' );
