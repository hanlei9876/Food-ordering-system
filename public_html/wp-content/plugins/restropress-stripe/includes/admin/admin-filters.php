<?php

/**
 * Given a Payment ID, extract the transaction ID from Stripe
 *
 * @param  string $payment_id       Payment ID
 * @return string                   Transaction ID
 */
function rpress_stripe_get_payment_transaction_id( $payment_id ) {

	$txn_id = '';
	$notes  = rpress_get_payment_notes( $payment_id );

	foreach ( $notes as $note ) {
		if ( preg_match( '/^Stripe Charge ID: ([^\s]+)/', $note->comment_content, $match ) ) {
			$txn_id = $match[1];
			continue;
		}
	}

	return apply_filters( 'rpress_stripe_set_payment_transaction_id', $txn_id, $payment_id );
}
add_filter( 'rpress_get_payment_transaction_id-stripe', 'rpress_stripe_get_payment_transaction_id', 10, 1 );

/**
 * Given a transaction ID, generate a link to the Stripe transaction ID details
 *
 * @since  1.1
 * @param  string $transaction_id The Transaction ID
 * @param  int    $payment_id     The payment ID for this transaction
 * @return string                 A link to the Stripe transaction details
 */
function rpress_stripe_link_transaction_id( $transaction_id, $payment_id ) {

	$test = rpress_get_payment_meta( $payment_id, '_rpress_payment_mode' ) === 'test' ? 'test/' : '';
	$url  = '<a href="https://dashboard.stripe.com/' . $test . 'payments/' . $transaction_id . '" target="_blank">' . $transaction_id . '</a>';

	return apply_filters( 'rpress_stripe_link_payment_details_transaction_id', $url );

}
add_filter( 'rpress_payment_details_transaction_id-stripe', 'rpress_stripe_link_transaction_id', 10, 2 );


/**
 * Display the payment status filters
 *
 * @since 1.1
 * @return array
 */
function rpress_stripe_payment_status_filters( $views ) {
	$payment_count             = wp_count_posts( 'rpress_payment' );
	$preapproval_count         = '&nbsp;<span class="count">(' . $payment_count->preapproval . ')</span>';
	$preapproval_pending_count = '&nbsp;<span class="count">(' . $payment_count->preapproval_pending . ')</span>';
	$cancelled_count           = '&nbsp;<span class="count">(' . $payment_count->cancelled . ')</span>';
	$current                   = isset( $_GET['status'] ) ? $_GET['status'] : '';

	$views['preapproval']         = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'preapproval', admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ), $current === 'preapproval' ? ' class="current"' : '', __( 'Preapproved', 'rpstripe' ) . $preapproval_count );
	$views['pending_preapproval'] = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'preapproval_pending', admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ), $current === 'preapproval_pending' ? ' class="current"' : '', __( 'Preapproval Pending', 'rpstripe' ) . $preapproval_pending_count );
	$views['cancelled']           = sprintf( '<a href="%s"%s>%s</a>', esc_url( add_query_arg( 'status', 'cancelled', admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ), $current === 'cancelled' ? ' class="current"' : '', __( 'Cancelled', 'rpstripe' ) . $cancelled_count );

	return $views;
}
add_filter( 'rpress_payments_table_views', 'rpress_stripe_payment_status_filters' );

/**
 * Show the Process / Cancel buttons for preapproved payments
 *
 * @since 1.1
 * @return string
 */
function rpress_stripe_payments_column_data( $value, $payment_id, $column_name ) {
	if ( $column_name == 'status' ) {
		$status      = get_post_status( $payment_id );
		$customer_id = get_post_meta( $payment_id, '_rpress_stripe_customer_id', true );

		if( ! $customer_id )
			return $value;

		$nonce = wp_create_nonce( 'rpress-stripe-process-preapproval' );

		$preapproval_args     = array(
			'payment_id'      => $payment_id,
			'nonce'           => $nonce,
			'rpress-action'      => 'charge_stripe_preapproval'
		);

		$cancel_args          = array(
			'preapproval_key' => $customer_id,
			'payment_id'      => $payment_id,
			'nonce'           => $nonce,
			'rpress-action'      => 'cancel_stripe_preapproval'
		);

		$actions = array();

		$value .= '<p class="row-actions">';

		if ( in_array( $status, array( 'preapproval', 'preapproval_pending' ), true ) ) {
			$actions[] = '<a href="' . esc_url( add_query_arg( $preapproval_args, admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ) . '">' . __( 'Process', 'rpstripe' ) . '</a>';

			if ( 'cancelled' !== $status ) {
				$actions[] = '<span class="delete"><a href="' . esc_url( add_query_arg( $cancel_args, admin_url( 'edit.php?post_type=fooditem&page=rpress-payment-history' ) ) ) . '">' . __( 'Cancel', 'rpstripe' ) . '</a></span>';
			}
		}

		$value .= implode( ' | ', $actions );

		$value .= '</p>';
	}
	return $value;
}
add_filter( 'rpress_payments_table_column', 'rpress_stripe_payments_column_data', 20, 3 );