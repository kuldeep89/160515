<?php
/**
 * The template for displaying search forms in PPM Layout
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>
	
		<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<div class="row">
				<div class="col-sm-12">
					<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
				</div>
			</div>
			<div class="clear-both"></div>
		</form>
