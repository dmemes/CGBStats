<?php
	include '../include/include.php';
	header("Content-Type: application/json");
	
	$userid = 0;
	if(isset($_GET['user'])){
		$userid = intval($_GET['user']);
	} else if(isset($_SESSION['userid'])) {
		$userid = intval($_SESSION['userid']);
	} else {
		die("{}");
	}
	
	$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` WHERE `userid`=? ORDER BY `date` DESC LIMIT 1", array($userid));
	if(sizeof($checkModified) > 0) $CGBStats->enableCachingWithData(intval($checkModified[0]['ts']), $userid + "");
	
	$type = "LOOT_VS_TIME";
	if(isset($_GET['type']) && array_key_exists($_GET['type'], $CGBStats->images->types)){
		$type = $_GET['type'];
	}
	
	$intervalDays = 2;
	if(isset($_GET['days'])) $intervalDays = intval($_GET['days']);
	if($intervalDays < 1 || $intervalDays > 30) $intervalDays = 2;
	
	// get averages
	$avgs = $CGBStats->database->query("SELECT AVG(`gold`) AS `avg-gold`, AVG(`elix`) AS `avg-elix`, AVG(`delix`) AS `avg-delix`, AVG(`trophy`) AS `avg-trophy`, AVG(`stars`) AS `avg-stars`, AVG(`search`) AS `avg-search` FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL ? DAY) AND `userid`=?", array($intervalDays, $userid))[0];
	// get totals
	$totals = $CGBStats->database->query("SELECT SUM(`gold`) AS `total-gold`, SUM(`elix`) AS `total-elix`, SUM(`delix`) AS `total-delix`, SUM(`trophy`) AS `total-trophy`, SUM(1) AS `total-raid`, SUM(`search`) AS `total-search` FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL ? DAY) AND `userid`=?", array($intervalDays, $userid))[0];
	// get records
	$records = $CGBStats->database->query("SELECT MAX(`gold`) AS `most-gold`, MAX(`elix`) AS `most-elix`, MAX(`delix`) AS `most-delix`, MAX(`trophy`) AS `most-trophy`, MAX(`stars`) AS `most-stars`, MAX(`search`) AS `most-search` FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL ? DAY) AND `userid`=?", array($intervalDays, $userid))[0];
	// get hourly stats
	$all = $CGBStats->database->query("SELECT SUM(`gold`) AS `gold`, SUM(`elix`) AS `elix`, SUM(`delix`) AS `delix`, SUM(`trophy`) AS `trophy`, SUM(1) AS `raid`, SUM(`search`) AS `search` FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL ? DAY) AND `userid`=? ORDER BY UNIX_TIMESTAMP(`date`) DESC", array($intervalDays, $userid));
	
	$timeInterval = $CGBStats->database->query("SELECT SUM(`range`) AS `time` FROM (SELECT ABS(d1 - d2) AS `range` FROM (SELECT MIN(UNIX_TIMESTAMP(T1.date)) AS `d1`, MIN(UNIX_TIMESTAMP(T2.date)) AS `d2` FROM (SELECT * FROM cgbstats_stats ORDER BY `date`) AS T1 INNER JOIN (SELECT * FROM cgbstats_stats ORDER BY `date`) AS T2 ON UNIX_TIMESTAMP(T1.date) > UNIX_TIMESTAMP(T2.date) - 7200 AND T1.userid = T2.userid WHERE UNIX_TIMESTAMP(T1.date) < UNIX_TIMESTAMP(T2.date) AND T1.userid = ? AND T1.date > DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY UNIX_TIMESTAMP(T1.date)) AS `temp`) AS `temp2`", array($userid, $intervalDays));
	
	$allDates = $CGBStats->database->query("SELECT COUNT(`date`) AS `all` FROM `cgbstats_stats` WHERE userid = ? AND date > DATE_SUB(NOW(), INTERVAL ? DAY)", array($userid, $intervalDays));
	$allDates = intval($allDates[0]['all']);
	
	$totals0 = array("gold" => 0, "elix" => 0, "delix" => 0, "trophy" => 0, "raid" => 0, "search" => 0);
	$time = intval($timeInterval[0]['time']);
	
	$time = $time / 3600; // $time is now in hours
	
	foreach($totals0 as $key=>$value){
		if($time > 0)
			$hourly["hourly-" . $key] = intval($all[0][$key]) / $time;
		else if($allDates > 0)
			$hourly["hourly-" . $key] = intval($all[0][$key]) / $allDates;
		else
			$hourly["hourly-" . $key] = 0;
	}
	
	$everything = array_merge($avgs, $totals, $records, $hourly);
	
	foreach($everything as $key => $value){
		$pv = floatval($value);
		if($pv > 1000000000) $pv = (floor($pv / 100000000) / 10) . "B";
		else if($pv > 1000000) $pv = (floor($pv / 100000) / 10) . "M";
		else if($pv > 1000) $pv = (floor($pv / 100) / 10) . "K";
		else $pv = (floor($pv * 10) / 10) . "";
		$everything[$key] = $pv;
	}
	
	echo json_encode($everything);
?>