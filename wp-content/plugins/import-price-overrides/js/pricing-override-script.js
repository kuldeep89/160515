jQuery(document).ready(function( $ ) {
	
	var ajaxurl = '/wp-admin/admin-ajax.php';
	
	$('body').on('click','#io_run_import', function(e){
		var io_run_import = $('#io_run_import');
		io_run_import.hide();
		$('#io_response').html('Running script, please wait, it might take a minute.');
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: { 
				action:'import_price_overrides'
			},
			success:function(response) {
				console.log(response.status);
				if(response.status=='success') {
					$('#io_response').html('done.');
				} else {
					$('#io_response').html(response.message);
				}
			},
			error: function(errorThrown){
				console.log(errorThrown);
				$('#io_response').html('There was an error processing your request.');
			}
		});
		
		e.preventDefault();
	});
});