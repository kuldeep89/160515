<?php
	
	require_once	dirname(__DIR__).'/lib/database.php';
	require_once	dirname(__DIR__).'/lib/transactional-mailer.php';
	
	$obj_mailer	= new Transactional_mailer();
	$obj_mailer->send_update(date('Y-m-d', strtotime("-8 days")), 'weekly');
	
?>