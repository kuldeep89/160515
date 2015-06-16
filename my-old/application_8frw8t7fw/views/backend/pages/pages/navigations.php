<?php	
	
	$arr_js[]	= 'plugins/jquery-nestable/jquery.nestable.js';
	$arr_js[]	= 'scripts/ui-nestable.js';
	$arr_js[]	= 'scripts/navigations.js';
	
	$arr_css[]	= 'plugins/jquery-nestable/jquery.nestable.css';
	$this->load->view('backend/includes/header', array('arr_css' => $arr_css));
	
?>

<div class="row-fluid">
    <div class="span12">
        <div class="portlet box green">
            <div class="portlet-title">
                <div class="caption">
                    Navigations
                </div>

                <div class="tools">
                    <a href="javascript:;" class="collapse"></a> <a href="#portlet-config" data-toggle="modal" class="config"></a> <a href="javascript:;" class="reload"></a> <a href="javascript:;" class="remove"></a>
                </div>
            </div>

            <div class="portlet-body form">
                <div class="clearfix">
                    <div class="btn-group">
                        <a href="<?php echo site_url('pages/add-page'); ?>" id="sample_editable_1_new" class="btn green">Add New </a>
                    </div>

                    <table class="table table-striped table-bordered table-hover" id="sample_1">
                        <thead>
                            <tr>

                                <th>Navigation ID</th>

                                <th class="hidden-480">Navigation Name</th>
                                <th>Action</th>

                            </tr>
                        </thead>

                        <tbody>
                        	
                        	<?php if( isset($arr_navigations) && count($arr_navigations) > 0 ) : ?>
                        		
                        		<?php foreach( $arr_navigations as $arr_navigation ) : ?>
                        			
                        			<tr>
                        				<td><?php echo $arr_navigation['nav_id']; ?></td>
                        				<td><?php echo $arr_navigation['name']; ?></td>
                        				<td><span class="label label-success"><a style="color: #fff !important;" href="<?php echo site_url('pages/navigation/'.$arr_navigation['nav_id']); ?>">Edit</a> | <a onclick="return confirm_delete();" href="<?php echo site_url('pages/delete-navigation/'.$arr_navigation['nav_id']); ?>" style="color: #fff !important;" >Delete</a></span></td>
                        			</tr>
                        			
                        		<?php endforeach; ?>
                        		
                        	<?php else: ?>
                        
	                            <tr class="odd gradeX">
	                                <td colspan="6">There are currently no navigations.</td>
	                            </tr>
                            
                            <?php endif; ?>
                            
                        </tbody>
                    </table>
                </div>

                <div>
                    <button type="button" class="btn blue" id="save-navigation">Save</button> <button type="button" class="btn">Cancel</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

	$this->load->view('backend/includes/footer', array('arr_js' => $arr_js));