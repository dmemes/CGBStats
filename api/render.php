<?php
	include '../include/include.php';
	
	$userid = 0;
	$global = FALSE;
	if(isset($_GET['user'])){
		$userid = intval($_GET['user']);
	} else if(isset($_SESSION['userid'])) {
		$userid = intval($_SESSION['userid']);
	} else {
		$global = TRUE;
	}
	
	$type = "LOOT_VS_TIME";
	if(isset($_GET['type']) && array_key_exists($_GET['type'], $CGBStats->images->types)){
		$type = $_GET['type'];
	}
	if(isset($_GET['global'])) {
		$global = TRUE;
	}
	
	$imageData = "";
	
	$width = 800;
	$height = 400;
	if(isset($_GET['signature'])) {
		$width = 800;
		$height = 150;
	}
	
	$intervalDays = 2;
	if(isset($_GET['days'])) $intervalDays = intval($_GET['days']);
	
	if($intervalDays < 1 || $intervalDays > 30) $intervalDays = 2;
	
	if(!$global){
		$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` WHERE `userid`=? ORDER BY `date` DESC LIMIT 1", array($userid));
		if(sizeof($checkModified) > 0) $CGBStats->enableCachingWithData(intval($checkModified[0]['ts']), $userid . $type . $width . $height . $intervalDays);
	}
	
	// set expires to 30 mins
	header("Expires: on, " . date("D, d M Y H:i:s", time() + 1800) . " GMT");
	
	try {
		if($global){
			$category = "Averages";
			if(isset($_GET['category']) && array_key_exists($_GET['category'], $CGBStats->images->globalCategories)){
				$category = $_GET['category'];
			}
			
			$checkModified = $CGBStats->database->query("SELECT UNIX_TIMESTAMP(`date`) AS `ts` FROM `cgbstats_stats` ORDER BY `date` DESC LIMIT 1", array());
			if(sizeof($checkModified) > 0) $CGBStats->enableCachingWithData(intval($checkModified[0]['ts']), "global" . $category);
			
			$types = $CGBStats->images->globalCategories[$category];
			$ret = array();
			foreach($types as $type){
				$imageData = $CGBStats->images->renderGlobal($type);
				array_push($ret, "data:image/png;base64," . base64_encode($imageData));
			}
			header("Content-Type: application/json");
			echo json_encode($ret);
		} else {
			$imageData = $CGBStats->images->renderGraph($userid, $type, $intervalDays, $width, $height);
			if(isset($_GET['dataurl'])) {
				header("Content-Type: text/plain");
				echo "data:image/png;base64," . base64_encode($imageData);
			} else {
				header("Content-Type: image/png");
				echo $imageData;
			}
		}
	} catch(Exception $e){
		echo "Render failed\n";
		echo $e->getMessage() . "\n";
		echo $e->getTraceAsString();
	}
?>