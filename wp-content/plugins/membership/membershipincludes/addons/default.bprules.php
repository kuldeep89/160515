<?php
/*
Addon Name: Default BuddyPress Rules
Description: Main BuddyPress rules
Author: Barry (Incsub)
Author URI: http://caffeinatedb.com
*/

// Buddypress pages - $directory_pages = apply_filters( 'bp_directory_pages', $directory_pages );
class M_BPPages extends M_Rule {

	var $name = 'bppages';
	var $label = 'BuddyPress Pages';
	var $description = 'Allows specific BuddyPress pages to be protected.';

	var $rulearea = 'public';

	function on_bp_page() {

	}

	function get_pages() {
		global $bp;

		$directory_pages = array();
		foreach ( $bp->loaded_components as $component_id ) {
			// Only components that need directories should be listed here
			if ( isset( $bp->{$component_id} ) && !empty( $bp->{$component_id}->has_directory ) ) {
				// component->name was introduced in BP 1.5, so we must provide a fallback
				$component_name = !empty( $bp->{$component_id}->name ) ? $bp->{$component_id}->name : ucwords( $component_id );
				$directory_pages[$component_id] = $component_name;
			}
		}

		return apply_filters( 'bp_directory_pages', $directory_pages );
	}

	function admin_main($data) {

		global $bp;

		if(!$data) $data = array();

		$existing_pages = bp_core_get_directory_page_ids();

		$directory_pages = $this->get_pages();

		?>
		<div class='level-operation' id='main-bppages'>
			<h2 class='sidebar-name'><?php _e('BuddyPress Pages', 'membership');?><span><a href='#remove' id='remove-bppages' class='removelink' title='<?php _e("Remove BuddyPress Pages from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the BuddyPress Pages to be covered by this rule by checking the box next to the relevant pages title.','membership'); ?></p>
				<?php

					//print_r($existing_pages);

					//print_r($directory_pages);

					if($directory_pages) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Page title', 'membership'); ?></th>
								</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Page title', 'membership'); ?></th>
								</tr>
							</tfoot>

							<tbody>
						<?php


						foreach($directory_pages as $key => $page) {

							if ( !empty( $existing_pages[$key] ) ) { ?>

							<tr valign="middle" class="alternate" id="post-<?php echo $post->ID; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $existing_pages[$key]; ?>" name="bppages[]" <?php if(in_array($existing_pages[$key], $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($page); ?></strong>
								</td>
						    </tr>
							<?php
							}
						}
						?>
							</tbody>
						</table>
						<?php
					}

				?>
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );

		add_action( 'pre_get_posts', array( $this, 'add_viewable_pages' ), 3 );
		add_filter( 'get_pages', array( $this, 'add_viewable_pages_menu' ), 2 );
	}

	function on_negative( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );

		add_action( 'pre_get_posts', array( $this, 'add_unviewable_pages' ), 3 );
		add_filter( 'get_pages', array( $this, 'add_unviewable_pages_menu' ), 2 );
	}

	function add_viewable_pages( $wp_query ) {
		if ( !$wp_query->is_single && !empty( $wp_query->query_vars['post__in'] ) ) {
			// We are not on a single page - so just limit the viewing
			foreach ( (array)$this->data as $value ) {
				$wp_query->query_vars['post__in'][] = $value;
			}

			$wp_query->query_vars['post__in'] = array_unique( $wp_query->query_vars['post__in'] );
		}
	}

	function add_viewable_pages_menu( $pages ) {
		$existing_pages = bp_core_get_directory_page_ids();
		foreach ( (array) $pages as $key => $page ) {
			if ( !in_array( $page->ID, (array)$this->data ) && in_array( $page->ID, (array)$existing_pages ) ) {
				unset( $pages[$key] );
			}
		}

		return $pages;
	}

	function add_unviewable_pages( $wp_query ) {
		if ( !$wp_query->is_single ) {
			// We are not on a single page - so just limit the viewing
			foreach ( (array) $this->data as $key => $value ) {
				$wp_query->query_vars['post__not_in'][] = $value;
			}

			$wp_query->query_vars['post__not_in'] = array_unique( $wp_query->query_vars['post__not_in'] );
		}
	}

	function add_unviewable_pages_menu( $pages ) {
		foreach ( (array) $pages as $key => $page ) {
			if ( in_array( $page->ID, (array) $this->data ) ) {
				unset( $pages[$key] );
			}
		}

		return $pages;
	}

	function validate_negative() {
		$component = bp_current_component();
		if ( $component ) {
			$pages = bp_core_get_directory_page_ids();
			if ( !empty( $pages[$component] ) ) {
				return !in_array( $pages[$component], $this->data );
			}
		}
		return parent::validate_negative();
	}

	function validate_positive() {
		$component = bp_current_component();
		if ( $component ) {
			$pages = bp_core_get_directory_page_ids();
			if ( !empty( $pages[$component] ) ) {
				return in_array( $pages[$component], $this->data );
			}
		}
		return parent::validate_positive();
	}

}

