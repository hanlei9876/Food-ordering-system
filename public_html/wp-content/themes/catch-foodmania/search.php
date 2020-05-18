<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Catch_Foodmania
 */

get_header(); ?>



	<section id="primary" class="content-area">
		<main id="main" class="site-main">
			<div class="archive-content-wrap">

				<?php
				$header_image = catch_foodmania_featured_overall_image();

				if ( ! $header_image ) : ?>

				<header class="page-header">
					<h1 class="page-title"><?php
							/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'catch-foodmania' ), '<span>' . get_search_query() . '</span>' );
					?></h1>
				</header><!-- .page-header -->

				<?php endif;
				if ( have_posts() ) : ?>
					<div class="section-content-wrapper">
					<?php
					/* Start the Loop */
					while ( have_posts() ) : the_post();

						/**
						 * Run the loop for the search to output the results.
						 * If you want to overload this in a child theme then include a file
						 * called content-search.php and that will be used instead.
						 */
						get_template_part( 'template-parts/content/content', 'search' );

					endwhile;
					?>
					</div> <!-- .section-content-wrapper -->

					<?php
					catch_foodmania_content_nav();

				else :

					get_template_part( 'template-parts/content/content', 'none' );

				endif; ?>
			</div><!-- .archive-content-wrap -->
		</main><!-- #main -->
	</section><!-- #primary -->
<?php
get_sidebar();
get_footer();
