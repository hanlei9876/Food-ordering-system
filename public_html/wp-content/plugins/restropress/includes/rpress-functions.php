<?php
/**
 * Custom Functions
 *
 * @package     RPRESS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



function addon_category_taxonomy_custom_fields($tag) {
  $t_id = $tag->term_id; 
  $term_meta = get_option( "taxonomy_term_$t_id" ); 
  $use_addon_like =  isset($term_meta['use_it_like']) ? $term_meta['use_it_like'] : 'checkbox';
?>
<?php if( $tag->parent != 0 ): ?>
<tr class="form-field">
  <th scope="row" valign="top">
    <label for="price_id"><?php _e('Price'); ?></label>
  </th>
  <td>
    <input type="number" step=".01" name="term_meta[price]" id="term_meta[price]" size="25" style="width:15%;" value="<?php echo $term_meta['price'] ? $term_meta['price'] : ''; ?>"><br />
    <span class="description"><?php _e('Price for this addon item'); ?></span>
  </td>
</tr>
<?php endif; ?>

<?php if( $tag->parent == 0 ): ?>
<tr class="form-field">
  <th scope="row" valign="top">
    <label for="use_it_as">
      <?php _e('Addon item selection type', 'restropress'); ?></label>
  </th>
  <td>
    <div class="use-it-like-wrap">
      <label for="use_like_radio">
        <input id="use_like_radio" type="radio" value="radio" name="term_meta[use_it_like]" <?php checked( $use_addon_like, 'radio'); ?> >
          <?php _e('Single item', 'restropress'); ?>
      </label>
      <br/><br/>
      <label for="use_like_checkbox">
        <input id="use_like_checkbox" type="radio" value="checkbox" name="term_meta[use_it_like]" <?php checked( $use_addon_like, 'checkbox'); ?> >
          <?php _e('Multiple Items', 'restropress'); ?>
      </label>
    </div>
  </td>
</tr>
<?php endif; ?>

<?php
}

/**
 * Update taxonomy meta data
 *
 * @since       1.0
 * @param       int | term_id
 * @return      update meta data
 */
function save_addon_category_custom_fields( $term_id ) {
  if( isset( $_POST['term_meta'] ) ) {
    $t_id = $term_id;
    $term_meta = get_option( "taxonomy_term_$t_id" );
    $cat_keys = array_keys( $_POST['term_meta'] );

    if( is_array( $cat_keys ) && !empty( $cat_keys ) ) {
      foreach ( $cat_keys as $key ){
        if( isset( $_POST['term_meta'][$key] ) ){
          $term_meta[$key] = $_POST['term_meta'][$key];
        }
      }
    }
    
    //save the option array
    update_option( "taxonomy_term_$t_id", $term_meta );
  }
}

// Add the fields to the "addon_category" taxonomy, using our callback function
add_action( 'addon_category_edit_form_fields', 'addon_category_taxonomy_custom_fields', 10, 2 );

// Save the changes made on the "addon_category" taxonomy, using our callback function
add_action( 'edited_addon_category', 'save_addon_category_custom_fields', 10, 2 );

/**
 * Get Cart Items By Key
 *
 * @since       1.0
 * @param       int | key
 * @return      array | cart items array
 */
function getCartItemsByKey( $key ) {
  $cart_items_arr = array();
  if( $key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    if( is_array( $cart_items ) && !empty( $cart_items ) ) {
      $items_in_cart = $cart_items[$key];
      if( is_array( $items_in_cart ) ) {
        if( isset( $items_in_cart['addon_items'] ) ) {
          $cart_items_arr = $items_in_cart['addon_items'];
        }
      }
    }
  }
  return $cart_items_arr;
}

/**
 * Get Cart Items Price
 *
 * @since       1.0
 * @param       int | key
 * @return      int | total price for cart
 */
