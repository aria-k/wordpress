<?php 
/**
 * Library of Theme options functions.
 * @package PaperCuts
 * @since PaperCuts 1.0.0
*/

// Display Breadcrumb navigation
function papercuts_get_breadcrumb() { 
global $papercuts_options_db;
		if ($papercuts_options_db['papercuts_display_breadcrumb'] != 'Hide') { ?>
		<?php if(function_exists( 'bcn_display' )){ _e('<p class="breadcrumb-navigation">', 'papercuts'); ?><?php bcn_display(); ?><?php _e('</p>', 'papercuts');} ?>
<?php } 
} 

// Display featured images on single posts
function papercuts_get_display_image_post() { 
global $papercuts_options_db;
		if ($papercuts_options_db['papercuts_display_image_post'] == '' || $papercuts_options_db['papercuts_display_image_post'] == 'Display') { ?>
		<?php if ( has_post_thumbnail() ) : ?>
      <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
    <?php endif; ?>
<?php } 
}

// Display featured images on pages
function papercuts_get_display_image_page() { 
global $papercuts_options_db;
		if ($papercuts_options_db['papercuts_display_image_page'] == '' || $papercuts_options_db['papercuts_display_image_page'] == 'Display') { ?>
		<?php if ( has_post_thumbnail() ) : ?>
      <div class="post-thumbnail"><?php the_post_thumbnail(); ?></div>
    <?php endif; ?>
<?php } 
} ?>