<?php
$CGBStats->graph = new CGBStatsBase();

$CGBStats->graph->im = NULL;
$CGBStats->graph->colors = array();

$CGBStats->graph->config = new CGBStatsBase();
$CGBStats->graph->config->font = realpath($_SERVER['DOCUMENT_ROOT'] . "/static/Supercell-Magic/Supercell-Magic.ttf");

// constants
$CGBStats->graph->idtLabelFunction = function($val){
	$str = strval(floor($val));
	if($val >= 1000) $str = strval(floor($val / 100) / 10) . "K";
	if($val >= 1000000)$str = strval(floor($val / 100000) / 10) . "M";
	return $str;
};

$CGBStats->graph->strvalLabelFunction = function($val){
	return strval($val);
};

$CGBStats->graph->decimalLabelFunction = function($val){
	return strval($val);
};

$CGBStats->graph->_lastTimeLabel = 0;

$CGBStats->graph->timeLabelFunction = function($val){
	global $CGBStats;
	if(time() - $val < 60 * 2) return "Now";
	
	if(isset($_COOKIE['cgbstz']))
		date_default_timezone_set($_COOKIE['cgbstz']);
	else
		date_default_timezone_set("UTC");
	$date = date("G:i", $val);
	
	$last = $CGBStats->graph->_lastTimeLabel = 0;
	$CGBStats->graph->_lastTimeLabel = $val;
	
	if(intval(date("G", $val)) < intval(date("G", $last))){
		return $date . "\n" . date("d M", $val);
	} else {
		return $date;
	}
};

$CGBStats->graph->interval521 = array(
		100000000,
		50000000, 20000000, 10000000,
		5000000, 2000000, 1000000,
		500000, 200000, 100000,
		50000, 20000, 10000,
		5000, 2000, 1000,
		500, 200, 100,
		50, 20, 10,
		5, 2, 1,
		0.5, 0.2, 0.1
	);
$CGBStats->graph->intervalTime = array(
		7 * 24 * 3600,
		2 * 24 * 3600,
		24 * 3600,
		12 * 3600,
		6 * 3600,
		2 * 3600,
		3600,
		1800,
		900,
		600,
		300,
		120,
		60
	);

// config

$CGBStats->graph->config->gridSizeX = 50;
$CGBStats->graph->config->gridSizeY = 15;
$CGBStats->graph->config->paddingX = 50;
$CGBStats->graph->config->paddingY = 40;

$CGBStats->graph->data = array();
$CGBStats->graph->title = "";

$CGBStats->graph->graphType = "line";

$CGBStats->graph->gridData = NULL;

$CGBStats->graph->xIntervals = array(1);
$CGBStats->graph->yIntervals = array(1);

$CGBStats->graph->snapX = "max";

$CGBStats->graph->combineMode = "sum";

$CGBStats->graph->xMax = -1;
$CGBStats->graph->xMin = -1;

$CGBStats->graph->startTime = 0;

$CGBStats->graph->startProfiling = function(){
	global $CGBStats;
	$CGBStats->graph->startTime = time();
};

$CGBStats->graph->labelFunctionX = function($val){
	return strval($val);
};

$CGBStats->graph->labelFunctionY = function($val){
	return strval($val);
};

