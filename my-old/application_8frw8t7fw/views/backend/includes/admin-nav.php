<div class="navbar hor-menu hidden-phone hidden-tablet">
    <div class="navbar-inner">
	    <?php if( !$this->security_lib->is_group_member(2) ) : ?>
	        <ul class="nav">
	            
	            <li class="visible-phone visible-tablet">
	                <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
	
	                <form class="sidebar-search">
	                    
	                    <div class="input-box">
	                        <a href="javascript:;" class="remove"></a> <input type="text" placeholder="Search..."> <input type="button" class="submit" value=" ">
	                    </div>
	                    
	                </form><!-- END RESPONSIVE QUICK SEARCH FORM -->
	                
	            </li>
				
				<?php if( $this->security_lib->accessible(2) ) : ?>
				
				<li class="<?php echo (is_active_nav('academy'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Academy</a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo site_url('academy/listing'); ?>">Listing</a></li>
	                    <li class="dropdown-submenu"><a href="#">Manage</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a href="<?php echo site_url('academy/manage-categories'); ?>">Categories</a></li>
	                    		<li><a href="<?php echo site_url('academy/manage-tags'); ?>">Tags</a></li>
	                    	</ul>
	                    </li>
	                </ul>
				</li>
	            
	            <?php endif; ?>
	            
	            <?php if( $this->security_lib->accessible(2) ) : ?>
				
				<li class="<?php echo (is_active_nav('resource'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Resource</a>
					<ul class="dropdown-menu">
						<li><a href="<?php echo site_url('resource/listing'); ?>">Listing</a></li>
	                    <li class="dropdown-submenu"><a href="#">Manage</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a href="<?php echo site_url('resource/manage-categories'); ?>">Categories</a></li>
	                    		<li><a href="<?php echo site_url('resource/manage-tags'); ?>">Tags</a></li>
	                    	</ul>
	                    </li>
	                </ul>
				</li>
	            
	            <?php endif; ?>
	            
	            <?php if( $this->security_lib->accessible(13) ) : ?>
	            
	            <li class="<?php echo (is_active_nav('pages'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Pages</a>
	            	<ul class="dropdown-menu">
	                    <li><a href="<?php echo site_url('pages'); ?>">Page Listing</a></li>
	                    <li class="dropdown-submenu"><a href="#">Create Page</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a href="<?php echo site_url('pages/add-page'); ?>">Create Page</a></li>
	                    		<li><a href="<?php echo site_url('pages/add-page-reference'); ?>">Create Page Reference</a></li>
	                    	</ul>
	                    </li>
	                    <li><a href="<?php echo site_url('pages/navigations'); ?>">Navigation Management</a></li>
	                </ul>
	            </li>
				
				<?php endif; ?>
				
				<?php if( $this->security_lib->accessible(16) ) : ?>
				
				<li class="<?php echo (is_active_nav('faq'))? ' active':''; ?>"><a href="<?php echo site_url('faq/listing'); ?>">FAQs</a></li>
	
				<?php endif; ?>
	
				<?php if( $this->security_lib->accessible(40) ) : ?>
	
				<li class="<?php echo (is_active_nav('users'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Users</a>
	            	<ul class="dropdown-menu">
	                    <li><a href="<?php echo site_url('users'); ?>">View All</a></li>
	                    <li class="dropdown-submenu"><a href="#">Moderators</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a href="<?php echo site_url('users/moderators'); ?>">View All Moderators</a></li>
	                    		<li><a href="<?php echo site_url('users/add-moderator'); ?>">Add New Moderator</a></li>
	                    	</ul>
	                    </li>
	                    <li class="dropdown-submenu"><a href="#">Members</a>
	                    	<ul class="dropdown-menu">
	                    		<li><a href="<?php echo site_url('users/members'); ?>">View All Members</a></li>
	                    		<li><a href="<?php echo site_url('users/add-member'); ?>">Add New Member</a></li>
	                    	</ul>
	                    </li>
	                </ul>
	            </li>
	            
	            <?php endif; ?>
	            
	            <?php if( $this->security_lib->accessible(41) ) : ?>
	            
	            <li class="<?php echo (is_active_nav('accounts'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Accounts</a>
	            	<ul class="dropdown-menu">
	                    <li><a href="<?php echo site_url('accounts/listing'); ?>">Account Listings</a></li>
	                </ul>
	            </li>
	            
	            <?php endif; ?>
	            
				<?php if( $this->security_lib->accessible(40) ) : ?>
	            
	            <li class="<?php echo (is_active_nav('permissions'))? ' active':''; ?>"><a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Permissions</a>
	            	<ul class="dropdown-menu">
	            		<li class="dropdown-submenu"><a href="#">Modules</a>
	                    	<ul class="dropdown-menu">
								<li><a href="<?php echo site_url('permissions/view-modules'); ?>">View Modules</a></li>
								<li><a href="<?php echo site_url('permissions/create-module'); ?>">Add New Module</a></li>
	                    	</ul>
	                    </li>
	                    <li><a href="<?php echo site_url('permissions/groups'); ?>">Edit Group Permissions</a></li>
	                </ul>
	            </li>
	            
				<?php endif; ?>
				
	            <!--<li class="">
	                <a data-toggle="dropdown" class="dropdown-toggle" href="javascript:;">Sections</a>
	
	                <ul class="dropdown-menu">
	                    <li><a href="#">Section 1</a></li>
	
	                    <li><a href="#">Section 2</a></li>
	
	                    <li><a href="#">Section 3</a></li>
	
	                    <li><a href="#">Section 4</a></li>
	
	                    <li><a href="#">Section 5</a></li>
	                </ul>
	            </li>-->
	
	        </ul>
	     <?php endif ?>
    </div>
</div>