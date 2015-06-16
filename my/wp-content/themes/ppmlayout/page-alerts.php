<?php
/**
 * The default template for displaying alerts
 * Template Name: Alerts
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
 
//Just making sure the user is logged in. 
if( is_user_logged_in() ) {
	
	//Get the user meta to see if they reset their password.
	$value	= get_user_meta(get_current_user_id(), 'reset_password', TRUE);
	
	if( $value == '1' ) {
		update_user_meta(get_current_user_id(), 'reset_password', '0');
		wp_redirect('/shop/change-password/?reset=true');
	}
	
	// Get rewards points
    $user_reward_points = get_user_reward_points();
	
	$user_ID = get_current_user_id();
	
	$wpdb->update( 'wp_ppttd_batch_alerts', array( 'read'=>'1' ), array( 'user_id'=>$user_ID ) );
	
	// Query for any new alerts
	$alertQuery = $wpdb->get_results( "SELECT * FROM wp_ppttd_batch_alerts WHERE user_id='".$user_ID."' ORDER BY date_created DESC;" );
	$alertBoxes = "";
	
	// Set up the alerts and display them below.
	foreach( $alertQuery as $alertRow ){
		if( $alertRow->alert_type == "batch_below" || $alertRow->alert_type == "days_since" || $alertRow->alert_type == "red" ){
			$alert_color = "alert-red";
		} elseif( $alertRow->alert_type == "yellow" ) {
			$alert_color = "alert-yellow";
		} else {
			$alert_color = "alert-green";
		}
		if( $alertRow->system == 1 ){
			$alert_icon = '<i class="alert-icon icon-exclamation-sign"></i>';
		} else {
			$alert_icon ='';
		}
		$alertBoxes .=	'<div class="alert saltsha-alert '.$alert_color.' alerts_page_alert" style="padding:.75em 1em;">'.
							'<strong>'.$alert_icon.' '.$alertRow->alert_text.'</strong>'.
							'<span style="float:right;">'.date('m/d/Y', strtotime($alertRow->date_created)).'</span>'.
						'</div>';
	}
}
get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-breadcrumb-heading">
	<h3 class="page-title">
		<?php the_title(); ?>
		<small>
			<?php if( function_exists('the_field') ) {the_field('page_subtitle');} ?>
		</small>
	</h3>
	<ul class="breadcrumb">
		<li>
			<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
		</li>	
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->
 <!-- BEGIN PAGE CONTAINER-->
 <div class="container-fluid">
    <div id="dashboard">
       <div class="row-fluid">
          <div class="span12 responsive">
	          <?php echo $alertBoxes; ?>
	      </div>                  
       </div>
       <div class="clearfix"></div>
    </div>
 </div>
 <!-- END PAGE CONTAINER-->    
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>

		