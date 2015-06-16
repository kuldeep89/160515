<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
	    <title><?php if ( is_category() ) {
	      echo 'Category Archive for &quot;'; single_cat_title(); echo '&quot; | '; bloginfo( 'name' );
	    } elseif ( is_tag() ) {
	      echo 'Tag Archive for &quot;'; single_tag_title(); echo '&quot; | '; bloginfo( 'name' );
	    } elseif ( is_archive() ) {
	      wp_title(''); echo ' Archive | '; bloginfo( 'name' );
	    } elseif ( is_search() ) {
	      echo 'Search for &quot;'.esc_html($s).'&quot; | '; bloginfo( 'name' );
	    } elseif ( is_home() || is_front_page() ) {
	      bloginfo( 'name' ); echo ' | '; bloginfo( 'description' );
	    }  elseif ( is_404() ) {
	      echo 'Error 404 Not Found | '; bloginfo( 'name' );
	    } elseif ( is_single() ) {
	      wp_title('');
	    } else {
	      echo wp_title( ' | ', 'false', 'right' ); bloginfo( 'name' );
	    } ?></title>
    
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,700' rel='stylesheet' type='text/css'>
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png" type="image/png">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-144x144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-114x114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-72x72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png">
		
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/app.css" />
		
			
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		
		<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			ga('create', 'UA-52596840-1', 'auto');
			ga('require', 'displayfeatures');
			ga('send', 'pageview');
		</script>
		
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
	
		<!--[if lt IE 9]>
	        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
	    <![endif]-->

		
		<?php do_action('foundationPress_after_body'); ?>
	
		<div class="off-canvas-wrap" data-offcanvas>
			<div class="inner-wrap">
	  
		  		<?php do_action('foundationPress_layout_start'); ?>
	  
			  	<nav class="tab-bar show-for-small-only">
			    	<section class="left-small">
						<a class="left-off-canvas-toggle menu-icon" ><span></span></a>
					</section>
					<section class="middle tab-bar-section">
						<h1 class="title">
							<a href="<?php echo home_url(); ?>">
								<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/assets/img/logo.png" alt=""/>
							</a>
						</h1>
					</section>
				</nav>
		
				<aside class="left-off-canvas-menu">
				
					<?php foundationPress_mobile_off_canvas(); ?>
					
				</aside>
	  
				<div class="top-bar-container contain-to-grid show-for-medium-up">
					<h1>
						<a href="<?php echo home_url(); ?>">
							<img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/assets/img/logo.png" alt=""/>
						</a>
					</h1>
					<span>Powered by</span>
					<a href="http://payprotec.com" target="_blank"><img src="<?php echo get_bloginfo('stylesheet_directory'); ?>/assets/img/pay-logo.png" alt=""/></a>
					<nav class="top-bar" data-topbar="">
						<?php wp_nav_menu( array('menu' => 'Header menu' )); ?>
					</nav>
				</div>
	
				<section class="container" role="document">
				
					<?php do_action('foundationPress_after_header'); ?>