$CGBStats->graph->graphBegin = function($w, $h){
	global $CGBStats;
	$im = $CGBStats->graph->im = @imagecreatetruecolor($w, $h);
	if($im === FALSE) throw new Exception("Failed to create image");
	
	$CGBStats->graph->colors = array();
	$CGBStats->graph->data = array();
	$CGBStats->graph->title = "";
	$CGBStats->graph->xIntervals = array();
	$CGBStats->graph->yIntervals = array();
	$CGBStats->graph->graphType = "line";
	$CGBStats->graph->xMax = -1;
	$CGBStats->graph->xMin = -1;
	$CGBStats->graph->combineMode = "sum";
	$CGBStats->graph->snapX = "max";
	$CGBStats->graph->_lastTimeLabel = 0;
	
	$CGBStats->graph->labelFunctionX = function($val){
		return strval($val);
	};

	$CGBStats->graph->labelFunctionY = function($val){
		return strval($val);
	};
	
	$CGBStats->graph->colors['white'] = @imagecolorallocate($im, 255, 255, 255);
	$CGBStats->graph->colors['black'] = @imagecolorallocate($im, 0, 0, 0);
	$CGBStats->graph->colors['darkelixir'] = $CGBStats->graph->colors['black'];
	$CGBStats->graph->colors['gray'] = @imagecolorallocate($im, 100, 100, 100);
	$CGBStats->graph->colors['lightgray'] = @imagecolorallocate($im, 200, 200, 200);
	$CGBStats->graph->colors['darkgray'] = @imagecolorallocate($im, 100, 100, 100);
	$CGBStats->graph->colors['gold'] = @imagecolorallocate($im, 230, 193, 50);
	$CGBStats->graph->colors['elixir'] = @imagecolorallocate($im, 226, 51, 214);
	
	foreach($CGBStats->graph->colors as $color => $val){
		if($val === FALSE) throw new Exception("Failed to allocate color: " . $color);
	}
	
	$w = imagesx($im);
	$h = imagesy($im);
	imagefilledrectangle($im, 0, 0, $w - 1, $h - 1, $CGBStats->graph->colors['white']);
	imagerectangle($im, 0, 0, $w - 1, $h - 1, $CGBStats->graph->colors['gray']);
};

$CGBStats->graph->graphEnd = function(){
	global $CGBStats;
	$im = $CGBStats->graph->im;
	
	if(!@ob_start()) throw new Exception("Failed to start output buffering");
	if(!@imagepng($im)) throw new Exception("Failed to write image");
	$data = @ob_get_contents();
	if($data === FALSE) throw new Exception("Failed to get output buffer");
	@ob_end_clean();
	
	@imagedestroy($im);
	$CGBStats->graph->colors = array();
	
	return $data;
};

$CGBStats->graph->drawGrid = function($colorName){
	global $CGBStats;
	$im = $CGBStats->graph->im;
	
	$w = imagesx($im);
	$h = imagesy($im);
	
	imagesetthickness($im, 1);
	
	$numX = 0;
	for($gx = $CGBStats->graph->config->paddingX; $gx < $w; $gx += $CGBStats->graph->config->gridSizeX){
		imageline($im, $gx, 0, $gx, $h - $CGBStats->graph->config->paddingY, $CGBStats->graph->colors[$colorName]);
		$numX++;
	}
	
	$numY = 0;
	for($gy = $h - $CGBStats->graph->config->paddingY; $gy > 0; $gy -= $CGBStats->graph->config->gridSizeY){
		imageline($im, $CGBStats->graph->config->paddingX, $gy, $w, $gy, $CGBStats->graph->colors[$colorName]);
		$numY++;
	}
	
	$CGBStats->graph->gridData = array("numX"=>$numX, "numY"=>$numY);
};

$CGBStats->graph->plot = function($dataPoints, $colorName, $label, $doScale){
	global $CGBStats;
	$plot = new CGBStatsBase();
	$plot->colorName = $colorName;
	$plot->dataPoints = $dataPoints;
	$plot->scale = $doScale;
	$plot->label = $label;
	
	if(sizeof($dataPoints) > 0) array_push($CGBStats->graph->data, $plot);
};

// use SRC_OVER alpha compositing
$CGBStats->graph->drawPoint = function($x, $y, $c, $color){
	global $CGBStats;
	$im = $CGBStats->graph->im;
	$w = imagesx($im);
	$h = imagesy($im);
	
	if($x < 0 || $x > $w - 1 || $y < 0 || $y > $h - 1) return;
	
	$bgcolor = imagecolorsforindex($im, imagecolorat($im, $x, $y));
	$color = imagecolorsforindex($im, $color);
	
	$pxc = @imagecolorallocate($im,
		max(min($bgcolor['red']   * (1 - $c) + $color['red']   * ($c), 255), 0),
		max(min($bgcolor['green'] * (1 - $c) + $color['green'] * ($c), 255), 0),
		max(min($bgcolor['blue']  * (1 - $c) + $color['blue']  * ($c), 255), 0)
	);
	imagesetpixel($im, $x, $y, $pxc);
};

