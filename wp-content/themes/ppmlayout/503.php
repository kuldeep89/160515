<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">   
    <head>	
        <title>Saltsha - Coming Soon</title>
        <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=no">
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
        <script type="text/javascript">
        	$(function() {
	        	$('#MERGE0').blur(function() {
	        		if ($(this).val() == '') {
		        		$(this).val('name@email.com');
	        		}
				}).focus(function() {
					if ($(this).val() == 'name@email.com') {
		        		$(this).val('');
	        		}
				});
        	});
        </script>
        <link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/style.css" />
        <link rel="stylesheet" type="text/css" href="https://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/font.css" />
        <!--[if IE]>
			<link rel="stylesheet" type="text/css" href="./css/ie.css" />
		<![endif]-->
        <style type="text/css">
        	body {
				background: url(http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/background.jpg) no-repeat center center fixed; 
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
				color: #fff;
        	}
            #subscribe {
	            background: url(http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/arrow.png) no-repeat center center;
	            background-color:#FFF; /* fallback for browser that not support rgba below */
	            height: 50px;
	            width: 50px;
			}
            #MERGE0 {
	            padding-left: 65px;
	            background: url(http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/email.png) no-repeat 20px center;
	            height: 50px;
	            width: 80%;
	            color: #fff;
	            font-size: 18px;
	            margin-right: 5px;
            }
            @media (max-width: 400px) {
            	#subscribe {
	            	background-image: url(http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/subscribe.png);
            	}
            }
        </style>
    </head>
    <body>
		<div id="logo">
			<img src="http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/logo.png" alt="logo" />
		</div>
        <div id="content">
            <h2 id="message"> 
	            Our dream is to help small businesses grow. 
            </h2>
            <div id="email">
				<p id="sign-up">Sign up now for our newsletter and updates!</p>
				<form id="subscribe-form" action="http://paypromedia.us7.list-manage.com/subscribe/post" method="post">
					<input type="hidden" name="u" value="e81d58b75c4280863de8148c2">
					<input type="hidden" name="id" value="3f7cb79cb9">
					<input type="email" autocapitalize="off" autocorrect="off" name="MERGE0" id="MERGE0" size="30" value="name@email.com" >
					<input type="submit" value="" name="subscribe" id="subscribe" />
				</form>
			</div>
			<div id="blog">
				Keep up with our progress by visiting our blog at <a href="http://blog.saltsha.com/">blog.saltsha.com</a>.
			</div>
			<div id="social-media">
				<div>CONNECT</div>
				<a href="https://www.facebook.com/Saltsha" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/fb.png" alt="facebook" /></a>
				<a href="https://plus.google.com/105387137872967257002" target="_blank" rel="publisher"><img src="http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/gplus.png" alt="Google+" /></a>
				<a href="https://twitter.com/saltsha" target="_blank"><img src="http://<?php echo $_SERVER['HTTP_HOST'] ?>/wp-content/themes/ppmlayout/maintenance_mode/tw.png" alt="twitter" /></a>
			</div>
        </div>
        <script>var _gaq=[['_setAccount', 'UA-46649787-1'],['_setDomainName', 'saltsha.com'],['_trackPageview']];(function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];g.src='//www.google-analytics.com/ga.js';s.parentNode.insertBefore(g,s)}(document,'script'))</script>
    </body>
</html>

