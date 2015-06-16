<?php
	// If user is editing, pull data
	if ($view_type == "edit_config") {
		$arr_widget_config_data = $this->dashboard_model->get_widget_data($db_id);
		$arr_widget_config_data = json_decode($arr_widget_config_data[0]['widget_data'], true);
	}
?>
<a href="javascript:stock_quote.addTicker('AAPL');" class="btn blue"><i class="icon-pencil"></i> Add</a>
<table class="table table-hover">
   <thead>
      <tr>
         <th style="width: 50%">Ticker</th>
         <th>Action</th>
      </tr>
   </thead>
   <tbody id="stock_tickers">
	  <tr id="no_tickers"<?php echo (isset($arr_widget_config_data['widget_items'])) ? ' style="display: none;"' : '' ?>>
         <td colspan="2"><em>No Stock Tickers Added</em></td>
      </tr>
   	  <?php
   	  	if (isset($arr_widget_config_data['widget_items'])) :
   	  		foreach ($arr_widget_config_data['widget_items'] as $key=>$value) :
      ?>
      <tr id="ticker_<?php echo $value ?>">
         <td><input type="text" id="stock_ticker_<?php echo $value ?>" value="<?php echo $value ?>" onkeyup="$(this).attr('id', 'stock_ticker_'+$(this).val());" class="widget-item" /></td>
         <td><a onclick="stock_quote.removeTicker($(this));" class="btn mini red"><i class="icon-trash"></i> Delete</a></td>
      </tr>
      <?php
      		endforeach;
      	endif;
      ?>
   </tbody>
</table>
<div>
	<button class="btn" onclick="dashboard.cancelWidget()"><i class="icon-ok"></i> Cancel</button> 
	<button class="btn green" id="save_data" onclick="javascript:dashboard.saveWidget();"><i class="icon-ok"></i> Save</button>
</div>