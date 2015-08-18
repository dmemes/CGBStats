<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
$CGBStats->disableCaching();
$CGBStats->refreshSession();
setcookie("token", $_SESSION['token'], 0, "/", "." . $CGBStats->config->domain, FALSE, TRUE);
?>