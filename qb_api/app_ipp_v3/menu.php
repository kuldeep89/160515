<?php
//25may2015: Chetu: use this when you need to require intuit menu
require_once dirname(__FILE__) . '/config.php';

// Display the menu
die($IntuitAnywhere->widgetMenu($the_username, $the_tenant));