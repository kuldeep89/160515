<?php	
	
	$arr_js[]	= 'plugins/jquery-nestable/jquery.nestable.js';
	$arr_js[]	= 'scripts/ui-nestable.js';
	$arr_js[]	= 'scripts/navigations.js';
	
	$arr_css[]	= 'plugins/jquery-nestable/jquery.nestable.css';
	$this->load->view('backend/includes/header', array('arr_css' => $arr_css));
	
?>
<input type="hidden" id="nav_id" value="<?php echo $nav_id; ?>" />
<div class="row-fluid">
    <div class="span6">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    Navigation
                </div>
                <div class="tools">
                    <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a>
                </div>
            </div>

            <div class="portlet-body form-horizontal">
                <div class="dd" id="nestable_list_1">
                    <ol class="dd-list">
                      	
						<?php
						
							$this->navigation_lib->print_edit_navigation($arr_order, $obj_pages);
							
						?>
                      	
                    </ol> 
                </div>
                <div>
                	<button type="button" class="btn blue" id="save-navigation"><i class="icon-ok"></i> Save</button> <button type="button" class="btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>
	
	<div class="span6">
		<div class="portlet box grey">
		
			<div class="portlet-title">
			
				<div class="caption">
					Available Pages
				</div>
			
				<div class="tools">
				 	<a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a>
				</div>
				 
			</div><!-- End of Portlet Title -->
			
			<div class="portlet-body">
				<div class="dd" id="nestable_list_2">
					
					<ol class="dd-list">
						
						<?php foreach( $obj_pages->get('arr_collection') as $obj_page ) : ?>
						
							<li class="dd-item" data-id="<?php echo $obj_page->get('id'); ?>">
								<div class="dd-handle">
									<?php echo $obj_page->get('name'); ?>
								</div>
							</li>
						
						<?php endforeach; ?>
						
					</ol>
					
				</div><!-- End of Nestable List 2 -->
			</div><!-- End of Portlet Body -->
			
		</div><!-- End of Portlet -->
	</div><!-- End of Span6 -->
</div>

<?php

	$this->load->view('backend/includes/footer', array('arr_js' => $arr_js));