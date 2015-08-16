<?php
include '../include/include.php';
$CGBStats->disableCaching();
unset($_SESSION['userid']);
$CGBStats->refreshSession();
$_SESSION['lastauthtime'] = strval(time());
?>