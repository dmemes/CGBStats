<?php
$CGBStats->images = new CGBStatsBase();

$CGBStats->images->typesId = array(
	"LOOT_VS_TIME" => "LD",
	"TOTAL_VS_TIME" => "SD",
	"TROPHIES_VS_TIME" => "TD",
	"LOOT_VS_TROPHIES" => "LT",
	"LOOT_VS_SEARCHES" => "LN",
	"TROPHIES_VS_TROPHIES" => "TT",
	"SEARCHES_VS_TROPHIES" => "NT",
	"SEARCHES_VS_TIME" => "SD",
	"STARS_VS_TIME" => "QD",
	"STARS_VS_TROPHIES" => "QT",
	"RAID_VS_TIME" => "RD"
);

$CGBStats->images->types = array(
	"LOOT_VS_TIME" => "Loot vs. time",
	"TOTAL_VS_TIME" => "Storage levels vs. time",
	"TROPHIES_VS_TIME" => "Trophy level vs. time",
	"LOOT_VS_TROPHIES" => "Loot vs. trophy level",
	"LOOT_VS_SEARCHES" => "Loot vs. number of skips",
	"TROPHIES_VS_TROPHIES" => "Gained trophies vs. trophy level",
	"SEARCHES_VS_TROPHIES" => "Searches vs. trophy level",
	"SEARCHES_VS_TIME" => "Number of skips vs. time",
	"STARS_VS_TIME" => "Stars vs. time",
	"STARS_VS_TROPHIES" => "Stars vs. trophy level",
	"RAID_VS_TIME" => "All raids"
);

$CGBStats->images->globalTypes = array(
	"LOOT_VS_TIME" => "Average loot",
	"TROPHIES_VS_TIME" => "Average trophies",
	"SEARCHES_VS_TIME" => "Average skips",
	"STARS_VS_TIME" => "Average stars",
	
	"LOOT_VS_TH" => "Average loot vs. town hall level",
	"TROPHIES_VS_TH" => "Average trophies vs. town hall level",
	"SEARCHES_VS_TH" => "Average skips vs. town hall level",
	"STARS_VS_TH" => "Average stars vs. town hall level",
	
	"LOOT_VS_TROPHIES" => "Average loot vs. trophy level",
	"TROPHIES_VS_TROPHIES" => "Average gained trophies vs. trophy level",
	"SEARCHES_VS_TROPHIES" => "Average skips vs. trophy level",
	"STARS_VS_TROPHIES" => "Average stars vs. trophy level",
	
	"LOOT_VS_SEARCHES" => "Average loot vs. skips",
	"TROPHIES_VS_SEARCHES" => "Average trophies vs. skips",
	"RAID_VS_SEARCHES" => "All raids for skips",
	"STARS_VS_SEARCHES" => "Average stars vs. skips"
);

$CGBStats->images->globalCategories = array(
	"Averages" => array(
		"LOOT_VS_TIME",
		"TROPHIES_VS_TIME",
		"SEARCHES_VS_TIME",
		"STARS_VS_TIME"),
	"Averages for town hall level" => array(
		"LOOT_VS_TH",
		"TROPHIES_VS_TH",
		"SEARCHES_VS_TH",
		"STARS_VS_TH"),
	"Averages for trophy level" => array(
		"LOOT_VS_TROPHIES",
		"TROPHIES_VS_TROPHIES",
		"SEARCHES_VS_TROPHIES",
		"STARS_VS_TROPHIES"),
	"Averages for number of skips" => array(
		"LOOT_VS_SEARCHES",
		"TROPHIES_VS_SEARCHES",
		"RAID_VS_SEARCHES",
		"STARS_VS_SEARCHES")
);

$CGBStats->images->endsWith = function($haystack, $needle) {
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
};

$CGBStats->images->startsWith = function($haystack, $needle) {
    return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== FALSE;
};

