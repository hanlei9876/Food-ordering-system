<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Catch_Foodmania
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="singular-content-wrap">
				<?php if ( is_active_sidebar( 'sidebar-notfound' ) ) :
						dynamic_sidebar( 'sidebar-notfound' );
					else : ?>
					<section class="error-404 not-found">
						<div class="page-content">

							<?php
							$header_image = catch_foodmania_featured_overall_image();

							if ( ! $header_image ) : ?>

							<header class="page-header">
								<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'catch-foodmania' ); ?></h1>
							</header><!-- .page-header -->

							<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try one of the links below or a search?', 'catch-foodmania' ); ?></p>

							<?php endif; ?>
							<?php
								get_search_form();

								the_widget( 'WP_Widget_Recent_Posts' );
							?>

							<div class="widget widget_categories">
								<h2 class="widget-title"><?php esc_html_e( 'Most Used Categories', 'catch-foodmania' ); ?></h2>
								<ul>
								<?php
									wp_list_categories( array(
										'orderby'    => 'count',
										'order'      => 'DESC',
										'show_count' => 1,
										'title_li'   => '',
										'number'     => 10,
									) );
								?>
								</ul>
							</div><!-- .widget -->

							<?php

								/* translators: %1$s: smiley */
								$archive_content = '<p>' . sprintf( esc_html__( 'Try looking in the monthly archives. %1$s', 'catch-foodmania' ), convert_smilies( ':)' ) ) . '</p>';
								the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content" );

								the_widget( 'WP_Widget_Tag_Cloud' );
							?>
						</div><!-- .page-content -->
					</section><!-- .error-404 -->
				<?php endif; ?>
			</div> <!-- .singular-content-wrap -->
		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
