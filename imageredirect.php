<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
	
	$data = $CGBStats->urlshortener->unshorten($_SERVER['REQUEST_URI']);
	$userid = $data['userid'];
	$days = $data['days'];
	$type = $data['type'];
	
	header("Location: /api/render.php?signature=true&user=$userid&type=$type&days=$days");
?>
