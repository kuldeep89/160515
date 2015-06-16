 <!-- BEGIN SIDEBAR --> 
<div class="page-sidebar nav-collapse collapse">
         <!-- BEGIN SIDEBAR MENU -->        	
		 	<ul>
				<!--
				<li>
					<div class="sidebar-toggler hidden-phone"></div>
				</li>
				-->
				<li>
					<!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
					
					<form class="sidebar-search" id="searchform" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						<div class="input-box">
							<a href="javascript:;" class="remove"></a>
							<input type="text" class="field" name="s" id="s" placeholder="Search..." />				
							<input type="submit" name="submit" id="searchsubmit" class="submit" value=" " />
						</div>
					</form>
					<!-- END RESPONSIVE QUICK SEARCH FORM -->
				</li>
                	        		
		        <?php 
					$defaults = array(
						'theme_location'  => '',
						'menu'            => '',
						'container'       => 'false',
						'container_class' => '',
						'container_id'    => '',
						'menu_class'      => 'menu',
						'menu_id'         => '',
						'echo'            => true,
						'fallback_cb'     => 'wp_page_menu',
						'before'          => '',
						'after'           => '',
						'link_before'     => '<i class="icon-briefcase"></i><span class="title">',
						'link_after'      => '</span>',
						'items_wrap'      => '%3$s',
						'depth'           => 0,
						'walker'          => ''
					);
					
					wp_nav_menu( $defaults ); 
				?>        	
			</ul>
			<div class="menu-social">
				<a href="https://www.facebook.com/Saltsha" target="_blank" title="Facebook"><i class="icon-facebook"></i></a>
				<a href="https://twitter.com/saltsha" target="_blank>" title="Twitter"><i class="icon-twitter"></i></a>
				<a href="https://plus.google.com/105387137872967257002" target="_blank>" title="Google Plus"><i class="icon-google-plus"></i></a>
				<a href="http://www.pinterest.com/saltsha/" target="_blank" title="pintrest"><i class="icon-pinterest"></i></a>
			</div>
		<!-- END SIDEBAR MENU -->
      </div>
      <!-- END SIDEBAR -->
