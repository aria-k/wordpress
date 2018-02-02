<?php
/**
 * The template for displaying comments.
 */
?>

<?php 
// If the post is protected by a password and the visitor has not yet entered the password no comments are loaded.
if ( post_password_required() )
	return;
?>

<div id="comments" class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h4 class="comments-title">
			<?php
				printf( _n( '1 response on %2$s', '%1$s responses on %2$s', get_comments_number(), 'onecolumn' ),
				number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
			?>
		</h4>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 32,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php
			// Are there comments to navigate through?
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
		?>
			<div class="nav-prev"><?php previous_comments_link( __( '&laquo; Older Comments', 'onecolumn' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &raquo;', 'onecolumn' ) ); ?></div>

		<?php endif; // Check for comment navigation ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
		<p class="no-comments"><?php _e( 'Comments are closed.' , 'onecolumn' ); ?></p>
		<?php endif; ?>

	<?php endif; // have_comments() ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
