<?php
/**
 * The template for displaying comments on the resource page.
 *
 * The area of the page that contains both current comments
 * and the comment form. The actual display of comments is
 * handled by a callback to ppmlayout_review() which is
 * located in the functions.php file.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>

<div class="resource_comments">
	<?php if(post_password_required()): ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'ppmlayout' ); ?></p>
	</div>
	<?php return; endif; ?>
	<div class="row-fluid">
		<div class="span9"> 
		</div>
		<div class="span3"> 
			<a class="leave_review" href="#">Leave a Review</a>
		</div>
	</div>
	<?php
    	
    $required_text = (isset($required_text)) ? $required_text : '';
    $aria_req = (isset($aria_req)) ? $aria_req : '';
	
	$args = array(
	  'id_form'           => 'commentform',
	  'id_submit'         => 'submit',
	  'title_reply'       => __( 'Leave a Reply' ),
	  'title_reply_to'    => __( 'Leave a Reply to %s' ),
	  'cancel_reply_link' => __( 'Cancel Reply' ),
	  'label_submit'      => __( 'Submit Review' ),
	
	  'comment_field' =>  '<p class="comment-form-comment"><textarea id="comment" name="comment" aria-required="true" placeholder="Enter your review here...">' .
	    '</textarea></p>',
	
	  'comment_notes_before' => '<p class="comment-notes">' .
	    __( 'Your email address will not be published.' ) . ( $req ? $required_text : '' ) .
	    '</p>',
	
	  'comment_notes_after' => '',
	
	  'fields' => apply_filters( 'comment_form_default_fields', array(
	    'author' =>
	      '<p class="comment-form-author">' .
	      '<label for="author">' . __( 'Name', 'domainreference' ) . '</label> ' .
	      ( $req ? '<span class="required">*</span>' : '' ) .
	      '<input id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
	      '" size="30"' . $aria_req . ' /></p>',
	
	    'email' =>
	      '<p class="comment-form-email"><label for="email">' . __( 'Email', 'domainreference' ) . '</label> ' .
	      ( $req ? '<span class="required">*</span>' : '' ) .
	      '<input id="email" name="email" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) .
	      '" size="30"' . $aria_req . ' /></p>',
	
	    'url' =>
	      '<p class="comment-form-url"><label for="url">' .
	      __( 'Website', 'domainreference' ) . '</label>' .
	      '<input id="url" name="url" type="text" value="' . esc_attr( $commenter['comment_author_url'] ) .
	      '" size="30" /></p>'
	    )
	  ),
	
	  'must_log_in' => '<p class="must-log-in">' .
	    sprintf(
	      __( 'You must be <a href="%s">logged in</a> to post a comment.' ),
	      wp_login_url( apply_filters( 'the_permalink', get_permalink() ) )
	    ) . '</p>',
	    
	  'logged_in_as' => '<p class="logged-in-as">' .
	    sprintf(
	    __( '<a href="%1$s">%2$s:</a> You are commenting using your My Saltsha account.' ),
	    	//<a href="%3$s" title="Log out of this account">Log out?</a>
	      admin_url( 'profile.php' ),
	      $user_identity,
	      wp_logout_url( apply_filters( 'the_permalink', get_permalink( ) ) )
	    ) . '</p>',
	
	);

	comment_form($args); 
	
	?>
	<?php if(have_comments()): ?>
		<h3>Reviews</h3>
	
		<?php if(get_comment_pages_count() > 1 && get_option( 'page_comments' )): ?>
			<nav id="comment-nav-above">
				<h1 class="assistive-text"><?php _e( 'Comment navigation', 'ppmlayout' ); ?></h1>
				<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'ppmlayout' ) ); ?></div>
				<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'ppmlayout' ) ); ?></div>
			</nav>
		<?php endif; ?>
	
		<ul class="commentlist">
			<?php wp_list_comments(array('callback' => 'ppmlayout_review')); ?>
		</ul>
	
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


</div>