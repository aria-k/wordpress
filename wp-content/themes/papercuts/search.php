<?php
/**
 * The search results template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if ( have_posts() ) : ?>
    <div class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php printf( __( 'Search Results for: %s', 'papercuts' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
        <div class="archive-meta"><p class="number-of-results"><?php _e( 'Number of Results: ', 'papercuts' ); ?><?php echo $wp_query->found_posts; ?></p></div>
      </div>
    </div>
<?php while (have_posts()) : the_post(); ?>      
<?php get_template_part( 'content', 'archives' ); ?>
<?php endwhile; ?>

<?php if ( $wp_query->max_num_pages > 1 ) : ?>
		<div class="navigation" role="navigation">
    <div class="navigation-inner">
			<h2 class="navigation-headline section-heading"><?php _e( 'Search results navigation', 'papercuts' ); ?></h2>
      <div class="nav-wrapper">
			<p class="nav-previous"><?php previous_posts_link( __( '<span class="meta-nav">&larr;</span> Previous results', 'papercuts' ) ); ?></p>
      <p class="nav-next"><?php next_posts_link( __( 'Next results <span class="meta-nav">&rarr;</span>', 'papercuts' ) ); ?></p>
      </div>
		</div>
    </div>
<?php endif; ?>

<?php else : ?>
    <div class="entry-content"> 
      <div class="entry-content-inner">
        <h1 class="content-headline"><?php _e( 'Nothing Found', 'papercuts' ); ?></h1>
        <div class="archive-meta"><p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'papercuts' ); ?></p><?php get_search_form(); ?></div>
      </div>
    </div>
<?php endif; ?>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>