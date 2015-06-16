<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
  <meta charset="utf-8" />
  <title><?php echo (isset($browser_title) && !empty($browser_title))? $browser_title:'Saltsha'; ?></title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport" />
  <meta content="" name="description" />
  <meta content="" name="author" />
  <link rel="icon" type="image/png" href="<?php echo base_url() ?>assets/img/mbp-favicon.png">
  <link rel="icon" type="image/x-icon" href="<?php echo base_url() ?>assets/img/mbp-favicon.ico">
  <!-- BEGIN GLOBAL MANDATORY STYLES -->
  
  <link href="<?php echo base_url() ?>assets/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/plugins/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/css/style-metro.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/css/style.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
  <link href="<?php echo base_url() ?>assets/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
  <link href="<?php echo base_url() ?>assets/css/style-responsive.css" rel="stylesheet" type="text/css"/>

  <!-- END GLOBAL MANDATORY STYLES -->
  <!-- BEGIN PAGE LEVEL STYLES -->
  
  <?php
	  echo page_level_styles($this->uri->segment(1));
  ?>
  <!-- END PAGE LEVEL STYLES -->
  <?php
  
	  ////////////////
	  // Check for Page Styles
	  ////////////////
	  if( isset($arr_css) && count($arr_css) > 0 ) {
		  foreach( $arr_css as $css ) {
			  echo '<link href="'.base_url().'assets/'.$css.'" rel="stylesheet" type="text/css" />';
		  }
	  }
	  
  ?>
  
  <link rel="shortcut icon" href="<?php echo base_url() ?>assets/favicon.ico" />
  
</head>
<!-- END HEAD -->



<body class="fixed-top">
	
	<?php
		$this->load->view('backend/includes/topnav');
	?>

   <!-- BEGIN CONTAINER -->
   <div class="page-container">

	   <?php
		   $this->load->view('backend/includes/nav');
	   ?>
	   
      <!-- BEGIN PAGE -->
<!-- -----------------Placing the Dashboard customization wheel only on Dashboard Isaiah Arnold --------------- -->
      <?php
if ($this->router->fetch_class() == "dashboard" || $this->router->fetch_class() == "") {
?>
	<!-- <button id="change-dashboard" class="btn blue" onclick="dashboard.toggleEdit()"><i class="icon-edit"></i> Edit</button>
	<button id="cancel-dashboard" class="btn" onclick="dashboard.cancelChanges()"><i class="icon-undo"></i> Cancel</button> -->
   <?php
}
?>                
      <div class="page-content">
         <!-- BEGIN PAGE CONTAINER-->
         <div class="container-fluid">
            <!-- BEGIN PAGE HEADER-->
            <div class="row-fluid">
               <div class="span12"> 
                  <!-- BEGIN PAGE TITLE & BREADCRUMB-->			
			<h3 class="page-title">
				<?php if ($this->router->class == 'faq') {
						$title = strtoupper($this->router->class);
					}else if(strtolower($this->router->class) == strtolower('Myaccount')) {
						$title = 'My Account';
					} else {
						$title = $this->router->class;
					}
					
					
					echo ucwords(str_replace('_', ' ', $title)); ?> 
			<small><?php echo $this->lang->line($this->router->class); ?></small>
			
			</h3>
			
			<ul class="breadcrumb">
				<li>
					<i class="icon-home"></i>
						<a href="<?php echo base_url() ?>index.php/dashboard">Home</a> 				
				</li>
				<li>
					<?php
						
						if (isset($breadcrumbs) && count($breadcrumbs) > 0) {
							foreach ($breadcrumbs as $cur_breadcrumb) {
								echo '<i class="icon-angle-right"></i> <a href="'.$cur_breadcrumb['url'].'">'.$cur_breadcrumb['title'].'</a>';
							}
						}
						else {
							
							echo '<i class="icon-angle-right"></i><a href="'.site_url($this->router->class).'">'.ucwords(str_replace('_', ' ', $this->router->class)).'</a>';
							echo '<i class="icon-angle-right"></i><a href="'.site_url($this->router->class.'/'.$this->router->method).'">'.ucwords(str_replace('_', ' ', $this->router->method)).'</a>';
							
						}
						
					?>
				</li>	
				<li class="pull-right no-text-shadow">
					<div id="dashboard-report-range" class="dashboard-date-range tooltips no-tooltip-on-touch-device responsive" data-tablet="" data-desktop="tooltips" data-placement="top" data-original-title="Change dashboard date range">
						<i class="icon-calendar"></i>
						<span></span>
						<i class="icon-angle-down"></i>
					</div>
				</li>
				<li class="pull-right no-text-shadow">
					<div id="dashboard-report-range" class="dashboard-date-range tooltips no-tooltip-on-touch-device responsive" data-tablet="" data-desktop="tooltips" data-placement="top" data-original-title="Change dashboard date range">
						<i class="icon-calendar"></i>
						<span></span>
						<i class="icon-angle-down"></i>
					</div>
				</li>
			</ul>
		<!-- END PAGE TITLE & BREADCRUMB-->
               </div>
            </div>
            
            <!-- END PAGE HEADER-->
        	<div class="row-fluid" id="notifications" style="min-height: 0px; padding-left: 0px !important">
        		<div class="span12" style="min-height: 0px !important">
	            <?php
	            	$this->notification_lib->print_notifications();
	            ?>
        		</div>
        	</div>
