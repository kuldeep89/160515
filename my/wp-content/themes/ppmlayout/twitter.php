<?php
date_default_timezone_set('America/Fort_Wayne');

// Define twitter api information
define('TWITTER_API_VERSION','1.1');
define('TWITTER_API_CONSUMER_KEY',' 44cCcViKCtzA6GXMw1ulVw');
define('TWITTER_API_CONSUMER_SECRET',' y8kRmr79VmmN3umYN1cmtxcHEkpPO3zeErLie2kOyc');
define('TWITTER_API_ACCESS_TOKEN',' 2194455631-qSh53hIdmyUzHjLE14hrRa9u9hLNvfBdMnKk7uT');
define('TWITTER_API_ACCESS_TOKEN_SECRET','A7qmq6I2O53HI0nnCyyXCzUJAnVPq232F3Q1Hmr5d9bxl');
define('NUM_TWEETS','3');

// Define OAuth information
define('OAUTH_VERSION','1.0');

// Setup twitter OAuth hash
$oauth_hash = 'count='.NUM_TWEETS.'&oauth_consumer_key='.TWITTER_API_CONSUMER_KEY.'&oauth_nonce=' . time() . '&oauth_signature_method=HMAC-SHA1&oauth_timestamp=' . time() . '&oauth_token='.TWITTER_API_ACCESS_TOKEN.'&oauth_version='.OAUTH_VERSION;

// Setup twitter API call
$base = 'GET&'.rawurlencode('https://api.twitter.com/'.TWITTER_API_VERSION.'/statuses/user_timeline.json').'&'.rawurlencode($oauth_hash);
$key = rawurlencode(TWITTER_API_CONSUMER_SECRET).'&'.rawurlencode(TWITTER_API_ACCESS_TOKEN_SECRET);

// Define twitter app signature
$signature = base64_encode(hash_hmac('sha1', $base, $key, true));
$signature = rawurlencode($signature);

// Setup twitter OAuth header
$oauth_header = 'oauth_consumer_key="'.TWITTER_API_CONSUMER_KEY.'", oauth_nonce="' . time() . '", oauth_signature="' . $signature . '", oauth_signature_method="HMAC-SHA1", oauth_timestamp="' . time() . '", oauth_token="'.TWITTER_API_ACCESS_TOKEN.'", oauth_version="'.OAUTH_VERSION.'"';
$curl_header = array("Authorization: Oauth {$oauth_header}", 'Expect:');

// Send API request to twitter
$curl_request = curl_init();
curl_setopt($curl_request, CURLOPT_HTTPHEADER, $curl_header);
curl_setopt($curl_request, CURLOPT_HEADER, false);
curl_setopt($curl_request, CURLOPT_URL, 'https://api.twitter.com/'.TWITTER_API_VERSION.'/statuses/user_timeline.json?count='.NUM_TWEETS);
curl_setopt($curl_request, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl_request, CURLOPT_SSL_VERIFYPEER, false);
$json = curl_exec($curl_request);
curl_close($curl_request);

// Parse data received from request
$tweets = json_decode($json, true);

// Current time and day of year
$cur_time = time();

// Display tweet(s)
foreach ($tweets as $cur_tweet) {
	// Time of tweet
	$tweet_time = strtotime($cur_tweet["created_at"]);

	// Difference - in minutes - in current time and time of tweet
	$tweet_diff = round(($cur_time-$tweet_time)/60);

	// Time increments
	$show_time = ($tweet_diff < 59) ? $tweet_diff.' minutes ago' : ((($tweet_diff/60) < 24) ? round($tweet_diff/60).' hours ago' : date('M j', $tweet_time));

	// Display tweet and time
	echo '<div class="news-blocks">';
	
	echo '<div class="tweet">'.linkify($cur_tweet["text"]).'<div class="time">'.$show_time.'</div></div></div>';
}

// Linkify tweets
function linkify($text) {
    $text= preg_replace("/(^|[\n ])([\w]*?)((ht|f)tp(s)?:\/\/[\w]+[^ \,\"\n\r\t<]*)/is", "$1$2<a href=\"$3\" target=\"_blank\">$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([\w]*?)((www|ftp)\.[^ \,\"\t\n\r<]*)/is", "$1$2<a href=\"http://$3\" target=\"_blank\">$3</a>", $text);
    $text= preg_replace("/(^|[\n ])([a-z0-9&\-_\.]+?)@([\w\-]+\.([\w\-\.]+)+)/i", "$1<a href=\"mailto:$2@$3\">$2@$3</a>", $text);
    return($text);
}
?>



