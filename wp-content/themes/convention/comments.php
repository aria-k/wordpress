<div class="show-comments">
<div class="countcomments"><?php comments_number( '', ''. __( '1 Comment', 'convention' ) .'', '% '. __( 'Comments', 'convention' ) .'' ); ?></div>
<?php wp_list_comments('type=comment&callback=convention_comment'); ?>
<?php paginate_comments_links(); ?></div></div>
<div id="comments"> 
<div class="comment-fields"><?php $comments_args = array(
        // remove "Text or HTML to be displayed after the set of comment fields"
        'comment_notes_after' => '',
        // redefine your own textarea (the comment body)
        'comment_field' => '<p class="comment-form-comment"><label for="comment">' . __( 'Comment', 'convention' ) . '</label></br>
        <textarea id="comment" rows="8" name="comment" aria-required="true"></textarea></p>',
);

comment_form($comments_args); ?></div></div>