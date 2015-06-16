/* 

The account page didn't like this very much... I replaced all occurances of $td_sub with $
var $td_sub = jQuery.noConflict();

*/

$(document).ready(function() {
	// Change subscription(s)
	$('.send_report').click(function() {
		var ppttd_the_subscription = $(this).attr('name');
		$('#saving_subscription').show();
		$.ajax({
			url: '/wp-admin/admin-ajax.php?subscription_timeframe='+ppttd_the_subscription+'&subscribe='+$(this).is(':checked'),
			data: { action: 'change_subscription' }
		}).done(function(resp) {
			$('#saving_subscription').hide();
		});
	});

	// Change "Week Starts On" day
	$('#ppttd_week_starts_on').change(function() {
		$('#saving_week_starts_on').show();
		$.ajax({
			url: '/wp-admin/admin-ajax.php?week_starts_on='+$('#ppttd_week_starts_on').val(),
			data: { action: 'change_week_starts_on' }
		}).done(function(resp) {
			$('#saving_week_starts_on').hide();
		});
	});
	
	// Show/hide alert settings if "Alerts Enabled" is not checked.
	$('body').on("click", "#ppttd_enable_alerts", function(){
		if( $(this).is(':checked') ){
			$('#alert_settings_table').show();
		} else if( !$(this).is(':checked') ) {
			$('#alert_settings_table').hide();
		}
	});
	
	// Update Alert Settings
	$('body').on("submit", "#alertsForm", function(){
				
		var enable_alerts = $('#ppttd_enable_alerts').is(':checked');
		//var alert_chargebacks = $('#ppttd_alert_chargebacks').is(':checked');
		//var alert_retrievals = $('#ppttd_alert_retrievals').is(':checked');
		//var alert_duplicate_transaction = $('#ppttd_alert_duplicate_transaction').is(':checked');
		
		var alert_batch_above = $('#ppttd_alert_batch_above').val();
		var alert_batch_below = $('#ppttd_alert_batch_below').val();
		var alert_no_processing = $('#ppttd_alert_no_processing').val();
		
		//var alertText = 'Enable alerts '+enable_alerts+'\nChargeback alerts '+alert_chargebacks+'\nRetrieval alerts '+alert_retrievals+'\nDuplicate alerts '+alert_duplicate_transaction+'\nBatch above '+alert_batch_above+'\nBatch below '+alert_batch_below+'\nNo processing in '+alert_no_processing;
		//alert(alertText);
		
		$.ajax({
			//url: '/wp-admin/admin-ajax.php?ppttd_enable_alerts='+enable_alerts+'&ppttd_alert_chargebacks='+alert_chargebacks+'&ppttd_alert_retrievals='+alert_retrievals+'&ppttd_alert_duplicate_transaction='+alert_duplicate_transaction+'&ppttd_alert_batch_above='+alert_batch_above+'&ppttd_alert_batch_below='+alert_batch_below+'&ppttd_alert_no_processing='+alert_no_processing,
			url: '/wp-admin/admin-ajax.php?ppttd_enable_alerts='+enable_alerts+'&ppttd_alert_batch_above='+alert_batch_above+'&ppttd_alert_batch_below='+alert_batch_below+'&ppttd_alert_no_processing='+alert_no_processing,
			data: { action: 'update_alert_settings' }
		}).done(function(resp) {
			//$('#saving_subscription').hide();
			alert('Settings Saved');
		});
		
		return false;
	});
});