<?php
/**
 * Manage the notices registry.
 *
 * @package RPRESS_Stripe
 * @since   1.1
 */

/**
 * Implements logic for displaying notifications.
 *
 * @since 1.1
 */
class RPRESS_Stripe_Admin_Notices {

	/**
	 * Registry.
	 *
	 * @since 1.1
	 * @var RPRESS_Stripe_Notices_Registry
	 */
	protected $registry;

	/**
	 * RPRESS_Stripe_Admin_Notices
	 *
	 * @param RPRESS_Stripe_Notices_Registry $registry Notices registry.
	 */
	public function __construct( $registry ) {
		$this->registry = $registry;
	}

	/**
	 * Retrieves the name of the option to manage the status of the notice.
	 *
	 * @since 1.1
	 *
	 * @param string $notice_id ID of the notice to generate the name with.
	 * @return string
	 */
	public function get_dismissed_option_name( $notice_id ) {
		// Ensures backwards compatibility for notices dismissed before 2.6.19
		switch ( $notice_id ) {
			case 'stripe-connect':
				$option_name = 'rpress_stripe_connect_intro_notice_dismissed';
				break;
			default:
				$option_name = sprintf( 'rpress_stripe_notice_%1$s_dismissed', $notice_id );
		}

		return $option_name;
	}

	/**
	 * Dismisses a notice.
	 *
	 * @since 1.1
	 *
	 * @param string $notice_id ID of the notice to dismiss.
	 * @return bool True if notice is successfully dismissed. False on failure.
	 */
	public function dismiss( $notice_id ) {
		return update_option( $this->get_dismissed_option_name( $notice_id ), true );
	}

	/**
	 * Restores a notice.
	 *
	 * @since 1.1
	 *
	 * @param string $notice_id ID of the notice to restore.
	 * @return bool True if notice is successfully restored. False on failure.
	 */
	public function restore( $notice_id ) {
		return delete_option( $this->get_dismissed_option_name( $notice_id ) );
	}

	/**
	 * Determine if a notice has been permanently dismissed.
	 *
	 * @since 1.1
	 *
	 * @param int $notice_id Notice ID.
	 * @return bool True if the notice is dismissed.
	 */
	public function is_dismissed( $notice_id ) {
		return (bool) get_option( $this->get_dismissed_option_name( $notice_id ), false );
	}

	/**
	 * Builds a given notice's output.
	 *
	 * @since 1.1
	 *
	 * @param string $notice_id ID of the notice to build.
	 */
	public function build( $notice_id ) {
		$output = '';
		$notice = $this->registry->get_item( $notice_id );

		if ( empty( $notice ) ) {
			return $output;
		}

		if ( true === $this->is_dismissed( $notice_id ) ) {
			return $output;
		}

		if ( is_callable( $notice['message'] ) ) {
			$message = call_user_func( $notice['message'] );
		} else {
			$message = $notice['message'];
		}

		$classes = array(
			'rpress-stripe-admin-notice',
			'notice',
			'notice-' . $notice['type'],
		);

		if ( $notice['dismissible'] ) {
			$classes[] = 'is-dismissible';
		}

		$output = sprintf(
			'<div id="rpress-stripe-%1$s-notice" class="%2$s" data-id="%1$s" data-nonce="%3$s" role="alert">%4$s</div>',
			esc_attr( $notice_id ),
			esc_attr( implode( ' ', $classes ) ),
			wp_create_nonce( "rpress-stripe-dismiss-{$notice_id}-nonce" ),
			$message
		);

		return $output;
	}

	/**
	 * Outputs a given notice.
	 *
	 * @since 1.1
	 */
	public function output( $notice_id ) {
		echo $this->build( $notice_id );
	}

}
