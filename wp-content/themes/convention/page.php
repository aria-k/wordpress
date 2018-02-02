<?php get_header(); ?>

<div id="content">
<div id="articles">


<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<div id="article">

<div class="article-title"><a class="h1" href="<?php esc_url( the_permalink() ); ?>"><?php the_title(); ?></a></div>

<?php the_content(); ?>

 <div class="clear"></div>
    
    <div class="page-links"><?php wp_link_pages('before=<div class="page-title">PAGES</div>'); ?></div>
    
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