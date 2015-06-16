<?php
	
	/**
	* Cron Controller
	* Author: Tim Keaton and Thomas Melvin
	* Date: 1 July 2013
	* Notes:
	* This controller handles the daily digest email.
	*
	*/
	
	
	
	/* NOTE: We will need to add some kind of passphrase to protect this page */
	
	
	class Cron extends MY_Controller {
		
		////////////////
		// Not needed?
		////////////////		
		/*******************************
		**	Default Constructor
		********************************/
		/*
		public function __construct() {
			parent::__construct();
		}
		*/
		

	
		////////////////
		// Runs query to get user-specific Academy posts for daily digest email.
		////////////////
		public function daily_digest_email( $user_id = 1 ){						// defaulting to admin, should change this later
//		public function daily_digest_email(){

			//echo '<head><link href="http://localhost/devmybiz/assets/css/email.css" rel="stylesheet" type="text/css"></head>';
			echo '<head><link href="/assets/css/email.css" rel="stylesheet" type="text/css"></head>';

			$this->load->helper('css_helper');
			$this->load->model('academy_model');

		// get user's preferred categories
			$this->db->where('user_id',$user_id);
			$this->db->where('academy_entry_category_id !=', 0);
			$this->db->order_by('dash_location', 'asc');
			$obj_query = $this->db->get('users_dashboard');    	// name of new table in db linking user id to category id for favorites/subscriptions
			
			$num_user_categories = $obj_query->num_rows();						// gets number of favorited user categories
			$matrix_user_categories = $obj_query->result_array();

			$user_info = $this->db->get('users');								// gets user info from db
			$user_info = $user_info->result_array();
			$user_first_name = $user_info[0]['first_name'];						// grabs user's first name

			$arr_user_categories = array();										// create array of user category id's
			foreach (array_keys($matrix_user_categories) as $i) $arr_user_categories[] = $matrix_user_categories[$i]['academy_entry_category_id'];

			$obj_entry_collection = $this->academy_model->get_entries();	
			
			if ($num_user_categories>0) $obj_entry_collection = $obj_entry_collection->get_categorized($arr_user_categories);		// narrows down by category if user has favorites

			$arr_entries = $obj_entry_collection->get('arr_collection');

			echo '<div id="main-body">';
			
			echo '<h1><img id="logo" src="/assets/img/email-logo.jpg" /><span id="title">Daily Digest</span> &nbsp; <span id="date">( '.date("F j, Y", time() - 60 * 60 * 24).' )</span></h1>';

			echo '<div id="recent-posts" '.($num_user_categories>0 ? 'style="width:60%"' : '').'>Hi '.$user_first_name.',<br/><br/>Here are some recent Academy posts that might interest you.</p><p><ul>';

			foreach( array_slice($arr_entries,0,5) as $obj_entry ) {														// gets five most recent articles
				echo '<li>';
				echo '<a href="/academy/entry/'.$obj_entry->get('id').'">'.$obj_entry->get('title').'</a><br/>';
				echo $obj_entry->get('description');
				//echo '&nbsp; (<a href="/academy/entry/'.$obj_entry->get('id').'">more</a>)';
				echo '</li>';
			}
			
			echo '</ul></p></div>';

			
			
		// for each of preferred categories, get entries
			if ($num_user_categories>0){
				echo '<div id="favorite-cats">Articles in your favorite categories:<br/>';
				$arr_categories = $this->academy_model->get_categories();

				foreach( $arr_user_categories as $category ){																// for each favorited category...
					
					echo '<br/><a href="/academy/category/'.$category.'" style="text-decoration:none;"><div class="cat-'.category_color($category).'">'.$arr_categories[$category]['name'].'</div></a>';				// get category name

					$obj_entry_collection_cat = $obj_entry_collection->get_categorized($arr_categories[$category]);			// get entries for category
					$arr_entries_cat = $obj_entry_collection_cat->get('arr_collection');
					
					echo '<ul>';

					foreach( array_slice(array_keys($arr_entries_cat),0,3) as $i ){											// for three most recent entries in category,
						echo '<li><a href="/academy/entry/'.$arr_entries_cat[$i]->get('id').'">'.$arr_entries_cat[$i]->get('title').'</a></li>';		// spit out title/link
					}
					echo '</ul>';
				}
				echo '</div>';
			}			
			
			echo '<div id="lower"><br/>';
			if ($num_user_categories == 0 )	echo 'You have not selected any preferred categories. Get more value out of this daily digest by customizing what categories interest YOU. ';
			echo 'Change your preferred categories anytime at <a href="#">this place</a>. Other disclaimery things.</p></div>';
			
			echo '</div>';		// end main-body
			
			echo '<div id="footer">
				  MyBizPerks &nbsp; 1313 Mockingbird Ln, Here, ST, 12345<br/>
				  You received this email because you are subscribed to Blah Blah Blah from MyBizPerks.<br/>
				  Update your <a href="##">email preferences</a> to choose what types of emails you receive.
				  </div>';
			
		}

	}


