(function() {
	tinymce.create('tinymce.plugins.CRFP', {
		// Plugin initialisation
		init: function(ed, url) {
			// Add command to be fired by button
			ed.addCommand('tinyCRFP', function() {
				ed.windowManager.open({
					title: 'Insert Average Rating',
					file : url + '/../views/popup.php',
                    width : 500,
                    height : 410,
                    inline : 1
                });
			});     
			
			// Add button, hooking to command above
			ed.addButton('crfp', {
				title: 'Insert Average Rating Shortcode for CRFP', 
				cmd: 'tinyCRFP',
				image: url + '/../images/icons/small.png'
			});
		},
		
		// Plugin info
		getInfo: function() {
			return {
				longname: 'CRFP Shortcode',
				author: 'WP Cube',
				authorurl: 'http://www.wpcube.co.uk',
				infourl: 'http://www.wpcube.co.uk',
				version: '1.0'
			};
		}
	});
	
	// Add plugin created above
	tinymce.PluginManager.add('crfp', tinymce.plugins.CRFP);
})();