class M_BPGroups extends M_Rule {

	var $name = 'bpgroups';
	var $label = 'Groups';
	var $description = 'Allows specific BuddyPress groups to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-bpgroups'>
			<h2 class='sidebar-name'><?php _e('Groups', 'membership');?><span><a href='#remove' id='remove-bpgroups' class='removelink' title='<?php _e("Remove Groups from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the groups to be covered by this rule by checking the box next to the relevant groups title.','membership'); ?></p>
				<?php

					if(function_exists('groups_get_groups')) {
						$groups = groups_get_groups(array('per_page' => MEMBERSHIP_GROUP_COUNT));
					}

					if($groups) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Group title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Group created', 'membership'); ?></th>
							</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Group title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Group created', 'membership'); ?></th>
							</tr>
							</tfoot>

							<tbody>
						<?php
						foreach($groups['groups'] as $key => $group) {
							?>
							<tr valign="middle" class="alternate" id="bpgroup-<?php echo $group->id; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $group->id; ?>" name="bpgroups[]" <?php if(in_array($group->id, $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($group->name); ?></strong>
								</td>
								<td class="column-date">
									<?php
										echo date("Y/m/d", strtotime($group->date_created));
									?>
								</td>
						    </tr>
							<?php
						}
						?>
							</tbody>
						</table>
						<?php
					}

					if($groups['total'] > MEMBERSHIP_GROUP_COUNT) {
						?>
						<p class='description'><?php echo __("Only the most recent ", 'membership') . MEMBERSHIP_GROUP_COUNT . __(" groups are shown above.",'membership'); ?></p>
						<?php
					}

				?>

			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );

		add_filter( 'groups_get_groups', array( $this, 'add_viewable_groups' ) );
		add_filter( 'bp_activity_get', array( $this, 'add_has_activity' ) );
		//add_filter( 'bp_has_groups', array( $this, 'add_has_groups'), 10, 2 );
	}

	function add_has_activity( $activities ) {
		$inneracts = $activities['activities'];

		foreach ( (array)$inneracts as $key => $act ) {
			if ( $act->component == 'groups' ) {
				if ( !in_array( $act->item_id, $this->data ) ) {
					unset( $inneracts[$key] );
					$activities['total']--;
				}
			}
		}

		$activities['activities'] = array();
		foreach ( (array) $inneracts as $key => $act ) {
			$activities['activities'][] = $act;
		}

		return $activities;
	}

	function add_has_groups( $one, $groups ) {
		$innergroups = $groups->groups;
		foreach ( (array)$innergroups as $key => $group ) {
			if ( !in_array( $group->group_id, $this->data ) ) {
				unset( $innergroups[$key] );
				$groups->total_group_count--;
			}
		}

		$groups->groups = array();
		foreach ( (array)$innergroups as $key => $group ) {
			$groups->groups[] = $group;
		}

		return !empty( $groups->groups );
	}

	function add_unhas_groups( $one, $groups ) {
		$innergroups = $groups->groups;
		foreach ( (array)$innergroups as $key => $group ) {
			if ( in_array( $group->group_id, $this->data ) ) {
				unset( $innergroups[$key] );
				$groups->total_group_count--;
			}
		}

		$groups->groups = array();
		foreach ( (array)$innergroups as $key => $group ) {
			$groups->groups[] = $group;
		}

		return !empty( $groups->groups );
	}

	function on_negative($data) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );

		add_filter( 'groups_get_groups', array( &$this, 'add_unviewable_groups' ) );
		add_filter( 'bp_activity_get', array( &$this, 'add_unhas_activity' ) );
		//add_filter( 'bp_has_groups', array( $this, 'add_unhas_groups'), 10, 2 );
	}

	function add_unhas_activity( $activities ) {
		$inneracts = $activities['activities'];
		foreach ( (array)$inneracts as $key => $act ) {
			if ( $act->component == 'groups' ) {
				if ( in_array( $act->item_id, $this->data ) ) {
					unset( $inneracts[$key] );
					$activities['total']--;
				}
			}
		}

		$activities['activities'] = array( );
		foreach ( (array)$inneracts as $key => $act ) {
			$activities['activities'][] = $act;
		}

		return $activities;
	}

	function add_viewable_groups( $groups ) {
		foreach ( (array)$groups['groups'] as $key => $group ) {
			if ( !in_array( $group->id, $this->data ) ) {
				unset( $groups['groups'][$key] );
				$groups['total']--;
			}
		}

		sort( $groups['groups'] );

		return $groups;
	}

	function add_unviewable_groups( $groups ) {
		foreach ( (array)$groups['groups'] as $key => $group ) {
			if ( in_array( $group->id, $this->data ) ) {
				unset( $groups['groups'][$key] );
				$groups['total']--;
			}
		}

		sort( $groups['groups'] );

		return $groups;
	}

	function validate_negative() {
		global $bp;

		if ( bp_current_component() != 'groups' ) {
			return parent::validate_negative();
		}

		return isset( $bp->groups->current_group ) && is_a( $bp->groups->current_group, 'BP_Groups_Group' )
			? !in_array( $bp->groups->current_group, $this->data )
			: parent::validate_negative();
	}

	function validate_positive() {
		global $bp;

		if ( bp_current_component() != 'groups' ) {
			return parent::validate_positive();
		}

		return isset( $bp->groups->current_group ) && is_a( $bp->groups->current_group, 'BP_Groups_Group' )
			? in_array( $bp->groups->current_group, $this->data )
			: parent::validate_positive();
	}

}

