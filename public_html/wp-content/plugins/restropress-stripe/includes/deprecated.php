<?php
/**
 * Manage deprecations.
 *
 * @package RPRESS_Stripe
 * @since   1.1
 */

/**
 * Process stripe checkout submission
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function rpress_stripe_process_stripe_payment( $purchase_data ) {
	_rpress_deprecated_function( 'rpress_stripe_process_stripe_payment', '2.7.0', 'rpress_stripe_process_purchase_form', debug_backtrace() );

	return rpress_stripe_process_purchase_form( $purchase_data );
}
