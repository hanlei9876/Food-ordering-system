/* global $ */

/**
 * Internal dependencies.
 */
import { forEach, getNextSiblings } from 'utils'; // eslint-disable-line @wordpress/dependency-group

/**
 *
 */
export function paymentMethods() {
	// Toggle only shows if using Full Address (for some reason).
	if ( getBillingFieldsToggle() ) {
		// Hide fields initially.
		toggleBillingFields( false );

		/**
		 * Binds change event to "Update billing address" toggle to show/hide address fields.
		 *
		 * @param {Event} e Change event.
		 */
		getBillingFieldsToggle().addEventListener( 'change', function( e ) {
			return toggleBillingFields( e.target.checked );
		} );
	}

	// Payment method toggles.
	const existingPaymentMethods = document.querySelectorAll( '.rpress-stripe-existing-card' );

	if ( 0 !== existingPaymentMethods.length ) {
		forEach( existingPaymentMethods, function( existingPaymentMethod ) {
			/**
			 * Binds change event to credit card toggles.
			 *
			 * @param {Event} e Change event.
			 */
			return existingPaymentMethod.addEventListener( 'change', function( e ) {
				return onPaymentSourceChange( e.target );
			} );
		} );

		// Simulate change of payment method to populate current fields.
		let currentPaymentMethod = document.querySelector( '.rpress-stripe-existing-card:checked' );

		if ( ! currentPaymentMethod ) {
			currentPaymentMethod = document.querySelector( '.rpress-stripe-existing-card:first-of-type' );
			currentPaymentMethod.checked = true;
		}

		const paymentMethodChangeEvent = document.createEvent( 'Event' );
		paymentMethodChangeEvent.initEvent( 'change', true, false );
		currentPaymentMethod.dispatchEvent( paymentMethodChangeEvent );
	}
}

/**
 * Determines if the billing fields can be toggled.
 *
 * @return {Bool} True if the toggle exists.
 */
function getBillingFieldsToggle() {
	return document.getElementById( 'rpress-stripe-update-billing-address' );
}

/**
 * Toggles billing fields visiblity.
 *
 * Assumes the toggle control is the first item in the "Billing Details" fieldset.
 *
 * @param {Bool} isVisible Billing item visibility.
 */
function toggleBillingFields( isVisible ) {
	const updateAddressWrapperEl = document.querySelector( '.rpress-stripe-update-billing-address-wrapper' );

	if ( ! updateAddressWrapperEl ) {
		return;
	}

	// Find all elements after the toggle.
	const billingFieldWrappers = getNextSiblings( updateAddressWrapperEl );
	const billingAddressPreview = document.querySelector( '.rpress-stripe-update-billing-address-current' );

	billingFieldWrappers.forEach( function( wrap ) {
		wrap.style.display = isVisible ? 'block' : 'none';
	} );

	// Hide address preview.
	if ( billingAddressPreview ) {
		billingAddressPreview.style.display = isVisible ? 'none' : 'block';
	}
}

/**
 * Manages UI state when the payment source changes.
 *
 * @param {HTMLElement} paymentSource Selected payment source. (Radio element with data).
 */
function onPaymentSourceChange( paymentSource ) {
	const isNew = 'new' === paymentSource.value;
	const newCardForm = document.querySelector( '.rpress-stripe-new-card' );
	const billingAddressToggle = document.querySelector( '.rpress-stripe-update-billing-address-wrapper' );

	// Toggle card details field.
	newCardForm.style.display = isNew ? 'block' : 'none';

	if ( billingAddressToggle ) {
		billingAddressToggle.style.display = isNew ? 'none' : 'block';
	}

	// @todo don't be lazy.
	$( '.rpress-stripe-card-radio-item' ).removeClass( 'selected' );
	$( paymentSource ).closest( '.rpress-stripe-card-radio-item' ).addClass( 'selected' );

	const addressFieldMap = {
		card_address: 'address_line1',
		card_address_2: 'address_line2',
		card_city: 'address_city',
		card_state: 'address_state',
		card_zip: 'address_zip',
		billing_country: 'address_country',
	};

	// New card is being used, show fields and reset them.
	if ( isNew ) {
		// Reset all fields.
		for ( const addressEl in addressFieldMap ) {
			if ( ! addressFieldMap.hasOwnProperty( addressEl ) ) {
				return;
			}

			const addressField = document.getElementById( addressEl );

			if ( addressField ) {
				addressField.value = '';
				addressField.selected = '';
			}
		}

		// Show billing fields.
		toggleBillingFields( true );

		// Existing card is being used.
		// Ensure the billing fields are hidden, and update their values with saved information.
	} else {
		const addressString = [];
		const billingDetailsEl = document.getElementById( paymentSource.id + '-billing-details' );

		if ( ! billingDetailsEl ) {
			return;
		}

		// Hide billing fields.
		toggleBillingFields( false );

		// Uncheck "Update billing address"
		if ( getBillingFieldsToggle() ) {
			getBillingFieldsToggle().checked = false;
		}

		// Update billing address fields with saved card values.
		const billingDetails = billingDetailsEl.dataset;

		for ( const addressEl in addressFieldMap ) {
			if ( ! addressFieldMap.hasOwnProperty( addressEl ) ) {
				continue;
			}

			const addressField = document.getElementById( addressEl );

			if ( ! addressField ) {
				continue;
			}

			const value = billingDetails[ addressFieldMap[ addressEl ] ];

			// Set field value.
			addressField.value = value;

			// Generate an address string from values.
			if ( '' !== value ) {
				addressString.push( value );
			}

			// This field is required but does not have a saved value, show all fields.
			if ( addressField.required && '' === value ) {
				// @todo DRY up some of this DOM usage.
				toggleBillingFields( true );

				if ( getBillingFieldsToggle() ) {
					getBillingFieldsToggle().checked = true;
				}

				if ( billingAddressToggle ) {
					billingAddressToggle.style.display = 'none';
				}
			}

			// Trigger change event when the Country field is updated.
			if ( 'billing_country' === addressEl ) {
				const changeEvent = document.createEvent( 'Event' );
				changeEvent.initEvent( 'change', true, true );
				addressField.dispatchEvent( changeEvent );
			}
		}

		// SUPER ghetto way to watch for core form state because no consistent events are in place.
		// Core calls `rpress_cart_billing_address_updated` but only on checkout. These methods
		// can be used anywhere.
		$( document ).ajaxSuccess( function( event, xhr, options ) {
			if ( ! options || ! options.data || ! xhr ) {
				return;
			}

			if (
				options.data.includes( 'action=rpress_get_shop_states' ) &&
				options.data.includes( 'field_name=card_state' ) &&
				( xhr.responseText && xhr.responseText.includes( 'card_state' ) )
			) {
				const stateField = document.getElementById( 'card_state' );

				if ( stateField ) {
					stateField.value = billingDetails.address_state;
				}
			}
		} );

		// Update address string summary.
		const billingAddressPreview = document.querySelector( '.rpress-stripe-update-billing-address-current' );

		if ( billingAddressPreview ) {
			billingAddressPreview.innerText = addressString.join( ', ' );
		}
	}
}
