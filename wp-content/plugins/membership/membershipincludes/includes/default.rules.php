<?php

class M_Posts extends M_Rule {

	var $name = 'posts';
	var $label = 'Posts';
	var $description = 'Allows specific posts to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-posts'>
			<h2 class='sidebar-name'><?php _e('Posts', 'membership');?><span><a href='#remove' id='remove-posts' class='removelink' title='<?php _e("Remove Posts from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the posts to be covered by this rule by checking the box next to the relevant posts title.','membership'); ?></p>
				<?php
					$args = array(
						'numberposts' => MEMBERSHIP_POST_COUNT,
						'offset' => 0,
						'orderby' => 'post_date',
						'order' => 'DESC',
						'post_type' => 'post',
						'post_status' => 'publish'
					);

					$posts = get_posts($args);
					if($posts) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Post title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Post date', 'membership'); ?></th>
							</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Post title', 'membership'); ?></th>
								<th style="" class="manage-column column-date" id="date" scope="col"><?php _e('Post date', 'membership'); ?></th>
							</tr>
							</tfoot>

							<tbody>
						<?php
						foreach($posts as $key => $post) {
							?>
							<tr valign="middle" class="alternate" id="post-<?php echo $post->ID; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $post->ID; ?>" name="posts[]" <?php if(in_array($post->ID, $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($post->post_title); ?></strong>
								</td>
								<td class="column-date">
									<?php
										echo date("Y/m/d", strtotime($post->post_date));
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

				?>
				<p class='description'><?php echo sprintf(__("Only the most recent %d posts are shown above, if you have more than that then you should consider using categories instead.",'membership'), MEMBERSHIP_POST_COUNT); ?></p>
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_action( 'pre_get_posts', array( $this, 'add_viewable_posts' ), 99 );
	}

	function on_negative( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_action( 'pre_get_posts', array( $this, 'add_unviewable_posts' ), 99 );
	}

	function add_viewable_posts( $wp_query ) {
		if ( !$wp_query->is_singular && empty( $wp_query->query_vars['pagename'] ) && ( !isset( $wp_query->query_vars['post_type'] ) || in_array( $wp_query->query_vars['post_type'], array( 'post', '' ) )) ) {

			// We are in a list rather than on a single post
			foreach ( (array) $this->data as $key => $value ) {
				$wp_query->query_vars['post__in'][] = $value;
			}

			$wp_query->query_vars['post__in'] = array_unique( $wp_query->query_vars['post__in'] );
		}
	}

	function add_unviewable_posts( $wp_query ) {
		if ( !$wp_query->is_singular && empty( $wp_query->query_vars['pagename'] ) && ( !isset( $wp_query->query_vars['post_type'] ) || in_array( $wp_query->query_vars['post_type'], array( 'post', '' ) ) ) ) {

			// We are on a list rather than on a single post
			foreach ( (array) $this->data as $key => $value ) {
				$wp_query->query_vars['post__not_in'][] = $value;
			}

			$wp_query->query_vars['post__not_in'] = array_unique( $wp_query->query_vars['post__not_in'] );
		}
	}

	function validate_negative() {
		if ( !is_single() || is_attachment() ) {
			return parent::validate_negative();
		}

		return !in_array( get_the_ID(), $this->data );
	}

	function validate_positive() {
		if ( !is_single() || is_attachment() ) {
			return parent::validate_positive();
		}

		return in_array( get_the_ID(), $this->data );
	}

}

class M_Pages extends M_Rule {

	var $name = 'pages';
	var $label = 'Pages';
	var $description = 'Allows specific pages to be protected.';

	var $rulearea = 'public';

	function admin_main( $data ) {
		if ( !$data ) {
			$data = array();
		}

		$posts = apply_filters( 'staypress_hide_protectable_pages', get_posts( array(
			'numberposts' => MEMBERSHIP_PAGE_COUNT,
			'offset'      => 0,
			'orderby'     => 'post_date',
			'order'       => 'DESC',
			'post_type'   => 'page',
			'post_status' => 'publish'
		) ) );

		?>
		<div id="main-pages" class="level-operation">
			<h2 class="sidebar-name">
				<?php _e( 'Pages', 'membership' ) ?>
				<span>
					<a id="remove-pages" href="#remove" class="removelink" title="<?php _e( "Remove Pages from this rules area.", 'membership' ) ?>"><?php
						_e( 'Remove', 'membership' )
					?></a>
				</span>
			</h2>

			<div class="inner-operation">
				<p><?php _e( 'Select the Pages to be covered by this rule by checking the box next to the relevant pages title. Pay attention that pages selected as Membership page (in the options) are not listed below.', 'membership' ) ?></p>

				<?php if ( $posts ) : ?>
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
						<?php foreach ( $posts as $post ) : ?>
							<?php if ( membership_is_special_page( $post->ID, false ) ) continue; ?>
							<tr valign="middle" class="alternate" id="post-<?php echo $post->ID ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $post->ID ?>" name="pages[]"<?php checked( in_array( $post->ID, $data ) ) ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html( $post->post_title ) ?></strong>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
				<?php endif; ?>

				<p class="description"><?php printf( __( "Only the most recent %d pages are shown above.", 'membership' ), MEMBERSHIP_PAGE_COUNT ) ?></p>
			</div>
		</div><?php
	}

	function on_positive( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_filter( 'get_pages', array( $this, 'add_viewable_pages_menu' ), 1 );
	}

	function on_negative( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
		add_filter( 'get_pages', array( $this, 'add_unviewable_pages_menu' ), 1 );
	}

	function add_viewable_pages_menu( $pages ) {
		$override_pages = apply_filters( 'membership_override_viewable_pages_menu', array() );

		foreach ( (array)$pages as $key => $page ) {
			if ( !in_array( $page->ID, (array) $this->data ) && !in_array( $page->ID, (array) $override_pages ) ) {
				unset( $pages[$key] );
			}
		}

		return $pages;
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
		$page = get_queried_object();
		return is_a( $page, 'WP_Post' ) && $page->post_type == 'page'
			? !in_array( $page->ID, $this->data )
			: parent::validate_negative();
	}

	function validate_positive() {
		$page = get_queried_object();
		return is_a( $page, 'WP_Post' ) && $page->post_type == 'page'
			? in_array( $page->ID, $this->data )
			: parent::validate_positive();
	}

}

class M_Categories extends M_Rule {

	var $name = 'categories';
	var $label = 'Categories';
	var $description = 'Allows posts to be protected based on their assigned categories.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
			?>
			<div class='level-operation' id='main-categories'>
				<h2 class='sidebar-name'><?php _e('Categories', 'membership');?><span><a href='#remove' class='removelink' id='remove-categories' title='<?php _e("Remove Categories from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
				<div class='inner-operation'>
					<p><?php _e('Select the Categories to be covered by this rule by checking the box next to the relevant categories name.','membership'); ?></p>
					<?php
						$categories = get_categories('get=all');

						if($categories) {
							?>
							<table cellspacing="0" class="widefat fixed">
								<thead>
									<tr>
										<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
										<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Category name', 'membership'); ?></th>
									</tr>
								</thead>

								<tfoot>
									<tr>
										<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
										<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Category name', 'membership'); ?></th>
									</tr>
								</tfoot>

								<tbody>
									<?php
									foreach($categories as $key => $category) {
										?>
										<tr valign="middle" class="alternate" id="post-<?php echo $category->term_id; ?>">
											<th class="check-column" scope="row">
												<input type="checkbox" value="<?php echo $category->term_id; ?>" name="categories[]" <?php if(in_array($category->term_id, $data)) echo 'checked="checked"'; ?>>
											</th>
											<td class="column-name">
												<strong><?php echo esc_html($category->name); ?></strong>
											</td>
									   </tr>
										<?php
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
	}

	function on_negative( $data ) {
		$this->data = array_filter( array_map( 'intval', (array)$data ) );
	}

	function validate_negative() {
		if ( is_single() ) {
			$categories = wp_get_post_categories( get_the_ID() );
			$intersect = array_intersect( $categories, $this->data );
			return empty( $intersect );
		}

		if ( is_category() ) {
			return !in_array( get_queried_object_id(), $this->data );
		}

		return parent::validate_negative();
	}

	function validate_positive() {
		if ( is_single() ) {
			$categories = wp_get_post_categories( get_the_ID() );
			$intersect = array_intersect( $categories, $this->data );
			return !empty( $intersect );
		}

		if ( is_category() ) {
			return in_array( get_queried_object_id(), $this->data );
		}

		return parent::validate_positive();
	}

}

class M_More extends M_Rule {

	var $name = 'more';
	var $label = 'More tag';
	var $description = 'Allows content placed after the More tag to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-more'>
			<h2 class='sidebar-name'><?php _e('More tag', 'membership');?><span><a href='#remove' class='removelink' id='remove-more' title='<?php _e("Remove More tag from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><strong><?php _e('Positive : ','membership'); ?></strong><?php _e('User can read full post content beyond the More tag.','membership'); ?></p>
				<p><strong><?php _e('Negative : ','membership'); ?></strong><?php _e('User is unable to read full post content beyond the More tag.','membership'); ?></p>
				<input type='hidden' name='more[]' value='yes' />
			</div>
		</div>
		<?php
	}

	function on_positive( $data ) {
		global $M_options, $membershippublic;

		$this->data = $data;

		if ( isset( $M_options['moretagdefault'] ) && $M_options['moretagdefault'] == 'no' ) {
			remove_filter( 'the_content_more_link', array( $membershippublic, 'show_moretag_protection' ), 99, 2 );
			remove_filter( 'the_content', array( $membershippublic, 'replace_moretag_content' ), 1 );
			remove_filter( 'the_content_feed', array( $membershippublic, 'replace_moretag_content' ), 1 );
		}
	}

	function on_negative( $data ) {
		global $M_options, $membershippublic;

		$this->data = $data;
		if ( isset( $M_options['moretagdefault'] ) && $M_options['moretagdefault'] != 'no' ) {
			// add the filters - otherwise we don't need to do anything
			add_filter( 'the_content_more_link', array( $membershippublic, 'show_moretag_protection' ), 99, 2 );
			add_filter( 'the_content', array( $membershippublic, 'replace_moretag_content' ), 1 );
			add_filter( 'the_content_feed', array( $membershippublic, 'replace_moretag_content' ), 1 );
		}
	}

}

class M_Comments extends M_Rule {

	var $name = 'comments';
	var $label = 'Comments';
	var $description = 'Allows the display of, or ability to comment on posts to be protected.';

	var $rulearea = 'public';

	function admin_main( $data ) {
		?><div id="main-comments" class="level-operation">
			<h2 class="sidebar-name">
				<?php _e( 'Comments', 'membership' ) ?>
				<span>
					<a id="remove-comments" class="removelink" href="#remove" title=""<?php _e( "Remove Comments from this rules area.", 'membership' ) ?>"><?php
						_e( 'Remove', 'membership' )
					?></a>
				</span>
			</h2>
			<div class="inner-operation">
				<p><strong><?php _e( 'Positive : ', 'membership' ) ?></strong><?php _e( 'User gets read and make comments of posts.', 'membership' ) ?></p>
				<p><strong><?php _e( 'Negative : ', 'membership' ) ?></strong><?php _e( 'User can not read or comment on posts.', 'membership' ) ?></p>
				<input type='hidden' name='comments[]' value='yes' />
			</div>
		</div><?php
	}

	function on_positive( $data ) {
		$this->data = $data;
		add_filter( 'comments_open', array( $this, 'open_comments' ), 99 );
	}

	function on_negative( $data ) {
		$this->data = $data;

		add_filter( 'comments_open', '__return_false', 99 );
		if ( !defined( 'MEMBERSHIP_VIEW_COMMENTS' ) || !filter_var( MEMBERSHIP_VIEW_COMMENTS, FILTER_VALIDATE_BOOLEAN )  ) {
			add_filter( 'comments_array', '__return_empty_array', 99 );
		}
	}

	function open_comments( $open ) {
		return $open;
	}

}

class M_Downloads extends M_Rule {

	var $name = 'downloads';
	var $label = 'Downloads';
	var $description = 'Allows media uploaded to the WordPress media library to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {

		global $wpdb, $M_options;

		if(!$data) $data = array();

		?>
		<div class='level-operation' id='main-downloads'>
			<h2 class='sidebar-name'><?php _e('Downloads', 'membership');?><span><a href='#remove' id='remove-downloads' class='removelink' title='<?php _e("Remove Downloads from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the Downloads / Media to be covered by this rule by checking the box next to the relevant group name.','membership'); ?></p>
				<?php
					$mediasql = $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", '_membership_protected_content' );
					$mediaids = $wpdb->get_col( $mediasql );

					if(!empty($mediaids)) {
						// We have some ids so grab the information
						$attachmentsql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND ID IN(" . implode(",", $mediaids) . ")" );

						$attachments = $wpdb->get_results( $attachmentsql );
					}
					?>
					<table cellspacing="0" class="widefat fixed">
						<thead>
						<tr>
							<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
							<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Download / Group name', 'membership'); ?></th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
							<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Download / Group name', 'membership'); ?></th>
						</tr>
						</tfoot>

						<tbody>
						<?php
						if(!empty($M_options['membershipdownloadgroups'])) {

							foreach($M_options['membershipdownloadgroups'] as $key => $value) {
								if(!empty($value)) {
									?>
									<tr valign="middle" class="alternate" id="group-<?php echo esc_attr(stripslashes(trim($value))); ?>">
										<th class="check-column" scope="row">
											<input type="checkbox" value="<?php echo esc_attr(stripslashes(trim($value))); ?>" name="downloads[]" <?php if(in_array(esc_attr(stripslashes(trim($value))), $data)) echo 'checked="checked"'; ?>>
										</th>
										<td class="column-name">
											<strong><?php echo esc_html(stripslashes(trim($value))); ?></strong>
										</td>
								    </tr>
									<?php
								}
							}

						} else {
							?>
							<tr valign="middle" class="alternate" id="group-nogroup">
								<td class="column-name" colspan='2'>
									<?php echo __('You have no download groups set, please visit the membership options page to set them up.','membership'); ?>
								</td>
						    </tr>
							<?php
						}

						?>
						</tbody>
					</table>

			</div>
		</div>
		<?php
	}

	function can_view_download( $area, $group ) {
		switch ( $area ) {
			case 'positive':
				if ( in_array( $group, (array)$this->data ) ) {
					return true;
				}
				break;
			case 'negative':
				if ( in_array( $group, (array)$this->data ) ) {
					return false;
				}
				break;
			default:
				return false;
		}
	}

}

