<?php
/**
 * Add an errors div
 *
 * @since       1.0
 * @return      void
 */
function rpress_stripe_add_stripe_errors() {
	echo '<div id="rpress-stripe-payment-errors"></div>';
}
add_action( 'rpress_after_cc_fields', 'rpress_stripe_add_stripe_errors', 999 );

/**
 * Stripe uses it's own credit card form because the card details are tokenized.
 *
 * We don't want the name attributes to be present on the fields in order to prevent them from getting posted to the server
 *
 * @since       1.0
 * @return      void
 */
function rpress_stripe_credit_card_form( $echo = true ) {

	global $rpress_options;

	if ( rpress_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		rpress_set_error( 'rpress_stripe_error_limit', __( 'We are unable to process your payment at this time, please try again later or contact support.', 'rpstripe' ) );
		return;
	}

	ob_start(); ?>

	<?php if ( ! wp_script_is ( 'rpress-stripe-js' ) ) : ?>
		<?php rpress_stripe_js( true ); ?>
	<?php endif; ?>

	<?php do_action( 'rpress_before_cc_fields' ); ?>

	<fieldset id="rpress_cc_fields" class="rpress-do-validate">
		<legend><?php _e( 'Credit Card Info', 'rpstripe' ); ?></legend>
		<?php if( is_ssl() ) : ?>
			<div id="rpress_secure_site_wrapper">
				<span class="padlock">
					<svg class="rpress-icon rpress-icon-lock" xmlns="http://www.w3.org/2000/svg" width="18" height="28" viewBox="0 0 18 28" aria-hidden="true">
						<path d="M5 12h8V9c0-2.203-1.797-4-4-4S5 6.797 5 9v3zm13 1.5v9c0 .828-.672 1.5-1.5 1.5h-15C.672 24 0 23.328 0 22.5v-9c0-.828.672-1.5 1.5-1.5H2V9c0-3.844 3.156-7 7-7s7 3.156 7 7v3h.5c.828 0 1.5.672 1.5 1.5z"/>
					</svg>
				</span>
				<span><?php _e( 'This is a secure SSL encrypted payment.', 'rpstripe' ); ?></span>
			</div>
		<?php endif; ?>

		<?php
		$existing_cards = rpress_stripe_get_existing_cards( get_current_user_id() );
		?>
		<?php if ( ! empty( $existing_cards ) ) { rpress_stripe_existing_card_field_radio( get_current_user_id() ); } ?>

		<div class="rpress-stripe-new-card" <?php if ( ! empty( $existing_cards ) ) { echo 'style="display: none;"'; } ?>>
			<?php do_action( 'rpress_stripe_new_card_form' ); ?>
			<?php do_action( 'rpress_after_cc_expiration' ); ?>
		</div>

	</fieldset>
	<?php

	do_action( 'rpress_after_cc_fields' );

	$form = ob_get_clean();

	if ( false !== $echo ) {
		echo $form;
	}

	return $form;
}
add_action( 'rpress_stripe_cc_form', 'rpress_stripe_credit_card_form' );

/**
 * Display the markup for the Stripe new card form
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_new_card_form() {
	if ( rpress_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		rpress_set_error( 'rpress_stripe_error_limit', __( 'Adding new payment methods is currently unavailable.', 'rpstripe' ) );
		rpress_print_errors();
		return;
	}
?>

<p id="rpress-card-name-wrap">
	<label for="card_name" class="rpress-label">
		<?php _e( 'Name on the Card', 'rpstripe' ); ?>
		<span class="rpress-required-indicator">*</span>
	</label>
	<span class="rpress-description"><?php _e( 'The name printed on the front of your credit card.', 'rpstripe' ); ?></span>
	<input type="text" name="card_name" id="card_name" class="card-name rpress-input required" autocomplete="cc-name" />
</p>

<div id="rpress-card-wrap">
	<label for="rpress-card-element" class="rpress-label">
		<?php _e( 'Credit Card', 'rpstripe' ); ?>
		<span class="rpress-required-indicator">*</span>
	</label>

	<div id="rpress-stripe-card-element"></div>
	<div id="rpress-stripe-card-errors" role="alert"></div>

	<p></p><!-- Extra spacing -->
</div>

<?php
	/**
	 * Allow output of extra content before the credit card expiration field.
	 *
	 * This content no longer appears before the credit card expiration field
	 * with the introduction of Stripe Elements.
	 *
	 * @deprecated 1.0
	 * @since unknown
	 */
	do_action( 'rpress_before_cc_expiration' );
}
add_action( 'rpress_stripe_new_card_form', 'rpress_stripe_new_card_form' );

