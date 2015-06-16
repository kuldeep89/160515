<?php
/**
 * PPM Layout functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, ppmlayout_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'ppmlayout_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */


/**
 * Force SSL admin
 */
remove_filter('template_redirect','redirect_canonical');
if (!defined('FORCE_SSL_ADMIN')) {
    define('FORCE_SSL_ADMIN', true);
}
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS']='on';
}


/**
 * Set global vars, requires and includes
 */
$GLOBALS['ppm_post_inc'] = 0;
require_once dirname(__FILE__).'/inc/wp-ajax.php';


/**
 * Set the default sender address for emails.
 */
add_filter('wp_mail_from', 'new_mail_from');
add_filter('wp_mail_from_name', 'new_mail_from_name');
function new_mail_from($old) {
    return 'success@saltsha.com';
}
function new_mail_from_name($old) {
    return 'Saltsha';
}


/**
 * Remove feed links
 */
remove_action( 'wp_head', 'feed_links' );


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 584;


/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override ppmlayout_setup() in a child theme, add your own ppmlayout_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_editor_style() To style the visual editor.
 * @uses add_theme_support() To add support for post thumbnails, automatic feed links, custom headers
 * 	and backgrounds, and post formats.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since PPM Layout 1.0
 */
add_action( 'after_setup_theme', 'ppmlayout_setup' );
if ( ! function_exists( 'ppmlayout_setup' ) ):
	function ppmlayout_setup() {
	
		/* Make PPM Layout available for translation.
		 * Translations can be added to the /languages/ directory.
		 * If you're building a theme based on PPM Layout, use a find and replace
		 * to change 'ppmlayout' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'ppmlayout', get_template_directory() . '/languages' );
	
		// This theme styles the visual editor with editor-style.css to match the theme style.
		add_editor_style();
	
		// Load up our theme options page and related code.
		require(get_template_directory() . '/inc/theme-options.php');
	
		// Grab PPM Layout's Ephemera widget.
		require(get_template_directory() . '/inc/widgets.php');
	
		// Add default posts and comments RSS feed links to <head>.
		// add_theme_support('automatic-feed-links');
	
		// This theme uses wp_nav_menu() in one location.
		register_nav_menu('primary', __( 'Primary Menu', 'ppmlayout' ));
		register_nav_menu('secondary', __( 'Secondary Menu', 'ppmlayout' ));
	
		// Add support for a variety of post formats
		add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );
	
		$theme_options = ppmlayout_get_theme_options();
		if ( 'dark' == $theme_options['color_scheme'] )
			$default_background_color = '1d1d1d';
		else
			$default_background_color = 'f1f1f1';
	
		// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
		add_theme_support('post-thumbnails');
		add_image_size('post-blurb-size', 600, 250, true);
	}
endif;


/**
 * Styles the header image and text displayed on the blog
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_header_style' ) ) :
	function ppmlayout_header_style() {
		$text_color = get_header_textcolor();
	
		// If no custom options for text are set, let's bail.
		if ( $text_color == HEADER_TEXTCOLOR )
			return;
			
		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
			// Has the text been hidden?
			if ( 'blank' == $text_color ) :
		?>
			#site-title,
			#site-description {
				position: absolute !important;
				clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php
			// If the user has set a custom color for the text use that
			else :
		?>
			#site-title a,
			#site-description {
				color: #<?php echo $text_color; ?> !important;
			}
		<?php endif; ?>
		</style>
		<?php
	}
endif;


/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in ppmlayout_setup().
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_admin_header_style' ) ) :
	function ppmlayout_admin_header_style() {
	?>
		<style type="text/css">
		.appearance_page_custom-header #headimg {
			border: none;
		}
		#headimg h1,
		#desc {
			font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
		}
		#headimg h1 {
			margin: 0;
		}
		#headimg h1 a {
			font-size: 32px;
			line-height: 36px;
			text-decoration: none;
		}
		#desc {
			font-size: 14px;
			line-height: 23px;
			padding: 0 0 3em;
		}
		<?php
			// If the user has set a custom color for the text use that
			if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
		?>
			#site-title a,
			#site-description {
				color: #<?php echo get_header_textcolor(); ?>;
			}
		<?php endif; ?>
		#headimg img {
			max-width: 1000px;
			height: auto;
			width: 100%;
		}
		</style>
	<?php
	}
endif;


/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in ppmlayout_setup().
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_admin_header_image' ) ) :
	function ppmlayout_admin_header_image() { ?>
		<div id="headimg">
			<?php
			$color = get_header_textcolor();
			$image = get_header_image();
			if ( $color && $color != 'blank' )
				$style = ' style="color:#' . $color . '"';
			else
				$style = ' style="display:none"';
			?>
			<h1><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
			<div id="desc"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
			<?php if ( $image ) : ?>
				<img src="<?php echo esc_url( $image ); ?>" alt="" />
			<?php endif; ?>
		</div>
	<?php }
endif;


/**
 * Sets the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove the filter and add your own
 * function tied to the excerpt_length filter hook.
 */
function ppmlayout_excerpt_length( $length ) {
	return (is_category() || is_archive() || is_author() || is_search()) ? 40 : 100;
}
add_filter( 'excerpt_length', 'ppmlayout_excerpt_length', 99 );


/**
 * Replaces "[...]" (appended to automatically generated excerpts) with an ellipsis and ppmlayout_continue_reading_link().
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 */
function ppmlayout_auto_excerpt_more( $more ) {

	global $post;
	
	$user = wp_get_current_user();

	$arr_subscriptions	= WC_Subscriptions_Manager::get_users_subscriptions(get_current_user_id());
	
	$active_subscription = FALSE;
	
	if( isset($arr_subscription) && count($arr_subscription) > 0 ) {
		
		foreach( $active_subscriptions as $arr_subscription ) {
			
			if( $arr_subscription['status'] == 'active' && strtotime($arr_subscription['trial_expiry_date']) > time() ) {
				$active_subscription = TRUE;
			}
			
		}
		
	}

	if (/* ( */is_user_logged_in() /* && $active_subscription && (user_in_group(6) || user_in_group(7)))  */|| in_array( "administrator", $user->roles ) || in_array( "editor", $user->roles ) ||  in_array( "author", $user->roles ) ||  in_array( "contributor", $user->roles ) ) {
		return '... <a href="'.get_permalink($post->ID).'" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>';
	}
	else {
	
		if (is_category() || is_archive() || is_author() || is_search()) {
			return '... <a href="'.get_permalink($post->ID).'" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>';
		}/*

		else if( is_user_logged_in() && $active_subscription !== TRUE ) {

			return '... <div class="alert-danger"><a href="'.site_url('/account/').'" style="cursor:pointer;cursor:hand;text-decoration:none;"><div class="alert alert-error" style="text-align:center;cursor:pointer;cursor:hand;" onclick="document.location=\''.site_url('/shop/').'\'"><strong>Your payment failed.</strong> Please try setting up your payment method again.</div></a></div>';
			
		}
*/ else {
			// return '... <p><a href="'.site_url('/shop/').'" class="trial-sign-up" target="_blank">SIGN UP FOR A FREE 30-DAY TRIAL!</a></p>';
			return '... <p><a href="#myModal" class="trial-sign-up" data-toggle="modal">Log In</a></p>';
		}
		
	}
	
	return '... <a href="'.get_permalink($post->ID).'" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>';
	
}
add_filter( 'excerpt_more', 'ppmlayout_auto_excerpt_more', 1 );


/**
 * Adds a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 */
function ppmlayout_custom_excerpt_more( $output ) {
	global $post;
	if ( has_excerpt() && ! is_attachment() ) {
		$go_to = (is_user_logged_in()) ? get_permalink($post->ID) : site_url('/upgrade/');
		$output .= '<a href="'. $go_to . '" class="news-block-btn">Read more <i class="m-icon-swapright m-icon-black"></i></a>';
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'ppmlayout_custom_excerpt_more' );


/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 */
function ppmlayout_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'ppmlayout_page_menu_args' );


/**
 * Register our sidebars and widgetized areas. Also register the default Epherma widget.
 *
 * @since PPM Layout 1.0
 */
function ppmlayout_widgets_init() {

	register_widget( 'PPM_Layout_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'ppmlayout' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Showcase Sidebar', 'ppmlayout' ),
		'id' => 'sidebar-2',
		'description' => __( 'The sidebar for the optional Showcase Template', 'ppmlayout' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'ppmlayout' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'ppmlayout' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'ppmlayout' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'ppmlayout' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'ppmlayout' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'ppmlayout' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'ppmlayout_widgets_init' );


/**
 * Display navigation to next/previous pages when applicable
 */
if ( ! function_exists( 'ppmlayout_content_nav' ) ) :
	function ppmlayout_content_nav( $nav_id ) {
		global $wp_query;
	
		if ( $wp_query->max_num_pages > 1 ) : ?>
			<nav id="<?php echo $nav_id; ?>">
				<h3 class="assistive-text"><?php _e( 'Post navigation', 'ppmlayout' ); ?></h3>
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'ppmlayout' ) ); ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'ppmlayout' ) ); ?></div>
			</nav><!-- #nav-above -->
		<?php endif;
	}
