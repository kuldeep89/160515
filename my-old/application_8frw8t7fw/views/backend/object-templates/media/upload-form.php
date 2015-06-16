<html>
<head>
	<style type="text/css">
		body {
			font-family: Arial;
		}
		#loading {
			height: 100%; width: 100%;
			position: absolute;
			top: 0px; left: 0px;
			background-color: #000;
			text-align: center;
			display: none;
		}
		.transparent {
			-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=60)";
			filter: alpha(opacity=60);
			-moz-opacity: 0.6;
			-khtml-opacity: 0.6;
			opacity: 0.6;
		}
	</style>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
	<div id="loading" class="transparent"><img src="<?php echo site_url('assets/img/media_loading.gif') ?>" style="padding-top: 17%;" /></div>
	<form enctype="multipart/form-data" method="post" action="<?php echo site_url('media/image-uploader/'); ?>">
		Browser for File: <input type="file" name="upload" onchange="$('#loading').css('display','block'); this.form.submit();" />
	</form>
</body>
</html>