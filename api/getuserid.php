<?php
include '../include/include.php';
header("Content-Type: application/json");

$CGBStats->enableCachingWithData(filemtime(__FILE__), isset($_SESSION['userid']) ? $_SESSION['userid'] : "");

if(isset($_SESSION['userid'])) echo json_encode(array("logged_in" => TRUE, "userid" => intval($_SESSION['userid'])));
else echo json_encode(array("logged_in" => FALSE));
?>