<?php
/**
 * Template Name: Logged In
 * The template file for displaying the page content only for logged in users.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php the_title(); ?></h1>
<?php if ( is_user_logged_in() ) { ?>
<?php papercuts_get_display_image_page(); ?>
<?php the_content(); ?>
<?php } else { ?>
<p class="logged-in-message"><?php _e( 'You must be logged in to view this page.', 'papercuts' ); ?></p>
<?php wp_login_form(); } ?>
<?php endwhile; endif; ?>
<?php if ( is_user_logged_in() ) { ?>
<?php comments_template( '', true ); ?>
<?php } ?>
      </div>
    </article>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>