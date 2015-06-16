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
 // Pre-select whether ther person has chosen monthly or yearly
		global $woocommerce;
		$args = array( 'post_type' => 'product' );
		$products = new WP_Query( $args );
		foreach ($products->posts as $cur_product) {
			// Get monthly product
			if (!isset($monthly_product)) {
				if (stripos($cur_product->post_title, 'month') !== false) {
					$monthly_product = array('id' => $cur_product->ID, 'name' => $cur_product->post_title);
				}
			}

			// Get yearly product
			if (!isset($yearly_product)) {
				if (stripos($cur_product->post_title, 'year') !== false || stripos($cur_product->post_title, 'annual') !== false) {
					$yearly_product = array('id' => $cur_product->ID, 'name' => $cur_product->post_title);
				}
			}

			// If both monthly and yearly product are set, exit for loop
			if (isset($monthly_product) && isset($yearly_product)) {
				break;
			}
		}
		// If setting billing from $_GET var, make the change
		if (isset($_GET['billing']) && !is_null($_GET['billing']) && trim($_GET['billing']) != '') {
			if ($_GET['billing'] == 'monthly') {
				change_subscription($monthly_product['id']);
			} else {
				change_subscription($yearly_product['id']);
			}
		}
		if( is_user_logged_in() ){
			// Get rewards points
            $user_reward_points = get_user_reward_points();
		}

        // Get merchant ID
        $merchant_data = get_the_author_meta('ppttd_merchant_info', get_current_user_id());

        // Get merchant ID
        $merchant_ids = get_merchant_ids();

        // If active merchant ID is not set, set it
        if (!isset($_SESSION['active_mid']) || !isset($_SESSION['active_mid']['merchant_id']) || trim($_SESSION['active_mid']['merchant_id']) === '') {
            // Assign initial merchant ID
            $_SESSION['active_mid'] = array('merchant_id' => $merchant_ids[0]->merchant_id, 'merchant_name' => $merchant_ids[0]->merchant_name);
        }

        // Display single or multiple merchant IDs
        $merchant_id_options = "";

        // Check if merchant ID is one or more
        if (count($merchant_ids) > 0) {
            // Loop through merchant IDs
            foreach ($merchant_ids as $cur_merchant) {
                // Get merchant info
                $cur_merchant_name = (trim($cur_merchant->merchant_name) === "") ? $cur_merchant->merchant_id : $cur_merchant->merchant_name;
                $cur_merchant_id = $cur_merchant->merchant_id;

                // Hide option if this is the active merchant ID
                $do_display = ($_SESSION['active_mid']['merchant_id'] === $cur_merchant_id) ? ' style="display:none;"' : '';

                // Display merchant option
                if ($cur_merchant_name === $cur_merchant_id) {
                    $merchant_id_options .= '<a class="mid_selector '.$cur_merchant->merchant_id.'"'.$do_display.'>'.$cur_merchant_id.'</a>';
                } else {
                    $merchant_id_options .= '<a class="mid_selector '.$cur_merchant->merchant_id.'"'.$do_display.'>'.$cur_merchant_name.' ('.$cur_merchant_id.')</a>';
                }
            }
        }
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
	<link href="<?php echo get_template_directory_uri() ?>/css/build/production.min.css" rel='stylesheet' type='text/css'>
<?php else: ?>
<!--<script>//var BugHerdConfig = {"feedback":{"tab_position":"bottom-left"}};</script>-->
	<link href="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/style-metro.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/style.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/gritter/css/jquery.gritter.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap-daterangepicker/daterangepicker.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css" rel="stylesheet" type="text/css"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/pages/blog.css" rel="stylesheet" type="text/css" media="screen"/>
    <link href="<?php echo get_template_directory_uri() ?>/assets/css/pages/news.css" rel="stylesheet" type="text/css"/>
	<link href="<?php echo get_template_directory_uri() ?>/css/build/style.css" rel='stylesheet' type='text/css'>
	<link href="<?php echo get_template_directory_uri() ?>/css/shop/style.css" rel='stylesheet' type='text/css'>
	<link href="<?php echo get_template_directory_uri() ?>/css/dashboard.css" rel='stylesheet' type='text/css'>
	<link href="<?php echo get_template_directory_uri() ?>/css/shop/fancy-product-designer.css" rel='stylesheet' type='text/css'>

<?php endif; ?>

<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

<!--[if lt IE 9]>
<script src="dist/html5shiv.js"></script>
<![endif]-->

<?php echo (is_user_logged_in()) ? '<script type="text/javascript">var user_logged_in = true; var active_mid = "'.$_SESSION['active_mid']['merchant_id'].'";</script>' : '<script type="text/javascript">var user_logged_in = false;</script>' ?>

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		ga('create', 'UA-52596840-2', 'auto');
		ga('require', 'displayfeatures');
		ga('send', 'pageview');
	</script>

</head>
<body class="fixed-top">
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an outdated browser. <a href="http://browsehappy.com/">Upgrade your browser today</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to better experience this site.</p>
    <![endif]-->