$CGBStats->graph->fpart = function($num){
	if($num < 0) return 1 - ($num - floor($num));
	else return $num - floor($num);
};

$CGBStats->graph->ipart = function($num){
	return intval($num);
};

$CGBStats->graph->rfpart = function($num){
	global $CGBStats;
	return 1 - $CGBStats->graph->fpart($num);
};

// apparently this has to be implemented by hand since
// the stupid Ubuntu build of PHP-GD doesn't have AA
// ported from Wikipedia
$CGBStats->graph->drawLine = function($x1, $x2, $y1, $y2, $thickness, $color){
	global $CGBStats;
	$im = $CGBStats->graph->im;
	$w = imagesx($im);
	$h = imagesy($im);
	
	$isSteep = abs($y2 - $y1) > abs($x2 - $x1);
	if($isSteep){
		$tmp = $x1;
		$x1 = $y1;
		$y1 = $tmp;
		
		$tmp = $x2;
		$x2 = $y2;
		$y2 = $tmp;
	}
	if($x1 > $x2){
		$tmp = $x1;
		$x1 = $x2;
		$x2 = $tmp;
		
		$tmp = $y1;
		$y1 = $y2;
		$y2 = $tmp;
	}
	
	$dx = $x2 - $x1;
	$dy = $y2 - $y1;
	$gradient = $dy / $dx;
	
	$xend = round($x1);
	$yend = round($y1) + $gradient * ($xend - $x1);
	$xgap = $CGBStats->graph->rfpart($x1 + 0.5);
	$xpxl1 = $xend;
	$ypxl1 = $CGBStats->graph->ipart($yend);
	if($isSteep){
		$CGBStats->graph->drawPoint($ypxl1, $xpxl1, $CGBStats->graph->rfpart($yend) * $xgap, $color);
		$CGBStats->graph->drawPoint($ypxl1+1, $xpxl1, $CGBStats->graph->fpart($yend) * $xgap, $color);
	} else {
		$CGBStats->graph->drawPoint($xpxl1, $ypxl1, $CGBStats->graph->rfpart($yend) * $xgap, $color);
		$CGBStats->graph->drawPoint($xpxl1, $ypxl1+1, $CGBStats->graph->fpart($yend) * $xgap, $color);
	}
	
	$intery = $yend + $gradient;
	
	$xend = round($x2);
	$yend = $y2 + $gradient * ($xend - $x2);
	$xgap = $CGBStats->graph->fpart($x2 + 0.5);
	$xpxl2 = $xend;
	$ypxl2 = $CGBStats->graph->ipart($yend);
	if($isSteep){
		$CGBStats->graph->drawPoint($ypxl2, $xpxl2, $CGBStats->graph->rfpart($yend) * $xgap, $color);
		$CGBStats->graph->drawPoint($ypxl2+1, $xpxl2, $CGBStats->graph->fpart($yend) * $xgap, $color);
	} else {
		$CGBStats->graph->drawPoint($xpxl2, $ypxl2, $CGBStats->graph->rfpart($yend) * $xgap, $color);
		$CGBStats->graph->drawPoint($xpxl2, $ypxl2+1, $CGBStats->graph->fpart($yend) * $xgap, $color);
	}
	
	for($x = $xpxl1 + 1; $x < $xpxl2; $x++){
		if($isSteep){
			$CGBStats->graph->drawPoint($CGBStats->graph->ipart($intery), $x, $CGBStats->graph->rfpart($intery), $color);
			$CGBStats->graph->drawPoint($CGBStats->graph->ipart($intery)+1, $x, $CGBStats->graph->fpart($intery), $color);
		} else {
			$CGBStats->graph->drawPoint($x, $CGBStats->graph->ipart($intery), $CGBStats->graph->rfpart($intery), $color);
			$CGBStats->graph->drawPoint($x, $CGBStats->graph->ipart($intery)+1, $CGBStats->graph->fpart($intery), $color);
		}
		$intery += $gradient;
	}
};

