<?php

	/**
	* PayProMedia Twitter Widget
	* Author: Enrique Marrufo
	* Date: 26 August 2013
	*
	* Notes: This widget uses the Twitter API. 
	*
	**/

	// Get tweets
	$arr_tweets = $this->widgets_model->get_tweets($widget_items);
?>
<div class="portlet dragme" column="<?php echo $widget_location['column'] ?>" row="<?php echo $widget_location['row'] ?>" widget_type="4" db_id="<?php echo $db_id ?>">
	<div class="portlet-title "style="padding:0px; margin-bottom: -5px; ">
		<div class="top-news">
			<a class="btn blue" style="margin-bottom: -10px;">	<span>Twitter</span>
				<em>Social Media
				</em><i class="icon-twitter top-news-icon"></i></a>	
		</div>

		<div class="tools" style="margin-top: -1.8em;">
			<a href="javascript:;" class="collapse btn-group"></a>
		</div>
	</div>
	<div class="portlet-body">			
		<div> 
			<div style="overflow: scroll; height: 260px;">
				<?php
					// Display tweet(s)
					foreach ($arr_tweets as $cur_tweet) :
						// Time of tweet
						$tweet_time = strtotime($cur_tweet->created_at);
						// Difference - in minutes - in current time and time of tweet
						$tweet_diff = round((time()-$tweet_time)/60);
						// Time increments
						$show_time = ($tweet_diff < 59) ? $tweet_diff.' minutes ago' : ((($tweet_diff/60) < 24) ? round($tweet_diff/60).' hours ago' : date('M j', $tweet_time)); 
				?>
					<div class="news-blocks">
						<?php echo '<p><em>'.$cur_tweet->user->screen_name.'</em><br />'.linkify($cur_tweet->text).'<br /><em>'.$show_time.'</em></p>'; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>