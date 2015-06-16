$(function() {

	// initiate layout and plugins
	UINestable.init();
	var JSON_nav = null;
	
	$('#save-navigation').click(function() {
		
		JSON_nav	= get_nav($('#nestable_list_1'));
		console.log(JSON_nav);
		$.ajax({
			type: "POST",
			url: sub+'/pages/update-navigation/'+$('#nav_id').val(),
			data: {JSON_nav: JSON_nav},
			success: function(msg) {
				if( msg.indexOf('UPDATE') != -1 ) {
					add_success('Navigation updated successfully.');
				}
				else {
					add_error(msg);
				}
			},
			error: function(msg) {	
				console.log(msg);
				add_error('Navigation failed to update, please check your network connection and try agian.');	
			}
		});
		
	});
	
});

function get_nav(e) {

	var list = e.length ? e : $(e.target),
        output = list.data('output');
    if (window.JSON) {
        return JSON.stringify(list.nestable('serialize')); //, null, 2));
    } else {
		return 'failed';
    }
    
}