endif;


/**
 * Return the URL for the first link found in the post content.
 *
 * @since PPM Layout 1.0
 * @return string|bool URL or false when no link is present.
 */
function ppmlayout_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}


/**
 * Count the number of footer sidebars to enable dynamic classes for the footer
 */
function ppmlayout_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}


/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own ppmlayout_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_comment' ) ) :
	function ppmlayout_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'ppmlayout' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'ppmlayout' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
			break;
			default:
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
			<article id="comment-<?php comment_ID(); ?>" class="comment">
				<div>
					<?php echo get_avatar( $comment ); ?>
				</div>
				<div>
					<div class="comment_author vcard ">
						<?php
							/* translators: 1: comment author, 2: date and time */
							printf( __( '<div>%1$s</div><div>%2$s</div>', 'ppmlayout' ),
								sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
								sprintf( '<time pubdate datetime="%2$s">%3$s</time>',
									esc_url( get_comment_link( $comment->comment_ID ) ),
									get_comment_time( 'c' ),
									/* translators: 1: date, 2: time */
									sprintf( __( '%1$s at %2$s', 'ppmlayout' ), get_comment_date(), get_comment_time() )
								)
							);
						?>
		
						
					</div>
				</div>
				<div>
					<?php if($comment->comment_approved == '0'): ?>
						<strong><?php _e('Your comment is awaiting moderation.', 'ppmlayout'); ?></strong>
					<?php endif; ?>
		
					<div class="comment_content"><?php comment_text(); ?></div>
					
					<div class="reply">
						<?php comment_reply_link( array_merge( $args, array('reply_text' => __( '<span></span> REPLY', 'ppmlayout' ), 'depth' => $depth, 'max_depth' => $args['max_depth']) ) ); ?>
						<?php edit_comment_link( __('Edit', 'ppmlayout'), '<span class="edit-link">', '</span>' ); ?>
					</div>
				</div>
			</article>
			<span class="clear"> </span>
	
		</li>
		
		<?php
		break;
		endswitch;
	}
endif;


/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own ppmlayout_review(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_review' ) ) :
	function ppmlayout_review( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		switch ( $comment->comment_type ) :
			case 'pingback' :
			case 'trackback' :
		?>
		<li class="post pingback">
			<p><?php _e( 'Pingback:', 'ppmlayout' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'ppmlayout' ), '<span class="edit-link">', '</span>' ); ?></p>
		<?php
			break;
			default:
		?>
		<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		
			<article id="comment-<?php comment_ID(); ?>" class="review">
				<?php if($comment->comment_approved == '0'): ?>
					<strong><?php _e('Your comment is awaiting moderation.', 'ppmlayout'); ?></strong>
				<?php endif; ?>
	
				<div class="review_content">
					<div class="row-fluid">
						<div class="span10">
							<?php comment_text(); ?>
						</div>
						<div class="span2 comment_review_area">
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
							<div class="reply">
								<?php // comment_reply_link( array_merge( $args, array('reply_text' => __( '<span></span> REPLY', 'ppmlayout' ), 'depth' => $depth, 'max_depth' => $args['max_depth']) ) ); ?>
								<?php edit_comment_link( __('Edit', 'ppmlayout'), '<span class="edit-link">', '</span>' ); ?>
							</div>
						</div>
					</div>
				</div>
				<div class="review_author vcard row-fluid">
					<?php
						/* translators: 1: comment author, 2: date and time */
						printf( __( '<div class="span6"><div class="review_triangle"></div><span class="review_author_name">%1$s</span></div><div class="span6 text-right"><span class="review_time">%2$s</span></div>', 'ppmlayout' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<time pubdate datetime="%2$s">%3$s</time>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s %2$s', 'ppmlayout' ), get_comment_date('m/j/Y'), get_comment_time() )
							)
						);
					?>
				</div>
			</article>
			<span class="clear"> </span>
	
		</li>
		
		<?php
		break;
		endswitch;
	}
