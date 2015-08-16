<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
$CGBStats->enableCachingWithData(filemtime(__FILE__), isset($_GET['userid']) ? $_GET['userid'] : "");
?>
<div class="container">
	<?php
	if(isset($_GET['userid'])){
	$statsuserid = intval($_GET['userid']);
	
	$res = $CGBStats->database->query("SELECT * FROM `cgbstats_user` WHERE `userid`=?", array($statsuserid));
	if(sizeof($res) > 0){
		$statsusername = $CGBStats->database->query("SELECT `username` FROM `cgbstats_user` WHERE `userid`=?", array($statsuserid))[0]['username'];
	?>
	<h1 class='stats-header'>Stats for <?php echo htmlspecialchars($statsusername); ?> <span class='info-userid'>(User #<?php echo $statsuserid; ?>)</span></h1>
	<script>
		// MEGA hacks
		setTimeout(function(){
			$(".tab.selected .tab-name").text(<?php echo json_encode($statsusername); ?> + " ");
			CGBStats.persist.addTab(CGBStats.nav.getPage() + CGBStats.nav.getQuery(), <?php echo json_encode($statsusername); ?>);
		}, 1000);
	</script>
	<?php 
	$displaychart_param_user = $statsuserid;
	include 'displaychart.php'; ?>
	
	<?php 
	$customquery_param_user = $statsuserid;
	include 'customquery.php'; ?>
	
	<?php } else { ?>
	<h1 class='stats-header'>Stats for user #<?php echo $statsuserid; ?></h1>
	<p>No such user</p>
	<?php } ?>
	<?php } else { ?>
	<h1 class='stats-header'>Error</h1>
	<p>You need to specify a user id</p>
	<?php } ?>
</div>
<style>
	.info-userid {
		font-size: 0.6em;
	}
</style>