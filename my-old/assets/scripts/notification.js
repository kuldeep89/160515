var notifications_errors	= 0;
var notifications_success	= 0;

function add_error(msg) {
	
	notifications_errors++;	
	$('#notifications').append('<div id="notification-error-'+notifications_errors+'" class="alert alert-error"><a data-dismiss="alert" class="close"></a>'+msg+'</div>');
	setTimeout(function() { $('#notification-success-'+notifications_errors).fadeOut('slow', function() { $(this).remove()}) }, 10000);
	setTimeout("$('#notification-success-"+notifications_errors+"').fadeOut()", 2000);

}

function add_success(msg) {

	notifications_success++;
	$('#notifications').append('<div id="notification-success-'+notifications_success+'" class="alert alert-success"><a data-dismiss="alert" class="close"></a>'+msg+'</div>');
	setTimeout("$('#notification-success-"+notifications_success+"').fadeOut()", 2000);

}