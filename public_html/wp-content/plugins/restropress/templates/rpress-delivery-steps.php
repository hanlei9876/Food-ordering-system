<?php
global $rpress_options;
$service_option = !empty( rpress_get_option( 'enable_service' ) ) ? rpress_get_option( 'enable_service' ) : 'delivery_and_pickup' ;
$store_timings = get_option( 'rpress_store_timing' );
$color = rpress_get_option( 'checkout_color', 'red' );

$pre_order_class = '';
if( is_array( $store_timings ) && isset( $store_timings['enable'] ) ) {
	if( isset( $store_timings['pre_order'] ) ) {
		$pre_order_class = 'pre-order-enable';
	}
}

$store_times = rp_get_store_timings();
$store_times = apply_filters( 'rpress_store_delivery_timings', $store_times );

//If empty check if pickup hours are available
if( empty( $store_times ) )
	$store_times = apply_filters( 'rpress_store_pickup_timings', $store_times );

$closed_message = rpress_get_option( 'store_closed_msg', __( 'Sorry, we are closed for ordering now.', 'restropress' ) );
?>

<div class="rpress-delivery-wrap <?php echo $color; ?> ">
	
  <?php if ( empty( $store_times ) ) : ?>
		<div class="alert alert-warning">
			<?php echo $closed_message; ?>
		</div>
	<?php else: ?>
	 
    <div class="rpress-row">
  			
      <!-- Error Message Starts Here -->
      <div class="alert alert-warning rpress-errors-wrap disabled"></div>
  	  <!-- Error Message Ends Here -->

      <?php do_action( 'rpress_delivery_location_field' ); ?>

		  <div class="rpress-tabs-wrapper rpress-delivery-options text-center service-option-<?php echo $service_option; ?>">
		    
        <ul class="nav nav-pills" id="rpressdeliveryTab">
			
        <?php
        switch ( $service_option ) {
          case 'delivery':
			   ?>
          <!-- Delivery Option Starts Here -->
          <li class="nav-item">
            <a class="nav-link single-service-selected <?php echo $color; ?>" id="nav-delivery-tab" data-delivery-type="delivery" data-toggle="tab" href="#nav-delivery" role="tab" aria-controls="nav-delivery" aria-selected="false">
  			   <?php esc_html_e( 'Delivery', 'restropress' ); ?>
  			   </a>
  			 </li>
  			<!-- Delivery Option Ends Here -->
			
        <?php
			   break;
						
			   case 'pickup':
			 ?>
        <!-- Pickup Option Starts Here -->
			 <li class="nav-item">
        <a class="nav-link single-service-selected <?php echo $color; ?>" id="nav-pickup-tab" data-delivery-type="pickup" data-toggle="tab" href="#nav-pickup" role="tab" aria-controls="nav-pickup" aria-selected="false">
          <?php esc_html_e( 'Pickup', 'restropress' ); ?>	
        </a>
			 </li>
			 <!-- Pickup Option Ends Here -->
			
        <?php
          break;
			    case 'delivery_and_pickup':
			  ?>
			
        <!-- Delivery Option Starts Here -->
			 <li class="nav-item">
			   <a class="nav-link <?php echo $color; ?>" id="nav-delivery-tab" data-delivery-type="<?php esc_html_e( 'delivery', 'restropress' ); ?>" data-toggle="tab" href="#nav-delivery" role="tab" aria-controls="nav-delivery" aria-selected="false">
			     <?php esc_html_e('Delivery', 'restropress'); ?>
			   </a>
		    </li>
			 <!-- Delivery Option Ends Here -->

			 <!-- Pickup Option Starts Here -->
			 <li class="nav-item">
			   <a class="nav-link <?php echo $color; ?>" id="nav-pickup-tab" data-delivery-type="<?php esc_html_e( 'pickup', 'restropress' );?>" data-toggle="tab" href="#nav-pickup" role="tab" aria-controls="nav-pickup" aria-selected="false">
			   <?php esc_html_e('Pickup', 'restropress'); ?>	
			   </a>
			</li>
			<!-- Pickup Option Ends Here -->
			<?php
		    break;
			}
		  ?>
		</ul>
				
		<div class="tab-content" id="rpress-tab-content">
		  <?php
		  switch ( $service_option ) {
        case 'delivery':
          rpress_get_template_part( 'rpress', 'delivery' );
          break;
						
        case 'pickup':
          rpress_get_template_part( 'rpress', 'pickup' );
          break;
						
        case 'delivery_and_pickup':
				  rpress_get_template_part( 'rpress', 'delivery' );
					rpress_get_template_part( 'rpress', 'pickup' );
					break;
			}
		  ?>
		  <button type="button" data-food-id='{FoodID}' class="btn btn-primary btn-block rpress-delivery-opt-update <?php echo $color;?> ">
		    <?php esc_html_e('Update','restropress'); ?> 
		  </button>
    </div>

    </div>
  </div>

<?php endif; ?>

</div>