<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
$CGBStats->enableCaching(filemtime(__FILE__));
?>
<div class="container">
	<p>CGBStats.cf is a site for recording and displaying your Clash Game Bot data. You can get persistent statistics and graphs for your bot, and even generate dynamic graphs for your CGB forums signature. Interested? <a href="javascript: void(0);" data-href="/signup">Join CGBStats</a>.</p>
	<hr />
	<p>These are graphs of combined data from all CGBStats users for the last 48 hours (a bit like Clash Forecaster).</p>
	<h2 class='category-header'>Display: <select class='category-select'></select></h2>
	<div class='dual-graph'>
		<div class='graph graph1 global-0'>
			<img class='graph-loading' src="/static/loading21.gif" />
			<img class='graph-img' />
			<p class="error">Graph failed to load</p>
		</div>
		<div class='graph graph2 global-1'>
			<img class='graph-loading' src="/static/loading21.gif" />
			<img class='graph-img' />
			<p class="error">Graph failed to load</p>
		</div>
	</div>
	<div class='dual-graph'>
		<div class='graph graph1 global-2'>
			<img class='graph-loading' src="/static/loading21.gif" />
			<img class='graph-img' />
			<p class="error">Graph failed to load</p>
		</div>
		<div class='graph graph2 global-3'>
			<img class='graph-loading' src="/static/loading21.gif" />
			<img class='graph-img' />
			<p class="error">Graph failed to load</p>
		</div>
	</div>
	<hr />
	<h2>Leaderboard</h2>
	<p>Display top raids in the last 48 hours for: <select class='leaderboard-select'>
		<option value="gold">Gold</option>
		<option value="elix">Elixir</option>
		<option value="delix">Dark Elixir</option>
		<option value="trophy">Trophies</option>
	</select></p>
	<div class="leaderboard">
		<div class="l-header">
			<div href="javascript:void(0);" class="lh-username">Username</div>
			<a href="javascript:void(0);" class="lh-gold order osel" data-sel="gold">Gold</a>
			<a href="javascript:void(0);" class="lh-elix order" data-sel='elix'>Elixir</a>
			<a href="javascript:void(0);" class="lh-delix order" data-sel="delix">Dark Elixir</a>
			<a href="javascript:void(0);" class="lh-trophy order" data-sel="trophy">Trophies</a>
		</div>
	</div>
</div>
<style>
	.leaderboard {
		display: table;
		border-collapse: collapse;
		border: 1px solid black;
		font-size: 1.2em;
		margin-bottom: 5em;
		background: rgba(255, 255, 255, 0.5);
		width: 100%;
	}
	
	.osel::after {
		content: "▼";
	}
	
	.order {
		transition: all 0.2s;
		text-decoration: none !important;
		color: black !important;
	}
	
	.order:hover {
		background: rgba(255, 255, 255, 0.7);
	}
	
	.l-header, .l-row{
		display: table-row;
		border: 1px solid black;
	}
	
	.l-header > div, .l-header > a {
		display: table-cell;
		font-weight: bold;
		border: 1px solid black;
		padding: 5px;
	}
	
	.l-row > div {
		display: table-cell;
		padding-left: 20px;
		padding-right: 20px;
		padding-top: 5px;
		padding-bottom: 5px;
		border: 1px solid black;
		
		background-size: 16px 16px;
		background-position: 2px 50%;
		background-repeat: no-repeat;
	}
	
	.l-gold {
		background-image: url(/static/gold.ico);
	}
	
	.l-elix {
		background-image: url(/static/elix.ico);
	}
	
	.l-delix {
		background-image: url(/static/delix.ico);
	}
	
	.l-trophy {
		background-image: url(/static/trophy.ico);
	}
	
	.container {
		width: calc(100% - 100px);
	}
	
	.category-select, .leaderboard-select {
		font-size: 1em;
		background: rgba(255, 255, 255, 0.5);
		border: 1px solid black;
	}
	
	.dual-graph {
		display: table;
		table-layout: fixed;
		width: calc(100% - 50px);
	}
	
	.dual-graph .graph {
		display: table-cell;
		vertical-align: top;
		text-align: center;
		width: 50%;
		min-width: 50%;
		max-width: 50%;
		padding-right: 0.2em;
		padding-left: 0.2em;
	}
	
	.dual-graph .graph.graph1 {
		padding-right: 0.1em;
	}
	
	.dual-graph .graph.graph2 {
		padding-left: 0.1em;
	}
	
	.graph-img {
		max-width: 100%;
		display: none;
	}
	
	.graph .error {
		display: none;
		color: red;
	}
	
	.graph-loading {
		max-width: 100%;
	}
