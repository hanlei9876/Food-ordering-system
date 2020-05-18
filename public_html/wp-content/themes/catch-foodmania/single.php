<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Catch_Foodmania
 */

get_header(); ?>

	<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<div class="singular-content-wrap">
					<?php
						while ( have_posts() ) : the_post();

						get_template_part( 'template-parts/content/content', 'single' );

						the_post_navigation( array(
				            'prev_text' => '<span class="nav-subtitle">' . __( 'Previous', 'catch-foodmania' ) . '</span>' . '<i class="fa fa-long-arrow-left" aria-hidden="true"></i>' .'<span>'. '%title' .'</span>',
				            'next_text' => '<span class="nav-subtitle">' . __( 'Next', 'catch-foodmania' ) . '</span>' . '<span>'.  '%title' .'</span>' . '<i class="fa fa-long-arrow-right" aria-hidden="true"></i>',
				        ) );

						get_template_part( 'template-parts/content/content', 'comment' );

						endwhile; // End of the loop.
					?>
				</div> <!--  .singular-content-wrap -->
			</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