class M_Shortcodes extends M_Rule {

	var $name = 'shortcodes';
	var $label = 'Shortcodes';
	var $description = 'Allows specific shortcodes and contained content to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {

		global $shortcode_tags;

		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-shortcodes'>
			<h2 class='sidebar-name'><?php _e('Shortcodes', 'membership');?><span><a href='#remove' id='remove-shortcodes' class='removelink' title='<?php _e("Remove Shortcodes from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the Shortcodes to be covered by this rule by checking the box next to the relevant shortcode tag.','membership'); ?></p>
				<?php
					if($shortcode_tags) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
								<tr>
									<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
									<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Shortcode tag', 'membership'); ?></th>
								</tr>
							</thead>

							<tfoot>
								<tr>
									<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
									<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Shortcode tag', 'membership'); ?></th>
								</tr>
							</tfoot>

							<tbody>
								<?php
								foreach($shortcode_tags as $key => $function) {
									?>
									<tr valign="middle" class="alternate" id="post-<?php echo $key; ?>">
										<th class="check-column" scope="row">
											<input type="checkbox" value="<?php echo esc_attr(trim($key)); ?>" name="shortcodes[]" <?php if(in_array(trim($key), $data)) echo 'checked="checked"'; ?>>
										</th>
										<td class="column-name">
											<strong>[<?php echo esc_html(trim($key)); ?>]</strong>
										</td>
								   </tr>
									<?php
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

	function on_creation() {
		//add_filter('the_content', array(&$this, 'override_shortcodes'), 1);
	}

	function override_shortcodes() {
		global $M_shortcode_tags, $shortcode_tags;

		$M_shortcode_tags = $shortcode_tags;

		foreach ( $shortcode_tags as $key => $function ) {
			if ( $key != 'subscriptionform' ) {
				$shortcode_tags[$key] = array( &$this, 'do_protected_shortcode' );
			}
		}

		return $content;
	}

	function on_positive( $data ) {
		global $M_options, $M_shortcode_tags, $shortcode_tags;

		$this->data = $data;

		if ( $M_options['shortcodedefault'] == 'no' ) {
			// Need to re-enable some shortcodes
			foreach ( (array) $data as $key => $code ) {
				if ( isset( $M_shortcode_tags[$code] ) && isset( $shortcode_tags[$code] ) ) {
					$shortcode_tags[$code] = $M_shortcode_tags[$code];
				}
			}
		}
	}

	function on_negative( $data ) {
		global $M_options, $M_shortcode_tags, $shortcode_tags;

		$this->data = $data;
		$M_shortcode_tags = $shortcode_tags;

		if ( $M_options['shortcodedefault'] != 'no' ) {
			// Need to disable some shortcodes
			foreach ( (array) $data as $key => $code ) {
				if ( isset( $M_shortcode_tags[$code] ) && isset( $shortcode_tags[$code] ) ) {
					if ( $code != 'subscriptionform' ) {
						$shortcode_tags[$code] = array( &$this, 'do_protected_shortcode' );
					}
				}
			}
		}
	}

	// Show the protected shortcode message
	function do_protected_shortcode( $atts, $content = null, $code = "" ) {
		global $M_options;
		return stripslashes( $M_options['shortcodemessage'] );
	}

}