class M_BPGroupcreation extends M_Rule {

	var $name = 'bpgroupcreation';
	var $label = 'Group Creation';
	var $description = 'Allows group creation to be allowed to members only.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-bpgroupcreation'>
			<h2 class='sidebar-name'><?php _e('Group Creation', 'membership');?><span><a href='#remove' id='remove-bpgroupcreation' class='removelink' title='<?php _e("Remove Group Creation from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><strong><?php _e('Positive : ','membership'); ?></strong><?php _e('User can create ','membership'); ?><input type='text' name='bpgroupcreation[number]' value='<?php echo esc_attr($data['number']); ?>' /><?php _e(' groups.','membership'); ?><br/><em><?php _e('Leave blanks for unlimited groups.','membership'); ?></em></p>
				<p><strong><?php _e('Negative : ','membership'); ?></strong><?php _e('User is unable to create any groups.','membership'); ?></p>
				<input type='hidden' name='bpgroupcreation[]' value='yes' />
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = $data;
		add_filter( 'groups_template_create_group', array( $this, 'pos_bp_groups_template' ) );
	}

	function on_negative( $data ) {
		$this->data = $data;
		add_filter( 'groups_template_create_group', array( $this, 'neg_bp_groups_template' ) );
	}

	function pos_bp_groups_template( $template ) {
		if ( !empty( $this->data['number'] ) ) {
			if ( !is_numeric( $this->data['number'] ) || (int)$this->data['number'] > $this->users_group_count() ) {
				return $this->neg_bp_groups_template( $template );
			}
		}

		return $template;
	}

	function users_group_count() {
		global $member, $wpdb, $bp;

		if ( !empty( $member ) && method_exists( $member, 'has_cap' ) ) {
			// We have a member and it is a correct object
			return (int)$wpdb->get_var( $wpdb->prepare(
				"SELECT count(*) FROM {$bp->activity->table_name} WHERE component = 'groups' AND type = 'created_group' AND user_id = %d",
				$member->ID
			) );
		}

		return 0;
	}

	function neg_bp_groups_template( $template ) {
		global $bp;

		//hack template steps to hide creation form elements
		$bp->action_variables[1] = 'disabled'; //nonsensical value, hide all group steps
		$bp->avatar_admin->step = 'crop-image'; //hides submit button
		add_action( 'template_notices', array( $this, 'neg_bp_message' ) );

		return $template;
	}

	function neg_bp_message() {
		if ( defined( 'MEMBERSHIP_GLOBAL_TABLES' ) && MEMBERSHIP_GLOBAL_TABLES === true ) {
			$MBP_options = function_exists( 'get_blog_option' )
				? get_blog_option( MEMBERSHIP_GLOBAL_MAINSITE, 'membership_bp_options', array() )
				: get_option( 'membership_bp_options', array() );
		} else {
			$MBP_options = get_option( 'membership_bp_options', array() );
		}

		echo '<div id="message" class="error"><p>' . stripslashes( $MBP_options['buddypressmessage'] ) . '</p></div>';
	}

}

