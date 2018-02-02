<div class="Comments">
<div class="List">
<!-- Start CommentsList-->
<?php // Do not delete these lines
if ('comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
die ('Please do not load this page directly. Thanks!');
if (!empty($post->post_password)) { // if there's a password
if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
?>
<p class="nocomments">Эта запись защищена паролем. Введите его для просмотра комментариев.<p>
<?php return; }}?>

<!-- You can start editing here. -->
<?php if ($comments) : ?>
<h3 id="comments"><?php comments_number('Нет ответов', 'Один ответ', '% ответов' );?> на "<?php the_title(); ?>"</h3> 
<ol>

<?php foreach ($comments as $comment) : ?>
<li class="ComListLi" id="comment-<?php comment_ID() ?>"><div class="ComListLiTop"></div>

<?php if (function_exists('gravatar')) { ?>
 <span class="ListGrav">
 <img src="<?php gravatar("R", 25); ?>" alt="<?php comment_author() ?>"/>
 </span>
 <?php } ?>
 <big><?php comment_author_link() ?></big>
 <small><a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('F jS, Y') ?> at <?php comment_time() ?><?php edit_comment_link('&nbsp;&nbsp;<strong>Изменить сообщение</strong>','',''); ?></a></small>
 <span class="ListNr"><? // php gravatar() ?><?php $commentNumber++; echo $commentNumber; ?></span>
 <span class="ListContent">
  <p><?php comment_text() ?></p> 
 </span>
 <?php if ($comment->comment_approved == '0') : ?>Ваше сообщение ожидает модерации.
</li>
<?php endif; ?> 
<?php endforeach; /* end for each comment */ ?>
</ol>
<?php else : // this is displayed if there are no comments so far ?>
<?php if ('open' == $post->comment_status) : ?> 
<!-- If comments are open, but there are no comments. -->
<?php else : // comments are closed ?>
<!-- If comments are closed. -->
<p class="nocomments">Комментирование закрыто.</p>
<?php endif; ?>
<?php endif; ?>
<?php if ('open' == $post->comment_status) : ?><br />
<!-- Ends CommentsList-->
</div>




<!-- Start Comments Form-->
<h3 id="respond" style="color:#000; letter-spacing: -1px;">Оставить отзыв</h3> 
<div class="Form"><div class="FormTop"></div>
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p>Вы должны быть <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?redirect_to=<?php the_permalink(); ?>">зарегистрированы</a> чтобы оставить отзыв.</p>
<?php else : ?>

<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<?php if ( $user_ID ) : ?>
<p style="display: block; margin-top: 5px;">Вы вошли как <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>. <a href="<?php echo get_option('siteurl'); ?>/wp-login.php?action=logout" title="<?php _e('Log out of this account') ?>">выйти &raquo;</a></p>
<?php else : ?>

<p>
<label for="author">
<small>Имя <?php if ($req) _e('(<strong>*</strong>)'); ?></small>
<input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" tabindex="1" class="TextField" style="width: 375px;" />
</label>
</p>
		
<p>
<label for="email">
<small>E-mail (<?php if ($req) _e('<strong>*</strong>'); ?>)</small>
<input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" tabindex="2" class="TextField"  style="width: 375px;" />
</label>
</p>
		
<p>
<label for="url">
<small><abbr title="Uniform Resource Identifier">Сайт</abbr></small>
<input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" tabindex="3" class="TextField" style="width: 375px;" />
</label>
</p>

<?php endif; ?>
<br clear="all" />
<p><textarea name="comment" id="comment" rows="10" tabindex="4" class="TextArea" style="width: 540px;"></textarea></p>

<p><input name="SubmitComment" type="image" class="SubmitComment" onmouseover="javascript:changeSty('SubmitCommentIE');" onmouseout="javascript:changeSty('SubmitComment');"  title="Post Your Comment" src="<?php bloginfo('template_url'); ?>/images/ButtonTransparent.png" alt="Post Your Comment" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" /></p>
<?php do_action('comment_form', $post->ID); ?>
</form>
<?php endif; // If registration required and not logged in ?>
<?php endif; // if you delete this the sky will fall on your head ?>
</div>

</div>