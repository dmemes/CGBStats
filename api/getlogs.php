<?php
include '../include/include.php';

header("Content-Type: application/json");

if(!isset($_SESSION['userid'])) die(json_encode(array()));

$userid = $_SESSION['userid'];

$query = "SELECT UNIX_TIMESTAMP(`date`) AS `dateDisplay`, `date` FROM `cgbstats_stats` WHERE `userid`=? ORDER BY UNIX_TIMESTAMP(`date`) DESC";
$params = array(intval($_SESSION['userid']));
if(isset($_GET['search'])){
	$search = $_GET['search'];
	$query = "SELECT `log`, MATCH(`log`) AGAINST(? IN BOOLEAN MODE) AS `relev`, `date`, UNIX_TIMESTAMP(`date`) AS `dateDisplay` FROM `cgbstats_stats` WHERE `userid`=? AND MATCH(`log`) AGAINST(? IN BOOLEAN MODE) ORDER BY UNIX_TIMESTAMP(`date`) DESC, `relev`";
	$params = array($search, intval($_SESSION['userid']), $search);
	$column = "log";
} else if(isset($_GET['date'])){
	$date = $_GET['date'];
	$query = "SELECT `log` FROM `cgbstats_stats` WHERE `date`=? AND `userid`=?";
	$params = array($date, intval($_SESSION['userid']));
	$column = "log";
} else {
	$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` WHERE `userid`=? ORDER BY `date` DESC LIMIT 1", array(intval($_SESSION['userid'])));
	if(sizeof($checkModified) > 0) $CGBStats->enableCaching(intval($checkModified[0]['ts']));
}

if(isset($_COOKIE['cgbstz']))
	date_default_timezone_set($_COOKIE['cgbstz']);
else
	date_default_timezone_set("UTC");

$res = $CGBStats->database->query($query, $params);
for($i = 0; $i < sizeof($res); $i++){
	if(isset($res[$i]['dateDisplay'])) $res[$i]['dateDisplay'] = date("D, d M Y H:i:s", intval($res[$i]['dateDisplay']));
}

echo json_encode($res);
?>