endif;


/**
 * Prints HTML with meta information for the current post-date/time and author.
 * Create your own ppmlayout_posted_on to override in a child theme
 *
 * @since PPM Layout 1.0
 */
if ( ! function_exists( 'ppmlayout_posted_on' ) ) :
	function ppmlayout_posted_on() {
		the_date();
	}
endif;

/**
 * Adds two classes to the array of body classes.
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since PPM Layout 1.0
 */
function ppmlayout_body_classes( $classes ) {

	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'ppmlayout_body_classes' ); 
 
 
/**
 * Add a custom field to the field editor (See editor screenshot)
 */
add_action("gform_field_standard_settings", "my_standard_settings", 10, 2);
 
function my_standard_settings($position, $form_id){
 
// Create settings on position 25 (right after Field Label)
if($position == 25){
?>

<li class="admin_label_setting field_setting" style="display: list-item; ">
<label for="field_placeholder">Placeholder Text

<!-- Tooltip to help users understand what this field does -->
<a href="javascript:void(0);" class="tooltip tooltip_form_field_placeholder" tooltip="&lt;h6&gt;Placeholder&lt;/h6&gt;Enter the placeholder/default text for this field.">(?)</a>
                       
</label>

<input type="text" id="field_placeholder" class="fieldwidth-3" size="35" onkeyup="SetFieldProperty('placeholder', this.value);">

</li>
<?php
}
}
 

/**
 * Now we execute some javascript technicalitites for the field to load correctly
 */
add_action("gform_editor_js", "my_gform_editor_js");
function my_gform_editor_js(){
?>
<script>
//binding to the load field settings event to initialize the checkbox
jQuery(document).bind("gform_load_field_settings", function(event, field, form){
jQuery("#field_placeholder").val(field["placeholder"]);
});
</script>
<?php
}
 

/**
 * We use jQuery to read the placeholder value and inject it to its field
 */
add_action('gform_enqueue_scripts',"my_gform_enqueue_scripts", 10, 2); 
function my_gform_enqueue_scripts($form, $is_ajax=false){
?>
<script>
 
jQuery(function(){
<?php
 
// Go through each one of the form fields
foreach($form['fields'] as $i=>$field){
 
// Check if the field has an assigned placeholder
if(isset($field['placeholder']) && !empty($field['placeholder'])){
                               
// If a placeholder text exists, inject it as a new property to the field using jQuery                            
?>

jQuery('#input_<?php echo $form['id']?>_<?php echo $field['id']?>').attr('placeholder','<?php echo $field['placeholder']?>');

<?php
}
}
?>
});
</script>
<?php
}


/**
 * Change the logo on the admin login page
 */