function getCartItemsByPrice( $key ) {
  $cart_items_price = array();
  
  if( $key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    
    if( is_array( $cart_items ) && !empty( $cart_items ) ) {
      $items_in_cart = $cart_items[$key];
      if( is_array( $items_in_cart ) ) {
        $item_price = rpress_get_fooditem_price( $items_in_cart['id'] );
        
        if( $items_in_cart['quantity'] > 0 ) {
          $item_price = $item_price * $items_in_cart['quantity'];
        }
        array_push( $cart_items_price, $item_price );
        
        if( isset( $items_in_cart['addon_items'] ) ) {
          foreach( $items_in_cart['addon_items'] as $key => $item_list ) {
            array_push( $cart_items_price, $item_list['price'] );
          }
        }
      }
    }
  }

  $cart_item_total = array_sum($cart_items_price);
  return $cart_item_total;
}

/**
 * Get food item quantity in the cart by key
 *
 * @since       1.0
 * @param       int | cart_key
 * @return      array | cart items array
 */
function rpress_get_item_qty_by_key( $cart_key ) {
  if( $cart_key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    $cart_items = $cart_items[$cart_key];
    return $cart_items['quantity'];
  }
}

add_action( 'wp_footer', 'rpress_popup' );
if( !function_exists('rpress_popup') ) {
  function rpress_popup() {
    rpress_get_template_part( 'rpress', 'popup' );
  }
}

add_action( 'rpress_get_food_categories', 'rpress_get_food_cats' );

if ( ! function_exists( 'rpress_get_food_cats' ) ) {
  function rpress_get_food_cats(){
    rpress_get_template_part('rpress', 'get-categories');
  }
}

if ( ! function_exists( 'rpress_search_form' ) ) {
  function rpress_search_form() {
    ?>
    <div class="rpress-search-wrap rpress-live-search">
      <input id="rpress-food-search" type="text" placeholder="<?php _e('Search Food Item', 'restropress') ?>">
    </div>
    <?php
  }
}

add_action( 'before_fooditems_list', 'rpress_search_form' );


add_action( 'rpress_before_categories', 'rpress_before_catgory_wrap' );

function rpress_before_catgory_wrap() {
  rpress_get_template_part( 'rpress', 'before-fooditem' );
}



if ( ! function_exists( 'rpress_product_menu_tab' ) ) {
  /**
   * Output the rpress menu tab content.
   */
  function rpress_product_menu_tab() {
    echo do_shortcode('[rpress_items]');
  }
}

/**
 * Get special instruction for food items
 *
 * @since       1.0
 * @param       array | food items
 * @return      string | Special instruction string
 */
function get_special_instruction( $items ) {
  $instruction = '';
  
  if( is_array($items) ) {
    if( isset($items['options']) ) {
      $instruction = $items['options']['instruction'];
    }
    else {
      if( isset($items['instruction']) ) {
        $instruction = $items['instruction'];
      }
    }
  }

  return apply_filters( 'rpress_sepcial_instruction', $instruction );
}

/**
 * Get instruction in the cart by key
 *
 * @since       1.0
 * @param       int | cart_key
 * @return      string | Special instruction string
 */
function rpress_get_instruction_by_key( $cart_key ) {
  if( $cart_key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    $cart_items = $cart_items[$cart_key];
    $instruction = '';
    if( isset($cart_items['instruction']) ) {
      $instruction = !empty($cart_items['instruction']) ? $cart_items['instruction'] : '';
    }
  }
  return $instruction;
}

add_action( 'rpress_get_cart', 'rpress_get_cart_items' );
function rpress_get_cart_items() {
  $class = rpress_get_cart_quantity() == 0 ? 'no-items' : '';
?>
  <div class="rp-col-lg-4 rp-col-md-4 rp-col-sm-12 rp-col-xs-12 pull-right rpress-sidebar-cart item-cart sticky-sidebar">
    <div class="rpress-mobile-cart-icons <?php echo $class; ?>">
      <i class='fa fa-shopping-cart' aria-hidden='true'></i>
      <span class='rpress-cart-badge rpress-cart-quantity'>
        <?php echo rpress_get_cart_quantity(); ?>
      </span>
    </div>
    <div class='rpress-sidebar-main-wrap'>
      <i class='fa fa-times close-cart-ic' aria-hidden='true'></i>
      <div class="rpress-sidebar-cart-wrap">
        <?php echo rpress_shopping_cart(); ?>
      </div>
    </div>
  </div>
  <?php
}


