(function($) {
	
	$(function() {
	
		//Handle adding additional uploads.
		$('#add-resource-uploader').click(function() {
			$('.wp_custom_attachment:last').after('<br /><input class="wp_custom_attachment" name="wp_custom_attachment'+$('.wp_custom_attachment').length+'" value="" size="25" type="file" />');
		});
		
		//Handle removing resources.
		$('.delete-resource-link').click(function(e) {

			e.preventDefault();
			
			if( confirm("Completely remove resource?") ) {
			
				var post_id	= $(this).attr('data-postid');
				var meta_id	= $(this).attr('data-metaid');
	
				if( $('.resource').length == 1 ) {
					$('.attached-resources').html('<p>No resources available.</p>');
				}
				
				$(this).parents('.resource').remove();
				
				$.ajax({
				
					type: "POST",
					url: '/wp-admin/admin-ajax.php?action=ajax_remove_resource',
					data: {post_id:post_id, meta_id: meta_id},
					success: function(response) {
						console.log(response);
					},
					dataType: 'html'
					
				});
			
			}
			
			return false;
			
		});
			
	});
	
})(jQuery);