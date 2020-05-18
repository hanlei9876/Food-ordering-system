/* global $, rpress_scripts */

/**
 * Internal dependencies
 */
import { paymentForm } from './payment.js';
import { paymentMethods } from './payment-methods.js';

export * from './payment.js';
export * from './payment-methods.js';

export function setup() {
	if ( '1' !== rpress_scripts.is_checkout ) {
		return;
	}

	// Initial load for single gateway.
	const singleGateway = document.querySelector( 'input[name="rpress-gateway"]' );

	if ( singleGateway && 'stripe' === singleGateway.value ) {
		paymentForm();
		paymentMethods();
	}

	// Gateway switch.
	$( document.body ).on( 'rpress_gateway_loaded', ( e, gateway ) => {
		if ( 'stripe' !== gateway ) {
			return;
		}

		paymentForm();
		paymentMethods();
	} );
}
