<?php
/**
 * The page template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php the_title(); ?></h1>
<?php papercuts_get_display_image_page(); ?>
<?php the_content(); ?>
<?php endwhile; endif; ?>
<?php comments_template( '', true ); ?>
      </div>
    </article>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>