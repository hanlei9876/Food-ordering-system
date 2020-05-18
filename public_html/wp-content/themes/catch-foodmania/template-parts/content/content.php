<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Catch_Foodmania
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="hentry-inner">

		<?php
		$thumb = get_the_post_thumbnail_url( $post->ID );

		if ( ! $thumb ) {
			$thumb = trailingslashit( esc_url ( get_template_directory_uri() ) ) . 'assets/images/no-thumb-666x666.jpg';
		}

		?>
		<div class="post-thumbnail" style="background-image: url( '<?php echo esc_url( $thumb ); ?>' )">
			<a href="<?php the_permalink(); ?>" rel="bookmark"></a>
		</div>

		<div class="entry-container">
			<?php if ( is_sticky() ) { ?>
			<span class="sticky-label"><?php esc_html_e( 'Featured', 'catch-foodmania' ); ?></span>
			<?php } ?>

			<header class="entry-header">
				<?php
				if ( 'post' === get_post_type() ) : ?>
					<div class="entry-category">
						<?php catch_foodmania_entry_category(); ?>
					</div><!-- .entry_category -->
					<?php

					if ( is_singular() ) :
						the_title( '<h1 class="entry-title">', '</h1>' );
					else :
						the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
					endif; ?>
					<div class="entry-meta">
						<?php catch_foodmania_posted_on(); ?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->

			<div class="entry-summary">
				<?php the_excerpt(); ?>
			</div><!-- .entry-summary -->
		</div> <!-- .entry-container -->
	</div> <!-- .hentry-inner -->
</article><!-- #post-<?php the_ID(); ?> -->
