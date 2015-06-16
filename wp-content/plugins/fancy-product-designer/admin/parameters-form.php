<form role="form" id="fpd-elements-form">
	<div class="fpd-clearfix fpd-children-floated">
		<div>
			<h4><?php _e('Position', 'radykal'); ?></h4>
			<label><?php _e('x', 'radykal'); ?>:</label><input type="text" name="x" size="3" placeholder="0" style="margin-right: 15px;" class="fpd-only-numbers">
			<br />
			<label><?php _e('y', 'radykal'); ?>:</label><input type="text" name="y" size="3" placeholder="0" class="fpd-only-numbers">
		</div>
		<div>
			<h4><?php _e('Scale', 'radykal'); ?></h4>
			<input type="text" name="scale" size="3" placeholder="1" class="fpd-only-numbers fpd-allow-dots"></label>
		</div>
		<div>
			<h4><?php _e('Angle', 'radykal'); ?></h4>
			<input type="text" name="angle" size="3" placeholder="0" class="fpd-only-numbers"></label>
		</div>
		<div>
			<h4><?php _e('Price', 'radykal'); ?></h4>
			<input type="text" name="price" size="3" placeholder="0"></label>
		</div>
	</div>
	<div>
		<h4><?php _e('Colors', 'radykal'); ?> <img class="help_tip" data-tip="<?php _e('One color value: Colorpicker, Multiple color values: Predefined color list, Element Title: Color Control', 'radykal'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></h4>
		<p class="description"><?php in_array($request_view_id, $first_views_ids) ? _e('Enter hex colors by , separating values.', 'radykal') : _e('Enter hex colors by , separating values or the title of an element in the first view to use color control.', 'radykal'); ?></p>
		<input type="text" name="colors" class="widefat input-sm" placeholder="<?php _e('E.g. #000000,#990000', 'radykal') ; ?>" /></label>
	</div>
	<div class="form-inline">
		<h4><?php _e('Modifications', 'radykal'); ?></h4>
		<label class="checkbox-inline"><input type="checkbox" name="removable" value="1"> <?php _e('Removable', 'radykal'); ?></label>
		<label class="checkbox-inline"><input type="checkbox" name="draggable" value="1"> <?php _e('Draggable', 'radykal'); ?></label>
		<label class="checkbox-inline"><input type="checkbox" name="rotatable" value="1"> <?php _e('Rotatable', 'radykal'); ?></label>
		<label class="checkbox-inline"><input type="checkbox" name="resizable" value="1"> <?php _e('Resizable', 'radykal'); ?></label>
		<label class="checkbox-inline"><input type="checkbox" name="zChangeable"value="1"> <?php _e('Z-Position Changeable', 'radykal'); ?></label>
		<label class="checkbox-inline only-for-text-elements"><input type="checkbox" name="patternable" value="1"> <?php _e('Patternable', 'radykal'); ?></label>
		<label class="checkbox-inline only-for-text-elements"><input type="checkbox" name="textEditable" value="0"> <?php _e('Prevent Editing', 'radykal'); ?></label>
	</div>
	<div>
		<h4><?php _e('Bounding Box', 'radykal'); ?></h4>
		<label class="checkbox-inline"><input type="checkbox" name="bounding_box_control" value="1"> <?php _e('Use another element as bounding box', 'radykal'); ?></label>
		<br />
		<div id="boundig-box-params">
			<label><?php _e('x', 'radykal'); ?>:</label><input type="text" name="bounding_box_x" size="3" placeholder="0" style="margin-right: 15px;">
			<label><?php _e('y', 'radykal'); ?>:</label><input type="text" name="bounding_box_y" size="3" placeholder="0">
			<br />
			<label><?php _e('width', 'radykal'); ?>:</label><input type="text" name="bounding_box_width" size="3" placeholder="0" style="margin-right: 15px;">
			<label><?php _e('height', 'radykal'); ?>:</label><input type="text" name="bounding_box_height" size="3" placeholder="0">
		</div>
		<input type="text" name="bounding_box_by_other" class="widefat input-sm" placeholder="<?php _e('Title of another element in the same view.', 'radykal'); ?>" style="display: none;" />
	</div>
	<div class="only-for-text-elements">
		<h4><?php _e('Font', 'radykal'); ?></h4>
		<?php
		?>
		<select name="font" data-placeholder="<?php _e('Select a font', 'radykal'); ?>" class="fpd-text-element-params">
			<?php
			foreach(Fancy_Product_Designer::get_active_fonts() as $font) {
				echo "<option value='$font' style='font-family: $font;'>$font</option>";
			}
			?>
		</select>
	</div>
</form>