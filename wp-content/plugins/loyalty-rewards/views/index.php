<?php
    global $wp_query;

    // Show filters
    rsp_filters();

    // Display product(s)
    if (isset($data->products) && count($data->products) > 0) :
        foreach($data->products as $cur_data) :
            // Build product link
            $product_link = site_url('/loyalty-rewards/product/'.$cur_data->catalog_item_id.'/');
            $product_link = (isset($wp_query->query_vars['rsp_category'])) ? trim($product_link, '/').'/category/'.$wp_query->query_vars['rsp_category'] : $product_link;
            $product_link = (isset($wp_query->query_vars['rsp_page'])) ? trim($product_link, '/').'/page/'.$wp_query->query_vars['rsp_page'] : $product_link;

            // Display product link
            echo '<div class="product-container"><div class="product-shopping-tools"><a href="'.$product_link.'" class="view-cart-button">View</a><a rsp-pid="'.$cur_data->catalog_item_id.'" class="view-cart-button rsp_add_to_cart">Add to Cart</a></div><a href="'.$product_link.'"><img src="'.$cur_data->image_300.'" style="width:100%;height:auto;" title="'.$cur_data->name.'" /></a><h4 class="rsp_name">'.neat_trim($cur_data->name, 55).'</h4><span class="rsp_price">'.number_format($cur_data->points).' Points</span></div>';
        endforeach;
    else:
        // No products found
        echo '<em>No products found</em>';
    endif;
?>