<?php
	
	/**
	* Add Account
	* Author: Thomas Melvin
	* Date: 19 August 2013
	* Notes:
	* This view presents a form to add an account to the system.
	*
	*/	
	
	$arr_css[]	= 'plugins/select2/select2_metro.css';
	$arr_js[]	= 'plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js';	
	$arr_js[]	= 'scripts/accounts_add_account.js';
	
	$arr_header['arr_css']	= $arr_css;
	$arr_footer['arr_js']	= $arr_js;
	
	$this->load->view('backend/includes/header', $arr_header);
	
?>

<div class="row-fluid">
               <div class="span12">
                  <div class="portlet box blue" id="account-setup">
                     <div class="portlet-title">
                        <div class="caption">
                           <i class="icon-reorder"></i> Account Setup
                        </div>
                        <div class="tools hidden-phone">
                           <a href="javascript:;" class="collapse"></a>
                           <a href="#portlet-config" data-toggle="modal" class="config"></a>
                           <a href="javascript:;" class="reload"></a>
                           <a href="javascript:;" class="remove"></a>
                        </div>
                     </div>
                     <div class="portlet-body form">
                        <form action="#" class="form-horizontal">
                           <div class="form-wizard">
                              <div class="navbar steps">
                                 <div class="navbar-inner">
                                    <ul class="row-fluid">
                                       <li class="span4">
                                          <a href="#tab1" data-toggle="tab" class="step active">
                                          <span class="number">1</span>
                                          <span class="desc"><i class="icon-ok"></i> Account Setup</span>   
                                          </a>
                                       </li>
                                       <li class="span4">
                                          <a href="#tab2" data-toggle="tab" class="step">
                                          <span class="number">2</span>
                                          <span class="desc"><i class="icon-ok"></i> Profile Setup</span>   
                                          </a>
                                       </li>
                                       <li class="span4">
                                          <a href="#tab3" data-toggle="tab" class="step">
                                          <span class="number">3</span>
                                          <span class="desc"><i class="icon-ok"></i> Confirm</span>   
                                          </a> 
                                       </li>
                                    </ul>
                                 </div>
                              </div>
                              <div id="bar" class="progress progress-success progress-striped">
                                 <div class="bar"></div>
                              </div>
                              <div class="tab-content">
                                 <div class="tab-pane active" id="tab1">
                                    <h3 class="block">Account Details</h3>
                                    <div class="control-group">
                                       <label class="control-label">Company Name</label>
                                       <div class="controls">
                                          <input id="company" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">This is the account name.</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Account Type</label>
                                       <div class="controls">
                                          <select id="type">
                                          	<option value="0">Tier 1 (non-pay)</option>
                                          	<option value="1">Tier 1</option>
                                          	<option value="2">Tier 2</option>
                                          	<option value="3">Tier 3</option>
                                          	<option value="4">Tier 4</option>
                                          </select>

                                          <span class="help-inline">The account tier level.</span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="tab-pane" id="tab2">
                                    <h3 class="block">Account Owner</h3>
                                    <div class="control-group">
                                       <label class="control-label">First Name</label>
                                       <div class="controls">
                                          <input id="first_name" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Account owner's first name</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Last Name</label>
                                       <div class="controls">
                                          <input id="last_name" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Account owner's last name</span>
                                       </div>
                                    </div>
                                     <div class="control-group">
                                       <label class="control-label">Email</label>
                                       <div class="controls">
                                          <input id="email" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Account owner's e-mail address (and their login)</span>
                                       </div>
                                    </div>
                                     <div class="control-group">
                                       <label class="control-label">Password</label>
                                       <div class="controls">
                                          <input id="password" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Account owner's password</span>
                                       </div>
                                    </div>
                                     <div class="control-group">
                                       <label class="control-label">Confirm Password</label>
                                       <div class="controls">
                                          <input id="confirm_password" type="text" class="span6 m-wrap" />
                                          <span class="help-inline"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Contact Number</label>
                                       <div class="controls">
                                          <input id="phone" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Account owner's contact number</span>
                                       </div>
                                    </div>
                                   
                                    <div class="control-group">
                                       <label class="control-label">Address 1</label>
                                       <div class="controls">
                                          <input id="address_1" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Street address</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Address 2</label>
                                       <div class="controls">
                                          <input id="address_2" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">P.O. box number, etc.</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Zip</label>
                                       <div class="controls">
                                          <input id="zip" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Zip code of account owner</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">City</label>
                                       <div class="controls">
                                          <input id="city" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">City of account owner</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">State</label>
                                       <div class="controls">
                                          <input id="state" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">State of account owner</span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Google ID</label>
                                       <div class="controls">
                                          <input id="google_id" type="text" class="span6 m-wrap" />
                                          <span class="help-inline">Google analytics ID</span>
                                       </div>
                                    </div>
                                 </div>
                                
                                 <div class="tab-pane" id="tab3">
                                    <h3 class="block">Confirm Account Information</h3>
                                    <div class="control-group">
                                       <label class="control-label">Organizations Name:</label>
                                       <div class="controls">
                                          <span id="confirm_company" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Account Type:</label>
                                       <div class="controls">
                                          <span id="confirm_type" class="text"></span>
                                       </div>
                                    </div>
                                    <h3 class="block">Confirm Account Owner Information</h3>
                                    <div class="control-group">
                                       <label class="control-label">First Name:</label>
                                       <div class="controls">
                                          <span id="confirm_first_name" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Last Name:</label>
                                       <div class="controls">
                                          <span id="confirm_last_name" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Email:</label>
                                       <div class="controls">
                                          <span id="confirm_email" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Contact Number:</label>
                                       <div class="controls">
                                          <span id="confirm_phone" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Address 1:</label>
                                       <div class="controls">
                                          <span id="confirm_address_1" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Address 2:</label>
                                       <div class="controls">
                                          <span id="confirm_address_2" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Zip:</label>
                                       <div class="controls">
                                          <span id="confirm_zip" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">City:</label>
                                       <div class="controls">
                                          <span id="confirm_city" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">State:</label>
                                       <div class="controls">
                                          <span id="confirm_state" class="text"></span>
                                       </div>
                                    </div>
                                    <div class="control-group">
                                       <label class="control-label">Google ID:</label>
                                       <div class="controls">
                                          <span id="confirm_google_id" class="text"></span>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                              <div class="form-actions clearfix">
                                 <a href="javascript:;" class="btn button-previous">
                                 <i class="m-icon-swapleft"></i> Back 
                                 </a>
                                 <a href="javascript:;" class="btn blue button-next">
                                 Continue <i class="m-icon-swapright m-icon-white"></i>
                                 </a>
                                 <a href="javascript:;" class="btn green button-submit">
                                 Submit <i class="m-icon-swapright m-icon-white"></i>
                                 </a>
                              </div>
                           </div>
                        </form>
                     </div>
                  </div>
               </div>
            </div>



<?php

	$this->load->view('backend/includes/footer', $arr_footer);