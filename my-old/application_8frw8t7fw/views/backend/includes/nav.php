<!-- BEGIN SIDEBAR -->
      <div class="page-sidebar nav-collapse collapse">
         <!-- BEGIN SIDEBAR MENU -->
         	<ul>
         		<li> <!-- BEGIN SIDEBAR TOGGLER BUTTON --> <div class="sidebar-toggler hidden-phone"></div> <!-- BEGIN SIDEBAR TOGGLER BUTTON --> </li>
         		<li> <!-- BEGIN RESPONSIVE QUICK SEARCH FORM --> <form method="post" action="<?php echo site_url('search/results'); ?>" class="sidebar-search"> <div class="input-box"> <a href="javascript:;" class="remove"></a> <input type="text" name="search" placeholder="Search..."><input type="submit" class="submit" value=""></div> </form> <!-- END RESPONSIVE QUICK SEARCH FORM --> </li>
				<?php $this->navigation_lib->print_user_navigation(); ?>
         	</ul>
      </div>
      <!-- END SIDEBAR -->