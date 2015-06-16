<?php
/*
	Template Name: Dashboard
	Main dashboard page for my.saltsha.com.
*/

//Just making sure the user is logged in. 
if( is_user_logged_in() ) {

	
	require_once get_home_path().'wp-includes/qb_api/app_ipp_v3/config.php';  //1june2015: Chetu:  require config file for including QB library 
	
	//Get the user meta to see if they reset their password.
	$value	= get_user_meta(get_current_user_id(), 'reset_password', TRUE);
	
	if( $value == '1' ) {
		update_user_meta(get_current_user_id(), 'reset_password', '0');
		wp_redirect('/shop/change-password/?reset=true');
	}
	
	// Get rewards points
    $get_current_user = wp_get_current_user();
    $user_reward_points = get_user_reward_points();
	
	$user_ID = get_current_user_id();
	
	// Query for any new alerts
	$alertQuery = $wpdb->get_results( "SELECT * FROM wp_ppttd_batch_alerts WHERE user_id='".$user_ID."' AND `read`=0 AND `system`=1 ORDER BY date_created DESC;" );
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
		$alertBoxes .=	'<div class="alert saltsha-alert '.$alert_color.'">'.
							'<button class="alert-close close" data-dismiss="alert" data-alertid="'.$alertRow->id.'"></button>'.
							'<i class="alert-icon icon-exclamation-sign"></i> <strong>'.$alertRow->alert_text.'</strong>'.
						'</div>';
	}
}
get_header(); 

?>
	<?php include('get-ip.php')?>
	<?php while ( have_posts() ) : the_post(); ?>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class="page-breadcrumb-heading">
		<div class="left">
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
		<?php if( is_user_logged_in() ): ?>
			<div class="right header_rewards">
				You have <strong><?php echo $user_reward_points; ?> points!</strong> <a href="/loyalty-rewards/">Redeem</a>
			</div>
		<?php endif; ?>
	</div>
	<!-- END PAGE TITLE & BREADCRUMB-->
	<!-- BEGIN PAGE CONTAINER-->
	<div class="container-fluid">
	<?php endwhile; // end of the loop. ?>

	<div id="dashboard">
		<?php echo ( isset($alertBoxes) ) ? $alertBoxes : ''; // Displays the alerts if logged in ?>
		<div class="clearfix"></div>
			<div class="row-fluid">
				<div class="span8">
					<!--
					<div class="row-fluid">
						<div class="span12 saltsha-academy-container">
							<div class="span4 saltsha-academy">
								<img src="/wp-content/themes/ppmlayout/assets/img/academy-logo.png" title="Saltsha Academy" />
							</div>
							<div class="span8 saltsha-academy-header-content">
								<div class="row-fluid">
									<div class="span12">
										<div class="span6 academy-heading">
											<h3>SALTSHA ACADEMY</h3>
										</div>
										<div class="span6 academy-features">
											<img src="/wp-content/themes/ppmlayout/assets/img/academy-features.png" title="Saltsha Academy" />
										</div>
									</div>
								</div>
								<div class="row-fluid">
									<div class="span12">
										<p>
											<?php
												$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_name = 'dashboard'");
												echo ($post && count($post) > 0) ? $post[0]->post_content: '';
											?>
										</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					-->
					
					<?php 
						// Display the Transactional data, Loyalty rewards, and Cash advance info on the dashboard.
						echo do_shortcode('[transactional_data_summary_dashboard]');
					
					?>
					
					<div class="row-fluid">
						<?php
							$catids = get_all_category_ids();					// retrieves all category id's
							unset($catids[array_search(1,$catids)]);			// removes category "Uncategorized" (id=1) from array	
							unset($catids[array_search(678,$catids)]);			// removes category "Upgrade Page" (id=678) from array	
							$catids = array_values($catids);  					// removes blank keys from array
							shuffle($catids);									// randomizes categories

							echo "<div class='span6'>";
							for ($i=0; $i<=3; $i++) {
								if ($i % 2 == 0) {
									$categories = get_category($catids[$i]);
								
									echo '<div id="'.$categories->slug.'">';
									echo '<div class="top-news"><a  class="btn dashboard-stat" href="/' . $categories->taxonomy.'/'.$categories->category_nicename.'/" title="' . sprintf( __( "View all posts in %s" ), $categories->name ) . '" ' . '>';
									echo '<span>' . $categories->name.'</span><em>' . $categories->description . '</em><i class="saltsha-cat-icon saltsha-cat-' . $categories->slug.'"></i></a></div>';
	
									// Get posts for this category, loop through and display
									query_posts( 'cat=' . $catids[$i] . '&posts_per_page=3&orderby=date&order=DESC' );
									while ( have_posts() ) : the_post();
									    $the_permalink = (stripos($post->guid, '?p') === false) ? $post->guid : get_permalink();
									    $the_excerpt = implode(' ', array_slice(explode(' ', strip_tags($post->post_content)), 0, 40));
	
										echo '<div class="news-blocks">';
										echo '<a href="' . $the_permalink . '">';
										echo (has_post_thumbnail()) ? the_post_thumbnail('post-blurb-size', array('class'=>'img-responsive')) : '';
										echo '</a>';
										echo '<div class="news-block-padding">';
									    echo '<h3><a href="' . $the_permalink . '">' . $post->post_title . '</a></h3>';
									    echo '<div class="news-block-tags">'.date('m/d/Y', strtotime($post->post_date)).'</div>';
									    echo '<p>' . $the_excerpt . '...</p>';
									    echo '</div></div>';
									endwhile;
	
									echo '</div>';
									
									   
									wp_reset_query();
								}
							}
							echo '</div>';
							echo '<div class="span6">';
								for ($i=0; $i<=3; $i++) {
									if ($i % 2 == 0) {
									} else {
										$categories = get_category($catids[$i]);
									
										echo '<div id="'.$categories->slug.'">';
										echo '<div class="top-news"><a  class="btn dashboard-stat" href="/' . $categories->taxonomy.'/'.$categories->category_nicename.'/" title="' . sprintf( __( "View all posts in %s" ), $categories->name ) . '" ' . '>';
										echo '<span>' . $categories->name.'</span><em>' . $categories->description . '</em><i class="saltsha-cat-icon saltsha-cat-' . $categories->slug.'"></i></a></div>';
		
										// Get posts for this category, loop through and display
										query_posts( 'cat=' . $catids[$i] . '&posts_per_page=3&orderby=date&order=DESC' );
										while ( have_posts() ) : the_post();
										    $the_permalink = (stripos($post->guid, '?p') === false) ? $post->guid : get_permalink();
										    $the_excerpt = implode(' ', array_slice(explode(' ', strip_tags($post->post_content)), 0, 40));
		
											echo '<div class="news-blocks">';
											echo '<a href="' . $the_permalink . '">';
											echo (has_post_thumbnail()) ? the_post_thumbnail('post-blurb-size', array('class'=>'img-responsive')) : '';
											echo '</a>';
											echo '<div class="news-block-padding">';
										    echo '<h3><a href="' . $the_permalink . '">' . $post->post_title . '</a></h3>';
										    echo '<div class="news-block-tags">'.date('m/d/Y', strtotime($post->post_date)).'</div>';
										    echo '<p>' . $the_excerpt . '...</p>';
										    echo '</div></div>';
										endwhile;
		
										echo '</div>';
										
										   
										wp_reset_query();
									}
								}
							echo '</div>';
						?>
					</div>
				</div> <!-- end the left span 8  -->
			

				<div class="span4 ui-portlet-widget ">
				
