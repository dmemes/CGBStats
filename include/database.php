<?php
$CGBStats->database = new CGBStatsBase();

$CGBStats->database->handle = NULL;

$CGBStats->database->connect = function(){};

$CGBStats->database->connected = FALSE;

$CGBStats->database->connect0 = function(){
	global $CGBStats;
	
	$numTries = 0;
	do {
		try {
			$conf = "mysql:host=" . $CGBStats->config->dbhost . ";dbname=" .  $CGBStats->config->dbname . ";charset=utf8";
			$dbh = new PDO($conf, $CGBStats->config->dbuser, $CGBStats->config->dbpass);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$CGBStats->database->handle = $dbh;
			$CGBStats->database->connected = TRUE;
			break;
		} catch(Exception $e){
			// probably too many connections, wait and try again
			sleep(2);
			$numTries++;
		}
	} while($numTries < 10);
};

$CGBStats->database->query = function($query, $params){
	global $CGBStats;
	
	if(!$CGBStats->database->connected){
		$CGBStats->database->connect0();
	}
	
	$stmt = $CGBStats->database->handle->prepare($query);
	$stmt->execute($params);
	if($stmt->rowCount() > 0){
		try {
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} catch(Exception $e){
			return array();
		}
	} else {
		return array();
	}
};

$CGBStats->database->setStringify = function($stringify){
	global $CGBStats;
	$CGBStats->database->handle->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, $stringify);
};
?>