<!-- BEGIN HEADER -->
   <div class="header navbar navbar-inverse navbar-fixed-top">
      <!-- BEGIN TOP NAVIGATION BAR -->
      <div class="navbar-inner">
         <div class="container-fluid">
            <!-- BEGIN LOGO -->
            <a class="brand" href="/">
            	<img src="<?php echo get_template_directory_uri() ?>/assets/img/logo-big.png" alt="logo" />
            </a>
            <?php if ( is_user_logged_in() ): 
		            $user_ID = $get_current_user->ID;
					$company_select = get_the_author_meta( 'company_select', $user_ID );
					$company = company_select_data($company_select);
	
		    ?>
		        
	            <a class="sub_brand" href="<?php echo $company['link']; ?>" target="_BLANK">
		            Powered by 
	            	<img src="<?php echo $company['logo']; ?>" alt="<?php echo $company['name']; ?>" />
	            </a>
            <?php else: ?>
        		<?php
					$company = company_get();
				?>
	            <a class="sub_brand" href="<?php echo $company['link']; ?>" target="_BLANK">
		            Powered by 
	            	<img src="<?php echo $company['logo']; ?>" alt="<?php echo $company['name']; ?>" />
	            </a>
			<?php endif; ?>
            
            <!-- END LOGO -->
            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="btn-navbar collapsed" data-toggle="collapse" data-target=".nav-collapse">
            	<img src="<?php echo get_template_directory_uri() ?>/assets/img/menu-toggler.png" alt="" />
            </a>
            <!-- END RESPONSIVE MENU TOGGLER -->

            <!-- BEGIN TOP NAVIGATION MENU -->
            
            <?php if (!is_user_logged_in()): ?> 
	            <a href="#myModal" role="button" class="login_register_button" data-toggle="modal">Log in / Register</a>
			<?php else: ?>
	            <ul class="nav pull-right">
		            <li class="alert_link">
		            	<?php
			            	
			            	
							// Query for any new alerts
							$alert_count = $wpdb->get_var( "SELECT COUNT(*) FROM wp_ppttd_batch_alerts WHERE user_id='".$user_ID."' AND `read`=0;" );
			            	if($alert_count > 0){
				            	$alert_class = 'has_alert';
				            	$alert_number = '<span>'.$alert_count.'</span>';
			            	} else {
				            	$alert_class = '';
				            	$alert_number = '';
			            	}
		            	?>
		            	<a href="/alerts" class="<?php echo $alert_class; ?>"><?php echo $alert_number; ?></a>
		            </li>

                    <!-- BEGIN MERCHANT DROPDOWN -->
                   <li class="dropdown merchant">
                      <div class="mid_loading">Please wait...</div>
                      <?php if (count($merchant_ids) > 1) { ?> <a class="dropdown-toggle" data-toggle="dropdown"><?php } else { ?><div class="mid_no_dropdown"><?php } ?>
                      <span class="show_mid">
                      	  <?php echo stripslashes($_SESSION['active_mid']['merchant_name']).' ('.$_SESSION['active_mid']['merchant_id'].')' ?>
                      </span>
                      <?php if (count($merchant_ids) > 1) { ?><i class="icon-angle-down"></i></a><?php } else { ?></div> <?php } ?>
                      
                      <ul class="dropdown-menu">
                        <li>
                            <?php echo $merchant_id_options ?>
                        </li>
                      </ul>
                   </li>
                   <!-- END MERCHANT DROPDOWN -->

	               <!-- BEGIN USER LOGIN DROPDOWN -->
	               <li class="dropdown user">
	                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
	                  <span class="username">
	                  	<?php
						    $current_user = wp_get_current_user();
							$user_ID = $current_user->ID;
						    if ( !($current_user instanceof WP_User) )
						        return;
	/* 						echo get_avatar( $user_ID, 25 );   */
							if ( !is_user_logged_in() ) { echo "Login"; }
						    echo ' &nbsp;' . $current_user->user_firstname . ' ';
						    echo  $current_user->user_lastname ;
						    if (isset($single)) {
							    $merchant_id = get_user_meta(get_current_user_id(), 'merchant_id', $single);
						    }
	
						    echo (isset($merchant_id)) ? ' | '.$merchant_id[0] : '';
	
	
						?>
	                  </span>
	                  <i class="icon-cog white-text"></i>
	                  </a>
	                  <ul class="dropdown-menu">
	                     <?php if (is_user_logged_in()) { ?><li><a href="/account"><i class="saltsha-nav-icon saltsha-account-profile"></i> My Profile</a></li><?php } ?>
	                     <li><a href="<?php echo (is_user_logged_in()) ? wp_logout_url(home_url()) : '#myModal' ?>"<?php echo (is_user_logged_in()) ? '' : ' data-toggle="modal"' ?>><i class="saltsha-nav-icon saltsha-account-log"></i> <?php echo (is_user_logged_in()) ? 'Log Out' : 'Log In' ?></a></li>
	                  </ul>
	               </li>
	               <!-- END USER LOGIN DROPDOWN -->
	            </ul>
			<?php endif; ?>
            <!-- END TOP NAVIGATION MENU -->
         </div>
      </div>
      <!-- END TOP NAVIGATION BAR -->
   </div>
<!-- END HEADER -->

<!-- BEGIN CONTAINER -->
   <div class="page-container">
	    <?php get_template_part( 'sidebar', 'navigation' ); ?>

	    <!-- BEGIN PAGE -->
	    <div class="page-content">
		 <!-- BEGIN SAMPLE PORTLET CONFIGURATION MODAL FORM-->
		 <div id="portlet-config" class="modal hide">
		    <div class="modal-header">
		       <button data-dismiss="modal" class="close" type="button"></button>
		       <h3>Widget Settings</h3>
		    </div>
		    <div class="modal-body">
		       Widget settings form goes here
		    </div>
		 </div>
		 <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