class M_Menu extends M_Rule {

	var $name = 'menu';
	var $label = 'Menu';
	var $description = 'Allows specific menu items to be protected.';

	var $rulearea = 'public';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-menu'>
			<h2 class='sidebar-name'><?php _e('Menu', 'membership');?><span><a href='#remove' id='remove-menu' class='removelink' title='<?php _e("Remove Menu from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the Menu items to be covered by this rule by checking the box next to the relevant menu labels.','membership'); ?></p>
				<?php

				$navs = wp_get_nav_menus( array('orderby' => 'name') );

					if(!empty($navs)) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Menu / Item title', 'membership'); ?></th>
								</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('Menu / Item title', 'membership'); ?></th>
								</tr>
							</tfoot>

							<tbody>
						<?php
						foreach($navs as $key => $nav) {
							?>
							<tr valign="middle" class="alternate" id="menu-<?php echo $nav->term_id; ?>-0">
								<td class="column-name" colspan='2'>
									<strong><?php echo __('MENU','membership') . " - " . esc_html($nav->name); ?></strong>
								</td>
						    </tr>
							<?php
							$items = wp_get_nav_menu_items($nav->term_id);
							if(!empty($items)) {
								foreach($items as $ikey => $item) {
									?>
									<tr valign="middle" class="alternate" id="menu-<?php //echo $nav->term_id . '-'; ?><?php echo $item->ID; ?>">
										<th class="check-column" scope="row">
											<input type="checkbox" value="<?php //echo $nav->term_id . '-'; ?><?php echo $item->ID; ?>" name="menu[]" <?php if(in_array($item->ID, $data)) echo 'checked="checked"'; ?>>
										</th>
										<td class="column-name">

											<strong>&nbsp;&#8211;&nbsp;<?php if($item->menu_item_parent != 0) echo "&#8211;&nbsp;"; ?><?php echo esc_html($item->title); ?></strong>
										</td>
								    </tr>
									<?php
								}
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

	function on_positive($data) {

		$this->data = $data;

		add_filter( 'wp_get_nav_menu_items', array(&$this, 'filter_viewable_menus'), 10, 3 );

	}

	function on_negative($data) {

		$this->data = $data;

		add_filter( 'wp_get_nav_menu_items', array(&$this, 'filter_unviewable_menus'), 10, 3 );
	}

	function filter_viewable_menus($items, $menu, $args) {

		if(!empty($items)) {
			foreach($items as $key => $item) {
				if(!in_array($item->ID, $this->data) || ($item->menu_item_parent != 0 && !in_array($item->menu_item_parent, $this->data))) {
					unset($items[$key]);
				}

			}
		}

		return $items;

	}

	function filter_unviewable_menus($items, $menu, $args) {

		if(!empty($items)) {
			foreach($items as $key => $item) {
				if(in_array($item->ID, $this->data) || ($item->menu_item_parent != 0 && in_array($item->menu_item_parent, $this->data))) {
					unset($items[$key]);
				}

			}
		}

		return $items;

	}

}

class M_Blogcreation extends M_Rule {

	var $name = 'blogcreation';
	var $label = 'Blog Creation';
	var $description = 'Allows the creation of blogs to be limited to members.';

	var $rulearea = 'core';

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-blogcreation'>
			<h2 class='sidebar-name'><?php _e('Blog Creation', 'membership');?><span><a href='#remove' id='remove-blogcreation' class='removelink' title='<?php _e("Remove Blog Creation from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<?php
					if(!isset($data['number'])) {
						$data['number'] = '';
					}
				?>
				<p><strong><?php _e('Positive : ','membership'); ?></strong><?php _e('User can create ','membership'); ?><input type='text' name='blogcreation[number]' value='<?php echo esc_attr($data['number']); ?>' /><?php _e(' blogs.','membership'); ?><br/><em><?php _e('Leave blank for unlimited blogs.','membership'); ?></em></p>
				<p><strong><?php _e('Negative : ','membership'); ?></strong><?php _e('User is unable to create any blogs.','membership'); ?></p>
				<input type='hidden' name='blogcreation[]' value='yes' />
			</div>
		</div>
		<?php
	}

	function on_creation() {

	}

	function on_positive($data) {

		$this->data = $data;

		add_filter( 'site_option_registration', array(&$this, 'pos_blog_creation'));
		add_filter( 'wpmu_active_signup', array(&$this, 'pos_blog_creation') );
	}

	function on_negative($data) {

		$this->data = $data;

		add_filter( 'site_option_registration', array(&$this, 'neg_blog_creation'));
		add_filter( 'wpmu_active_signup', array(&$this, 'neg_blog_creation') );

	}

	function neg_blog_creation( $active = 'all' ) {

		if($active == 'user' || $active == 'none') {
			return $active;
		} else {
			return 'none';
		}

	}

	function pos_blog_creation( $active = 'all' ) {

		if($active == 'user' || $active == 'none') {
			return $active;
		} else {
			// Check our count
			if(empty($this->data['number'])) {
				//  unlimited
				return $active;
			} else {
				$thelimit = (int) $this->data['number'];

				if( $thelimit > (int) $this->current_blog_count() ) {
					return $active;
				} else {
					return $this->neg_blog_creation( $active );
				}
			}

		}

	}

	function current_blog_count() {

		global $member, $wpdb;

		if(!empty($member) && method_exists($member, 'has_cap')) {
			// We have a member and it is a correct object
			$count = 0;
			$blogs = get_blogs_of_user( $member->ID );
			foreach( $blogs as $blog ) {
				if( $this->is_user_blog_admin( $member->ID, $blog->userblog_id ) ) {
					$count++;
	         	}
			}

			return (int) $count;
		} else {
			return 0;
		}

	}

	function is_user_blog_admin( $user_id, $blog_id ) {
		global $wpdb;

	    $meta_key = $wpdb->base_prefix . $blog_id . "_capabilities";

		$role_sql = $wpdb->prepare( "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s", $meta_key );

		$role = $wpdb->get_results( $role_sql );

		//clean the role
		foreach($role as $key => $r) {
			$role[$key]->meta_value = maybe_unserialize($r->meta_value);
		}

		foreach($role as $key => $r) {
			if( $r->meta_value['administrator'] == 1 && $r->user_id == $user_id ) {
				return true;
			}
		}

		return false;

	}

}

