<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>
<a href="<?php echo site_url() ?>/shop/shopping-cart/" class="view-cart-button" style="margin-right:20px;">View <?php echo ($woocommerce->cart->has_quoted_item == false) ? 'Cart' : 'Quote' ?></a>
<?php get_header('shop'); ?>
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		do_action('woocommerce_before_main_content');
	?>
	
	<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

		<h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

	<?php endif; ?>

	<?php 
	
		//**
		// Get Product Categories
		//**
		$woocommerce_model	= new WooCommerce_model();
		$arr_categories		= $woocommerce_model->get_product_categories();
		
		//Omit categories
		$arr_omit	= array('Subscriptions' => 'Subscriptions');
		
	?>
	
	<div class="row-fluid">
	
		<section id="product-categories">
						
			<?php foreach( $arr_categories as $obj_category ) : ?>
				
				<?php
				 
					if( $obj_category->name == 'Subscriptions' ) {
						continue; 
					}
					
				?>
				
				<div class="product-category">

					<?php 
					
						$thumbnail_id	= get_woocommerce_term_meta( $obj_category->term_id, 'thumbnail_id', true );
					    $image			= wp_get_attachment_url( $thumbnail_id );
					    
					?>
					
					<?php if( $image ) : ?>
						<a href="/product-category/<?php echo $obj_category->slug; ?>"><span class="image-thumb" style="background-image:url('<?php echo $image; ?>');"></span></a>
					<?php else: ?>
						<a href="/product-category/<?php echo $obj_category->slug; ?>"><span class="image-thumb"></span></a>
					<?php endif; ?>
					
					<h3><a href="/product-category/<?php echo $obj_category->slug; ?>"><?php echo $obj_category->name; ?></a></h3>
					
				</div>
				
			<?php endforeach; ?>
		
		</div>
		
	</div>

	<?php
	
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
		
	?>

<?php get_footer('shop'); ?>

<?php 

	class WooCommerce_model {
		
		public function get_product_categories() {
			
			$args = array(
				'number' => 'null',
				'orderby' => 'name',
				'order' => 'ASC',
				'columns' => '4',
				'hide_empty' => '1',
				'parent' => '',
				'ids' => ''
			 );
			
			return get_terms( 'product_cat', $args );
			
		}
		
	}

?>