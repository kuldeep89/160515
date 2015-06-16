<?php
	////////////////
	// Include CSS Files
	////////////////
	$arr_css[]	= 'plugins/gritter/css/jquery.gritter.css';
	$arr_css[]	= 'css/pages/news.css';
	$arr_css[]	= 'css/dashboard.css';
	$arr_css[]	= 'css/stock_quote.css';
	$arr_css[]	= 'plugins/glyphicons/css/glyphicons.css';
	$arr_css[]	= 'plugins/bootstrap-daterangepicker/daterangepicker.css';
	$arr_css[]	= 'plugins/fullcalendar/fullcalendar/fullcalendar.css';
	$arr_css[]	= 'plugins/jqvmap/jqvmap/jqvmap.css';
	$arr_css[]	= 'plugins/jquery-easy-pie-chart/jquery.easy-pie-chart.css';

	////////////////
	// Include JS Files
	////////////////
	$arr_js[]	= 'plugins/flot/jquery.flot.js';
	$arr_js[]	= 'plugins/flot/jquery.flot.resize.js';
	$arr_js[]	= 'plugins/jquery.pulsate.min.js';
	$arr_js[]	= 'plugins/bootstrap-daterangepicker/date.js';
	$arr_js[]	= 'plugins/bootstrap-daterangepicker/daterangepicker.js';
	$arr_js[]	= 'scripts/dashboard.js';

	$arr_footer = array('arr_js' => $arr_js);
	$arr_header = array('arr_css' => $arr_css);
	
	$this->load->view('backend/includes/header', $arr_header);
