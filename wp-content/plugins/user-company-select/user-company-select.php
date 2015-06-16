<?php
/**
 * Plugin Name: User Company Select
 * Author: Curtis
 * Description: Allows users to be assigned to a company.
 * Version: 0.0.1
*/


/**
 * Runs when plugin is activated
 */
register_activation_hook( __FILE__,'ucs_install' ); 
register_uninstall_hook( __FILE__, 'ucs_uninstall' );
register_deactivation_hook(__FILE__, 'ucs_uninstall');


/**
 * Creates new database field(s) associated with plugin
 */
function ucs_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
/*
	$user_company_list = $wpdb->prefix . "user_company_list";
	
	//Create user history table.
	$query = "CREATE TABLE IF NOT EXISTS `" . $user_company_list . "` (
				`id` int(11) NOT NULL auto_increment,
				`company` int(11) NOT NULL,
				`company` INT(11) NULL DEFAULT NULL,
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;";
	dbDelta( $query );
*/
}

/**
 * Uninstall function
 **/
function ucs_uninstall() {
	global $wpdb;
/*
	$user_company_select = $wpdb->prefix . 'user_company_list';
	
	//Delete user history table.
	$query = "DROP TABLE " . $user_company_list;
	$wpdb->query( $query );
*/
}


/**
 * Adds Company selection to user accounts
 */
add_action( 'show_user_profile', 'show_company_select_fields' );
add_action( 'edit_user_profile', 'show_company_select_fields' );

function show_company_select_fields( $user ) { ?>

	<h3>Company</h3>

	<table class="form-table">

		<tr>
			<th><label for="company_select">Select Company</label></th>

			<td>
				<select name="company_select" id="company_select">
					<!--
<option value="PayProTec" <?php echo ( $company_select == 'PayProTec' || !isset($company_select) || empty($company_select) ? "selected" : ""); ?>>PayProTec</option>
					<option value="Pilothouse" <?php echo ( isset($company_select) && trim($company_select) == 'Pilothouse' ? "selected" : ""); ?>>Pilothouse Solutions</option>
					<option value="SuperiorProcessing" <?php echo ( isset($company_select) && trim($company_select) == 'SuperiorProcessing' ? "selected" : ""); ?>>Superior Processing</option>
-->
					
					<?php
						$company_select = trim(get_the_author_meta( 'company_select', $user->ID ));
						
						if( have_rows('companies', 'option') ){
							echo '<option value="PayProTec">PayProTec - Default</option>';
							while ( have_rows('companies', 'option') ) { 
								the_row();
								
								$company_select_name = get_sub_field('company_name');
								$company_select_link = get_sub_field('company_link');
								$company_select_logo = get_sub_field('company_logo');
								
								$cur_company = preg_replace('/\s+/', '', $company_select_name);
								if( $company_select === $cur_company ){
									$selected = 'selected';
								} else {
									$selected = '';
								}
								
								echo '<option value="'.$cur_company.'" '.$selected.'>'.$company_select_name.'</option>';
							
							}
						} else {
							echo '<option value="PayProTec">PayProTec - Default</option>';
						}	
					?>
				</select>
			</td>
		</tr>

	</table><br /><br />
<?php }

add_action( 'personal_options_update', 'save_company_select_fields' );
add_action( 'edit_user_profile_update', 'save_company_select_fields' );

function save_company_select_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'company_select', $_POST['company_select'] );
}




function company_select_data($company_select=null){
	
	if( !is_null($company_select) ){
		
		if( have_rows('companies', 'option') ){
			
			// loop through the rows of data
			while ( have_rows('companies', 'option') ) { the_row();
				
				$company_name = get_sub_field('company_name');
					
				$cur_company = preg_replace('/\s+/', '', $company_select);
				if( $company_name === $cur_company ){
					
					// display a sub field value
					$company_select_name = $company_name;
					$company_select_link = get_sub_field('company_link');
					$company_select_logo = get_sub_field('company_logo');
					
				}
			
			}
			if( !isset($company_select_name) && !isset($company_select_link)  ) {
				$company_select_name = 'PayProTec';
				$company_select_link = 'http://payprotec.com';
				$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
			}
		
		} else {
			$company_select_name = 'PayProTec';
			$company_select_link = 'http://payprotec.com';
			$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
		}
	} else {
		$company_select_name = 'PayProTec';
		$company_select_link = 'http://payprotec.com';
		$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
	}
	
/*
	if( !is_null($company_select) && trim($company_select) == 'Pilothouse' ) {
		$company_select_name = 'Pilothouse';
		$company_select_link = 'http://pilothousepayments.com/';
		$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/pilothouse-white.png';
	} elseif( !is_null($company_select) && trim($company_select) == 'SuperiorProcessing' ) {
		$company_select_name = 'Superior Processing';
		$company_select_link = 'http://www.superiorprocessingsolutions.com/';
		$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/superiorps-logo.png';
	} else {
		$company_select_name = 'PayProTec';
		$company_select_link = 'http://payprotec.com';
		$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
	}
*/
	return array('name'=>$company_select_name, 'link'=>$company_select_link, 'logo'=>$company_select_logo);
}

function company_get($company_select=null){
	if( isset($_GET['comp']) && trim($_GET['comp']) !== '' ){
		session_start();
		$_SESSION['company'] = $_GET['comp'];
	}
	
	if(isset($_SESSION['company']) && $_SESSION['company']==='phs'){
		
		if( have_rows('companies', 'option') ){
			
			// loop through the rows of data
			while ( have_rows('companies', 'option') ) { the_row();
				
				$company_name = get_sub_field('company_name');
					
				$cur_company = preg_replace('/\s+/', '', $company_select);
				if( $company_name == 'Pilothouse' ){
					
					// display a sub field value
					$company_select_name = $company_name;
					$company_select_link = get_sub_field('company_link');
					$company_select_logo = get_sub_field('company_logo');
					
				}
			
			}
			if( !isset($company_select_name) && !isset($company_select_link)  ) {
				$company_select_name = 'PayProTec';
				$company_select_link = 'http://payprotec.com';
				$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
			}
		
		} else {
			$company_select_name = 'PayProTec';
			$company_select_link = 'http://payprotec.com';
			$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
		}
	} else {
		$company_select_name = 'PayProTec';
		$company_select_link = 'http://payprotec.com';
		$company_select_logo = 'https://my.saltsha.com/wp-content/plugins/transactional-data/images/ppt-white.png';
	}
	return array('name'=>$company_select_name, 'link'=>$company_select_link, 'logo'=>$company_select_logo);
}

?>