<?php
/**
 * Set the timezone to the wordpress install timezone setting.
 */
$timezone = get_option( 'timezone_string' );
date_default_timezone_set( $timezone );

/**
 * A function to write out how long ago something was. i.e. 1 day ago, 2 days ago, 4 hours ago.
 */
function ulh_elapsed_time( $timestamp ) {
	$timestamp = strtotime( $timestamp );
    $timestamp = time() - $timestamp;
    //if no time was passed return 0 seconds
    if ( $timestamp < 1 ) {
        return '0 seconds';
    }

    //create multi-array with seconds and define values
    $values = array( 
        12*30*24*60*60  =>  'year',
        30*24*60*60     =>  'month',
        24*60*60        =>  'day',
        60*60           =>  'hour',
        60              =>  'minute',
        1               =>  'second'
    );

    //loop over the array
    foreach ( $values as $secs => $point ) {
        //check if timestamp is equal or bigger the array value
        $divRes = $timestamp / $secs;
        if ( $divRes >= 1 )
        {
            //if timestamp is bigger, round the divided value and return it
            $res = round( $divRes );
            return $res . ' ' . $point . ( $res > 1 ? 's' : '' ) . ' ago';
        }
    }
}

/**
 * Updates the user history table whenever someone logs in.
 */
function ulh_update_table( $username, $password ){
	$username = trim( $username );
	if ( !empty( $username ) && !empty( $password ) && $username != '' && trim( $password ) != '' && username_exists( $username ) ) {
		$auth = wp_authenticate_username_password( NULL, $username, $password );
		if(!is_wp_error($auth)){
			global $wpdb;
			$user = get_user_by( 'login', $username );
			$user_ID = $user->ID;
			$wp_user_history = $wpdb->prefix . "user_history";
			$query = $wpdb->prepare( "INSERT INTO " . $wp_user_history . " (user_id) VALUES (%d)", $user_ID );
			$wpdb->query( $query );
		}
	}
}
add_action( 'wp_authenticate', 'ulh_update_table', 10, 2 );


/**
 * Export Data
 */
