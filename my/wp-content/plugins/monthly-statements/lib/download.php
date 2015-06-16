<?php
// Include CDN libraries
require_once('class.rs_cdn.php');
require_once('functions.php');
require_once('php-opencloud-1.5.10/lib/php-opencloud.php');

// Get month and merchant id
$the_month          =   $_GET['the_month'];
$the_year           =   $_GET['the_year'];
$the_merchant_id    =   $_GET['the_merchant_id'];

// File to download
$file_to_download = $the_year.$the_month.'01-'.$the_merchant_id.'-statement.pdf';

// Create new CDN instance
$the_cdn = new RS_CDN();

// Get object
try {
    $object = $the_cdn->container_object()->DataObject($file_to_download);
} catch (Exception $exc) {
    echo 'Sorry, there was an error processing your request. The file does not exist.';
    exit;
}

// Echo object
try {
    // Read the file into a string
    $the_file = $object->SaveToString();

    // Echo file contents and force download
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.$file_to_download);
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($the_file));
    echo $the_file;
    exit;
} catch (Exception $exc) {
    echo 'Sorry, there was an error processing your request. Unable to read file contents.';
    exit;
}
?>