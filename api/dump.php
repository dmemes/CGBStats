<?php
include '../include/include.php';

$userid = 0;
if(isset($_GET['userid'])){
	$userid = intval($_GET['userid']);
} else if(isset($_SESSION['userid'])){
	$userid = intval($_SESSION['userid']);
} else {
	die("[]");
}

$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` WHERE `userid`=? ORDER BY `date` DESC LIMIT 1", array($userid));
if(sizeof($checkModified) > 0) $CGBStats->enableCaching(intval($checkModified[0]['ts']));

$query = "SELECT `userid`, UNIX_TIMESTAMP(`date`) AS `date`, `ctrophy`, `cgold`, `celix`, `cdelix`, `thlevel`, `search`, `gold`, `elix`, `delix`, `trophy`, `stars`, `bgold`, `belix`, `bdelix` FROM `cgbstats_stats` WHERE `userid`=? AND `date` > DATE_SUB(NOW(), INTERVAL 30 DAY)";
$params = array($userid);

$res = $CGBStats->database->query($query, $params);
for($i = 0; $i < sizeof($res); $i++){
	foreach($res[$i] as $k=>$v){
		$res[$i][$k] = intval($v);
	}
}

header("Content-Type: application/json");

echo json_encode($res);
?>