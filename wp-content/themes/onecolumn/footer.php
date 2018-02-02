<?php
/*
 * The footer for displaying site-info.
 */
?>

<div id="footer">
	
	<div class="site-info">
		Copyright &copy; <?php echo date('Y'); ?>  <a href="<?php echo home_url() ; ?>" title="<?php bloginfo('name'); ?>"><?php bloginfo('name'); ?></a> - <a href="http://wordpress.org" title="WordPress Blog Platform">Proudly powered by WordPress</a>
	</div>

</div>
</div><!-- #container -->

<?php
   /* Always have wp_footer() just before the closing </body>
    * tag of your theme, or you will break many plugins, which
    * generally use this hook to reference JavaScript files.
    */
    wp_footer();
?>
</body>
</html>