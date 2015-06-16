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
		<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'ppmlayout' ); ?>" />
	</form>
<!--

	/*====== Search Form Styles ======*/
	#searchform{
		margin-top: 30px;
	}

	#searchform .assistive-text{
		display: none;
	}

	#searchform .field{
		border: 1px solid #999;
		border-radius: 3px;
		box-shadow: inset 0 0 5px #999;
		color: #0b234a;
		line-height: 22px;
		padding: 3px;
		width: 400px;
	}

	#searchform .submit{
		border: 1px solid #555;
		border-radius: 5px;
		background-color: #555;
		color: white;
		line-height: 22px;
		padding: 2px 6px;
		cursor: pointer;
		font: 18px "MuseoSlab-500", Arial;
	}
	
-->