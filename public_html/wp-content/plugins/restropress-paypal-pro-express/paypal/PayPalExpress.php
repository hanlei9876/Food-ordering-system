<?php
class PayPalExpressGateway extends PayPalFunctions {

	protected $_purchase_data;
	protected $_api_version = 121;

	public function purchase_data( $data ) {
		$this->_purchase_data = $data;
	}

	public function retrieve_token() {
		$ppfunctions = new PayPalFunctions();

		$ppfunctions->api_end_point( $this->_purchase_data['api_end_point'] );

		$cart_details = $this->_purchase_data['cart_details'];
		$items        = array();
		$description  = '';

		if ( is_array( $cart_details ) && ! empty( $cart_details ) ) {

			foreach ( $cart_details as $item ) {

				$item_amount = round( ( $item['subtotal'] / $item['quantity'] ) - ( $item['discount'] / $item['quantity'] ), 2 );

				if ( $item_amount <= 0 ) {
					$item_amount = 0;
				}

				$description .= stripslashes_deep( html_entity_decode( rpress_get_cart_item_name( $item ), ENT_COMPAT, 'UTF-8' ) ) . ', ';
				$item_data   = array(
					'name'     => stripslashes_deep( html_entity_decode( rpress_get_cart_item_name( $item ), ENT_COMPAT, 'UTF-8' ) ),
					'amount'   => $item_amount,
					'number'   => rpress_use_skus() ? rpress_get_download_sku( $item['id'] ) : $item['item_number']['id'],
					'quantity' => $item['quantity'],
					'tax'      => $item['tax']
				);

				$items[] = $item_amount * $item['quantity'];

				$ppfunctions->new_item( $item_data );

			}

		}

		if ( ! empty( $this->_purchase_data['fees'] ) ) {

			foreach ( $this->_purchase_data['fees'] as $fee ) {

				if ( floatval( $fee['amount'] ) > '0' ) {
					$description .= stripslashes_deep( html_entity_decode( wp_strip_all_tags( $fee['label'] ), ENT_COMPAT, 'UTF-8' ) ) . ', ';
					$item_data   = array(
						'name'     => stripslashes_deep( html_entity_decode( wp_strip_all_tags( $fee['label'] ), ENT_COMPAT, 'UTF-8' ) ),
						'amount'   => rpress_sanitize_amount( $fee['amount'] ),
						'quantity' => 1
					);

					$items[] = $fee['amount'];

					$ppfunctions->new_item( $item_data );
				}
				else if ( empty( $fee['download_id'] ) ) {
					// If it has a download_id it has already been subtracted from the total - Discounts Pro fix

					$description .= stripslashes_deep( html_entity_decode( wp_strip_all_tags( $fee['label'] ), ENT_COMPAT, 'UTF-8' ) ) . ', ';
					$item_data   = array(
						'name'     => stripslashes_deep( html_entity_decode( wp_strip_all_tags( $fee['label'] ), ENT_COMPAT, 'UTF-8' ) ),
						'amount'   => rpress_sanitize_amount( $fee['amount'] ),
						'quantity' => 1
					);

					$items[] = $fee['amount'];

					$ppfunctions->new_item( $item_data );

				}

			}

		}

		// Convert all numbers to integers
		$total_amount = intval( round( (float) $this->_purchase_data['price'] * 100 ) );
		$item_total   = intval( round( (float) array_sum( $items ) * 100 ) );
		$tax          = intval( round( (float) $this->_purchase_data['tax'] * 100 ) );
		$discount     = intval( round( (float) $this->_purchase_data['discount_amount'] * 100 ) );

		if ( ( $item_total + $tax ) > $total_amount ) {

			$description .= __( 'Discount', 'rpress-pp' ) . ', ';
			$item_data = array(
				'name' => __( 'Discount', 'rpress-pp' ),
				'amount' => rpress_sanitize_amount( 0 - $discount ),
				'number' => $this->_purchase_data['discount'],
				'quantity' => 1
			);
			$items[] = rpress_sanitize_amount( 0 - $discount );

			$ppfunctions->new_item( $item_data );

			$item_total = intval( round( (float) array_sum( $items ) * 100 ) );

		}
		else if ( ( $item_total + $tax ) < $total_amount ) {
			// This is the super rare scenario that the total amount is greater than the total of all items added up.  In this case we need to make the total match the item totals
			$total_amount = $item_total + $tax;
		}

		$lang = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );

		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {

			switch ( ICL_LANGUAGE_CODE ) {
				// WPML is active so use its language code
				case "fr":
					$locale = 'FR';
					break;
				case "it":
					$locale = 'IT';
					break;
				case "de":
					$locale = 'DE';
					break;
				default:
					$locale = 'US';
					break;
			}

		}
		else {

			switch ( $lang ) {
				// use browser language code
				case "fr":
					$locale = 'FR';
					break;
				case "it":
					$locale = 'IT';
					break;
				case "de":
					$locale = 'DE';
					break;
				default:
					$locale = 'US';
					break;
			}

		}
		$description = trim( substr( $description, 0, -2 ) ); // take off trailing comma and trim any whitespace
		$paypal_data = array(
			'USER'                               => $this->_purchase_data['credentials']['api_username'],
			'PWD'                                => $this->_purchase_data['credentials']['api_password'],
			'SIGNATURE'                          => $this->_purchase_data['credentials']['api_signature'],
			'VERSION'                            => $this->_api_version,
			'METHOD'                             => 'SetExpressCheckout',
			'PAYMENTREQUEST_0_PAYMENTACTION'     => 'Sale',
			'LANDINGPAGE'                        => 'Billing',
			'SOLUTIONTYPE'                       => 'Sole',
			'NOSHIPPING'                         => 1,
			'RETURNURL'                          => $this->_purchase_data['urls']['return_url'],
			'CANCELURL'                          => $this->_purchase_data['urls']['cancel_url'],
			'LOCALECODE'                         => $locale,
			'PAYMENTREQUEST_0_CURRENCYCODE'      => $this->_purchase_data['currency_code'],
			'PAYMENTREQUEST_0_AMT'               => number_format( $total_amount / 100, 2, '.', '' ),
			'PAYMENTREQUEST_0_DESC'              => strlen( $description ) > 127 ? substr( $description, 0, 124 ) . '...' : $description,
			'PAYMENTREQUEST_0_TAXAMT'            => number_format( $tax / 100, 2, '.', '' ),
			'PAYMENTREQUEST_0_ITEMAMT'           => number_format( $item_total / 100, 2, '.', '' ),
			'PAYMENTREQUEST_0_SHIPPINGAMT'       => number_format( 0, 2, '.', '' ),
			'PAYMENTREQUEST_0_CUSTOM'            => $this->_purchase_data['payment_id'],
			'PAYMENTREQUEST_0_SHIPTONAME'        => $this->_purchase_data['first_name'] . ' ' . $this->_purchase_data['last_name'],
			'PAYMENTREQUEST_0_SHIPTOSTREET'      => $this->_purchase_data['address1'],
			'PAYMENTREQUEST_0_SHIPTOSTREET2'     => $this->_purchase_data['address2'],
			'PAYMENTREQUEST_0_SHIPTOCITY'        => $this->_purchase_data['city'],
			'PAYMENTREQUEST_0_SHIPTOSTATE'       => $this->_purchase_data['state'],
			'PAYMENTREQUEST_0_SHIPTOZIP'         => $this->_purchase_data['zip'],
			'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE' => $this->_purchase_data['country'],
			'EMAIL'                              => $this->_purchase_data['email'],
			'FIRSTNAME'                          => $this->_purchase_data['first_name'],
			'LASTNAME'                           => $this->_purchase_data['last_name']
		);

