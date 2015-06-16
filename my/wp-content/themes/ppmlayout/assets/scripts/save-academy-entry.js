var featured_image	= ($('#featured-image-input').val().trim() != '' && $('#featured-image-input').val() != null) ? $('#featured-image-input').val() : null;

$(function() {

	$('#academy-entry-form').submit(function() {
		return save_entry();
	});

	handleToggleButtons();
	retrieve_academy_images();

	$('#cancel').click(function() {
		$('.featured-image').removeClass('clicked');
		if ($('img[alt="'+featured_image+'"]').length > 0) {
			$('img[alt="'+featured_image+'"]').addClass('clicked');
		}
	});

	$('#save-and-close').click(function() {
		// Save academy entry
		save_entry();
	});

	$('#view-academy-images').click(function() {
		// Retrieve academy images from CDN
		retrieve_academy_images();	
	});

	$('#upload-image').click(function() {
		// Reset upload form when tab clicked
		$('#upload_iframe').attr('src', $('#upload_iframe').attr('uploadpath'));
	});

	$('#remove-featured-image').click(function() {
		// Remove featured image from article
		featured_image = null;
		$('#featured-image-input').val(null);
		save_entry();
		retrieve_academy_images(); 
	});

	// Image has been uploaded
	$('#upload_iframe').load(function() {
		if ($(this).contents().children().find('#success').length > 0) {
			$('#view-academy-images').trigger('click');
		}
	});
});

// Retrieve images
function retrieve_academy_images() {
	$('#media_loading').show();
	$.ajax({
		url: sub+'/media/image-browser/academy',
		type: 'GET',
		dataType: 'json',
		success: function(msg) {
			populate_images(msg);
			$('#media_loading').hide();

			// Show current image as selected
			var get_featured_image = ($('#featured-image-input').val() != '') ? $('#featured-image-input').val() : featured_image;
			if ($('img[alt="'+get_featured_image+'"]').length > 0) {
				$('img[alt="'+get_featured_image+'"]').addClass('clicked');
			}
		}
	});
}

// Delete image
function delete_image ( img_url ) {
	if (img_url == featured_image) {
		alert('You cannot delete the currently selected image.');
	} else {
		var confirm_delete = confirm('Are you sure you want to delete this image? This cannot be undone.');
		if (confirm_delete) {
			$('#media_loading').show();

			var image_data = {};
			image_data.img_url = img_url;
			image_data.namespace = 'academy';

			$.ajax({
	            url: sub+'/media/delete-image/',
	            type: 'POST',
	            data: { 'image_data': image_data },
	            dataType: 'json',
	            success: function(msg) {
	            	if (msg.status == 'success') {
	            		retrieve_academy_images();
	            	} else {
		            	alert('Sorry, there was an error deleting your image. Please refresh the page and try again.');
	            	}
	            },
	            error: function(err) {
		            alert('Sorry, there was an error deleting your image. Please refresh the page and try again.');
	            }
	      
			});
		}
	}
}

function populate_images( msg ) {
	$('.cdn_image').remove();

	$(msg).each(function() {
		var show_delete = '';
		if ($(this).attr('image') == featured_image) {
			show_delete = 'display: none;';
		}
		$('.featured-images').append('<div class="cdn_image" style="display: inline-block; position: relative;"><img alt="'+$(this).attr('image')+'" src="'+$(this).attr('thumb')+'" class="featured-image"/><div class="cdn_delete_button" style="position: absolute;bottom: 15px;right: 15px;background-color: #efefef;border: 1px solid #000;padding: 0px 5px;margin: 0px; pointer: cursor;'+show_delete+'" onclick="delete_image(\''+$(this).attr('image')+'\')"><img src="../../assets/img/remove-icon-small.png" /></div></div>');
	});

	$('.featured-image').click(function() {

		$('.featured-image').removeClass('clicked');

		$(this).addClass('clicked');
	});
}

function handleToggleButtons() {

    if (!jQuery().toggleButtons) {
        return;

    }

    $('.success-toggle-button').toggleButtons({
    
    	width: 200,
        label: {
            enabled: "Published",
            disabled: "Unpublished"
        },
        style: {
	        enabled: "success",
	        disabled: "danger"
        },
        onChange: function ($el, status, e) {
        
			$('#published').val((status)?1:0);
			
		}
		
    });

};

function update_featured_image() {
	var current_featured_image = (featured_image != null) ? featured_image : $('#featured-image-input').val();
	$('.featured-image-preview').attr('src', current_featured_image);
	$('#featured-image-input').val(current_featured_image);

	featured_image = current_featured_image;

	// If .content-image image exists, set featured image
	if ($('.content-image').length > 0) {
		$('.featured-image-preview.no-content-image').attr('src', current_featured_image);
	}
}

var entry_id	= null;

function save_entry() {

		var entry			= {};
		entry.title			= $('#entry-title').text();
		entry.schedule_post	= $('#entry-publish-date').val();
		entry.content		= $('#entry-content').html();
		entry.keywords		= $('#keywords').val();
		entry.description 	= $('#description').val();
		entry.browser_title	= $('#browser-title').val();
		entry.tags			= $('#select-tags').val();
		entry.categories	= $('#select-categories').val();
		entry.author_id		= $('#author_id').val();
		entry.published		= $('#published').val();
		entry.entry_id			= $('#entry_id').val();
		entry.featured_image = featured_image = ($('.cdn_image > img.featured-image.clicked').length > 0 && $('.cdn_image > img.featured-image.clicked').attr('alt').length > 0) ? $('.cdn_image > img.featured-image.clicked').attr('alt') : null;

		//This is for updating the featured image: update_featured_image();
		entry_id	= $('#entry_id').val();

		var json	= JSON.stringify(entry);

		$.ajax({
            url: sub+'/academy/update-entry/'+entry_id,
            type: 'POST',
            data: { 'json': json },
            dataType: 'json',
            success: function(msg) {
	            if( msg.responseText == 'UPDATED' ) {
	            	update_featured_image();
					add_success("You have successfully updated this entry.");
				}
				else {
					add_error("Failed to update entry, please try again.");
				}

				window.scrollTo(0,0);
            },
			error: function(msg) {
			
				if( msg.responseText == 'UPDATED' ) {
					update_featured_image();
					add_success("You have successfully updated this entry.");
				}
				else {
					add_error("Failed to update entry, please try again.");
					
				}
				
				window.scrollTo(0,0);
				
			}
			
        });
		
		return false;
		
}