/**
 * Get formatted array of food item details
 *
 * @since       1.0.2
 * @param       array | Food items
 * @param       int | cart key by default blank
 * @return      array | Outputs the array of food items with formatted values in the key value
 */
function getFormattedCatsList( $terms, $cart_key = '' ) {
    $parent_ids = $child_ids =  $list_array = $child_arr = array();
    $html = '';
    
    if( $terms ) {
      foreach( $terms as $term ) {
        if( $term->parent == 0 ) {
          $parent_id = $term->term_id;
          array_push( $parent_ids, $parent_id);
        }
        else {
          $child_id = $term->term_id;;
          array_push( $child_ids, $child_id );
        }
      }
    }

    if( is_array( $parent_ids ) && !empty($parent_ids) ) {
      foreach( $parent_ids as $parent_id ) {
        $term_data = get_term_by('id', $parent_id, 'addon_category');
        $children = get_term_children( $term_data->term_id, 'addon_category' );

        if( is_array($children) && !empty($children) ) {

          foreach( $children as $key => $children_data ) {
            if( in_array($children_data, $child_ids) ) {
              array_push( $child_arr, $children_data);

              if( is_array($child_arr) && !empty($child_arr) ) {
                foreach( $child_arr as $data => $child_arr_list ) {
                  $term_data = get_term_by('id', $child_arr_list, 'addon_category');
                  $t_id = $child_arr_list;
                  $term_meta = get_option( "taxonomy_term_$t_id" );
                  $term_price = !empty($term_meta['price']) ? $term_meta['price'] : '';
                  $term_quantity = !empty($term_meta['enable_quantity']) ? $term_meta['enable_quantity'] : '';

                  $list_array[$data]['id'] = $term_data->term_id;
                  $list_array[$data]['name'] = $term_data->name;
                  $list_array[$data]['price'] = html_entity_decode( rpress_currency_filter( rpress_format_amount( $term_price ) ), ENT_COMPAT, 'UTF-8' );
                  $list_array[$data]['price'] =  $term_price;
                  $list_array[$data]['slug'] = $term_data->slug;
                }
              }
            }
          }
        }
      }
    }
  return $list_array;
}



/**
 * Save order type in session
 *
 * @since       1.0.4
 * @param       string | Delivery Type
 * @param           string | Delivery Time
 * @return      array  | Session array for delivery type and delivery time
 */
function rpress_checkout_delivery_type( $delivery_type, $delivery_time ) {

  $_COOKIE['deliveryMethod'] = $delivery_type;
  $_COOKIE['deliveryTime']  = $delivery_time;
}



/**
 * Show delivery options in the cart
 *
 * @since       1.0.2
 * @param       void
 * @return      string | Outputs the html for the delivery options with texts
 */
function get_delivery_options( $changeble ) {
  $color = rpress_get_option( 'checkout_color', 'red' );
  $service_date = isset( $_COOKIE['DeliveryDate'] ) ? $_COOKIE['DeliveryDate'] : '';
  ob_start();
  ?>
  <div class="delivery-wrap">
    <div class="delivery-opts">
      <?php if ( isset( $_COOKIE['deliveryMethod'] ) 
      && $_COOKIE['deliveryMethod'] !== '' ) : ?>
      <span class="delMethod">
        <?php echo $_COOKIE['deliveryMethod'] . ' ' . $service_date; ?></span>
        <?php if( isset($_COOKIE['deliveryTime'])
          && $_COOKIE['deliveryTime'] !== '' ) : ?>
          <span class="delTime"> 
            <?php esc_html_e( 'at', 'restropress' ); ?> 
            <?php echo $_COOKIE['deliveryTime']; ?>    
          </span>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php
      if( $changeble && isset( $_COOKIE['deliveryMethod'] ) && $_COOKIE['deliveryMethod'] !== '' ) :
      ?>
      <span class="delivery-change <?php echo $color; ?>"><?php esc_html_e('Change?', 'restropress'); ?></span>
      <?php
      endif;
     ?>
  </div>
  <?php
  $data = ob_get_contents();
  ob_get_clean();
  return $data;
}


