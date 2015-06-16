<?php
    // Require db info file, include globals
	require_once(dirname(dirname(__DIR__)).'/lib/database.php');

    // Add merchant IDs
    $users_mids = $mysqli->query("SELECT meta_value,user_id FROM wp_usermeta WHERE meta_key='ppttd_merchant_info'");
    foreach ($users_mids as $cur_user) {
        $merchant_data = unserialize($cur_user['meta_value']);
        // echo '<br/><br/><strong>USER ID: '.$cur_user['user_id'].'</strong><br/>';
        if (is_array($merchant_data['ppttd_merchant_id'])) {
            foreach ($merchant_data['ppttd_merchant_id'] as $merchant_id => $merchant_name) {
                if (trim($merchant_id) !== '') {
                    if (!$mysqli->query("INSERT INTO wp_merchant_user_relationships (user_id,merchant_id,merchant_name) VALUES (".$cur_user['user_id'].",'".str_pad($merchant_id, 16, '0', STR_PAD_LEFT)."','".addslashes($merchant_name)."')")) {
                    echo 'Failed adding merchant ID "'.$merchant_id.'" for user '.$cur_user['user_id'].'<br/>';
                }
            }
                    
            }
        } else {
            // echo '- '.$merchant_data['ppttd_merchant_id'];
            if (trim($merchant_data['ppttd_merchant_id']) !== '') {
                    if (!$mysqli->query("INSERT INTO wp_merchant_user_relationships (user_id,merchant_id) VALUES (".$cur_user['user_id'].",'".str_pad($merchant_data['ppttd_merchant_id'], 16, '0', STR_PAD_LEFT)."')")) {
                    echo 'Failed adding merchant ID "'.str_pad($merchant_data['ppttd_merchant_id'], 16, '0', STR_PAD_LEFT).'" for user '.$cur_user['user_id'].'<br/>';
                }
            }
        }
    }


    // Update price overrides
    $users_po = $mysqli->query("SELECT meta_value,user_id FROM wp_usermeta WHERE meta_key='ppttd_price_override'");
    foreach ($users_po as $cur_user) {
        $po_data = unserialize($cur_user['meta_value']);
        $merchant_data = unserialize($cur_user['meta_value']);
        foreach ($merchant_data as $merchant_id => $override_price) {
            if ($override_price !== '') {
                if (!$mysqli->query("UPDATE wp_merchant_user_relationships SET price_override='".$override_price."' WHERE user_id=".$cur_user['user_id']." AND merchant_id=".str_pad($merchant_id, 16, '0', STR_PAD_LEFT))) {
                    echo 'Failed updating price override "'.$merchant_id.'" for user '.$cur_user['user_id'].'<br/>';
                }
            }
        }
    }
?>