<!-- 					start social media  links		 -->
<!--
					<div class="portlet ">
						<div class="portlet-title "style="padding:0px;">
							<div class="top-news">
								<a class="btn dashboard-stat" style="margin-bottom: -10px;">	<span>Connect With Us</span>
									<i class="saltsha-cat-icon saltsha-cat-connect"></i></a>	
							</div>
						</div>
						<div class="portlet-body">			
							<div class="news-blocks db-connect-icons">
								<a href="https://www.facebook.com/Saltsha" target="_blank" data-original-title="Facebook" class="saltsha-social saltsha-social-fb"></a>
								<a href="https://plus.google.com/105387137872967257002" target="_blank>" data-original-title="Google Plus" class="saltsha-social saltsha-social-gp"></a>
								<a href="http://www.pinterest.com/saltsha/" target="_blank" data-original-title="pintrest" class="saltsha-social saltsha-social-pi"></a>
								<a href="https://twitter.com/saltsha" target="_blank>" data-original-title="Twitter" class="saltsha-social saltsha-social-tw"></a>
								<a href="http://www.stumbleupon.com/submit?url=https://saltsha.com/&title=Saltsha" target="_blank>" data-original-title="StumbleUpon" class="saltsha-social saltsha-social-su"></a>
								<a href="https://www.linkedin.com/company/5046905?trk=tyah&trkInfo=tarId%3A1396373617580%2Ctas%3Asaltsha%2Cidx%3A1-1-1" target="_blank>" data-original-title="LinkedIn" class="saltsha-social saltsha-social-li"></a>
							</div> 
						</div> 
					</div>
