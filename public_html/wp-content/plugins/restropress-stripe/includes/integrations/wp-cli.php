<?php
/**
 * RestroPress WP-CLI Tools for Stripe
 *
 * This class provides an integration point with the WP-CLI plugin allowing
 * access to RPRESS from the command line.
 *
 * @package     RPRESS
 * @subpackage  Classes/CLI
 * @copyright   Copyright (c) 2015, Chris Klosowski
 * @license     http://opensource.org/license/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

WP_CLI::add_command( 'rpress-stripe', 'RPRESS_Stripe_CLI' );

/**
 * Work with RPRESS through WP-CLI
 *
 * RPRESS_CLI Class
 *
 * Adds CLI support to RPRESS through WP-CL
 *
 * @since   1.0
 */
class RPRESS_Stripe_CLI extends RPRESS_CLI {
	/**
	 * Migrate the Stripe customer IDs from the usermeta table to the rpress_customermeta table.
	 *
	 * ## OPTIONS
	 *
	 * --force=<boolean>: If the routine should be run even if the upgrade routine has been run already
	 *
	 * ## EXAMPLES
	 *
	 * wp rpress-stripe migrate_customer_ids
	 * wp rpress-stripe migrate_customer_ids --force
	 */
	public function migrate_customer_ids( $args, $assoc_args ) {
		global $wpdb;
		$force  = isset( $assoc_args['force'] ) ? true : false;

		$upgrade_completed = rpress_has_upgrade_completed( 'stripe_customer_id_migration' );

		if ( ! $force && $upgrade_completed ) {
			WP_CLI::error( __( 'The Stripe customer ID migration has already been run. To do this anyway, use the --force argument.', 'rpstripe' ) );
		}

		$sql     = "SELECT user_id, meta_key, meta_value FROM $wpdb->usermeta WHERE meta_key IN ( '_rpress_stripe_customer_id', '_rpress_stripe_customer_id_test' )";
		$results = $wpdb->get_results( $sql );
		$total   = count( $results );

		if ( ! empty( $total ) ) {

			$progress = new \cli\progress\Bar( 'Processing user meta', $total );

			foreach ( $results as $result ) {
				$user_data = get_userdata( $result->user_id );
				$customer  = new RPRESS_Customer( $user_data->user_email );

				if ( ! $customer->id > 0 ) {
					$customer = new RPRESS_Customer( $result->user_id, true );

					if ( ! $customer->id > 0 ) {
						continue;
					}
				}

				$stripe_customer_id = $result->meta_value;

				// We should try and use a recurring ID if one exists for this user
				if ( class_exists( 'RPRESS_Recurring_Subscriber' ) ) {
					$subscriber         = new RPRESS_Recurring_Subscriber( $customer->id );
					$stripe_customer_id = $subscriber->get_recurring_customer_id( 'stripe' );
				}

				$customer->update_meta( $result->meta_key, $stripe_customer_id );

				$progress->tick();
			}

			$progress->finish();
			WP_CLI::line( __( 'Migration complete.', 'rpstripe' ) );
		} else {
			WP_CLI::line( __( 'No user records were found that needed to be migrated.', 'rpstripe' ) );
		}

		update_option( 'rpress_stripe_version', preg_replace( '/[^0-9.].*/', '', RPRESS_STRIPE_VERSION ) );
		rpress_set_upgrade_complete( 'stripe_customer_id_migration' );

	}
}