$CGBStats->images->renderGlobal = function($type){
	global $CGBStats;
	
	$CGBStats->graph->graphBegin(800, 400);
	
	// set label function and intervals for the type of data
	if($CGBStats->images->endsWith($type, "VS_TIME")){
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->timeLabelFunction;
		$CGBStats->graph->xIntervals = $CGBStats->graph->intervalTime;
		$CGBStats->graph->xMax = time();
	} else {
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->idtLabelFunction;
		$CGBStats->graph->xIntervals = $CGBStats->graph->interval521;
	}
	
	$CGBStats->graph->labelFunctionY = $CGBStats->graph->idtLabelFunction;
	
	$CGBStats->graph->yIntervals = $CGBStats->graph->interval521;
	
	// set graph type
	$CGBStats->graph->graphType = "bar";
	$CGBStats->graph->combineMode = "average";
	
	if($CGBStats->images->endsWith($type, "VS_TROPHIES") || $CGBStats->images->endsWith($type, "VS_SEARCHES") || $CGBStats->images->endsWith($type, "VS_TH")){
		$CGBStats->graph->xMin = 0;
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->strvalLabelFunction;
		$CGBStats->graph->snapX = "min";
	}
	
	if($CGBStats->images->endsWith($type, "VS_TROPHIES") || $CGBStats->images->endsWith($type, "VS_SEARCHES")){
		//if($CGBStats->isDev()) $CGBStats->graph->graphType = "linefilled";
	}
	
	// draw the base grid
	$CGBStats->graph->drawGrid("gray");
	
	// select the mysql data
	$query = "SELECT ";
	$x_column = "";
	$y_column = array();
	
	if($CGBStats->images->startsWith($type, "LOOT")){
		$query .= "AVG(`gold`) AS `gold`, AVG(`elix`) AS `elix`, AVG(`delix`) AS `delix`";
		$y_column = array("gold", "elix", "delix");
	} else if($CGBStats->images->startsWith($type, "TROPHIES")){
		$query .= "AVG(`trophy`) AS `trophy`";
		$y_column = array("trophy");
	} else if($CGBStats->images->startsWith($type, "SEARCHES")){
		$query .= "AVG(`search`) AS `search`";
		$y_column = array("search");
	} else if($CGBStats->images->startsWith($type, "STARS")){
		$CGBStats->graph->labelFunctionY = $CGBStats->graph->decimalLabelFunction;
		$query .= "AVG(`stars`) AS `stars`";
		$y_column = array("stars");
	} else if($CGBStats->images->startsWith($type, "RAID")){
		$query .= "SUM(1) AS `raid`";
		$y_column = array("raid");
	}
	
	if($CGBStats->images->endsWith($type, "VS_TIME")){
		$query .= ", FLOOR(UNIX_TIMESTAMP(MIN(`date`)) / 18000) * 18000 AS `tdate`";
		$x_column = "tdate";
	} else if($CGBStats->images->endsWith($type, "VS_TH")){
		$query .= ", `thlevel`";
		$x_column = "thlevel";
	} else if($CGBStats->images->endsWith($type, "VS_TROPHIES")){
		$query .= ", FLOOR(MIN(`ctrophy`) / 200) * 200 AS `tctrophy`";
		$x_column = "tctrophy";
	} else if($CGBStats->images->endsWith($type, "VS_SEARCHES")){
		$query .= ", FLOOR(MIN(`search`) / 10) * 10 AS `tsearch`";
		$x_column = "tsearch";
	}
	
	$query .= " FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL 2 DAY) GROUP BY";
	
	if($CGBStats->images->endsWith($type, "VS_TIME")){
		$query .= " FLOOR(UNIX_TIMESTAMP(`date`) / 18000) * 18000 ORDER BY `tdate`";
	} else if($CGBStats->images->endsWith($type, "VS_TH")){
		$query .= " `thlevel` ORDER BY `thlevel`";
	} else if($CGBStats->images->endsWith($type, "VS_TROPHIES")){
		$query .= " FLOOR(`ctrophy` / 200) * 200 ORDER BY `tctrophy`";
	} else if($CGBStats->images->endsWith($type, "VS_SEARCHES")){
		$query .= " FLOOR(`search` / 10) * 10 ORDER BY `tsearch`";
	}
	
	$allCols = array_merge($y_column, array($x_column));
	
	$selectData = implode(", ", $allCols);
	$query = "SELECT $selectData FROM (" . $query . ") AS `temp` WHERE `$x_column` != 0";
	
	//if($CGBStats->isDev()) var_dump($query);
	
	// get the data
	$res = $CGBStats->database->query($query, array());
	//if($CGBStats->isDev()) var_dump($res);
	// plot the data
	
	//if($CGBStats->isDev()) var_dump(array($x_column, $y_column));
	
	$predefined_colors = array(
		"gold" => "gold",
		"elix" => "elixir",
		"delix" => "darkelixir",
		"bgold" => "gold",
		"belix" => "elixir",
		"bdelix" => "darkelixir",
		"cgold" => "gold",
		"celix" => "elixir",
		"cdelix" => "darkelixir",
		"trophy" => "gold",
		"ctrophy" => "gold",
		"stars" => "gold"
	);
	
	$predefined_names = array(
		"ctrophy" => "TR",
		"cgold" => "G",
		"celix" => "E",
		"cdelix" => "DE",
		"search" => "Skips",
		"gold" => "Gained G",
		"elix" => "Gained E",
		"delix" => "Gained DE",
		"trophy" => "Gained TR",
		"bgold" => "Bonus G",
		"belix" => "Bonus E",
		"bdelix" => "Bonus DE",
		"raid" => "Raids",
		"stars" => "Stars",
		"thlevel" => "TH Level"
	);
	
	$predefined_scale = array(
		"cdelix" => TRUE,
		"delix" => TRUE,
		"bdelix" => TRUE
	);
	
	for($i = 0; $i < sizeof($y_column); $i++){
		$plot_data = array();
		for($j = 0; $j < sizeof($res); $j++){
			$plot_data[intval($res[$j][$x_column])] = intval($res[$j][$y_column[$i]]);
		}
		$color = "black";
		if(array_key_exists($y_column[$i], $predefined_colors)){
			$color = $predefined_colors[$y_column[$i]];
		}
		$name = "Plot";
		if(array_key_exists($y_column[$i], $predefined_names)){
			$name = $predefined_names[$y_column[$i]];
		}
		$scale = FALSE;
		if(array_key_exists($y_column[$i], $predefined_scale)){
			$scale = $predefined_scale[$y_column[$i]];
		}
		//if($CGBStats->isDev()) var_dump($plot_data);
		$CGBStats->graph->plot($plot_data, $color, $name, $scale);
	}
	
	// set the title and render
	$CGBStats->graph->title = $CGBStats->images->globalTypes[$type];
	$CGBStats->graph->render();
	
	// get the image data
	$image = $CGBStats->graph->graphEnd();
	
	return $image;
};

