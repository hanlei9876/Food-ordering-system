/* global $, rpress_stripe_admin */

let testModeCheckbox;
let testModeToggleNotice;

$( document ).ready( function() {
	testModeCheckbox = document.getElementById( 'rpress_settings[test_mode]' );
	if ( testModeCheckbox ) {
		testModeToggleNotice = document.getElementById( 'rpress_settings[stripe_connect_test_mode_toggle_notice]' );
		RPRESS_Stripe_Connect_Scripts.init();
	}

	// Show the hidden API key fields
	$( '#rpress-stripe-api-keys-row-reveal a' ).on( 'click', function( event ) {
		event.preventDefault();
		$( '.rpress-stripe-api-key-row' ).removeClass( 'rpress-hidden' );
		$( this ).parent().addClass( 'rpress-hidden' );
		$( '#rpress-stripe-api-keys-row-hide' ).removeClass( 'rpress-hidden' );
	} );

	// Hide API key fields
	$( '#rpress-stripe-api-keys-row-hide a' ).on( 'click', function( event ) {
		event.preventDefault();
		$( '.rpress-stripe-api-key-row' ).addClass( 'rpress-hidden' );
		$( this ).parent().addClass( 'rpress-hidden' );
		$( '#rpress-stripe-api-keys-row-reveal' ).removeClass( 'rpress-hidden' );
	} );
} );

const RPRESS_Stripe_Connect_Scripts = {

	init() {
		this.listeners();
	},

	listeners() {
		const self = this;

		testModeCheckbox.addEventListener( 'change', function() {
			// Don't run these events if Stripe is not enabled.
			if ( ! rpress_stripe_admin.stripe_enabled ) {
				return;
			}

			if ( this.checked ) {
				if ( 'false' === rpress_stripe_admin.test_key_exists ) {
					self.showNotice( testModeToggleNotice, 'error' );
					self.addHiddenMarker();
				} else {
					self.hideNotice( testModeToggleNotice );
					const hiddenMarker = document.getElementById( 'rpress-test-mode-toggled' );
					if ( hiddenMarker ) {
						hiddenMarker.parentNode.removeChild( hiddenMarker );
					}
				}
			}

			if ( ! this.checked ) {
				if ( 'false' === rpress_stripe_admin.live_key_exists ) {
					self.showNotice( testModeToggleNotice, 'error' );
					self.addHiddenMarker();
				} else {
					self.hideNotice( testModeToggleNotice );
					const hiddenMarker = document.getElementById( 'rpress-test-mode-toggled' );
					if ( hiddenMarker ) {
						hiddenMarker.parentNode.removeChild( hiddenMarker );
					}
				}
			}
		} );
	},

	addHiddenMarker() {
		const submit = document.getElementById( 'submit' );

		if ( ! submit ) {
			return;
		}

		submit.parentNode.insertAdjacentHTML( 'beforeend', '<input type="hidden" class="rpress-hidden" id="rpress-test-mode-toggled" name="rpress-test-mode-toggled" />' );
	},

	showNotice( element = false, type = 'error' ) {
		if ( ! element ) {
			return;
		}

		if ( typeof element !== 'object' ) {
			return;
		}

		element.className = 'notice notice-' + type;
	},

	hideNotice( element = false ) {
		if ( ! element ) {
			return;
		}

		if ( typeof element !== 'object' ) {
			return;
		}

		element.className = 'rpress-hidden';
	},
};