-->

					
					<!-- <div id="call-to-action ">
						<?php //the_field('hubspot_callout'); ?>
					</div> -->
					<?php
						if( have_rows('dashboard_ads', 'option') ){
							
							// loop through the rows of data
							while ( have_rows('dashboard_ads', 'option') ) { the_row();
								
									$ad_image = get_sub_field('ad_image');
									$ad_link = get_sub_field('ad_link');
									
									echo	'<div class="portlet">
												<a href="'.$ad_link.'" target="_blank" class="responsive_image">
													<img src="'.$ad_image["url"].'" alt="'.$ad_image["alt"].'"  />
												</a>	
											</div>';
									
							}
						
						}	
					?>
					<?php 
					
						include_once(ABSPATH.WPINC.'/rss.php');
						$feed = fetch_feed('https://saltsha.com/feed/');
						if ( ! is_wp_error( $feed ) ) :
							$maxitems = $feed->get_item_quantity( 5 ); 
							$feed_items = $feed->get_items( 0, $maxitems );
							//$feed = array_slice($feed->items, 0, 5);
							//echo	print_r($feed, true);
					?>	
					<div class="portlet satlsha-blog">
						<div class="portlet-title "style="padding:0px;">
							<div class="top-news">
								<a class="btn dashboard-stat" >	<span>Saltsha Blog</span>
									<i class="saltsha-cat-icon saltsha-cat-connect"></i></a>	
							</div>
						</div>
						<div class="portlet-body">			

							<div class="news-blocks">
								<?php foreach( $feed_items as $article ) : ?>
								
									<div class="news-blocks">
										<div class="news-block-padding">
									
											<h3><a target="_blank" href="<?php echo esc_url( $article->get_permalink() ); ?>"><?php echo esc_html( $article->get_title() ); ?></a></h3>
											
											<div class="article-meta">
												
												<a target="_blank" href="<?php echo esc_url( $article->get_permalink() ); ?>" class="news-block-btn feed-link">Read more <i class="m-icon-swapright m-icon-black"></i></a>
												
												<span class="feed-timestamp" title="<?php echo $article->get_date('j F Y | g:i a'); ?>">
													<?php echo $article->get_date('j F Y | g:i a'); ?>
												</span>
												
											</div>
											
										</div>
									</div>
								
								<?php endforeach; ?>
							</div>
							
						</div> <!-- end portlet body -->
					</div> <!-- end portlet  -->
					<?php
						else:
							// echo $feed->get_error_message();
						endif;
						
					?>		
					
					<div class="portlet ">
						<div class="portlet-title "style="padding:0px;">
							<div class="top-news">
								<a class="btn dashboard-stat" >	<span>Twitter Feed</span>
									<i class="saltsha-cat-icon saltsha-cat-twitter"></i></a>	
							</div>
						</div>
						<div class="portlet-body">			
							<div class="news-blocks">
								<div class="saltsha-twitter-name">
									@Saltsha
								</div>
								<div class="twitter-follow-button">
									<a href="https://twitter.com/saltsha" class="twitter-follow-button" data-size="large" data-show-count="false" data-show-screen-name="false" data-lang="en">
										Follow
									</a>
									<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
								</div>
				<?php	echo do_shortcode('[really_simple_twitter username="Saltsha" consumer_key="44cCcViKCtzA6GXMw1ulVw" consumer_secret="y8kRmr79VmmN3umYN1cmtxcHEkpPO3zeErLie2kOyc" access_token="2194455631-qSh53hIdmyUzHjLE14hrRa9u9hLNvfBdMnKk7uT" access_token_secret="A7qmq6I2O53HI0nnCyyXCzUJAnVPq232F3Q1Hmr5d9bxl"]'); ?>

							</div> 
						</div> <!-- end portlet body -->
					</div> <!-- end portlet  -->


				</div> <!-- end row 4 -->


		</div> <!-- end clearfix -->
	 </div> <!-- end dashboard -->


<div id="cashAdvanceVideo" class="modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
	<div class="modal-header">
		<h3 id="myModalLabel">Cash Advance Video</h3>
	</div>

	<div class="modal-body">
		<div class="row-fluid">
			<div class="span12">
				<iframe width="100%" height="400" src="https://www.youtube.com/embed/f6W0FEIqVAs" frameborder="0" allowfullscreen></iframe>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<a href="#" class="btn cancel" style="display:block; margin-top: 1.25em; font-weight:bolder; font-size: 1.2rem;" data-dismiss="modal" aria-hidden="true">Close</a>
			</div>
		</div>
	</div>
</div>

