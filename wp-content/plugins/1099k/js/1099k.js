var Docs = {
	
	init: function(){
    	$('#container_1099k table').DataTable( {
	        "aaSorting": [[ 0, "desc" ], [ 1, 'asc' ]],
            "bPaginate": false,
            "bFilter": false,
            "bInfo" : false
	    } );
	}
	
}