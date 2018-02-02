<?php get_header(); ?>

<div id="content">
<div id="articles">


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div id="article">

<?php if ( is_sticky() ) : ?><div class="post-title"><?php _e( 'Featured', 'convention' ); ?></div><?php endif; ?>
<div class="article-title"><a class="h1" href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></br>
<div class="article-meta"><img class="meta" src="<?php echo get_template_directory_uri() . '/icons/user.png'; ?>" width="10">&nbsp;&nbsp;<?php the_author(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img class="meta" src="<?php echo get_template_directory_uri() . '/icons/calendar.png'; ?>" width="12"> &nbsp;&nbsp;<?php convention_get_date(); ?></div></div>

<?php if ( has_post_thumbnail() ) { ?><div class="post-thumbnail"><a href="<?php esc_url( the_permalink() ); ?>"><?php the_post_thumbnail(); ?></a></div><?php } ?>

<?php the_content( __( 'Continue reading &rarr;', 'convention' ) ); ?>

<div class="clear"></div>

</div></div>
<?php if ( is_sticky() ) : ?><div class="line-one"></div><?php endif; ?>

<?php endwhile; ?> 

 <div id="buttons"><?php next_posts_link('<div id="button">&laquo;</div>') ?><?php previous_posts_link('<div id="button2">&raquo;</div>'); ?></div>
  </div>

<?php endif; ?>
</div>

<div id="sidebars">

<div id="sidebar">
	<?php get_sidebar(); ?>
</div>

</div>

<div class="clear"></div>

</div>


<?php get_footer(); ?>