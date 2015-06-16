<?php
	// If user is editing, pull data
	if ($view_type == "edit_config") {
		$arr_widget_config_data = $this->dashboard_model->get_widget_data($db_id);
		$arr_widget_config_data = json_decode($arr_widget_config_data[0]['widget_data'], true);
	}
?>
RSS feed URL<br>
<input type="text" id="rss_feed_url"<?php echo (isset($arr_widget_config_data)) ? ' value="'.$arr_widget_config_data['widget_items']['rss_feed_url'].'"' : '' ?> class="widget-item"><br>
<br/>
Number of articles to show<br/>
<input type="text" id="rss_feed_num_articles"<?php echo (isset($arr_widget_config_data)) ? ' value="'.$arr_widget_config_data['widget_items']['rss_feed_num_articles'].'"' : '' ?> class="widget-item"><br>
<br/>
<div>
	<button class="btn" onclick="dashboard.cancelWidget()"><i class="icon-ok"></i> Cancel</button> 
	<button class="btn green" onclick="javascript:dashboard.saveWidget();"><i class="icon-ok"></i> Save</button>
</div>