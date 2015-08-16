<?php
	include '../include/include.php';
	header("Content-Type: application/json");
	
	$CGBStats->disableCaching();
	
	$username = NULL;
	if(isset($_GET['username'])){
		$username = $_GET['username'];
	} else {
		die("[]");
	}
	
	// stupid mysql requires the concat thing
	// using php to add the % doesn't work
	$query = "SELECT `username`, `userid` FROM `cgbstats_user` WHERE `username` LIKE CONCAT('%', ?, '%')";
	$res = $CGBStats->database->query($query, array($username));
	echo json_encode($res);
?>