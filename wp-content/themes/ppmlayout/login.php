<?php
	$location = $_SERVER['HTTP_HOST'];
		if (strpos($location,'local') !== false) { $location =  'local.my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']); }
		else{ $location = 'my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']); }
?>
<div class="modal-body">      	
  	<!-- LOGIN -->
  	<div>
	  	<div id="myLogin">
	  		<form name="loginform" id="loginform" action="<?php echo (isset($_SERVER['HTTPS'])) ? 'https://my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']).'/wp-login.php' : 'http://my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']).'/wp-login.php' ?>" method="post">
				<div class="error-messages"></div>
				<div class="form-group">
					<label for="user_login" class="control-label visible-ie8 visible-ie9">Username or Email</label>
					<div class="input-icon">
						<!-- <input class="form-control placeholder-no-fix" type="text" name="log" id="user_login" type="text" autocomplete="off" placeholder="Username or Email"  size="20"> -->
						<input type="text" name="log" id="user_login" class="form-control placeholder-no-fix" placeholder="" size="20">
					</div>
				</div>
				<div class="form-group">
					<label for="user_password" class="control-label visible-ie8 visible-ie9">Password</label>
					<div class="input-icon">
						<!-- <input class="form-control placeholder-no-fix" autocomplete="off" placeholder="Password" type="password" name="pwd" id="user_password" size="20"> -->
						<input type="password" name="pwd" id="user_pass" class="form-control placeholder-no-fix" placeholder="" size="20">
					</div>
				</div>
				<input type="submit" name="wp-submit" id="wp-submit" value="Log In">
				<input type="hidden" name="redirect_to" value="">
				<input type="hidden" name="testcookie" value="1">
				<a href="#" class="reset active lost-password-btn" data-target="#myReset">Lost Password?</a>
				
			</form>
			
			
			
	  	</div>
	  	<!-- RESET PASSWORD -->
	  	<div id="myReset">
	      	<form id="reset-form" action="<?php echo 'http://my.' . $_SERVER['HTTP_HOST'] . '/reset-password' ?>" method="get" novalidate="novalidate">
				<div class="error-messages"></div>
				<div class="success-messages"></div>		
				<div class="form-group">
					<label for="email" class="control-label visible-ie8 visible-ie9">Email</label>
					<div class="input-icon">
						<i class="fa fa-lock"></i>
						<input class="form-control placeholder-no-fix error" autocomplete="off" placeholder="Email" type="email" name="email_address" id="email">
					</div>
				</div>
				<input type="submit" name="submit" value="Reset">
				<a href="#" class="back-to-login">Back</a>
			</form>
	  	</div>
	  	
		<!-- REGISTER -->
		<div id="myRegister">
			<form id="register-form">
				<a href="<?php echo 'https://my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']).'/shop/checkout/?billing=yearly' ?>" class="">Yearly Subscription</a>
				<a href="<?php echo 'https://my.'.str_replace('local.', '', $_SERVER['HTTP_HOST']).'/shop/checkout/?billing=monthly' ?>" class="">or Pay Monthly</a>
			</form>
		</div>
  	</div>
  	
	<span class="clear"></span>
</div>