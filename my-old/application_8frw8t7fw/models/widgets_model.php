<?php
	
	class Widgets_model extends CI_Model {

		/*******************************
		**	Get category
		**
		**	Description:
		**	This method returns ALL category information for an Academy widget
		**
		**	@param:		void
		**	@return:	array
		**
		**/
		public function get_category( $int_category_id ) {

			$this->db->where('academy_entry_category_id', $int_category_id);
			$db_data = $this->db->get('academy_entry_categories')->result_array();
			return array('category'=>$db_data[0]);

		}

		/*******************************
		**	Get Quotes
		**
		**	Description:
		**	This method will retrieve stock quote information
		**  from google based on the passed information.
		**
		**	@param:		$arr_stock_ids
		**	@return:	$arr_quotes
		**
		**  Author: Thomas Melvin
		**
		**/
		public function get_quotes( $arr_stock_codes ) {
			// Get Stock Quotes
			$query_url = 'http://finance.google.com/finance/info?client=ig&q=';

			//Build Query URL
			foreach ($arr_stock_codes as $key=>$value) {
				$query_url .= $value.',';
			}

			//Pull in quotes.
			$json_quotes = @file_get_contents($query_url);

			//Remove leading comment slashes.
			$json_quotes = str_replace('//', '', $json_quotes);

			//Decode JSON
			$arr_quotes = json_decode($json_quotes, true);

			// Return stocks or false if error
			if ($json_quotes) :
				return $arr_quotes;
			else:
				return false;
			endif;
		}

		/**	Get Tweets
		**
		**	Description:
		**	This method will retrieve Twitter information
		**  from Twitter based on the passed information.
		**
		**	@param:		$arr_twitter_profile
		**	@return:	$arr_tweets
		**
		**  Author: Enrique Marrufo
		**
		**/
		public function get_tweets($arr_follow_data) {
			// Include twitter OAuth lib
			require_once('classes/standard/twitterOAuth.php');

			// New twitter OAuth
			$twitter = new TwitterOAuth('YLexXHa7teH5ZDAB3PW7Vg', 'asLvhkQjwiGgpwL0oIVl12R9svDjXgG0fpsiebD9xk', '1561422698-UuP2X3b5kW4uRihy50kICf2lQuvtKfs1rHxBuq8', 'vpGE3dnj5PyMaLF0SxVjaIoSKD1q7B0VmQOMneH3sc');

			// Set number of tweets to get if not set
			$num_tweets = isset($arr_follow_data['num_tweets']) ? $arr_follow_data['num_tweets'] : '5';

			// Get tweets or messages with hashtag
			if (preg_match('/user/i', $arr_follow_data['follow_type'])) :
				$arr_tweets = $twitter->get('https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name='.$arr_follow_data['who_to_follow'].'&count='.$num_tweets);
			else :
				$arr_tweets = $twitter->get('https://api.twitter.com/1.1/search/tweets.json?q='.urlencode($arr_follow_data['who_to_follow']).'&count='.$num_tweets)->statuses;
			endif;

			// Return array of tweets
			return $arr_tweets;
		}
		
		/** Get widget name from ID
		**
		**	Description:
		**	This method will retrieve widget information
		**  and pass it back to the requesting entity.
		**
		**	@param:		$int_id
		**	@return:	$arr_widget_data
		**
		**  Author: Bobbie Stump
		**
		**/
		public function get_widget_from_id($int_id) {
			$this->db->where('widget_type', $int_id);
			$db_data = $this->db->get('widgets')->result_array();
			return $db_data[0];
		}
	}
	
?>