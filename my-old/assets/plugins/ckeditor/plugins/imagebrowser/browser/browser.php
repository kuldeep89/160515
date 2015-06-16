
<!doctype html>
<html>
	<head>
		<link rel="stylesheet" href="/assets/plugins/ckeditor/plugins/imagebrowser/browser/browser.css" />
	</head>

	<body>
		<script type="text/x-template-html" id="js-template-image">
			<a href="javascript://" class="thumbnail js-image-link" data-url="%imageUrl%"><img src="%thumbUrl%" /></a>
		</script>

		<select class="folder-switcher" id="js-folder-switcher"><option>Loading..</option></select>

		<div id="js-images-container"></div>

		<script type="text/javascript" src="/assets/plugins/ckeditor/plugins/imagebrowser/browser/jquery-1.9.1.min.js"></script>
		<script type="text/javascript" src="/assets/plugins/ckeditor/plugins/imagebrowser/browser/browser.js"></script>

		<script type="text/javascript">
			CkEditorImageBrowser.init();
		</script>
	</body>
</html>
