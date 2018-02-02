<div class="SRL">

<div class="Categ">
<h3>Категории</h3>
 <ul>
  <?php wp_list_cats(); ?>
 </ul>

<h3>Ссылки</h3>
<ul>
	<?php get_links('-1', '<li>', '</li>', '', FALSE, 'id', FALSE, FALSE, -1, FALSE); ?>
</ul>

<br />

<h3>Архив</h3>
<ul>
	<?php wp_get_archives('type=monthly'); ?>
</ul>

<br />
				
<h3>Мета</h3>
<ul>
	<li><a href="<?php bloginfo('rss2_url'); ?>" title="<?php _e('Syndicate this site using RSS'); ?>"><?php 
_e('<abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
	<li><a href="<?php bloginfo('comments_rss2_url'); ?>" title="<?php _e('The latest comments to all posts in 
RSS'); ?>"><?php _e('Комментарии в <abbr title="Really Simple Syndication">RSS</abbr>'); ?></a></li>
	<li><a href="http://wordpresse.ru">WordPress</a></li>
	<?php wp_meta(); ?>
</ul>
</div>

</div>