add_action('login_head', 'ppm_custom_login_logo');
function ppm_custom_login_logo() {
    echo '<style type="text/css">
    h1 a { background-image:url(https://79f7a05e1bf55ecda2cf-90bf115d91d2675e58839f40166e83c0.ssl.cf2.rackcdn.com/2014.03.18.16.06.49.6_saltsha-admin-logo.png) !important; background-size: 218px 38px !important;height: 38px !important; padding-bottom: 1em !important; width: 218px !important; }
    </style>';
}

/**
 * Change the URL of the WordPress login logo
 */
add_filter('login_headerurl', 'ppm_url_login_logo');
function ppm_url_login_logo(){
    return get_bloginfo( 'wpurl' );
}


/**
 * Change the hover text on login page
 */
add_filter( 'login_headertitle', 'ppm_login_logo_url_title' );
function ppm_login_logo_url_title() {
    return 'PayProMedia';
}


/**
 * Login Screen: Don't inform user which piece of credential was incorrect
 */
add_filter ( 'login_errors', 'ppm_failed_login' );
function ppm_failed_login () {
    return 'The login information you have entered is incorrect. Please try again.';
}


/**
 * Add a favicon for your admin 
 */
add_action('admin_head', 'admin_favicon');
add_action('login_head', 'admin_favicon');
function admin_favicon() { 
	echo '<link rel="shortcut icon" type="image/png" href="https://www.paypromedia.com/favicon.png" />
	<link rel="apple-touch-icon" href="https://www.paypromedia.com/apple-touch.png" />';
}


/**
 * Custom jQuery register
 */
if( !is_admin()){
	// Enqueue custom jQuery
	function my_enqueue_script() {
		wp_deregister_script('jquery');
		wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"), false, '1.8.3', false);
		wp_enqueue_script('jquery');
	}
	add_action( 'wp_enqueue_scripts', 'my_enqueue_script' );
}
add_filter('show_admin_bar', '__return_false');


/**
 * Setup for Primary Navigation
 */
class description_walker extends Walker_Nav_Menu {
  function start_el(&$output = null, $item = null, $depth = null, $args = null, $id = null) {
       global $wp_query;

       $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

       $class_names = $value = '';

       $classes = empty( $item->classes ) ? array() : (array) $item->classes;

       $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
       $class_names = ' class="'. esc_attr( $class_names ) . '"';

       $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';

       $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
       $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
       $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
       $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

       $prepend = '<strong>';
       $append = '</strong>';
       $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

       if($depth != 0)
       {
                 $description = $append = $prepend = "";
       }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
        $item_output .= $description.$args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}


/**
 * Remove plugin menu from non-local dev installs
 */
function remove_menus(){
	remove_menu_page( 'plugins.php' );
}
$host_name = $_SERVER['HTTP_HOST'];
$banned_host_names = array('dev.saltsha.com','my.dev.saltsha.com','qa.saltsha.com','my.qa.saltsha.com','stage.saltsha.com','my.stage.saltsha.com','my.saltsha.com','saltsha.com','www.saltsha.com');
if (in_array($host_name, $banned_host_names)) {
	add_action( 'admin_menu', 'remove_menus' );
}


/**
 * Get time since post was authored
 */
function time_since( $timestamp_past, $timestamp_future = FALSE, $years = true, $months = true, $days = true, $hours = true, $mins = FALSE, $secs = FALSE, $display_output = true ) {

	if( $timestamp_future === FALSE ) {
		$timestamp_future = time();
	}

	$diff = $timestamp_future - $timestamp_past;
    $calc_times = array();
    $timeleft   = array();

    // Prepare array, depending on the output we want to get.
    if ($years)  $calc_times[] = array('Year',   'Years',   31104000);
    if ($months) $calc_times[] = array('Month',  'Months',  2592000);
    if ($days)   $calc_times[] = array('Day',    'Days',    86400);
    if ($hours)  $calc_times[] = array('Hour',   'Hours',   3600);
    if ($mins)   $calc_times[] = array('Minute', 'Minutes', 60);
    if ($secs)   $calc_times[] = array('Second', 'Seconds', 1);

    foreach ($calc_times AS $timedata)
    {
        list($time_sing, $time_plur, $offset) = $timedata;

        if ($diff >= $offset)
        {
            $left = floor($diff / $offset);
            $diff -= ($left * $offset);
            if ($display_output === true) {
                $timeleft[] = "{$left} " . ($left == 1 ? $time_sing : $time_plur);
            } else {
                if (!isset($timeleft[strtolower($time_sing)]))
                    $timeleft[strtolower($time_sing)] = 0;
                $timeleft[strtolower($time_sing)] += $left;
            }
        }
    }
    if ($display_output === false)
        return $timeleft;
        
    return $timeleft ? ($timestamp_future > $timestamp_past ? null : '-') . implode(', ', $timeleft) : 0;
}


/*
 *	Checks if a user belongs to a group (based on the Groups plugin)
 */
function user_in_group($user_group) {
	return Groups_User_Group::read(get_current_user_id(), $user_group);
}
function userID_in_group($userID, $user_group) {
	return Groups_User_Group::read($userID, $user_group);
}


/**
 * Show content based on user
 */
function show_saltsha_content() {
	$user = wp_get_current_user();
// 	if (is_user_logged_in() && (user_in_group(6) || user_in_group(7) || in_array( "administrator", $user->roles ) || in_array( "editor", $user->roles ) ||  in_array( "author", $user->roles ) ||  in_array( "contributor", $user->roles ))) {
	if (is_user_logged_in()) {
		return the_content();
	} else {
		return the_excerpt();
	}
}


/**
 * Login Redirect
 */
add_filter("login_redirect", "my_login_redirect", 10, 3);
function my_login_redirect( $redirect_to, $request, $user ) {

    //is there a user to check?
    $admin_url = get_site_url()."/wp-admin";
    $user_url = get_site_url()."/dashboard";
    
    if( @is_array( $user->roles ) ) {
        //check for admins
        if( in_array( "administrator", $user->roles ) || in_array( "editor", $user->roles ) ||  in_array( "author", $user->roles ) || in_array("ppt-user", $user->roles) ) {
            // redirect them to the default place
            return $admin_url;
        } else {
            return $user_url;
        }
    }
    
}


/**
 * Redirect non-admin users to home page
 *
 * This function is attached to the 'admin_init' action hook.
 */
function ts_edit_password_email_text ( $text ) {
	if ( $text == 'A password will be e-mailed to you.' ) {
		$text = 'If you leave password fields empty one will be generated for you. Password must be at least eight characters long.';
	}
	return $text;
}
add_filter( 'gettext', 'ts_edit_password_email_text' );


/**
 * Register post
 */
function tml_register_post() {
	if ( ! empty( $_POST['user_email'] ) ) {
		$_POST['user_login'] = $_POST['user_email'];
	} else {
		$_POST['user_login'] = wp_generate_password( 16, false );
	}
}
add_action( 'register_post', 'tml_register_post' );


/**
 * Add to change breadcrumb to Metronics breadcrumbs
 */
add_filter( 'woocommerce_breadcrumb_defaults', 'jk_woocommerce_breadcrumbs' );
function jk_woocommerce_breadcrumbs() {
    return array(
        'delimiter'   => ' &gt; ',
        'wrap_before' => '<br /><ul class="breadcrumb">',
        'wrap_after'  => '</ul>',
        'before'      => '<li>',
        'after'       => '</li>',
        'home'        => _x( 'Dashboard', 'breadcrumb', 'woocommerce' ),
    );
}


/**
 * Removes all categories with no articles
 */
function new_nav_menu_items($items) {
    preg_match_all('/<li.*?>(.*?)<\/li>/i', $items, $menu_items);
    foreach ($menu_items[0] as $cur_menu_item) {
		preg_match('/<a title="(.*?)">/i', $cur_menu_item, $menu_link);
	    preg_match('/\/category\/(.*?)\//i', $cur_menu_item, $category);
	    if (count($category) > 0) {
		    $category_id = get_category_by_slug($category[1]);
		    $posts_in_category = get_term_by('id', $category_id->term_id, 'category');
		    if ($posts_in_category->count == 0) {
			    if (isset($menu_link[0])) {
				    $items = str_replace($menu_link[0], '', $items);
			    }
		    }
	    }

		// Only show "Loyalty Rewards" menu item if person is a merchant
		if (strripos($cur_menu_item, 'loyalty-rewards') !== false) { 
			if (!user_in_group(7) && !user_in_group(9)) {
				$items = preg_replace('/\<li id="menu-item-5976"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-6262"(.*)\<\/li\>/i', '', $items);
			}
		}
		
		// Only show "Terminal Supplies" menu item if person is a merchant
		if (strripos($cur_menu_item, 'terminal-supplies') !== false) { 
			if (!user_in_group(7) && !user_in_group(9)) {
				$items = preg_replace('/\<li id="menu-item-5544"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-2072"(.*)\<\/li\>/i', '', $items);
			}
		}
		
		// Only show Customers menu item if person is a merchant
		if (strripos($cur_menu_item, 'customers') !== false) { 
			//if (!user_in_group(7) && !user_in_group(9)) {
			if (!is_user_logged_in() && !current_user_can('add_users')) {
				$items = preg_replace('/\<li id="menu-item-2142"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-2097"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-6049"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-5910"(.*)\<\/li\>/i', '', $items);
			}
		}

		// Only show Sales Data menu item if person is a merchant
		if (strripos($cur_menu_item, 'sales-data')) {
			if (!user_in_group(7) && !user_in_group(9)) {
				$items = preg_replace('/\<li id="menu-item-904"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-5896"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-5904"(.*)\<\/li\>/i', '', $items);
			}
		}

		// Only show "Returning Customers" menu item if person is a merchant
		if (strripos($cur_menu_item, 'returning-customers') !== false) {
			if (!user_in_group(7) && !user_in_group(9)) {
				$items = preg_replace('/\<li id="menu-item-6049"(.*)\<\/li\>/i', '', $items);
			}
		}

		// Only show "PCI Compliance" menu item if person is a merchant
		if (strripos($cur_menu_item, 'pci-compliance') !== false) {
			if (!user_in_group(7) && !user_in_group(9)) {
				$items = preg_replace('/\<li id="menu-item-1906"(.*)\<\/li\>/i', '', $items);
				$items = preg_replace('/\<li id="menu-item-4786"(.*)\<\/li\>/i', '', $items);
			}
		}
		

		// Check if dashboard
	    if (strripos($cur_menu_item, 'dashboard') !== false) {
	    	if (get_post_type(get_the_ID()) == 'resource') {
		    	$dashboard_link = $cur_menu_item.'<li id="menu-item-resources" class="menu-item menu-item-type-post_type menu-item-object-page current-menu-item page_item page-item-5 current_page_item menu-item-resources"><a title="Resources" href="'.site_url('/resources/').'"><div class="saltsha-nav-icon saltsha-nav-faq"></div><span class="title">Resources</span><span class="selected"></span></a></li>';
	    	} else {
	    		$active_class = (isset($active_class)) ? $active_class : '';
		    	$dashboard_link = $cur_menu_item.'<li class="menu-item menu-item-type-custom menu-item-object-custom"><a title="Resources" href="'.site_url('/resources/').'"'.$active_class.'><div class="saltsha-nav-icon saltsha-nav-faq"'.$active_class.'></div><span class="title">Resources</span></a></li>';
	    	}

			$items = str_replace('<i class=', '<div class=', $items);
			$items = str_replace('</i>', '</div>', $items);
			$items = str_replace($cur_menu_item, $dashboard_link, $items);
		}
    }
    //$items .= '<li class="menu-item menu-item-type-custom menu-item-object-custom"><a title="Back To Saltsha" href="'.str_replace('my.', '', site_url()).'"><div class="saltsha-nav-icon saltsha-nav-goback"></div><span class="title">Back to '.str_replace(array('my.','http://'), '', site_url()).'</span></a></li>';
    return $items;
}
add_filter( 'wp_nav_menu_items', 'new_nav_menu_items' );
add_action( 'admin_init', 'redirect_non_admin_users' );


/**
 * Redirect bottom level users to home page
 *
 * This function is attached to the 'admin_init' action hook.
 */
function redirect_non_admin_users() {
	$arr_roles	= wp_get_current_user()->roles;
	if ( !in_array( "administrator", $arr_roles ) && !in_array( "editor", $arr_roles ) && !in_array( "author", $arr_roles ) && !in_array("ppt-user", $arr_roles) && $_SERVER['SCRIPT_NAME'] !== '/wp-admin/admin-ajax.php' ) {
		wp_redirect( home_url() );
		exit;
	}
}


/**
 * Removes resource via AJAX
 */
function ajax_remove_resource() {

    global $wpdb; // this is how you get access to the database

	if( isset($_POST['meta_id']) ) {

		$_POST['post_id']	= mysql_real_escape_string($_POST['post_id']);
		$_POST['meta_id']	= mysql_real_escape_string($_POST['meta_id']);

		$arr_resource	= get_post_meta_by_id($_POST['meta_id']);

		$cdn = new CFCDN_CDN();
		$cdn->api_settings['container'] = 'saltsha_resource';
		$cdn->delete_file( basename( $arr_resource->meta_value['file'] ) );

		$wpdb->get_results('DELETE FROM wp_postmeta WHERE post_id='.$_POST['post_id'].' AND meta_id='.$_POST['meta_id']);

	} else {
		echo 'Not set.';
	}

    die(); // this is required to return a proper result
    
}
add_action( 'wp_ajax_ajax_remove_resource', 'ajax_remove_resource' );


/*
 * A simple filter to disable a user-specified payment gateway when a product with a user-specified category is added to the shopping cart
 *  - Note:  If multiple products are added and only one has a matching category, it will remove the payment gateway
 * Requires:
 *    payment_NAME : One of the five hardcoded Woocommerce standard types of payment gateways - paypal, cod, bacs, cheque or mijireh_checkout

 * This code was tested against Woocommerce 2.0.8 and WordPress 3.5.1
 */
function filter_gateways($gateways){

 global $woocommerce;

 if (!user_in_group(9) && !user_in_group(8) && !user_in_group(7)) {
 	unset($gateways['cod']);
 	// If you want to remove another payment gateway, add it here i.e. unset($gateways['cod']);
 }
 
 return $gateways;

}
add_filter('woocommerce_available_payment_gateways','filter_gateways');


/**
 * Mark virtual orders completed
 */
function virtual_order_payment_complete_order_status( $order_status, $order_id ) {
  $order = new WC_Order( $order_id );
 
  if ( 'processing' == $order_status &&
       ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
 
    $virtual_order = null;
 
    if ( count( $order->get_items() ) > 0 ) {
 
      foreach( $order->get_items() as $item ) {
 
        if ( 'line_item' == $item['type'] ) {
 
          $_product = $order->get_product_from_item( $item );
 
          if ( ! $_product->is_virtual() ) {
            // once we've found one non-virtual product we know we're done, break out of the loop
            $virtual_order = false;
            break;
          } else {
            $virtual_order = true;
          }
        }
      }
    }
 
    // virtual order, mark as completed
    if ( $virtual_order ) {
      return 'completed';
    }
  }
 
  // non-virtual order, return original status
  return $order_status;
}
add_filter( 'woocommerce_payment_complete_order_status', 'virtual_order_payment_complete_order_status', 10, 2 );


/**
 * YouTube shortcode allow
 */
function youtube_embed($attributes) {
	// Extract attributes
	extract(shortcode_atts(array(
		'width' => 320,
		'height' => 240,
		'url' => null,
		'position' => null
	), $attributes));

	// Set height and width of video
	$height = isset($height) ? $height : '320';
	$width = isset($width) ? $width : '240';
	$position = isset($position) ? $position : 'center';

	// Get video URL so we can get the "v" attribute for embedding
	$get_url = parse_url($url);
	parse_str($get_url['query'], $query);

	// Echo embed code
	return '<style>.clear-left { clear: left; }.video-container {position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;}.video-container iframe,.video-container object,.video-container embed{position:absolute;top:0; clear: left;left:0;width:100%;height:100%;}</style><div class="clear-left"><div style="max-height:'.$height.';max-width:'.$width.';"><div class="video-container" style="text-align:'.$position.';"><iframe frameborder="0" style="border:0px;" src="//www.youtube.com/embed/'.$query['v'].'" frameborder="0" allowfullscreen></iframe></div></div></div>';
}
add_shortcode('youtube', 'youtube_embed');


/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process() {
	global $woocommerce;

	// Check if set, if its not set add an error. This one is only requite for companies
	
	if (strpos($_POST['account_password'], '.') || strpos($_POST['account_password'], ';') ||  strpos($_POST['account_password'], '+') ||  strpos($_POST['account_password'], '&') ||  strpos($_POST['account_password'], '?') !== FALSE){
    	$woocommerce->add_error( __('Password has illegal characters') );
    }

}


/**
 * Change product in cart
 */
global $woocommerce;
function change_subscription_ajax() {
	global $woocommerce;

	// Remove all items from cart
	$cart_data = $woocommerce->cart->get_cart();
	foreach ($cart_data as $product_key => $product_data) {
		try {
			$woocommerce->cart->set_quantity($product_key, 0);
		} catch (Exception $exc) {
			echo '{"response":"error", "message":"Error changing subscription. Please reload the page and try again. (001)"}';
			die();
		}
	}

	// Add new item to cart
	try {
		$woocommerce->cart->add_to_cart($_GET['subscription_id']);
	} catch (Exception $exc) {
		echo '{"response":"error", "message":"Error changing subscription. Please reload the page and try again. (002)"}';
		die();
	}

	if (!defined('WOOCOMMERCE_CHECKOUT')) define('WOOCOMMERCE_CHECKOUT', true);
	
	if (sizeof($woocommerce->cart->get_cart())==0) :
		echo '<div class="woocommerce_error">'.__('Sorry, your session has expired.', 'woocommerce').' <a href="'.home_url().'">'.__('Return to homepage &rarr;', 'woocommerce').'</a></div>';
		die();
	endif;
	
	do_action('woocommerce_checkout_update_order_review', $_POST['post_data']);
	
	if (isset($_POST['shipping_method'])) $_SESSION['_chosen_shipping_method'] = $_POST['shipping_method'];
	if (isset($_POST['payment_method'])) $_SESSION['_chosen_payment_method'] = $_POST['payment_method'];
	if (isset($_POST['country'])) $woocommerce->customer->set_country( $_POST['country'] );
	if (isset($_POST['state'])) $woocommerce->customer->set_state( $_POST['state'] );
	if (isset($_POST['postcode'])) $woocommerce->customer->set_postcode( $_POST['postcode'] );
	if (isset($_POST['s_country'])) $woocommerce->customer->set_shipping_country( $_POST['s_country'] );
	if (isset($_POST['s_state'])) $woocommerce->customer->set_shipping_state( $_POST['s_state'] );
	if (isset($_POST['s_postcode'])) $woocommerce->customer->set_shipping_postcode( $_POST['s_postcode'] );
	
	$woocommerce->cart->calculate_totals();
	
	do_action('woocommerce_checkout_order_review');

	die();
}
add_action( 'wp_ajax_change_subscription_ajax', 'change_subscription_ajax' );
add_action( 'wp_ajax_nopriv_change_subscription_ajax', 'change_subscription_ajax' );


/**
 * Change product in cart via function
 */
function change_subscription($subscription_id = null, $is_ajax = null) {
	global $woocommerce;

	// Remove all items from cart
	$cart_data = $woocommerce->cart->get_cart();
	foreach ($cart_data as $product_key => $product_data) {
		try {
			$woocommerce->cart->set_quantity($product_key, 0);
		} catch (Exception $exc) {
			return false;
		}
	}

	// Add new item to cart
	try {
		$woocommerce->cart->add_to_cart($subscription_id);
	} catch (Exception $exc) {
		return false;
	}
	return true;
}


/**
 * Don't show related products
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20);


/**
 * Image Rotator Shortcode for Blog
 */
function get_blog_slider($atts) {
	$html = '';	
	$i = 0;
	$c = 0;
	
	if(have_rows('image_rotators')):
		while(have_rows('image_rotators')): the_row();
			
			$title = get_sub_field('rotator_name');
			
			if($title === $atts['name']): 
				
				$html .= '<div id="slider_' . $i . '" class="slider"><div class="prev_' . $i . '"><span>&lt;</span></div>';
				if(have_rows('slides')):
					$html .= '<div>';
					while(have_rows('slides')): the_row();
							
						$image = get_sub_field('image');
						$c++;
						
						$html .= '<span class="slide';
						if($c == 1) {
							$html .= ' current';
						}
						$html .= '"><img class="img-responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" /><br><em>' . $image['caption'] . '</em></span>';
									
					endwhile; 
					$html .= '</div>';
				endif;
				$html .= '<div class="next_' . $i . '"><span>&gt;</span></div></div>';
				
			endif;
			
			$i++;
			
		endwhile;
	endif;
	
	return $html;
}
add_shortcode('slider', 'get_blog_slider');


/**
 * Hide JetPack from users.
 */
add_action( 'jetpack_admin_menu', 'hide_jetpack_from_others' );
function hide_jetpack_from_others() {
    if ( ! current_user_can( 'administrator' ) ) {
        remove_menu_page( 'jetpack' );
    }
}


/**
 * Get the first image in the post
 */
function get_first_image() {
    global $post, $posts;
    $first_img = '';
    $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
    $first_img = (!empty($matches[1][0])) ? $matches[1][0] : false;
    return $first_img;
}


/**
 * Set custom posts per page
 */
function custom_posts_per_page( $query ) { 
    if ( is_tax('resource-categories')) {
    	$query->set('posts_per_page', 8);
    }
    return $query;  
}  
if ( !is_admin() ) 
    add_filter( 'pre_get_posts', 'custom_posts_per_page' ); 


/**
 * Add custom Woocommerce user type field
 */
function wc_new_user_custom_fields( $buffer ) {
	global $wpdb;

	$input_html = '<select name="initial_woocommerce_group">';

	// Get list of available groups
	$the_groups = $wpdb->get_results('SELECT group_id,name FROM '.$wpdb->prefix.'groups_group');
	
	foreach ($the_groups as $cur_group) {
		$input_html .= '<option value="'.$cur_group->group_id.'">'.$cur_group->name.'</option>';
	}

	$input_html .= '</select>';
	
	if( have_rows('companies', 'option') ){
		$company_select_name = 'PayProTec';
		$company_options = '<option value="'.$company_select_name.'" selected>'.$company_select_name.' - Default</option>';
		while ( have_rows('companies', 'option') ) { 
			the_row();
			
			$company_select_name = get_sub_field('company_name');
			$company_select_link = get_sub_field('company_link');
			$company_select_logo = get_sub_field('company_logo');
			
			$cur_company = preg_replace('/\s+/', '', $company_select_name);
			$company_options .= '<option value="'.$cur_company.'">'.$company_select_name.'</option>';
		
		}
	} else {
		$company_select_name = 'PayProTec';
		$company_options = '<option value="'.$company_select_name.'">'.$company_select_name.'</option>';
	}	

	$buffer = preg_replace( '~<label\s+for="role">(.*?)</tr>~ims', '<label for="role">$1</tr><tr class="form-field"><th>User Type</th><td>' . $input_html . '</td></tr><tr>
	<th>Select Company</th>
	<td>
		<select name="company_select" id="company_select">
			'.$company_options.'
		</select>
	</td>
	</tr>', $buffer );
	
	return $buffer;
}


/**
 * Add admin page buffer, only on add user page
 */
function wc_new_user_buffer_start() {
	$is_add_user = (stripos($_SERVER['PHP_SELF'], 'user-new.php') !== false) ? true : false;
	if ($is_add_user) {
		ob_start("wc_new_user_custom_fields");
	}
}
add_action('admin_head', 'wc_new_user_buffer_start', 1);


/**
 * End admin page buffer
 */
function wc_new_user_buffer_end() {
	ob_end_flush();
}
add_action('admin_footer', 'wc_new_user_buffer_end', 1);


/**
 * Save additional user profile field(s) for woocommerce when user is saved
 */
function wc_save_account_field( $user_id ) {
	global $wpdb;

	if ($_POST['initial_woocommerce_group'] != '1') {
		$wpdb->insert(
			$wpdb->prefix.'groups_user_group',
			array(
				'user_id' => $user_id,
				'group_id' => $_POST['initial_woocommerce_group']
			)
		);
	}
	if ( isset($_POST['company_select']) ){
		update_usermeta( $user_id, 'company_select', $_POST['company_select'] );
	}
	
}
add_action( 'user_register' , 'wc_save_account_field');


/**
 * This exploits the tempating system in woocommerce.
 */
function ppm_template_include($template) {
	
	if( $_SERVER['REQUEST_URI'] == '/shop/' ) {
		return get_template_directory().'/woocommerce/listings/category-listing.php';
	}
	
	return $template;
	
}
add_filter( 'template_include', 'ppm_template_include', 11 );


/**
 * This spits out the images for the category and product images.
 */
function woocommerce_template_loop_product_thumbnail(){
	$url	= wp_get_attachment_image_src( get_post_thumbnail_id(get_the_id()), 'large');
	echo '<span class="image-thumb" style="background-image:url(\''.$url[0].'\');"></span>';
}


/**
 * Check if there are any quoted items in the cart any time the cart is updated
 */
function custom_woocommerce_cart_updated() {
	global $woocommerce;

	$quoted_items = array();
	foreach ($woocommerce->cart->get_cart() as $cur_item) {
		if ($cur_item['data']->product_type == 'variation' || strtolower(get_field('product_purchase_type', $cur_item['data']->id)) == 'quote') {
			$quoted_items[] = $cur_item;
		}
	}
	$woocommerce->cart->has_quoted_item = (count($quoted_items) > 0) ? true : false;
	$send_quote_or_cart = (count($quoted_items) > 0) ? 'quote' : 'cart';
}
add_filter( 'woocommerce_cart_updated', 'custom_woocommerce_cart_updated', 11 );


/**
 * Don't require payment for quotes
 */
function custom_woocommerce_cart_needs_payment() {
	global $woocommerce;

	return ( $woocommerce->total > 0 ) ? (($woocommerce->cart->has_quoted_item == true) ? false : true) : false;

}
add_filter( 'woocommerce_cart_needs_payment', 'custom_woocommerce_cart_needs_payment' );







function update_alert_read_status() {
	global $wpdb;
	//$wpdb->show_errors();
	
	$user_ID = get_current_user_id();
	$alertID =  $_POST['alertID'];
	
	if( isset($alertID) && isset($user_ID) ){
		$alertQuery = $wpdb->query(
			$wpdb->prepare( 
				"
					UPDATE wp_ppttd_batch_alerts
					SET `read` = %d
					WHERE `user_id` = %d
					AND `id` = %d
				",
		        1, $user_ID, $alertID
	        )
		);
		if($alertQuery){
			// echo "Success!";
		} else {
			// echo "query failed. ";
			// $wpdb->print_error();
		}
	} else {
		// echo "Alert ID or User ID isn't set.";
	}
	
	die();
}
add_action( 'wp_ajax_update_alert_read_status', 'update_alert_read_status' );
// add_action( 'wp_ajax_nopriv_update_alert_read_status', 'update_alert_read_status' );


function cash_advance_form_submit() {
	global $wpdb;
	
	// Set the posted variables
	$username = $_POST['username'];
	$full_name = $_POST['full_name'];
	$email = $_POST['email_add'];
	$phone = $_POST['phone'];
	$business_start_date = $_POST['business_start_date'];
	$amount = $_POST['elig'];
	
/*
	$property_lease_date = $_POST['property_lease_date'];
	$property_lease_term = $_POST['property_lease_term'];
	$property_building_type = $_POST['property_building_type'];
	$property_square_footage = $_POST['property_square_footage'];
	$business_name_1 = $_POST['business_name_1'];
	$contact_name_1 = $_POST['contact_name_1'];
	$contact_phone_1 = $_POST['contact_phone_1'];
	$business_name_2 = $_POST['business_name_2'];
	$contact_name_2 = $_POST['contact_name_2'];
	$contact_phone_2 = $_POST['contact_phone_2'];
	$business_name_3 = $_POST['business_name_3'];
	$contact_name_3 = $_POST['contact_name_3'];
	$contact_phone_3 = $_POST['contact_phone_3'];
	
	$message .= 		'<br />
		Property Lease Date: '.$property_lease_date.' <br />
		Property Lease Term: '.$property_lease_term.' <br />
		Property Building Type: '.$property_building_type.' <br />
		Property Square Footage: '.$property_square_footage.' <br />
		<br />
		Business Name 1: '.$business_name_1.' <br />
		Contact Name 1: '.$contact_name_1.' <br />
		Contact Phone 1: '.$contact_phone_1.' <br />
		<br />
		Business Name 2: '.$business_name_2 .' <br />
		Contact Name 2: '.$contact_name_2 .' <br />
		Contact Phone 2: '.$contact_phone_2 .' <br />
		<br />
		Business Name 3: '.$business_name_3 .' <br />
		Contact Name 3: '.$contact_name_3 .' <br />
		Contact Phone 3: '.$contact_phone_3;
*/
	
	
	
	//$to = 'curtis.wolfenberger@gmail.com';
	$to = 'cash@payprotec.com,marcb@payprotec.com';
	//$to = 'jfarrell@payprotec.com';
	
	$subject = 'Saltsha - Cash Advance Request';

	$message = '
		<h1 style="margin:0; padding:0; color:#444;">Cash Advance Request</h1>
		<p style="margin:0; padding:0; color:#444;">A user has submitted this information from the Cash Advance form on Saltsha.</p>
		<hr />
		<br />
		Name: '.$full_name.' <br />
		MID: '.$username.'<br />
		Email: '.$email.' <br />
		Phone: '.$phone.' <br />
		Business Start Date: '.$business_start_date.' <br />
		Could be eligible for: '.$amount.' <br />

	';
	
	// Set up the headers to send html and attachments in the email
	$mime_boundary="==Multipart_Boundary_x".md5(mt_rand())."x";
	$headers =	"From: Saltsha <success@saltsha.com>\r\n" .
				"Reply-To: Saltsha <success@saltsha.com>\r\n" .
				"MIME-Version: 1.0\r\n" .
				"X-Mailgun-Native-Send: true\r\n" .
				"Content-Type: multipart/mixed;\r\n" .
				" boundary=\"{$mime_boundary}\"";
				
	// Start the message
	$message =	"This is a multi-part message in MIME format.\n\n" .
				"--{$mime_boundary}\n" .
				"Content-Type: text/html; charset=\"iso-8859-1\"\n" .
				"Content-Transfer-Encoding: 7bit\n\n" .
				$message . "\n\n";
	
	// Loop through and attach the uploaded files
	foreach($_FILES as $userfile){
		$tmp_name = $userfile['tmp_name'];
		$type = $userfile['type'];
		$name = $userfile['name'];
		$size = $userfile['size'];
		if (file_exists($tmp_name)){
			if(is_uploaded_file($tmp_name)){
				$file = fopen($tmp_name,'rb');
				$data = fread($file,filesize($tmp_name));
				fclose($file);
				$data = chunk_split(base64_encode($data));
			}
			$message .=	"--{$mime_boundary}\n" .
						"Content-Type: {$type};\n" .
						" name=\"{$name}\"\n" .
						"Content-Disposition: attachment;\n" .
						" filename=\"{$fileatt_name}\"\n" .
						"Content-Transfer-Encoding: base64\n\n" .
						$data . "\n\n";
		}
	}
	$message.="--{$mime_boundary}--\n";

		
		
	$sendCashMail = @mail($to, $subject, $message, $headers);
	if($sendCashMail){
		echo "<span class='success'>Thank you for sending your information. We'll get back to you as soon as possible.</span>";
	} else {
		echo "<span class='failure'>Something went wrong while trying to send your information. Please try again later.</span>";
	}
	
	
	die();
}
add_action( 'wp_ajax_cash_advance_form_submit', 'cash_advance_form_submit' );
add_action( 'wp_ajax_nopriv_cash_advance_form_submit', 'cash_advance_form_submit' );


/**
 * Extends the user search to include MID's
 */
add_action( 'pre_user_query', 'search_user_mids' );
function search_user_mids( $user_search ){
	global $wpdb;
	// Make sure this is only applied to user search
	$search = trim( $user_search->query_vars['search'], '*' );
	if (!is_null($search)) {
		$user_search->query_from .= " INNER JOIN {$wpdb->usermeta} ON " . 
			"{$wpdb->users}.ID={$wpdb->usermeta}.user_id AND " .
			"{$wpdb->usermeta}.meta_key='ppttd_merchant_info' ";
		$description_where = $wpdb->prepare("{$wpdb->usermeta}.meta_value LIKE '%s'",
			"%{$search}%");
		$user_search->query_where = str_replace('WHERE 1=1 AND (',
			"WHERE 1=1 AND ({$description_where} OR ",$user_search->query_where); 
	}
}





/**
 * Adds new user's info to our Saltsha Mailchimp list upon user creation
 */
function add_new_user_to_mailchimp( $user_id = null ) {

	if(isset($user_id) && $user_id!==null){
		
		$user = get_userdata($user_id);
		
		require_once(get_template_directory()."/MailChimp-api.php");
		
		$email = $user->user_email;
		$first_name = $user->first_name;
		$last_name = $user->last_name;
		$company = $user->company_select;
		$mid = ltrim($user->user_login, '0');
		
		if (is_numeric($mid)) {
			$MailChimp	= new \Drewm\MailChimp('bcc0cef13b11b2b85eb690de7a0e7abe-us8');
			$MailChimp->call('lists/subscribe', 
								array(
							        'id'                =>	'433ce1636f',
							        'email'             =>	array('email'=>$email),
							        'merge_vars'        =>	array(
								        						'FNAME'=>$first_name,
								        						'LNAME'=>$last_name,
								        						'MERGE3'=>$mid,
								        						'MERGE14'=>$company
															),
							        'double_optin'      =>	false,
							        'update_existing'   =>	false,
							        'replace_interests' =>	false,
							        'send_welcome'      =>	false,
							    )
						    );		
			
		}
		
	}


}
add_action( 'user_register', 'add_new_user_to_mailchimp' );


// Active MID changing functions
include 'functions/merchant_functions.php';
?>
