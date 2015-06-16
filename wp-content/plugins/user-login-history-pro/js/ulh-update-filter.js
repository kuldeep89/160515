jQuery(document).ready(function( $ ) {
	// Grab GET variables from url
	var $_GET = [];
	var ajaxurl = '/wp-admin/admin-ajax.php';

	document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function () {
	    function decode(s) {
	        return decodeURIComponent(s.split("+").join(" "));
	    }
	    $_GET[decode(arguments[1])] = decode(arguments[2]);
	});
	// Add ulhFilter to current GET variables
	$('body').on('change','#ulh_filter', function(){
    	var ulhFilter = '&ulhFilter='+$(this).val();
		var loc = '?page=user-login-history-pro'+ulhFilter;
		if( $_GET["s"] ){
			loc = loc+"&s="+$_GET["s"];
		}
		if( $_GET["orderby"] ){
			loc = loc+"&orderby="+$_GET["orderby"];
		}
		if( $_GET["order"] ){
			loc = loc+"&order="+$_GET["order"];
		}
		if( $_GET["dateFrom"] || $_GET["dateTo"] && $_GET["dateFrom"]!='' && $_GET["dateTo"]!='' ){
			loc = loc+"&dateFrom="+$_GET["dateFrom"]+"&dateTo="+$_GET["dateTo"];
		}
    	document.location.href = loc;
	});
	
	$('body').on('submit', '#dateSelect',function(){
		if( $('.beatpicker-inputnode-from').val()!='' && $('.beatpicker-inputnode-to').val()!='' && $('.beatpicker-inputnode-from').val().length != 0 && $('.beatpicker-inputnode-to').val().length != 0 ){
	    	var dateRange = '&dateFrom='+$('.beatpicker-inputnode-from').val()+'&dateTo='+$('.beatpicker-inputnode-to').val();
			var loc = '?page=user-login-history-pro';
			if( $_GET["ulhFilter"] ){
				loc = loc+"&ulhFilter="+$_GET["ulhFilter"];
			}
			if( $_GET["s"] ){
				loc = loc+"&s="+$_GET["s"];
			}
			if( $_GET["orderby"] ){
				loc = loc+"&orderby="+$_GET["orderby"];
			}
			if( $_GET["order"] ){
				loc = loc+"&order="+$_GET["order"];
			}
	    	document.location.href = loc+dateRange;
    	} else {
	    	
    	}
		return false;
	});
	
	if( $_GET["dateFrom"] && $_GET["dateTo"] && $_GET["dateFrom"]!='' && $_GET["dateTo"]!='' ){
		$('.beatpicker-inputnode-from').val($_GET["dateFrom"]);
		$('.beatpicker-inputnode-to').val($_GET["dateTo"]);
	}
	
	$('body').on('click','#export_ulh_csv', function(e){
		var dateFrom = $('.beatpicker-inputnode-from').val();
		var dateTo = $('.beatpicker-inputnode-to').val();
		var ulh_filter = $('#ulh_filter').val();
		var search = $('#ULH-search-input').val();
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: { 
				action:'ulh_export_to_csv',
				search: search,
				ulhFilter: ulh_filter,
				dateFrom: dateFrom,
				dateTo: dateTo
			},
			success:function(response) {
				var location = "/wp-content/plugins/user-login-history-pro/export_csv.php?file_name="+response.file_name;
				window.open(location,'_blank');
			},
			error: function(errorThrown){
				console.log(errorThrown);
			}
		});
		
		e.preventDefault();
	});
});