function rpress_get_delivery_price() {
  $delivery_fee_settings = get_option( 'rpress_delivery_fee', array() );
  $free_delivery_above = isset($delivery_fee_settings['free_delivery_above']) ? $delivery_fee_settings['free_delivery_above'] : 0;

  $cart_subtotal = rpress_get_cart_subtotal();

  if( isset( $_COOKIE['rpress_delivery_price'] ) ) {
    ob_start();
        
    if( $cart_subtotal < $free_delivery_above ) {
      echo rpress_currency_filter( rpress_format_amount( $_COOKIE['rpress_delivery_price'] ) );
    }

    return ob_get_clean();
  }
}


function show_address_on_checkout() {
  $hide_address_on_pickup = rpress_get_option( 'hide_address_on_pickup' );
  $delivery_method = isset( $_COOKIE['deliveryMethod'] ) ? $_COOKIE['deliveryMethod'] : '';

  $cond = true;

  if ( $delivery_method == 'pickup' && $hide_address_on_pickup ) {
    $cond = false;
  }
  else {
    $cond = true;
  }

  $cond = (bool) apply_filters( 'rpress_show_address_on_checkout', $cond );

  return $cond;
}


function rpress_display_checkout_fields() {
  $enable_phone = rpress_get_option( 'enable_phone' );
  $enable_flat = rpress_get_option( 'enable_door_flat' );
  $enable_landmark = rpress_get_option( 'enable_landmark' );
  $delivery_method = isset( $_COOKIE['deliveryMethod'] ) ? $_COOKIE['deliveryMethod'] : '';
  $service_type = rpress_get_option( 'enable_service' );
  $order_note = rpress_get_option( 'enable_order_note' );
  
  ?>
    <p id="rpress-phone-wrap">
      <label class="rpress-label" for="rpress-phone"><?php esc_html_e('Phone Number', 'restropress'); ?><span class="rpress-required-indicator">*</span></label>
      <span class="rpress-description">
        <?php esc_html_e('Enter your phone number so we can get in touch with you.', 'restropress'); ?>
      </span>
      <input class="rpress-input" type="text" name="rpress_phone" id="rpress-phone" placeholder="<?php esc_html_e('Phone Number', 'restropress'); ?>" />
    </p>

    <?php 

    if ( show_address_on_checkout()  ) : ?>
    <p id="rpress-customer-address">
      <label class="rpress-customer-address" for="rpress-customer-address"><?php esc_html_e('Address', 'restropress') ?><span class="rpress-required-indicator">*</span></label>
      <span class="rpress-description">
          <?php esc_html_e('Enter the address you would like to use for this order.', 'restropress'); ?>
      </span>
      <input class="rpress-input" type="text" name="rpress_customer_address" id="rpress-customer-address" placeholder="<?php esc_html_e('Address', 'restropress'); ?>" />
    </p>
  <?php endif; ?>

  <?php if( $enable_flat ) : ?>
    <p id="rpress-door-flat">
      <label class="rpress-flat" for="rpress-flat"><?php esc_html_e('Door/Flat No.', 'restropress'); ?><span class="rpress-required-indicator">*</span></label>
        <span class="rpress-description">
          <?php esc_html_e('Enter your Door/Flat number', 'restropress'); ?>
        </span>
        <input class="rpress-input" type="text" name="rpress_door_flat" id="rpress-door-flat" placeholder="<?php esc_html_e('Door/Flat Number', 'restropress'); ?>" />
    </p>
  <?php endif; ?>

  <?php if( $enable_landmark ): ?>
    <p id="rpress-landmark">
    <label class="rpress-landmark" for="rpress-landmark"><?php _e('Landmark', 'restropress') ?><span class="rpress-required-indicator">*</span></label>
    <span class="rpress-description">
        <?php esc_html_e('Enter any landmark so that we can easily find you.', 'restropress'); ?>
    </span>
    <input class="rpress-input" type="text" name="rpress_landmark" id="rpress-landmark" placeholder="<?php esc_html_e('Landmark', 'restropress'); ?>" />
    </p>
  <?php endif; ?>

  <?php if( $order_note ): ?>
    <p id="rpress-order-note">
    <label class="rpress-order-note" for="rpress-order-note"><?php _e('Order Note', 'restropress') ?></label>
    <span class="rpress-description">
        <?php esc_html_e('Enter note for this order.', 'restropress'); ?>
    </span>
    <textarea name="rpress_order_note" class="rpress-input" rows="5" cols="8"></textarea>
    </p>
  <?php endif; ?>

  <?php
}
add_action( 'rpress_purchase_form_user_info_fields', 'rpress_display_checkout_fields' );

