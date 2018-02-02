<?php get_header(); ?>
<div class="art-contentLayout">
<?php include (TEMPLATEPATH . '/sidebar1.php'); ?><div class="art-content">

<div class="art-Block">
    <div class="art-Block-body">

<div class="art-BlockContent">
    <div class="art-BlockContent-tl"></div>
    <div class="art-BlockContent-tr"></div>
    <div class="art-BlockContent-bl"></div>
    <div class="art-BlockContent-br"></div>
    <div class="art-BlockContent-tc"></div>
    <div class="art-BlockContent-bc"></div>
    <div class="art-BlockContent-cl"></div>
    <div class="art-BlockContent-cr"></div>
    <div class="art-BlockContent-cc"></div>
    <div class="art-BlockContent-body">

<h2><?php _e('Archives by Month:', 'kubrick'); ?></h2>
<ul><?php wp_get_archives('type=monthly'); ?></ul>
<h2><?php _e('Archives by Subject:', 'kubrick'); ?></h2>
<ul><?php wp_list_categories(); ?></ul>

    </div>
</div>


    </div>
</div>


</div>
<?php include (TEMPLATEPATH . '/sidebar2.php'); ?>
</div>
<div class="cleared"></div>

<?php get_footer(); ?>