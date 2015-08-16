<?php
	include '../include/include.php';
	header("Content-Type: application/json");
	
	$types = array("gold", "elix", "delix", "trophy");
	$type = "gold";
	if(isset($_GET['type']) && in_array($type, $types)){
		$type = $_GET['type'];
	}
	
	$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` ORDER BY `date` DESC LIMIT 1", array());
	if(sizeof($checkModified) > 0) $CGBStats->enableCachingWithData(intval($checkModified[0]['ts']), $type);
	else $CGBStats->enableCachingWithData(filemtime(__FILE__), $type); // shouldn't happen
	
	$query = "SELECT A.`userid`, `username`, `gold`, `elix`, `delix`, `trophy` FROM (SELECT userid, MAX(gold) AS gold, MAX(elix) AS elix, MAX(delix) AS delix, MAX(trophy) AS trophy FROM cgbstats_stats WHERE `date` > DATE_SUB(NOW(), INTERVAL 2 DAY) GROUP BY userid ORDER BY `$type` DESC LIMIT 20) AS A INNER JOIN cgbstats_user AS B ON A.userid = B.userid";
	
	$res = $CGBStats->database->query($query, array());
	
	echo json_encode($res);
?>