/**
 * Show the checkbox for updating the billing information on an existing Stripe card
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_update_billing_address_field() {
	$payment_mode   = strtolower( rpress_get_chosen_gateway() );
	if ( rpress_is_checkout() && 'stripe' !== $payment_mode ) {
		return;
	}

	$existing_cards = rpress_stripe_get_existing_cards( get_current_user_id() );
	if ( empty( $existing_cards ) ) {
		return;
	}

	if ( ! did_action( 'rpress_stripe_cc_form' ) ) {
		return;
	}

	$default_card = false;

	foreach ( $existing_cards as $existing_card ) {
		if ( $existing_card['default'] ) {
			$default_card = $existing_card['source'];
			break;
		}
	}
	?>
	<p class="rpress-stripe-update-billing-address-current">
		<?php
		if ( $default_card ) :
			$address_fields = array( 
				'line1'   => isset( $default_card->address_line1 ) ? $default_card->address_line1 : null,
				'line2'   => isset( $default_card->address_line2 ) ? $default_card->address_line2 : null,
				'city'    => isset( $default_card->address_city ) ? $default_card->address_city : null,
				'state'   => isset( $default_card->address_state ) ? $default_card->address_state : null,
				'zip'     => isset( $default_card->address_zip ) ? $default_card->address_zip : null,
				'country' => isset( $default_card->address_country ) ? $default_card->address_country : null,
			);

			$address_fields = array_filter( $address_fields );

			echo esc_html( implode( ', ', $address_fields ) );
		endif;
		?>
	</p>

	<p class="rpress-stripe-update-billing-address-wrapper">
		<input type="checkbox" name="rpress_stripe_update_billing_address" id="rpress-stripe-update-billing-address" value="1" />
		<label for="rpress-stripe-update-billing-address"><?php _e( 'Enter new billing address', 'rpstripe' ); ?></label>
	</p>
	<?php
}
add_action( 'rpress_cc_billing_top', 'rpress_stripe_update_billing_address_field', 10 );

/**
 * Display a radio list of existing cards on file for a user ID
 *
 * @since 1.1
 * @param int $user_id
 *
 * @return void
 */
function rpress_stripe_existing_card_field_radio( $user_id = 0 ) {
	if ( rpress_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		rpress_set_error( 'rpress_stripe_error_limit', __( 'We are unable to process your payment at this time, please try again later or contacts support.', 'rpstripe' ) );
		return;
	}

	// Can't use just rpress_is_checkout() because this could happen in an AJAX request.
	$is_checkout = rpress_is_checkout() || ( isset( $_REQUEST['action'] ) && 'rpress_load_gateway' === $_REQUEST['action'] );

	rpress_stripe_css( true );
	$existing_cards = rpress_stripe_get_existing_cards( $user_id );
	if ( ! empty( $existing_cards ) ) : ?>
	<div class="rpress-stripe-card-selector rpress-card-selector-radio">
		<?php foreach ( $existing_cards as $card ) : ?>
			<?php $source = $card['source']; ?>
			<div class="rpress-stripe-card-radio-item existing-card-wrapper <?php if ( $card['default'] ) { echo ' selected'; } ?>">
				<input type="hidden" id="<?php echo $source->id; ?>-billing-details"
					   data-address_city="<?php echo $source->address_city; ?>"
					   data-address_country="<?php echo $source->address_country; ?>"
					   data-address_line1="<?php echo $source->address_line1; ?>"
					   data-address_line2="<?php echo $source->address_line2; ?>"
					   data-address_state="<?php echo $source->address_state; ?>"
					   data-address_zip="<?php echo $source->address_zip; ?>"
				/>
				<label for="<?php echo $source->id; ?>">
					<input <?php checked( true, $card['default'], true ); ?> type="radio" id="<?php echo $source->id; ?>" name="rpress_stripe_existing_card" value="<?php echo $source->id; ?>" class="rpress-stripe-existing-card">
					<span class="card-label">
						<span class="card-data">
							<span class="card-name-number">
								<span class="card-brand"><?php echo $source->brand; ?></span>
								<span class="card-ending-label"><?php _e( 'ending in', 'rpstripe' ); ?></span>
								<span class="card-last-4"><?php echo $source->last4; ?></span>
							</span>
							<span class="card-expires-on">
								<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
								<span class="card-expiration-label"><?php _e( 'expires', 'rpstripe' ); ?></span>
								<span class="card-expiration">
									<?php echo $source->exp_month . '/' . $source->exp_year; ?>
								</span>
							</span>
						</span>
						<?php
							$current  = strtotime( date( 'm/Y' ) );
							$exp_date = strtotime( $source->exp_month . '/' . $source->exp_year );
							if ( $exp_date < $current ) :
							?>
							<span class="card-expired">
									<?php _e( 'Expired', 'rpstripe' ); ?>
								</span>
							<?php
							endif;
						?>
					</span>
					<?php if ( $card['default'] && $is_checkout ) { ?>
						<span class="card-status">
							<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
							<span class="card-is-default"><?php _e( 'Default', 'rpstripe'); ?></span>
						</span>
					<?php } ?>
				</label>
			</div>
		<?php endforeach; ?>
		<div class="rpress-stripe-card-radio-item new-card-wrapper">
			<input type="radio" id="rpress-stripe-add-new" class="rpress-stripe-existing-card" name="rpress_stripe_existing_card" value="new" />
			<label for="rpress-stripe-add-new"><span class="add-new-card"><?php _e( 'Add New Card', 'rpstripe' ); ?></span></label>
		</div>
	</div>
	<?php endif;
}