function ulh_export_to_csv() {
    global $wpdb;
    
	$wp_user_history = $wpdb->prefix . "user_history";
	$wp_users = $wpdb->prefix . "users";
	
	if( isset($_POST["ulhFilter"]) && !empty($_POST["ulhFilter"]) ){
		$ulhFilter	= $_POST["ulhFilter"];
	}
	if( isset($_POST["dateFrom"]) && !empty($_POST["dateFrom"]) ){
		$dateFrom	= $_POST["dateFrom"];
	}
	if( isset($_POST["dateTo"]) && !empty($_POST["dateTo"]) ){
		$dateTo		= $_POST["dateTo"];
	}
	
    $orderby = 'uh_id';
    $order = 'DESC';
	
	
	$wp_users = mysql_real_escape_string( $wp_users );
	$wp_user_history = mysql_real_escape_string( $wp_user_history );
	
	// Let's build the query based on GET variables.
	$searchQuery = "SELECT 
		    			uh.id AS uh_id, 
		    			uh.user_id 		AS uh_user_id, 
		    			uh.login_date 	AS uh_login_date, 
		    			uh.login_date 	AS uh_last_log_in, 
		    			u.user_email 	AS u_user_email, 
		    			u.user_login 	AS u_user_login, 
		    			u.display_name 	AS u_display_name ";
		    			
	// If per user is selected - show a row count	    			
    if( isset( $ulhFilter ) && $ulhFilter=='perUser' ) {
	    $searchQuery .= ",
	    			COUNT(*) 	AS uh_count ";
    }
    	    
    $searchQuery .= " 
    				FROM ".$wp_user_history." uh 
	    				LEFT JOIN ".$wp_users." u 
						ON uh.user_id=u.ID";
				
    		// If a search was submitted, add search results to query
	if( isset( $_POST["search"] ) && trim( $_POST["search"] ) != '' && !is_null( $_POST["search"] ) ){
    	$search = trim( $_POST["search"] );
    	$searchQuery .= " 
			        	WHERE
			    			(u.display_name LIKE '%$search%'
			    			OR u.user_email LIKE '%$search%'
			    			OR u.user_login LIKE '%$search%'
			    			OR u.ID LIKE '%$search%')";
		// If date range was selected, add date range to query
		if( isset( $dateFrom ) && isset( $dateTo ) ) {
			$searchQuery .= "
				    		AND (uh.login_date >= '$dateFrom'
							AND uh.login_date <= '$dateTo 23:59:59')";
	    }
	} elseif ( isset( $dateFrom ) && isset( $dateTo ) ) {
			$searchQuery .= "
				    		WHERE
					    		(uh.login_date >= '$dateFrom'
								AND uh.login_date <= '$dateTo 23:59:59')";
	}
			
	// Group by user id if Per User was selected
	if( isset( $ulhFilter ) && $ulhFilter=='perUser' ) {
		$searchQuery .= "
			    		GROUP BY uh.user_id";
    }
    
    // Set the order of results
	$searchQuery .= "
			    	ORDER BY $orderby $order";
		
	$user_data = $wpdb->get_results($searchQuery);


	$folder = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/user-login-history-pro/export/";
	
	if ( isset( $dateFrom ) && isset( $dateTo ) ) {
		$file_name = "UserLoginHistory-".$dateFrom."-".$dateTo.".csv";
	} else {
		$file_name = "UserLoginHistory-".rand(0,99999).".csv";
	}
	
	$file_uri = $folder.$file_name;
	
	$output = fopen($file_uri,'w+');		
	
	if( isset( $ulhFilter ) && $ulhFilter=='perUser' ) {
		fputcsv($output, array('Login', 'Name', 'Email', 'Number of Logins', 'Last Login Date'));
	} else {
		fputcsv($output, array('Login', 'Name', 'Email', 'Date'));
	}
	
	foreach($user_data as $row) {
		$u_display_name = ( !empty($row->u_display_name) ? $row->u_display_name : 'User Deleted' );
		$u_user_email = $row->u_user_email;
		$u_user_login = $row->u_user_login;
		$uh_login_date = $row->uh_login_date;
		if( isset( $ulhFilter ) && $ulhFilter=='perUser' ) {
			$uh_count = $row->uh_count;
			$uh_last_log_in = $row->uh_last_log_in;
			fputcsv( $output, array('Login' => $u_user_login, 'Name' => $u_display_name, 'Email' => $u_user_email, 'Number of Logins' => $uh_count, 'Last Login Date' => $uh_last_log_in ) );
	    } else {
			fputcsv( $output, array('Login' => $u_user_login, 'Name' => $u_display_name, 'Email' => $u_user_email, 'Date' => $uh_login_date ) );
	    }
	}
	
	fclose($output);
	
	echo json_encode( array( 'status' => 'success', 'file_name' => $file_name ) );
	die();
}
add_action('wp_ajax_ulh_export_to_csv', 'ulh_export_to_csv');
add_action('wp_ajax_nopriv_ulh_export_to_csv', 'ulh_export_to_csv');



/**
 * Create a new Wordpress table class
 */
require_once( 'class-wp-list-table.php' );
class ULH_Table extends UHL_List_Table {
   /**
    * Constructor focusing on three parameters: singular and plural labels, as well as whether the class supports AJAX.
    */
    public function __construct() {
		parent::__construct( array(
			'singular'	=> 'record', //Singular label
			'plural' 	=> 'records', //plural label, also this well be one of the table css class
			'ajax'   	=> false
		) );

        $this->set_order();
        $this->set_orderby();
    }
    
