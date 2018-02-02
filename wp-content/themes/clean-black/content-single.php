<article id="post-<?php the_ID(); ?>">
		<h1 class="entry-title"><?php the_title(); ?></h1>
<div class="entry-content">
  <?php if(is_single()) { ?><div class="mediumfont"><div class="dateview">Written by <a rel="author" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author() ?></a>
	&nbsp;on&nbsp;<?php the_time('F j, Y'); ?>&nbsp;<?php edit_post_link( __( 'Edit', 'cleanblack' ), '<span class="edit-link">(', ')</span>' ); ?></div></div><div style="clear:both;"></div><?php } ?>
  <?php the_content(); ?>
		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'cleanblack' ),
				'after'  => '</div>',
			) );
		?>
  </div><!-- .entry-content -->
</article><!-- #post-## -->
<?php if(is_single()) { ?>
<div class="tagscat">
  <p><span class="categorytag">Category</span> : <?php the_category(' • ','',''); ?></p>
  <?php if(has_tag()) { ?><p><span class="categorytag">Tags</span> : <?php the_tags('',' • ','') ?></p><?php } ?></div>
<?php } ?>