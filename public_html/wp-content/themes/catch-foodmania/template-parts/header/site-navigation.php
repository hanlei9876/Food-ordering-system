<?php
/**
 * Primary Menu Template
 *
 * @package Catch Foodmania Pro
 */

?>
<div id="site-header-menu" class="site-header-menu">
	<div id="primary-menu-wrapper" class="menu-wrapper">
		<div class="menu-toggle-wrapper">
			<button id="menu-toggle" class="menu-toggle" aria-controls="top-menu" aria-expanded="false">
				<span class="menu-label"><?php echo esc_html_e( 'Menu', 'catch-foodmania' ); ?></span>
			</button>
		</div><!-- .menu-toggle-wrapper -->

		<div class="menu-inside-wrapper">
			<?php if ( has_nav_menu( 'menu-1' ) ) : ?>

				<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'catch-foodmania' ); ?>">
					<?php
						wp_nav_menu( array(
								'container'      => '',
								'theme_location' => 'menu-1',
								'menu_id'        => 'primary-menu',
								'menu_class'     => 'menu nav-menu',
							)
						);
					?>

			<?php else : ?>

				<nav id="site-navigation" class="main-navigation default-page-menu" role="navigation" aria-label="<?php esc_attr_e( 'Primary Menu', 'catch-foodmania' ); ?>">
					<?php wp_page_menu(
						array(
							'menu_class' => 'primary-menu-container',
							'before'     => '<ul id="primary-menu" class="menu nav-menu">',
							'after'      => '</ul>',
						)
					); ?>

			<?php endif; ?>

				</nav><!-- .main-navigation -->

			<div class="mobile-social-search">

				<?php if ( ! get_theme_mod( 'my_music_band_primary_search_disable' ) ) : ?>
				<div class="search-container">
					<?php get_search_form(); ?>
				</div>
				<?php endif; ?>
			</div><!-- .mobile-social-search -->
		</div><!-- .menu-inside-wrapper -->
	</div><!-- #primary-menu-wrapper.menu-wrapper -->

	<?php if ( ! get_theme_mod( 'my_music_band_primary_search_disable' ) ) : ?>
	<div id="primary-search-wrapper" class="menu-wrapper">
		<?php if ( has_nav_menu( 'social-menu' ) && 'classic' === get_theme_mod( 'my_music_band_menu_type' ) ) : ?>
			<?php get_template_part('template-parts/navigation/navigation', 'social'); ?>
		<?php endif; ?>

		<div class="menu-toggle-wrapper">
			<button id="social-search-toggle" class="menu-toggle">
				<span class="menu-label screen-reader-text"><?php echo esc_html_e( 'Search', 'catch-foodmania' ); ?></span>
			</button>
		</div><!-- .menu-toggle-wrapper -->

		<div class="menu-inside-wrapper">
			<div class="search-container">
				<?php get_Search_form(); ?>
			</div>
		</div><!-- .menu-inside-wrapper -->
	</div><!-- #social-search-wrapper.menu-wrapper -->
	<?php endif; ?>
</div><!-- .site-header-menu -->

