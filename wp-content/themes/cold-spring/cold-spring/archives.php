<?php get_header(); ?>
<!-- Container -->
<div class="CON">

<!-- start content left -->
<?php get_sidebar(); ?>
<!-- end content left -->
<!-- Start SC -->
<div class="SC">

<?php include (TEMPLATEPATH . '/searchform.php'); ?>

<h2>Архив на месяц:</h2>
<ul>
 <?php wp_get_archives('type=monthly'); ?>
</ul>

<h2>Архив на тему:</h2>
<ul>
 <?php wp_list_categories(); ?>
</ul>

</div> 
<!-- End SC -->

<!-- Container -->
</div>
<?php get_footer(); ?>