		$ppfunctions->request_fields( apply_filters( 'rpress_pp_get_express_paypal_data', $paypal_data, $this->_purchase_data ) );

		$response = $ppfunctions->paypal_query();

		return $response;
	}

	public function refund_transaction( $transaction_id, $refund_type, $amount, $currency, $note ) {
		$ppfunctions = new PayPalFunctions();
		$ppfunctions->api_end_point( $this->_purchase_data['api_end_point'] );

		$data =  array(
			'USER'          => $this->_purchase_data['credentials']['api_username'],
			'PWD'           => $this->_purchase_data['credentials']['api_password'],
			'SIGNATURE'     => $this->_purchase_data['credentials']['api_signature'],
			'VERSION'       => $this->_api_version,
			'METHOD'        => 'RefundTransaction',
			'TRANSACTIONID' => $transaction_id,
			'REFUNDTYPE'    => $refund_type,
			'AMT'           => number_format( $amount, 2, '.', '' ),
			'CURRENCYCODE'  => $currency,
			'NOTE'          => $note,
		);

		$ppfunctions->request_fields( $data );
		$response = $ppfunctions->paypal_query();

		return $response;
	}

	public function express_checkout_details( $token ) {
		$token = urlencode( $token );

		$ppfunctions = new PayPalFunctions();
		$ppfunctions->api_end_point( $this->_purchase_data['api_end_point'] );

		$ppfunctions->request_fields( array(
			'USER'      => $this->_purchase_data['credentials']['api_username'],
			'PWD'       => $this->_purchase_data['credentials']['api_password'],
			'SIGNATURE' => $this->_purchase_data['credentials']['api_signature'],
			'VERSION'   => $this->_api_version
		) );

		$response = $ppfunctions->get_express_token_details( $token );

		return $response;
	}

	public function express_checkout( $token, $payer_id, $amount, $item_total, $tax, $currency ) {
		$token = urlencode( $token );

		$ppfunctions = new PayPalFunctions();
		$ppfunctions->api_end_point( $this->_purchase_data['api_end_point'] );

		$data =  array(
			'USER'                           => $this->_purchase_data['credentials']['api_username'],
			'PWD'                            => $this->_purchase_data['credentials']['api_password'],
			'SIGNATURE'                      => $this->_purchase_data['credentials']['api_signature'],
			'VERSION'                        => $this->_api_version,
			'METHOD'                         => 'DoExpressCheckoutPayment',
			'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
			'TOKEN'                          => $token,
			'PAYERID'                        => $payer_id,
			'PAYMENTREQUEST_0_AMT'           => number_format( $amount, 2, '.', '' ),
			'PAYMENTREQUEST_0_ITEMAMT'       => number_format( $item_total, 2, '.', '' ),
			'PAYMENTREQUEST_0_SHIPPINGAMT'   => 0,
			'PAYMENTREQUEST_0_TAXAMT'        => $tax,
			'PAYMENTREQUEST_0_CURRENCYCODE'  => $currency
		);

		$ppfunctions->request_fields( $data );
		$response = $ppfunctions->paypal_query();

		return $response;
	}

	public function get_purchase_id_by_token( $token ) {
		global $wpdb;
		$purchase = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_rpress_ppe_token' AND meta_value = %s LIMIT 1", $token ) );
		if ( $purchase != NULL ) {
			return $purchase;
		}
		return 0;
	}

}