/**
 * Make checkout fields required
 *
 * @since       1.0.3
 * @param       array | An array of required fields
 * @return      array | An array of fields
 */
function rpress_required_checkout_fields( $required_fields ) {
  $enable_flat = rpress_get_option( 'enable_door_flat' );
  $enable_landmark = rpress_get_option( 'enable_landmark' );
  $delivery_method = isset( $_COOKIE['deliveryMethod'] ) ? $_COOKIE['deliveryMethod'] : '';

  $required_fields['rpress_phone'] = array(
    'error_id'      => 'invalid_phone',
    'error_message' =>  __('Please enter a valid phone number', 'restropress')
  );

  if( $enable_flat ) :
    if( $delivery_method !== 'pickup' ) :
      $required_fields['rpress_door_flat'] = array(
        'error_id'          => 'invalid_door_flat',
        'error_message' => __('Please enter your door/flat', 'restropress')
      );
    endif;
  endif;

  if( $enable_landmark ):
    if( $delivery_method !== 'pickup' ) :
      $required_fields['rpress_landmark'] = array(
        'error_id'          => 'invalid_landmark',
        'error_message' => __('Please enter landmark', 'restropress')
      );
    endif;
  endif;

  if ( show_address_on_checkout() ) :
    $required_fields['rpress_customer_address'] = array(
      'error_id'      => 'invalid_address',
      'error_message' => __( 'Please enter your address', 'restropress' )
    );
  endif;

  return $required_fields;
}
add_filter( 'rpress_purchase_form_required_fields', 'rpress_required_checkout_fields' );


/**
 * Stores custom data in payment fields
 *
 * @since       1.0.3
 * @param       array | Payment meta array
 * @return      array | Custom data with payment meta array
 */
function rpress_store_custom_fields( $delivery_address_meta ) {
    
    $delivery_address_meta['phone'] = isset( $_POST['rpress_phone'] ) ? sanitize_text_field( $_POST['rpress_phone'] ) : '';

    $delivery_address_meta['flat'] = isset( $_POST['rpress_door_flat'] ) ? sanitize_text_field( $_POST['rpress_door_flat'] ) : '';

    $delivery_address_meta['landmark'] = isset( $_POST['rpress_landmark'] ) ? sanitize_text_field( $_POST['rpress_landmark'] ) : '';

    $delivery_address_meta['address'] = isset( $_POST['rpress_customer_address'] ) ? sanitize_text_field( $_POST['rpress_customer_address'] ) : '';
  
  return $delivery_address_meta;
}
add_filter( 'rpress_delivery_address_meta', 'rpress_store_custom_fields');



add_filter( 'rpress_order_note_meta', 'rpress_order_note_fields' );

function rpress_order_note_fields( $order_note ) {
  $order_note = isset( $_POST['rpress_order_note'] ) ? sanitize_text_field( $_POST['rpress_order_note'] ) : '';
  return $order_note;
}
/**
 * Add the phone number to the "View Order Details" page
 * Add the flat number to the "View Order Details" page
 * Add the landmark to the "View Order Details" page
 */
