<?php
/**
 * The template for displaying archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * If you'd like to further customize these archive views, you may create a
 * new template file for each one. For example, tag.php (Tag archives),
 * category.php (Category archives), author.php (Author archives), etc.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package GWT
 * @since Government Website Template 2.0
 */

get_header();
include_once('inc/banner.php');
?>
<?php govph_displayoptions( 'govph_panel_top' ); ?>

<div id="container-main" class="container-main" role="document">
    <div id="main-content" class="row">
        <div id="content" class="text-justify <?php govph_displayoptions( 'govph_content_position' ); ?>columns"
            role="main">
            <!-- For Version 1 -->
            <!-- <div class="large-12 container-main">
                <header>
                    <h1 class="page-title">
                        <?php
							if ( is_category() ) : single_cat_title();
							elseif ( is_tag() ) : single_tag_title();
							elseif ( is_author() ) :
							/* Queue the first post, that way we know
							 * what author we're dealing with (if that is the case).
							 */
							the_post();
							printf( __( 'Author: %s', 'gwt_wp' ), '<span class="vcard">' . get_the_author() . '</span>' );
							/* Since we called the_post() above, we need to
							 * rewind the loop back to the beginning that way
							 * we can run the loop properly, in full.
							 */
							rewind_posts();

							elseif ( is_day() ) : printf( __( 'Day: %s', 'gwt_wp' ), '<span>' . get_the_date() . '</span>' );
							elseif ( is_month() ) : printf( __( 'Month: %s', 'gwt_wp' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );
							elseif ( is_year() ) : printf( __( 'Year: %s', 'gwt_wp' ), '<span>' . get_the_date( 'Y' ) . '</span>' );
							elseif ( is_tax( 'post_format', 'post-format-aside' ) ) : _e( 'Asides', 'gwt_wp' );
							elseif ( is_tax( 'post_format', 'post-format-image' ) ) : _e( 'Images', 'gwt_wp');
							elseif ( is_tax( 'post_format', 'post-format-video' ) ) : _e( 'Videos', 'gwt_wp' );
							elseif ( is_tax( 'post_format', 'post-format-quote' ) ) : _e( 'Quotes', 'gwt_wp' );
							elseif ( is_tax( 'post_format', 'post-format-link' ) ) : _e( 'Links', 'gwt_wp' );
							else : _e( 'Archives', 'gwt_wp' );
							endif;
							?>
                    </h1>
                    <?php
						// Show an optional term description.
						$term_description = term_description();
						if ( ! empty( $term_description ) ) : printf( '<div class="taxonomy-description">%s</div>', $term_description );
						endif;
						?>
                </header>
            </div> -->
            <!-- End for Version 1 -->
            <?php if ( have_posts() ) : ?>
            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

            <?php
							/* Include the Post-Format-specific template for the content.
							 * If you want to overload this in a child theme then include a file
							 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
							 */
							get_template_part( 'template-parts/content', get_post_format() );
						?>

            <?php endwhile; ?>

            <?php gwt_wp_content_nav( 'nav-below' ); ?>

            <?php else : ?>

            <?php get_template_part( 'no-results', 'archive' ); ?>

            <?php endif; ?>
        </div><!-- #content -->

        <?php 
			if(is_active_sidebar('left-sidebar')){
				govph_displayoptions( 'govph_sidebar_left' );
			}
			?>
        <?php 
			if(is_active_sidebar('right-sidebar')){
				govph_displayoptions( 'govph_sidebar_right' );
			}
			?>
    </div><!-- #main -->
</div><!-- #primary -->

<?php govph_displayoptions( 'govph_panel_bottom' ); ?>

<?php get_footer(); ?>