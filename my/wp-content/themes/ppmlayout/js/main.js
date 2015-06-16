var ajaxurl = '/wp-admin/admin-ajax.php';

jQuery(document).ready(function() { 	
 	App.init(); // initlayout and core plugins
 	Index.init();

     // Merchant ID selector
     $('.mid_selector').click(function() {
        // Disable summary dropdown
        $('#goal_select').attr('disabled','disabled');

        // Show loading div
        $('.mid_loading').show();

		// Clear merchant search box
		if ($('#search_merchant_id').length > 0) {
    		$('#search_merchant_id').val('');
		}

        // Clear cash advance amount
		if ($('.sales_amount').length > 0) {
    		//$('.sales_amount,.cash_advance_amount,.ppttd_section_result,.goalAmount,.circle-text').html('--');
    		$('.sales_amount,.ppttd_section_result,.goalAmount,.circle-text').html('--');
        }

        // Get merchant info
        var merchant_data = $(this).html();
        var merchant_name = (merchant_data.indexOf('(') >= 0) ? merchant_data.split(' (')[0] : merchant_data;
        var merchant_id = (merchant_data.indexOf('(') >= 0) ? merchant_data.match(/\(([^)]+)\)/)[1] : merchant_data;

        // Switch MIDs, update dashboard
        update_dashboard(merchant_name, merchant_id, null);
     });

	/*  The Following Code Needs to Be Properly Rebuilt!  */

	// Adds appropriate icon for each menu item.
	var navIcons = function(elementTitle, iconClass) {
		$('a[title='+elementTitle+']').find('div').removeClass('icon-briefcase icon-circle-arrow-up');
		$('a[title='+elementTitle+']').find('div').addClass(iconClass);
	}
	navIcons('Dashboard', 'saltsha-nav-icon saltsha-nav-dashboard'); 
	navIcons('Academy', 'saltsha-nav-icon saltsha-nav-academy');
	navIcons('SalesData', 'saltsha-nav-icon saltsha-nav-transactional-reporting');
	navIcons('Customers', 'saltsha-nav-icon saltsha-nav-customers');
	navIcons('ReturningCustomers', 'saltsha-nav-icon saltsha-nav-transactional-reporting');
	navIcons('MonthlyStatements', 'saltsha-nav-icon saltsha-nav-transactional-reporting');
	navIcons('Suport', 'saltsha-nav-icon saltsha-nav-support');
	navIcons('FAQ', 'saltsha-nav-icon saltsha-nav-faq');
	navIcons('Resources', 'saltsha-nav-icon saltsha-nav-resources');
	navIcons('PCICompliance', 'saltsha-nav-icon saltsha-nav-pcicompliance');
	navIcons('TerminalSupplies', 'saltsha-nav-icon saltsha-nav-terminal-supplies');

	// Adds arrow for dropdown menu items.
	$('ul.sub-menu').prev().append('<span class="arrow"></span>');

	// Uses "span.selected" for active tabs.
	$('.current_page_item').find('a').append('<span class="selected"></span>');
	$('.current-menu-parent').find('span.arrow').toggleClass('arrow selected');

	// Sets submenu to display
	$('.current-menu-parent').find('a').trigger('click');

	// Hide 'login' link if logged in
	$(".woocommerce-info").each(function(){
		if($(this).find('a').text() == "Click here to login"){
			$(".showlogin").parent().remove(); 
		}
	});

	// Move coupon code
	$('.checkout_coupon').appendTo('#coupon_div').show();

	// Set active menu item
	$('.current-menu-item,.current-menu-parent').find('.saltsha-nav-icon').css('background-position-x', '-18px');
	
	
	$(".button.cancel").click(function(){
	    return confirm("Are you sure you want to cancel?");
	})
	
	// Show/hide the review form on resources page
	$(".leave_review").toggle(function () {
	    $("#respond").css({display: "block"});
    	return false;
	}, function () {
	    $("#respond").css({display: "none"});
    	return false;
	});
	
	$('.span2.comment_review_area').append(function() {
	    return $(this).prev('.span10').find('.rating-container');
	});
	
	$('body').on('click', '#editGoals', function(e){
		// e.preventDefault();
		
		
		if($('#setGoal').is(":visible")){
			$('#setGoal').hide();
			$('#goalBox').show();
		} else{
			$('#setGoal').show();
			$('#goalBox').hide();
		}
		
		// console.log('THIS IS A TEST');
		return false;
	});
	
	
	$('body').on('click', '.alert-close', function(e){
		var alertID = $(this).data('alertid');
		var alert_count = $('.has_alert span');
		if( alert_count.html() > 1 ) {
			alert_count.html(alert_count.html()-1);
		} else {
			$('.alert_link a').removeClass('has_alert').html('');
		}
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: { 
				action:'update_alert_read_status',
				alertID: alertID
			},
			success:function(data) {
				//console.log(data);
			},
			error: function(errorThrown){
				console.log(errorThrown);
			}
		});
	});
	


	$('#cashAdvanceForm').submit(function(e) {
		e.preventDefault();
		
		var eligible = $('.cash_advance_amount').html();
		$('#elig').val(eligible);
	
		var cashFormData = new FormData(this);
		var data = cashFormData;
		
		$('#sendButtons').hide();
		$('#sending').show();
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: data,
            processData: false,
			contentType: false,
			success: function(response) {
				//alert(response);
				$('#responseMsg').html(response);
				$('#sending').hide();
				$('#closeModal').show();
			},
			error: function(response) {
				//alert(response);
				$('#responseMsg').html(response);
				$('#sending').hide();
				$('#closeModal').show();
			}
		});
	});
	
	$('#weeklyGoal').circliful();
	$('#monthlyGoal').circliful();
	$('#yearlyGoal').circliful();
});

