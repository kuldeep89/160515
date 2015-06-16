// Object to store widget locations
var widgetLocations = [];
		
var dashboard = {
	onLoad: function() {
		// Load Google Analytics API
		gapi.client.load('analytics', 'v3', dashboard.checkAuth);

		// Setup date range picker
		var pickerStartDate = dashboard.nullStartDate();
		var pickerEndDate = dashboard.nullEndDate();

		// Setup date range picker
		$('#daterange').daterangepicker({format: 'yyyy-MM-dd', startDate: pickerStartDate, endDate: pickerEndDate, opens: 'left'});

		// Date range picker apply button
		$('.daterangepicker .btn-success').click(function () {
			// Get stats for date range
			dashboard.getStats('standard');
		});

		// Set click event for add widget buttons to open add widget function
		$('.add-widget').click(function() {
			// Set hidden field used to store current widget location we're adding a widget to
			$('#widget_column').val($(this).parent().attr('column'));
			$('#widget_row').val($(this).parent().attr('row'));
	
			// Show add widget form
			$('#add-widget-form').modal('show');
		});

		// Reset modal when closed/hidden
		$('#add-widget-form').on('hidden', function() {
			// Reset add widget form
			dashboard.showWidgets();
		});

		// Setup add widget form
		dashboard.showWidgets();

		// Run stock quotes query
		setInterval(function(){query_stock_quotes()},7000);

		/////////////////
		// * Note: Setting RSS vars to be used for the RSS feed
		// E.Marrufo
		/////////////////
		var feedcontainer=document.getElementById("feeddiv");
		var feedurl="http://feeds.feedburner.com/Paypromedia";
		var feedlimit=10;
		var rssoutput="";

		/////////////////
		// * Note: This function uses google feed API methods used for the RSS feed
		// E.Marrufo
		/////////////////
		function rssfeedsetup(){
		var feedpointer=new google.feeds.Feed(feedurl); //Google Feed API method
		feedpointer.setNumEntries(feedlimit);//Google Feed API method
		feedpointer.load(displayfeed); //Google Feed API method
		}

		/////////////////
		// * Note: This function displays the results of teh google API feed queries into RSS feeds
		// E.Marrufo
		/////////////////
		function displayfeed(result){
		if (!result.error){
		var thefeeds=result.feed.entries;
		for (var i=0; i<thefeeds.length; i++)
			rssoutput+="<div class=\"news-blocks\"><a href='" + thefeeds[i].link + "'>" + thefeeds[i].title + "</a><br /><i class=\"icon-external-link\"></i>"+ thefeeds[i].contentSnippet +"<br />"+ thefeeds[i].publishedDate.substr(0, 16) +"<a href=" + thefeeds[i].link + " class=\"news-block-btn\">Read more <i class=\"m-icon-swapright m-icon-black\"></i></a></div>";
			feedcontainer.innerHTML=rssoutput;
		}
		else
		alert("Error fetching feeds!");
		}

		/////////////////
		// * Note: Call to this function to show results on the rss_feed.php view
		// E.Marrufo
		/////////////////
		rssfeedsetup();

		// Reposition and resize elements when page is resized
		$(document).resize(function() {
			// If in edit mode...
			if (editMode == true) {
				// Resize widgets
				$('.dragme').each(function() {
					// Get widget column and row
					var widgetColumn = $(this).attr('column');
					var widgetRow = $(this).attr('row');

					// Make sure this item is assigned a widget column and row
					if (widgetColumn && widgetRow) {
						// Get droppable element data
						var droppableElement = $('.droppable[column="'+widgetColumn+'"][row="'+widgetRow+'"]');

						// Hide add widget button
						droppableElement.find('.add-widget').hide();

						// Position current page element
						$(this).css('top', (droppableElement.position().top+40));
						$(this).css('left', (droppableElement.position().left+10));
						$(this).css('width', (droppableElement.width()-15));
					}
				});
			}
		});
	},
	checkAuth: function() {
		gapi.auth.authorize({client_id: CLIENT_ID, scope: 'https://www.googleapis.com/auth/analytics', immediate: true}, dashboard.handleAuthResult);
	},
	handleAuthResult: function(authResult) {
		if (authResult && !authResult.error) {
			dashboard.getStats('standard');
		} else {
			$('#site_statistics_loading').hide();
			$('#site_statistics_content').show();
			$('#site_statistics').html('<div class="alert alert-warning" style="text-align: center;"><h2>We\'re almost there!</h2>We need you to authorize access to your Google Analytics account!<br/><br/><button class="btn green" id="authorize-button" onclick="dashboard.handleAuthClick()" style="margin-left: 15px; display: inline-block;"><i class="icon-key"></i> Authorize</button><br/><br/></div>').show();
		}
	},
	handleAuthClick: function() {
		gapi.auth.authorize({client_id: CLIENT_ID, scope: 'https://www.googleapis.com/auth/analytics', immediate: false}, dashboard.handleAuthResult);
        return false;
	},
	getStats: function(chartType) {
		// Do this if if a standard chart type
		$('#site_statistics_loading').show();
		if (chartType == 'standard') {
			// Setup picker data
			var todaysDate = new Date();
			var padDate = todaysDate.getDate().toString();
			if (padDate.length <= 1) {
				padDate = '0'+todaysDate.getDate();
			}
			var padMonth = todaysDate.getMonth().toString();
			if (padMonth.length <= 1) {
				padMonth = '0'+(todaysDate.getMonth()+1);
			}
			todaysDate = todaysDate.getFullYear()+'-'+padMonth+'-'+padDate;

			// Setup date range picker
			var pickerStartDate = ($('.daterangepicker input[name="daterangepicker_start"]').val() != todaysDate) ? $('.daterangepicker input[name="daterangepicker_start"]').val() : dashboard.nullStartDate();
			var pickerEndDate = ($('.daterangepicker input[name="daterangepicker_end"]').val() != "") ? $('.daterangepicker input[name="daterangepicker_end"]').val() : dashboard.nullEndDate();

			// Setup date range picker
			$('.daterangepicker input[name="daterangepicker_start"]').val(pickerStartDate);
			$('.daterangepicker input[name="daterangepicker_end"]').val(pickerEndDate);
			$('#daterange').val(pickerStartDate+' - '+pickerEndDate);

			// Check if standard or compare chart
			if (chartType == 'standard') {
				// Build Google Analytics API query and execute for standard chart type
				gapi.client.analytics.data.ga.get({
					'ids': TABLE_ID,
					'start-date': pickerStartDate,
					'end-date': pickerEndDate,
					'dimensions': 'ga:date',
					'metrics': 'ga:visits,ga:pageviews'
				}).execute(function(thisResult) { dashboard.populateChart(thisResult, chartType); });
			} else {
				// Build Google Analytics API query and execute for compare chart type
				gapi.client.analytics.data.ga.get({
					'ids': TABLE_ID,
					'start-date': pickerStartDate,
					'end-date': pickerEndDate,
					'dimensions': 'ga:date',
					'metrics': 'ga:visits'
				}).execute(function(thisResult) { dashboard.populateChart(thisResult, chartType); });
			}
		}
	},
	populateChart: function(result, chartType) {
		// If standard chart type, run this
		$('#site_statistics_content').hide();
		$('#site_statistics_loading').show();
		if (chartType == 'standard') {
			// Setup page views and visitors
			var pageviews = [];
			var visitors = [];
			var visitor_dates = [];
			
			/////////////////
			// * Note: Line 160 - 167 checks the TABLE_ID to be sure that its correct if not displays an error message. 
			// E.Marrufo
			/////////////////
			if (isNaN(TABLE_ID.substr(3)) || result.code == 403) {	
				$('#site_statistics_loading').hide();
				$('#site_statistics_content').show();
				$('#not-valid').removeClass('hide');
			} else if (result.code == 400) {
				$('#site_statistics_loading').hide();
				$('#site_statistics_content').show();
				$('#enter-ga-code').removeClass('hide');
			}

			for (var i = 0; i<result.rows.length; i++) {
				var cur_date = dashboard.formatDate(result.rows[i][0]);
				visitors.push([i+1, result.rows[i][1], cur_date, 'Visitors: ']);
				pageviews.push([i+1, result.rows[i][2], cur_date, 'Views: ']);
				if (i % Math.floor(result.rows.length*.10) == 0 || result.rows.length <= 10) {
					visitor_dates.push([i+1, dashboard.formatDate(result.rows[i][0], false)]);
				}
			}
	
	        if ($('#site_statistics').size() != 0) {
	
	            $('#site_statistics_loading').hide();
	            $('#site_statistics_content').show();
	
	            var plot_statistics = $.plot($("#site_statistics"), [{
	                    data: pageviews,
	                    label: 'Page Views'
	                }, {
	                    data: visitors,
	                    label: 'Visitors'
	                }
	            ], {
	                series: {
	                    lines: {
	                        show: true,
	                        lineWidth: 2,
	                        fill: true,
	                        fillColor: {
	                            colors: [{
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
	                },
	                grid: {
	                    hoverable: true,
	                    clickable: true,
	                    tickColor: "#eee",
	                    borderWidth: 0
	                },
	                colors: ["#d12610", "#37b7f3", "#52e136"],
	                xaxis: {
						ticks: visitor_dates
					},
	                yaxis: {
	                    ticks: 11,
	                    tickDecimals: 0
	                }
	            });
	
	            // Listen for hover and show/hide tooltip(s)
	            var previousPoint = null;
	            $("#site_statistics").bind("plothover", function (event, pos, item) {
	                $("#x").text(pos.x.toFixed(2));
	                $("#y").text(pos.y.toFixed(2));
	                if (item) {
	                    if (previousPoint != item.dataIndex) {
	                        previousPoint = item.dataIndex;
	                        $("#tooltip").remove();
	                        var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(2);
	                        var show_label = (item.dataIndex == 0 || item.series.data[item.dataIndex][1] >= item.series.data[item.dataIndex-1][1]) ? 'success' : 'important';
							dashboard.showTooltip(item.series.data[item.dataIndex][2], item.pageX, item.pageY, item.series.data[item.dataIndex][3]+item.series.data[item.dataIndex][1], show_label, chartType);
	                    }
	                } else {
	                    $("#tooltip").remove();
	                    previousPoint = null;
	                }
	            });
	        }
	    } else {
			// Setup page months and data
			var monthOne = [];
			var monthTwo = [];
			var show_days = [];
	
			for (var i = 0; i<result.rows.length; i++) {
				var cur_date = dashboard.formatDate(result.rows[i][0]);
				// if (this is month one) {
					// monthTwo.push([i+1, result.rows[i][1], cur_date, 'Visitors: ']);
				// } else {
					// monthOne.push([i+1, result.rows[i][2], cur_date, 'Visitors: ']);
				// }
				if (i % Math.floor(result.rows.length*.10) == 0 || result.rows.length <= 10) {
					var padDay = (i+1);
					if (padDay.length <= 1) {
						padDay = '0'+padDay;
					}
					show_days.push([i+1, 'Day '+padDay]);
				}
			}
	
	        if ($('#site_statistics').size() != 0) {
	
	            $('#site_statistics_loading').hide();
	            $('#site_statistics_content').show();
	
	            var plot_statistics = $.plot($("#site_statistics"), [{
	                    data: monthOne,
	                    label: 'Page Views'
	                }, {
	                    data: monthTwo,
	                    label: 'Visitors'
	                }
	            ], {
	                series: {
	                    lines: {
	                        show: true,
	                        lineWidth: 2,
	                        fill: true,
	                        fillColor: {
	                            colors: [{
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
	                },
	                grid: {
	                    hoverable: true,
	                    clickable: true,
	                    tickColor: "#eee",
	                    borderWidth: 0
	                },
	                colors: ["#d12610", "#37b7f3", "#52e136"],
	                xaxis: {
						ticks: show_days
					},
	                yaxis: {
	                    ticks: 11,
	                    tickDecimals: 0
	                }
	            });
	
	            // Listen for hover and show/hide tooltip(s)
	            var previousPoint = null;
	            $("#site_statistics").bind("plothover", function (event, pos, item) {
	                $("#x").text(pos.x.toFixed(2));
	                $("#y").text(pos.y.toFixed(2));
	                if (item) {
	                    if (previousPoint != item.dataIndex) {
	                        previousPoint = item.dataIndex;
	                        $("#tooltip").remove();
	                        var x = item.datapoint[0].toFixed(2), y = item.datapoint[1].toFixed(2);
	                        var show_label = (item.dataIndex == 0 || item.series.data[item.dataIndex][1] >= item.series.data[item.dataIndex-1][1]) ? 'success' : 'important';
							dashboard.showTooltip(item.series.data[item.dataIndex][2], item.pageX, item.pageY, item.series.data[item.dataIndex][3]+item.series.data[item.dataIndex][1], show_label, chartType);
	                    }
	                } else {
	                    $("#tooltip").remove();
	                    previousPoint = null;
	                }
	            });
	        }
	    }
	},
	showTooltip: function(title, x, y, data, show_label, chartType) {
		// Display tooltip based on chart type
		var boxes = '';
		if (chartType == 'standard') {
			boxes = '<div class="label label-'+show_label+'">'+data+'<\/div>';
		} else {
			boxes = '<div class="label label-'+show_label+'">'+data[0]+'<\/div><div class="label label-'+show_label+'">'+data[1]+'<\/div>';
		}

		// Show tooltip
		$('<div id="tooltip" class="chart-tooltip"><div class="date">' + title + '<\/div>' + boxes).css({
	        position: 'absolute',
	        display: 'none',
	        top: y - 100,
	        width: 75,
	        left: x - 40,
	        border: '0px solid #ccc',
	        padding: '2px 6px',
	        'background-color': '#fff',
	    }).appendTo("body").fadeIn(200);
	},
	formatDate: function(dateString, showYear) {
		var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
		var fullDate = new Date(dateString.substr(0, 4)+'/'+dateString.substr(4, 2)+'/'+dateString.substr(6,2));
		var padDate = fullDate.getDate().toString();
		if (padDate.length <= 1) {
			padDate = '0'+fullDate.getDate();
		}
		var theYear = (showYear == false) ? '' : ', '+fullDate.getFullYear();
		return months[fullDate.getMonth()]+' '+padDate+theYear;
	},
	nullStartDate: function() {
		var d  = new Date();
		var y  = (d.getUTCMonth() == 0 ) ? d.getUTCFullYear() -1 : d.getUTCFullYear();
		var m  = (d.getUTCMonth() <= 9) ? '0'+d.getUTCMonth() : d.getUTCMonth();
		var sd = '01';
		if (d.getUTCMonth() == '00') {
			y = d.getUTCFullYear() -1;
			m = 11;
		}
		var start = y+'-'+m+'-'+sd;

		return start;	
	},
	nullEndDate: function() {
		var d  = new Date();
		var y  = (d.getUTCMonth() == 0 ) ? d.getUTCFullYear() -1 : d.getUTCFullYear();
		var m  = (d.getUTCMonth() <= 9) ? '0'+d.getUTCMonth() : d.getUTCMonth();
		var ed = dashboard.isLeapYear(y, d.getUTCMonth()-1); 
		if (d.getUTCMonth() == '00') {
			y = d.getUTCFullYear() -1;
			m = 11;
		}
		var end = y+'-'+m+'-'+ed;

		return end;
	},
	isLeapYear: function(year, month) {
		var isLeap = ((year % 4) == 0 && ((year % 100) != 0 || (year % 400) == 0));
		return [31, (isLeap ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
	},
	toggleEdit: function() {
		if (editMode == true) {
			// Save widget changes
			dashboard.saveWidgets();

			// Hide cancel button, set edit button
			$('#cancel-dashboard').hide();
			$('#change-dashboard').removeClass('green').addClass('blue');
			$('#change-dashboard').html('<i class="icon-edit"></i> Edit');

			// Remove remove/edit widget buttons from page
			$('.dragme').remove('.remove_widget');
			$('.dragme').remove('.edit_widget');

			// Set edit mode to false
			editMode = false;
		} else {
			// Put all widgets in edit mode
			dashboard.editWidgets();

			// Show cancel button, set save button
			$('#cancel-dashboard').show();
			$('#change-dashboard').removeClass('blue').addClass('green');
			$('#change-dashboard').html('<i class="icon-ok"></i> Save');

			// Add remove/edit widget buttons to page
			$('.dragme > .portlet-title > .top-news').each(function() {
				$(this).prepend('<a href="javascript:dashboard.removeWidgetConfirm('+$(this).parent().parent().attr('db_id')+');" class="remove_widget glyphicons no-js bin" style="z-index: 999;position: absolute;right: 100px;"><i></i></a>');
				$(this).prepend('<a href="javascript:dashboard.editWidget('+$(this).parent().parent().attr('db_id')+');" class="edit_widget glyphicons no-js pencil" style="z-index: 999;position: absolute;right: 150px;"><i></i></a>');
			});

			// Set edit mode to true
			editMode = true;
		}
	},
	widgetLocationInUse: function(widgetLocationId) {
		if ($('[curloc="'+widgetLocationId+'"]').length > 0) {
			return true;
		} else {
			return false;
		}
	},
	showWidgets: function() {
		// Show all widget options
		$.ajax({
            url: sub+'/dashboard/show_widgets',
            type: 'POST',
            // dataType: 'json',
            success: function(msg) {
				$('#add-widget-form .modal-body').html(msg);
			},
			error: function(err) {
				$('#add-widget-form .modal-body').html('Sorry, there was an error adding your widget. Please refresh the page and try again. ('+JSON.stringify(err)+')');
			}
		});
	},
	addWidget: function(widget_type) {
		// Set hidden field used to store current widget location we're adding a widget to
		$('#widget_type').val(widget_type);

		// Collect widget data
		var allWidgetData = {};
		$('.widget-data').each(function() {
			allWidgetData[$(this).attr('id')] = $(this).val();
		});

		// Get widget data
		allWidgetData['view_type'] = 'config';
		$.ajax({
            url: sub+'/dashboard/get_widget_view',
            type: 'POST',
            data: { 'json': JSON.stringify(allWidgetData) },
            success: function(msg) {
				$('#add-widget-form .modal-body').html(msg);
				$('.modal-body input').first().focus();
			},
			error: function(err) {
				$('#add-widget-form .modal-body').html('Sorry, there was an error adding your widget. Please refresh the page and try again. ('+JSON.stringify(err)+')');
			}
		});
	},
	editWidget: function(widget_db_id) {
		// Set current widget ID
		$('#widget_db_id').val(widget_db_id);

		// Set widget edit form to "loading" text
		$('#add-widget-form .modal-body').html('<div style="width: 100%; text-align: center; font-style: italic; padding: 20px 0px;">Loading<br/><img src="/assets/img/loading.gif" alt="loading"></div>');

		// Show edit widget modal
		$('#add-widget-form').modal('show');

		// Set current widget item
		var widgetItem = $('[db_id="'+widget_db_id+'"]');

		// Get widget data
		$.ajax({
            url: sub+'/dashboard/get_widget_view',
            type: 'POST',
            data: { 'json': '{"db_id":"'+widget_db_id+'","widget_type":"'+widgetItem.attr('widget_type')+'","view_type":"edit_config"}' },
            success: function(msg) {
				$('#add-widget-form .modal-body').html(msg);
				$('.modal-body input').first().focus().select();
			},
			error: function(err) {
				$('#add-widget-form .modal-body').html('Sorry, there was an error adding your widget. Please refresh the page and try again. ('+JSON.stringify(err)+')');
			}
		});
	},
	saveWidget: function() {
		// Setup variable to store widget data
		var allWidgetData = {};

		// If widget db id is set, send it so we update the widget instead of adding a new one
		if ($('#widget_db_id').val() != "") {
			allWidgetData.db_id = $('#widget_db_id').val();
		} 

		// Get widget location
		allWidgetData.widget_location = {};
		allWidgetData.widget_location.column = ($('#widget_column').val() != "") ? $('#widget_column').val() : $('[db_id="'+allWidgetData.db_id+'"]').attr('column');
		allWidgetData.widget_location.row = ($('#widget_row').val() != "") ? $('#widget_row').val() : $('[db_id="'+allWidgetData.db_id+'"]').attr('row');

		// Get widget data
		$('.widget-data').each(function() {
			allWidgetData[$(this).attr('id')] = ($(this).val() != "") ? $(this).val() : $('[db_id="'+allWidgetData.db_id+'"]').attr($(this).attr('id'));
		});

		// Collect widget items
		allWidgetData.widget_items = {};
		$('.widget-item').each(function() {
			if ($(this).attr('id').trim() != "") {
				var item_value = ($(this).val().trim() == "") ? $(this).html() : $(this).val();
				allWidgetData.widget_items[$(this).attr('id')] = item_value;
			}
		});

		// Collect widget array items
		$('.widget-item-array-container').each(function() {
			var item_data = {};
			$(this).find('.widget-item-array').each(function() {
				if ($(this).attr('id').trim() != "") {
					item_data[$(this).attr('id')] = ($(this).val().trim() == "") ? $(this).html() : $(this).val();
					if (!allWidgetData.widget_items["items"]) {
						allWidgetData.widget_items["items"] = [];
					}
				}
			});
			allWidgetData.widget_items["items"].push(item_data);
		});

		// Make sure there is data to save
		if (Object.keys(allWidgetData.widget_items).length > 0) {
			// Save widget
			$.ajax({
	            url: sub+'/dashboard/save_widget',
	            type: 'POST',
	            dataType: 'html',
	            data: { 'json': JSON.stringify(allWidgetData) },
	            success: function(msg) {
	            	if (msg != "ERRROR") {
		            	// Get new item
		            	var newItem = $(msg);
		            	console.log('RESP: '+msg);

	            		// Check if this item already exists
	            		var oldItem = $('.dragme[column="'+newItem.attr('column')+'"][row="'+newItem.attr('row')+'"]');
	            		if (oldItem.length == 0) {
		            		// Append new widget to column
		            		$('#column_'+newItem.attr('column')).append(newItem);

							// Add edit/remove buttons
							newItem.find('.top-news').prepend('<a href="javascript:dashboard.removeWidgetConfirm('+newItem.attr('db_id')+');" class="remove_widget glyphicons no-js bin" style="z-index: 999;position: absolute;right: 100px;"><i></i></a>');
							newItem.find('.top-news').prepend('<a href="javascript:dashboard.editWidget('+newItem.attr('db_id')+');" class="edit_widget glyphicons no-js pencil" style="z-index: 999;position: absolute;right: 150px;"><i></i></a>');

			            	// Minimize new widget
							newItem.find('.portlet-body').slideUp();
							newItem.find('.portlet-title > .tools > .collapse').removeClass('collapse').addClass('expand');
	            		} else {
		            		// Replace current widget with new widget data
		            		oldItem.html(newItem.html());

							// Add edit/remove buttons
							oldItem.find('.top-news').prepend('<a href="javascript:dashboard.removeWidgetConfirm('+oldItem.attr('db_id')+');" class="remove_widget glyphicons no-js bin" style="z-index: 999;position: absolute;right: 100px;"><i></i></a>');
							oldItem.find('.top-news').prepend('<a href="javascript:dashboard.editWidget('+oldItem.attr('db_id')+');" class="edit_widget glyphicons no-js pencil" style="z-index: 999;position: absolute;right: 150px;"><i></i></a>');

			            	// Minimize widget
							oldItem.find('.portlet-body').slideUp();
							oldItem.find('.portlet-title > .tools > .collapse').removeClass('collapse').addClass('expand');
	            		}

						// Hide modal
						$('#add-widget-form').modal('hide');
					} else {
						$('#add-widget-form .modal-body').html('Sorry, there was an error adding your widget. Please refresh the page and try again.');
					}
				},
				error: function(err) {
					console.log('ERROR2: '+JSON.stringify(err));
					$('#add-widget-form .modal-body').html('Sorry, there was an error adding your widget. Please refresh the page and try again.');
				}
			});
		} else {
			// No widget items, so no widget data to save, let the user know
			alert('No data to save!');
		}
	},
	cancelWidget: function() {
		// User cancelled adding widget, hide add widget form
		$('#add-widget-form').modal('hide');	
	},
	removeWidgetConfirm: function(widget_db_id) {
		// Set widget ID to be removed
		$('#remove_widget_id').val(widget_db_id);

		// Show add widget form
		$('#remove-widget-form').modal('show');
	},
	removeWidget: function() {
		// Remove widget from database
		$.ajax({
            url: sub+'/dashboard/remove_widget',
            type: 'POST',
            data: { 'json': '{"db_id":"'+$('#remove_widget_id').val()+'"}' },
            dataType: 'json',
            success: function(msg) {
            	if (msg.status == "success") {
					// Set widget item
					var widgetItem = $('[db_id="'+$('#remove_widget_id').val()+'"]');

					// Show add widget button for this widget since the old is gone
					$('.droppable[column="'+widgetItem.attr('column')+'"][row="'+widgetItem.attr('row')+'"]').find('.add-widget').show();

					// Remove widget item from page
					widgetItem.remove();

					// Remove widget from page
					$('[db_id="'+$('#remove_widget_id').val()+'"]').remove();

					// Set widget id to remove to blank
					$('#remove_widget_id').val('');

	            	// Hide remove widget form
	            	$('#remove-widget-form').modal('hide');

					// Add success message
					add_success(msg.statusmsg);
            	} else {
	            	// Add error message
	            	add_error(msg.statusmsg);
            	}
			},
			error: function(err) {
				$('#remove-widget-form .modal-body').html('Sorry, there was an error removing your widget. Please refresh the page and try again. (Error: )'+JSON.stringify(err));
			}
		});
	},
	editWidgets: function() {
		// Show droppables
		$('.droppable').show();

		// Setup draggables (items that can be drug)
		$('.dragme').draggable({
			revert: 'invalid'
		});

		// Setup droppables (areas items can be drug into)
		$('.droppable').droppable({
			drop: function(event, ui) {
				// Hide the add button for new location
				$(this).find('.add-widget').hide();

				// Show the add button for old location
				$('.droppable[column="'+ui.draggable.attr('column')+'"][row="'+ui.draggable.attr('row')+'"]').find('.add-widget').show();

				// Snap draggable to droppable
				ui.draggable.attr('column', $(this).attr('column'));
				ui.draggable.attr('row', $(this).attr('row'));
				ui.draggable.css('position', 'absolute');
				ui.draggable.css('width', ($(this).width()-15));
				ui.draggable.css('left', ($(this).position().left+10));
				ui.draggable.css('top', ($(this).position().top+40));
			},
			accept: function(draggable) {
				// If object has class 'dragme' and widget location is NOT in use, accept draggable
				if ($(draggable).hasClass('dragme') && !dashboard.widgetLocationInUse($(this).attr('id'))) {
					return true;
				}

				// Object does not have 'dragme' class OR widget locatin is in use, reject draggable
				return false;
			}
        });

		// Collapse widget display
		$('.portlet-title > .tools > .collapse').removeClass('collapse').addClass('expand');

		$('.portlet-body').slideUp().promise().done(function() {
			// Move draggables to their correct dashboard location
			$('.dragme').each(function() {
				// Get widget column and row
				var widgetColumn = $(this).attr('column');
				var widgetRow = $(this).attr('row');

				// Make sure this item is assigned a widget column and row
				if (widgetColumn && widgetRow) {
					// Get droppable element data
					var droppableElement = $('.droppable[column="'+widgetColumn+'"][row="'+widgetRow+'"]');

					// Hide add widget button
					droppableElement.find('.add-widget').hide();

					// Position current page elements
					$(this).css('position','absolute');
					$(this).css('top', $(this).position().top);
					$(this).css('left', $(this).position().left);
					$(this).animate({duration: 500, top: (droppableElement.position().top+40), left: (droppableElement.position().left+10), width: (droppableElement.width()-15)});
				}
			});
		});
	},
	saveWidgets: function() {
		// Maximize all portlets for dragging
		$('.portlet-body').slideDown();
		$('.portlet-title > .tools > .expand').removeClass('expand').addClass('collapse');
		$('.portlet').css('position','relative').css('top','').css('left','');

		// Fade out droppables
		$('.droppable').hide();

		// Move draggables to the correct column
		$('.dragme').each(function() {
			$('#column_'+$(this).attr('column')).append($(this));
		});

		// Sort items inside div so they are in the correct order
		$('.widget_column').each(function() {
			// Get draggables and sort
			var divDraggables = $.makeArray($(this).find('.dragme')).sort(function(a,b){
				return $(a).attr('row') < $(b).attr('row') ? -1 : 1;
			});

			// Get droppables and sort
			var divDroppables = $(this).find('.droppable');

			// Clear div so we can add elements back in sort order
			$(this).empty();

			// Add droppables
			for (var i = 0; i<divDroppables.length; i++) {
				$(this).append(divDroppables[i]);
			}

			// Add draggables
			for (var i = 0; i<divDraggables.length; i++) {
				$(this).append(divDraggables[i]);
			}
		});
		
		// Build item array
		var allWidgets = [];
		$('.dragme').each(function() {
			// Setup object to store current widget data
			var curWidget = {};

			// Set database ID for current widget
			curWidget.db_id = $(this).attr('db_id');

			// Store widget data in object
			curWidget.widget_data = {};

			// Set widget location
			curWidget.widget_data.widget_location = {};
			curWidget.widget_data.widget_location.column = $(this).attr('column');
			curWidget.widget_data.widget_location.row = $(this).attr('row');

			// Push to widget object
			allWidgets.push(curWidget);
		});

		// Submit dashboard settings
		$.ajax({
            url: sub+'/dashboard/save_dashboard',
            type: 'POST',
            data: { 'json': JSON.stringify(allWidgets) },
            dataType: 'json',
            success: function(msg) {
				if (msg.status == 'success') {
					add_success(msg.statusmsg);
				} else {
					add_error(msg.statusmsg);
				}
			},
			error: function(err) {
				add_error('Error: '+JSON.stringify(err));
			}
		});

		// Remove custom CSS so widgets go back to original location
		$('.dragme').removeAttr('style');
	},
	cancelChanges: function() {
		// Maximize all portlets for dragging
		$('.portlet-body').slideDown();
		$('.portlet-title > .tools > .expand').removeClass('expand').addClass('collapse');
		$('.portlet').css('position','relative').css('top','').css('left','');
		
		// Fade out droppables
		$('.droppable').hide();

		// Remove custom CSS so widgets go back to original location
		$('.dragme').removeAttr('style');

		// Hide cancel button, set edit button
		$('#cancel-dashboard').hide();
		$('#change-dashboard').removeClass('green').addClass('blue');
		$('#change-dashboard').html('<i class="icon-edit"></i> Edit');

		// Remove butttons for removing widgets
		$('.dragme').find('.remove_widget').remove();

		// Set edit mode to false
		editMode = false;
	}
	
	
}