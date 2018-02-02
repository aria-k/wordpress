<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="grid">
  <div <?php post_class(); ?>>
  <h1><a href="<?php the_permalink() ?>" rel="bookmark"><?php if(get_the_title()) { the_title(); } else { echo'#'; } ?></a></h1>
  <div class="mediumfont"><div class="dateview">Written by <a rel="author" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author() ?></a>&nbsp;on&nbsp;<?php the_time('F j, Y'); ?></div><div style="clear:both;"></div></div>
  <?php if(cleanblack_get_options("postthumb")) { ?><div id="thumbnailright"><?php the_post_thumbnail('thumbnail'); ?></div><?php }?>
  <p><?php cleanblack_content_limit(150, ""); ?></p>
  </div></div>
			<?php endwhile; else: ?>
			
			<p><?php echo 'Sorry, no posts matched your criteria.'; ?></p><?php endif; ?>
<?php if(function_exists('wp_pagenavi')) { ?><div class="pagenavigat"><?php wp_pagenavi(); ?></div><?php } else { ?>
<div class="navigation"><p><span class="previous"><?php next_posts_link(__('&laquo; Older Entries', 'cleanblack')); ?></span>  <span class="newest"><?php previous_posts_link(__('Newer Entries &raquo;', 'cleanblack')); ?></span></p></div><?php } ?>
