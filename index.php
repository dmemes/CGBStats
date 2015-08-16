<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
	
	$uri = $_SERVER['REQUEST_URI'];
	if($uri == "/" || $uri == "/index.php"){
		$uri = "global";
	} else {
		$uri = substr($uri, 1);
		if(strpos($uri, "?") > -1) $uri = substr($uri, 0, strpos($uri, "?"));
	}
	
	// lol, no ../ hackers today
	$uri = preg_replace("/[\.\/]+/", "", $uri);
	
	if(file_exists("templates/" . $uri . ".php")){
		$mtime1 = filemtime(__FILE__);
		$mtime2 = filemtime("templates/" . $uri . ".php");
		
		$CGBStats->enableCachingWithData(max($mtime1, $mtime2), $_SERVER['REQUEST_URI']);
	} else {
		$CGBStats->enableCachingWithData(filemtime(__FILE__), $_SERVER['REQUEST_URI']);
	}
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>CGBStats.cf</title>
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="stylesheet" href="/static/cgbstats.css" />
</head>
<body>
	<?php if(!isset($_COOKIE['CGBStatsCookieBanner'])){ ?>
	<div class='cookie-banner'>
		CGBStats uses cookies. The <a href="javascript:void(0)" data-href="/about">privacy policy</a> outlines CGBStats' use of cookies. By using this site, you are agreeing to CGBStats' use of cookies. <button class='cookie-banner-dismiss'>Dismiss</button>
	</div>
	<?php } ?>
	<div class="tabview">
		<div class="tab-header">
			<div class='scrollbar'><div class='scrollbar-track'><div class='scrollbar-thumb'></div></div></div>
			<div class='tab-header-inner'>
				<a class="header" href="javascript:void(0)" data-href="/about">CGBStats.cf</a>
				<a class="tab selected" href="javascript:void(0)" data-href="/global">Global Stats</a>
				<a class="tab login-required" href="javascript:void(0)" data-href="/mystats">My Stats</a>
				<a class="tab login-required" href="javascript:void(0)" data-href="/mylogs">My Bot Logs</a>
				<a class="tab" href="javascript:void(0)" data-href="/finduser">Find a user</a>
				<a class="tab account-tab" href="javascript:void(0)" data-href="/signup">Join</a>
			</div>
		</div>
		<script src="/static/vendor/jquery/jquery-2.1.4.min.js"></script>
		<script src="/static/vendor/jailed/jailed.js"></script>
		<script src="/static/cgbstats.js"></script>
		<script src="/static/vendor/jstz/jstz-1.0.4.min.js"></script>
		<script>
			// detect timezone, or at least try to
			var tz = jstz.determine();
			var name = tz.name();
			document.cookie="cgbstz=" + name + "; path=/; domain=.<?php echo $CGBStats->config->domain; ?>; expires=" + new Date(Date.now() + 1000 * 3600 * 24 * 365 * 20);
		</script>
		<div class="tab-content">
			<noscript>
				<h1 style="color:red;background:rgba(255, 0, 0, 0.5);border: 1px solid red; border-radius: 1em;text-align: center;padding: 1em;">You need JavaScript enabled to use this site.</h1>
			</noscript>
			<?php
				if(file_exists("templates/" . $uri . ".php")){
					include "templates/" . $uri . ".php";
				} else { ?>
					<div class='container'><h1>Page not found</h1><p>That page does not exist.<br/><span style='font-size: 0.8em; color: gray;'>HTTP code 404</span></p></div>
				<?php 
				}
			?>
		</div>
		<script>
			CGBStats.nav.onPageLoaded();
		</script>
	</div>
</body>
</html>