function rpress_view_order_details( $payment_id, $payment_meta, $user_info ) {

  $delivery_address_meta = get_post_meta( $payment_id, '_rpress_delivery_address', true );

  $phone = !empty( $payment_meta['phone'] ) ? $payment_meta['phone'] : '';

  if ( empty( $phone ) ) {
    $phone = !empty( $delivery_address_meta['phone'] ) ? $delivery_address_meta['phone'] : '';
  }


  $flat = !empty( $payment_meta['flat'] ) ? $payment_meta['flat'] : '';

  if ( empty( $flat ) ) {
    $flat = !empty( $delivery_address_meta['flat'] ) ? $delivery_address_meta['flat'] : '';
  }

  $landmark = !empty( $payment_meta['landmark'] ) ? $payment_meta['landmark'] : '';

  if( empty( $landmark ) ) {
    $landmark = !empty( $delivery_address_meta['landmark'] ) ? $delivery_address_meta['landmark'] : '';
  }

  $customer_address = !empty( $payment_meta['address'] ) ? $payment_meta['address'] : '';


  if( empty( $customer_address ) ) {
    $customer_address = !empty( $delivery_address_meta['address'] ) ? $delivery_address_meta['address'] : '';
  }

?>
    <div class="column-container">
    <div class="column">

      <?php if ( $customer_address ) : ?>
        <div style="margin-top:10px; margin-bottom:10px;">
          <strong><?php esc_html_e( 'Address:', 'restropress' ); ?> </strong>
          <?php echo $customer_address; ?>
        </div>
      <?php endif; ?>

      
      <?php if( $phone ) : ?>
        <div style="margin-top:10px; margin-bottom:10px;">
          <strong><?php echo __('Phone:', 'restropress'); ?> </strong>
          <?php echo $phone; ?>
        </div>
      <?php endif; ?>

      <?php if( $flat ) : ?>
        <div style="margin-bottom:10px;">
          <strong><?php echo __('Flat:', 'restropress'); ?> </strong>
            <?php echo $flat; ?>
        </div>
      <?php endif; ?>

      <?php if( $landmark) : ?>
        <div style="margin-bottom:10px;">
          <strong><?php echo __('Landmark:', 'restropress'); ?> </strong>
            <?php echo $landmark; ?>
        </div>
      <?php endif; ?>

    </div>
  </div>
<?php
}
add_action( 'rpress_payment_personal_details_list', 'rpress_view_order_details', 10, 3 );

/**
 * Add a {phone} tag for use in either the purchase receipt email or admin notification emails
 * Add a {flat} tag for use in either the purchase receipt email or admin notification emails
 * Add a {landmark} tag for use in either the purchase receipt email or admin notification emails
 */
function checkout_rpress_add_email_tag() {
  rpress_add_email_tag( 'phone', 'Customer\'s phone number', 'rpress_email_tag_phone' );
  rpress_add_email_tag( 'flat', 'Customer\'s flat number', 'rpress_email_tag_flat' );
  rpress_add_email_tag( 'landmark', 'Customer\'s landmark number', 'rpress_email_tag_landmark' );
  rpress_add_email_tag( 'service_type', 'Service Type', 'rpress_email_tag_service_type' );
  rpress_add_email_tag( 'service_time', 'Service Time', 'rpress_email_tag_service_time' );
  rpress_add_email_tag( 'order_note', 'Order note for the order', 'rpress_email_tag_order_note' );
}
add_action( 'rpress_add_email_tags', 'checkout_rpress_add_email_tag' );

/**
* The {order_note} email tag
*/
function rpress_email_tag_order_note( $payment_id ) {
  $order_note = get_post_meta( $payment_id, '_rpress_order_note', true );
  return $order_note;
}

/**
 * The {phone} email tag
 */
function rpress_email_tag_phone( $payment_id ) {
  $payment_meta = get_post_meta( $payment_id, '_rpress_payment_meta', true );
  $delivery_address_meta = get_post_meta( $payment_id, '_rpress_delivery_address', true );

  $phone = !empty( $payment_meta['phone'] ) ? $payment_meta['phone'] : '';

  if ( empty( $phone ) ) {
    $phone = !empty( $delivery_address_meta['phone'] ) ? $delivery_address_meta['phone'] : '';
  }

  return $phone;
}

/**
 * The {flat} email tag
 */
function rpress_email_tag_flat( $payment_id ) {
  $payment_meta = get_post_meta( $payment_id, '_rpress_payment_meta', true );
  $delivery_address_meta = get_post_meta( $payment_id, '_rpress_delivery_address', true );

  $flat = !empty( $payment_meta['flat'] ) ? $payment_meta['flat'] : '';

  if ( empty( $flat ) ) {
    $flat = !empty( $delivery_address_meta['flat'] ) ? $delivery_address_meta['flat'] : '';
  }

  return $flat;
}

