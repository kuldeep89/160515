<?php
	// If user is editing, pull data
	if ($view_type == "edit_config") {
		$arr_widget_config_data = $this->dashboard_model->get_widget_data($db_id);
		$arr_widget_config_data = json_decode($arr_widget_config_data[0]['widget_data'], true);
	}
?>
City, State or Zip Code<br>
<input type="text" id="city_or_zip"<?php echo (isset($arr_widget_config_data)) ? ' value="'.$arr_widget_config_data['widget_items']['city_or_zip'].'"' : '' ?> class="widget-item"><br>
<br/>
<div>
	<button class="btn" onclick="dashboard.cancelWidget()"><i class="icon-ok"></i> Cancel</button> 
	<button class="btn green" onclick="javascript:dashboard.saveWidget();"><i class="icon-ok"></i> Save</button>
</div>