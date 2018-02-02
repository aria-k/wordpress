<?php get_header(); ?>
<!-- Container -->
<div class="CON">

<!-- start content left -->
<?php get_sidebar(); ?>
<!-- end content left -->
<!-- Start SC -->
<div class="SC">

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<div class="Nav"><?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?></div>

<?php $attachment_link = get_the_attachment_link($post->ID, true, array(420, 800)); // This also populates the iconsize for the next line ?>
<?php $_post = &get_post($post->ID); $classname = ($_post->iconsize[0] <= 128 ? 'small' : '') . 'attachment'; // This lets us style narrow icons specially ?>
<div class="Post" id="post-<?php the_ID(); ?>">
<div class="PostHead">
<h1><a href="<?php echo get_permalink($post->post_parent); ?>" rev="attachment"><?php echo get_the_title($post->post_parent); ?></a></h1> <h3><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>
</div>
<div class="PostContent">
<p><?php echo $attachment_link; ?><br /><br /><?php echo basename($post->guid); ?></p>
<?php the_content('<p class="serif">Читать далее &raquo;</p>'); ?>
<?php wp_link_pages(array('before' => '<p><strong>Страницы:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
</div>
</div>

<?php endwhile; else: ?>
<p>Извините, нет записей удовлетворяющих вашим критериям.</p>

<?php endif; ?>
</div> 

<!-- Container -->
</div>

<?php get_footer(); ?>