$CGBStats->graph->render = function(){
	global $CGBStats;
	
	$im = $CGBStats->graph->im;
	$font = $CGBStats->graph->config->font;
	$w = imagesx($im);
	$h = imagesy($im);
	
	$type = $CGBStats->graph->graphType;
	
	imagesetthickness($im, 1);
	
	if(sizeof($CGBStats->graph->data) === 0) {
		$color = $CGBStats->graph->colors["black"];
		$text = "No Data";
		$bbox = imagettfbbox(10, 0, $CGBStats->graph->config->font, $text);
		$fontw = abs($bbox[2] - $bbox[0]);
		$fonth = abs($bbox[1] - $bbox[7]);
		
		$outlinecolor = "lightgray";
		
		for($dx = -2; $dx <= 2; $dx++){
			for($dy = -2; $dy <= 2; $dy++){
				imagettftext($im, 10, 0, $w - $fontw - 10 + $dx, 10 + $fonth + $dy, $CGBStats->graph->colors[$outlinecolor], $CGBStats->graph->config->font, $text);
			}
		}
		imagettftext($im, 10, 0, $w - $fontw - 10, 10 + $fonth, $color, $CGBStats->graph->config->font, $text);
		return;
	}
	
	// figure out how to label
	$xMin = PHP_INT_MAX;
	$xMax = -PHP_INT_MAX - 1;
	$yMax = -PHP_INT_MAX - 1;
	$yMin = 0;
	$allowYMin = FALSE;
	
	for($i=0;$i<sizeof($CGBStats->graph->data);$i++){
		$plot = $CGBStats->graph->data[$i];
		
		foreach($plot->dataPoints as $xVal => $yVal){
			if($xVal < $xMin) $xMin = $xVal;
			if($xVal > $xMax) $xMax = $xVal;
			if($yVal > $yMax) $yMax = $yVal;
			if($yVal < $yMin) {
				$yMin = $yVal;
				$allowYMin = TRUE;
			}
		}
	}
	
	if($allowYMin) $yMin -= 1;
	
	// is custom xMax/xMin set?
	if($CGBStats->graph->xMax != -1) $xMax = $CGBStats->graph->xMax;
	if($CGBStats->graph->xMin != -1) $xMin = $CGBStats->graph->xMin;
		
	$xInterval = 1;
	
	$xCount = $CGBStats->graph->gridData['numX'];
	$xRange = $xMax - $xMin;

	$xIntervals = $CGBStats->graph->xIntervals;
	// find the smallest interval that fits
	for($j = sizeof($xIntervals) - 1; $j >= 0; $j--){
		if($xRange < $xIntervals[$j] * ($xCount - 1) // check range
			&& $xMin > intval(ceil($xMax / $xIntervals[$j]) * $xIntervals[$j]) - ($xCount - 1) * $xIntervals[$j] // check minimum
			&& $xMax <= intval(ceil($xMax / $xIntervals[$j]) * $xIntervals[$j]) // check maximum
			){
			$xInterval = $xIntervals[$j];
			break;
		}
	}
	
	if($type == "bar" || $type == "linefilled"){
		// combine data points if necessary
		for($i=0;$i<sizeof($CGBStats->graph->data);$i++){
			$plot = $CGBStats->graph->data[$i];
			$dataPoints = array();
			$lastX1 = 0;
			$lastXVal = 0;
			$lastYVal = 0;
			$startXVal = -1;
			$combined = array();
			$gridSizeX = $CGBStats->graph->config->gridSizeX;
			$gridSizeY =$CGBStats->graph->config->gridSizeY;
			$paddingX = $CGBStats->graph->config->paddingX;
			$paddingY = $CGBStats->graph->config->paddingY;
			foreach($plot->dataPoints as $xVal => $yVal){
				$x1 = ($xVal / $xInterval) * $gridSizeX;
				$startX1 = ($startXVal / $xInterval) * $gridSizeX;
				
				if($x1 - $lastX1 < 18 && ($startXVal == -1 || $x1 - $startX1 <= 36)){
					if($startXVal == -1) {
						$startXVal = $xVal;
						unset($dataPoints[$lastXVal]);
						$combined[$lastXVal] = $lastYVal;
					}
					$combined[$xVal] = $yVal;
				} else {
					if(sizeof($combined) > 0){
						$combinedX = 0; $combinedY = 0; $combinedCount = 0;
						foreach($combined as $xv2 => $yv2){
							$combinedCount++;
							$combinedX += $xv2;
							$combinedY += $yv2;
						}
						$combinedX /= $combinedCount;
						if($CGBStats->graph->combineMode == "average") $combinedY /= $combinedCount;
						if($combinedY > $yMax) $yMax = $combinedY;
						$dataPoints[$combinedX] = $combinedY;
						$combined = array();
						$startXVal = -1;
					}
					$dataPoints[$xVal] = $yVal;
				}
				$lastX1 = $x1;
				$lastXVal = $xVal;
				$lastYVal = $yVal;
			}
			if(sizeof($combined) > 0){
				$combinedX = 0; $combinedY = 0; $combinedCount = 0;
				foreach($combined as $xv2 => $yv2){
					$combinedCount++;
					$combinedX += $xv2;
					$combinedY += $yv2;
				}
				$combinedX /= $combinedCount;
				if($CGBStats->graph->combineMode == "average") $combinedY /= $combinedCount;
				if($combinedY > $yMax) $yMax = $combinedY;
				$dataPoints[$combinedX] = $combinedY;
				$combined = array();
				$startXVal = -1;
			}
			$CGBStats->graph->data[$i]->dataPoints = $dataPoints;
		}
	}

	$yInterval = 1;
	
	$yCount = $CGBStats->graph->gridData['numY'];
	$yRange = $yMax - $yMin;
	
	$yIntervals = $CGBStats->graph->yIntervals;
	
	// find the smallest interval that fits
	for($j = sizeof($yIntervals) - 1; $j >= 0; $j--){
		if($yRange < $yIntervals[$j] * ($yCount - 1) // check range
			&& (!$allowYMin || $yMin > intval(ceil($yMax / $yIntervals[$j]) * $yIntervals[$j]) - ($yCount - 1) * $yIntervals[$j]) // check minimum
			&& $yMax <= $yMin + ($yCount - 1) * $yIntervals[$j] // check max
			// min is forced to 0 so it should be ok
			){
			$yInterval = $yIntervals[$j];
			break;
		}
	}
	
	// set corrected intervals for the graph scale
	if(!$allowYMin) $yMin = 0;
	$yMax = $yMin + ($yCount - 1) * $yInterval;
	if($CGBStats->graph->snapX === "max"){
		$xMax = intval(ceil($xMax / $xInterval) * $xInterval);
		$xMin = $xMax - ($xCount - 1) * $xInterval;
	} else {
		$xMin = intval(floor($xMin / $xInterval) * $xInterval);
		$xMax = $xMax + ($xCount - 1) * $xInterval;
	}
	
	// draw y axis labels
	for($yl = $yMin, $ylp = 0; $yl <= $yMax; $yl += $yInterval, $ylp++){
		$yLabel = $CGBStats->graph->labelFunctionY($yl);
		$bbox = imagettfbbox(8, 0, $CGBStats->graph->config->font, $yLabel);
		$fontw = abs($bbox[2] - $bbox[0]);
		$fonth = abs($bbox[1] - $bbox[7]);
		imagettftext($im, 8, 0, $CGBStats->graph->config->paddingX - $fontw, $h - (($ylp * $CGBStats->graph->config->gridSizeY) + $CGBStats->graph->config->paddingY - ($fonth / 2)), $CGBStats->graph->colors['gray'], $CGBStats->graph->config->font, $yLabel);
	}
	
	// draw x axis labels
	for($xl = $xMin, $xlp = 0; $xl <= $xMax; $xl += $xInterval, $xlp++){
		$xLabel = $CGBStats->graph->labelFunctionX($xl);
		
		$xLabelParts = explode("\n", $xLabel);
		$xLabel = $xLabelParts[0];
		
		$bbox = imagettfbbox(8, 0, $CGBStats->graph->config->font, $xLabel);
		$fontw = abs($bbox[2] - $bbox[0]);
		$fonth = abs($bbox[1] - $bbox[7]);
		imagettftext($im, 8, 0, ($xlp * $CGBStats->graph->config->gridSizeX) + $CGBStats->graph->config->paddingX - ($fontw / 2), $h - $CGBStats->graph->config->paddingY + ($fonth * 1.5), $CGBStats->graph->colors['gray'], $CGBStats->graph->config->font, $xLabel);
		
		if(sizeof($xLabelParts) > 1){
			$extra = $xLabelParts[1];
			
			$bbox = imagettfbbox(8, 0, $CGBStats->graph->config->font, $extra);
			$fontw = abs($bbox[2] - $bbox[0]);
			$fonth = abs($bbox[1] - $bbox[7]);
			imagettftext($im, 8, 0, ($xlp * $CGBStats->graph->config->gridSizeX) + $CGBStats->graph->config->paddingX - ($fontw / 2), $h - $CGBStats->graph->config->paddingY + ($fonth * 2.5), $CGBStats->graph->colors['gray'], $CGBStats->graph->config->font, $extra);
		}
	}
	
	// graph the data
	for($i=0;$i<sizeof($CGBStats->graph->data);$i++){
		$plot = $CGBStats->graph->data[$i];
		$color = $CGBStats->graph->colors[$plot->colorName];
		$lastXVal = $xMin;
		$lastYVal = $yMin;
		
		$gridSizeX = $CGBStats->graph->config->gridSizeX;
		$gridSizeY =$CGBStats->graph->config->gridSizeY;
		$paddingX = $CGBStats->graph->config->paddingX;
		$paddingY = $CGBStats->graph->config->paddingY;
		
		$doScale = $plot->scale;
		//if($CGBStats->isDev()){var_dump($plot);var_dump($doScale);}
		$label = $plot->label;
		
		if($doScale){
			// figure out how much to scale by
			$scaleBy = 1;
			
			$valMax = -PHP_INT_MAX - 1;
			foreach($plot->dataPoints as $xVal => $yVal){
				if($yVal > $valMax) $valMax = $yVal;
			}
			$val = (($valMax - $yMin) / $yInterval);
			$mul = 2;
			while($val * $scaleBy < ($yCount - 1)){
				if($mul == 5) $mul = 2;
				else $mul = 5;
				$scaleBy *= $mul;
			}
			
			$scaleBy /= $mul;
			
			//var_dump(array($valMax, $val, $xCount, $xMin, $xMax, $scaleBy));
			
			// do the scaling
			foreach($plot->dataPoints as $xVal => $yVal){
				$plot->dataPoints[$xVal] = $yVal * $scaleBy;
			}
			
			$plot->scaleBy = $scaleBy;
			// if($CGBStats->isDev()) var_dump($plot);
		}
		
		$isFirst = TRUE;
		foreach($plot->dataPoints as $xVal => $yVal){
			$x1 = ((($xVal - $xMin) / $xInterval) * $gridSizeX) + $paddingX;
			$y1 = $h - (((($yVal - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
			
			if($type == "line") imagefilledrectangle($im, $x1 - 3, $y1 - 3, $x1 + 3, $y1 + 3, $color);
			else if($type == "bar") {
				$xOffset = $i * 6;
				$totalOffset = sizeof($CGBStats->graph->data) * 6;
				$xOffset -= $totalOffset / 2;
				
				$y2 = $h - $paddingY - 1;
				if($allowYMin) $y2 = $h - ((((0 - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
				imagefilledrectangle($im, $x1 + $xOffset, $y1, $x1 + $xOffset + 5, $y2, $color);
			} else if($type == "linefilled"){
				if(!$isFirst){
					$fy2 = $h - (((($lastYVal - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
					$fxmin = floor(((($lastXVal - $xMin) / $xInterval) * $gridSizeX) + $paddingX);
					$fxmax = floor($x1);
					
					imagesetthickness($im, 1);
					for($ix = $fxmin + ($i * 2) + 1; $ix < $fxmax + ($i * 2) + 1; $ix+=sizeof($CGBStats->graph->data) * 2){
						imageline($im, $ix, $h - $paddingY, $ix, $fy2 + (($y1 - $fy2) * ($ix - $fxmin) / ($fxmax - $fxmin)), $color);
					}
				}
			}
			
			if($isFirst){
				$isFirst = FALSE;
				$lastXVal = $xVal;
				$lastYVal = $yVal;
				continue;
			}
			
			$x2 = ((($lastXVal - $xMin) / $xInterval) * $gridSizeX) + $paddingX;
			$y2 = $h - (((($lastYVal - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
			
			if($type == "line" || $type == "linefilled"){
				imagesetthickness($im, 3);
				imageline($im, $x1, $y1, $x2, $y2, $color);
				$CGBStats->graph->drawLine($x1, $x2, $y1 + 1, $y2 + 1, 1, $color);
				$CGBStats->graph->drawLine($x1, $x2, $y1 - 2, $y2 - 2, 1, $color);
			}
			
			$lastXVal = $xVal;
			$lastYVal = $yVal;
		}
		
		// render the title
		imagettftext($im, 15, 0, $CGBStats->graph->config->paddingX + 12, 22, $CGBStats->graph->colors['gray'], $CGBStats->graph->config->font, $CGBStats->graph->title);
		imagettftext($im, 15, 0, $CGBStats->graph->config->paddingX + 10, 20, $CGBStats->graph->colors['black'], $CGBStats->graph->config->font, $CGBStats->graph->title);
	}
	
	// draw a line for 0 if necessary
	if($allowYMin){
		$yLevel = $h - ((((0 - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
		imageline($im, $paddingX, $yLevel, $w - 1, $yLevel, $CGBStats->graph->colors['black']);
	}
	
	$legendY = 10;
	// mark legend and scaled values
	for($i=0;$i<sizeof($CGBStats->graph->data);$i++){
		$plot = $CGBStats->graph->data[$i];
		$color = $CGBStats->graph->colors[$plot->colorName];
		$lastXVal = $xMin;
		$lastYVal = $yMin;
		
		$gridSizeX = $CGBStats->graph->config->gridSizeX;
		$gridSizeY =$CGBStats->graph->config->gridSizeY;
		$paddingX = $CGBStats->graph->config->paddingX;
		$paddingY = $CGBStats->graph->config->paddingY;
		
		$doScale = $plot->scale;
		//if($doScale && $CGBStats->isDev()) var_dump($plot);
		$label = $plot->label;
		
		if($doScale){
			// mark actual values
			$lastX1 = 0;
			$lastY1 = 0;
			foreach($plot->dataPoints as $xVal => $yVal){
				$x1 = ((($xVal - $xMin) / $xInterval) * $gridSizeX) + $paddingX;
				$y1 = $h - (((($yVal - $yMin) / $yInterval) * $gridSizeY) + $paddingY);
				
				$unscaled = $yVal / $plot->scaleBy;
				$text =  $CGBStats->graph->labelFunctionY($unscaled);
				
				$bbox = imagettfbbox(8, 0, $CGBStats->graph->config->font, $text);
				$fontw = abs($bbox[2] - $bbox[0]);
				$fonth = abs($bbox[1] - $bbox[7]);
				
				if($x1 <= $lastX1 && $y1 >= $lastY1 - $fonth && $y1 <= $lastY1){
					continue;
				} else {
					$lastX1 = $x1 + $fontw;
					$lastY1 = $y1;
				}
				
				if($y1 > 20){
					for($dx = -1; $dx <= 1; $dx++){
						for($dy = -1; $dy <= 1; $dy++){
							imagettftext($im, 5, 0, $x1 + $dx - 5, $y1 - 5 + $dx,$CGBStats->graph->colors['lightgray'], $CGBStats->graph->config->font, $text);
						}
					}
					imagettftext($im, 5, 0, $x1 - 5, $y1 - 5, $color, $CGBStats->graph->config->font, $text);
				} else {
					for($dx = -1; $dx <= 1; $dx++){
						for($dy = -1; $dy <= 1; $dy++){
							imagettftext($im, 5, 0, $x1 + $dx - 5, $y1 + 15 + $dx,$CGBStats->graph->colors['lightgray'], $CGBStats->graph->config->font, $text);
						}
					}
					imagettftext($im, 5, 0, $x1 - 5, $y1 + 15, $color, $CGBStats->graph->config->font, $text);
				}
			}
		}
	}
	
	for($i=0;$i<sizeof($CGBStats->graph->data);$i++){
		$plot = $CGBStats->graph->data[$i];
		$color = $CGBStats->graph->colors[$plot->colorName];
		$lastXVal = $xMin;
		$lastYVal = $yMin;
		
		$gridSizeX = $CGBStats->graph->config->gridSizeX;
		$gridSizeY =$CGBStats->graph->config->gridSizeY;
		$paddingX = $CGBStats->graph->config->paddingX;
		$paddingY = $CGBStats->graph->config->paddingY;
		
		$doScale = $plot->scale;
		if(isset($plot->scaleBy)) $scaleBy = $plot->scaleBy;
		else $scaleBy = 1;
		$label = $plot->label;
		
		if($doScale){
			// mark legend
			$bbox = imagettfbbox(10, 0, $CGBStats->graph->config->font, $label . "x" . $scaleBy);
			$fontw = abs($bbox[2] - $bbox[0]);
			$fonth = abs($bbox[1] - $bbox[7]);
			
			for($dx = -2; $dx <= 2; $dx++){
				for($dy = -2; $dy <= 2; $dy++){
					imagettftext($im, 10, 0, $w - $fontw - 10 + $dx, $legendY + $fonth + $dy, $CGBStats->graph->colors['lightgray'], $CGBStats->graph->config->font, $label . "x" . $scaleBy);
				}
			}
			imagettftext($im, 10, 0, $w - $fontw - 10, $legendY + $fonth, $color, $CGBStats->graph->config->font, $label . "x" . $scaleBy);
			
			$legendY += $fonth + 10;
		} else {
			// mark legend
			$bbox = imagettfbbox(10, 0, $CGBStats->graph->config->font, $label);
			$fontw = abs($bbox[2] - $bbox[0]);
			$fonth = abs($bbox[1] - $bbox[7]);
			
			$outlinecolor = "lightgray";
			if($color === $CGBStats->graph->colors["gold"] /*|| $color === $CGBStats->graph->colors["elixir"]*/) $outlinecolor = "darkgray";
			
			for($dx = -2; $dx <= 2; $dx++){
				for($dy = -2; $dy <= 2; $dy++){
					imagettftext($im, 10, 0, $w - $fontw - 10 + $dx, $legendY + $fonth + $dy, $CGBStats->graph->colors[$outlinecolor], $CGBStats->graph->config->font, $label);
				}
			}
			imagettftext($im, 10, 0, $w - $fontw - 10, $legendY + $fonth, $color, $CGBStats->graph->config->font, $label);
			
			$legendY += $fonth + 10;
		}
	}
	
	if($CGBStats->graph->startTime > 0){
		$color = $CGBStats->graph->colors["black"];
		$text = (time() - $CGBStats->graph->startTime) . " secs";
		$bbox = imagettfbbox(10, 0, $CGBStats->graph->config->font, $text);
		$fontw = abs($bbox[2] - $bbox[0]);
		$fonth = abs($bbox[1] - $bbox[7]);
		
		$outlinecolor = "lightgray";
		if($color === $CGBStats->graph->colors["gold"] /*|| $color === $CGBStats->graph->colors["elixir"]*/) $outlinecolor = "darkgray";
		
		for($dx = -2; $dx <= 2; $dx++){
			for($dy = -2; $dy <= 2; $dy++){
				imagettftext($im, 10, 0, $w - $fontw - 10 + $dx, $legendY + $fonth + $dy, $CGBStats->graph->colors[$outlinecolor], $CGBStats->graph->config->font, $text);
			}
		}
		imagettftext($im, 10, 0, $w - $fontw - 10, $legendY + $fonth, $color, $CGBStats->graph->config->font, $text);
	}
};
?>