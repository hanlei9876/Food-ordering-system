<?php
/**
 * RPRESS_Stripe_Utils_Static_Registry interface.
 *
 * @package RPRESS_Stripe
 * @since   1.0
 */

/**
 * Defines the contract for a static (singleton) registry object.
 *
 * @since 1.0
 */
interface RPRESS_Stripe_Utils_Static_Registry {

	/**
	 * Retrieves the one true registry instance.
	 *
	 * @since 1.0
	 *
	 * @return RPRESS_Stripe_Utils_Static_Registry Registry instance.
	 */
	public static function instance();

}
