var ajaxurl = '/wp-admin/admin-ajax.php';
var num_days = 30;
var loading_msgs = ['Loading...', 'Still working on it...', 'Sorry this is taking a while...'];
var loading_msgs_msg_num = 0;
var loading_msgs_timer = null;
var current_card_type = 'all_cards';
var ppttd_transactions = {};
var returning_customers = null;
var the_response = null;
var transactions_table = returning_customers_table = show_batches_table = show_chargebacks_table = show_retrievals_table = show_settlements_table = null;
var card_names = {'all_cards':'All Cards', 'AX':'American Express', 'CU':'China Union Pay', 'DB':'Debit Card', 'DC':'Diners Club', 'DI':'Discover', 'EB':'EBT', 'FS':'FSU Private Label', 'HB':'Hudson Bay', 'HC':'HSBC Private Label', 'HR':'HRSI', 'JC':'JCB', 'MC':'MasterCard', 'SC':'Sears Canada', 'UK':'Unkown', 'VI':'Visa'};

var Charts = {
	init: function() {
	    // Get dates
		var the_start_date = -7;

        // Set height/width of charts
        $('.chart').width($('#sales-data').width());

        // Setup date-time picker
		var handleDateTimePickers = function () {
	        $('#form-date-range').daterangepicker({
	            ranges: {
	                'Last 7 Days': [Date.today().add({
	                        days: -7
	                    }), 'today'],
	                'Last 30 Days': [Date.today().add({
	                        days: -30
	                    }), 'today'],
	                'This Month': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
	                'Last Month': [Date.today().moveToFirstDayOfMonth().add({
	                        months: -1
	                    }), Date.today().moveToFirstDayOfMonth().add({
	                        days: -1
	                    })]
	            },
	            opens: (App.isRTL() ? 'left' : 'right'),
	            format: 'MM/dd/yyyy',
	            separator: ' to ',
	            startDate: Date.today().add({
	                days: the_start_date
	            }),
	            endDate: Date.today(),
	            minDate: '01/01/2000',
	            maxDate: Date.today(),
	            locale: {
	                applyLabel: 'Submit',
	                fromLabel: 'From',
	                toLabel: 'To',
	                customRangeLabel: 'Custom Range',
	                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
	                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
	                firstDay: 1
	            },
	            showWeekNumbers: true,
	            buttonClasses: ['btn-danger']
	        },
	        function (start, end) {
	            $('#form-date-range span').html(start.toString('MMMM d, yyyy') + ' - ' + end.toString('MMMM d, yyyy'));
	            Charts.show_sales(start, end);
	        });

	        $('#form-date-range span').html(Date.today().add({
	            days: the_start_date
	        }).toString('MMMM d, yyyy') + ' - ' + Date.today().toString('MMMM d, yyyy'));
	    };
	    handleDateTimePickers();
	    Charts.show_sales(Date.today().add({ days: the_start_date }), Date.today());

        // Setup graph tab clicks
        $('#graph-tabs li a').click(function() {
            // Show loading...
            Charts.show_data_loading(true, true, 'Loading...', true);

            // Get the chart we are reloading
            var the_attr = $(this).attr('href');
            the_attr = the_attr.replace('#','');
            the_attr = 'plot_'+the_attr.replace('_tab','');

            // Wait 500 ms before we load data so DOM can update
            setTimeout(function(){
                eval('Charts.'+the_attr+'();');
                if (ppttd_transactions.all_cards && the_response.data.length > 0) {
                    // Hide loading div(s)
                    $('.sales-data-graph,.average-sale-graph').hide();

                    // Hide loading divs
                    Charts.show_data_loading(false, false, 'Loading...', true);
                } else {
                    // Show no results
                    Charts.show_data_loading(false, true, 'No Results', true);
                }
            }, 500);
        });

        // Search merchant form
        $('body').on('submit', '#search_merchant_form', function() {
            Charts.show_sales($('#form-date-range').data('daterangepicker').startDate, $('#form-date-range').data('daterangepicker').endDate);
	        Charts.show_summary();

            return false;
        });
        
        $('body').on('click', '#export_to_csv', function(e) {
	        Charts.export_csv($('#form-date-range').data('daterangepicker').startDate, $('#form-date-range').data('daterangepicker').endDate, active_mid);
	        e.preventDefault();
        });
	},
	last_monday: function(str){
		var d = new Date(str);
		var day = d.getDay(), diff = d.getDate() - day + (day == 0 ? -6 : 1);
		return new Date(d.setDate(diff)).toString('MMMM d, yyyy');
	},
	show_summary: function() {
		//$('#td_summary_loading').show();
		$('.number').html('--');
		var the_merchant_id;
		if ($.trim( $('#search_merchant_id').val() ) != '') {
			the_merchant_id = $('#search_merchant_id').val();
		} else {
			the_merchant_id = active_mid;
		}
		$.ajax({
			url: ajaxurl,
			data: { action: 'ppttd_td_summary', the_merchant_id: the_merchant_id }
		}).done(function(resp) {
		    $('#transaction_summary_container').html(resp);
		    //$('#td_summary_loading').hide();
		});
	},
	show_sales: function(start, end) {
		// Show loading...
		Charts.show_data_loading(true, true, loading_msgs[0], false);

		// Destroy data table so we can reinitialize it with new data
		$('#show_transactions').dataTable().fnDestroy();

		// Get data
		ppttd_transactions = {};
		
		// Get the merchant ID
		var the_merchant_id;
		if ( $.trim( $('#search_merchant_id').val() ) != '' ) {
			the_merchant_id = $('#search_merchant_id').val();
		} else {
			the_merchant_id = active_mid;
		}

        // Send request for data
		$.ajax({
			url: ajaxurl,
			data: { action: 'get_transactional_data', show_data_by: $('#show_data_by').val(), the_merchant_id: the_merchant_id, start: start, end: end }
		}).done(function(resp) {
			// Try to parse response JSON
			var response = null;
			try {
				response = $.parseJSON(resp);
			} catch (err) {
				response = null;
			}

			// Set global response content
			the_response = response;

			// Parse response OR error out
			if (response != null && response.data != null) {
				// Increment days
				num_days = Math.abs((end-start)/86400000);
				var transaction_dates = [];
				transaction_dates.push(start.getTime());
				for (var i=1;i<num_days;i++) {
					var new_date = start.getTime() + ((24 * 60 * 60 * 1000)*i);
					transaction_dates.push(new_date);
				}

                // Setup card type list
                var card_type_list = '<ul id="cc_icons">';

				// Set all available dates in the timeframe
				$.each(transaction_dates, function( index, value ) {
					var cur_date = new Date(value);
					var mdy = cur_date.getFullYear()+'/'+(cur_date.getMonth()+1)+'/'+cur_date.getDate();

					// Setup all dates for all card types, add card type icon to sort list
					$.each(response.card_types, function( index, value ) {
						// Setup all days in transaction report for all cards
						if (ppttd_transactions[value] == undefined) {
						    // Add card type to list
						    card_type_list += '<li class="cc_'+value+' cc_inactive" alt="'+card_names[value]+'" title="'+card_names[value]+'"></li>';

							// Add transaction to card type data
							ppttd_transactions[value] = {};
    						ppttd_transactions[value].total_sales = {};
    						ppttd_transactions[value].total_sales.data = [];
    						ppttd_transactions[value].total_sales.label = card_names[value];
							ppttd_transactions[value].average_sale = {};
							ppttd_transactions[value].average_sale.data = [];
							ppttd_transactions[value].average_sale.label = card_names[value];
						}
						ppttd_transactions[value].total_sales.data.push([(new Date(mdy)).getTime(), 0, 0]);
						ppttd_transactions[value].average_sale.data.push([(new Date(mdy)).getTime(), 0]);
					});
				});
                
                // Close card type list, setup card type list
                card_type_list += '</ul>';
                $('#select-transaction-type').html(card_type_list);

                // Set "all cards" data as active data
                $('#cc_icons > li:first-child').removeClass('cc_inactive').addClass('cc_active');

                // Replot data when credit card icon is clicked
                $('#cc_icons > li').click(function() {
                    // Check if this is the same icon, if so, don't run anything
                    if ($(this).hasClass('cc_inactive')) {
                        // Remove all active classes
                        $('#cc_icons > li').removeClass('cc_active').addClass('cc_inactive');
    
                        // Set this icon as active
                        $(this).removeClass('cc_inactive');
    
                        // Set current card type
                        var the_card_type = $(this).attr('class');
                        the_card_type = the_card_type.split(' ');
                        the_card_type = the_card_type[0];
                        current_card_type = the_card_type.replace('cc_','');
    
                        // Show loading...
                        Charts.show_data_loading(true, true, 'Loading...', true);
    
                        // Get the chart we are reloading
                        var the_attr = $('#graph-tabs li.active a').attr('href');
                        the_attr = the_attr.replace('#','');
                        the_attr = 'plot_'+the_attr.replace('_tab','');
    
                        // Wait 500 ms before we load it so DOM can update
                        setTimeout(function(){
                            eval('Charts.'+the_attr+'();');
                            if (ppttd_transactions.all_cards && the_response.data.length > 0) {
                                // Hide loading div(s)
                                $('.sales-data-graph,.average-sale-graph').hide();
            
                                // Hide loading divs
                                Charts.show_data_loading(false, false, 'Loading...', true);
                            } else {
                                Charts.show_data_loading(false, true, 'No Results', true);
                            }
                        }, 500);
                    }
                });

                // Replot data when graph type is changed
                $('#the_graph_type').change(function() {
                        // Show loading...
                        Charts.show_data_loading(true, true, 'Loading...', true);
    
                        // Get the chart we are reloading
                        var the_attr = $('#graph-tabs li.active a').attr('href');
                        the_attr = the_attr.replace('#','');
                        the_attr = 'plot_'+the_attr.replace('_tab','');
    
                        // Wait 500 ms before we load it so DOM can update
                        setTimeout(function(){
                            eval('Charts.'+the_attr+'();');
                            if (ppttd_transactions.all_cards) {
                                Charts.show_data_loading(false, false, 'Loading...', true);
                            } else {
                                Charts.show_data_loading(false, true, 'No Results', true);
                            }
                        }, 500);
                });

				// Parse response transaction data
				$.each(response.data, function( index, value ) {
					// Parse date
					var d = Date.parse(value.transaction_time);
					var mdy = d.getFullYear()+'/'+(d.getMonth()+1)+'/'+d.getDate();

					// Add transaction value to all transactions
					var transaction_index = Charts.does_exist((new Date(mdy).getTime()), ppttd_transactions.all_cards.total_sales.data);
					if (transaction_index !== false) {
    					ppttd_transactions.all_cards.total_sales.data[transaction_index][1] = parseFloat(ppttd_transactions.all_cards.total_sales.data[transaction_index][1])+parseFloat(value.amt);
    					ppttd_transactions.all_cards.total_sales.data[transaction_index][2] = parseInt(ppttd_transactions.all_cards.total_sales.data[transaction_index][2])+1;

    					// Add transaction value to card type transactions, increment
    					var ct_transaction_index = Charts.does_exist((new Date(mdy)).getTime(), ppttd_transactions[value.card_type].total_sales.data);
    					ppttd_transactions[value.card_type].total_sales.data[ct_transaction_index][1] = parseFloat(ppttd_transactions[value.card_type].total_sales.data[ct_transaction_index][1])+parseFloat(value.amt);
    					ppttd_transactions[value.card_type].total_sales.data[ct_transaction_index][2] = parseInt(ppttd_transactions[value.card_type].total_sales.data[ct_transaction_index][2])+1;
					}
				});

				// Calculate and set transaction averages
				$.each(ppttd_transactions, function( index, value ) {
					$.each(value.total_sales.data, function( trans_date, trans_value ) {
						var average_transaction = parseFloat(trans_value[1])/trans_value[2];
						var value_index = Charts.does_exist(trans_value[0], ppttd_transactions[index].average_sale.data);
						if (isNaN(average_transaction)) {
							ppttd_transactions[index].average_sale.data[value_index][1] = 0;
						} else {
							ppttd_transactions[index].average_sale.data[value_index][1] = average_transaction;
						}
					});
				});

				// Hide "loading" screens
				Charts.show_data_loading(false, false, 'Loading...', false);

                // Plot sales amount data
                Charts.plot_total_sales();

                // Plot average sale by card type data
                Charts.plot_average_sale();

    			var previousPoint = null;
    			$("#sales_data,#average_sale").bind("plothover", function (event, pos, item) {
    				$("#x").text(pos.x.toFixed(2));
    				$("#y").text(pos.y.toFixed(2));
    
    				if (item) {
    					if (previousPoint != item.dataIndex) {
    						previousPoint = item.dataIndex;
    
    						$("#tooltip").remove();
    						var x = item.datapoint[0].toFixed(2),
    						y = item.datapoint[1].toFixed(2);
    
    						Charts.show_tooltip(item.pageX, item.pageY, "$" + parseFloat(y).toFixed(2));
    					}
    				} else {
    					$("#tooltip").remove();
    					previousPoint = null;
    				}
    			});
			}

            // Load data tables
            Charts.load_data_tables();
		});
	},
	load_data_tables: function() {
	    // Table options
	    var table_options = {
        	"aLengthMenu": [
				[5, 10, 15, 25, 50, -1],
				[5, 10, 15, 25, 50, "All"]
			],
			"iDisplayLength": 5,
			"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
				"sLengthMenu": "_MENU_ records per page",
				"oPaginate": {
					"sPrevious": "Prev",
					"sNext": "Next"
				}
			},
			"bRetrieve": true
	    };

        // Setup transactions table
		if (the_response != null && the_response.data != null) {
            // Initialize OR redraw table
    		if (transactions_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.data, function( index, value ) {
    				$('#transactions_table').append('<tr><td>$'+value.amt+'</td><td>'+value.card_type+'</td><td>'+value.transaction_time+'</td><td>'+value.card_lastfour+'</td></tr>');
    			});

    		    // Setup transactions table
    			transactions_table = $('#show_transactions').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                transactions_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.data, function( index, value ) {
    				transactions_table.dataTable().fnAddData(
    				    ['$'+value.amt,value.card_type,value.transaction_time,value.card_lastfour]
    				);
    			});
    		}
    	}
    	transactions_table.dataTable().fnDraw();

        // Hide "loading" if more than zero records
        if (the_response.data.length > 0) {
            $('.transactions-loading,.sales-data-graph,.average-sale-graph').hide();
        }

        // Setup returning customers table
		if (the_response != null && the_response.returning_customers != null) {
            // Initialize OR redraw table
    		if (returning_customers_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.returning_customers, function( index, value ) {
    				$('#returning_customers_table').append('<tr><td>'+index+'</td><td>'+value.number_of_visits+'</td><td>$'+value.total_sales+'</td></tr>');
    			});

    		    // Setup transactions table
    			returning_customers_table = $('#show_returning_customers').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                returning_customers_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.returning_customers, function( index, value ) {
    				returning_customers_table.dataTable().fnAddData(
    				    [index,value.number_of_visits,'$'+value.total_sales]
    				);
    			});
    		}
        }
        returning_customers_table.dataTable().fnDraw();
        returning_customers_table.dataTable().fnSort([[1,'desc']]);

        // Hide "loading" if more than zero records
        if (Object.keys(the_response.returning_customers).length > 0) {
            $('.returning-customers-loading').hide();
        }

        // Setup batch detail table
		if (the_response != null && the_response.batches != null) {
            // Remove sorting for table
            table_options.aoColumnDefs = [
                { 'bSortable': false, 'aTargets': [ 5, 6, 7 ] }
            ];

    		// Initialize OR redraw table
    		if (show_batches_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.batches, function( index, value ) {
        			// Format float values
        			var total_purch_amt = parseFloat(value.total_purch_amt).toFixed(2);
        			var total_return_amt = parseFloat(value.total_return_amt).toFixed(2);
        			var total_volume = parseFloat(value.total_volume).toFixed(2);

                    // Add row(s) to table
    				$('#show_batches_table').append('<tr class="'+value.uniq_batch_id+' batch_row"><td>'+value.batch_date+'</td><td>'+value.uniq_batch_id+'</td><td>'+value.total_purch_trans+'</td><td>'+value.total_return_trans+'</td><td>'+value.total_trans+'</td><td>$'+total_purch_amt+'</td><td>$'+total_return_amt+'</td><td>$'+total_volume+'</td></tr>');
    			});

    		    // Setup table
    			show_batches_table = $('#show_batches').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                show_batches_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.batches, function( index, value ) {
        			// Format float values
        			var total_purch_amt = parseFloat(value.total_purch_amt).toFixed(2);
        			var total_return_amt = parseFloat(value.total_return_amt).toFixed(2);
        			var total_volume = parseFloat(value.total_volume).toFixed(2);

    				// Add new data to table
    				show_batches_table.dataTable().fnAddData(
    				    [value.batch_date,value.uniq_batch_id,value.total_purch_trans,value.total_return_trans,value.total_trans,'$'+total_purch_amt,'$'+total_return_amt,'$'+total_volume]
    				);
    			});
    		}

            // Reset batch clicking
            $('#show_batches_table tr').click(function() {
                Charts.show_batch_detail($('td', this).eq(1).text().trim(), $(this));
            });
        }
        show_batches_table.dataTable().fnDraw();
        show_batches_table.dataTable().fnSort([[0,'desc']]);

        // Hide "loading" if more than zero records
        if (the_response.batches.length > 0) {
            $('.batch-detail-loading').hide();
        }

        // Setup settlement detail table
		if (the_response != null && the_response.settlements != null) {
            // Remove sorting for table
            table_options.aoColumnDefs = [
                { 'bSortable': false, 'aTargets': [ 0, 1, 2 ] }
            ];

    		// Initialize OR redraw table
    		if (show_settlements_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.settlements, function( index, value ) {
        			// Format float values
        			var amount_to_clear = parseFloat(value.amount_to_clear).toFixed(2);

                    // Add row(s) to table
    				$('#show_settlements_table').append('<tr class="settlement_row"><td>'+value.deposit_date+'</td><td>'+value.transit_number+'</td><td>'+amount_to_clear+'</td></tr>');
    			});

    		    // Setup table
    			show_settlements_table = $('#show_settlements').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                show_settlements_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.settlements, function( index, value ) {
        			// Format float values
        			var amount_to_clear = parseFloat(value.amount_to_clear).toFixed(2);

    				// Add new data to table
    				show_settlements_table.dataTable().fnAddData(
    				    [value.deposit_date,value.transit_number,'$'+amount_to_clear]
    				);
    			});
    		}
        }
        show_settlements_table.dataTable().fnDraw();

        // Hide "loading" if more than zero records
        if (the_response.settlements.length > 0) {
            $('.settlements-loading').hide();
        }

        // Setup chargeback detail table
		if (the_response != null && the_response.chargebacks != null) {
    		// Remove sorting for table
            table_options.aoColumnDefs = [
                { 'bSortable': false, 'aTargets': [ 0, 1, 2, 3 ] }
            ];

    		// Initialize OR redraw table
    		if (show_chargebacks_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.chargebacks, function( index, value ) {
        			// Format float values
        			var case_amount = parseFloat(value.case_amount).toFixed(2);

                    // Add row(s) to table
    				$('#show_chargebacks_table').append('<tr class="chargeback_row"><td>'+value.date_received+'</td><td>'+value.transaction_date+'</td><td>$'+case_amount+'</td><td>'+value.cardholder_number+'</td></tr>');
    			});

    		    // Setup table
    			show_chargebacks_table = $('#show_chargebacks').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                show_chargebacks_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.chargebacks, function( index, value ) {
        			// Format float values
        			var case_amount = parseFloat(value.case_amount).toFixed(2);

    				// Add new data to table
    				show_chargebacks_table.dataTable().fnAddData(
    				    [value.date_received,value.transaction_date,'$'+case_amount,value.cardholder_number]
    				);
    			});
    		}
        }
        show_chargebacks_table.dataTable().fnDraw();
         
        // Hide "loading" if more than zero records
        if (the_response.chargebacks.length > 0) {
            $('.chargebacks-loading').hide();
        }

        // Setup retrievals detail table
		if (the_response != null && the_response.retrievals != null) {
    		// Remove sorting for table
            table_options.aoColumnDefs = [
                { 'bSortable': false, 'aTargets': [ 0, 1, 2, 3 ] }
            ];

    		// Initialize OR redraw table
    		if (show_retrievals_table == null) {
                // Add initial row(s) to table
    			$.each(the_response.retrievals, function( index, value ) {
        			// Format float values
        			var case_amount = parseFloat(value.case_amount).toFixed(2);

                    // Add row(s) to table
    				$('#show_retrievals_table').append('<tr class="retrieval_row"><td>'+value.date_received+'</td><td>'+value.transaction_date+'</td><td>$'+case_amount+'</td><td>'+value.cardholder_number+'</td></tr>');
    			});

    		    // Setup table
    			show_retrievals_table = $('#show_retrievals').dataTable(table_options);
    		} else {
        		// Updating table, clear it first
                show_retrievals_table.dataTable(table_options).fnClearTable();

                // Add row(s) to table
    			$.each(the_response.retrievals, function( index, value ) {
        			// Format float values
        			var case_amount = parseFloat(value.case_amount).toFixed(2);

    				// Add new data to table
    				show_retrievals_table.dataTable().fnAddData(
    				    [value.date_received,value.transaction_date,'$'+case_amount,value.cardholder_number]
    				);
    			});
    		}
        }
        show_retrievals_table.dataTable().fnDraw();

        // Hide "loading" if more than zero records
        if (the_response.retrievals.length > 0) {
            $('.retrievals-loading').hide();
        }

        // Remove loading div from overview
        //$('#td_summary_loading').hide();
	},
	show_batch_detail: function(batch_id, batch_row) {
    	// Remove any old alerts
    	$('.batch_alert').remove();

        // Load batch detail
    	if (batch_row.next().hasClass('batch_detail_transactions_container')) {
        	if (batch_row.next().is(':visible')) {
            	batch_row.next().hide();
        	} else {
            	batch_row.next().show();
        	}
        } else {
            // Set "loading data" notification
            batch_row.after('<tr class="batch_detail_transactions_container '+batch_id+'"><td><a onclick="Charts.export_batch_detail(this, \''+batch_id+'\');" class="btn green export-data '+batch_id+'" style="padding:4px 8px !important;" disabled><i class="icon-download"></i> Export</a></td><td colspan="7" class="batch_table"><em>Loading transactions...</em></td></tr>');

            // Send request for data
            $.ajax({
    			url: ajaxurl,
    			data: { action: 'get_batch_transactions', uniq_batch_id: batch_id }
    		}).done(function(resp) {
        		// Parse JSON data
        		try {
            		resp = $.parseJSON(resp);

                    // Store batch table data
            		var batch_data_table = '<table class="batch_detail_transactions"><thead><tr role="head"><th>Amount</th><th>Card Type</th><th>Time</th><th>Last Four</th></tr></thead><tbody>';
    
            		$.each(resp.transactions, function( index, value ) {
                		var amount = parseFloat(value.amt).toFixed(2);
                		batch_data_table += '<tr><td>$'+amount+'</td><td>'+value.card_type+'</td><td>'+value.transaction_time+'</td><td>'+value.card_lastfour+'</td></tr>';
                    });
    
            		// Add data to table
            		batch_row.next().find('.batch_table').html(batch_data_table+'</tbody></table>');

                    // Enable export button
                    jQuery('.export-data.'+batch_id).removeAttr('disabled');
        		} catch (err) {
            		// Failed to parse response, alert the user
            		// <div class="alert error">Success</div>
            		$('#show_batches').before('<div class="batch_alert alert error">There was an error while loading the batch data. Please try again.</div>');
            		setTimeout(function() { $('.batch_alert').remove(); } , 5000);

            		// Remove empty data table
            		$('.batch_detail_transactions_container.'+batch_id).remove();
        		}
            });
        }
	},
	export_batch_detail: function(element, batch_id) {
    	if (!$(element).attr('disabled')) {
            // Disable export button and let user know we are exporting
            jQuery('.export-data.'+batch_id).val('Exporting...');
            jQuery('.export-data.'+batch_id).attr('disabled', 'disabled');
    
    		$.ajax({
    			url: ajaxurl,
    			data: { 
    				action: 'export_batch_detail',
    				batch_id: batch_id,
    				export_data: true }
    		}).done(function(resp) {
    			// Try to parse response JSON
    			var response = $.parseJSON(resp);		
    			var iframe_location = "/wp-content/plugins/transactional-data/export_csv.php?file_name="+response.file_name;
    
    			// Set URL to export file
    			jQuery('#exportIframe').attr('src', iframe_location);
    
                // Enable export button and let user know we are done exporting
                jQuery('.export-data.'+batch_id).val('Export');
                jQuery('.export-data.'+batch_id).removeAttr('disabled');
    		});	
    	}
	},
	plot_total_sales: function() {
        // Change cursor
        $("#sales_data").bind("plothover", function(event, pos, item) {
            if(item)
                $("#sales_data").css("cursor","pointer","important");
            else
                $("#sales_data").css("cursor","default", "important");
        });

	    // Setup format based on bar graph type
	    var series_format = {};
	    if ($('#the_graph_type').val() == 'bar') {
    	    series_format = {
                bars: {
                    show: true,
                    barWidth: 80000000, // Make bar width one day
					lineWidth: 1,
					fill: true,
					fillColor: 'rgb(103, 172, 237)',
                },
                highlightColor: 'rgb(92, 213, 98)'
    	    }
	    } else {
    	    series_format = {
				lines: {
					show: true,
					lineWidth: 2,
					fill: true,
					fillColor: {
						colors: [
							{
								opacity: 0.05
							}, {
								opacity: 0.02
							}
						]
					}
				},
				points: {
					show: true
				},
				shadowSize: 0
    	    }
	    }

        // Plot the graph
	    var plot = $.plot($("#sales_data"), [ppttd_transactions[current_card_type].total_sales],
			{
			series: series_format,
			grid: {
				hoverable: true,
				tickColor: "#eee",
				borderWidth: 0
			},
			colors: ['rgb(103, 172, 237)'],
			xaxis: {
				ticks: num_days,
				tickSize: [1, "day"],
				mode: "time",
				timezone: "browser",
				timeformat: "%m/%d"
			},
			yaxis: {
				ticks: 5,
				tickFormatter: function(val, axis) {
					return '$'+val.toFixed(2);
				}
			}
		});

        // If sales data, hide loading divs
        if (the_response.data.length > 0) {
            $('.transactions-loading,.sales-data-graph,.average-sale-graph').hide();
        } else {
            $('.transactions-loading,.sales-data-graph,.average-sale-graph').show();
        }
	},
	plot_average_sale: function() {
        // Change cursor
        $("#average_sale").bind("plothover", function(event, pos, item) {
            if(item)
                $("#average_sale").css("cursor","pointer","important");
            else
                $("#average_sale").css("cursor","default", "important");
        });

	    // Setup format based on bar graph type
	    var series_format = {};
	    if ($('#the_graph_type').val() == 'bar') {
    	    series_format = {
                bars: {
                    show: true,
                    barWidth: 80000000, // Make bar width one day
					lineWidth: 1,
					fill: true,
					fillColor: 'rgb(103, 172, 237)',
                },
                highlightColor: 'rgb(92, 213, 98)'
    	    }
	    } else {
    	    series_format = {
				lines: {
					show: true,
					lineWidth: 2,
					fill: true,
					fillColor: {
						colors: [
							{
								opacity: 0.05
							}, {
								opacity: 0.01
							}
						]
					}
				},
				points: {
					show: true
				},
				shadowSize: 2
    	    }
	    }

        // Plot the graph
	    $.plot($("#average_sale"), [ppttd_transactions[current_card_type].average_sale],
			{
			series: series_format,
			grid: {
				hoverable: true,
				clickable: true,
				tickColor: "#eee",
				borderWidth: 0
			},
			colors: ['rgb(103, 172, 237)'],
			xaxis: {
				ticks: num_days,
				tickSize: [1, "day"],
				mode: "time",
				timezone: "browser",
				timeformat: "%m/%d"
			},
			yaxis: {
				ticks: 5,
				tickFormatter: function(val, axis) {
					return '$'+val.toFixed(2);
				}
			}
		});	
	},
	does_exist: function(check_value, check_array) {
		for (var i=0;i<check_array.length;i++) {
			if (check_array[i][0] == check_value) {
				return i;
			}
		}
		return false;
	},
	show_data_loading: function(startTimer, showContent, contentToShow, graphsOnly) {
		// Start / end timer
		if (startTimer == true) {
            // Clear old timer, start over
			clearInterval(loading_msgs_timer);
			loading_msgs_timer=null;
			loading_msgs_msg_num=0;

            // Set loading div text and show/hide
            if (graphsOnly) {
                $('.sales-data-graph,.average-sale-graph').html('Loading...').show();
            } else {
                $('.ppttd-data-loading').html('Loading...').show();
            }

			// Set loading message
			Charts.set_loading_message();
			loading_msgs_timer = setInterval(function(){Charts.set_loading_message();}, 3000);
		} else {
            // Set no results text
    		$('.ppttd-data-loading').html('No Results');

			// Clear old timer, start over
			clearInterval(loading_msgs_timer);
			loading_msgs_timer=null;
			loading_msgs_msg_num=0;
		}
	},
	set_loading_message: function() {
    	if (loading_msgs_msg_num == loading_msgs.length) {
			loading_msgs_msg_num=0;
		}
		$('.ppttd-data-loading').html(loading_msgs[loading_msgs_msg_num]);	
		loading_msgs_msg_num++;
	},
	show_tooltip: function(x, y, contents) {
		$('<div id="tooltip">' + contents + '</div>').css({
			position: 'absolute',
			display: 'none',
			top: y - 40,
			left: x - 30,
			border: '1px solid #333',
			padding: '4px',
			color: '#fff',
			'border-radius': '3px',
			'background-color': '#333',
			opacity: 0.80
		}).appendTo("body").fadeIn(200);
	},
	export_csv: function(the_start, the_end, the_merchant_id) {
		
		$.ajax({
			url: ajaxurl,
			data: { 
				action: 'get_transactional_data',
				the_merchant_id: the_merchant_id,
				start: the_start,
				end: the_end,
				export_data: true }
		}).done(function(resp) {
			// Try to parse response JSON
			var response = $.parseJSON(resp);		
			var iframe_location = "/wp-content/plugins/transactional-data/export_csv.php?file_name="+response.file_name;

			jQuery('#exportIframe').attr('src',iframe_location);
			
		});
	}
}