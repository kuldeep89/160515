<?php
	
	/**
	* Account Listing View
	* Author: Thomas Melvin
	* Date: 19 August 2013
	* Notes:
	* This method prints out existing users to the screen.
	*
	*/	
	////////////////
	// Build JS Includes
	////////////////
	$arr_js[]	= 'scripts/accounts_listing.js';
	$arr_js[]	= 'scripts/index.js';
	$arr_js[]	= 'plugins/select2/select2.min.js';
	$arr_js[]	= 'plugins/data-tables/jquery.dataTables.js';
	$arr_js[]	= 'plugins/data-tables/DT_bootstrap.js';
	
	////////////////
	// Footer Array
	////////////////
	$arr_footer['arr_js']	= $arr_js;
	
	$this->load->view('backend/includes/header');
	
?>

	<div class="row-fluid">
        <div class="span12">

            <div class="row-fluid">
                <div class="span12">
                
                	<!-- BEGIN EXAMPLE TABLE PORTLET-->
	                  <div class="portlet box light-grey">
	                     <div class="portlet-title">
	                        <div class="caption"><i class="icon-globe"></i>Accounts Listing</div>
	                        <div class="tools">
	                           <a href="javascript:;" class="collapse"></a>
	                           <a href="#portlet-config" data-toggle="modal" class="config"></a>
	                           <a href="javascript:;" class="reload"></a>
	                           <a href="javascript:;" class="remove"></a>
	                        </div>
	                     </div>
	                     <div class="portlet-body">
	                        <div class="clearfix">
	                        	<div class="btn-group">
	                              <a href="<?php echo site_url('accounts/add-account'); ?>" id="sample_editable_1_new" class="btn green">
	                              Add New Account <i class="icon-plus"></i>
	                              </a>
	                           </div>
	                           <div class="btn-group pull-right">
	                              <button class="btn dropdown-toggle" data-toggle="dropdown">Tools <i class="icon-angle-down"></i>
	                              </button>
	                              <ul class="dropdown-menu pull-right">
	                                 <li><a href="#">Print</a></li>
	                                 <li><a href="#">Save as PDF</a></li>
	                                 <li><a href="#">Export to Excel</a></li>
	                              </ul>
	                           </div>
	                        </div>

							<?php $this->load->view('backend/object-templates/accounts/accounts-listing'); ?>
								
						</div>
					</div>

                </div>
            </div>
            
        </div><!-- END PAGE CONTAINER-->
    </div><!-- END PAGE -->

<?php

	$this->load->view('backend/includes/footer', $arr_footer);