?>

			
			<div id="add-widget-form" class="modal hide" aria-hidden="true" style="display: none;">
				<div class="modal-header">
					<button data-dismiss="modal" class="close" type="button"></button>
					<h3>Add Widget</h3>
				</div>
				<input type="hidden" id="widget_db_id" />
				<input type="hidden" id="widget_column" />
				<input type="hidden" id="widget_row" />
				<input type="hidden" id="widget_type" class="widget-data" />
				<div class="modal-body"></div>
			</div>
			<div id="remove-widget-form" class="modal hide" aria-hidden="true" style="display: none;">
				<div class="modal-header">
					<button data-dismiss="modal" class="close" type="button"></button>
					<h3>Remove Widget</h3>
				</div>
				<input type="hidden" id="remove_widget_id" />
				<div class="modal-body">
					Are you sure you want to remove this widget? <strong>This cannot be undone.</strong><br/>
					<br/>
					<button class="btn" onclick="$('#remove_widget_id').val('');$('#remove-widget-form').modal('hide');" style="display: inline-block; margin-right: 10px;"><i class="icon-undo"></i> Cancel</button>
					<button class="btn red" onclick="dashboard.removeWidget()"><i class="icon-remove"></i> Remove</button>
				</div>
			</div>
            <div id="dashboard">
               <div class="clearfix"></div>
               <div class="row-fluid">
                 <div class="span8">
                 
                 	We are glad to have you here! Saltsha resource portal to help YOU succeed. Our goal is to provide you with all the relevant information and tools necessary for you to have incredible growth with your business. If you have trouble navigating the portal or finding the necessary information, please do not hesitate to call us at <a href="tel:8003602591">800.360.2591</a> or email <a href="mailto:support@saltsha.com" target="_blank">support@saltsha.com</a>.<br/><br/>

                     <!-- BEGIN PORTLET-->
                     <div class="portlet solid bordered light-grey" style="margin-bottom: 30px;">
                        <div class="portlet-title">
                           <div class="caption"><i class="icon-bar-chart"></i>Site Visits
                           </div>
                           <div class="tools">
		                        <!-- BEGIN Date Range FORM-->
		                        <form action="#" class="form-horizontal" style="display: inline-block; float: right;">
		                           <div class="control-group">
		                              <label class="control-label">Select Date Range:</label>
		                              <div class="controls">
		                                 <div class="input-prepend">
		                                    <span class="add-on"><i class="icon-calendar"></i></span><input type="text" class="m-wrap m-ctrl-medium date-range" id="daterange"/>
		                                 </div>
		                              </div>
		                           </div>
		                         </form>
		                        <!-- END Date Range FORM-->
                           </div>
                        </div>
                        <div style="height: 300px;">
                           <div id="site_statistics_loading" style="text-align: center; padding-top: 100px;">
                              <em>Loading</em><br/><img src="<?php echo base_url() ?>assets/img/loading.gif" alt="loading" />
                           </div>
                            <?php 
					 
						 		$type	= ($this->current_user->get('account') == 2)? 'member':'moderator';
						 
						 	?>
                           <div id="site_statistics_content" class="hide">
	                           <div id="enter-ga-code" class="alert alert-error hide" style="text-align: center;"><h2>Sorry!</h2>You do not have access to Google Analytics for your website<br/><a href="<?php echo site_url('users/'.$type.'/'.$this->current_user->get('id')); ?>">Click here</a> to set your Google Analytics code or <a href="http://www.google.com/analytics/" target="_blank">click here</a> to find out what it is.</div>
	                           <div id="not-vaid" class="alert alert-error hide" style="text-align: center;"><h2>Sorry!</h2>The Google Analytics code you entered is not valid.<br/><a href="">Click here</a> to enter a valid code.</div>
                              <div id="site_statistics" class="chart"></div>
                           </div>
                        </div>
                     </div>
                     <!-- END PORTLET-->
                 <div class="row-fluid">
                 <div class="span6 ui-portlet-widget widget_column" id="column_1">
                 		<?php
	                 		// Echo widgets
	                 		if (isset($widgets[1]) && count($widgets[1]) > 0) {
		                 		foreach ($widgets[1] as $cur_widget) {
			                 		$widget_view = $this->dashboard_model->get_widget_view($cur_widget['widget_type'])->result_array();
			                 		$this->load->view($widget_view[0]['widget_display_url'], $cur_widget);
		                 		}
		                 	}

	                 		// Echo widget droppables
	                 		for ($i=1; $i<($max_rows+1); $i++) {
		                 		echo '<div class="droppable" column="1" row="'.$i.'"><div class="add-widget">+</div></div>';
	                 		}
                 		?>
				 </div>
               	 <div class="span6 ui-portlet-widget widget_column" id="column_2">
                 		<?php
	                 		// Echo widgets
	                 		if (isset($widgets[2]) && count($widgets[2]) > 0) {
		                 		foreach ($widgets[2] as $cur_widget) {
			                 		$widget_view = $this->dashboard_model->get_widget_view($cur_widget['widget_type'])->result_array();
			                 		$this->load->view($widget_view[0]['widget_display_url'], $cur_widget);
		                 		}
		                 	}

	                 		// Echo widget droppables
	                 		for ($i=1; $i<($max_rows+1); $i++) {
		                 		echo '<div class="droppable" column="2" row="'.$i.'"><div class="add-widget">+</div></div>';
	                 		}
                 		?>
				  </div>
                  </div>
                 </div>
                  <div class="span4 ui-portlet-widget">
                  <div class="span12">				  
					  <div class="top-news">
                  		<a href="" class="btn purple">
					 	<span>Social Media Links</span>
					 	<em>Saltsha Social Media Links</em>
					 	<i class="icon-bullhorn top-news-icon"></i></a>
					 </div>
					 <div id="social-buttons">
					  <ul class="social-icons">
	                 	<li><a href="https://www.facebook.com/Saltsha" target="_blank" data-original-title="Facebook" class="facebook"></a></li>
	                 	<li><a href="https://twitter.com/saltsha" target="_blank>" data-original-title="Twitter" class="twitter"></a></li>
	                 	  <li><a href="https://plus.google.com/105387137872967257002" target="_blank>" data-original-title="Goole Plus" class="googleplus"></a></li>
                 	</ul>
					 </div>
                 	<!-- Social Media Buttons -->
					<!--HubSpot Call-to-Action Code -->
					<span class="hs-cta-wrapper" id="hs-cta-wrapper-88da89a8-59b3-446d-8413-726e68f127e7">
					    <span class="hs-cta-node hs-cta-88da89a8-59b3-446d-8413-726e68f127e7" id="hs-cta-88da89a8-59b3-446d-8413-726e68f127e7">
					        <!--[if lte IE 8]><div id="hs-cta-ie-element"></div><![endif]-->
					        <a href="http://cta-redirect.hubspot.com/cta/redirect/212386/88da89a8-59b3-446d-8413-726e68f127e7"><img class="hs-cta-img" id="hs-cta-img-88da89a8-59b3-446d-8413-726e68f127e7" style="border-width:0px;" src="http://no-cache.hubspot.com/cta/default/212386/88da89a8-59b3-446d-8413-726e68f127e7.png" /></a>
					    </span>
					    <script type="text/javascript">
					        (function(){
					            var s='hubspotutk',r,c=((r=new RegExp('(^|; )'+s+'=([^;]*)').exec(document.cookie))?r[2]:''),w=window;w[s]=w[s]||c,
					                hsjs = document.createElement("script"), el=document.getElementById("hs-cta-88da89a8-59b3-446d-8413-726e68f127e7");
					            hsjs.type = "text/javascript";hsjs.async = true;
					            hsjs.src = "//cta-service-cms2.hubspot.com/cs/loader-v2.js?pg=88da89a8-59b3-446d-8413-726e68f127e7&pid=212386&hsutk=" + encodeURIComponent(c);
					            (document.getElementsByTagName("head")[0]||document.getElementsByTagName("body")[0]).appendChild(hsjs);
					            try{el.style.visibility="hidden";}catch(err){}
					            setTimeout(function() {try{el.style.visibility="visible";}catch(err){}}, 2500);
					        })();
					    </script>
					</span>
				  <div id="column_3" class="widget_column">
					<!-- end HubSpot Call-to-Action Code -->
                 		<?php
	                 		// Echo widgets
	                 		if (isset($widgets[3]) && count($widgets[3]) > 0) {
		                 		foreach ($widgets[3] as $cur_widget) {
			                 		$widget_view = $this->dashboard_model->get_widget_view($cur_widget['widget_type'])->result_array();
			                 		$this->load->view($widget_view[0]['widget_display_url'], $cur_widget);
		                 		}
		                 	}

	                 		// Echo widget droppables
	                 		for ($i=1; $i<($max_rows+1); $i++) {
		                 		echo '<div class="droppable" column="3" row="'.$i.'"><div class="add-widget">+</div></div>';
	                 		}
                 		?>
                  </div>
               </div>
            </div>
         </div>
         <!-- END PAGE CONTAINER-->
      </div>
      <!-- END PAGE -->
      <!-- Load all Google JS libraries -->
  <script src="https://apis.google.com/js/client.js"></script>
  <?php
  	$obj_user	= $this->current_user;
  ?>
  <script type="text/javascript">
  	// Google Analytics access stuff
	var CLIENT_ID = '664997135128.apps.googleusercontent.com';
	var TABLE_ID  = 'ga:<?php echo $obj_user->get('google_id'); ?>';

	// Do this when page loads
	window.onload = function() {
		// Load Google Analytics data
		dashboard.onLoad();
	}
  </script>
<?php $this->load->view('backend/includes/footer', $arr_footer); ?>