    private function get_sql_results() {
        global $wpdb;
        
		$wp_user_history = $wpdb->prefix . "user_history";
		$wp_users = $wpdb->prefix . "users";
		$orderby = $this->orderby;
		$order = $this->order;
		
		$order = mysql_real_escape_string( $order );
		$orderby = mysql_real_escape_string( $orderby );
		$wp_users = mysql_real_escape_string( $wp_users );
		$wp_user_history = mysql_real_escape_string( $wp_user_history );
		
		// Let's build the query based on GET variables.
		$searchQuery = "SELECT 
			    			uh.id AS uh_id, 
			    			uh.user_id 		AS uh_user_id, 
			    			uh.login_date 	AS uh_login_date, 
			    			uh.login_date 	AS uh_last_log_in, 
			    			u.user_email 	AS u_user_email, 
			    			u.user_login 	AS u_user_login, 
			    			u.display_name 	AS u_display_name ";
			    			
		// If per user is selected - show a row count	    			
	    if( isset( $_GET["ulhFilter"] ) && $_GET["ulhFilter"]=='perUser' ) {
		    $searchQuery .= ",
		    			COUNT(*) 	AS uh_count ";
	    }
	    	    
	    $searchQuery .= " 
	    				FROM ".$wp_user_history." uh 
		    				LEFT JOIN ".$wp_users." u 
							ON uh.user_id=u.ID";
					
	    		// If a search was submitted, add search results to query
		if( isset( $_GET["s"] ) && trim( $_GET["s"] ) != '' && !is_null( $_GET["s"] ) ){
        	$search = trim( $_GET["s"] );
        	$searchQuery .= " 
				        	WHERE
				    			(u.display_name LIKE '%$search%'
				    			OR u.user_email LIKE '%$search%'
				    			OR u.user_login LIKE '%$search%'
				    			OR u.ID LIKE '%$search%')";
			// If date range was selected, add date range to query
			if( isset( $_GET["dateFrom"] ) && isset( $_GET["dateTo"] ) ) {
				$dateFrom	= $_GET["dateFrom"];
				$dateTo		= $_GET["dateTo"];
				$searchQuery .= "
					    		AND (uh.login_date >= '$dateFrom'
								AND uh.login_date <= '$dateTo 23:59:59')";
		    }
		} elseif ( isset( $_GET["dateFrom"] ) && isset( $_GET["dateTo"] ) ) {
				$dateFrom	= $_GET["dateFrom"];
				$dateTo		= $_GET["dateTo"];
				$searchQuery .= "
					    		WHERE
						    		(uh.login_date >= '$dateFrom'
									AND uh.login_date <= '$dateTo 23:59:59')";
		}
				
		// Group by user id if Per User was selected
		if( isset( $_GET["ulhFilter"] ) && $_GET["ulhFilter"]=='perUser' ) {
			$searchQuery .= "
				    		GROUP BY uh.user_id";
	    }
	    
	    // Set the order of results
		$searchQuery .= "
				    	ORDER BY $orderby $order";
			
		$sql_results = $wpdb->get_results($searchQuery);
        return $sql_results;
    }
	
	public function set_order() {
        if ( isset( $_GET['order'] ) ) {
            $order = $_GET['order'];
        } else {
        	// Set order to descending by default
	    	$order = 'DESC';
        }
        $this->order = esc_sql( $order );
    }
    
    public function set_orderby() {
        if ( isset( $_GET['orderby'] ) && $_GET['orderby']=='uh_count' && isset( $_GET['ulhFilter'] ) && $_GET['ulhFilter']=='perLogin' ) {
            $orderby = 'uh_id';
        } elseif ( isset( $_GET['orderby'] ) ) {
            $orderby = $_GET['orderby'];
        } else {
        	//order by history id by default (shows newest first)
	    	$orderby = 'uh_id';
        }
        $this->orderby = esc_sql( $orderby );
    }
    
	/**
	 * Define the columns that are going to be used in the table
	 * return array $columns, the array of columns to use with the table
	 */
	public function get_columns() {
		if( isset( $_GET["ulhFilter"]) && $_GET["ulhFilter"]=='perUser' ) {
			if( isset( $_GET["dateFrom"] ) && isset( $_GET["dateTo"] ) ){
				$columns = array(
					'u_user_login'		=> __('Login'),
					'u_display_name'	=> __('Name'),
					'u_user_email'		=> __('Email'),
					'uh_last_log_in'	=> __('Last Log In'),
					'uh_login_date'		=> __('Date'),
					'uh_count'			=> __('Total Logins In Date Range')
				);
			} else {
				$columns = array(
					'u_user_login'		=> __('Login'),
					'u_display_name'	=> __('Name'),
					'u_user_email'		=> __('Email'),
					'uh_last_log_in'	=> __('Last Log In'),
					'uh_login_date'		=> __('Date'),
					'uh_count'			=> __('Total Logins')
				);
			}
		} else {
			$columns = array(
				'u_user_login'		=> __('Login'),
				'u_display_name'	=> __('Name'),
				'u_user_email'		=> __('Email'),
				'uh_last_log_in'	=> __('Last Log In'),
				'uh_login_date'		=> __('Date')
			);
		}
		return $columns;
	}
	
