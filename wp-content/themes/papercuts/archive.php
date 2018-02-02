<?php
/**
 * The archive template file.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/
get_header(); ?>
<?php if ( have_posts() ) : ?>
    <div class="entry-content"> 
      <div class="entry-content-inner">
<?php papercuts_get_breadcrumb(); ?>
        <h1 class="content-headline"><?php
					if ( is_day() ) :
						printf( __( 'Daily Archive: %s', 'papercuts' ), '<span>' . get_the_date() . '</span>' );
					elseif ( is_month() ) :
						printf( __( 'Monthly Archive: %s', 'papercuts' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'papercuts' ) ) . '</span>' );
					elseif ( is_year() ) :
						printf( __( 'Yearly Archive: %s', 'papercuts' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'papercuts' ) ) . '</span>' );
					else :
						_e( 'Archive', 'papercuts' );
					endif;
				?></h1>
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