/**
 * The {landmark} email tag
 */
function rpress_email_tag_landmark( $payment_id ) {
  $payment_meta = get_post_meta( $payment_id, '_rpress_payment_meta', true );
  $delivery_address_meta = get_post_meta( $payment_id, '_rpress_delivery_address', true );

  $landmark = !empty( $payment_meta['landmark'] ) ? $payment_meta['landmark'] : '';

  if( empty( $landmark ) ) {
    $landmark = !empty( $delivery_address_meta['landmark'] ) ? $delivery_address_meta['landmark'] : '';
  }

  return $landmark;
}

/**
 * The {service_type} email tag
 */
function rpress_email_tag_service_type( $payment_id ) {
  $service_type = get_post_meta( $payment_id, '_rpress_delivery_type', true );
  return $service_type;
}

/**
 * The {service_time} email tag
 */
function rpress_email_tag_service_time( $payment_id ) {
  $service_time = get_post_meta( $payment_id, '_rpress_delivery_time', true );
  return $service_time;
}

/**
 * Get order by statemeny by taxonomy
 *
 * @since       1.0.2
 * @param       string | order by
 * @return      string | order by string passed
 */
function edit_posts_orderby($orderby_statement) {
    $orderby_statement = " term_taxonomy_id ASC ";
  return $orderby_statement;
}

/**
 * Get Delivery type
 *
 * @since       1.0.4
 * @param       Int | Payment_id
 * @return      string | Delivery type string
 */
function rpress_get_delivery_type( $payment_id ) {
  if( $payment_id  ) {
    $delivery_type = get_post_meta( $payment_id, '_rpress_delivery_type', true );

    $deivery_type = !empty( $delivery_type ) ? ucfirst( $delivery_type ) : '-';
    return $deivery_type;
  }
}



function apply_delivery_fee() {
  return apply_filters( 'rpress_apply_delivery_fee', false );
}

function get_delivery_fees() {
  return apply_filters( 'rpress_delivery_fees', 0 );
}


/* Remove View Link From Food Items */
add_filter('post_row_actions','rpress_remove_view_link', 10, 2);

function rpress_remove_view_link($actions, $post){
  if ($post->post_type =="fooditem"){
    unset($actions['view']);
  }
  return $actions;
}

/* Remove View Link From Food Addon Category */
add_filter('addon_category_row_actions','rpress_remove_tax_view_link', 10, 2);

function rpress_remove_tax_view_link($actions, $taxonomy) {
    if( $taxonomy->taxonomy == 'addon_category' ) {
        unset($actions['view']);
    }
    return $actions;
}


/* Remove View Link From Food Category */
add_filter('food-category_row_actions','rpress_remove_food_cat_view_link', 10, 2);

function rpress_remove_food_cat_view_link($actions, $taxonomy) {
  if( $taxonomy->taxonomy == 'food-category' ) {
    unset($actions['view']);
  }
  return $actions;
}


/* Function to check delivery fee addon is enabled so that it would init google map js on popup */
function check_delivery_fee_enabled() {
  $delivery_settings = get_option( 'rpress_delivery_fee', array() );

  $delivery_fee_enable =  isset( $delivery_settings['enable'] ) ? $delivery_settings['enable'] : '';

  $delivery_fee_enable = $delivery_fee_enable ? true : false;

  return apply_filters( 'rpress_delivery_fee_enable', $delivery_fee_enable );
}

