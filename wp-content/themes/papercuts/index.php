<?php
/**
 * The main template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header();

echo 1111;
 
 ?>

<?php if ($papercuts_options_db['papercuts_display_latest_posts'] != 'Hide'){ ?>    
  <section class="home-latest-posts">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php get_template_part( 'content', 'archives' ); ?>
<?php endwhile; endif; ?>
<?php papercuts_content_nav( 'nav-below' ); ?>
  </section>
<?php } ?>
<?php if ( dynamic_sidebar( 'sidebar-6' ) ) : else : ?>
<?php endif; ?>  
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>