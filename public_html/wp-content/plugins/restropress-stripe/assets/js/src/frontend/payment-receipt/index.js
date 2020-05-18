/**
 * Internal dependencies
 */
import { updatePaymentMethodForm } from './update-payment-method';

export function setup() {
	if ( ! document.getElementById( 'rpress-stripe-update-payment-method' ) ) {
		return;
	}

	updatePaymentMethodForm();
}
