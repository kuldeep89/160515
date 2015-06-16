var ajaxurl = '/wp-admin/admin-ajax.php';

var Statements = {
	init: function() {
		$('#the_statement_year').change(function() {
    		Statements.get();
		});
		$('body').on('submit','#opt_in_form', function(e){
			Statements.optIn();
			
			e.preventDefault();
		});
	},
    get: function() {
        // Let the user know we are loading data then start loading it
		$('#statements_container table').html('<tr><td colspan="2" style="text-align:center;"><em>Loading...</td></tr>');
		$.ajax({
			url: ajaxurl,
			data: { action: 'smms_monthly_statements', the_statement_year: $('#the_statement_year').val() }
		}).done(function(resp) {
		    $('#statements_container table').html(resp);
		});
    },
    optIn: function() {
	    var opt_in_form = $('#opt_in_form');
	    var opt_in_form_resp = $('#opt_in_response');
	    var opt_in_form_submit = $('#opt_in_submit');
	    var opt_in_form_data = opt_in_form.serialize();
	    
		opt_in_form_submit.hide();
		opt_in_form_resp.hide();
		
	    $.ajax({
			url: ajaxurl,
			type: "POST",
			dataType: "json",
			data: { 
				action: 'smms_opt_in', 
				form_data: opt_in_form_data
			}
		}).done(function(resp) {
		    //$('#statements_container table').html(resp);
		    console.log(resp.status);
		    if(resp.status=='success'){
			    opt_in_form_resp.html(resp.message);
				opt_in_form_resp.show();
		    } else if(resp.status=='fail') {
			    opt_in_form_submit.show();
			    opt_in_form_resp.html(resp.message);
				opt_in_form_resp.show();
		    }
		});
    }
}