<?php get_header(); ?>
<!-- Container -->
<div class="CON">

<!-- start content left -->
<?php get_sidebar(); ?>
<!-- end content left -->

<!-- Start SC -->
<div class="SC">

<?php if (have_posts()) : ?>
<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
<?php /* If this is a category archive */ if (is_category()) { ?>
<h2 class="pagetitle">Архив на категорию &#8216;<strong><?php single_cat_title(); ?></strong></h2>
<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
<h2 class="pagetitle">Архив на месяц <?php the_time('F jS, Y'); ?></h2>
<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
<h2 class="pagetitle">Архив на год <?php the_time('F, Y'); ?></h2>
<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
<h2 class="pagetitle">Архив <?php the_time('Y'); ?></h2>
<?php /* If this is an author archive */ } elseif (is_author()) { ?>
<h2 class="pagetitle">Архив автора</h2>
<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
<h2 class="pagetitle">Архив блога</h2>
<?php } ?>


<div class="Nav"><?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?></div>

<?php while (have_posts()) : the_post(); ?>
<div class="Post" id="post-<?php the_ID(); ?>" style="padding-bottom: 40px;">

<div class="PostHead">
<h1><a title="Permanent Link to <?php the_title(); ?>" href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h1>
<small class="PostAuthor">Автор: <?php the_author() ?> <?php edit_post_link('Изменить'); ?></small>
<p class="PostDate">
<small class="day"><?php the_time('j') ?></small>
<small class="month"><?php the_time('M') ?></small>
<small class="year"><? // php the_time('Y') ?></small>
</p>
</div>
  
<div class="PostContent">
<?php the_content() ?>
</div>
<div class="clearer"></div>
<div class="PostDet">
 <li class="PostCom"><?php comments_popup_link('0 Комментариев', '1 Комментарий', '% Комментариев'); ?></li>
 <li class="PostCateg">Категория:
 <?php the_category(', ') ?></li>
</div>
</div>
<?php endwhile; ?>

<div class="Nav"><?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); } ?></div>

<?php else : ?>
<h2 class="center">Не найдено</h2>
<?php include (TEMPLATEPATH . '/searchform.php'); ?>
<?php endif; ?>
</div> 
<!-- End SC -->

<!-- Container -->
</div>

<?php get_footer(); ?>