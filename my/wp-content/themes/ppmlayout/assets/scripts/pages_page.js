$(function() {
	
	// Add / update page form
	$('#page-form').submit(function() {

		var page			= {};
		page.page_id		= ($('#page_id').val() == '' ? null : $('#page_id').val());
		page.content		= $('#page-content').html();
		page.title			= $('#title').html();
		page.name			= $('#page_name').val();
		page.url			= $('#page_url').val();
		page.browser_title	= $('#browser-title').val();
		page.description	= $('#description').val();
		page.keywords		= $('#keywords').val();
		page.template		= $('#template').val();
		
		page_JSON			= JSON.stringify(page); 
		
		// If there is a page id, update, else, add page
		var post_url = ($('#page_id').val() == '' ? 'create-page' : 'update-page');

		$.ajax({
            url: sub+'/pages/'+post_url,
            type: 'POST',
            data: { 'json': page_JSON },
            dataType: 'json',
            success: function(msg) {
				if (msg.status == 'success') {
					add_success(msg.statusmsg);
					if (msg.page_id) {
						$('#page_id').val(msg.page_id);
						$('button').html('Update Page');
					}
				} else {
					add_error(msg.statusmsg);
				}
			},
			error: function(err) {
				var create_or_update = ($('#page_id').val() == '' ? 'creating' : 'updating');
				add_error('There was an error '+create_or_update+' your page, please try again.');
				console.log(JSON.stringify(err));
			}
		});
		
		return false;

	});
	
	// Check onfocus/onblur events of certain elements
	$('#title,#page-content').focus(function() {
		if ($(this).text() == 'Enter Page Title' || $(this).text() == 'Enter your page content here') {
			$(this).html('');
		}
	}).blur(function() {
		if ($(this).attr('id') == 'title' && $(this).text() == '') {
			$(this).html('Enter Page Title');
		} else if ($(this).attr('id') == 'page-content' && $(this).text() == '') {
			$(this).html('<em>Enter your page content here</em>');
		} else {
			if ($(this).text() == '') {
				$(this).html('Enter text');
			}
		}
	});

});

<<<<<<< HEAD
	$('#title').blur(function() {
		
		if( !($('#page_name').val().trim().length > 0) ) {
			$('#page_name').val($(this).html());
		}
		
		if( !($('#browser-title').val().length > 0) ) {
			$('#browser-title').val($(this).html());
		}
		      
		if( !($('#page_url').val().length > 0) ) {
		
			var url	= $(this).html().toString();
			
			
			
			//while( url.indexOf(' ') != -1 )
			//	url.replace(' ', '-');

			$('#page_url').val($(this).text().toLowerCase().replace(/[^a-zA-Z0-9.]/g, "-"));
		}
		
	});

});
=======
// Page functions
var page_functions = {
	checkBlank: function(message) {
		return message;
	}
}
>>>>>>> 30e3b5ee04ff185acad51b8db0d5c713ede1e897
