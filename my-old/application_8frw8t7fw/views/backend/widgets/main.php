Select the type of widget you want to add.<br/>
<br/>
<?php
	// Get all widgets
	$all_widgets = $this->dashboard_model->get_all_widgets();

	// Loop through and display all widgets
	foreach ($all_widgets as $cur_widget) :
?>
<a href="javascript:dashboard.addWidget(<?php echo $cur_widget['widget_type'] ?>);" class="<?php echo $cur_widget['widget_icon'] ?>">
	<i></i><br/><br/><br/>
	<?php echo $cur_widget['widget_display_name'] ?>
</a>
<?php
	endforeach;
?>