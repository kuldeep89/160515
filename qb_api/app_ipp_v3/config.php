<?php

/**
 * 4june2015: Chetu: Intuit Partner Platform configuration variables
 */

// Turn on some error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Require the library code
require_once dirname(__FILE__) . '/../QuickBooks.php'; 
define('HOME_PATH','/home/payprotec/');
require_once HOME_PATH.'wp-config.php';

/*** get app_token, oauth_consumerk and oauth_consumers ***/
global $wpdb;
$app_token = "a832d336b6dd1b45e4ba490b3b41a5da6695";
$oauth_consumerk = "qyprdJsSZmHYilIwYuYaX0p4wU7bZy";
$oauth_consumers = "31lL1rR86bmF4UcXzVRbmNWjOWvEUUJkT7VY1unW";

// Your OAuth token 
$token = $app_token;

// Your OAuth consumer key and secret 
$oauth_consumer_key = $oauth_consumerk;
$oauth_consumer_secret = $oauth_consumers;

// If you're using DEVELOPMENT TOKENS, you MUST USE SANDBOX MODE!!!  If you're in PRODUCTION, then DO NOT use sandbox.
$sandbox = true;     // When you're using development tokens
//$sandbox = false;    // When you're using production tokens

// This is the URL of your OAuth auth handler page


$quickbooks_oauth_url = includes_url('qb_api/app_ipp_v3/oauth.php');

// This is the URL to forward the user to after they have connected to IPP/IDS via OAuth
$quickbooks_success_url = includes_url('qb_api/app_ipp_v3/success.php');

// This is the menu URL script 
$quickbooks_menu_url = includes_url('qb_api/app_ipp_v3/menu.php');

// This is a database connection string that will be used to store the OAuth credentials 
// $dsn = 'pgsql://username:password@hostname/database';
// $dsn = 'mysql://username:password@hostname/database';
//$dsn = 'mysqli://payprotec:Chetu@123@localhost/payprotec';
		
$dsn = 'mysqli://payprotec:Chetu@123@localhost/payprotec';		

// You should set this to an encryption key specific to your app
$encryption_key = 'bcde1234';

// Do not change this unless you really know what you're doing!!!  99% of apps will not require a change to this.
$the_username = 'root';

// The tenant that user is accessing within your own app
$the_tenant = 12345;

// Initialize the database tables for storing OAuth information
if (!QuickBooks_Utilities::initialized($dsn))
{
	// Initialize creates the neccessary database schema for queueing up requests and logging
	QuickBooks_Utilities::initialize($dsn);
}

// Instantiate Intuit auth handler 
$IntuitAnywhere = new QuickBooks_IPP_IntuitAnywhere($dsn, $encryption_key, $oauth_consumer_key, $oauth_consumer_secret, $quickbooks_oauth_url, $quickbooks_success_url);

// Are they connected to QuickBooks right now? 
if ($IntuitAnywhere->check($the_username, $the_tenant) and 
	$IntuitAnywhere->test($the_username, $the_tenant))
{
	// Yes, they are 
	$quickbooks_is_connected = true;
	
	// Set up the IPP instance
	$IPP = new QuickBooks_IPP($dsn);
	
	// Get our OAuth credentials from the database
	$creds = $IntuitAnywhere->load($the_username, $the_tenant);
	// Tell the framework to load some data from the OAuth store
	$IPP->authMode(
		QuickBooks_IPP::AUTHMODE_OAUTH, 
		$the_username, 
		$creds);

	if ($sandbox)
	{
		// Turn on sandbox mode/URLs 
		$IPP->sandbox(true);
	}

	// This is our current realm
	$realm = $creds['qb_realm'];
	$oauth_token = $creds['oauth_request_token'];

	// Load the OAuth information from the database
	$Context = $IPP->context();
	
	// Get some company info
	$CompanyInfoService = new QuickBooks_IPP_Service_CompanyInfo();
	$quickbooks_CompanyInfo = $CompanyInfoService->get($Context, $realm);
	
}
else
{
	// No, they are not
	$quickbooks_is_connected = false;
}

?>