<?php
/**
 * Webhooks.
 *
 * @package RPRESS_Stripe
 * @since   1.0
 */

/**
 * Listen for Stripe Webhooks.
 *
 * @since 1.0
 */
function rpress_stripe_event_listener() {
	if ( ! isset( $_GET['rpress-listener'] ) || 'stripe' !== $_GET['rpress-listener'] ) {
		return;
	}

	try {
		// Retrieve the request's body and parse it as JSON.
		$body = @file_get_contents( 'php://input' );
		$event = json_decode( $body );

		if ( isset( $event->id ) ) {
			$event = rpress_stripe_api_request( 'Event', 'retrieve', $event->id );
		} else {
			throw new \Exception( esc_html__( 'Unable to find Event', 'rpstripe' ) );
		}

		// Handle events.
		//
		switch ( $event->type ) {

			// Charge succeeded. Update RPRESS Payment address.
			case 'charge.succeeded' :
				$charge     = $event->data->object;
				$payment_id = rpress_get_purchase_id_by_transaction_id( $charge->id );
				$payment    = new RPRESS_Payment( $payment_id );

				if ( $payment && $payment->ID > 0 ) {
					$payment->address = array(
						'line1'   => $charge->billing_details->address->line1,
						'line2'   => $charge->billing_details->address->line2,
						'state'   => $charge->billing_details->address->state,
						'city'    => $charge->billing_details->address->city,
						'zip'     => $charge->billing_details->address->postal_code,
						'country' => $charge->billing_details->address->country,
					);

					$payment->save();
				}

				break;

			// Charge refunded. Ensure RPRESS Payment status is correct.
			case 'charge.refunded' :
				$charge = $event->data->object;
				$payment_id = rpress_get_purchase_id_by_transaction_id( $charge->id );
				$payment    = new RPRESS_Payment( $payment_id );

				// This is an uncaptured PaymentIntent, not a true refund.
				if ( ! $charge->captured ) {
					return;
				}

				if ( ! $charge->refunded ) {
					return;
				}

				if ( $payment && $payment->ID > 0 ) {
					$payment->status = 'refunded';
					$payment->add_note( __( 'Charge refunded in Stripe.', ' rpress-stripe' ) );
					$payment->save();
				}

				break;

			// Review started.
			case 'review.opened' :
				$is_live = ! rpress_is_test_mode();
				$review  = $event->data->object;

				// Make sure the modes match.
				if ( $is_live !== $review->livemode ) {
					return;
				}

				$charge = $review->charge;

				// Get the charge from the PaymentIntent.
				if ( ! $charge ) {
					$payment_intent = $review->payment_intent;

					if ( ! $payment_intent ) {
						return;
					}

					$payment_intent = rpress_stripe_api_request( 'PaymentIntent', 'retrieve', $payment_intent );
					$charge         = $payment_intent->charges->data[0]->id;
				}

				$payment_id = rpress_get_purchase_id_by_transaction_id( $charge );
				$payment    = new RPRESS_Payment( $payment_id );

				if ( $payment && $payment->ID > 0 ) {
					$payment->add_note( sprintf( __( 'Stripe Radar review opened with a reason of %s.', 'rpstripe' ), $review->reason ) );
					$payment->save();

					do_action( 'rpress_stripe_review_opened', $review, $payment_id );
				}

				break;

			// Review closed.
			case 'review.closed' :
				$is_live = ! rpress_is_test_mode();
				$review  = $event->data->object;

				// Make sure the modes match
				if ( $is_live !== $review->livemode ) {
					return;
				}

				$charge = $review->charge;

				// Get the charge from the PaymentIntent.
				if ( ! $charge ) {
					$payment_intent = $review->payment_intent;

					if ( ! $payment_intent ) {
						return;
					}

					$payment_intent = rpress_stripe_api_request( 'PaymentIntent', 'retrieve', $payment_intent );
					$charge         = $payment_intent->charges->data[0]->id;
				}

				$payment_id = rpress_get_purchase_id_by_transaction_id( $charge );
				$payment    = new RPRESS_Payment( $payment_id );

				if ( $payment && $payment->ID > 0 ) {
					$payment->add_note( sprintf( __( 'Stripe Radar review closed with a reason of %s.', 'rpstripe' ), $review->reason ) );
					$payment->save();

					do_action( 'rpress_stripe_review_closed', $review, $payment_id );
				}

				break;
		}

		do_action( 'rpress_stripe_event_' . $event->type, $event );

		// Nothing failed, mark complete.
		status_header( 200 );
		die( '1' );
	
	// Fail, allow a retry.
	} catch( \Exception $e ) {
		status_header( 500 );
		die( '-2' );
	}
}
add_action( 'init', 'rpress_stripe_event_listener' );