	/**
	 * Decide which columns to activate the sorting functionality on
	 * return array $sortable, the array of columns that can be sorted by the user
	 */
	public function get_sortable_columns() {
		if( isset( $_GET["ulhFilter"]) && $_GET["ulhFilter"]=='perUser' ) {
			$sortable = array(
				//'uh_user_id'=> array('uh_user_id', true),
				'u_user_login'		=> array('u_user_login', true),
				'u_display_name'	=> array('u_display_name', true),
				'u_user_email'		=> array('u_user_email', true),
				'uh_last_log_in'	=> array('uh_last_log_in', true),
				'uh_login_date'		=> array('uh_login_date', true),
				'uh_count'			=> array('uh_count', true)
			);
		} else {
			$sortable = array(
				//'uh_user_id'=> array('uh_user_id', true),
				'u_user_login'		=> array('u_user_login', true),
				'u_display_name'	=> array('u_display_name', true),
				'u_user_email'		=> array('u_user_email', true),
				'uh_last_log_in'	=> array('uh_last_log_in', true),
				'uh_login_date'		=> array('uh_login_date', true)
			);
		}
		return $sortable;
	}
	
	// Prepare the table with different parameters, pagination, columns and table elements
	public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        // SQL results
        $records = $this->get_sql_results();
        empty( $records ) AND $records = array();
        
        // Time to put together the pagination..
	        $per_page     = 25;
	        $current_page = $this->get_pagenum();
	        $total_items  = count( $records );
	        $this->set_pagination_args( array (
	            'total_items' => $total_items,
	            'per_page'    => $per_page,
	            'total_pages' => ceil( $total_items / $per_page ),
	        ) );
	        $last_post = $current_page * $per_page;
	        $first_post = $last_post - $per_page + 1;
	        $last_post > $total_items && $last_post = $total_items;
	
	        /**
	         * Set up the range of keys that contain 
	         * the posts on the currently displayed page(d).
	         * Flip keys with values as the range outputs the range in the values.
	         */
	        $range = array_flip( range( $first_post - 1, $last_post - 1, 1 ) );
	
	        // Filter out the posts we're not displaying on the current page.
	        $records_array = array_intersect_key( $records, $range );

        // Prepare the data
        foreach ( $records_array as $key => $record ) {
        	//Change uh_login_date date format - example 08/15/2014 - 11:56 AM
			$date = $record->uh_login_date;
			$record->uh_login_date = date("m/d/Y - g:i A", strtotime($date));
			//Change uh_last_log_in to show the elapsed time since last login
			$date_logged = $record->uh_last_log_in;
			$record->uh_last_log_in = ulh_elapsed_time($date_logged);
        }
        $this->items = $records_array;
        
	}
	
	public function column_default( $item, $column_name ) {
        return $item->$column_name;
    }
    
    public function display_tablenav( $which ) {
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>
            <br class="clear" />
        </div>
        <?php
    }
    
    public function extra_tablenav( $which ) {
	    global $wpdb, $wp_meta_boxes;
        if(isset($_GET['ulhFilter'])){
        	$ulhFilter = $_GET['ulhFilter'];
        } else {
	        $ulhFilter = '';
        }
	    if ( $which == "top" ){
	        ?>
	        <div class="alignleft">
	            <select id="ulh_filter" name="ulh_filter">
	                <option value="perLogin" <?php if( $ulhFilter=='perLogin' ){ echo 'selected'; } ?>>View Results Per Login</option>
	                <option value="perUser" <?php if( $ulhFilter=='perUser' ){ echo 'selected'; } ?>>View Results Per User</option>
	            </select>
	        </div>
	        <form class="alignleft" id="dateSelect">
	        	&nbsp;&nbsp;Date Range:
				<input type="text" data-beatpicker="true" data-beatpicker-position="['*','*']" data-beatpicker-range="true" data-beatpicker-module="footer, clear">
				<input class="button" type="submit" value="Go" />
	        </form>
	        <?php
	    }
	    
        $views = $this->get_views();
        if ( empty( $views ) )
            return;

        $this->views();
    }
}

?>