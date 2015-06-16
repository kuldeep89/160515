<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.1
 */
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php if( function_exists('the_field') ): ?>
	<link rel="icon" type="image/ico" href="<?php the_field('ico_favicon', 'option') ?>"/>
	<link rel="icon" type="image/png" href="<?php the_field('png_favicon', 'option') ?>" />
	<link rel="apple-touch-icon" href="<?php the_field('apple_touch_icon', 'option') ?>" />
<?php endif; ?>
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	//bloginfo( 'name' );

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'ppmlayout' ), max( $paged, $page ) );
	?></title>

<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

<?php
	// WP head file (required)
	wp_head();
?>

<?php 
$serverList = array('localhost', '127.0.0.1');
if(!in_array($_SERVER['REMOTE_ADDR'], $serverList)): ?>
	<link href="<?php echo get_template_directory_uri() ?>/css/build/checkout.production.min.css" rel='stylesheet' type='text/css'>
<?php else: ?>
	<link href="<?php echo get_template_directory_uri() ?>/css/build/checkout.css" rel='stylesheet' type='text/css'>
<?php endif; ?>

<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
<script src="dist/html5shiv.js"></script>
<![endif]-->

<?php echo (is_user_logged_in()) ? '<script type="text/javascript">var user_logged_in = true;</script>' : '<script type="text/javascript">var user_logged_in = false;</script>' ?>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	
	ga('create', 'UA-46649787-1', 'saltsha.com');
	ga('require', 'displayfeatures');
	ga('send', 'pageview');
</script>
</head>
<body class="fixed-top">
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
    <![endif]-->

<!-- BEGIN CONTAINER -->
   <div class="page-container">
	    