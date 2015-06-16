<html>
	<head>
		<title>PayProTec App</title>

		<!-- Every page of your app should have this snippet of Javascript in it, so that it can show the Blue Dot menu -->
		<script type="text/javascript" src="https://appcenter.intuit.com/Content/IA/intuit.ipp.anywhere.js"></script>
		<script type="text/javascript">
		intuit.ipp.anywhere.setup({
			menuProxy: '<?php print($quickbooks_menu_url); ?>',
			grantUrl: '<?php print($quickbooks_oauth_url); ?>'
		});
		</script>

		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="views/bootstrap/css/bootstrap.min.css">

		<!-- Optional theme -->
		<link rel="stylesheet" href="views/bootstrap/css/bootstrap-theme.min.css">

		<!-- Latest compiled and minified JavaScript -->
		<script src="views/bootstrap/js/bootstrap.min.js"></script>
	</head>
	<body>

		