$CGBStats->images->renderGraph = function($userid, $type, $dateInterval, $w, $h){
	global $CGBStats;
	
	//$CGBStats->graph->startProfiling();
	
	$CGBStats->graph->graphBegin($w, $h);
	
	// set label function and intervals for the type of data
	if($CGBStats->images->endsWith($type, "VS_TIME")){
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->timeLabelFunction;
		$CGBStats->graph->xIntervals = $CGBStats->graph->intervalTime;
		$CGBStats->graph->xMax = time();
	} else {
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->idtLabelFunction;
		$CGBStats->graph->xIntervals = $CGBStats->graph->interval521;
	}
	
	$CGBStats->graph->labelFunctionY = $CGBStats->graph->idtLabelFunction;
	if($CGBStats->images->startsWith($type, "STARS")){
		$CGBStats->graph->labelFunctionY = $CGBStats->graph->decimalLabelFunction;
	}
	
	$CGBStats->graph->yIntervals = $CGBStats->graph->interval521;
	
	
	// set graph type
	if($type === "TOTAL_VS_TIME" ||
			$type === "TROPHIES_VS_TIME"){
		$CGBStats->graph->graphType = "line";
	} else {
		$CGBStats->graph->graphType = "bar";
	}
	
	if($CGBStats->images->endsWith($type, "VS_TROPHIES") || $CGBStats->images->endsWith($type, "VS_SEARCHES")){
		$CGBStats->graph->combineMode = "average";
		$CGBStats->graph->labelFunctionX = $CGBStats->graph->strvalLabelFunction;
		// $CGBStats->graph->xMin = 0;
	} else {
		$CGBStats->graph->combineMode = "sum";
	}
	
	// draw the base grid
	$CGBStats->graph->drawGrid("gray");
	
	// select the mysql data
	$query = "";
	$x_column = "";
	$y_column = array();
	
	switch($type){
	case "LOOT_VS_TIME":
		$query = "SELECT `gold`, `elix`, `delix`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("gold", "elix", "delix");
		break;
	case "TOTAL_VS_TIME":
		$query = "SELECT `cgold`, `celix`, `cdelix`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("cgold", "celix", "cdelix");
		break;
	case "TROPHIES_VS_TIME":
		$query = "SELECT `ctrophy`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("ctrophy");
		break;
	case "LOOT_VS_TROPHIES":
		$query = "SELECT `ctrophy`, `gold`, `elix`, `delix`";
		$x_column = "ctrophy";
		$y_column = array("gold", "elix", "delix");
		break;
	case "LOOT_VS_SEARCHES":
		$query = "SELECT `search`, `gold`, `elix`, `delix`";
		$x_column = "search";
		$y_column = array("gold", "elix", "delix");
		break;
	case "TROPHIES_VS_TROPHIES":
		$query = "SELECT `ctrophy`, `trophy`";
		$x_column = "ctrophy";
		$y_column = array("trophy");
		break;
	case "SEARCHES_VS_TROPHIES":
		$query = "SELECT `ctrophy`, `search`";
		$x_column = "ctrophy";
		$y_column = array("search");
		break;
	case "SEARCHES_VS_TIME":
		$query = "SELECT `search`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("search");
		break;
	case "STARS_VS_TIME":
		$query = "SELECT `stars`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("stars");
		break;
	case "STARS_VS_TROPHIES":
		$query = "SELECT `stars`, `ctrophy`";
		$x_column = "ctrophy";
		$y_column = array("stars");
		break;
	case "RAID_VS_TIME":
		$query = "SELECT 1 as `raid`, UNIX_TIMESTAMP(`date`) AS `date`";
		$x_column = "date";
		$y_column = array("raid");
		break;
	}
	
	$query .= " FROM `cgbstats_stats` WHERE `date` > DATE_SUB(NOW(), INTERVAL ? DAY) AND `userid`=?";
	
	if($CGBStats->images->endsWith($type, "VS_TROPHIES")){
		$query .= " ORDER BY `ctrophy`";
	} else if($CGBStats->images->endsWith($type, "VS_SEARCHES")) {
		$query .= " ORDER BY `search`";
	} else {
		$query .= " ORDER BY `date`";
	}
	
	// get the data
	$res = $CGBStats->database->query($query, array($dateInterval, $userid));
	// plot the data
	
	$predefined_colors = array(
		"gold" => "gold",
		"elix" => "elixir",
		"delix" => "darkelixir",
		"bgold" => "gold",
		"belix" => "elixir",
		"bdelix" => "darkelixir",
		"cgold" => "gold",
		"celix" => "elixir",
		"cdelix" => "darkelixir",
		"trophy" => "gold",
		"ctrophy" => "gold",
		"stars" => "gold"
	);
	
	$predefined_names = array(
		"ctrophy" => "TR",
		"cgold" => "G",
		"celix" => "E",
		"cdelix" => "DE",
		"search" => "Skips",
		"gold" => "Gained G",
		"elix" => "Gained E",
		"delix" => "Gained DE",
		"trophy" => "Gained TR",
		"bgold" => "Bonus G",
		"belix" => "Bonus E",
		"bdelix" => "Bonus DE",
		"raid" => "Raids",
		"stars" => "Stars"
	);
	
	$predefined_scale = array(
		"cdelix" => TRUE,
		"delix" => TRUE,
		"bdelix" => TRUE
	);
	
	for($i = 0; $i < sizeof($y_column); $i++){
		$plot_data = array();
		for($j = 0; $j < sizeof($res); $j++){
			$plot_data[intval($res[$j][$x_column])] = intval($res[$j][$y_column[$i]]);
		}
		$color = "black";
		if(array_key_exists($y_column[$i], $predefined_colors)){
			$color = $predefined_colors[$y_column[$i]];
		}
		$name = "Plot";
		if(array_key_exists($y_column[$i], $predefined_names)){
			$name = $predefined_names[$y_column[$i]];
		}
		$scale = FALSE;
		if(array_key_exists($y_column[$i], $predefined_scale)){
			$scale = $predefined_scale[$y_column[$i]];
		}
		
		$CGBStats->graph->plot($plot_data, $color, $name, $scale);
	}
	
	// set the title and render
	$statsuser = $CGBStats->database->query("SELECT `username` FROM `cgbstats_user` WHERE `userid`=?", array($userid))[0]['username'];
	$CGBStats->graph->title = $CGBStats->images->types[$type] . " for " . $statsuser;
	$CGBStats->graph->render();
	
	// get the image data
	$image = $CGBStats->graph->graphEnd();
	
	return $image;
};
?>