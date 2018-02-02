<?php get_header(); ?>
<div id="content">
<div id="homepage">
<div class="archivebox">
<h1 class="categoryh"><?php single_cat_title(); ?></h1>
<?php echo category_description(); ?>
</div> 
<?php get_template_part('loop','');?>
</div>

<?php get_template_part('sidebar','');?>
</div>
<!-- The main column ends  -->

<?php get_footer(); ?>