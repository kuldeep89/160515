$(function() {
	
	$('#tags-table').dataTable({
        "aoColumns": [
          { "bSortable": false },
          null,
          null,
          null,
          { "bSortable": false },
          { "bSortable": false }
        ],
        "aLengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"] // change per page values here
        ],
        // set the initial value
        "iDisplayLength": 10,
        "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ records per page",
            "oPaginate": {
                "sPrevious": "Prev",
                "sNext": "Next"
            }
        },
        "aoColumnDefs": [{
                'bSortable': true,
                'aTargets': [0]
            }
        ]
    });
	
});