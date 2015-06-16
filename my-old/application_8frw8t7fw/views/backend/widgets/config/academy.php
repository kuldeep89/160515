<?php
	// If user is editing, pull data
	if ($view_type == "edit_config") {
		$arr_widget_config_data = $this->dashboard_model->get_widget_data($db_id);
		$arr_widget_config_data = json_decode($arr_widget_config_data[0]['widget_data'], true);
	}
?>
Academy Category<br>
<select id="academy_entry_category_id" class="widget-item">
<?php
	// Get categories
	foreach ($this->academy_model->get_categories() as $cur_category) {
		echo '<option value="'.$cur_category['academy_entry_category_id'].'"';
			echo (isset($arr_widget_config_data["widget_items"]["academy_entry_category_id"]) && $arr_widget_config_data["widget_items"]["academy_entry_category_id"] == $cur_category['academy_entry_category_id']) ? ' selected' : '';
			echo '>'.$cur_category['name'].'</option>';
	}
?>
</select><br>
<br/>
<button class="btn" onclick="dashboard.cancelWidget()"><i class="icon-ok"></i> Cancel</button> 
<button class="btn green" onclick="javascript:dashboard.saveWidget();"><i class="icon-ok"></i> Save</button>