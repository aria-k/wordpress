<?php
/**
 * The tag archive template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if ( have_posts() ) : ?>
    <div class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php printf( __( 'Tag Archive: %s', 'papercuts' ), '<span>' . single_tag_title( '', false ) . '</span>' ); ?></h1>
<?php if ( tag_description() ) : ?>
        <div class="archive-meta"><?php echo tag_description(); ?></div>
<?php endif; ?>
      </div>
    </div>
<?php while (have_posts()) : the_post(); ?>      
<?php get_template_part( 'content', 'archives' ); ?>
<?php endwhile; endif; ?>
<?php papercuts_content_nav( 'nav-below' ); ?>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>