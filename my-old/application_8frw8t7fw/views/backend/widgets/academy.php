<?php
	// Get category information
	$category = $this->widgets_model->get_category($widget_items['academy_entry_category_id']);

	// Get articles
	$obj_article_collection = $this->academy_model->get_categorized_entries($widget_items['academy_entry_category_id'], false, 5);

	// Get article tags for category
	$arr_tags = $obj_article_collection->get_tags();
?>
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_type="3" academy_entry_category_id="<?php echo $widget_items['academy_entry_category_id'] ?>" db_id="<?php echo $db_id ?>">
	<div class="portlet-title">
		<div class="top-news">
			<a href="<?php echo site_url('academy/category/'.$widget_items['academy_entry_category_id']); ?>" class="btn <?php echo ($category['category']['color'])? $category['category']['color']:category_color($widget_items['academy_entry_category_id']); ?>"><span><?php echo $category['category']['name']; ?></span>
			<em><?php echo implode(', ', array_splice($arr_tags, 0, 3)); ?></em> <i class="<?php echo $category['category']['icon']; ?> top-news-icon"></i></a>
		</div>
	
		<div class="tools" style="margin-top: -2.5em;">
			<a href="javascript:;" class="collapse btn-group"></a>
			<!-- <a href="#portlet-config" data-toggle="modal" class="config"></a> -->
		</div>
	</div>
	<div class="portlet-body">
	
		<?php foreach( $obj_article_collection->get('arr_collection', array('limit' =>8)) as $obj_entry ) : ?>
		
		<?php $this->load->view('backend/object-templates/academy/academy-article-listing', array('obj_entry'=>$obj_entry)); ?>
		
		<?php endforeach; ?>
	</div>
</div>
