<?php

/**
*	Displays all terminal rows in a table where called
**/
function display_terminals(){
	global $wpdb;
	
	$terminals = $wpdb->prefix . "terminals";
	
	$terminal_query =	$wpdb->get_results("SELECT * FROM $terminals");
						
	// Set up the terminals table
	$display_terminals = 	"<h3>Terminals</h3>".
							"<table class='widefat' style='max-width:600px;'>".
								"<thead><tr>".
									"<th>Terminal Name</th>".
									"<th></th>".
								"</tr></thead>".
								"<tbody>";
	$i=0;
	foreach($terminal_query as $terminal){
		$i++;
		if($i % 2 == 0){
			$class = 'alternate';
		} else {
			$class = '';
		}
		$display_terminals .= 	"<tr class='".$class."' >".
									"<td>".$terminal->terminal_name."</td>".
									"<td align='right'><a href='#' data-id='".$terminal->id."' data-type='terminal' class='delete_terminal_item'>Delete</a></td>".
								"</tr>";
	}
	
	$display_terminals .= 	"</tbody>".
					"</table>";
					
	echo $display_terminals;
	
	// Necessary for Ajax to correctly display the data in Wordpress
	if(isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax']==1){
		die();
	}
}
add_action('wp_ajax_display_terminals', 'display_terminals');
add_action('wp_ajax_nopriv_display_terminals', 'display_terminals');

/**
*	Displays all paper rows in a table where called
**/
function display_paper(){
	global $wpdb;
	
	$terminal_paper = $wpdb->prefix . "terminal_paper";
	
	$paper_query =	$wpdb->get_results("SELECT * FROM $terminal_paper");
					
	// Set up the paper table
	$display_paper = 	"<h3>Paper</h3>".
						"<table class='widefat' style='max-width:600px;'>".
							"<thead><tr>".
								"<th>Size</th>".
								"<th>Type</th>".
								"<th>Transactions</th>".
								"<th></th>".
							"</tr></thead>".
							"<tbody>";
	$i=0;
	foreach($paper_query as $paper){
		$i++;
		if($i % 2 == 0){
			$class = 'alternate';
		} else {
			$class = '';
		}
		$display_paper .= 	"<tr class='".$class."' >".
								"<td>".$paper->size."</td>".
								"<td>".$paper->type."</td>".
								"<td>".$paper->transactions."</td>".
								"<td align='right'><a href='#' data-id='".$paper->id."' data-type='paper' class='delete_terminal_item'>Delete</a></td>".
							"</tr>";
	}
	
	$display_paper .= 	"</tbody>".
					"</table>";
						
	echo $display_paper;
	
	// Necessary for Ajax to correctly display the data in Wordpress
	if(isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax']==1){
		die();
	}
}
add_action('wp_ajax_display_paper', 'display_paper');
add_action('wp_ajax_nopriv_display_paper', 'display_paper');

/**
*	Adds a new row to the database when the terminal form is submitted.
*	Data sent via ajax
**/
function update_terminal(){
	global $wpdb;

	$terminals = $wpdb->prefix . "terminals";
	
	// Parses the incoming string
	$terminal = array();
	parse_str($_POST['terminal'], $terminal);
	
	$terminal_name = $terminal['terminal_name'];
	
	if( isset($terminal_name) && !empty(trim($terminal_name)) ){
		
		$terminal_insert = $wpdb->query('INSERT INTO '.$terminals.' (terminal_name) VALUES ("'.$terminal_name.'");');
		if($terminal_insert){
			echo "Done";
		} else {
			echo "Something went wrong while updating the database.";
		}
	} else {
		echo "Fields not set";
	}
			
	die();
}
add_action('wp_ajax_update_terminal', 'update_terminal');
add_action('wp_ajax_nopriv_update_terminal', 'update_terminal');

/**
*	Adds a new row to the database when the paper form is submitted.
*	Data sent via ajax
**/
function update_paper(){
	global $wpdb;
	
	$terminal_paper = $wpdb->prefix . "terminal_paper";
	
	// Parses the incoming string
	$paper = array();
	parse_str($_POST['paper'], $paper);
	
	$paper_size = $paper['paper_size'];
	$paper_type = $paper['paper_type'];
	$paper_transactions = $paper['transactions'];
	
	if( isset($paper_size) && !empty(trim($paper_size)) && isset($paper_type) && !empty(trim($paper_type)) && isset($paper_transactions) && !empty(trim($paper_transactions)) ){
		$paper_insert = $wpdb->insert($terminal_paper, array(
						   "size" => $paper_size,
						   "type" => $paper_type,
						   "transactions" => $paper_transactions
						));
		if($paper_insert){
			echo "Done";
		} else {
			echo "Something went wrong while updating the database.";
		}
	} else {
		echo "Fields not set";
	}
	
	die();
}
add_action('wp_ajax_update_paper', 'update_paper');
add_action('wp_ajax_nopriv_update_paper', 'update_paper');

/**
*	Deletes the row in the database that corresponds to the item clicked in the paper or terminals tables.
*	Data sent via ajax
**/
function delete_terminal_item(){
	global $wpdb;
	
	if( isset($_POST['type']) && !empty(trim($_POST['type'])) && isset($_POST['item_id']) && !empty(trim($_POST['item_id'])) ){
	
		$type		= 	$_POST['type'];
		$item_id	=	$_POST['item_id'];
	
		if( $type=='paper' ){
			$table = $wpdb->prefix . "terminal_paper";
		} elseif( $type=='terminal' ) {
			$table = $wpdb->prefix . "terminals";
		} else {
			echo "No type";
			return false;
			die();
		}
	
		$delete_item =	$wpdb->delete( $table, array( 'id' => $item_id ) );
		
		if($delete_item){
			echo "Done";
		} else {
			echo "MySQL Error";
		}
	}
	
	die();
}
add_action('wp_ajax_delete_terminal_item', 'delete_terminal_item');
add_action('wp_ajax_nopriv_delete_terminal_item', 'delete_terminal_item');


/**
*	Create shortcode for terminal selection
**/
function terminal_select(){
	global $wpdb;
	
	$terminals = $wpdb->prefix . "terminals";
	
	$terminal_query =	$wpdb->get_results("SELECT * FROM $terminals");
	
	echo	"<select class='form-control small' name='terminal_type' id='terminal_type'>".
				"<option disabled selected>Terminal Type</option>";
				
					foreach($terminal_query as $terminal){
						echo "<option value='".$terminal->terminal_name."'>".$terminal->terminal_name."</option>";
					}
					
	echo	"</select>";
	
}
add_shortcode('terminal_select', 'terminal_select');

/**
*	Create shortcode for paper selection
**/
function paper_select(){
	global $wpdb;
	
	$terminal_paper = $wpdb->prefix . "terminal_paper";
	
	$paper_query =	$wpdb->get_results("SELECT * FROM $terminal_paper");
	
	echo	"<select class='form-control small' name='paper_rolls' id='paper_rolls'>".
				"<option disabled selected>Paper Rolls</option>";
				
					foreach($paper_query as $paper){
						echo "<option value='".$paper->size." - ".$paper->type."'>".$paper->size." - ".$paper->type."</option>";
					}
					
	echo	"</select>";
	
}
add_shortcode('paper_select', 'paper_select');

?>