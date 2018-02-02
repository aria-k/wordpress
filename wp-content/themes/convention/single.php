<?php get_header(); ?>

<div id="content">
<div id="articles">


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div id="article">

<div class="article-title"><a class="h1" href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></br>
<div class="article-meta"><img class="meta" src="<?php echo get_template_directory_uri() . '/icons/user.png'; ?>" width="10">&nbsp;&nbsp;<?php the_author(); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img class="meta" src="<?php echo get_template_directory_uri() . '/icons/calendar.png'; ?>" width="12"> &nbsp;&nbsp;<?php convention_get_date(); ?></div></div>

<?php if ( has_post_thumbnail() ) { ?><div class="post-thumbnail"><?php the_post_thumbnail(); ?></div><?php } ?>

<?php the_content(); ?>

 <div class="clear"></div>
    
    <div class="page-links"><?php wp_link_pages('before=<div class="page-title">PAGES</div>'); ?></div>
    
    <div class="tag-links"><?php the_tags('<div class="tags-title">TAGS</div>', '', ''); ?></div><div class="clear"></div>
    
     <div class="category-links"><div class="tags-title"><?php _e( 'CATEGORIES', 'convention' ); ?></div><?php the_category(''); ?></div><div class="clear"></div>
    
   <?php edit_post_link( __( 'Edit', 'convention' ), '<div id="edit-link"><span class="edit-link">', '</span></div>' ); ?>

</div></div>
<?php endwhile; ?> 



<?php endif; ?>

<?php if ( comments_open() ) : ?>
<div id="comments">
<?php comments_template(); ?>
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