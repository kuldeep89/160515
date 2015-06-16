<?php
/**
* Fields WP_List_Table
* 
* @package WordPress
* @subpackage engageaholic
* @author n7 Studios
* @version 1.0
* @copyright n7 Studios 
*/ 
class CRFP_List_Table extends WP_List_Table {
	/**
	* Constructor, we override the parent to pass our own arguments
	*
	* We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
	*/
	function __construct() {
		parent::__construct( array(
			'singular'=> 'field', // Singular label
			'plural' => 'fields', // plural label, also this well be one of the table css class
			'ajax'	=> false // We won't support Ajax for this table
		));
	}
	 
	/**
 	* Define the columns that are going to be used in the table
 	*
 	* @return array $columns, the array of columns to use with the table
 	*/
	function get_columns() {
		return array(
			'cb' => '<input type="checkbox" class="toggle" />',
			'col_field_label' => __('Field Label', 'comment-rating-field-pro'),
			'col_field_targeting' => __('Targeting', 'comment-rating-field-pro'),
			'col_field_required' => __('Required', 'comment-rating-field-pro'),
		);
	}
	
	/**
 	* Decide which columns to activate the sorting functionality on
 	*
 	* @return array $sortable, the array of columns that can be sorted by the user
 	*/
	public function get_sortable_columns() {
		return $sortable = array(
			'col_field_label' => array('label', true)
		);
	}
	
	/**
	* Overrides the list of bulk actions in the select dropdowns above and below the table
	*/
	public function get_bulk_actions() {
		return array(
			'delete' => __('Delete', 'comment-rating-field-pro'),
		);
	}
	
	/**
 	* Prepare the table with different parameters, pagination, columns and table elements
 	*/
	function prepare_items() {
		global $crfpFields, $_wp_column_headers;
		$screen = get_current_screen();
		
		// Get params
		$search = (isset($_REQUEST['s']) ? $_REQUEST['s'] : '');
		$orderBy = (isset($_GET['orderby']) ? $_GET['orderby'] : '');
  		$order = (isset($_GET['order']) ? $_GET['order'] : '');
		
		// Adjust as necessary to display the required number of rows per screen
		$rowsPerPage = 10;

		// Get all records
		$total = $crfpFields->GetTotal($search);
		
		// Define pagination if required
		$paged = ((isset($_GET['paged']) AND !empty($_GET['paged'])) ? $_GET['paged'] : '');
        if(empty($paged) OR !is_numeric($paged) OR $paged<=0 ) $paged = 1;
        $totalPages = ceil($total / $rowsPerPage);
		$this->set_pagination_args( array(
			'total_items' => $total,
			'total_pages' => $totalPages,
			'per_page' => $rowsPerPage,
		));
		
		// Set table columns and rows
		$columns = $this->get_columns();
  		$hidden  = array();
  		$sortable = $this->get_sortable_columns();
  		$this->_column_headers = array( $columns, $hidden, $sortable );
  		$this->items = $crfpFields->GetAll($orderBy, $order, $paged, $rowsPerPage, $search);
	}

	/**
	* Display the rows of records in the table
	* @return string, echo the markup of the rows
	*/
	function display_rows() {
		global $crfpFields, $crfpCore;

		// Get rows and columns
		$records = $this->items;
		list($columns, $hidden) = $this->get_column_info();
		
		// Go through each row
		if (!empty($records)) {
			foreach ($records as $key=>$rec) {
				echo ('<tr id="record_'.$rec->fieldID.'"'.(($key % 2 == 0) ? ' class="alternate"' : '').'>');
				
				// Go through each column
				foreach ($columns as $columnName=>$columnDisplayName) {
					switch ($columnName) {
						case 'cb': 
							echo ('<th scope="row" class="check-column"><input type="checkbox" name="fieldIDs['.stripslashes($rec->fieldID).']" value="'.stripslashes($rec->fieldID).'" /></th>'); 
							break;
						case 'col_field_label': 
							echo ('	<td class="'.$columnName.' column-'.$columnName.'">
										<strong><a href="admin.php?page='.$crfpCore->plugin->name.'-rating-fields&cmd=edit&pKey='.stripslashes($rec->fieldID).'" title="'.__('Edit this item', 'comment-rating-field-pro').'">'.stripslashes($rec->label).'</a></strong>
										<div class="row-actions">
											<span class="edit"><a href="admin.php?page='.$crfpCore->plugin->name.'-rating-fields&cmd=edit&pKey='.stripslashes($rec->fieldID).'" title="'.__('Edit this item', 'comment-rating-field-pro').'">'.__('Edit', 'comment-rating-field-pro').'</a> | </span>
											<span class="trash"><a href="admin.php?page='.$crfpCore->plugin->name.'-rating-fields&cmd=delete&pKey='.stripslashes($rec->fieldID).'" title="'.__('Delete this item', 'comment-rating-field-pro').'" class="delete">'.__('Delete', 'comment-rating-field-pro').'</a></span>
										</div>
									</td>'); 
							break;
						case 'col_field_targeting':
							$targeting = '';
							echo ('<td class="'.$columnName.' column-'.$columnName.'">');
							
							if (isset($rec->targeting->type) AND is_array($rec->targeting->type) AND count($rec->targeting->type) > 0) {
								foreach ($rec->targeting->type as $t=>$type) $targeting .= $type.'<br />';
							}
							if (isset($rec->targeting->tax) AND is_array($rec->targeting->tax) AND count($rec->targeting->tax) > 0) {
								foreach ($rec->targeting->tax as $tax=>$types) {
									foreach ($types as $index=>$type) $targeting .= $tax.': '.$type['name'].'<br />';
								}
							}
							echo rtrim($targeting, '<br />');
							echo ('</td>'); 
							break;
						case 'col_field_required': 
							echo ('<td class="'.$columnName.' column-'.$columnName.'">' . ($rec->required == 1 ? '&check;' : '&cross;') . '</td>'); 
							break;
					}
				}	
				
				echo ('</tr>');
			}
		}
	}
}
?>