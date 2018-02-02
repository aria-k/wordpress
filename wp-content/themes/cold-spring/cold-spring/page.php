<?php get_header(); ?>
<!-- Container -->
<div class="CON">

<!-- start content left -->
<div class="SR">
<?php get_sidebar(); ?>
</div>
<!-- end content left -->

<!-- Start SC -->
<div class="SCS">

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>
<h1><?php the_title(); ?></a></h1>
<?php the_content("<p>__('Читать далее &raquo;')</p>"); ?>
<?php edit_post_link(__('Изменить'), '<p>', '</p>'); ?>
<?php endwhile; endif; ?>
</div> 
<!-- End SC -->

<!-- Container -->

</div>
<?php get_footer(); ?>