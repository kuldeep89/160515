var ajaxurl = '/wp-admin/admin-ajax.php';
var the_response = null;
var returning_customers_table = null;
var loading_msgs = ['Loading...', 'Still working on it...', 'Sorry this is taking a while...'];
var loading_msgs_msg_num = 0;
var loading_msgs_timer = null;
var table_options = { "aLengthMenu": [[5, 10, 15, 25, 50, -1],[5, 10, 15, 25, 50, "All"]],"iDisplayLength": 5,"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>","sPaginationType": "bootstrap","oLanguage": { "sLengthMenu": "_MENU_ records per page","oPaginate": { "sPrevious": "Prev", "sNext": "Next" } }, "bRetrieve": true };

var Customers = {
	init: function() {
	    // Scroll to top
	    $(document).scrollTop();
	},
	get: function(the_merchant_id) {
	    // Setup loading div
	    if (returning_customers_table != null) {
    	    returning_customers_table.dataTable(table_options).fnClearTable();
    	    returning_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }

	    // Show loading message
	    Customers.show_data_loading('Loading...');

	    // Send request for data
		$.ajax({
			url: ajaxurl,
			data: { action: 'ppttd_td_customers', the_merchant_id: the_merchant_id }
		}).done(function(resp) {
			// Try to parse response JSON
			try {
				the_response = $.parseJSON(resp);
			} catch (err) {
				the_response = null;
			}

            // Check if null
            if (the_response != null && the_response.returning_customers != null) {
                // Display the customer data, if any
                if (the_response.returning_customers.length == 0) {
                    Customers.show_data_loading(null);
                } else {
                    Customers.load_data_tables(the_merchant_id);
                }
            } else {
                // No results OR error
                Customers.show_data_loading(null);
            }
		});	
	},
	load_data_tables: function(the_merchant_id) {
        // Setup returning customers table
		if (the_response != null && the_response.returning_customers != null) {
		    // Get current merchant ID
            var cur_mid = the_merchant_id;

            // Initialize OR redraw table
    		if (returning_customers_table == null) {
    		    // Remove 'loading' message
    		    $('#returning_customers_table').html('');

                // Add initial row(s) to table
    			$.each(the_response.returning_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
    				$('#returning_customers_table').append('<tr><td>'+cardholder_name+'</td><td>'+value.number_of_visits+'</td><td>$'+value.total_sales+'</td><td>'+cardholder_link+'</td></tr>');
    			});

    		    // Setup transactions table
    			returning_customers_table = $('#show_returning_customers').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                returning_customers_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.returning_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
                    returning_customers_table.dataTable().fnAddData([cardholder_name, value.number_of_visits, '$'+value.total_sales, cardholder_link]);
    			});

                // Draw table
    			returning_customers_table.dataTable().fnDraw();
    		}

            // Sort by number of purchases
            returning_customers_table.dataTable().fnSort([[1,'desc']]);
        }
    },
    add_customer_data: function(the_customer_id) {
        // Clear table of customer data, set to loading
        returning_customers_table.dataTable(table_options).fnClearTable();
        returning_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);

        // Get customer data form
        $.ajax({
			url: ajaxurl,
			data: { action: 'ppttd_td_edit_customer', the_customer_id: the_customer_id }
		}).done(function(resp) {
		    $('#dashboard').html(resp);
		});
    },
    save_customer_data: function() {
        // Retrieve customer data from form
        var the_post_data = {};
        $('.customer-data').each(function(index, value) {
            the_post_data[$(this).attr('id')] = encodeURIComponent($(this).val());
        });

        // Setup AJAX fields we need
        the_post_data.action = 'ppttd_td_save_customer';

        // Send request
        $.ajax({
			url: ajaxurl,
			data: the_post_data
		}).done(function(resp) {
		    // Display response while we reload the page
		    $('#dashboard').html(resp);

            // Reload page
		    window.location.href = '/customers/';
		});
    },
	show_data_loading: function(contentToShow) {
		// Start / end timer
		if (contentToShow == null) {
			// Clear old timer
			clearInterval(loading_msgs_timer);
			loading_msgs_timer=null;
			loading_msgs_msg_num=0;

            // Show content in loading div
            $('.ppttd-table-loading').html('Sorry, no results were found for your search.');
		} else {
			// Clear timer, start over
			clearInterval(loading_msgs_timer);
			loading_msgs_timer=null;
			loading_msgs_msg_num=0;

			// Set loading message
			Customers.set_loading_message();
			loading_msgs_timer = setInterval(function(){Customers.set_loading_message();}, 3000);
		}
	},
	set_loading_message: function() {
		// Loop through "loading" messages, display a new one
		if (loading_msgs_msg_num == loading_msgs.length) {
			loading_msgs_msg_num=0;
		}
		$('.ppttd-table-loading').html(loading_msgs[loading_msgs_msg_num]);	
		loading_msgs_msg_num++;
	}
};