<?php
/*
 * Template Name: Right Sidebar ( Content, Primary Sidebar )
 *
 * Template Post Type: post, page
 *
 * The template for displaying Page/Post with Sidebar on right
 *
 * @package Catch_Foodmania
 */

get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <div class="singular-content-wrap">
                <?php
                // Start the loop.
                while ( have_posts() ) : the_post();

                    $template = 'single';

                    if ( is_page() ) {
                        $template = 'page';
                    }

                    // Include the page content template.
                    get_template_part( 'template-parts/content/content', $template );

                    // Comments Templates
                    get_template_part( 'template-parts/content/content', 'comment' );

                    // End of the loop.
                endwhile;
                ?>
                </div> <!-- singular-content-wrap -->
        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_sidebar();
get_footer();
