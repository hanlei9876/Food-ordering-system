<?php
/**
 * The template for displaying search form
 *
 * @package Catch_Foodmania
 */
?>

<?php
$unique_id   = uniqid( 'search-form-' );
$search_text = get_theme_mod( 'catch_foodmania_search_text', esc_html__( 'Search ...', 'catch-foodmania' ) );
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $unique_id ); ?>">
		<span class="screen-reader-text"><?php echo esc_html_x( 'Search for:', 'label', 'catch-foodmania' ); ?></span>
		<input type="search" id="<?php echo esc_attr( $unique_id ); ?>" class="search-field" placeholder="<?php echo esc_attr( $search_text ); ?>" value="<?php the_search_query(); ?>" name="s" title="<?php echo esc_attr( _x( 'Search for:', 'label', 'catch-foodmania' ) ); ?>">
	</label>
	
	<button type="submit" class="search-submit fa fa-search"></button>
</form>
