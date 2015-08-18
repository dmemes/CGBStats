<?php
include '../include/include.php';
header("Content-Type: application/json");
$CGBStats->disableCaching();
if(isset($_SESSION['userid'])) die(json_encode(array("status"=>"error","message"=>"Already logged in")));
if(!isset($_POST['apikey'])) die(json_encode(array("status"=>"error","message"=>"Malformed request")));
if(!isset($_COOKIE['token']) || $_COOKIE['token'] !== $_SESSION['token']) die(json_encode(array("status"=>"error","message"=>"Bad token","extra"=>"bad_token")));

try {
	if($CGBStats->authenticate($_POST['apikey'])){
		$CGBStats->refreshSession();
		
		$CGBStats->database->query("UPDATE `cgbstats_user` SET `lastlogin`=NOW() WHERE `apikey`=?", array($_POST['apikey']));
		$_SESSION['lastauthtime'] = strval(time());
		
		echo json_encode(array("status"=>"success", "message"=>"Logged in"));
	} else {
		echo json_encode(array("status"=>"error", "message"=>"Wrong key"));
	}
} catch(Exception $e){
	die(json_encode(array("status"=>"error","message"=>"Server error")));
}
?>