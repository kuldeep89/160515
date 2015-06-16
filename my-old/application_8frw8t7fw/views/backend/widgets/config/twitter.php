<?php
	// If user is editing, pull data
	if ($view_type == "edit_config") {
		$arr_widget_config_data = $this->dashboard_model->get_widget_data($db_id);
		$arr_widget_config_data = json_decode($arr_widget_config_data[0]['widget_data'], true);
	}
?>
Follow user or hashtag?<br>
<select id="follow_type" class="widget-item">
	<option value="user"<?php echo (isset($arr_widget_config_data["widget_items"]["follow_type"]) && ($arr_widget_config_data["widget_items"]["follow_type"] == "user" || $arr_widget_config_data["widget_items"]["follow_type"] == "@user")) ? ' selected' : ''; ?>>@user</option>
	<option value-"hashtag"<?php echo (isset($arr_widget_config_data["widget_items"]["follow_type"]) && ($arr_widget_config_data["widget_items"]["follow_type"] == "hashtag" || $arr_widget_config_data["widget_items"]["follow_type"] == "#hashtag")) ? ' selected' : ''; ?>>#hashtag</option>
	<option value-"keyword"<?php echo (isset($arr_widget_config_data["widget_items"]["follow_type"]) && $arr_widget_config_data["widget_items"]["follow_type"] == "keyword") ? ' selected' : ''; ?>>keyword</option>
</select><br/><br/>
User or hashtag to follow<br>
<input type="text" id="who_to_follow" value="<?php echo isset($arr_widget_config_data["widget_items"]["who_to_follow"]) ? $arr_widget_config_data["widget_items"]["who_to_follow"] : ''; ?>" class="widget-item"><br>
<br/>
Number of tweets to show<br/>
<input type="text" id="num_tweets" value="<?php echo isset($arr_widget_config_data["widget_items"]["num_tweets"]) ? $arr_widget_config_data["widget_items"]["num_tweets"] : '5'; ?>" class="widget-item"><br>
<br/>
<div>
	<button class="btn" onclick="dashboard.cancelWidget()"><i class="icon-ok"></i> Cancel</button> 
	<button class="btn green" onclick="javascript:dashboard.saveWidget();"><i class="icon-ok"></i> Save</button>
</div>