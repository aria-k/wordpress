<?php get_header(); ?>

<div id="content">
	<div id="homepage">
		<div class="postarea">
			<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		<?php endwhile; // end of the loop. ?>
		  </div>
  </div>
<?php get_template_part('sidebar','');?>
		
</div>
<?php get_footer(); ?>