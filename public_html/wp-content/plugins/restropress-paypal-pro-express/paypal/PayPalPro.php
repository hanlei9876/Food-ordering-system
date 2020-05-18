<?php
class PayPalProGateway {

	protected $_purchase_data;
	protected $_api_version = 124;

	public function purchase_data( $data ) {
		$this->_purchase_data = $data;
	}

	public function process_sale() {
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
				'name'      => __( 'Discount', 'rpress-pp' ),
				'amount'    => rpress_sanitize_amount( 0 - $discount ),
				'number'    => $this->_purchase_data['discount'],
				'quantity'  => 1
			);
			$items[] = rpress_sanitize_amount( 0 - $discount );

			$ppfunctions->new_item( $item_data );

			$item_total = intval( round( (float) array_sum( $items ) * 100 ) );
		}
		else if ( ( $item_total + $tax ) < $total_amount ) {
			// This is the super rare scenario that the total amount is greater than the total of all items added up.  In this case we need to make the total match the item totals
			$total_amount = $item_total + $tax;
		}

		$description = trim( substr( $description, 0, -2 ) ); // take off trailing comma and trim any whitespace
		$data = array(
			'USER'            => $this->_purchase_data['credentials']['api_username'],
			'PWD'             => $this->_purchase_data['credentials']['api_password'],
			'SIGNATURE'       => $this->_purchase_data['credentials']['api_signature'],
			'VERSION'         => $this->_api_version,
			'METHOD'          => 'DoDirectPayment',
			'PAYMENTACTION'   => 'Sale',
			'IPADDRESS'       => rpress_get_ip(),
			'CREDITCARDTYPE'  => $this->_purchase_data['card_data']['card_type'],
			'ACCT'            => $this->_purchase_data['card_data']['number'],
			'EXPDATE'         => $this->_purchase_data['card_data']['exp_month'] . $this->_purchase_data['card_data']['exp_year'], // needs to be in the format 062019
			'CVV2'            => $this->_purchase_data['card_data']['cvc'],
			'EMAIL'           => $this->_purchase_data['card_data']['email'],
			'FIRSTNAME'       => $this->_purchase_data['card_data']['first_name'],
			'LASTNAME'        => $this->_purchase_data['card_data']['last_name'],
			'STREET'          => $this->_purchase_data['card_data']['billing_address'],
			'CITY'            => $this->_purchase_data['card_data']['billing_city'],
			'STATE'           => $this->_purchase_data['card_data']['billing_state'],
			'COUNTRYCODE'     => $this->_purchase_data['card_data']['billing_country'],
			'ZIP'             => $this->_purchase_data['card_data']['billing_zip'],
			'AMT'             => number_format( $total_amount / 100, 2, '.', '' ),
			'ITEMAMT'         => number_format( $item_total / 100, 2, '.', '' ),
			'SHIPPINGAMT'     => 0,
			'TAXAMT'          => number_format( $tax / 100, 2, '.', '' ),
			'CURRENCYCODE'    => $this->_purchase_data['currency_code'],
			'BUTTONSOURCE'    => 'EasyDigitalDownloads_SP',
			'DESC'            => strlen( $description ) > 127 ? substr( $description, 0, 124 ) . '...' : $description,
			'CUSTOM'          => $this->_purchase_data['payment_id']
		);

		$ppfunctions->request_fields( $data );

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

}