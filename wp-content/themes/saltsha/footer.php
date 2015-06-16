				</section>
				
				<footer>
				
					<?php do_action('foundationPress_before_footer'); ?>
					
					<div class="footer-top">
						<div class="menu-list">
							<?php wp_nav_menu( array('menu' => 'Footer menu' )); ?>
						</div>
						<div class="social">
							<?php wp_nav_menu( array('menu' => 'Social list' )); ?>
						</div>
						<div class="newsletter_signup">
							<span>Join the Newsletter</span>
							<?php //echo do_shortcode('[contact-form-7 id="4" title="Join form"]'); ?>
							<form id="signup_form">
								<input type="email" id="signup_email" name="signup_email" />
								<input type="submit" value="Sign Up" id="signUpButton" />
							</form>
							<div id="response"></div>
						</div>
					</div>
					<div class="footer-bottom">
						<p class="left">Powered by <a href="http://www.payprotec.com" target="_blank">PayProTec</a>.</p>
						<p class="right">Copyright &copy; <?php date_default_timezone_set('America/Indiana/Indianapolis'); $start_year = 2014; if($start_year == date("Y")) {echo $start_year;}else{echo $start_year."-".date("Y");} ?> <strong><?php echo esc_attr( get_bloginfo('name', 'display') ); ?></strong></p>
					</div>
					
					<?php do_action('foundationPress_after_footer'); ?>
					
				</footer>
				
				<a class="exit-off-canvas"></a>
					
				<?php do_action('foundationPress_layout_end'); ?>
			
			</div>
		</div>
		
		
		<div id="infoModal" class="reveal-modal small">
			<h4>Tell me more about Saltsha!</h4>
			<p id="contact_response"></p>
			<form id="contact_form">
				<label>
					Name
					<input type="text" id="contact_name" name="contact_name" />
				</label>
				<label>
					Email
					<input type="email" id="contact_email" name="contact_email" />
				</label>
				<label>
					Phone
					<input type="text" id="contact_phone" name="contact_phone" />
				</label>
				<input type="submit" value="Send" id="submit_contact_form" class="btn green-btn expand" />
				<span>Someone will reach out to you soon to give you more information on Saltsha.</span>
			</form>
			<a class="close-reveal-modal">&#215;</a>
		</div>
		
		<div id="upgradeModal" class="reveal-modal small">
			<h4>Upgrade now!</h4>
			<p id="upgrade_response"></p>
			<form id="upgrade_form">
				<label>
					Name
					<input type="text" id="upgrade_name" name="upgrade_name" required />
				</label>
				<label>
					Email
					<input type="email" id="upgrade_email" name="upgrade_email" required />
				</label>
				<label>
					Phone
					<input type="text" id="upgrade_phone" name="upgrade_phone" />
				</label>
				<label>
					MID
					<input type="text" id="upgrade_mid" name="upgrade_mid" required />
				</label>
				<input type="submit" value="Send" id="submit_upgrade_form" class="btn green-btn expand" />
				<span>Someone will reach out to you after you click send.</span>
			</form>
			<a class="close-reveal-modal">&#215;</a>
		</div>
		
		<?php wp_footer(); ?>	
		<?php do_action('foundationPress_before_closing_body'); ?>
		
		    <script src="<?php echo get_template_directory_uri() ?>/share42/share42.js"></script>
		<!-- CrazyEgg -->
		<script type="text/javascript">
		setTimeout(function(){var a=document.createElement("script");
		var b=document.getElementsByTagName("script")[0];
		a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0025/3852.js?"+Math.floor(new Date().getTime()/3600000);
		a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
		</script>
	</body>
</html>