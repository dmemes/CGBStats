<?php
include '../include/include.php';
header("Content-Type: application/json");

$CGBStats->disableCaching();

if(isset($_SESSION['userid'])) echo json_encode(array("logged_in" => TRUE, "userid" => intval($_SESSION['userid'])));
else echo json_encode(array("logged_in" => FALSE));
?>