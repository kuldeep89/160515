<?php
/*
Plugin Name: WP Awesome FAQ
Plugin URI: http://h2cweb.net
Description:Accordion based awesome WordPress FAQ. 
Version: 1.3
Author: Liton Arefin
Author URI: http://www.h2cweb.net
License: GPL2
http://www.gnu.org/licenses/gpl-2.0.html
*/

// Custom FAQ Post Type 
function h2cweb_faq() {
	$labels = array(
		'name'               => _x( 'FAQ', 'post type general name' ),
		'singular_name'      => _x( 'FAQ', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'book' ),
		'add_new_item'       => __( 'Add New FAQ' ),
		'edit_item'          => __( 'Edit FAQ' ),
		'new_item'           => __( 'New FAQ Items' ),
		'all_items'          => __( 'All FAQ\'s' ),
		'view_item'          => __( 'View FAQ' ),
		'search_items'       => __( 'Search FAQ' ),
		'not_found'          => __( 'No FAQ Items found' ),
		'not_found_in_trash' => __( 'No FAQ Items found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'FAQ'
	);

	$args = array(
		'labels'        => $labels,
		'description'   => 'Holds FAQ specific data',
		'public'        => true,
		'show_ui'       => true,
		'show_in_menu'  => true,
		'query_var'     => true,
		'rewrite'       => true,
		'capability_type'=> 'post',
		'has_archive'   => true,
		'hierarchical'  => false,
		'menu_position' => 5,
		'supports'      => array( 'title', 'editor'),
		'menu_icon'		=> site_url('/wp-content/plugins/wordpress-seo/images/question-mark.png') // Icon Path
	);
	register_post_type( 'faq', $args ); 

	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'FAQ Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'FAQ Category', 'taxonomy singular name' ),
		'search_items'      =>  __( 'Search FAQ Categories' ),
		'all_items'         => __( 'All FAQ Category' ),
		'parent_item'       => __( 'Parent FAQ Category' ),
		'parent_item_colon' => __( 'Parent FAQ Category:' ),
		'edit_item'         => __( 'Edit FAQ Category' ),
		'update_item'       => __( 'Update FAQ Category' ),
		'add_new_item'      => __( 'Add New FAQ Category' ),
		'new_item_name'     => __( 'New FAQ Category Name' ),
		'menu_name'         => __( 'FAQ Category' ),
	);

	register_taxonomy('faq_cat',array('faq'), array(
		'hierarchical' => true,
		'labels'       => $labels,
		'show_ui'      => true,
		'query_var'    => true,
		'rewrite'      => array( 'slug' => 'faq_cat' ),
	));
}
add_action( 'init', 'h2cweb_faq' );

