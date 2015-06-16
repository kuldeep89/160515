

 	  </div>

	</div>
   <!-- END CONTAINER -->

  <!-- BEGIN COPYRIGHT -->
	  <div class="copyright">
	    <?php echo date('Y') ?> &copy; Saltsha | Powered by <a href="http://www.paypromedia.com/" style="color:rgb(77,145,255); ">PayProMedia</a>
	  </div>
  <!-- END COPYRIGHT -->
  
  
  <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
  
	  <!-- BEGIN CORE PLUGINS -->
	  	<script src="<?php echo base_url() ?>assets/plugins/jquery-1.8.3.min.js" type="text/javascript"></script>
	  	
	  <!-- IMPORTANT! Load jquery-ui-1.10.1.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
	  	<script src="<?php echo base_url() ?>assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
	  	<script src="<?php echo base_url() ?>assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
	  	
	  <!--[if lt IE 9]>
	  	<script src="<?php echo base_url() ?>assets/plugins/excanvas.js"></script>
	  	<script src="<?php echo base_url() ?>assets/plugins/respond.js"></script>
	  <![endif]-->

	  <script src="<?php echo base_url() ?>assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>

	  <!-- IMPORTANT! jquery.slimscroll.min.js depends on jquery-ui-1.10.1.custom.min.js -->
	  <script src="<?php echo base_url() ?>assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
	  <script src="<?php echo base_url() ?>assets/plugins/jquery.blockui.js" type="text/javascript"></script>
	  <script src="<?php echo base_url() ?>assets/plugins/jquery.cookie.js" type="text/javascript"></script>
	  <script src="<?php echo base_url() ?>assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript" ></script>
	  <script src="<?php echo base_url() ?>assets/scripts/notification.js" type="text/javascript" ></script>
	  <script src="<?php echo base_url() ?>assets/scripts/stock_quote.js" type="text/javascript" ></script>

	  <!-- RSS FEED uses google API Key-->
	  <script type="text/javascript" src="http://www.google.com/jsapi?key=AIzaSyCXUJ9Rr6sR7ljPXD1qv-aVed7dpYjlrSo"></script>
	  <script type="text/javascript">
		google.load("feeds", "1") //Load Google Ajax Feed API (version 1)
	  </script>

		  <!-- Livechat script -->
		  		
			  <script type="text/javascript">
				var __lc = {};
				__lc.license = 2699592;
			
				(function() {
				var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
				lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
				var s = document.getElementsByTagName('script')[2]; s.parentNode.insertBefore(lc, s);
				})();
			  </script>
			  
		  <!-- End Livechat Script -->
	  <!-- END CORE PLUGINS -->

	  <!-- BEGIN PAGE LEVEL SCRIPTS -->
	  <script src="<?php echo base_url() ?>assets/scripts/app.js" type="text/javascript"></script>
		<?php
	
			////////////////
			// Generate JS.
			////////////////
			if( isset($arr_js_views) && is_array($arr_js_views) && count($arr_js_views) > 0 ) {
	
				////////////////
				// Load the View (Data is passed from the controller's view call, directly to this view.
				////////////////
				foreach( $arr_js_views as $js_view ) {
					$this->load->view($js_view);
				}
	
			}
	
		?>
		<!-- END PAGE LEVEL SCRIPTS -->
	  	<?php
	
	  		if( isset($arr_js) && count($arr_js) > 0 ) {
	
		  		foreach( $arr_js as $js ) {
			  		echo '<script src="'.base_url().'assets/'.$js.'" type="text/javascript"> </script>';
		  		}
	
	  		}
	
	  	?>
	  	<script type="text/javascript">
		  	jQuery(document).ready(function() {
				App.init();
			});
	  	</script>
	  	
	  	<script>var _gaq=[['_setAccount', 'UA-46649787-1'],['_setDomainName', 'saltsha.com'],['_trackPageview']];(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.src='//www.google-analytics.com/ga.js';s.parentNode.insertBefore(g,s)}(document,'script'))</script>
  <!-- END JAVASCRIPTS -->
</body>
<?php

	////////////////
	// Set the Referrer Page (ALSO FIND A BETTER SPOT FOR THIS)
	////////////////
	$this->session->set_flashdata('referrer', $_SERVER['REQUEST_URI']);

?>
<!-- END BODY -->
</html>