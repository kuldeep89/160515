var ajaxurl = '/wp-admin/admin-ajax.php';
var the_response = null;
var top_spenders_table = null;
var recent_customers_table = null;
var repeat_customers_table = null;
var registered_customers_table = null;
var loading_msgs = ['Loading...', 'Still working on it...', 'Sorry this is taking a while...'];
var loading_msgs_msg_num = 0;
var loading_msgs_timer = null;
var table_options = { "aLengthMenu": [[5, 10, 15, 25, 50, -1],[5, 10, 15, 25, 50, "All"]],"iDisplayLength": 10,"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>","sPaginationType": "bootstrap","oLanguage": { "sLengthMenu": "_MENU_ records per page","oPaginate": { "sPrevious": "Prev", "sNext": "Next" } }, "bRetrieve": true };

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
	c = isNaN(c = Math.abs(c)) ? 2 : c, 
	d = d == undefined ? "." : d, 
	t = t == undefined ? "," : t, 
	s = n < 0 ? "-" : "", 
	i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
	j = (j = i.length) > 3 ? j % 3 : 0;
	return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};


// Setup loading divs
if ($('#repeat_customers_table').length > 0) {
	var ajax_action = 'ppttd_td_repeat_customers';
} else if ($('#top_spenders_table').length > 0) {
	var ajax_action = 'ppttd_td_top_spenders';
} else if ($('#recent_customers_table').length > 0) {
	var ajax_action = 'ppttd_td_recent_customers';
} else if ($('#registered_customers_table').length > 0) {
	var ajax_action = 'ppttd_td_registered_customers';
}

