<?php

/**
 * Trigger preapproved payment charge
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_process_preapproved_charge() {

	if( empty( $_GET['nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_GET['nonce'], 'rpress-stripe-process-preapproval' ) )
		return;

	$payment_id  = absint( $_GET['payment_id'] );
	$charge      = rpress_stripe_charge_preapproved( $payment_id );

	if ( $charge ) {
		wp_redirect( esc_url_raw( add_query_arg( array( 'rpress-message' => 'preapproval-charged' ), admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ) ); exit;
	} else {
		wp_redirect( esc_url_raw( add_query_arg( array( 'rpress-message' => 'preapproval-failed' ), admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ) ); exit;
	}

}
add_action( 'rpress_charge_stripe_preapproval', 'rpress_stripe_process_preapproved_charge' );


/**
 * Cancel a preapproved payment
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_process_preapproved_cancel() {
	global $rpress_options;

	if( empty( $_GET['nonce'] ) )
		return;

	if( ! wp_verify_nonce( $_GET['nonce'], 'rpress-stripe-process-preapproval' ) )
		return;

	$payment_id  = absint( $_GET['payment_id'] );
	$customer_id = get_post_meta( $payment_id, '_rpress_stripe_customer_id', true );

	if( empty( $customer_id ) || empty( $payment_id ) ) {
		return;
	}

	if ( 'preapproval' !== get_post_status( $payment_id ) ) {
		return;
	}

	rpress_insert_payment_note( $payment_id, __( 'Preapproval cancelled', 'rpstripe' ) );
	rpress_update_payment_status( $payment_id, 'cancelled' );
	delete_post_meta( $payment_id, '_rpress_stripe_customer_id' );

	wp_redirect( esc_url_raw( add_query_arg( array( 'rpress-message' => 'preapproval-cancelled' ), admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ) ); exit;
}
add_action( 'rpress_cancel_stripe_preapproval', 'rpress_stripe_process_preapproved_cancel' );

/**
 * Admin Messages
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_admin_messages() {

	if ( isset( $_GET['rpress-message'] ) && 'preapproval-charged' == $_GET['rpress-message'] ) {
		 add_settings_error( 'rpress-stripe-notices', 'rpress-stripe-preapproval-charged', __( 'The preapproved payment was successfully charged.', 'rpstripe' ), 'updated' );
	}
	if ( isset( $_GET['rpress-message'] ) && 'preapproval-failed' == $_GET['rpress-message'] ) {
		 add_settings_error( 'rpress-stripe-notices', 'rpress-stripe-preapproval-charged', __( 'The preapproved payment failed to be charged. View order details for further details.', 'rpstripe' ), 'error' );
	}
	if ( isset( $_GET['rpress-message'] ) && 'preapproval-cancelled' == $_GET['rpress-message'] ) {
		 add_settings_error( 'rpress-stripe-notices', 'rpress-stripe-preapproval-cancelled', __( 'The preapproved payment was successfully cancelled.', 'rpstripe' ), 'updated' );
	}
	if ( isset( $_GET['rpress-message'] ) && 'connect-to-stripe' === $_GET['rpress-message'] ) {
		add_settings_error( 'rpress-stripe-notices', 'rpress-stripe-connect-to-stripe', __( 'Connect your Stripe account using the "Connect with Stripe" button below.', 'rpstripe' ), 'updated' );
		// I feel dirty, but RPRESS does not remove `rpress-message` params from settings URLs and the message carries to all links if not removed, and well I wanted this all to work without touching RPRESS core yet.
		add_filter( 'wp_parse_str', function( $ar ) {
			if( isset( $ar['rpress-message'] ) && 'connect-to-stripe' === $ar['rpress-message'] ) {
				unset( $ar['rpress-message'] );
			}
			return $ar;
		});
	}

	if( isset( $_GET['rpress_gateway_connect_error'], $_GET['rpress-message'] ) ) {
		echo '<div class="notice notice-error"><p>' . sprintf( __( 'There was an error connecting your Stripe account. Message: %s. Please <a href="%s">try again</a>.', 'rpstripe' ), esc_html( urldecode( $_GET['rpress-message'] ) ), esc_url( admin_url( 'edit.php?post_type=fooditems&page=rpress-settings&tab=gateways&section=rpress-stripe' ) ) ) . '</p></div>';
		add_filter( 'wp_parse_str', function( $ar ) {
			if( isset( $ar['rpress_gateway_connect_error'] ) ) {
				unset( $ar['rpress_gateway_connect_error'] );
			}

			if( isset( $ar['rpress-message'] ) ) {
				unset( $ar['rpress-message'] );
			}
			return $ar;
		});
	}

	settings_errors( 'rpress-stripe-notices' );
}
add_action( 'admin_notices', 'rpress_stripe_admin_messages' );

/**
 * Add payment meta item to payments that used an existing card
 *
 * @since 1.1
 * @param $payment_id
 * @return void
 */
function rpress_stripe_show_existing_card_meta( $payment_id ) {
	$payment = new RPRESS_Payment( $payment_id );
	$existing_card = $payment->get_meta( '_rpress_stripe_used_existing_card' );
	if ( ! empty( $existing_card ) ) {
		?>
		<div class="rpress-order-stripe-existing-card rpress-admin-box-inside">
			<p>
				<span class="label"><?php _e( 'Used Existing Card:', 'rpstripe' ); ?></span>&nbsp;
				<span><?php _e( 'Yes', 'rpstripe' ); ?></span>
			</p>
		</div>
		<?php
	}
}
add_action( 'rpress_view_order_details_payment_meta_after', 'rpress_stripe_show_existing_card_meta', 10, 1 );

/**
 * Handles redirects to the Stripe settings page under certain conditions.
 *
 * @since 1.1
 */
function rpress_stripe_connect_test_mode_toggle_redirect() {

	// Check for our marker
	if( ! isset( $_POST['rpress-test-mode-toggled'] ) ) {
		return;
	}

	if( ! current_user_can( 'manage_shop_settings' ) ) {
		return;
	}

	if( ! rpress_is_gateway_active( 'stripe' ) ) {
		return;
	}

	/**
	 * Filter the redirect that happens when options are saved and
	 * add query args to redirect to the Stripe settings page
	 * and to show a notice about connecting with Stripe.
	 */
	add_filter( 'wp_redirect', function( $location ) {
		if( false !== strpos( $location, 'page=rpress-settings' ) && false !== strpos( $location, 'settings-updated=true' ) ) {
			$location = add_query_arg(
				array(
					'section' => 'rpress-stripe',
					'rpress-message' => 'connect-to-stripe',
				),
				$location
			);
		}
		return $location;
	} );

}
add_action( 'admin_init', 'rpress_stripe_connect_test_mode_toggle_redirect' );
