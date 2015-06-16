<?php

	include '../../../wp-config.php';



	/*
$wp_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_dev_my");
	
	$term_count_result = $wp_db->query("SELECT * FROM wp_term_taxonomy");
	
	while ($cat = $term_count_result->fetch_object()) {
		$term_id = $cat->term_taxonomy_id;
		
		$uncategorized = $wp_db->query("SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id=1");
		
		while ($cat = $uncategorized->fetch_object()) {
			$num_results = $wp_db->query("SELECT object_id FROM wp_term_relationships WHERE object_id=$cat->object_id");
			echo $cat->object_id.' ('.$num_results->num_rows.')<br/>';
			$i++;
		}
	}
	echo 'TOTAL: '.$i;
*/


	/*
$ci_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_live_2vWt7V0nrPoS");
	$wp_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_dev_my");
	
	$term_count_result = $wp_db->query("SELECT * FROM wp_term_taxonomy");
	
	while ($cat = $term_count_result->fetch_object()) {
		$term_id = $cat->term_taxonomy_id;
		
		$num_results = $wp_db->query("SELECT object_id FROM wp_term_relationships WHERE term_taxonomy_id=$term_id");
		
		echo $term_id.' has '.$num_results->num_rows.' results.<br/>';
		
		// $num_results = $wp_db->query("UPDATE wp_term_taxonomy SET count=".$num_results->num_rows." WHERE term_taxonomy_id=$term_id");
	}
*/
	
	
	
	
	/** UPDATE TAGS **/
	/*
$ci_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_live_2vWt7V0nrPoS");
	$wp_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_dev_my");
	
	$categories_result = $ci_db->query("SELECT * FROM academy_entry_tags");
	
	while ($cat = $categories_result->fetch_object()) {
		$cat_id = $cat->academy_entry_tag_id;
		if (strtolower($cat->name) == "google+") {
			$cat->name = 'google plus';
		}
		$slug = preg_replace("/[^a-z ]/", "", strtolower($cat->name));
		$slug = preg_replace('/\s+/', '-', $slug);
		
		$categories[$cat_id] = $slug;
	}
	
	foreach ($categories as $cat_id => $cat_slug) {
		$categories_assoc_result = $ci_db->query("SELECT * FROM academy_entry_tags_assoc WHERE academy_entry_tag_id=".$cat_id." ORDER BY academy_entry_id");
		echo '<strong>'.$cat_slug.' ('.$categories_assoc_result->num_rows.')</strong><br/>';
		while ($cat_assoc = $categories_assoc_result->fetch_object()) {
			$wp_cat_result = $wp_db->query("SELECT wpt.term_id,wtt.term_taxonomy_id FROM wp_terms AS wpt INNER JOIN wp_term_taxonomy AS wtt WHERE wpt.slug='$cat_slug' AND wpt.term_id=wtt.term_id AND wtt.taxonomy='post_tag'");

			// echo '<pre>'.print_r($wp_cat_result->fetch_object(), true).'</pre>';
			$term_to_taxonomy_result = $wp_cat_result->fetch_object();

			if (isset($term_to_taxonomy_result->term_taxonomy_id)) {
				// $my_resp = $wp_db->query("INSERT INTO wp_term_relationships (object_id,term_taxonomy_id) VALUES ('$cat_assoc->academy_entry_id','$term_to_taxonomy_result->term_taxonomy_id')");
				echo "INSERT  INTO wp_term_relationships (object_id,term_taxonomy_id) VALUES ('$cat_assoc->academy_entry_id','$term_to_taxonomy_result->term_taxonomy_id')";
				echo '<br/>';
				$i++;
			}
		}
		echo '<br/><br/>';
	}
	
	echo 'TOTAL ASSOCIATED ARTICLES: '.$i;
*/


	/** UPDATE CATEGORIES **/
	/*
$ci_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_live_2vWt7V0nrPoS");
	$wp_db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, "saltshac_dev_my");
	
	$categories_result = $ci_db->query("SELECT * FROM academy_entry_categories");
	
	while ($cat = $categories_result->fetch_object()) {
		$cat_id = $cat->academy_entry_category_id;
		$slug = preg_replace("/[^a-z ]/", "", strtolower($cat->name));
		$slug = preg_replace('/\s+/', '-', $slug);
		
		$categories[$cat_id] = $slug;
	}
	
	foreach ($categories as $cat_id => $cat_slug) {
		$categories_assoc_result = $ci_db->query("SELECT * FROM academy_entry_categories_assoc WHERE academy_entry_category_id=".$cat_id." ORDER BY academy_entry_id");
		echo '<strong>'.$cat_slug.' ('.$categories_assoc_result->num_rows.')</strong><br/>';
		while ($cat_assoc = $categories_assoc_result->fetch_object()) {
			$wp_cat_result = $wp_db->query("SELECT wpt.term_id,wtt.term_taxonomy_id FROM wp_terms AS wpt INNER JOIN wp_term_taxonomy AS wtt WHERE wpt.slug='$cat_slug' AND wpt.term_id=wtt.term_id AND wtt.taxonomy='category'");

			$term_to_taxonomy_result = $wp_cat_result->fetch_object();

			if (isset($term_to_taxonomy_result->term_taxonomy_id)) {
				$my_resp = $wp_db->query("INSERT INTO wp_term_relationships (object_id,term_taxonomy_id) VALUES ('$cat_assoc->academy_entry_id','$term_to_taxonomy_result->term_taxonomy_id')");
				echo "INSERT INTO wp_term_relationships (object_id,term_taxonomy_id) VALUES ('$cat_assoc->academy_entry_id','$term_to_taxonomy_result->term_taxonomy_id')";
				echo '<br/>';
				$i++;
			}
		}
		echo '<br/><br/>';
	}
	
	echo 'TOTAL ASSOCIATED ARTICLES: '.$i;
*/

	exit;
?>