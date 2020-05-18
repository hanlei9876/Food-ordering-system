<?php
$color = rpress_get_option( 'checkout_color', 'red' );

global $data;

$taxonomy_name = 'food-category';

$get_child_terms = array();

if ( $data['category_menu'] ) {
  $get_all_items = rpress_get_child_cats( $data['ids'] );
}
else {
  $get_all_items = rpress_get_categories( $data );
}
?>
<div class="rp-col-lg-2 rp-col-md-2 rp-col-sm-3 rp-col-xs-12 sticky-sidebar cat-lists">
  <div class="rpress-filter-wrapper">
    <div class="rpress-categories-menu">
      <?php if( !empty( $get_all_items ) ) : ?>
      <ul class="rpress-category-lists">
        <?php foreach ( $get_all_items as $get_all_item ) : ?>
          <li class="rpress-category-item">
            <a href="#<?php echo $get_all_item->slug; ?>" data-id="<?php echo $get_all_item->term_id; ?>" class="rpress-category-link  nav-scroller-item <?php echo $color; ?>"><?php echo $get_all_item->name; ?>  
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
    </div>
  </div>

</div>