<div id="cashAdvanceModal" class="modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
	<div class="modal-header">
		<h3 id="myModalLabel">Cash Advance</h3>
		<p>Prefer to do this over the phone? Call <a href="tel:8884754153">888.475.4153</a></p>
	</div>

	<div class="modal-body">

		<form id="cashAdvanceForm" enctype="multipart/form-data">
			<input type="hidden" value="<?php echo $get_current_user->user_firstname.' '.$user_firstname->user_lastname; ?>" name="full_name" id="full_name" />
			<input type="hidden" value="<?php echo $get_current_user->user_login; ?>" name="username" id="username" />
			<div class="row-fluid">
				<div class="span6">
					<label for="email_add">Email Address <small class="required">*</small></label>
					<input type="email" class="input-text" name="email_add" id="email_add" value="<?php echo $get_current_user->user_email ?>" required />
				</div>
				<div class="span6">
					<label for="phone">Phone Number <small class="required">*</small></label>
					<input type="text" class="input-text" name="phone" id="phone" required />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label for="business_start_date">Business Start Date <small class="required">*</small></label>
					<input type="text" class="input-text" name="business_start_date" id="business_start_date" required />
				</div>
			</div>
			<!--
			<div class="row-fluid">
				<div class="span6">
					<label for="property_lease_date">Property Lease Date <small class="required">*</small></label>
					<input type="text" class="input-text" name="property_lease_date" id="property_lease_date" required />
				</div>
				<div class="span6">
					<label for="property_lease_term">Property Lease Term <small class="required">*</small></label>
					<input type="text" class="input-text" name="property_lease_term" id="property_lease_term" required />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span6">
					<label for="property_building_type">Property Building Type <small class="required">*</small></label>
					<input type="text" class="input-text" name="property_building_type" id="property_building_type" required />
				</div>
				<div class="span6">
					<label for="property_square_footage">Property Square Footage <small class="required">*</small></label>
					<input type="text" class="input-text" name="property_square_footage" id="property_square_footage" required />
				</div>
			</div>
<div class="row-fluid">
				<div class="span12">
					<p style="border-bottom:1px solid #ccc;">Trade References (3) <small class="required">*</small></p>
				</div>
			</div>
			<div class="row-fluid" style="border-bottom:1px solid #E9F0FA; padding-bottom:0; margin-bottom:.75em;">
				<div class="span4">
					<input type="text" class="input-text" name="business_name_1" id="business_name_1" placeholder="Business Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_name_1" id="contact_name_1" placeholder="Contact Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_phone_1" id="contact_phone_1" placeholder="Phone" required />
				</div>
			</div>
			<div class="row-fluid" style="border-bottom:1px solid #E9F0FA; padding-bottom:0; margin-bottom:.75em;">
				<div class="span4">
					<input type="text" class="input-text" name="business_name_2" id="business_name_2" placeholder="Business Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_name_2" id="contact_name_2" placeholder="Contact Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_phone_2" id="contact_phone_2" placeholder="Phone" required />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span4">
					<input type="text" class="input-text" name="business_name_3" id="business_name_3" placeholder="Business Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_name_3" id="contact_name_3" placeholder="Contact Name" required />
				</div>
				<div class="span4">
					<input type="text" class="input-text" name="contact_phone_3" id="contact_phone_3" placeholder="Phone" required />
				</div>
			</div>
-->
			<div class="row-fluid">
				<div class="span12">
					<p style="border-bottom:1px solid #ccc;">Latest Three Bank Statements</p>
				</div>
			</div>
			<div class="row-fluid" style="border-bottom:1px solid #E9F0FA; padding-bottom:0; margin-bottom:.75em;">
				<div class="span12">
					<input type="file" name="statement1" />
				</div>
			</div>
			<div class="row-fluid" style="border-bottom:1px solid #E9F0FA; padding-bottom:0; margin-bottom:.75em;">
				<div class="span12">
					<input type="file" name="statement2" />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<input type="file" name="statement3" />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<div id="sendButtons">
						<input id="msgSend" type="submit" class="btn redeem_points right" value="Send" />
						<a href="#" class="btn redeem_points cancel left" id="no-thanks" data-dismiss="modal" aria-hidden="true">Cancel</a>
					</div>
					<span id="sending">Sending...</span>
					<span id="responseMsg">
					</span>
					<a href="#" id="closeModal" class="btn redeem_points cancel left" data-dismiss="modal" aria-hidden="true">Close</a>
				</div>
			</div>

			<?php //$woocommerce->nonce_field('cash_advance_form_submit')?>
			<input type="hidden" name="action" value="cash_advance_form_submit" />
			<input type="hidden" name="elig" id="elig" />

		</form>
	</div>
</div>
<!-- 1june2015: Chetu: send ajax request to quick book api call page -->
<?php if ($quickbooks_is_connected) { ?>

<script>
	$.ajax({
			url:'<?php echo includes_url("qb_api/app_ipp_v3/qbpayment_apicall.php"); ?>',
			cache: false,
			dataType : 'json',    
			type: 'POST',
			success: function(result) {
				console.log(result.pull_record);
				if(result.pull_record!=0) {
					var pull_message = result.pull_record +' new records pulled successfully';
					console.log(pull_message);
				} else {
					console.log('No new Record found');
				}
			}		
    });
</script>
<?php 	
	} 
?>
<?php get_footer(); ?>

