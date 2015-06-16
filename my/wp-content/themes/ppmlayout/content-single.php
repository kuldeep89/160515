<?php
/**
 * The template for displaying content in the single.php template
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>

<!-- BEGIN PAGE CONTENT-->
<div class="row-fluid">
   <div class="span12 blog-page">
      <div class="row-fluid">
         <div class="span9 article-block">
            <h1><?php the_title(); ?></h1>
			<?php
			//Get the first image in the post for Pinterest.
			$first_image_thumb=get_first_image();
			if(!$first_image_thumb)	{
			    $first_image_thumb=wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
			    $first_image_thumb=$first_image_thumb[0];
			}
			
			?>
			<div class="share42init" data-url="<?php the_permalink() ?>" data-image="<?php echo $first_image_thumb; ?>" data-title="<?php the_title() ?>"></div>
            <div class="blog-tag-data">
               <div class="row-fluid">
                  <div class="span6">
                     <ul class="unstyled inline blog-tags">
                        <li>
                           <i class="icon-tags"></i> 
                           <?php $categories_list = get_the_category_list( __( ' ', 'ppmlayout' ) ); 
							   	printf($categories_list); 		
						   	?>
                        </li>
                     </ul>
                  </div>
                  <div class="span6 blog-tag-data-inner">
                     <ul class="unstyled inline">
                        <li style="color: #555;"><i class="icon-calendar"></i> <?php the_date(); ?></li>
                        <li><i class="icon-user"></i> <?php the_author_posts_link() ?></li>
                     </ul>
                  </div>
               </div>
            </div>
            <!--end news-tag-data-->
            <div>
            
               <?php
               		$featured_image_loc = (strtolower(get_field('featured_image_placement')) == 'right') ? 'float:right;margin-left:25px;margin-bottom:20px;' : 'float:left;margin-right:25px;margin-bottom:20px;';
               		echo (has_post_thumbnail()) ? the_post_thumbnail('medium', array('style' => $featured_image_loc)) : '';
               		show_saltsha_content();
               	?>

               	<?php echo comments_template(); ?>
               	
               	<hr>
               <p>While every effort has been taken to provide users with accurate and thorough information, it is always advisable to consult legal counsel, human resources, and to use discretion when considering how Saltsha content can help business owners and entrepreneurs learn, grow, and stay current. View Saltsha's Indemnity policy <a href="<?php get_site_url() ?>/terms-and-conditions">here.</a></p>
            </div>
            <hr>
         </div>
         <!--end span9-->
         <div class="span3 blog-sidebar">
            <h2>Related Articles</h2>
            <div class="top-news">
            	<?php if(function_exists('echo_ald_crp')) echo_ald_crp(); ?>
            </div>
            <div class="space20"></div>
            <h2>News Tags</h2>
            
    		<?php
				$posttags = get_the_tags();
				if ($posttags) {
					echo '<ul class="unstyled inline sidebar-tags">';
					 	foreach($posttags as $tag) {
					 		echo '<li><a href="/tag/' . $tag->slug . '"><i class="icon-tags"></i>' . $tag->name . '</a></li> '; 
					 	}
				 	echo '</ul>';
				}
			?>
         </div>
         <!--end span3-->
      </div>
   </div>
</div>
<!-- END PAGE CONTENT-->

