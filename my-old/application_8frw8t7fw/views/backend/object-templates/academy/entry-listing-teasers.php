<?php if( isset($obj_entry_collection) && $obj_entry_collection->size() > 0 ) : ?>
	<?php
		$track	= 0;
	?>
	
	<?php foreach( $obj_entry_collection->get('arr_collection') as $obj_entry ) : ?>

		<?php if( $track === 0 ) : ?>
		
			<div class="row-fluid">
				
				
		<?php endif; ?>
		
		<?php
			$track++;
		?>		
				<div class="span3">
					<?php $this->load->view('backend/object-templates/academy/academy-article-listing', array('obj_entry'=>$obj_entry)); ?>
				</div>
				
		<?php if( ($track % 4) == 0 ) : ?>

				
			</div>
			<div class="row-fluid">

		<?php endif; ?>
	
	<?php endforeach; ?>
		
			</div>
			
<?php else: ?>

	<div class="row-fluid">
		<div class="span12">
			<p>No related articles.</p>
		</div>
	</div>

<?php endif; ?>