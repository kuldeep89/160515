<div class="wrap" id="manage-designs-page">
	<h2><i class="fa fa-picture-o"></i> <?php _e('Manage Fancy Designs', 'radykal'); ?></h2>
	<?php

		if( isset($_POST['save_designs']) ) {

			check_admin_referer( 'fpd_save_designs' );

			//get terms
			$category_ids = get_terms( 'fpd_design_category', array(
			 	'hide_empty' => false,
			 	'fields' => 'ids'
			));
			
			$args = array(
				'posts_per_page' => -1,
				'post_type' => 'attachment',
				'tax_query' => array(
			        array(
			        'taxonomy' => 'fpd_design_category',
			        'field' => 'term_id',
			        'terms' => $category_ids)
			    )
			);

			//get all attachments and remove the from category
			$designs = get_posts( $args );
			foreach( $designs as $design ) {
				wp_delete_object_term_relationships($design->ID, 'fpd_design_category');
			}

			//update menu order for attachment and set terms for attachments
			$index = 0;

			$temp_category_slug = NULL;
			foreach( $_POST['image_ids'] as $image_id ) {
				if($temp_category_slug != $_POST['category_slugs'][$index]) {
					$temp_category_slug = $_POST['category_slugs'][$index];
					$category_index = 0;
				}
				$attachment = array(
					'ID'           => $image_id,
					'menu_order' => $category_index
				);
				wp_update_post( $attachment );
				wp_set_object_terms( $image_id, $_POST['category_slugs'][$index], 'fpd_design_category', true );
				update_post_meta( $image_id, 'fpd_parameters', $_POST['parameters'][$index]);
				$index++;
				$category_index++;
			}

			echo '<div class="updated"><p><strong>'.__('Designs saved.', 'radykal').'</strong></p></div>';
		}

	?>
	<br /><br />
	<a href="<?php echo admin_url('edit-tags.php?taxonomy=fpd_design_category&post_type=attachment'); ?>"><?php _e('Create a new category', 'radykal'); ?></a>
	<br /><br />
	<form method="post" id="fpd-designs-form">
		<?php
			$category_terms = get_terms( 'fpd_design_category', array(
			 	'hide_empty' => false
			 ));

			 foreach($category_terms as $category_term) {
				 ?>
				 <div class="postbox fpd-design-category" id="<?php echo $category_term->slug; ?>">
				 	<h3 class="fpd-clearfix">
				 		<span><?php echo $category_term->name; ?></span>
				 		<button class="button button-secondary fpd-add-designs"><?php _e('Add Designs', 'radykal'); ?></button>
				 	</h3>
				 	<div class="inside">
					 	<ul class="fpd-clearfix">
					 	<?php
					 	$args = array(
							 'posts_per_page' => -1,
							 'post_type' => 'attachment',
							 'orderby' => 'menu_order',
							 'order' => 'ASC',
							 'fpd_design_category' => $category_term->slug
						);

						$designs = get_posts( $args );
						foreach( $designs as $design ) {
							$parameters = get_post_meta($design->ID, 'fpd_parameters', true);
							echo '<li><img src="'.$design->guid.'" /><a href="#" class="fa fa-gear fpd-edit-parameters"></a><a href="#" class="fa fa-times fpd-remove-design"></a><input type="hidden" value="'.$design->ID.'" name="image_ids[]" /><input type="hidden" value="'.$category_term->slug.'" name="category_slugs[]" /><input type="hidden" value="'.$parameters.'" name="parameters[]" /></li>';
						}
					 	?>
					 	</ul>
				 	</div>

				 </div>
				 <?php
			 }

		wp_nonce_field( 'fpd_save_designs');
		?>
		<input type="submit" name="save_designs"  value="<?php _e('Save Changes', 'radykal'); ?>" class="button button-primary" />
	</form>
