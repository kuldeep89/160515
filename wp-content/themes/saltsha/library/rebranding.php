<?php

// Change the logo on the admin login page.
add_action('login_head', 'ppm_custom_login_logo');
function ppm_custom_login_logo() {
    echo '<style type="text/css">
    h1 a { background-image:url(https://www.paypromedia.com/paypromedia-logo.png) !important; background-size: 331px 75px !important;height: 75px !important; padding-bottom: 1em !important; width: 320px !important; }
    </style>';
}

// Change the URL of the WordPress login logo.
add_filter('login_headerurl', 'ppm_url_login_logo');
function ppm_url_login_logo(){
    return get_bloginfo( 'wpurl' );
}

// Change the hover text on login page.
add_filter( 'login_headertitle', 'ppm_login_logo_url_title' );
function ppm_login_logo_url_title() {
    return 'PayProMedia';
}

// Login Screen: Don't inform user which piece of credential was incorrect.
add_filter ( 'login_errors', 'ppm_failed_login' );
function ppm_failed_login () {
    return 'The login information you have entered is incorrect. Please try again.';
}

// Add a favicon for your admin.
add_action('admin_head', 'admin_favicon');
add_action('login_head', 'admin_favicon');
function admin_favicon() { 
	echo '<link rel="shortcut icon" type="image/png" href="https://www.paypromedia.com/favicon.png" />
	<link rel="apple-touch-icon" href="https://www.paypromedia.com/apple-touch.png" />';
}

?>