<?php
/**
 * The 404 page (Not Found) template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
    <article class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php _e( 'Nothing Found', 'papercuts' ); ?></h1>
<p><?php _e( 'Apologies, but no results were found for your request. Perhaps searching will help you to find a related content.', 'papercuts' ); ?></p><?php get_search_form(); ?>
      </div>
    </article>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>