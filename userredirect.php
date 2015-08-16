<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
	
	var_dump($_SERVER['REQUEST_URI']);
	$userid = intval(substr($_SERVER['REQUEST_URI'], 1, 4));
	
	header("Location: /userstats?userid=$userid");
?>
