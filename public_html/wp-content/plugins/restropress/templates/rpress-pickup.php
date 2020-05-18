<div class="tab-pane fade delivery-settings-wrapper" id="nav-pickup" role="tabpanel" aria-labelledby="nav-pickup-tab">

  <!-- Pickup Time Starts Here -->
  <div class="rpress-pickup-time-wrap rpress-time-wrap  <?php echo $preorder_class; ?>">

    <?php do_action( 'rpress_pre_order_dates' ); ?>
      
    <?php 

    if ( rpress_is_service_enabled( 'pickup' ) ) : 
      
      $current_time = current_time( 'h:ia' );
      $store_times = rp_get_store_timings();
      $store_timings = apply_filters( 'rpress_store_pickup_timings', $store_times );

      $store_time_format = rpress_get_option( 'store_time_format' );

      if ( empty( $store_time_format ) ) {
        $store_time_format = '12hrs';
      }

      if ( $store_time_format == '24hrs' ) {
        $time_format = 'H:i';
      }
      else {
        $time_format = 'h:ia';
      }
      
      ?>
      <div class="pickup-time-text">
        <?php echo __( 'Select a pickup time', 'restropress' ); ?>
      </div>

      <select class="<?php echo $preorder_class; ?> rpress-pickup rpress-allowed-pickup-hrs rpress-hrs rp-form-control" id="rpress-pickup-hours" name="rpress_allowed_hours">
    	
        <?php
        if ( is_array( $store_timings ) ) :
          foreach( $store_timings as $time ) :
            $loop_time = date( $time_format, $time );
        ?>
        <option value='<?php echo $loop_time; ?>'>
          <?php echo $loop_time; ?>    
        </option>
        
        <?php
          endforeach;
        endif;
    		?>
    	</select>
    <?php endif; ?>

    <?php do_action( 'after_delivery_time', 'pickup' ); ?>

	</div>
	<!-- Pickup Time Ends Here -->
</div>