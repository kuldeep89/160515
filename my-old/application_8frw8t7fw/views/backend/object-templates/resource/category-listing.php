<?php
	$arr_tags	= $obj_entry_collection->get_tags();
?>

<!-- Category Title -->
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_id="<?php echo $widget_id; ?>" resource_entry_category_id="<?php echo $resource_entry_category_id ?>">
	<div class="portlet-title">
		<div class="top-news">
			<a href="<?php echo site_url('resource/category/'.$obj_entry_collection->get('category_id')); ?>" class="btn <?php echo category_color($obj_entry_collection->get('category_id')); ?>"><span><?php echo $obj_entry_collection->get('category'); ?></span>
			<em><?php echo implode(', ', array_splice($arr_tags, 0, 3)); ?></em> <i class="<?php echo category_icon($obj_entry_collection->get('category_id')); ?> top-news-icon"></i></a>
		</div>
	
	<div class="tools" style="margin-top: -2.5em;">
		<a href="javascript:;" class="collapse btn-group"></a>
		<!-- <a href="#portlet-config" data-toggle="modal" class="config"></a> -->
	</div>
	
</div>
<!-- End of Category Title -->

<div class="portlet-body">
	<?php foreach( $obj_entry_collection->get('arr_collection', array('limit' =>8)) as $obj_entry ) : ?>
	
	<?php $this->load->view('backend/object-templates/resource/resource-article-listing', array('obj_entry'=>$obj_entry)); ?>
	
	<?php endforeach; ?>
	</div>
</div>
<!-- END Portlet PORTLET-->