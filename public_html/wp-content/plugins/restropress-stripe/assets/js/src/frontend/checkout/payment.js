/* global $, rpress_stripe_vars, rpress_global_vars */

/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import {
	mountCardElement,
	getPaymentMethod,
	capture as captureIntent,
	handle as handleIntent,
} from 'frontend/elements'; // eslint-disable-line @wordpress/dependency-group

import { apiRequest, generateNotice } from 'utils'; // eslint-disable-line @wordpress/dependency-group

/**
 * Binds Payment submission functionality.
 *
 * Resets before rebinding to avoid duplicate events
 * during gateway switching.
 */
export function paymentForm() {
	// Mount Card Element.
	window.rpressStripe.cardElement = mountCardElement( window.rpressStripe.elements() );

	// Bind form submission.
	// Needs to be jQuery since that is what core submits against.
	$( '#rpress_purchase_form' ).off( 'submit', onSubmit );
	$( '#rpress_purchase_form' ).on( 'submit', onSubmit );

	// SUPER ghetto way to watch for core form validation because no events are in place.
	// Called after the purchase form is submitted (via `click` or `submit`)
	$( document ).off( 'ajaxSuccess', watchInitialValidation );
	$( document ).on( 'ajaxSuccess', watchInitialValidation );
}

/**
 * Processes Stripe gateway-specific functionality after core AJAX validation has run.
 */
async function onSubmitDelay() {
	try {
		// Form data to send to intent requests.
		const formData = $( '#rpress_purchase_form' ).serialize();

		// Retrieve or create a PaymentMethod.
		const paymentMethod = await getPaymentMethod( document.getElementById( 'rpress_purchase_form' ), window.rpressStripe.cardElement );

		// Run the modified `_rpress_stripe_process_purchase_form` and create an Intent.
		const checkoutForm = await processForm( paymentMethod.id, paymentMethod.exists );

		// Handle any actions required by the Intent State Machine (3D Secure, etc).
		const handledIntent = await handleIntent(
			checkoutForm.intent,
			{
				form_data: formData,
			}
		);

		// Create an RPRESS payment record.
		const rpressPayment = await createPayment( handledIntent );

		// Capture any unpcaptured intents.
		const finalIntent = await captureIntent(
			rpressPayment.intent,
			{
				form_data: formData,
			}
		);

		// Attempt to transition payment status and redirect.
		// @todo Maybe confirm payment status as well? Would need to generate a custom
		// response because the private RPRESS_Payment properties are not available.
		if (
			( 'succeeded' === finalIntent.status ) ||
			( 'canceled' === finalIntent.status && 'abandoned' === finalIntent.cancellation_reason )
		) {
			await completePayment( finalIntent );

			window.location.replace( rpress_stripe_vars.successPageUri );
		} else {
			window.location.replace( rpress_stripe_vars.failurePageUri );
		}
	} catch ( error ) {
		handleException( error );
		enableForm();
	}
}

/**
 * Processes the purchase form.
 *
 * Generates purchase data for the current session and
 * uses the PaymentMethod to generate an Intent based on data.
 *
 * @param {string} paymentMethodId PaymentMethod ID.
 * @param {Bool} paymentMethodExists If the PaymentMethod has already been attached to a customer.
 * @return {Promise} jQuery Promise.
 */
export function processForm( paymentMethodId, paymentMethodExists ) {
	return apiRequest( 'rpress_stripe_process_purchase_form', {
		// Send available form data.
		form_data: $( '#rpress_purchase_form' ).serialize(),
		payment_method_id: paymentMethodId,
		payment_method_exists: paymentMethodExists,
	} );
}

/**
 * Complete a Payment in RPRESS.
 *
 * @param {object} intent Intent.
 * @return {Promise} jQuery Promise.
 */
export function createPayment( intent ) {
	return apiRequest( 'rpress_stripe_create_payment', {
		// Send available form data.
		form_data: $( '#rpress_purchase_form' ).serialize(),
		intent,
	} );
}

/**
 * Complete a Payment in RPRESS.
 *
 * @param {object} intent Intent.
 * @return {Promise} jQuery Promise.
 */
export function completePayment( intent ) {
	return apiRequest( 'rpress_stripe_complete_payment', {
		form_data: $( '#rpress_purchase_form' ).serialize(),
		intent,
	} );
}


/**
 * Listen for initial RPRESS core validation.
 *
 * @param {Object} event Event.
 * @param {Object} xhr AJAX request.
 * @param {Object} options Request options.
 */
function watchInitialValidation( event, xhr, options ) {
	if ( ! options || ! options.data || ! xhr ) {
		return;
	}

	if (
		options.data.includes( 'action=rpress_process_checkout' ) &&
		options.data.includes( 'rpress-gateway=stripe' ) &&
		( xhr.responseText && 'success' === xhr.responseText.trim() )
	) {
		return onSubmitDelay();
	}
};

/**
 * RPRESS core listens to a a `click` event on the Checkout form submit button.
 *
 * This submit event handler captures true submissions and triggers a `click`
 * event so RPRESS core can take over as normoal.
 *
 * @param {Object} event submit Event.
 */
function onSubmit( event ) {
	// Ensure we are dealing with the Stripe gateway.
	if ( ! (
		// Stripe is selected gateway and total is larger than 0.
		$( 'input[name="rpress-gateway"]' ).val() === 'stripe'	&&
		$( '.rpress_cart_total .rpress_cart_amount' ).data( 'total' ) > 0
	) ) {
		return;
	}

	// While this function is tied to the submit event, block submission.
	event.preventDefault();

	// Simulate a mouse click on the Submit button.
	//
	// If the form is submitted via the "Enter" key we need to ensure the core
	// validation is run.
	//
	// When that is run and then the form is resubmitted
	// the click event won't do anything because the button will be disabled.
	$( '#rpress_purchase_form #rpress_purchase_submit [type=submit]' ).trigger( 'click' );
}

/**
 * Enables the Checkout form for further submissions.
 */
function enableForm() {
	// Update button text.
	document.querySelector( '#rpress_purchase_form #rpress_purchase_submit [type=submit]' ).value = rpress_global_vars.complete_purchase;

	// Enable form.
	$( '.rpress-loading-ajax' ).remove();
	$( '.rpress_errors' ).remove();
	$( '.rpress-error' ).hide();
	$( '#rpress-purchase-button' ).attr( 'disabled', false );
}

/**
 * Handles error output for stripe.js promises, or jQuery AJAX promises.
 *
 *
 * @param {Object} error Error data.
 */
function handleException( error ) {
	const notice = generateNotice( ( error && error.message ) ? error.message : rpress_stripe_vars.generic_error );

	// Hide previous messages.
	// @todo These should all be in a container, but that's not how core works.
	$( '.rpress-stripe-alert' ).remove();
	$( rpress_global_vars.checkout_error_anchor ).before( notice );
	$( document.body ).trigger( 'rpress_checkout_error', [ error ] );
}
