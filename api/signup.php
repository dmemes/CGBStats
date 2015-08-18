<?php
include '../include/include.php';
header("Content-Type: application/json");
$CGBStats->disableCaching();
if(isset($_SESSION['userid'])) die(json_encode(array("status"=>"error","message"=>"Already logged in")));
if(!isset($_COOKIE['token']) || $_COOKIE['token'] !== $_SESSION['token']) die(json_encode(array("status"=>"error","message"=>"Bad token", "extra"=>"bad_token")));

try {
	$res;
	$apikey;
	do {
		$apikey = md5(uniqid(mt_rand()));
		$res = $CGBStats->database->query("SELECT * FROM `cgbstats_user` WHERE `apikey`=?", array($apikey));
	} while(sizeof($res) > 0);
	
	$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
	if($ip === NULL || $ip === "") $ip = $_SERVER['HTTP_X_REAL_IP'];
	if($ip === NULL || $ip === "") $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	
	$ua = $_SERVER['HTTP_USER_AGENT'];
	$cfray = $_SERVER['HTTP_CF_RAY'];
	$cfcountry = $_SERVER['HTTP_CF_IPCOUNTRY'];
	
	$CGBStats->database->query("INSERT INTO `cgbstats_user` (`apikey`, `ip`, `ua`, `cfray`, `cfcountry`) VALUES (?, ?, ?, ?, ?)", array($apikey, $ip, $ua, $cfray, $cfcountry));
	$res = $CGBStats->database->query("SELECT * FROM `cgbstats_user` WHERE `apikey`=?", array($apikey));
	if(sizeof($res) === 0) throw new Exception("Failed to create user");

	$CGBStats->refreshSession();
	$_SESSION['userid'] = $res[0]['userid'];
	
	setcookie("CGBStats_signed_up", "TRUE", time() + 3600 * 24 * 365 * 10, "/", "." . $CGBStats->config->domain);
	
	echo json_encode($res[0]);
} catch(Exception $e){
	die(json_encode(array("status"=>"error","message"=>"Server error", "ex" => $e->getMessage())));
}
?>