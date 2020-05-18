<?php
/**
 * Display Breadcrumb
 *
 * @package Catch_Foodmania
 */

if ( ! function_exists( 'catch_foodmania_breadcrumb' ) ) :
	function catch_foodmania_breadcrumb() {
		if ( ! get_theme_mod( 'catch_foodmania_breadcrumb_option', 1 ) ) {
			return false;
		}
		/* === OPTIONS === */
		$text['home']     = esc_html__( 'Home', 'catch-foodmania' ); // text for the 'Home' link
		$text['category'] = esc_html__( '%1$s Archive for %2$s', 'catch-foodmania' ); // text for a category page
		$text['search']   = esc_html__( '%1$sSearch results for: %2$s', 'catch-foodmania' ); // text for a search results page
		$text['tag']      = esc_html__( '%1$sPosts tagged %2$s', 'catch-foodmania' ); // text for a tag page
		$text['author']   = esc_html__( '%1$sView all posts by %2$s', 'catch-foodmania' ); // text for an author page
		$text['404']      = esc_html__( 'Error 404', 'catch-foodmania' ); // text for the 404 page

		$before = '<span class="breadcrumb-current">'; // tag before the current crumb
		$after  = '</span>'; // tag after the current crumb
		/* === END OF OPTIONS === */

		global $post, $paged, $page;
		$linkBefore = '<span class="breadcrumb" typeof="v:Breadcrumb">';
		$linkAfter  = '</span>';
		$linkAttr   = ' rel="v:url" property="v:title"';
		$link       = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;


		if ( ! is_front_page() ) {
			echo '<div class="breadcrumb-area custom">
			<div class="wrapper">
				<nav class="entry-breadcrumbs">';

			echo sprintf( $link, esc_url( home_url( '/' ) ), $text['home'] ); // WPCS: XSS OK.

			if ( is_home() ) {
				echo $before . get_the_title( get_option( 'page_for_posts', true ) ) . $after;
			} elseif ( is_category() ) {
				$thisCat = get_category( get_query_var( 'cat' ), false );

				if ( $thisCat->parent != 0 ) {
					$cats = get_category_parents( $thisCat->parent, true, false );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats;
				}

				the_archive_title( $before . sprintf( $text['category'], '<span class="archive-text">', '</span>' ), $after );

			} elseif ( is_search() ) {
				echo $before . sprintf( $text['search'], '<span class="search-text">', '</span>' . get_search_query() ) . $after;

			} elseif ( is_day() ) {
				echo sprintf( $link, esc_url( get_year_link( get_the_time( __( 'Y', 'catch-foodmania' ) ) ) ), get_the_time( __( 'Y', 'catch-foodmania' ) ) ) ;
				echo sprintf( $link, esc_url( get_month_link( get_the_time( __( 'Y', 'catch-foodmania' ) ), get_the_time( __( 'm', 'catch-foodmania' ) ) ) ), get_the_time( __( 'F', 'catch-foodmania' ) ) );
				echo $before . get_the_time( __( 'd', 'catch-foodmania' ) ) . $after;
			} elseif ( is_month() ) {
				echo sprintf( $link, esc_url( get_year_link( get_the_time( __( 'Y', 'catch-foodmania' ) ) ) ), get_the_time( __( 'Y', 'catch-foodmania' ) ) ) ;
				echo $before . get_the_time( __( 'F', 'catch-foodmania' ) ) . $after;

			} elseif ( is_year() ) {
				echo $before . get_the_time( __( 'Y', 'catch-foodmania' ) ) . $after;

			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					$post_type = get_post_type_object( get_post_type() );
					$post_link = get_post_type_archive_link( $post_type->name );

					printf( $link, esc_url( $post_link ), $post_type->labels->singular_name );

					echo $before . get_the_title() . $after;
				}
				else {
					$cat  = get_the_category();
					$cat  = $cat[0];
					$cats = get_category_parents( $cat, true, '' );
					$cats = preg_replace( "#^(.+)$#", "$1", $cats );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats;

					echo $before . get_the_title() . $after;
				}
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
				$post_type = get_post_type_object( get_post_type() );
				echo isset( $post_type->labels->singular_name ) ? $before . $post_type->labels->singular_name . $after : '';
			} elseif ( is_attachment() ) {
				$parent = get_post( $post->post_parent );
				$cat    = get_the_category( $parent->ID );

				if ( isset( $cat[0] ) ) {
					$cat = $cat[0];
				}

				if ( $cat ) {
					$cats = get_category_parents( $cat, true );
					$cats = str_replace( '<a', $linkBefore . '<a' . $linkAttr, $cats );
					$cats = str_replace( '</a>', '</a>' . $linkAfter, $cats );
					echo $cats;
				}

				printf( $link, esc_url( get_permalink( $parent ) ), $parent->post_title );
				echo $before . get_the_title() . $after;
			} elseif ( is_page() && ! $post->post_parent ) {
				echo $before . get_the_title() . $after;
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id   = $post->post_parent;
				$breadcrumbs = array();

				while( $parent_id ) {
					$page_child    = get_post( $parent_id );
					$breadcrumbs[] = sprintf( $link, esc_url( get_permalink( $page_child->ID ) ), get_the_title( $page_child->ID ) );
					$parent_id     = $page_child->post_parent;
				}

				$breadcrumbs = array_reverse( $breadcrumbs );

				for( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
					echo $breadcrumbs[$i];
				}

				echo $before . get_the_title() . $after;
			} elseif ( is_tag() ) {
				the_archive_title( $before . sprintf( $text['tag'], '<span class="tag-text">', '</span>' ), $after );

			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata( $author );
				echo $before . sprintf( $text['author'], '<span class="author-text">', '</span>' . $userdata->display_name ) . $after;

			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;

			}

			if ( get_query_var( 'paged' ) ) {
				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ' (';
				}

				echo sprintf( esc_html__( 'Page %s', 'catch-foodmania' ), max( $paged, $page ) );

				if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
					echo ')';
				}
			}

			echo '</nav><!-- .entry-breadcrumbs -->
			</div> <!-- .wrapper -->
			</div><!-- .breadcrumb-area -->';
		}
	} // end catch_foodmania_breadcrumb_lists
endif;
