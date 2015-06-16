jQuery(document).ready(function($) {
    $('#cu_add_mid').click(function() {
        $('#merchant_ids').append('<div> <div class="td_div_body"><input type="text" name="merchant_names[]" placeholder="Merchant Name"></div> <div class="td_div_body"><input type="text" name="merchant_ids[]" placeholder="Merchant ID"></div> <div class="td_div_body"><input type="text" name="price_overrides[]" placeholder="Price Override" /></div><div class="td_div_body"></div> <div class="td_div_body"><input type="button" value="Delete" onclick="javascript:jQuery(this).parent().parent().remove();" class="button button-primary" /></div> </div>');
    });
});