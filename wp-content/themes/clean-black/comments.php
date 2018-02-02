<?php
	if ( post_password_required() ) {
		echo '<p class="nocomments">This post is password protected. Enter the password to view comments.</p>';
		return;
	}
?>
<?php if ( have_comments() ) : ?>

<div id="thecomments">
	  <div class="headingred">Comments</div>
	<p class="comments"><?php comments_number('No Responses', 'One Response', '% Responses' );?></p>
		<ol class="commentlist">
		<?php wp_list_comments('avatar_size=48'); ?>
		</ol>
		<?php	
			if (get_option('page_comments')) {
				$comment_pages = paginate_comments_links('echo=0');
				if($comment_pages){
				 echo '<div class="commentnavi"><span class="commentpages">'.$comment_pages.'</span></div>';
				}
			}
		?>
<div style="clear:both;"></div></div>

<?php else : // this is displayed if there are no comments so far ?>

<?php if ('open' == $post->comment_status) : ?>
		<!-- If comments are open, but there are no comments. -->
<?php else : // comments are closed ?>
	<!-- If comments are closed. -->
<p class="nocomments">Comments are closed.</p>
<?php endif; ?>
<?php endif; ?>
<?php comment_form(); ?>