// Load pricing scripting
function change_subscription(recurring_time) {
	if ($('#'+recurring_time).hasClass('choose_sub_active') == false) {
		// Block interface
		$('#order_review').block({
			message: '<img src="/wp-content/plugins/woocommerce/assets/images/ajax-loader@2x.gif" style="width:16px;" />',
			css: {
				border: '0px',
				backgroundColor: 'transparent',
				width: '100%',
				top: '50%',
				left: '0px'
			},
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.6,
				cursor: 'wait'
			}
		});

		// Submit subscription change request
		$.ajax({
			url: '/wp-admin/admin-ajax.php',
			data: { action: 'change_subscription_ajax', subscription_id: $('#'+recurring_time).attr('subscription_id') }
		}).done(function(resp) {
			// Unblock subscription picker
			// $('#order_review').unblock();

			// Show subscription has changed
			$('#order_review_container').html(resp);
		});
	}
}

if(location.pathname == '/resources/') {
	$('#menu-item-2023').addClass('open');
	$('#menu-item-2023 > ul').css('overflow', 'hidden');
	$('#menu-item-2023 > ul').css('display', 'block');
	$('#menu-item-2023 > ul').css('display', 'block');
	$('#menu-item-2023 > ul').css('display', 'block');
	$('#menu-item-2022').addClass('current-page-item current-page-parent current-menu-item');
}


(function($) {
	$('#goal_select').change(function(){
        // Show loading div
        $('.mid_loading').show();

        // Disable this dropdown
        $(this).attr('disabled','disabled');

		// Show loading text
		$('.sales_amount,.ppttd_section_result').html('--');

		// Update dashboard
		update_dashboard(null, null, $(this).val());
	});
	
})(jQuery);

/**
 * Update dashboard data
 */
function update_dashboard(merchant_name, merchant_id, goal_select) {
    // Sned AJAX request
	$.ajax({
		url: ajaxurl,
		type: 'POST',
		dataType: 'json',
		data: { 
			action:'change_active_mid',
			merchant_id: merchant_id,
			merchant_name: merchant_name,
			goal_select: goal_select
		},
		success:function(data) {
    		// Change merchant info if not null
    		if (merchant_id != null && merchant_name != null) {
    			// Show new MID data
    			if (merchant_name == merchant_id) {
        			$('.show_mid').html(merchant_id);
    			} else {
        			$('.show_mid').html(merchant_name+' ('+merchant_id+')');
    			}

                // Set global merchant id
                active_mid = merchant_id;
            }

            // Enable goal select dropdown
            $('#goal_select').removeAttr('disabled');
            $('#goal_select').val(data.goal_select);

            // Hide active selector
            $('.mid_selector').show();
            $('.'+active_mid).hide();

            // Hide loading div
            $('.mid_loading').hide();

            // Show charts
            if (typeof Charts !== 'undefined') {
                Charts.show_sales($('#form-date-range').data('daterangepicker').startDate, $('#form-date-range').data('daterangepicker').endDate, active_mid);
                Charts.show_summary(active_mid);
            }

            // Get customers
            if (typeof Customers !== 'undefined') {
                Customers.get(active_mid);
            }

            // Get statements
            if (typeof Statements !== 'undefined') {
                Statements.get();
            }

            // Update cash advance amount
            if ($('.sales_amount').length > 0) {
                // Set cash advance amount
                //$('.cash_advance_amount').html(data.dashboard_cash_advance);

                // Set total sales
                $('.ppttd_section_total,.sales_amount').html(data.total);

                // Set new customers
                $('.ppttd_section_total_customers').html(data.total_customers);

                // Set returning customers
                $('.ppttd_section_returning_customers').html(data.returning_customers);

                // Set weekly goal
                $('.goal_weekly').html('$'+data.goal_weekly);

                // Set monthly goal
                $('.goal_monthly').html('$'+data.goal_monthly);

                // Set yearly goal
                $('.goal_yearly').html('$'+data.goal_yearly);

                // Set weekly, monthly, and yearly goal percentages
                $('#weeklyGoal').data('percent', data.weekly_percentage).data('text', data.weekly_percentage+'%').html('').circliful();
                $('#monthlyGoal').data('percent', data.monthly_percentage).data('text', data.monthly_percentage+'%').html('').circliful();
                $('#yearlyGoal').data('percent', data.yearly_percentage).data('text', data.yearly_percentage+'%').html('').circliful();
            }
		},
		error: function(errorThrown){
            // Hide loading div
            $('.mid_loading').hide();

            // Alert client that there was an error
			alert('There was an error changing the active merchant ID, please try again.');
		}
	});
}