</style>
<script>
	CGBStats.global = {};
	CGBStats.global.graphTypes = <?php echo json_encode($CGBStats->images->globalCategories); ?>;
	for(var type in CGBStats.global.graphTypes){
		if(CGBStats.global.graphTypes.hasOwnProperty(type)){
			var option = $("<option>");
			option.attr("value", type);
			option.text(type);
			$(".category-select").append(option.clone());
		}
	}
	
	if(typeof CGBStats.persist.globalCache === 'undefined') CGBStats.persist.globalCache = {time: Math.floor(Date.now() / 1000)};
	
	CGBStats.global.display = function(data){
		for(var i = 0; i < 4; i++){
			$(".global-" + i + " .graph-img").attr("src", data[i]);
		}
		$(".graph-loading").hide();
		$(".graph-img").show();
	};
	
	CGBStats.global.render = function(){
		$(".graph-img, .graph .error").hide();
		$(".graph-loading").show();
		var category = $(".category-select").val();
		if(CGBStats.compat.localStorage) localStorage.CGBGlobal = $(".category-select").val();
		
		if(typeof CGBStats.persist.globalCache[category] !== 'undefined' && Math.floor(Date.now() / 1000) - CGBStats.persist.globalCache.time < 300){
			CGBStats.global.display(CGBStats.persist.globalCache[category]);
			return;
		} else if(Math.floor(Date.now() / 1000) - CGBStats.persist.globalCache.time > 300) {
			CGBStats.persist.globalCache = {time: Math.floor(Date.now() / 1000)};
		}
		
		$.get("/api/render.php", {global: "true", category: category}, function(data){
			if(data.constructor === Array && data.length == 4){
				CGBStats.persist.globalCache[category] = data;
				CGBStats.global.display(data);
			} else {
				$(".graph .error").show();
				$(".graph-loading").hide();
			}
		}).fail(function(){
			$(".graph .error").show();
			$(".graph-loading").hide();
		});
	};
	
	if(CGBStats.compat.localStorage && typeof localStorage.CGBGlobal !== 'undefined')
		$(".category-select").val(localStorage.CGBGlobal);
	CGBStats.global.render();
	
	$(".category-select").on("change", CGBStats.global.render);
	
	
	CGBStats.global.updateLeaderboard = function(){
		$(".l-row").remove();
		var type = $(".leaderboard-select").val();
		if(CGBStats.compat.localStorage) localStorage.CGBLeaderboard = $(".leaderboard-select").val();
		
		function lFail(){
			var row = $("<div>").addClass("l-row");
			row.text("Failed to load leaderboard");
			row.appendTo($(".leaderboard"));
		}
		
		$.get("/api/leaderboard.php?type=" + type, function(data){
			if(typeof data === 'undefined' || data === null || data.constructor !== Array){
				lFail();
				return;
			}
			
			$(".osel").removeClass("osel");
			$(".lh-" + type).addClass("osel");
			
			for(var i = 0; i < data.length; i++){
				var row = $("<div>").addClass("l-row");
				
				var userlink = $("<a>").attr("href", "javascript:void(0)").attr("data-userid", data[i].userid + "").click(function(){
					CGBStats.nav.setLocation("/userstats?userid=" + $(this).attr("data-userid"));
				}).text(data[i].username);
				
				row.append($("<div>").addClass("l-username").append(userlink));
				row.append($("<div>").addClass("l-gold").text(data[i].gold));
				row.append($("<div>").addClass("l-elix").text(data[i].elix));
				row.append($("<div>").addClass("l-delix").text(data[i].delix));
				row.append($("<div>").addClass("l-trophy").text(data[i].trophy));
				row.appendTo($(".leaderboard"));
			}
			
			$(".leaderboard").css("min-height", $(".leaderboard").height() + "px");
		}).fail(lFail);
	};
	
	if(CGBStats.compat.localStorage && typeof localStorage.CGBLeaderboard !== 'undefined')
		$(".leaderboard-select").val(localStorage.CGBLeaderboard);
	
	CGBStats.global.updateLeaderboard();
	$(".leaderboard-select").on("change", CGBStats.global.updateLeaderboard);
	
	$(".order").click(function(){
		$(".leaderboard-select").val($(this).attr("data-sel"));
		CGBStats.global.updateLeaderboard();
	});
</script>