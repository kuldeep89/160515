<?php if( isset($obj_user_collection) && count($obj_user_collection->size() > 0) ) : ?>
	
		<table class="table table-striped table-bordered table-hover dataTable" id="users-table">
		   <thead>
		      <tr>
		         <th style="width:8px; display:none;"><input type="checkbox" class="group-checkable" data-set="#users-table .checkboxes" /></th>
		         <th>Username</th>
		         <th>First Name</th>
		         <th class="hidden-480">Last Name</th>
		         <th class="hidden-480">Company</th>
		         <th class="hidden-480">Email</th>
		         <th>Phone</th>
		         <th></th>
		      </tr>
		   </thead>
		   <tbody>
		   
			   <?php if( isset($obj_user_collection) && $obj_user_collection->size() > 0 ) : ?>
			   		
			   		<?php foreach( $obj_user_collection->get('arr_collection') as $obj_user ) : ?>
			   				
			   				<?php 
			   				
			   					$type	= 'member';
			   				
			   					//Member or Moderator?
			   					if( $obj_user->get('account') == '1' || $obj_user->get('account') == 0 ) {
				   					$type	= 'moderator';
			   					}
			   				
			   				?>
			   				
		   				<tr class="odd gradeX">
		   					<td style="display:none;"><input type="checkbox" class="checkboxes" value="<?php echo $obj_user->get('id'); ?>" /></td>
							<td><a href="<?php echo site_url('users/'.$type.'/'.$obj_user->get('id')); ?>"><?php echo $obj_user->get('username'); ?></a></td>
							<td><?php echo $obj_user->get('first_name'); ?></td>
							<td><?php echo $obj_user->get('last_name'); ?></td>
							<td><?php echo $obj_user->get('company'); ?></td>
							<td><?php echo $obj_user->get('email'); ?></td>
							<td><?php echo $obj_user->get('phone'); ?></td>
							<td><span class="label label-success"><a href="<?php echo site_url('users/'.$type.'/'.$obj_user->get('id')); ?>">Edit</a> | <a href="<?php echo site_url('users/remove-'.$type.'/'.$obj_user->get('id')); ?>" class="confirm-delete">Delete</a></span></td>
						</tr>
			   		
			   		<?php endforeach; ?>
		    
				<?php else : ?>
					
					<tr class="odd gradeX">
						<td colspan="7">There are currently no users to display.</td>
					</tr>  
					
				<?php endif; ?>
				
		   </tbody>
		</table>
		
		<!-- END EXAMPLE TABLE PORTLET-->
	
<?php else: ?>

	There are no users!
	
<?php endif; ?>






						