<?php if( isset($obj_all_permissions) ) : ?>

	<?php foreach( $obj_all_permissions->get('arr_collection') as $obj_module ) : ?>
	
		<h2 class="form-section"><?php echo ucwords($obj_module->get('module_name')); ?></h2>
		<div class="row-fluid">
			
			<?php foreach( $obj_module->get('obj_component_collection')->get('arr_collection') as $obj_component ) : ?>
			
				<?php if( $obj_component->get('obj_permissions_collection')->size() > 0 ) : ?>
				
				<div class="span2">
				
					<h4 class="form-section"><?php echo $obj_component->get('component_name'); ?></h4>
					<table>
						<thead>
							<tr>
								<td align="left">Permissions</td>
								<td align="left"><i data-original-title="Permission Overriding" data-trigger="hover" data-content="Permission overriding is when you want a specific user to retain the specified permission value regardless of their group permission settings." data-placement="bottom" class="icon-info-sign icon popovers"></i>Override</td>
							</tr>
						</thead>
						<tbody>
					<?php foreach( $obj_component->get('obj_permissions_collection')->get('arr_collection') as $obj_permission ) : ?>
						
							<tr>
								<td>
								
									<i data-original-title="<?php echo ucwords($obj_module->get('module_name')).' > '.ucwords($obj_component->get('component_name')).' > '.$obj_permission->get('permission_name'); ?>" data-trigger="hover" data-content="<?php echo $obj_permission->get('permission_description'); ?>" data-placement="bottom" class="icon-info-sign icon popovers"></i>
									
									<input <?php echo $obj_permission->is_set($arr_user_permissions); ?> type="checkbox" name="permissions[<?php echo $obj_permission->get('id'); ?>]" /><?php echo $obj_permission->get('permission_name'); ?>
								
								</td>
								<td align="center">
									<input type="checkbox" <?php echo $obj_permission->is_overrided($arr_user_permissions); ?> name="overrides[<?php echo $obj_permission->get('id'); ?>]" />
								</td>
							</tr>
						</tbody>
					<?php endforeach; ?>
					</table>
				
				</div>
				
				<?php endif; ?>
				
			<?php endforeach; ?>
		
		</div>
		
	<?php endforeach; ?>

<?php else: ?>
	
	<h4 class="form-section">No Permissions</h4>
	
<?php endif; ?>