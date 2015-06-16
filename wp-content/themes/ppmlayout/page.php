<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header(); ?>

	<?php if(get_field('sign_up_now') == true): ?>
		<div class="sign_up_now_side_button">
			<a href="https://my.<?php echo $_SERVER['HTTP_HOST']; ?>/shop/checkout/?billing=yearly">Sign Up Now!</a>
		</div>
	<?php endif; ?>

	<?php while(has_sub_field('templates')): ?>
		<?php if(get_row_layout() == 'text_section'): ?>
			<section class="texts <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row <?php the_sub_field('section_alignment'); ?>">
						<h2 class="col-xs-12"><?php the_sub_field('section_title'); ?></h2>
						<div class="col-xs-12"><?php the_sub_field('section_content'); ?></div>
					</div>
				</div>
			</section>
			
		<?php elseif(get_row_layout() == 'callout_section'): ?>
			<section class="callouts">
				<div class="container">
					<div class="row">
						<h2 class="col-md-5 col-sm-12"><?php the_sub_field("callout_large_title"); ?></h2>
						<div class="col-md-7 col-sm-12">
							<h4><?php the_sub_field('callout_small_title'); ?></h4>
							<p><?php the_sub_field('callout_content'); ?></p>
							<div class="row">
								<div class="col-sm-7 col-xs-12 button">
									<?php if(!get_sub_field('use_external_link')): ?>
										<a href="<?php the_sub_field('callout_link'); ?>">
									<?php else: ?>
										<a href="<?php the_sub_field('external_link'); ?>" target="_blank">
									<?php endif; ?>
										<?php the_sub_field('callout_button_text'); ?>
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
			
		<?php elseif(get_row_layout() == 'image_callout_section'): ?>
			<section class="imageCallouts <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<?php if(get_sub_field('image_alignment') == 'top'): ?>
							<div class="col-xs-12">
								<?php 
									$image = get_sub_field('callout_image');
									echo '<img class="img-responsive calloutImage" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
								?>
							</div>
								<?php echo '<div class="col-xs-12 calloutContent top">';
									include 'imageCallout-Content.php';
								echo '</div>'; ?>
						<?php else: ?>
							<?php if(get_sub_field('image_alignment') == 'right') { 
								echo '<div class="col-md-7 col-xs-12 calloutContent">'; 
								include 'imageCallout-Content.php'; 
								echo '</div>'; 
							} ?>
							<div class="col-md-5 col-sm-12">
								<?php 
									$image = get_sub_field('callout_image');
									echo '<img class="img-responsive calloutImage" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />';
								?>
							</div>
							<?php if(get_sub_field('image_alignment') == 'left') {
								echo '<div class="col-md-7 col-xs-12 calloutContent">'; 
								include 'imageCallout-Content.php'; 
								echo '</div>'; 
							} ?>
						<?php endif; ?>
					</div>
				</div>
			</section>
			
		<?php elseif(get_row_layout() == 'full_banner_section'): ?>
			<section class="full_banner">
				<?php 
					$image = get_sub_field('banner_image');
					echo '<img class="img-responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />';
				?>
			</section>
		
		<?php elseif(get_row_layout() == 'learn_more_section'): ?>
			<section class="learn_more <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<?php $columns = get_sub_field('how_many_columns'); ?>
						<?php if(have_rows('group')): ?>
							<?php while (have_rows('group')): the_row();  ?>
								
								<?php if(!get_sub_field('use_alt_image')): ?>
									<section class="icons col-sm-<?php echo $columns; ?> col-xs-12">
										<div>
											<div class="<?php the_sub_field('icon'); ?>"></div>
								<?php else: ?>
									<section class="col-sm-<?php echo $columns; ?> col-xs-12">
										<div>
											<div class="logo_wrap">
												<?php 
													$image = get_sub_field('alt_image');
													echo '<img class="img-responsive calloutImage" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
												?>
											</div>
								<?php endif; ?>
								<?php if(get_sub_field('title')):
									echo '<h4>' . get_sub_field('title') . '</h4>';
								endif;
								if(get_sub_field('blurb')):
									echo '<p>' . get_sub_field('blurb') . '</p>';
								endif; ?>
									</div>
								</section>
							<?php endwhile; ?>
						<?php endif; ?>

					</div>
				</div>
			</section>
			
		<?php elseif((get_row_layout() == 'posts_section') && get_sub_field('posts')): ?>
			<section class="posts <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<?php if ( have_posts() ): ?>
							<h2 class="col-xs-12"><?php the_sub_field('section_title'); ?></h2>
							<?php
								$args = array( 'posts_per_page' => 4 );
								$rowCount = 0;
								$columnStart = '<div class="col-md-6 col-sm-12">';
								$postsList = get_posts( $args );
									
								setup_postdata( $post ); 
								foreach ( $postsList as $post ):
								if ($rowCount == 0) { 
									echo $columnStart;
								} elseif (($rowCount % 2) == 0 ) { 
									echo "</div>".$columnStart;
								}
								$rowCount++;
							?> 
								  	
								<article>
									<div class="post">
										<a class="thumbLink" href="<?php the_permalink(); ?>">
											<?php the_post_thumbnail( array(555, 243), array('class' => 'img-responsive postThumb') ); ?> 
										</a>
										<a href="<?php the_permalink(); ?>">
											<h3><?php the_title(); ?></h3>
										</a>
										<div>
											<?php the_field('post_blurb'); ?>
											<a class="readMore" href="<?php the_permalink(); ?>">READ MORE</a>	
										</div>
									</div>
								</article>
										
							<?php
								endforeach; 
								echo "</div>";
								wp_reset_postdata();
							?>
			
						<?php endif; ?>
						<div class="col-xs-12 archiveButton"><a href="<?php echo 'http://my.' . $_SERVER['HTTP_HOST'] . '/academy'; ?>" target="_blank"><?php the_sub_field('posts_button') ?></a></div>
					</div>
				</div>
			</section>
			
		<?php elseif(get_row_layout() == 'video_section'): ?>
			<section class="videos <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<h2 class="col-xs-12"><?php the_sub_field('section_title'); ?></h2>
						<?php if( get_sub_field('video_section_blurb') ): ?>
							<p class="col-xs-12"><?php the_sub_field('video_section_blurb'); ?></p>
						<?php endif; ?>
						<div class="row videos clear">
							<div class="col-lg-7 col-lg-offset-1 col-md-9 col-xs-12">							
								<div class="video featuredVideo">
									<iframe width="100%" src="//www.youtube.com/embed/<?php the_sub_field('featured_video'); ?>?version=3&autoplay=0&controls=0&color=white&theme=light" frameborder="0" allowfullscreen></iframe>
								</div>
							</div>
							<div class="col-md-3 col-xs-12">
								<div class="video">
									<iframe width="100%" src="//www.youtube.com/embed/<?php the_sub_field('side_video_one'); ?>?version=3&autoplay=0&controls=0&color=white&theme=light" frameborder="0" allowfullscreen></iframe>
								</div>
								<div class="video">
									<iframe width="100%" src="//www.youtube.com/embed/<?php the_sub_field('side_video_two'); ?>?version=3&autoplay=0&controls=0&color=white&theme=light" frameborder="0" allowfullscreen></iframe>
								</div>
							</div>
						</div>
						<div class="col-xs-12 archiveButton"><a href="<?php the_sub_field('youtube_link'); ?>" target="_blank"><?php the_sub_field('videos_button') ?></a></div>
					</div>
				</div>
			</section>	
		
		<?php elseif(get_row_layout() == 'pricing'): ?>
			<section class="pricing <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<header class="saltsha_cta">
					<div class="container">
						<div class="row">
							<div class="pricing_image col-md-6 col-xs-12">
								<?php 
									$image = get_field('cta_image', 'options');
									echo '<img class="icon responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
								?>
							</div>
							<div class="col-md-6 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-12">
								<h1><?php the_field('cta_title', 'options'); ?></h1>
								<?php if(have_rows('cta_features', 'options')): ?>
									<ul>
										<?php while (have_rows('cta_features', 'options')): the_row(); ?>
											<li><?php the_sub_field('feature', 'options'); ?></li>
										<?php endwhile; ?>
									</ul>
								<?php endif; ?>
								<?php if(!is_page('pricing')): ?>
									<a href="#"><?php the_field('cta_button_text', 'options'); ?></a>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</header>
				<div class="container">
					<div class="row">
						<?php 
							$monthlyPrice = get_sub_field('monthly_price');
							$yearlyPrice = get_sub_field('yearly_price');
						?>
						<div class="col-sm-6 col-xs-12">
							<h5>Monthly Pricing</h5>
							<h3><sup>$</sup><?php echo $monthlyPrice; ?></h3>
							<div class="col-sm-4 col-sm-offset-4 col-xs-10 col-xs-offset-1">
								<div class="signUpButton">
									<a href="<?php echo 'http://my.' . $_SERVER['HTTP_HOST'] . '/add-subscription.php?billing=monthly'; ?>"><?php the_sub_field('signup_button'); ?></a>
								</div>
							</div>
						</div>
						<div class="col-sm-6 col-xs-12">
							<h5>Yearly Pricing</h5>
							<h3><sup>$</sup><?php echo $yearlyPrice ?></h3>
							<?php $pct = round(100*($monthlyPrice*12/$yearlyPrice-1));
								  if ($pct >= 5) { ?>
							<aside>*Save &nbsp;<span><span><?php echo $pct; ?></span>%</span></aside>
							<?php } ?>
							<div class="col-sm-4 col-sm-offset-4 col-xs-10 col-xs-offset-1">
								<div class="signUpButton">
									<a href="<?php echo 'http://my.' . $_SERVER['HTTP_HOST'] . '/add-subscription.php?billing=yearly'; ?>"><?php the_sub_field('signup_button'); ?></a>
								</div>
							</div>
						</div>
					</div>
					<footer class="row">
						<aside class="col-xs-12"><?php the_sub_field('signup_disclaimer'); ?></aside>
					</footer>
				</div>
			</section>
		
		<?php elseif(get_row_layout() == 'icon_section'): ?>
			<section class="icons <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<h2><?php the_sub_field("section_title"); ?></h2>
						</div>
					</div>
					<?php if( have_rows('large_group') ): ?>
						<div class="row large_group<?php if(!get_sub_field('alt_layout')) echo ' alt_layout'; ?>">
							<?php while( have_rows('large_group') ): the_row(); ?> 
								<div class="iconGroup col-sm-4 col-xs-12">
									<a href="<?php the_sub_field("group_link"); ?>">
										<div class="large_icon_bk">
											<?php 
												$image = get_sub_field('group_icon');
												echo '<img class="icon responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
											?>
										</div>
									</a>
									<h3><?php the_sub_field('group_title'); ?></h3>
									<p><?php the_sub_field('group_content'); ?></p>
									<a href="<?php the_sub_field("group_link"); ?>">Learn More</a>
								</div>
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
					<?php if( have_rows('small_group') ): ?>
						<div class="row small_group">	
							<?php while( have_rows('small_group') ): the_row(); ?> 
								<!-- <a href="<?php the_sub_field("group_link"); ?>"> -->
									<div class="iconGroup col-md-3 col-sm-6 col-xs-12">
										<?php 
											$image = get_sub_field('group_icon');
											echo '<img class="icon responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
										?>	
										<h3><?php the_sub_field('group_title'); ?></h3>
										<p><?php the_sub_field('group_content'); ?></p>
									</div>
								<!-- </a> -->
							<?php endwhile; ?>
						</div>
					<?php endif; ?>
				</div>
			</section>
			
		<?php elseif(get_row_layout() == 'profiles_section'): ?>
			<section class="profiles <?php if(get_sub_field('background_color') != 'default') {the_sub_field('background_color');} ?>">
				<div class="container">
					<div class="row">
						<div class="col-xs-12">
							<h2><?php the_sub_field('section_title'); ?></h2>
						</div>
						<?php if( have_rows('profiles') ): $i = 0; ?>
							<?php while( have_rows('profiles') ): the_row(); ?>
								<a href="#" data-toggle="modal" data-target="#profile_<?php echo $i; ?>">
								<div class="profile col-sm-6 col-xs-12">
									<?php 
										$image = get_sub_field('profile_image');
										echo '<img class="profile_image responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
									?>
									<div>
										<h4><?php the_sub_field('profile_name'); ?></h4>
										<h3><?php the_sub_field('profile_title'); ?></h3>
									</div>
								</div>
								</a>
								<div class="profile_modal modal fade" id="profile_<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
									<div class="container">
										<div class="modal-content">
											<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span></span></button>
											<?php 
												$image = get_sub_field('profile_image');
												echo '<img class="profile_image responsive" src="' . $image['url'] . '" alt="' . $image['alt'] . '" title="' . $image['title'] . '" />'
											?>
											<div>
												<h4><?php the_sub_field('profile_name'); ?></h4>
												<h3><?php the_sub_field('profile_title'); ?></h3>
												<ul>
													<?php if( have_rows('list_items') ): ?>
														<?php while( have_rows('list_items') ): the_row(); ?>
															<li><?php the_sub_field('list_item'); ?></li>
														<?php endwhile; ?>
													<?php endif; ?>
												</ul>
											</div>
										</div>
									</div>
								</div>
							<?php $i++; endwhile; ?>
						<?php endif; ?>
					</div>
				</div>
			</section>
			
		<?php endif; ?>
	<?php endwhile; ?>		
<?php get_footer(); ?>