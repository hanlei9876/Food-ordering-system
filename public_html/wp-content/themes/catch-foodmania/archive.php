<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Catch_Foodmania
 */

get_header(); ?>
		<div id="primary" class="content-area">
			<main id="main" class="site-main">
				<div class="archive-content-wrap">

					<?php
					if ( have_posts() ) : ?>

						<?php
						$header_image = catch_foodmania_featured_overall_image();

						if ( ! $header_image ) : ?>

						<header class="page-header">
							<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
						</header><!-- .page-header -->

						<?php the_archive_description( '<p>', '</p>' ); ?>

						<?php endif; ?>

						<div class="section-content-wrapper">
							<?php
							/* Start the Loop */
							while ( have_posts() ) : the_post();

								/*
								 * Include the Post-Format-specific template for the content.
								 * If you want to override this in a child theme, then include a file
								 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
								 */
								get_template_part( 'template-parts/content/content', get_post_format() );

							endwhile;
							?>
						</div> <!-- .section-content-wrapper -->

						<?php
						catch_foodmania_content_nav();

					else :

						get_template_part( 'template-parts/content/content', 'none' );

					endif; ?>
				</div>  <!-- .archive-content-wrap -->
			</main><!-- #main -->
		</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
