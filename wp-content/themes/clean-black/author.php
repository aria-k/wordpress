<?php get_header(); ?>
<div id="content">
<div id="homepage">
<div class="descriptionbox">
  <?php 
$curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author)); ?>
<?php $ID1 = $curauth->ID; 
  $name1 = $curauth->first_name;
  $name1 .= ' ';
  $name1 .= $curauth->last_name;
  ?>
  
  <h1><?php echo $curauth->display_name; ?></h1>
  <?php echo get_avatar($ID1, '80','',$name1); ?>
<p><?php echo $curauth->description; ?></p>
  <div style="clear:both;"></div>
  </div>
<?php get_template_part('loop',''); ?>	
</div>
<?php get_template_part('sidebar','');?>
</div>
<!-- The main column ends  -->

<?php get_footer(); ?>