var ajaxurl = '/wp-admin/admin-ajax.php';

var MerchantDataReport = {
    export: function(batch_id) {
        // Disable button and let user know we are exporting
        jQuery('#export-merchants-no-data').val('Exporting...');
        jQuery('#export-merchants-no-data').attr('disabled', 'disabled');

		jQuery.ajax({
			url: ajaxurl,
			data: { action: 'export_merchant_data' }
		}).done(function(resp) {
			// Try to parse response JSON
			var response = jQuery.parseJSON(resp);		
			var iframe_location = "/wp-content/plugins/transactional-data/export_csv.php?file_name="+response.file_name;

			// Set iFrame URL to URL of file
			jQuery('#exportIframe').attr('src', iframe_location);

            // Disable button and let user know we are exporting
            jQuery('#export-merchants-no-data').val('Export');
            jQuery('#export-merchants-no-data').removeAttr('disabled');
		});	
	}
}