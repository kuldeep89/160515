var ajaxurl = '/wp-admin/admin-ajax.php';

var MerchantIdsReport = {
    export: function(batch_id) {
        // Disable button and let user know we are exporting
        jQuery('#export-merchant-ids').val('Exporting...');
        jQuery('#export-merchant-ids').attr('disabled', 'disabled');

		jQuery.ajax({
			url: ajaxurl,
			data: { action: 'export_merchant_ids' }
		}).done(function(resp) {
			// Try to parse response JSON
			var response = jQuery.parseJSON(resp);		
			var iframe_location = "/wp-content/plugins/transactional-data/export_csv.php?file_name="+response.file_name;

			// Set iFrame URL to URL of file
			jQuery('#exportIframe').attr('src', iframe_location);

            // Disable button and let user know we are exporting
            jQuery('#export-merchant-ids').val('Export');
            jQuery('#export-merchant-ids').removeAttr('disabled');
		});	
	}
}