var Customers = {
	init: function() {
	    // Scroll to top
	    $(document).scrollTop();

	    // Get customers
	    if (active_mid != null) {
    	    Customers.get(active_mid);
        }
	},
	get: function(merchant_id) {
	    // Setup loading divs
	    if (repeat_customers_table != null && $('#repeat_customers_table').length > 0) {
    	    repeat_customers_table.dataTable(table_options).fnClearTable();
    	    repeat_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }
	    
	    if (top_spenders_table != null && $('#top_spenders_table').length > 0) {
    	    top_spenders_table.dataTable(table_options).fnClearTable();
    	    top_spenders_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }
    	    
	    if (recent_customers_table != null && $('#recent_customers_table').length > 0) {
    	    recent_customers_table.dataTable(table_options).fnClearTable();
    	    recent_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '', '']);
	    }
    	    
	    if (registered_customers_table != null && $('#registered_customers_table').length > 0) {
    	    registered_customers_table.dataTable(table_options).fnClearTable();
    	    registered_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }

	    // Show loading message
	    Customers.show_data_loading('Loading...');

	    // Send request for data
		$.ajax({
			url: ajaxurl,
			data: { action: ajax_action }
		}).done(function(resp) {
			// Try to parse response JSON
			try {
				the_response = $.parseJSON(resp);
			} catch (err) {
				the_response = null;
			}

            // Check if null
            if (the_response != null && the_response.status == 'success') {
                // Display the customer data, if any
                Customers.load_data_tables();
            } else {
                // No results OR error
                Customers.show_data_loading(null);
            }
		});	
	},
	load_data_tables: function() {
        
        // Setup returning customers table
		if (the_response != null && the_response.top_spenders != null && $('#top_spenders_table').length > 0) {
    		if (top_spenders_table == null) {
    		    $('#top_spenders_table').html('');

                // Add initial row(s) to Top Spenders table
    			$.each(the_response.top_spenders, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
    				$('#top_spenders_table').append('<tr><td>'+cardholder_name+'</td><td>'+value.number_of_visits+'</td><td>'+(value.total_sales).formatMoney(2, '.', '')+'</td><td>'+cardholder_link+'</td></tr>');
    			});
    			
    			top_spenders_table = $('#show_top_spenders').dataTable(table_options);
    		    
	    	} else {
        		// Updating tables, clear them first
                top_spenders_table.dataTable(table_options).fnClearTable();
                

                // Add row(s) to Top Spenders table
    			$.each(the_response.top_spenders, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
                    top_spenders_table.dataTable().fnAddData([cardholder_name, value.number_of_visits, (value.total_sales).formatMoney(2, '.', ''), cardholder_link]);
    			});
    			
    			// Draw table
    			top_spenders_table.dataTable().fnDraw();
	    		
    		}
            // Sort by number of purchases
            top_spenders_table.dataTable().fnSort([[2,'desc']]);
    	}
    		
		if (the_response != null && the_response.recent_customers != null && $('#recent_customers_table').length > 0) {
    		if (recent_customers_table == null) {
    		    $('#recent_customers_table').html('');
    		    
                // Add initial row(s) to Recent Customers table
    			$.each(the_response.recent_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
    				$('#recent_customers_table').append('<tr><td>'+cardholder_name+'</td><td>'+value.transaction_time+'</td><td>'+value.number_of_visits+'</td><td>'+(value.total_sales).formatMoney(2, '.', '')+'</td><td>'+cardholder_link+'</td></tr>');
    			});
    			
    			recent_customers_table = $('#show_recent_customers').dataTable(table_options);
    		    
	    	} else {
        		// Updating tables, clear them first
                recent_customers_table.dataTable(table_options).fnClearTable();
                
    			// Add row(s) to Recent Customers table
    			$.each(the_response.recent_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
                    recent_customers_table.dataTable().fnAddData([cardholder_name, value.transaction_time, value.number_of_visits, (value.total_sales).formatMoney(2, '.', ''), cardholder_link]);
    			});
    			
    			// Draw table
    			recent_customers_table.dataTable().fnDraw();
	    		
    		}
            // Sort by number of purchases
            recent_customers_table.dataTable().fnSort([[1,'asc']]);
    	}
    		
		if (the_response != null && the_response.repeat_customers != null && $('#repeat_customers_table').length > 0) {
    		if (repeat_customers_table == null) {
    		    $('#repeat_customers_table').html('');
    		    
                // Add initial row(s) to Repeat Customers table
    			$.each(the_response.repeat_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
    				$('#repeat_customers_table').append('<tr><td>'+cardholder_name+'</td><td>'+value.number_of_visits+'</td><td>'+(value.total_sales).formatMoney(2, '.', '')+'</td><td>'+cardholder_link+'</td></tr>');
    			});
    			
    			repeat_customers_table = $('#show_repeat_customers').dataTable(table_options);
    		    
    		} else {
        		// Updating tables, clear them first
                repeat_customers_table.dataTable(table_options).fnClearTable();
                
    			// Add row(s) to Repeat Customers table
    			$.each(the_response.repeat_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
                    repeat_customers_table.dataTable().fnAddData([cardholder_name, value.number_of_visits, (value.total_sales).formatMoney(2, '.', ''), cardholder_link]);
    			});
    			
    			// Draw table
    			repeat_customers_table.dataTable().fnDraw();
	    		
    		}
            // Sort by number of purchases
            repeat_customers_table.dataTable().fnSort([[1,'desc']]);
    	}
    		
		if (the_response != null && the_response.registered_customers != null && $('#registered_customers_table').length > 0) {
            // Initialize OR redraw table
    		if (registered_customers_table == null) {
    		    // Remove 'loading' message
    		    $('#registered_customers_table').html('');
    		    
                // Add initial row(s) to Registered Customers table
    			$.each(the_response.registered_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
    				$('#registered_customers_table').append('<tr><td>'+cardholder_name+'</td><td>'+value.number_of_visits+'</td><td>'+(value.total_sales).formatMoney(2, '.', '')+'</td><td>'+cardholder_link+'</td></tr>');
    			});

    		    // Setup tables
    			registered_customers_table = $('#show_registered_customers').dataTable(table_options);
    		} else {
        		// Updating tables, clear them first
                registered_customers_table.dataTable(table_options).fnClearTable();
                
    			// Add row(s) to Registered Customers table
    			$.each(the_response.registered_customers, function( index, value ) {
    				var cardholder_name = index;
    				var cardholder_id = index;
    				if (value.cardholder_name) {
        				cardholder_name = value.cardholder_name+' ('+index+')';
    				}
    				if (value.cardholder_id) {
        				cardholder_id = value.cardholder_id;
    				}
    				var cardholder_link = '<a onclick="Customers.add_customer_data(\''+cardholder_id+'\');"><i class="icon-edit"></i> Edit</a>';
                    registered_customers_table.dataTable().fnAddData([cardholder_name, value.number_of_visits, (value.total_sales).formatMoney(2, '.', ''), cardholder_link]);
    			});

                // Draw table
    			registered_customers_table.dataTable().fnDraw();
    		}

            // Sort by number of purchases
            registered_customers_table.dataTable().fnSort([[0,'asc']]);
        }
    },
    add_customer_data: function(the_customer_id) {
	    if (repeat_customers_table != null && $('#repeat_customers_table').length > 0) {
    	    repeat_customers_table.dataTable(table_options).fnClearTable();
    	    repeat_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }
	    
	    if (top_spenders_table != null && $('#top_spenders_table').length > 0) {
    	    top_spenders_table.dataTable(table_options).fnClearTable();
    	    top_spenders_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }
    	    
	    if (recent_customers_table != null && $('#recent_customers_table').length > 0) {
    	    recent_customers_table.dataTable(table_options).fnClearTable();
    	    recent_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '', '']);
	    }
    	    
	    if (registered_customers_table != null && $('#registered_customers_table').length > 0) {
    	    registered_customers_table.dataTable(table_options).fnClearTable();
    	    registered_customers_table.dataTable().fnAddData(['<em class="ppttd-table-loading">Loading...</em>','','', '']);
	    }

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
            
            setTimeout(function(){
		    	window.location.href = document.URL;
            }, 500);
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