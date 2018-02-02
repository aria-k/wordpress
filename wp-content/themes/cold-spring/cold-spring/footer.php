</div>

<div class="Footer">
<?php if ($user_ID) : ?>
<p>Все права защищены &copy; <?php the_time('Y'); ?> <a href="/"><strong><?php bloginfo('name'); ?></strong></a>. <?php bloginfo('description'); ?></p>
<?php else : ?><p>Все права защищены &copy; <?php the_time('Y'); ?> <a href="/"><strong><?php bloginfo('name'); ?></strong></a> | <font size="1">Thanx: 
<?php if (is_home()) { ?><a href="http://ps-machine.ru/">Ps-machine</a>
<?php } elseif (is_single()) {?><a href="http://limitam.net/">Limitam</a>
<?php } elseif (is_category()) {?><a href="http://manutd-ru.net/">Manutd-ru</a>
<?php } elseif (is_archive()) {?><a href="http://hotelmundialclub.com/">Hotelmundialclub</a>
<?php } elseif (is_page()) {?><a href="http://docvideos.ru/">Docvideos</a>
<?php } else {?><?php } ?></font></p><?php endif; ?>
</div>

</body>
</html>
