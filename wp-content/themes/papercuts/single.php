<?php
/**
 * The post template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php the_title(); ?></h1>
<?php papercuts_get_display_image_post(); ?>
<?php if ( $papercuts_options_db['papercuts_display_meta_post'] != 'Hide' ) { ?>
        <p class="post-meta">
          <span class="post-info-author"><?php _e( 'Author: ', 'papercuts' ); ?><?php the_author_posts_link(); ?></span>
          <span class="post-info-date"><?php echo get_the_date(); ?></span>
<?php if ( comments_open() ) : ?>
          <span class="post-info-comments"><a href="<?php comments_link(); ?>"><?php printf( _n( '1 Comment', '%1$s Comments', get_comments_number(), 'papercuts' ), number_format_i18n( get_comments_number() ), get_the_title() ); ?></a></span>
<?php endif; ?>
        </p>
        <div class="post-info">
          <p class="post-category"><span class="post-info-category"><?php the_category(', '); ?></span></p>
          <p class="post-tags"><?php the_tags( '<span class="post-info-tags">', ', ', '</span>' ); ?></p>
        </div>
<?php } ?>
<?php the_content(); ?>
<?php wp_link_pages( array( 'before' => '<p class="page-link"><span>' . __( 'Pages:', 'papercuts' ) . '</span>', 'after' => '</p>' ) ); ?>
<?php endwhile; endif; ?>
<?php if ( $papercuts_options_db['papercuts_next_preview_post'] != 'Hide' ) { ?>
<?php papercuts_prev_next('papercuts-post-nav'); ?>
<?php } ?>
<?php comments_template( '', true ); ?>
      </div>
    </article>
  </div> <!-- end of content -->
<?php get_sidebar(); ?>
  </div> <!-- end of main-content -->
</div> <!-- end of container -->
<?php get_footer(); ?>