<?php
/**
 * Only show this page if the user can add users.
 **/
defined('TERM_PATH') or die();
if ( !current_user_can( 'create_users' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}

?>
<div class="wrap">
	<h2>Terminals & Paper</h2>
	<a href="#" id="terminal_button" class="button ">Add Terminal</a>
	<a href="#" id="paper_button" class="button ">Add Paper</a>
	<br />
	<div id="ajax_form_loading">Loading... Hold your horses...</div>	
	
	<form id="terminal_form" class="terminal_forms">
		<table>
			<tr>
				<th colspan="2" align="left">Add Terminal</th>
			</tr>
			<tr>
				<td><label for="terminal_name" required>Terminal Name</label></td>
				<td><input type="text" name="terminal_name" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="button button-primary" /></td>
			</tr>
		</table>
	</form>
	
	<form id="paper_form" class="terminal_forms">
		<table>
			<tr>
				<th colspan="2" align="left">Add Paper Type</th>
			</tr>
			<tr>
				<td><label for="paper_type" required>Paper Type</label></td>
				<td><input type="text" name="paper_type" /></td>
			</tr>
			<tr>
				<td><label for="paper_size" required>Paper Size</label></td>
				<td><input type="text" name="paper_size" /></td>
			</tr>
			<tr>
				<td><label for="transactions" required>Transactions Per Roll</label></td>
				<td><input type="text" name="transactions" /></td>
			</tr>
			<tr>
				<td colspan="2"><input type="submit" class="button button-primary" /></td>
			</tr>
		</table>
	</form>
	
	<br />
	<br />
	<div id="terminal_table">
		<?php display_terminals(); ?>
	</div>
	<br />
	<div id="paper_table">
		<?php display_paper(); ?>
	</div>
</div>