function rp_get_store_timings() {
  $current_time = current_time( 'timestamp' );
  $prep_time = !empty( rpress_get_option( 'prep_time' ) ) ? rpress_get_option( 'prep_time' ) : 0;
  $open_time = !empty( rpress_get_option( 'open_time' ) ) ? rpress_get_option( 'open_time' ) : '9:00am';
  
  $close_time = !empty( rpress_get_option( 'close_time' ) ) ? rpress_get_option( 'close_time' ) : '11:30pm';

  $time_interval = apply_filters( 'rp_store_time_interval', 30 );
  $time_interval = $time_interval * 60;

  $prep_time  = $prep_time * 60;
  $open_time  = strtotime( $open_time );
  $close_time = strtotime( $close_time );
  $time_today = apply_filters( 'rpress_timing_for_today', true );

  $store_times = range( $open_time, $close_time, $time_interval );
  
  //If not today then return normal time
  if( !$time_today ) return $store_times;

  //Add prep time to current time to determine the time to display for the dropdown
  if( $prep_time > 0 ) {
    $current_time = $current_time + $prep_time;
  }

  //Store timings for today.
  $store_timings = [];
  foreach( $store_times as $store_time ){
    if( $store_time > $current_time )
      $store_timings[] = $store_time;
  }
  return $store_timings;
}

function rp_get_current_time() {
  $current_time = '';
  $timezone = get_option( 'timezone_string' );
  if( !empty( $timezone ) ) {
    $tz = new DateTimeZone( $timezone );
    $dt = new DateTime( "now", $tz );
    $current_time = $dt->format("H:i:s");
  }
  return $current_time;
}

function rp_get_timezone_date() {
  $current_timezone = get_option( 'timezone_string', true );

  if ( !empty( $timezone ) ) {
    $tz = new DateTimeZone( $timezone );
    $dt = new DateTime( "now", $tz );
    $current_date = $dt->format("F j");
  }
  else {
    $current_date = date("F j");
  }
  return $current_date;
}

/**
 * Get list of categories
 *
 * @since 2.2.4
 * @return array of categories
 */
function rpress_get_categories( $params = array() ) {

  $include = !empty( $params['ids'] ) ? $params['ids'] : array();
  $order_by = !empty( $params['orderby'] ) ? $params['orderby'] : 'include';
  $order = !empty( $params['order'] ) ? $params['order'] : '';

  $taxonomy_name = 'food-category';
  $term_args = array(
    'taxonomy'    => $taxonomy_name,
    'hide_empty'  => true,
    'include'     => $include,
  );

  
  if ( !empty( $order_by ) ) {
    $term_args['orderby'] = $order_by;
  }

  if ( !empty( $order ) ) {
    $term_args['order'] = $order;
  }

  $term_args = apply_filters( 'rpress_get_categories', $term_args );

  $get_all_items = get_terms( $term_args );

  return $get_all_items;
}


/**
 * Get list of categories/subcategories
 *
 * @since 2.5
 * @return array of Get list of categories/subcategories
 */
function rpress_get_child_cats( $category ) {
  $taxonomy_name = 'food-category';
  $parent_term = $category[0];
  $get_child_terms = get_terms( $taxonomy_name, 
      ['child_of'=> $parent_term ] );

  if ( empty( $get_child_terms ) ) {
    $parent_terms = array(
      'taxonomy'    => $taxonomy_name,
      'hide_empty'  => true,
      'include'     => $category,
    );

    $get_child_terms = get_terms( $parent_terms );
  }
  return $get_child_terms;
}


add_action( 'rp_get_categories', 'get_fooditems_categories' );
function get_fooditems_categories( $params ) {
  global $data;
  $data = $params;
  rpress_get_template_part( 'rpress', 'get-categories' );
}

add_filter( 'post_updated_messages', 'rpress_fooditem_update_messages' );
function rpress_fooditem_update_messages( $messages ) {
  global $post, $post_ID;

  $post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );

  foreach( $post_types as $post_type => $post_object ) {
    if ( $post_type == 'fooditem' ) {
      $messages[$post_type] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
        2  => __( 'Custom field updated.' ),
        3  => __( 'Custom field deleted.' ),
        4  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
        5  => isset( $_GET['revision']) ? sprintf( __( '%s restored to revision from %s' ), $post_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6  => sprintf( __( '%s published.' ), $post_object->labels->singular_name ),
        7  => sprintf( __( '%s saved.' ), $post_object->labels->singular_name ),
        8  => sprintf( __( '%s submitted'), $post_object->labels->singular_name),
        9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>'), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), $post_object->labels->singular_name ),
        10 => sprintf( __( '%s draft updated.'), $post_object->labels->singular_name ),
        );
    }
  }

  return $messages;

}
