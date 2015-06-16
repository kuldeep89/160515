<?php
// Load WordPress Environment
require(substr(str_replace("\\", "/", dirname(__FILE__)), 0, strpos(str_replace("\\", "/", dirname(__FILE__)), "/wp-content")).'/wp-blog-header.php');
if (!have_posts()) header('HTTP/1.1 200 OK'); // Force 200 OK to replace 404 error
?>
<!DOCTYPE html>
<head>
	<title><?php _e('Insert Average Rating', 'comment-rating-field-pro'); ?></title>
	
	<!-- CSS -->
	<link rel="stylesheet" href="<?php echo admin_url(); ?>load-styles.php?c=1&load=buttons,wp-admin" type="text/css" media="all" />
	<link rel="stylesheet" href="<?php echo admin_url(); ?>css/colors-fresh.min.css" type="text/css" media="all" />
	<link rel="stylesheet" href="../_modules/dashboard/css/admin.css" type="text/css" media="all" />
	<link rel="stylesheet" href="../css/popup.css" type="text/css" media="all" />
</head>
<body class="wp-core-ui">
	<form class="crfp-popup">
		<div class="option">
        	<p>
        		<strong><?php _e('Display', 'comment-rating-field-pro'); ?></strong>
            	<select name="enabled" size="1">
                	<option value="1"><?php _e('Display when ratings exist', 'comment-rating-field-pro'); ?></option>
                	<option value="2"><?php _e('Always Display', 'comment-rating-field-pro'); ?></option>
                </select>  
            </p>
        </div>
        
        <div class="option">    
            <p>
            	<strong><?php _e('Style', 'comment-rating-field-pro'); ?></strong>
            	<select name="displayStyle" size="1">
                	<option value="yellow"><?php _e('Yellow Stars only', 'comment-rating-field-pro'); ?></option>
                	<option value="grey"><?php _e('Yellow Stars with Grey Stars', 'comment-rating-field-pro'); ?></option>
                </select>
            </p>
        </div>
        
        <div class="option">
        	<p>
        		<strong><?php _e('Show Average', 'comment-rating-field-pro'); ?></strong>
            	<select name="displayAverage" size="1">
                	<option value="1"><?php _e('Yes', 'comment-rating-field-pro'); ?></option>
                	<option value="2"><?php _e('No', 'comment-rating-field-pro'); ?></option>
                </select>  
            </p>
        </div>
        
        <div class="option">    
            <p>
            	<strong><?php _e('Average Rating Text', 'comment-rating-field-pro'); ?></strong>
            	<input type="text" name="averageRatingText" value="" />   
            </p>
        </div>
        
        <div class="option">    
            <p>
            	<strong><?php _e('Total Ratings', 'comment-rating-field-pro'); ?></strong>
            	<select name="displayTotalRatings" size="1">
                	<option value="0"><?php _e('Do not display', 'comment-rating-field-pro'); ?></option>
                	<option value="1"><?php _e('Display the total number of ratings', 'comment-rating-field-pro'); ?></option>
                </select>
            </p>
        </div>
        
        <div class="option">
        	<p>
        		<strong><?php _e('Show Breakdown', 'comment-rating-field-pro'); ?></strong>
            	<select name="displayBreakdown" size="1">
                	<option value="1"><?php _e('Yes', 'comment-rating-field-pro'); ?></option>
                	<option value="0"><?php _e('No', 'comment-rating-field-pro'); ?></option>
                </select>  
            </p>
        </div>
        
        <div class="option">
        	<p>
        		<strong><?php _e('Link to Comments Section', 'comment-rating-field-pro'); ?></strong>
            	<select name="displayLink" size="1">
                	<option value="1"><?php _e('Yes', 'comment-rating-field-pro'); ?></option>
                	<option value="0"><?php _e('No', 'comment-rating-field-pro'); ?></option>
                </select>  
            </p>
        </div>
        
        <div class="option">
        	<p>
        		<strong><?php _e('Post ID', 'comment-rating-field-pro'); ?></strong>
            	<input type="text" name="postID" size="5" /> 
            </p>
            <p class="description">
            	<?php _e('Only required if you want to show the average ratings for a different Post/Page than this one', 'comment-rating-field-pro'); ?>
            </p>
        </div>

		<div class="option">
			<p><input type="submit" name="submit" value="<?php _e('Insert', 'comment-rating-field-pro'); ?>" class="button button-primary" /></p>
		</div>
	</form>
	
	<!-- Javascript -->
	<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?php echo admin_url(); ?>load-scripts.php?c=1&load=jquery-core"></script>
	<script type="text/javascript" src="../js/admin.js"></script>
</body>
</html>