<?php

/**
 * Register payment statuses for preapproval
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_register_post_statuses() {
	register_post_status( 'preapproval_pending', array(
		'label'                     => _x( 'Preapproval Pending', 'Pending preapproved payment', 'rpstripe' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'rpstripe' )
	) );
	register_post_status( 'preapproval', array(
		'label'                     => _x( 'Preapproved', 'Preapproved payment', 'rpstripe' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'rpstripe' )
	) );
	register_post_status( 'cancelled', array(
		'label'                     => _x( 'Cancelled', 'Cancelled payment', 'rpstripe' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Active <span class="count">(%s)</span>', 'Active <span class="count">(%s)</span>', 'rpstripe' )
	) );
}
add_action( 'init',  'rpress_stripe_register_post_statuses', 110 );

/**
 * Register the statement_descriptor email tag.
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_register_email_tags() {
	$statement_descriptor = rpress_stripe_get_statement_descriptor();
	if ( ! empty( $statement_descriptor ) ) {
		rpress_add_email_tag( 'stripe_statement_descriptor', __( 'Outputs a line stating what charges will appear as on customer\'s credit card statements.', 'rpstripe' ), 'rpress_stripe_statement_descriptor_template_tag' );
	}
}
add_action( 'rpress_add_email_tags', 'rpress_stripe_register_email_tags' );

/**
 * Swap the {statement_descriptor} email tag with the string from the option
 *
 * @since 1.1
 * @param $payment_id
 *
 * @return mixed
 */
function rpress_stripe_statement_descriptor_template_tag( $payment_id ) {
	$payment = new RPRESS_Payment( $payment_id );
	if ( 'stripe' !== $payment->gateway ) {
		return '';
	}

	$statement_descriptor = rpress_stripe_get_statement_descriptor();
	if ( empty( $statement_descriptor ) ) {
		return '';
	}

	// If you want to filter this, use the %s to define where you want the actual statement descriptor to show in your message.
	$email_tag_output = __( apply_filters( 'rpress_stripe_statement_descriptor_email_tag', 'Charges will appear on your card statement as %s' ), 'rpstripe' );

	return sprintf( $email_tag_output, $statement_descriptor );
}
