<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to ppmlayout_comment() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>

<div class="comments">
	<?php if(post_password_required()): ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'ppmlayout' ); ?></p>
	</div>
	<?php return; endif; ?>

	<?php if(have_comments()): ?>
		<h2><?php printf( _n( 'One Thought on &ldquo;%2$s&rdquo;', '%1$s Thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'ppmlayout' ), number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' ); ?></h2>

		<?php if(get_comment_pages_count() > 1 && get_option( 'page_comments' )): ?>
			<nav id="comment-nav-above">
				<h1 class="assistive-text"><?php _e( 'Comment navigation', 'ppmlayout' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'ppmlayout' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'ppmlayout' ) ); ?></div>
			</nav>
		<?php endif; ?>

		<ol class="commentlist">
			<?php wp_list_comments(array('callback' => 'ppmlayout_comment')); ?>
		</ol>

		<?php if(get_comment_pages_count() > 1 && get_option( 'page_comments' )): ?>
			<nav id="comment-nav-below">
				<h1 class="assistive-text"><?php _e('Comment navigation', 'ppmlayout'); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( __('&larr; Older Comments', 'ppmlayout') ); ?></div>
				<div class="nav-next"><?php next_comments_link( __('Newer Comments &rarr;', 'ppmlayout') ); ?></div>
			</nav>
		<?php endif; ?>

	<?php elseif ( !comments_open() && !is_page() && post_type_supports(get_post_type(), 'comments')): ?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'ppmlayout' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div>
