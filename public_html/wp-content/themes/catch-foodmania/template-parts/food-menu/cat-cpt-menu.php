<?php
/**
 * The template for displaying food_menu items
 *
 * @package Catch_Foodmania
 */
?>

<?php
$number = get_theme_mod( 'catch_foodmania_food_menu_number', 5 );

if ( ! $number ) {
	// If number is 0, then this section is disabled
	return;
}

$cat_list = array();

for ( $i = 1; $i <= $number; $i++ ) {
	$cat_id = get_theme_mod( 'catch_foodmania_food_menu_cpt_' . $i );

	if ( $cat_id && '' !== $cat_id ) {
		$cat_list = array_merge( $cat_list, array( $cat_id ) );
	}
}

if ( empty( $cat_list ) ) {
	// Bail if category list is empty.
	return;
}
?>
<div id="tabs" class="tabs">
	<div class="tabs-nav">
		<ul class="ui-tabs-nav">
			<?php
			$i = 0;
			foreach ( $cat_list as $cat ) :
				$term_obj = get_term_by( 'id', absint( $cat ), 'ect_food_menu' );
				if( $term_obj ) {
					$term_name = $cat_name[] = $term_obj->name;

					$class = 'ui-tabs-tab';

					if ( 0 === $i ) {
						$class .= ' ui-state-active';
					}

					?>
					<li class="<?php echo $class; ?>"><a href="#tab-<?php echo esc_attr( $i + 1 ); ?>" class="ui-tabs-anchor"><?php echo esc_html( $term_obj->name ) ?></a></li>
					<?php
				}
				$i++;
			endforeach;
			?>
		</ul>
	</div><!-- .tabs-nav -->

	<?php
	$i = 0;
	foreach ( $cat_list as $cat ) :
		if( isset( $cat_name ) ) {
	?>

		<div class="ui-tabs-panel-wrap">
			<h4 class="ui-nav-collapse<?php  echo ( 0 === $i ) ? ' ui-state-active' : ''; ?>"><a href="#tab-<?php echo esc_attr( $i + 1 ); ?>" class="ui-tabs-anchor"><?php echo esc_html( $cat_name[ $i ] ); ?></a></h4>
			<div id="tab-<?php echo esc_attr( $i + 1 ); ?>" class="layout-two ui-tabs-panel<?php  echo ( 0 === $i ) ? ' active-tab' : ''; ?>">
				<?php
				$tax_query = array(
					array(
						'taxonomy'  => 'ect_food_menu',
						'terms'     => absint( $cat ),
						'field'     => 'term_id',
						'post_type' => 'ect_food_menu_item',
					),
				);

				$args['tax_query'] = $tax_query;

				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) :
					while ( $loop->have_posts() ) :
						$loop->the_post();

						get_template_part( 'template-parts/food-menu/content', 'menu' );
					endwhile;
				endif;
				wp_reset_postdata();
				?>
			</div><!-- #tab-1 -->
		</div><!-- .ui-tabs-panel-wrap -->

	<?php
		}
		$i++;
	endforeach;
	?>
</div><!-- .tabs -->
