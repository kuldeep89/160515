<link href="<?php echo get_template_directory_uri() ?>/css/dashboard.css" rel="stylesheet" type="text/css" />
<?php

if(isset($_GET['mid'])){
	$MID = $_GET['mid'];
} else {
	$MID = '';
}

if(isset($_GET['comp']) && $_GET['comp']==='phs'){
	$company_select_name = 'Pilothouse';
} else {
	$company_select_name = 'PayProTec';
}

	$company = company_get();

?>
<?php if (!(current_user_can('level_0'))): ?>   <!--  Check if logged in -->
	
	<div id="myModal" class="modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
		<div class="modal-header">
			<h3 id="myModalLabel">Welcome To Saltsha, <small>a <?php echo $company['name']; ?> product.</small></h3>
		</div>
		
		<div class="modal-body">
			<div class="row-fluid">	
			
				<div id="login" class="span12" >	
					<form id="login-form" action="/" method="post">
						<!-- <h3 class="form-title">Login to your account</h3> -->
						<div class="success-messages "></div>
						<div class="error-messages"></div>
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Merchant ID, Username, or Email</label>
							<div class="input-icon">
								<i class="fa fa-user"></i>
								<input class="form-control placeholder-no-fix login-input" type="text" name="log" id="log" value="<?php if($MID!=''){ echo $MID; }else{ echo @wp_specialchars(stripslashes($user_login), 1); } ?>" type="text" autocomplete="off" placeholder="Merchant ID, Username, or Email">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Password</label>
							<div class="input-icon">
								<i class="fa fa-lock"></i>
								<input class="form-control placeholder-no-fix login-input"  autocomplete="off" placeholder="Password" type="password" name="pwd" id="pwd">
							</div>
						</div>
						
						<button type="submit" id="login-submit" name="submit1" class="btn green ">Log in <i class="m-icon-swapright m-icon-white"></i></button> 	
						&nbsp; &nbsp; <a id="reset-button" href="#">Lost Password?</a>
						
					</form>
				</div>
			</div>
			<div class="row-fluid">	
				<div id="reset" class="span12 hide" >			
					<form id="reset-form" action="/"  method="get" >
						<h3 class="form-title">Reset Your Password</h3>
						
						<div class="error-messages "></div>
						<div class="form-group">
							<label class="control-label visible-ie8 visible-ie9">Email</label>
							<div class="input-icon">
								<i class="fa fa-lock"></i>
								<input class="form-control placeholder-no-fix"  autocomplete="off" placeholder="Email" type="email" name="email" id="email">
							</div>
						</div>
						<div class="button_group">
							<a id="reset-button-off" href="#" class=" btn black">Login</a>
							<button type="submit" name="submit" id="send-reset" class=" btn green ">Reset <i class="m-icon-swapright m-icon-white"></i></button>
						</div> 	
					</form>
				</div>
			</div>
			<div class="row-fluid">	
				<div class="span12">
						<button class="btn span 12" id="no-thanks" data-dismiss="modal" aria-hidden="true">No, Thanks. I want to check it out first.</button>
				</div>
			</div>
		</div> <!-- modal body -->
	</div> <!-- modal -->
<?php endif; ?>