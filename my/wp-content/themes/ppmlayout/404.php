<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header(); ?>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class="page-breadcrumb-heading">
		<h3 class="page-title">
					<?php _e( '404', 'ppmlayout' ); ?>
					<small>
						page not found
					</small>
		</h3>
		<ul class="breadcrumb">
			<li>
				<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
			</li>	
		</ul>
	</div>
	<!-- END PAGE TITLE & BREADCRUMB-->
	<!-- BEGIN PAGE CONTAINER-->
	 <div class="container-fluid">
	    <div id="dashboard">
	       <div class="row-fluid">
	          <div class="span12 responsive">
				<div class="row-fluid page-404">
                     <div class="span5 number">
                        404
                     </div>
                     <div class="span7 details">
                        <h3>Oops, you're lost.</h3>
                        <p>
                           We can not find the page you're looking for.<br />
                           Is there a typo in the url? Or try the search bar below.
                        </p>
                      
                        
                        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                           <div class="input-append">                      
                              <input class="m-wrap noResults" class="field" name="s" id="s" size="16" type="text" placeholder="keyword..." />
                              <input type="submit" name="submit" id="searchsubmit" class="btn blue" value="Search" />
                           </div>
                        </form>
                     </div>
                  </div>	
	          </div>                  
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	 <!-- END PAGE CONTAINER-->   
	  
<?php get_footer(); ?>