function h2cweb_accordion_shortcode() { 
	// Getting FAQs from WordPress Awesome FAQ plugin's Custom Post Type questions
	$args = array( 'posts_per_page' => -1,  'post_type' => 'faq', 'order'=>"DESC");
	$query = new WP_Query( $args );
	$arr_categories = array(); 
	$arr_output		= array(); 
	$arr_another	= array(); 

	global $faq;
?>
		<div class="row-fluid">
		   <div class="span12">

		      <div class="span3">
		         <ul class="ver-inline-menu tabbable margin-bottom-10">
		         	<?php
					if( $query->have_posts() ) {
						while ( $query->have_posts() ) {
							$post_data = $query->the_post();
							$terms = wp_get_post_terms(get_the_ID(), 'faq_cat' );
							$t = $c = array();

							$ordered_categories = array();
							foreach($terms as $term) {
								$t[] = $term->name;
							    $term_meta = get_option("taxonomy_".$term->term_id);
							    $c[] = $cat_order = $term_meta['order'];
							    if (!isset($arr_categories[$cat_order]['name'])) {
								    $arr_categories[$cat_order] = array('name' => $term->name, 'cat_id' => $term->term_id);
							    }
							}
							
							// Add articles
							$arr_categories[$cat_order]['articles'][] = array(
								'faq_id' => get_the_ID(),
								'name'=> $term->name,
								'faq'=> get_the_title(),
								'response'=> get_the_content(),
								'faq_entry_category_id'=>$term->term_taxonomy_id
							);
						}
					}
					
					// Sort categories
					ksort($arr_categories);

		         	// Display category
		         	if( isset($arr_categories) && count($arr_categories) > 0 ) : ?>
		         		<?php $first = true ?>
		         		<?php foreach( $arr_categories as $arr_category_entries ) : ?>
		         			 <li class="<?php echo ($first)? 'active':''; ?>">
				               <a href="#tab_<?php echo $arr_category_entries['cat_id'] ?>" data-toggle="tab">
				               <i class="icon-briefcase"></i> 
				               <?php echo $arr_category_entries['name'] ?>
				               </a> 
				               <span class="after"></span>                                    
				            </li>
				            <?php $first = FALSE ?>
						<?php  endforeach; ?>
					<?php else: ?>
                   		<li>
                   			<a href="#" data-toggle="tab"><i class="icon-info-sign"></i> No Categories</a></li>
                   		</li>
                     <?php endif; ?>
		         </ul>
		      </div>
		      <div class="span9">
		         <div class="tab-content">
					 <?php if( isset($arr_categories) && count($arr_categories) > 0 ) :
							$first_tab = true;
							foreach( $arr_categories as $arr_category_entries ) : ?>
							    <?php $article_num = (isset($article_num)) ? $article_num : 0; ?>
								<div class="tab-pane<?php echo ($first_tab==true) ? ' active' : '' ?>" id="tab_<?php echo $arr_category_entries['cat_id']; ?>">
								<?php foreach( $arr_category_entries['articles'] as $current_article ) : ?>
									<?php $article_num++ ?>
									<div class="accordion in collapse<?php /* echo ($article_num == 1) ? '' : ' collapse' */ ?>" id="accordion_<?php echo $current_article['faq_entry_category_id'] ?>" style="height: auto;">
										<div class="accordion-group">
											<div class="accordion-heading">
												<a class="accordion-toggle<?php /* echo ($article_num == 1) ? '' : ' collapsed' */ ?> collapsed" data-toggle="collapse" data-parent="#accordion_<?php echo $current_article['faq_entry_category_id'] ?>" href="#collapse_<?php echo $current_article['faq_id'] ?>">
													<p><?php echo $current_article['faq'] ?></p>
												</a>
											</div>
											<div id="collapse_<?php echo $current_article['faq_id'] ?>" class="accordion-body collapse<?php /* echo ($article_num == 1) ? '' : ' collapse'*/ ?>" >
												<div class="accordion-inner">
													<?php echo $current_article['response'] ?>
												</div>
											</div>
										</div>
									</div>
								<?php endforeach; ?>
								</div>
							<?php $first_tab = false ?>
							<?php endforeach; ?>
					 <?php endif; ?>		
		         </div>
		      </div>
		   </div>
		</div>
		
		
<?php
	//Reset the query
	wp_reset_query();
	wp_reset_postdata();
		
}

$first	= TRUE;
add_shortcode('faq', 'h2cweb_accordion_shortcode');

//add extra fields to custom taxonomy edit form callback function
function extra_faq_cat_fields_edit($tag) {
   //check for existing taxonomy meta for term ID
    $t_id = $tag->term_id;
    $term_meta = get_option( "taxonomy_$t_id");
?>
	<tr class="form-field">
		<th scope="row" valign="top"><label for="term_meta[order]">FAQ Category Order</label></th>
		<td>
			<input type="text" name="term_meta[order]" id="term_meta[order]" value="<?php echo $term_meta['order'] ? $term_meta['order'] : ''; ?>">
		</td>
	</tr>
<?php
}

// Add extra meta to "Add New FAQ Category" page
function faq_cat_add_new_meta_field() {
	?>
	<div class="form-field">
		<label for="term_meta[order]">FAQ Category Order</label>
		<input type="text" name="term_meta[order]" id="term_meta[order]" value="">
	</div>
<?php
}
add_action( 'category_add_form_fields', 'pippin_taxonomy_add_new_meta_field', 10, 2 );
 
// save extra taxonomy fields callback function
function save_extra_taxonomy_fields( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
        $t_id = $term_id;
        $term_meta = get_option( "taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
            foreach ($cat_keys as $key){
            if (isset($_POST['term_meta'][$key])){
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        //save the option array
        update_option( "taxonomy_$t_id", $term_meta );
    }
}

add_action('faq_cat_edit_form_fields', 'extra_faq_cat_fields_edit', 10, 2);
add_action('edited_faq_cat', 'save_extra_taxonomy_fields', 10, 2);
add_action('faq_cat_add_form_fields', 'faq_cat_add_new_meta_field', 10, 2 );
add_action('created_faq_cat', 'save_extra_taxonomy_fields', 10, 2);