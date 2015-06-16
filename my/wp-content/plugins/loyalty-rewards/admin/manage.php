<?php
	/**
	 * Start session, if not started
	 */
	/*
if( !session_id() )
		session_start();
*/


	/**
	 * Make sure CDN constants are defined
	 */
	defined('RSP_PATH') or die();


	/**
	 * Save API settings
	 */
    $rsp_options = null;
	if (isset($_POST['save_rsp_settings'])) {
		try {
			// Update RSP options
			update_option(RSP_OPTIONS, json_encode($_POST['rsp_options']));
		} catch (Exception $exc) {
			$settings_error = true;
		}
	}


    /**
     * Set RewardsStore options
     */
    $rsp_options = json_decode(get_option(RSP_OPTIONS));
    $default_catalog = (isset($rsp_options->default_catalog)) ? $rsp_options->default_catalog : '';

    /**
     * Create RewardsStore instance
     */
    $rsp_object = new RewardsStore($rsp_options);
?>
<script type="text/javascript">
	var plugin_path = "<?php echo RSP_URL ?>";
</script>
<div class="wrap rs_cdn">
	<h2 class="left">Loyalty Rewards Settings</h2>
	<div class="clear"></div>
	<hr />
	<form method="post" action="">
		<div id="error_notifications">
		<?php
			// Show error if error
			if (isset($show_errors) && count($show_errors) > 0) {
				foreach ($show_errors as $cur_error) {
		?>
			<div id="setting-error-settings_updated" class="error settings-error"> 
				<p><strong>Ruh-Roh!</strong><br /><?php echo isset($cur_error) ? $cur_error : 'Your settings are busted. Please verify and make sure you have the correct credentials.' ?></p>
			</div>
		<?php
				}
            }
		?>
		</div>
	    <h3>API Settings</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label>Token</label>
					</th>
					<td>
						<input name="rsp_options[token]" type="text" value="<?php echo (isset($rsp_options->token)) ? $rsp_options->token : ''; ?>" class="regular-text" required="required" />
					</td>
				</tr>
			</tbody>
		</table>
        <table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label>Key</label>
					</th>
					<td>
						<input name="rsp_options[key]" type="text" value="<?php echo (isset($rsp_options->key)) ? $rsp_options->key : ''; ?>" class="regular-text" required="required" />
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label>Subdomain</label>
					</th>
					<td>
						<input name="rsp_options[subdomain]" type="text" value="<?php echo (isset($rsp_options->subdomain)) ? $rsp_options->subdomain : ''; ?>" class="regular-text" required="required" />
					</td>
				</tr>
			</tbody>
		</table>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label>Environment</label>
					</th>
					<td>
    				    <select name="rsp_options[environment]" class="regular-text" required="required">
        				    <?php
            				    $environments = array('sandbox.dev' => 'Sandbox', 'dev' => 'Development', 'prod' => 'Production');
            				    foreach ($environments as $subdomain => $env_name) {
                				    if (isset($rsp_options->environment) && $subdomain === $rsp_options->environment) {
                    				    echo '<option value="'.$subdomain.'" selected>'.$env_name.'</option>';
                    				} else {
                        				echo '<option value="'.$subdomain.'">'.$env_name.'</option>';
                    				}
            				    }
            				?>
    				    </select>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
    	    // Retrieve catalogs
    	    $all_catalogs = $rsp_object->list_available_catalogs();

            // Display catalog settings if not null
            if (!isset($all_catalogs->Fault)) :
        ?>
	    <h3>Catalog Settings</h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="rsp_options[default_catalog]">Default Catalog</label>
					</th>
					<td>
    					<select name="rsp_options[default_catalog]">
        					<?php
            				    foreach ($all_catalogs->catalogs as $cur_catalog) {
                				    if ($cur_catalog->socket_id == $rsp_options->default_catalog) {
                    				    echo '<option value="'.$cur_catalog->socket_id.'" selected>'.$cur_catalog->socket_name.'</option>';
                    				} else {
                        				echo '<option value="'.$cur_catalog->socket_id.'">'.$cur_catalog->socket_name.'</option>';
                    				}
            				    }
            				?>
    					</select>
					</td>
			</tbody>
		</table>
		<?php endif; ?>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"></th>
					<td>
						<p><input class="button-primary" class="left" type="submit" name="save_rsp_settings" value="Save" />&nbsp;</p>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>