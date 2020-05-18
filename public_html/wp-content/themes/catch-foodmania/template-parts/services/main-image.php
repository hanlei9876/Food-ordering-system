<?php
/**
 * The template for displaying services image content
 *
 * @package Catch_Foodmania
 */

$image  = get_theme_mod( 'catch_foodmania_service_main_image' );
if ( $image ) :
?>
	<div class="special-image">
		<img src="<?php echo esc_url( $image ); ?>"/>
	</div>
<?php endif; ?>
