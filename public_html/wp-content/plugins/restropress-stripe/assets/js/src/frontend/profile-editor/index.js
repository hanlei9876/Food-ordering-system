/**
 * Internal dependencies
 */
import { cardActions } from './actions.js';
import { addNewForm } from './add-new.js';

export function setup() {
	if ( ! document.getElementById( 'rpress-stripe-manage-cards' ) ) {
		return;
	}

	cardActions();
	addNewForm();
}
