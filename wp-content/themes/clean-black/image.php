<?php get_header(); ?>

<div id="onebar-content">
	<div id="onebar-postarea">
<?php
if ( have_posts() ) : while ( have_posts() ) : the_post();
//$photographer = get_post_meta($post->ID, 'be_photographer_name', true);
//$photographerurl = get_post_meta($post->ID, 'be_photographer_url', true);
?>

<h1><?php the_title(); ?></h1>
<div class="mediumfont"><div class="dateview">Uploaded by <a rel="author" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author() ?></a>
	&nbsp;<?php edit_post_link( __( 'Edit', 'cleanblack' ), '<span class="edit-link">', '</span>' ); ?></div></div><div style="clear:both;"></div>
  
                        <div class="entry-attachment">
<?php if ( wp_attachment_is_image( $post->id ) ) : $att_image = wp_get_attachment_image_src( $post->id, "full"); ?>
                        <p class="attachmentview"><a href="<?php echo wp_get_attachment_url($post->id); ?>" title="<?php the_title(); ?>" rel="attachment"><img src="<?php echo $att_image[0];?>" width="<?php echo $att_image[1];?>" height="<?php echo $att_image[2];?>"  class="attachment-medium" alt="<?php $post->post_excerpt; ?>" /></a>
                        <?php echo $post->post_excerpt; ?></p>
						  <br/>
						  <?php echo $post->post_content; ?>
<?php else : ?>
                        <a href="<?php echo wp_get_attachment_url($post->ID) ?>" title="<?php echo esc_html( get_the_title($post->ID), 1 ) ?>" rel="attachment"><?php echo basename($post->guid) ?></a>
<?php endif; ?>
                        </div>

<?php endwhile; ?>

<?php endif; ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
if ( comments_open() || '0' != get_comments_number() )
					comments_template();
			?>

		
	  </div>
	
</div>
<?php get_footer(); ?>