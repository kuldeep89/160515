var postLocation = window.location.origin;

// Sends request for new query for featured section of archive-resource.php.
$('#featured_nav li a').click(function(e) {
	var organization = $(this).data('identifier');
	e.preventDefault();
	
	$('#featured_nav li a').removeClass('current');
	$(this).addClass('current');
	
	$('.featured').css('opacity', '0');

	$.ajax({
		type: "POST",
		url: postLocation+"/wp-content/themes/ppmlayout/ajax-resources.php",
		data: { target: organization },
		dataType: 'json',
		success: function(response) {
			$('.featured').html(response.html);
			$('.featured').css('opacity', '1');
			
			//Make title's clickable.
			clickable_title();
			
		},
		error: function(response) {
			console.log(response);
			$('.featured').css('opacity', '1');
		}
	});
});

var pagination = 0;

// Sends request for new query for general resources section of archive-resource.php.
var bulk_nav = function(event) {
	if($(this).attr('class') !== 'inactive') {
		var organization = $('#general_nav').val();
		pagination = pagination + event.data.paging;
		
		$('.general, #pagination span').css('opacity', '0');
		
		$.ajax({
			type: "POST",
			url: postLocation+"/wp-content/themes/ppmlayout/ajax-resources.php",
			data: { target: organization, page: pagination },
			dataType: 'json',
			success: function(response) {
				$('.general').html(response.html);
				var data = JSON.parse(response.data);
				$('button[value=prev]').attr('class', data.prev);
				$('button[value=next]').attr('class', data.next);
				$('#pagination span').html('Page ' + (pagination + 8)/8);
				$('.general, #pagination span').css('opacity', '1');
				
				//Make title's clickable.
				clickable_title();
				
			},
			error: function(response) {
				console.log(response);
				$('.general, #pagination span').css('opacity', '1');
			}
		});
	}
}

$('#general_nav').change({paging: 0}, bulk_nav);
$('button#prev').click({paging: -8}, bulk_nav);
$('button#next').click({paging: 8}, bulk_nav);
/*

jQuery(function() {
	clickable_title();
});

function clickable_title() {

	if( jQuery('.news-block-padding').length ) {
		
		jQuery('.news-block-padding').each(function() {
			jQuery(this).css({cursor: 'pointer'});
			jQuery(this).click(function() {
				window.location = jQuery(this).attr('data-single');
			});
		})
		
	}
	
}
*/

jQuery(function() {
	
	$("#select_item").change(function(){
		
		var PR = $('#paper_rolls_selection');
		var RT = $('#replacement_terminal_selection');
		var TT = $('#type_of_terminal');
		
		if( $(this).val()=='PR' ){
			PR.show();
			TT.show();
			$('#terminal_info').val('');
			RT.hide();
		} else if( $(this).val()=='RT' ) {
			PR.hide();
			TT.hide();
			$('#paper_rolls').val('');
			$('#terminal_type').val('');
			RT.show();
		}
	});
	
	jQuery('#terminal_thank_you').hide();
	jQuery('#terminal_supplies_form').submit(function(e) {
		
		$('#submit_form').hide();
		$('#sending').html('<em>Sending...</em>');
		
		//**
		// Validate that they have entered in paper roll quantity, or replacement terminal information.
		//**
		if( $('#terminal_info').val().length == 0 && $('#paper_rolls').val().length == 0 ) {
			
			$('#error').html('<div class="alert alert-danger"><strong>Error!</strong> You must select an item and fill out either "Replacement Terminal" or "Paper Rolls" input fields.</div>');
			
			$('#submit_form').show();
			$('#sending').html('');
			
			e.preventDefault();
			
			return 0;
			
		}
		
		var obj_form	= {};
		
		obj_form['Merchant ID'] = $('#merchant_id').val();
		
		jQuery('#terminal_supplies_form').find('input','textarea').each(function() {
			obj_form[$(this).attr('name')] = $(this).val();
		});
		
		jQuery.ajax({
			type: 'POST',
			url: '/wp-admin/admin-ajax.php',
			data: { action: 'terminal_supplies', fields: obj_form }
		}).done(function(resp) {
			resp	= jQuery.parseJSON(resp);
			if( resp.status == 'good' ) {
				jQuery('#terminal_supplies_form').hide('fast', function() {
					jQuery('#terminal_thank_you').show('fast');
				});
			} else {
				jQuery('#terminal_supplies_form').hide('fast', function() {
					alert('Something went wrong. Please try again later.');
				});
			}
		});
		
		//prevent default.
		e.preventDefault();

	});
	
});