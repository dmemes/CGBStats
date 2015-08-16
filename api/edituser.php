<?php
include '../include/include.php';
header("Content-Type: application/json");

$CGBStats->disableCaching();

if(!isset($_SESSION['userid']) || !isset($_POST['username'])) die(json_encode(array("status"=>"error", "message"=>"Missing params")));

try {
	$CGBStats->database->query("UPDATE `cgbstats_user` SET `username`=? WHERE `userid`=?", array(substr($_POST['username'], 0, 32), intval($_SESSION['userid'])));
	
	$new_username = $CGBStats->database->query("SELECT `username` FROM `cgbstats_user` WHERE `userid`=?", array(intval($_SESSION['userid'])))[0]['username'];
	echo json_encode(array("status"=>"success", "username"=>$new_username));
} catch(Exception $e){
	 die(json_encode(array("status"=>"error", "message"=>"Server error")));
}

?>