/**
 * Output the management interface for a user's Stripe card
 *
 * @since 1.1
 * @return void
 */
function rpress_stripe_manage_cards() {
	$enabled = rpress_stripe_existing_cards_enabled();
	if ( ! $enabled ) {
		return;
	}

	$stripe_customer_id = rpress_stripe_get_stripe_customer_id( get_current_user_id() );
	if ( empty( $stripe_customer_id ) ) {
		return;
	}

	if ( rpress_stripe()->rate_limiting->has_hit_card_error_limit() ) {
		rpress_set_error( 'rpress_stripe_error_limit', __( 'Payment method management is currently unavailable.', 'rpstripe' ) );
		rpress_print_errors();
		return;
	}

	$existing_cards = rpress_stripe_get_existing_cards( get_current_user_id() );

	rpress_stripe_css( true );
	rpress_stripe_js( true );
	$display = rpress_get_option( 'stripe_billing_fields', 'full' );
?>
	<div id="rpress-stripe-manage-cards">
		<fieldset>
			<legend><?php _e( 'Manage Payment Methods', 'rpstripe' ); ?></legend>
			<input type="hidden" id="stripe-update-card-user_id" name="stripe-update-user-id" value="<?php echo get_current_user_id(); ?>" />
			<?php if ( ! empty( $existing_cards ) ) : ?>
				<?php foreach( $existing_cards as $card ) : ?>
				<?php $source = $card['source']; ?>
				<div id="<?php echo esc_attr( $source->id ); ?>_card_item" class="rpress-stripe-card-item">

					<span class="card-details">
						<span class="card-brand"><?php echo $source->brand; ?></span>
						<span class="card-ending-label"><?php _e( 'ending in', 'rpstripe' ); ?></span>
						<span class="card-last-4"><?php echo $source->last4; ?></span>
						<?php if ( $card['default'] ) { ?>
							<span class="default-card-sep"><?php echo '&mdash; '; ?></span>
							<span class="card-is-default"><?php _e( 'Default', 'rpstripe'); ?></span>
						<?php } ?>
					</span>

					<span class="card-meta">
						<span class="card-expiration"><span class="card-expiration-label"><?php _e( 'Expires', 'rpstripe' ); ?>: </span><span class="card-expiration-date"><?php echo $source->exp_month; ?>/<?php echo $source->exp_year; ?></span></span>
						<span class="card-address">
							<?php
							$address_fields = array( 
								'line1'   => isset( $source->address_line1 ) ? $source->address_line1 : '',
								'zip'     => isset( $source->address_zip ) ? $source->address_zip : '',
								'country' => isset( $source->address_country ) ? $source->address_country : '',
							);

							echo esc_html( implode( ' ', $address_fields ) );
							?>
						</span>
					</span>

					<span id="<?php echo esc_attr( $source->id ); ?>-card-actions" class="card-actions">
						<span class="card-update">
							<a href="#" class="rpress-stripe-update-card" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Update', 'rpstripe' ); ?></a>
						</span>

						<?php if ( ! $card['default'] ) : ?>
						 |
						<span class="card-set-as-default">
							<a href="#" class="rpress-stripe-default-card" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Set as Default', 'rpstripe' ); ?></a>
						</span>
						<?php
						endif;

						$can_delete = apply_filters( 'rpress_stripe_can_delete_card', true, $card, $existing_cards );
						if ( $can_delete ) :
						?>
						|
						<span class="card-delete">
							<a href="#" class="rpress-stripe-delete-card delete" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Delete', 'rpstripe' ); ?></a>
						</span>
						<?php endif; ?>
						
						<span style="display: none;" class="rpress-loading-ajax rpress-loading"></span>
					</span>

					<form id="<?php echo esc_attr( $source->id ); ?>-update-form" class="card-update-form" data-source="<?php echo esc_attr( $source->id ); ?>">
						<label><?php _e( 'Billing Details', 'rpstripe' ); ?></label>

						<div class="card-address-fields">
							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--address1">
							<?php
							echo RPRESS()->html->text( array(
								'id'    => sprintf( 'rpress_stripe_address_line1_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_line1 ) ? $source->address_line1 : '' ),
								'label' => esc_html__( 'Address Line 1', 'rpstripe' ),
								'name'  => 'address_line1',
								'class' => 'card-update-field address_line1 text rpress-input',
								'data'  => array(
									'key' => 'address_line1',
								)
							) );
							?>
							</p>
							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--address2">
							<?php
							echo RPRESS()->html->text( array(
								'id'    => sprintf( 'rpress_stripe_address_line2_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_line2 ) ? $source->address_line2 : '' ),
								'label' => esc_html__( 'Address Line 2', 'rpstripe' ),
								'name'  => 'address_line2',
								'class' => 'card-update-field address_line2 text rpress-input',
								'data'  => array(
									'key' => 'address_line2',
								)
							) );
							?>
							</p>
							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--city">
							<?php
							echo RPRESS()->html->text( array(
								'id'    => sprintf( 'rpress_stripe_address_city_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_city ) ? $source->address_city : '' ),
								'label' => esc_html__( 'City', 'rpstripe' ),
								'name'  => 'address_city',
								'class' => 'card-update-field address_city text rpress-input',
								'data'  => array(
									'key' => 'address_city',
								)
							) );
							?>
							</p>
							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--zip">
							<?php
							echo RPRESS()->html->text( array(
								'id'    => sprintf( 'rpress_stripe_address_zip_%1$s', $source->id ),
								'value' => sanitize_text_field( isset( $source->address_zip ) ? $source->address_zip : '' ),
								'label' => esc_html__( 'ZIP Code', 'rpstripe' ),
								'name'  => 'address_zip',
								'class' => 'card-update-field address_zip text rpress-input',
								'data'  => array(
									'key' => 'address_zip',
								)
							) );
							?>
							</p>
							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--country">
								<label for="<?php echo esc_attr( sprintf( 'rpress_stripe_address_country_%1$s', $source->id ) ); ?>">
									<?php esc_html_e( 'Country', 'rpstripe' ); ?>
								</label>

								<?php
								$countries = array_filter( rpress_get_country_list() );
								$country   = isset( $source->address_country ) ? $source->address_country : rpress_get_shop_country();
								echo RPRESS()->html->select( array(
									'id'               => sprintf( 'rpress_stripe_address_country_%1$s', $source->id ),
									'name'             => 'address_country',
									'label'            => esc_html__( 'Country', 'rpstripe' ),
									'options'          => $countries,
									'selected'         => $country,
									'class'            => 'card-update-field address_country',
									'data'             => array( 'key' => 'address_country' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
								?>
							</p>

							<p class="rpress-stripe-card-address-field rpress-stripe-card-address-field--state">
								<label for="<?php echo esc_attr( sprintf( 'rpress_stripe_address_state_%1$s', $source->id ) ); ?>">
									<?php esc_html_e( 'State', 'rpstripe' ); ?>
								</label>

								<?php
								$selected_state = isset( $source->address_state ) ? $source->address_state : rpress_get_shop_state();
								$states         = rpress_get_shop_states( $country );
								echo RPRESS()->html->select( array(
									'id'               => sprintf( 'rpress_stripe_address_state_%1$s', $source->id ),
									'name'             => 'address_state',
									'options'          => $states,
									'selected'         => $selected_state,
									'class'            => 'card-update-field address_state card_state',
									'data'             => array( 'key' => 'address_state' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
								?>
							</p>
						</div>

						<p class="card-expiration-fields">
							<label for="<?php echo esc_attr( sprintf( 'rpress_stripe_card_exp_month_%1$s', $source->id ) ); ?>" class="rpress-label">
								<?php _e( 'Expiration (MM/YY)', 'rpstripe' ); ?>
							</label>

							<?php
								$months = array_combine( $r = range( 1, 12 ), $r );
								echo RPRESS()->html->select( array(
									'id'               => sprintf( 'rpress_stripe_card_exp_month_%1$s', $source->id ),
									'name'             => 'exp_month',
									'options'          => $months,
									'selected'         => $source->exp_month,
									'class'            => 'card-expiry-month rpress-select rpress-select-small card-update-field exp_month',
									'data'             => array( 'key' => 'exp_month' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
							?>

							<span class="exp-divider"> / </span>

							<?php
								$years = array_combine( $r = range( date( 'Y' ), date( 'Y' ) + 30 ), $r );
								echo RPRESS()->html->select( array(
									'id'               => sprintf( 'rpress_stripe_card_exp_year_%1$s', $source->id ),
									'name'             => 'exp_year',
									'options'          => $years,
									'selected'         => $source->exp_year,
									'class'            => 'card-expiry-year rpress-select rpress-select-small card-update-field exp_year',
									'data'             => array( 'key' => 'exp_year' ),
									'show_option_all'  => false,
									'show_option_none' => false,
								) );
							?>
						</p>

						<p>
							<input
								type="submit"
								class="rpress-stripe-submit-update"
								data-loading="<?php echo esc_attr( 'Please Wait…', 'rpstripe' ); ?>"
								data-submit="<?php echo esc_attr( 'Update Card', 'rpstripe' ); ?>"
								value="<?php echo esc_attr( 'Update Card', 'rpstripe' ); ?>"
							/>

							<a href="#" class="rpress-stripe-cancel-update" data-source="<?php echo esc_attr( $source->id ); ?>"><?php _e( 'Cancel', 'rpstripe' ); ?></a>

							<input type="hidden" name="card_id" data-key="id" value="<?php echo $source->id; ?>" />
							<?php wp_nonce_field( $source->id . '_update', 'card_update_nonce_' . $source->id, true ); ?>
						</p>
					</form>
				</div>
				<?php endforeach; ?>
			<?php endif; ?>
			<form id="rpress-stripe-add-new-card">
				<div class="rpress-stripe-add-new-card" style="display: none;">
					<label><?php _e( 'Add New Card', 'rpstripe' ); ?></label>
					<fieldset id="rpress_cc_card_info" class="cc-card-info">
						<legend><?php _e( 'Credit Card Details', 'restropress' ); ?></legend>
						<?php do_action( 'rpress_stripe_new_card_form' ); ?>
					</fieldset>
					<?php
					switch( $display ) {
					case 'full' :
						rpress_default_cc_address_fields();
						break;

					case 'zip_country' :
						rpress_stripe_zip_and_country();
						add_filter( 'rpress_purchase_form_required_fields', 'rpress_stripe_require_zip_and_country' );

						break;
					}
					?>
				</div>
				<div class="rpress-stripe-add-card-errors"></div>
				<div class="rpress-stripe-add-card-actions">

					<input
						type="submit"
						class="rpress-button rpress-stripe-add-new"
						data-loading="<?php echo esc_attr( 'Please Wait…', 'rpstripe' ); ?>"
						data-submit="<?php echo esc_attr( 'Add new card', 'rpstripe' ); ?>"
						value="<?php echo esc_attr( 'Add new card', 'rpstripe' ); ?>"
					/>
					<a href="#" id="rpress-stripe-add-new-cancel" style="display: none;"><?php _e( 'Cancel', 'rpstripe' ); ?></a>
					<?php wp_nonce_field( 'rpress-stripe-add-card', 'rpress-stripe-add-card-nonce', false, true ); ?>
				</div>
			</form>
		</fieldset>
	</div>
	<?php
}
add_action( 'rpress_profile_editor_after', 'rpress_stripe_manage_cards' );

/**
 * Zip / Postal Code field for when full billing address is disabled
 *
 * @since       1.1
 * @return      void
 */
function rpress_stripe_zip_and_country() {

	$logged_in = is_user_logged_in();
	$customer  = RPRESS()->session->get( 'customer' );
	$customer  = wp_parse_args( $customer, array( 'address' => array(
		'line1'   => '',
		'line2'   => '',
		'city'    => '',
		'zip'     => '',
		'state'   => '',
		'country' => ''
	) ) );

	$customer['address'] = array_map( 'sanitize_text_field', $customer['address'] );

	if( $logged_in ) {
		$existing_cards = rpress_stripe_get_existing_cards( get_current_user_id() );
		if ( empty( $existing_cards ) ) {

			$user_address = rpress_get_customer_address( get_current_user() );

			foreach( $customer['address'] as $key => $field ) {

				if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
					$customer['address'][ $key ] = $user_address[ $key ];
				} else {
					$customer['address'][ $key ] = '';
				}

			}
		} else {
			foreach ( $existing_cards as $card ) {
				if ( false === $card['default'] ) {
					continue;
				}

				$source = $card['source'];
				$customer['address'] = array(
					'line1'   => $source->address_line1,
					'line2'   => $source->address_line2,
					'city'    => $source->address_city,
					'zip'     => $source->address_zip,
					'state'   => $source->address_state,
					'country' => $source->address_country,
				);
			}
		}

	}
?>
	<fieldset id="rpress_cc_address" class="cc-address">
		<legend><?php _e( 'Billing Details', 'rpstripe' ); ?></legend>
		<p id="rpress-card-country-wrap">
			<label for="billing_country" class="rpress-label">
				<?php _e( 'Billing Country', 'rpstripe' ); ?>
				<?php if( rpress_field_is_required( 'billing_country' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The country for your billing address.', 'rpstripe' ); ?></span>
			<select name="billing_country" id="billing_country" class="billing_country rpress-select<?php if( rpress_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>"<?php if( rpress_field_is_required( 'billing_country' ) ) {  echo ' required '; } ?> autocomplete="billing country">
				<?php

				$selected_country = rpress_get_shop_country();

				if( ! empty( $customer['address']['country'] ) && '*' !== $customer['address']['country'] ) {
					$selected_country = $customer['address']['country'];
				}

				$countries = rpress_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<p id="rpress-card-zip-wrap">
			<label for="card_zip" class="rpress-label">
				<?php _e( 'Billing Zip / Postal Code', 'rpstripe' ); ?>
				<?php if( rpress_field_is_required( 'card_zip' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The zip or postal code for your billing address.', 'rpstripe' ); ?></span>
			<input type="text" size="4" name="card_zip" id="card_zip" class="card-zip rpress-input<?php if( rpress_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Zip / Postal Code', 'rpstripe' ); ?>" value="<?php echo $customer['address']['zip']; ?>"<?php if( rpress_field_is_required( 'card_zip' ) ) {  echo ' required '; } ?> autocomplete="billing postal-code" />
		</p>
	</fieldset>
<?php
}

/**
 * Determine how the billing address fields should be displayed
 *
 * @access      public
 * @since       1.1
 * @return      void
 */
function rpress_stripe_setup_billing_address_fields() {

	if( ! function_exists( 'rpress_use_taxes' ) ) {
		return;
	}

	if( rpress_use_taxes() || 'stripe' !== rpress_get_chosen_gateway() || ! rpress_get_cart_total() > 0 ) {
		return;
	}

	$display = rpress_get_option( 'stripe_billing_fields', 'full' );

	switch( $display ) {

		case 'full' :

			// Make address fields required
			add_filter( 'rpress_require_billing_address', '__return_true' );

			break;

		case 'zip_country' :

			remove_action( 'rpress_after_cc_fields', 'rpress_default_cc_address_fields', 10 );
			add_action( 'rpress_after_cc_fields', 'rpress_stripe_zip_and_country', 9 );

			// Make Zip required
			add_filter( 'rpress_purchase_form_required_fields', 'rpress_stripe_require_zip_and_country' );

			break;

		case 'none' :

			remove_action( 'rpress_after_cc_fields', 'rpress_default_cc_address_fields', 10 );

			break;

	}

}
add_action( 'init', 'rpress_stripe_setup_billing_address_fields', 9 );

/**
 * Force zip code and country to be required when billing address display is zip only
 *
 * @access      public
 * @since       2.5
 * @return      array $fields The required fields
 */
function rpress_stripe_require_zip_and_country( $fields ) {

	$fields['card_zip'] = array(
		'error_id' => 'invalid_zip_code',
		'error_message' => __( 'Please enter your zip / postal code', 'rpstripe' )
	);

	$fields['billing_country'] = array(
		'error_id' => 'invalid_country',
		'error_message' => __( 'Please select your billing country', 'rpstripe' )
	);

	return $fields;
}