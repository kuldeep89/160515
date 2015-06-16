<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
if ( is_user_logged_in() ): 
 
	$user_ID = get_current_user_id();
	$company_select = get_the_author_meta( 'company_select', $user_ID );
	$company = company_select_data($company_select);
else:
	$company = company_get();
endif;
?>
				
			</div>
		<!-- END PAGE -->
	</div>
<!-- END CONTAINER -->
<div class="footer">
  <div class="copyright">
	 2014 - <?php echo date('Y'); ?> © Saltsha | A product of <a target="_blank" href="<?php echo $company['link']; ?>" style="color:rgb(77,145,255); "><?php echo $company['name']; ?></a> | <a target="_blank" href="https://saltsha.com/privacy-policy/" style="color:rgb(77,145,255); ">Privacy Policy</a> | <a target="_blank" href="https://saltsha.com/terms-conditions/" style="color:rgb(77,145,255); ">Terms & Conditions</a>
  </div>
</div>
<!-- BEGIN FOOTER -->
<?php //include 'signin.php'; ?>  
  
<?php wp_footer(); // Required ?>

<?php 
$serverList = array('localhost', '127.0.0.1');
if(!in_array($_SERVER['REMOTE_ADDR'], $serverList)): ?>
    <script src="<?php echo get_template_directory_uri() ?>/js/build/production.min.js" async></script>
<?php else: ?>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery-ui/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>      
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script> 
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/breakpoints/breakpoints.js" type="text/javascript"></script>  
   <!-- IMPORTANT! jquery.slimscroll.min.js depends on jquery-ui-1.10.1.custom.min.js -->
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery.blockui.js" type="text/javascript"></script>  
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery.cookie.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script> 
   <!-- END CORE PLUGINS -->
   
   <!-- BEGIN PAGE LEVEL PLUGINS -->
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/jquery.vmap.js" type="text/javascript"></script>   
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js" type="text/javascript"></script>  
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap-daterangepicker/date.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/bootstrap-daterangepicker/daterangepicker.js" type="text/javascript"></script>     
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/gritter/js/jquery.gritter.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/jquery.sparkline.min.js" type="text/javascript"></script>  
   <!-- END PAGE LEVEL PLUGINS -->
   
   <!-- BEGIN PAGE LEVEL SCRIPTS -->
   <script src="<?php echo get_template_directory_uri() ?>/assets/scripts/app.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/scripts/index.js" type="text/javascript"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/progress-circle.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/plugins.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/main.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/carhartl-jquery-cookie-3caf209/jquery.cookie.js" ></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/jquery-validate.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/signon.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/share42/share42.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/blog-rotator.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/ajax-resources.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/js/fancy-product-designer.js"></script>


<?php endif; ?>

<!--[if lt IE 9]>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/excanvas.js"></script>
   <script src="<?php echo get_template_directory_uri() ?>/assets/plugins/respond.js"></script>  
<![endif]--> 
<!-- CrazyEgg Script -->
<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0025/3852.js?"+Math.floor(new Date().getTime()/3600000);
a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>
</body>
</html>		            