class M_BPBlogs extends M_Rule {

	var $name = 'bpblogs';
	var $label = 'Blogs';
	var $description = 'Allows the protection of specific blogs.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-bpblogs'>
			<h2 class='sidebar-name'><?php _e('Blogs', 'membership');?><span><a href='#remove' id='remove-bpblogs' class='removelink' title='<?php _e("Remove Blogs from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the blogs to be covered by this rule by checking the box next to the relevant blogs name.','membership'); ?></p>
				<?php

					if(function_exists('bp_blogs_get_blogs')) {
						$blogs = bp_blogs_get_blogs(array('per_page' => 50));
					}

					if($blogs) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Blog title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Last activity', 'membership'); ?></th>
							</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Blog title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Last activity', 'membership'); ?></th>
							</tr>
							</tfoot>

							<tbody>
						<?php
						foreach($blogs['blogs'] as $key => $blog) {
							?>
							<tr valign="middle" class="alternate" id="bpblog-<?php echo $blog->blog_id; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $blog->blog_id; ?>" name="bpblogs[]" <?php if(in_array($blog->blog_id, $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($blog->name); ?></strong>
								</td>
								<td class="column-date">
									<?php
										echo date("Y/m/d", strtotime($blog->last_activity));
									?>
								</td>
						    </tr>
							<?php
						}
						?>
							</tbody>
						</table>
						<?php
					}

					if($blogs['total'] > 50) {
						?>
						<p class='description'><?php _e("Only the most recently updated 50 blogs are shown above.",'membership'); ?></p>
						<?php
					}

				?>

			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = $data;

		add_filter( 'bp_blogs_get_blogs', array( $this, 'add_viewable_blogs' ), 10, 2 );
		add_filter( 'bp_has_blogs', array( $this, 'add_has_blogs' ), 10, 2 );
		add_filter( 'bp_activity_get', array( $this, 'add_has_activity' ), 10, 2 );
		add_filter( 'bp_get_total_blog_count', array( $this, 'fix_blog_count' ) );
	}

	function on_negative( $data ) {
		$this->data = $data;

		add_filter( 'bp_blogs_get_blogs', array( $this, 'add_unviewable_blogs' ), 10, 2 );
		add_filter( 'bp_has_blogs', array( $this, 'add_unhas_blogs' ), 10, 2 );
		add_filter( 'bp_activity_get', array( $this, 'add_unhas_activity' ), 10, 2 );
		add_filter( 'bp_get_total_blog_count', array( $this, 'fix_unblog_count' ) );
	}

	function fix_blog_count( $count ) {
		$count = count( $this->data );
		return $count;
	}

	function fix_unblog_count( $count ) {
		$count -= count( $this->data );
		return $count;
	}

	function add_has_activity( $activities, $two ) {
		$inneracts = $activities['activities'];
		foreach ( (array)$inneracts as $key => $act ) {
			if ( $act->component == 'blogs' ) {
				if ( !in_array( $act->item_id, $this->data ) ) {
					unset( $inneracts[$key] );
					$activities['total']--;
				}
			}
		}

		$activities['activities'] = array();
		foreach ( (array)$inneracts as $key => $act ) {
			$activities['activities'][] = $act;
		}

		return $activities;
	}

	function add_unhas_activity( $activities, $two ) {
		$inneracts = $activities['activities'];
		foreach ( (array)$inneracts as $key => $act ) {
			if ( $act->component == 'blogs' ) {
				if ( in_array( $act->item_id, $this->data ) ) {
					unset( $inneracts[$key] );
					$activities['total']--;
				}
			}
		}

		$activities['activities'] = array();
		foreach ( (array)$inneracts as $key => $act ) {
			$activities['activities'][] = $act;
		}

		return $activities;
	}

	function add_unhas_blogs( $one, $blogs ) {
		$innerblogs = $blogs->blogs;
		foreach ( (array)$innerblogs as $key => $blog ) {
			if ( in_array( $blog->blog_id, $this->data ) ) {
				unset( $innerblogs[$key] );
				$blogs->total_blog_count--;
			}
		}

		$blogs->blogs = array();
		foreach ( (array) $innerblogs as $key => $blog ) {
			$blogs->blogs[] = $blog;
		}

		return !empty( $blogs->blogs );
	}

	function add_has_blogs( $one, $blogs ) {
		$innerblogs = $blogs->blogs;
		foreach ( (array)$innerblogs as $key => $blog ) {
			if ( !in_array( $blog->blog_id, $this->data ) ) {
				unset( $innerblogs[$key] );
				$blogs->total_blog_count--;
			}
		}

		$blogs->blogs = array();
		foreach ( (array)$innerblogs as $key => $blog ) {
			$blogs->blogs[] = $blog;
		}

		return !empty( $blogs->blogs );
	}

	function add_viewable_blogs( $blogs, $params ) {
		$innerblogs = $blogs['blogs'];
		foreach ( (array)$innerblogs as $key => $blog ) {
			if ( !in_array( $blog->blog_id, $this->data ) ) {
				unset( $innerblogs[$key] );
				$blogs['total']--;
			}
		}

		$blogs['blogs'] = array();
		foreach ( (array)$innerblogs as $key => $blog ) {
			$blogs['blogs'][] = $blog;
		}

		return $blogs;
	}

	function add_unviewable_blogs( $blogs, $params ) {
		$innerblogs = $blogs['groups'];
		foreach ( (array)$innerblogs as $key => $blog ) {
			if ( in_array( $blog->blog_id, $this->data ) ) {
				unset( $innerblogs[$key] );
				$blogs['total']--;
			}
		}

		$blogs['blogs'] = array();
		foreach ( (array)$innerblogs as $key => $blog ) {
			$blogs['blogs'][] = $blog;
		}

		return $blogs;
	}

}