</div>
<div class="fpd-modal-wrapper" id="fpd-modal-parameters">
	<div class="modal-dialog">
		<h3><?php _e('Edit Parameters', 'radykal'); ?></h3>
		<p class="description"><?php _e('If enabling the parameters for a single design, these will be used instead the default parameters for designs, that are set in the settings.', 'radykal'); ?></p>
		<br />
		<form id="fpd-category-parameters">
			<label><input type="checkbox" value="1" name="enabled" /> <strong><?php _e('Enable single design parameters!', 'radykal'); ?></strong></label>
			<br />
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label><?php _e('X-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" class="" value="0" style="width:50px;" name="x"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Y-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" class="" value="0" style="width:50px;" name="y"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Z-Position', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" class="" value="-1" style="width:50px;" name="z"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Scale', 'radykal'); ?></label></th>
						<td><input type="number" style="width:50px;" name="scale"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Color(s)', 'radykal'); ?></label></th>
						<td><input type="text" class="" value="#000000" style="width:300px;" name="colors"></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label><?php _e('Price', 'radykal'); ?></label></th>
						<td><input type="number" step="1" min="0" class="" value="0" style="width:50px;" name="price"></td>
					</tr>
				</tbody>
			</table>
		</form>
		<br />
		<p class="fpd-clearfix">
			<button class="button button-secondary fpd-close-modal"><?php _e('Cancel', 'radykal'); ?></button>
			<button class="button button-primary fpd-save-modal"><?php _e('Set', 'radykal'); ?></button>
		</p>
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {

		var mediaUploader = null,
			$currentParametersInput = null;

		$( ".fpd-design-category ul" ).sortable({
			placeholder: 'ui-state-highlight',
			connectWith: ".fpd-design-category ul",
			receive: function(evt, ui) {
				ui.item.children('input[name="category_slugs[]"]').val(ui.item.parents('.fpd-design-category:first').attr('id'));
			}
		}).disableSelection();

		$('.fpd-design-category').on('click', 'h3 span', function() {
			$(this).parent().next('div:first').stop().slideToggle(200);
		});

		$('.fpd-design-category').on('click', '.fpd-add-designs', function(evt) {
			evt.preventDefault();

			var categoryId = $(this).parents('.fpd-design-category:first').attr('id');

			if (mediaUploader) {
				mediaUploader.categoryId = categoryId;
	            mediaUploader.open();
	            return;
	        }

	        mediaUploader = wp.media({
	            title: '<?php _e( 'Choose Designs', 'radykal' ); ?>',
	            button: {
	                text: '<?php _e( 'Set Designs', 'radykal' ); ?>'
	            },
	            multiple: true
	        });

			mediaUploader.categoryId = categoryId;
			mediaUploader.on('select', function() {
				var $targetCategoryList = $('#'+mediaUploader.categoryId).find('.inside ul');
				mediaUploader.state().get('selection').each(function(item) {
					var attachment = item.toJSON();
					$targetCategoryList.append('<li><img src="'+attachment.url+'" /><a href="#" class="fa fa-gear fpd-edit-parameters"></a><a href="#" class="fa fa-times fpd-remove-design"></a><input type="hidden" value="'+attachment.id+'" name="image_ids[]" /><input type="hidden" value="'+mediaUploader.categoryId+'" name="category_slugs[]" /><input type="hidden" value="" name="parameters[]" /></li>');
				});

	        });

	        mediaUploader.open();
		});

		$('#fpd-designs-form').on('click', '.fpd-edit-parameters', function(evt) {

			evt.preventDefault();

			$currentParametersInput = $(this).nextAll('input[name="parameters[]"]');

			var parameter_str = $currentParametersInput.val().length > 0 ? $currentParametersInput.val() : 'enabled=0&x=0&y=0&z=-1&scale=1&colors=%23000000&price=0';

			$.each(parameter_str.split('&'), function (index, elem) {
				var vals = elem.split('='),
					$targetElement = $("#fpd-modal-parameters form [name='" + vals[0] + "']");

				$targetElement.val(unescape(vals[1]));
				if($targetElement.is(':checkbox')) {
					$targetElement.prop('checked', $targetElement.val() == 1);
				}
			});


			$('#fpd-category-parameters input[name="enabled"]').change();

			openModal($('#fpd-modal-parameters'));

		});

		$('.fpd-modal-wrapper').on('click', '.fpd-save-modal', function(evt) {

			evt.preventDefault();

			var $modalWrapper = $(this).parents('.fpd-modal-wrapper:first');

			closeModal($modalWrapper);

			$currentParametersInput.val($modalWrapper.find('form').serialize());

			$currentParametersInput = null;

		});

		$('.fpd-modal-wrapper').on('click', '.fpd-close-modal', function(evt) {

			evt.preventDefault();
			closeModal($(this).parents('.fpd-modal-wrapper:first'));
			$currentParametersInput = null;

		});

		$('.fpd-design-category').on('click', '.fpd-remove-design', function(evt) {

			evt.preventDefault();
			$(this).parent('li:first').remove();

		});

		$('#fpd-category-parameters').on('change', 'input[name="enabled"]', function() {

			var $this = $(this),
				$allInputs = $this.parent().nextAll('table').find('input');

			if($this.is(':checked')) {
				$this.val(1);
				$allInputs.attr('disabled', false);
			}
			else {
				$this.val(0);
				$allInputs.attr('disabled', true);
			}

		});

	});
</script>