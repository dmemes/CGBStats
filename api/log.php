<?php
include '../include/include.php';
header("Content-Type: application/json");
$CGBStats->disableCaching();

function dieWithError($error, $code){
	http_response_code($code);
	die(json_encode(array("status"=>"error", "message" => $error)));
}

if($_SERVER['REQUEST_METHOD'] !== 'POST') dieWithError("POST requests only");

$required_fields = array(
	// auth
	"apikey",
	// current storage / trophy values
	"ctrophy", "cgold", "celix", "cdelix",
	// searches
	"search",
	// raided loot, gained trophies
	"gold", "elix", "delix", "trophy",
	// bonus loot
	"bgold", "belix", "bdelix",
	// stars
	"stars",
	// TH level
	"thlevel",
	// bot log
	"log"
);

$log_data = array();
$query = "INSERT INTO `cgbstats_stats`"
	."       (date,   userid,  ctrophy,  cgold,  celix,  cdelix,  thlevel,  search,  gold,  elix,  delix,  trophy,  bgold,  belix,  bdelix,   stars,  log)"
	. "VALUES(NOW(), :userid, :ctrophy, :cgold, :celix, :cdelix, :thlevel, :search, :gold, :elix, :delix, :trophy, :bgold, :belix, :bdelix,  :stars, :log)";

for($i = 0; $i < sizeof($required_fields); $i++){
	$field = $required_fields[$i];
	if(!isset($_POST[$field])) dieWithError("Missing param: " . $field, 400);
	
	if($field !== 'apikey'){
		$log_data[":" . $field] = $_POST[$field];
	}
}

$userid = $CGBStats->getUserIdFromApiKey($_POST['apikey']);
if($userid < 0) dieWithError("Auth failed", 400);

// let's not have spam please
$res = $CGBStats->database->query("SELECT * FROM `cgbstats_stats` WHERE `userid`=? AND `date`>DATE_SUB(NOW(), INTERVAL 30 SECOND)", array($userid));
if(sizeof($res) > 0) dieWithError("Requesting too fast", 429);

$log_data[':userid'] = $userid;

try {
	$CGBStats->database->query($query, $log_data);
	echo json_encode(array("status"=>"success"));
} catch(Exception $e){
	dieWithError("Server error", 500);
}

// delete old stats 20% of the time
try {
	if(rand(0, 10) > 8) $CGBStats->database->query("DELETE FROM `cgbstats_stats` WHERE `date`<DATE_SUB(NOW(), INTERVAL 30 DAY)", array());
} catch(Exception $e){
}
?>