class M_BPPrivatemessage extends M_Rule {

	var $name = 'bpprivatemessage';
	var $label = 'Private Messaging';
	var $description = 'Allows the sending of private messages to be limited to members.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-bpprivatemessage'>
			<h2 class='sidebar-name'><?php _e('Private Messaging', 'membership');?><span><a href='#remove' id='remove-bpprivatemessage' class='removelink' title='<?php _e("Remove Private Messaging from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><strong><?php _e('Positive : ','membership'); ?></strong><?php _e('User can send messages.','membership'); ?></p>
				<p><strong><?php _e('Negative : ','membership'); ?></strong><?php _e('User is unable to send messages.','membership'); ?></p>
				<input type='hidden' name='bpprivatemessage[]' value='yes' />
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = $data;
		add_filter( 'messages_template_compose', array( $this, 'pos_bp_messages_template' ) );
	}

	function on_negative( $data ) {
		$this->data = $data;
		add_filter( 'messages_template_compose', array( $this, 'neg_bp_messages_template' ) );
	}

	function pos_bp_messages_template( $template ) {
		return $template;
	}

	function neg_bp_messages_template( $template ) {
		add_action( 'bp_template_content', array( $this, 'neg_bp_message' ) );
		return 'members/single/plugins';
	}

	function neg_bp_message() {
		if ( defined( 'MEMBERSHIP_GLOBAL_TABLES' ) && MEMBERSHIP_GLOBAL_TABLES === true ) {
			$MBP_options = function_exists( 'get_blog_option' )
				? get_blog_option( MEMBERSHIP_GLOBAL_MAINSITE, 'membership_bp_options', array() )
				: get_option( 'membership_bp_options', array() );
		} else {
			$MBP_options = get_option( 'membership_bp_options', array( ) );
		}

		echo '<div id="message" class="error"><p>' . stripslashes( $MBP_options['buddypressmessage'] ) . '</p></div>';
	}

}

// Pass thru function
function MBP_can_access_page( $page ) {

	global $user, $member;

	if(!empty($member) && method_exists($member, 'pass_thru')) {
		return $member->pass_thru( 'bppages', array( 'can_access_page' => $page ) );
	}

}

function M_AddBuddyPressSection($sections) {
	$sections['bp'] = array(	"title" => __('BuddyPress','membership') );

	return $sections;
}

