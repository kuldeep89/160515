<?php if( isset($obj_account_collection) ) : ?>
	
		<table class="table table-striped table-bordered table-hover dataTable" id="accounts-table">
		   <thead>
		      <tr>
		         <th style="width:8px; display:none;"><input type="checkbox" class="group-checkable" data-set="#users-table .checkboxes" /></th>
		         <th>Company Name</th>
		         <th>Account Type</th>
		         <th>Owner First Name</th>
		         <th class="hidden-480">Owner Last Name</th>		         
		         <th class="hidden-480">Owner Email</th>
		         <th>Contact Phone</th>
		         <th></th>
		      </tr>
		   </thead>
		   <tbody>
		   
			   <?php if( $obj_account_collection->size() > 0 ) : ?>
			   		
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
							<td><a href="#">Company Name</a></td>
							<td>Account Type</td>
							<td>Owner First Name</td>
							<td>Owner Last Name</td>
							<td>Owner Email</td>
							<td>Contact Phone</td>
							<td><span class="label label-success"><a href="#">Edit</a> | <a href="#" class="confirm-delete">Delete</a></span></td>
						</tr>
			   		
			   		<?php endforeach; ?>
		    
				<?php else : ?>
					
					<tr class="odd gradeX">
						<td colspan="7">There are currently no accounts to display.</td>
					</tr>  
					
				<?php endif; ?>
				
		   </tbody>
		</table>
		
		<!-- END EXAMPLE TABLE PORTLET-->
	
<?php else: ?>

	There are no accounts!
	
<?php endif; ?>






						