$(function() {
	$('#myaccount-update-form').submit(function() {

		var entry				= {};
		entry.id				= $('#entry_id').val();
		entry.first_name		= $('#first_name').val();
		entry.last_name			= $('#last_name').val();
		entry.company			= $('#company').val();
		entry.google_id			= $('#google_id').val();
		entry.phone				= $('#phone').val();
		entry.password			= $('#password').val();
		entry.password_confirm	= $('#password_confirm').val();
		entry.arr_groups		= $('#arr_groups').val();
		
		console.log(entry);

		var json	= JSON.stringify(entry);

		$.ajax({
		
            url: '/users/update-moderator/ajax',
            type: 'POST',
            data: { 'json': json},
            dataType: 'json',
            success: function(msg) {
	            if( msg.responseText == 'UPDATED' ) {
					add_success("You have successfully updated this user.");
				}
				else {
					add_error("Failed to update user, please try again."+msg);
				}
				
				window.scrollTo(0,0);
				
            },
			error: function(msg) {
			
				if( msg.responseText == 'UPDATED' ) {
					add_success("You have successfully updated this entry.");
				}
				else {
					add_error("Failed to update entry, please try again."+JSON.stringify(msg.responseText));
				}
				
				window.scrollTo(0,0);
				
			}
        });
		
		return false;
		
	});

    // Initialize the jQuery File Upload widget:
    $('.fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: sub+'media/image-uploader/profile'
    });
	
	$('#profile-image-form').change(function() {
		$(this).submit();
	});
	
    /*
// Load existing files:
    // Demo settings:
    $.ajax({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $('.fileupload').fileupload('option', 'url'),
        dataType: 'json',
        context: $('#fileupload')[0],
        maxFileSize: 5000000,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        process: [{
                action: 'load',
                fileTypes: /^image\/(gif|jpeg|png)$/,
                maxFileSize: 20000000 // 20MB
            }, {
                action: 'resize',
                maxWidth: 1440,
                maxHeight: 900
            }, {
                action: 'save'
            }
        ]
    }).done(function (result) {
        $(this).fileupload('option', 'done')
            .call(this, null, {
            result: result
        });
    });
*/
});