// BuddyPress options
function M_AddBuddyPressOptions() {

	if(defined('MEMBERSHIP_GLOBAL_TABLES') && MEMBERSHIP_GLOBAL_TABLES === true) {
		if(function_exists('get_blog_option')) {
			$MBP_options = get_blog_option(MEMBERSHIP_GLOBAL_MAINSITE, 'membership_bp_options', array());
		} else {
			$MBP_options = get_option('membership_bp_options', array());
		}
	} else {
		$MBP_options = get_option('membership_bp_options', array());
	}

	?>
		<div class="postbox">
			<h3 class="hndle" style='cursor:auto;'><span><?php _e('BuddyPress protected content message','membership'); ?></span></h3>
			<div class="inside">
				<p class='description'><?php _e('This is the message that is displayed when a BuddyPress related operation is restricted. Depending on your theme this is displayed in a red bar, and so should be short and concise.','membership'); ?></p>

				<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('BuddyPress No access message','membership'); ?>
						</th>
						<td>
						<?php
							$args = array("textarea_name" => "buddypressmessage");
							wp_editor( stripslashes($MBP_options['buddypressmessage']), "buddypressmessage", $args );
						?>
						</td>
					</tr>
				</tbody>
				</table>
			</div>
		</div>

	<?php
}

function M_AddBuddyPressOptionsProcess() {

	if(defined('MEMBERSHIP_GLOBAL_TABLES') && MEMBERSHIP_GLOBAL_TABLES === true) {
		if(function_exists('get_blog_option')) {
			$MBP_options = get_blog_option(MEMBERSHIP_GLOBAL_MAINSITE, 'membership_bp_options', array());
		} else {
			$MBP_options = get_option('membership_bp_options', array());
		}
	} else {
		$MBP_options = get_option('membership_bp_options', array());
	}

	$MBP_options['buddypressmessage'] = $_POST['buddypressmessage'];

	if(defined('MEMBERSHIP_GLOBAL_TABLES') && MEMBERSHIP_GLOBAL_TABLES === true) {
		if(function_exists('get_blog_option')) {
			update_blog_option(MEMBERSHIP_GLOBAL_MAINSITE, 'membership_bp_options', $MBP_options);
		} else {
			update_option('membership_bp_options', $MBP_options);
		}
	} else {
		update_option('membership_bp_options', $MBP_options);
	}

}

function M_HideBuddyPressPages( $pages ) {

	if(function_exists('bp_core_get_directory_page_ids')) {
		$existing_pages = bp_core_get_directory_page_ids();
	}

	foreach( $pages as $key => $page ) {
		if( in_array( $page->ID, (array) $existing_pages ) ) {
			unset( $pages[$key] );
		}
	}

	return $pages;

}

function M_KeepBuddyPressPages( $pages ) {

	$existing_pages = bp_core_get_directory_page_ids();

	if(!empty($existing_pages)) {
		$pages = array_merge( $pages, $existing_pages );
	}

	return $pages;

}

function M_overrideBPSignupSlug( $slug ) {

	$permalink = M_get_registration_permalink();

	if(!empty($permalink)) {
		return basename($permalink);
	} else {
		return $slug;
	}
}

add_action( 'plugins_loaded', 'M_setup_BP_addons', 99 );
function M_setup_BP_addons() {
	if ( defined( 'BP_VERSION' ) && version_compare( preg_replace( '/-.*$/', '', BP_VERSION ), "1.5", '>=' ) ) {
		M_register_rule( 'bppages', 'M_BPPages', 'bp' );
		M_register_rule( 'bpprivatemessage', 'M_BPPrivatemessage', 'bp' );
		M_register_rule( 'bpblogs', 'M_BPBlogs', 'bp' );
		M_register_rule( 'bpgroupcreation', 'M_BPGroupcreation', 'bp' );
		M_register_rule( 'bpgroups', 'M_BPGroups', 'bp' );

		add_action( 'membership_postoptions_page', 'M_AddBuddyPressOptions', 11 );
		add_action( 'membership_option_menu_process_posts', 'M_AddBuddyPressOptionsProcess', 11 );

		add_filter( 'membership_level_sections', 'M_AddBuddyPressSection' );
		add_filter( 'membership_hide_protectable_pages', 'M_HideBuddyPressPages' );
		add_filter( 'membership_override_viewable_pages_menu', 'M_KeepBuddyPressPages' );
		add_filter( 'bp_get_signup_slug', 'M_overrideBPSignupSlug' );
	}
}