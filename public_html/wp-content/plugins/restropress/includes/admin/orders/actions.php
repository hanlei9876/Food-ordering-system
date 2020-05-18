<?php
/**
 * Orders Actions
 *
 * @package     RPRESS
 * @copyright   Copyright (c) 2019, MagniGenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Update order on edit
 *
 * @access      private
 * @since       2.2
 * @return      void
*/
function rpress_update_order_status( $payment_id = 0, $new_status = 'delivered' ) {

  if ( empty( $payment_id ) ) {
    return;
  }

  if ( $new_status == 'delivered' ) {
    rpress_update_payment_status( $payment_id, 'publish' );
  }

  update_post_meta( $payment_id, '_order_status', $new_status );
  
}


/**
 * Get order ststus by payment id
 *
 * @access      private
 * @since       2.1
 * @param       int $payment_id Payment id
 * @return      void
*/
function rpress_get_order_status( $payment_id ) {

  if( empty( $payment_id ) ) {
    return;
  }

  $order_status = !empty( get_post_meta( $payment_id, '_order_status', true ) ) ? get_post_meta( $payment_id, '_order_status', true ) : 'pending'; 

  return apply_filters( 'rp_get_order_status', $order_status );
}