<?php
// define boilerplate
class CGBStatsBase {
	public function __call($method, $args) {
        if (isset($this->$method)) {
            $func = $this->$method;
            return call_user_func_array($func, $args);
        }
    }
}

// main object
$CGBStats = new CGBStatsBase();

// include extra files
require_once 'config/config.php';
require_once 'database.php';
require_once 'urlshortener.php';
require_once 'render/graph.php';
require_once 'data/images.php';

// connect database
$CGBStats->database->connect();

// deal with session stuff
ini_set("session.gc_maxlifetime", 3600 * 24 * 7);
ini_set('session.gc_probability', 10);
ini_set('session.gc_divisor', 100);
session_save_path(realpath($_SERVER['DOCUMENT_ROOT'] . '/session0/'));
session_set_cookie_params(3600 * 24 * 7, "/", "." . $CGBStats->config->domain, FALSE, TRUE);
session_name("CGBStatsSession");
session_start();

$CGBStats->refreshSession = function(){
	session_regenerate_id();
	$_SESSION['token'] = substr(uniqid(), 8);
};

if(!isset($_SESSION['token'])) $CGBStats->refreshSession();

$CGBStats->authenticate = function($apiKey){
	global $CGBStats;
	$res = $CGBStats->database->query("SELECT * FROM `cgbstats_user` WHERE `apikey`=?", array($apiKey));
	if(sizeof($res) > 0){
		$CGBStats->refreshSession();
		$_SESSION['userid'] = intval($res[0]['userid']);
		return TRUE;
	} else {
		return FALSE;
	}
};

$CGBStats->getUserIdFromApiKey = function($apiKey){
	global $CGBStats;
	$res = $CGBStats->database->query("SELECT * FROM `cgbstats_user` WHERE `apikey`=?", array($apiKey));
	if(sizeof($res) > 0){
		return intval($res[0]['userid']);
	} else {
		return -1;
	}
};

if(isset($_SESSION['userid'])){
	$CGBStats->username = $CGBStats->database->query("SELECT `username` FROM `cgbstats_user` WHERE `userid`=?", array(intval($_SESSION['userid'])))[0]['username'];
}

$CGBStats->__hostSpecificHeaderRemove = function(){
	header_remove("Pragma");
	header_remove("Cache-Control");
	header_remove("Expires");
};

// current host adds these annoying headers
$CGBStats->__hostSpecificHeaderRemove();

$CGBStats->cachingSet = FALSE;

$CGBStats->disableCaching = function(){
	global $CGBStats;
	if($CGBStats->cachingSet) return;
	header("Expires: on, 01 Jan 1970 00:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	$CGBStats->cachingSet = TRUE;
};

$CGBStats->enableCaching = function($timestamp){
	global $CGBStats;
	$CGBStats->enableCachingWithData($timestamp, "");
};

$CGBStats->isDev = function(){
	return FALSE;
};

$CGBStats->enableCachingWithData = function($timestamp, $data){
	global $CGBStats;
	if($CGBStats->cachingSet) return;
	date_default_timezone_set("UTC");
	$tsstring = date('D, d M Y H:i:s ', $timestamp) . 'GMT';
	$etag = $timestamp . $data;
	if(isset($_SESSION['userid'])) $etag .= $_SESSION['userid'];
	
	$etag = md5($etag);
	
	$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
	if($if_modified_since !== FALSE) $if_modified_since = strtotime($if_modified_since);
	$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;
	
	if ((($if_none_match && strpos($if_none_match, $etag) !== FALSE) || (!$if_none_match)) && ($if_modified_since && $if_modified_since >= $timestamp)){
		http_response_code(304);
		header('HTTP/1.1 304 Not Modified');
		exit();
	} else {
		header("Last-Modified: $tsstring", true);
		header("ETag: \"{$etag}\"");
		// header("Expires: on, " . date("D, d M Y H:i:s", time() + 3600 * 4) . " GMT");
	}
	$CGBStats->cachingSet = TRUE;
};
?>