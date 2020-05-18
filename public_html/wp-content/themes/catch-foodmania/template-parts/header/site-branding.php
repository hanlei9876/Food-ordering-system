<div class="site-branding">
	<?php
	if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) :
	    the_custom_logo();
	?>

	<a class="scrolled-logo-link" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>">
		<?php
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$logo           = wp_get_attachment_image_src( $custom_logo_id , 'full' );
		?>

		<img class="scrolled-logo" alt="<?php bloginfo( 'name' ); ?>" src="<?php echo esc_url( $logo[0] ); ?>">
	</a>
	<?php endif; // has_custom_logo check. ?>

	<div class="site-identity">
		<?php if ( is_front_page() && is_home() ) : ?>
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		<?php else : ?>
			<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
		<?php endif; ?>

		<?php
		$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) : ?>
			<p class="site-description"><?php echo $description; /* WPCS: xss ok. */ ?></p>
		<?php endif; ?>
	</div>
</div><!-- .site-branding -->
