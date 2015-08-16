<?php
$CGBStats->urlshortener = new CGBStatsBase();

$CGBStats->urlshortener->unshorten = function($path){
	global $CGBStats;
	$userid = intval(substr($path, 1, 4));
	$days = intval(substr($path, 5, 2));
	$type = array_search(substr($path, 7, 2), $CGBStats->images->typesId);
	return array("userid" => $userid, "days" => $days, "type" => $type);
};
?>