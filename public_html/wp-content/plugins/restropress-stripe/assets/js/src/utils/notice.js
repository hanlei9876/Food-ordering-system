/* global $, rpress_stripe_vars */

/**
 * Generates a notice element.
 *
 * @param {string} message The notice text.
 * @param {string} type The type of notice. alert or success.
 * @return {Element} HTML element containing errors.
 */
export function generateNotice( message, type = 'error' ) {
	const notice = document.createElement( 'p' );
	notice.classList.add( 'rpress-alert' );
	notice.classList.add( 'rpress-stripe-alert' );
	notice.style.clear = 'both';

	if ( 'error' === type ) {
		notice.classList.add( 'rpress-alert-error' );
	} else {
		notice.classList.add( 'rpress-alert-success' );
	}

	notice.innerText = message || rpress_stripe_vars.generic_error;

	return notice;
}

/**
 * Outputs a notice.
 */
export function outputNotice( {
	errorType,
	errorMessage,
	errorContainer,
	errorContainerReplace = true,
} ) {
	const $errorContainer = $( errorContainer );
	const notice = generateNotice( errorMessage, errorType );

	if ( true === errorContainerReplace ) {
		$errorContainer.html( notice );
	} else {
		$errorContainer.before( notice );
	}
}