class M_URLGroups extends M_Rule {

	var $name = 'urlgroups';
	var $label = 'URL Groups';
	var $description = "Allows specific URL's to be protected (includes ability to protect using regular expressions).";

	var $rulearea = 'core';

	function get_groups() {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM " . membership_db_prefix( $wpdb, 'urlgroups' ) . " WHERE groupname NOT LIKE (%s) ORDER BY id ASC",
			'\_%'
		) );
	}

	function admin_main($data) {
		if(!$data) $data = array();
		?>
		<div class='level-operation' id='main-urlgroups'>
			<h2 class='sidebar-name'><?php _e('URL Groups', 'membership');?><span><a href='#remove' id='remove-urlgroups' class='removelink' title='<?php _e("Remove URL Groups from this rules area.",'membership'); ?>'><?php _e('Remove','membership'); ?></a></span></h2>
			<div class='inner-operation'>
				<p><?php _e('Select the URL Groups to be covered by this rule by checking the box next to the relevant URL Group name.','membership'); ?></p>
				<?php
					$urlgroups = $this->get_groups();

					if(!empty($urlgroups)) {
						?>
						<table cellspacing="0" class="widefat fixed">
							<thead>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('URL Group', 'membership'); ?></th>
								</tr>
							</thead>

							<tfoot>
							<tr>
								<th style="" class="manage-column column-cb check-column" id="cb" scope="col"><input type="checkbox"></th>
								<th style="" class="manage-column column-name" id="name" scope="col"><?php _e('URL Group', 'membership'); ?></th>
								</tr>
							</tfoot>

							<tbody>
						<?php
						foreach($urlgroups as $key => $urlgroup) {
							?>
							<tr valign="middle" class="alternate" id="urlgroup-<?php echo $urlgroup->id; ?>">
								<th class="check-column" scope="row">
									<input type="checkbox" value="<?php echo $urlgroup->id; ?>" name="urlgroups[]" <?php if(in_array($urlgroup->id, $data)) echo 'checked="checked"'; ?>>
								</th>
								<td class="column-name">
									<strong><?php echo esc_html($urlgroup->groupname); ?></strong>
								</td>
						    </tr>
							<?php
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
		$this->data = $data;
		if ( !empty( $this->data ) && is_array( $this->data ) ) {
			foreach ( $this->data as $group_id ) {
				$group = new M_Urlgroup( $group_id );
				M_add_to_global_urlgroup( $group->group_urls_array(), 'positive' );
			}
		}
	}

	function on_negative( $data ) {
		$this->data = $data;
		if ( !empty( $this->data ) && is_array( $this->data ) ) {
			foreach ( $this->data as $group_id ) {
				$group = new M_Urlgroup( $group_id );
				M_add_to_global_urlgroup( $group->group_urls_array(), 'negative' );
			}
		}
	}

	function validate_negative() {
		global $M_global_groups;

		$host = is_ssl() ? "https://" : "http://";
		$host .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$exclude = apply_filters( 'membership_excluded_urls', array() );
		if( membership_check_expression_match( $host, $exclude ) ) {
			return true;
		}

		$found = false;

		$negative = !empty( $M_global_groups['negative'] )
			? array_unique( $M_global_groups['negative'] )
			: array();

		if ( !empty( $negative ) ) {
			$found |= membership_check_expression_match( $host, $negative );
		}

		return !$found;

	}

	function validate_positive() {
		global $M_global_groups;

		$host = is_ssl() ? "https://" : "http://";
		$host .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		$exclude = apply_filters( 'membership_excluded_urls', array() );
		if( membership_check_expression_match( $host, $exclude ) ) {
			return true;
		}

		$found = false;

		$negative = !empty( $M_global_groups['positive'] )
			? array_unique( $M_global_groups['positive'] )
			: array();

		if ( !empty( $negative ) ) {
			$found |= membership_check_expression_match( $host, $negative );
		}

		return $found;
	}

}

add_action( 'plugins_loaded', 'M_setup_default_rules', 99 );
function M_setup_default_rules() {
	M_register_rule( 'downloads', 'M_Downloads', 'content' );
	M_register_rule( 'comments', 'M_Comments', 'main' );
	M_register_rule( 'more', 'M_More', 'main' );
	M_register_rule( 'categories', 'M_Categories', 'main' );
	M_register_rule( 'pages', 'M_Pages', 'main' );
	M_register_rule( 'posts', 'M_Posts', 'main' );
	M_register_rule( 'shortcodes', 'M_Shortcodes', 'content' );
	M_register_rule( 'menu', 'M_Menu', 'main' );
	M_register_rule( 'urlgroups', 'M_URLGroups', 'main' );

	if ( is_multisite() ) {
		M_register_rule( 'blogcreation', 'M_Blogcreation', 'admin' );
	}
}