<div class='dual-graph'>
	<div class='graph graph1'>
		<select class='graph-type-select' data-for="graph1"></select>
		<img class='graph-loading' src="/static/loading21.gif" />
		<img class='graph-img' />
		<p class="error">Graph failed to load</p>
	</div>
	<div class='graph graph2'>
		<select class='graph-type-select' data-for="graph2"></select>
		<img class='graph-loading' src="/static/loading21.gif" />
		<img class='graph-img' />
		<p class="error">Graph failed to load</p>
	</div>
	<div class='graph-footer'>
		<label for="graph-interval">Stats from the past</label>
		<input type="number" id="graph-interval" name="graph-interval" value="2" min="1" max="30" step="1" />
		<label for="graph-interval">day(s)</label>
		<span>&nbsp;</span>
		<span>&nbsp;<button class='refresh'>Refresh</button></span><br/><hr/>
		<h3 class='stats-values-header'>Stats</h3>
		<div class='stats-values'>
			<div class='section'>
				<h4>Raid Averages</h4>
				<img class='stat-icon gold' src="/static/gold.ico" /> <span class='stat-value avg-gold'></span> gold<br/>
				<img class='stat-icon elix' src="/static/elix.ico" /> <span class='stat-value avg-elix'></span> elixir<br/>
				<img class='stat-icon delix' src="/static/delix.ico" /> <span class='stat-value avg-delix'></span> dark elixir<br/>
				<img class='stat-icon trophy' src="/static/trophy.ico" /> <span class='stat-value avg-trophy'></span> <span class='plural'>trophies</span><span class='singular'>trophy</span><br/>
				<img class='stat-icon stars' src="/static/stars.ico" /> <span class='stat-value avg-stars'></span> <span class='plural'>stars</span><span class='singular'>star</span><br/>
				<img class='stat-icon search' src="/static/search.png" /> <span class='stat-value avg-search'></span> <span class='plural'>skips</span><span class='singular'>skip</span>
			</div>
			<div class='section'>
				<h4>Hourly Stats</h4>
				<img class='stat-icon gold' src="/static/gold.ico" /> <span class='stat-value hourly-gold'></span> gold/hr<br/>
				<img class='stat-icon elix' src="/static/elix.ico" /> <span class='stat-value hourly-elix'></span> elixir/hr<br/>
				<img class='stat-icon delix' src="/static/delix.ico" /> <span class='stat-value hourly-delix'></span> dark elixir/hr<br/>
				<img class='stat-icon trophy' src="/static/trophy.ico" /> <span class='stat-value hourly-trophy'></span> <span class='plural'>trophies</span><span class='singular'>trophy</span>/hr<br/>
				<img class='stat-icon attack' src="/static/attack.png" /> <span class='stat-value hourly-raid'></span> <span class='plural'>raids</span><span class='singular'>raid</span>/hr<br/>
				<img class='stat-icon search' src="/static/search.png" /> <span class='stat-value hourly-search'></span> <span class='plural'>skips</span><span class='singular'>skip</span>/hr<br/>
			</div>
			<div class='section'>
				<h4>Raid Totals</h4>
				<img class='stat-icon gold' src="/static/gold.ico" /> <span class='stat-value total-gold'></span> gold<br/>
				<img class='stat-icon elix' src="/static/elix.ico" /> <span class='stat-value total-elix'></span> elixir<br/>
				<img class='stat-icon delix' src="/static/delix.ico" /> <span class='stat-value total-delix'></span> dark elixir<br/>
				<img class='stat-icon trophy' src="/static/trophy.ico" /> <span class='stat-value total-trophy'></span> <span class='plural'>trophies</span><span class='singular'>trophy</span><br/>
				<img class='stat-icon attack' src="/static/attack.png" /> <span class='stat-value total-raid'></span> <span class='plural'>raids</span><span class='singular'>raid</span><br/>
				<img class='stat-icon search' src="/static/search.png" /> <span class='stat-value total-search'></span> <span class='plural'>skips</span><span class='singular'>skip</span>
			</div>
			<div class='section'>
				<h4>Raid Records</h4>
				<img class='stat-icon gold' src="/static/gold.ico" /> <span class='stat-value most-gold'></span> gold<br/>
				<img class='stat-icon elix' src="/static/elix.ico" /> <span class='stat-value most-elix'></span> elixir<br/>
				<img class='stat-icon delix' src="/static/delix.ico" /> <span class='stat-value most-delix'></span> dark elixir<br/>
				<img class='stat-icon trophy' src="/static/trophy.ico" /> <span class='stat-value most-trophy'></span> <span class='plural'>trophies</span><span class='singular'>trophy</span><br/>
				<img class='stat-icon stars' src="/static/stars.ico" /> <span class='stat-value most-stars'></span> <span class='plural'>stars</span><span class='singular'>star</span><br/>
				<img class='stat-icon search' src="/static/search.png" /> <span class='stat-value most-search'></span> <span class='plural'>skips</span><span class='singular'>skip</span>
			</div>
			<div class='stats-loading'>
				<img src="/static/loading.gif" />
			</div>
			<hr />
		</div>
	</div>
</div>

<style>
	.stats-values .singular { display: none; }
	
	.stat-icon {
		width: 16px;
		height: 16px;
	}
	
	.stats-values {
		text-align: center;
		position: relative;
	}
	
	.stats-values .stats-loading {
		text-align: center;
		position: absolute;
		top: -30%;
		left: 0px;
		width: 100%;
	}
	
	.stats-values .stats-loading .img {
		height: 100px;
	}
	
	.stats-values .section {
		display: inline-block;
		text-align: left;
		padding-left: 2em;
		padding-right: 2em;
		
		opacity: 0;
		
		border-left: 2px solid white;
	}
	
	.stats-values .section  h4 {
		margin: 0;
	}
	
	.stats-values .section:last-child {
		border-right: 2px solid white;
	}
	
	.stat-value {
		font-weight: bold;
	}
	
	.dual-graph {
		display: table;
		table-layout: fixed;
		border-top: 1px solid gray;
		border-bottom: 1px solid gray;
		width: calc(100% - 50px);
		
		background: rgba(0, 0, 0, 0.5);
		border-top-right-radius: 0.5em;
		border-top-left-radius: 0.5em;
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
	
	.graph-type-select {
		width: 100%;
	}
	
	.graph-img {
		max-width: 100%;
		display: none;
	}
	
	.graph-loading {
		max-width: 100%;
	}
	
	.graph .error {
		display: none;
		color: red;
	}
	
	.graph-footer {
		text-align: center;
		caption-side: bottom;
		display: table-caption;
		color: white;
		background: rgba(0, 0, 0, 0.5);
		border-bottom-right-radius: 0.5em;
		border-bottom-left-radius: 0.5em;
	}
	
	#graph-interval {
		width: 3em;
		color: black;
	}
</style>

<script>
	CGBStats.graphs = {
		types: <?php echo json_encode($CGBStats->images->types); ?>,
		render: function(force){
			$(".graph-type-select").each(function(){
				var graph = $(this).attr("data-for");
				var type = $(this).val();
				var url = "/api/render.php?dataurl=true&days=" + $("#graph-interval").val() + "&type=" + type<?php if(isset($displaychart_param_user) && $displaychart_param_user > -1) echo " + '&user=" . $displaychart_param_user . "'"; ?>;
				var loadFunc = (function(type, graph){
					<?php if(!isset($displaychart_param_user) || $displaychart_param_user === -1){ ?>
					CGBStats.account.getUserId(function(userid){
						if(userid === false){
							CGBStats.persist.sessionExpired = true;
							CGBStats.nav.setLocation("/mystats");
						}
					});
					<?php } ?>
					$(".graph." + graph + " .graph-img").hide();
					$(".graph." + graph + " .error").hide();
					$(".graph." + graph + " .graph-loading").show();
					
					$.get(url, function(data){
						$(".graph." + graph + " .graph-img").attr("src", data).fadeIn(200);
						$(".graph." + graph + " .graph-loading").hide();
						$(".graph." + graph).attr("data-type", type);
					}).fail(function(){
						$(".graph." + graph + " .error").show();
						$(".graph." + graph + " .graph-loading").hide();
					});
				});
				
				if($(".graph." + graph).attr("data-type") !== type) loadFunc(type, graph);
				else if(arguments.length > 0 && force) loadFunc(type, graph);
			});
			
			$(".stats-values .section").css({opacity:0});
			$(".stats-values .stats-loading").show();
			
			$.get("/api/stats.php?days="+ $("#graph-interval").val()<?php if(isset($displaychart_param_user) && $displaychart_param_user > -1) echo " + '&user=" . $displaychart_param_user . "'"; ?>, function(data){
				for(key in data){
					$(".stats-values ." + key).text(data[key]);
					if(key.endsWith("trophy") || key.endsWith("raid") || key.endsWith("search") || key.endsWith("stars")){
						if(data[key] == "1"){
							$(".stats-values ." + key).next().hide();
							$(".stats-values ." + key).next().next().show();
						} else {
							$(".stats-values ." + key).next().show();
							$(".stats-values ." + key).next().next().hide();
						}
					}
				}
				$(".stats-values .section").animate({opacity:1});
				$(".stats-values .stats-loading").fadeOut();
			}).fail(function(){
				$(".stats-values-header").text("Stats failed to load!");
			});
		}
	};
	
	for(var type in CGBStats.graphs.types){
		if(CGBStats.graphs.types.hasOwnProperty(type)){
			var option = $("<option>");
			option.attr("value", type);
			option.text(CGBStats.graphs.types[type]);
			$(".graph-type-select").each(function(){
				$(this).append(option.clone());
			});
		}
	}
	
	var g1 = 'LOOT_VS_TIME';
	var g2 = 'TOTAL_VS_TIME';
	var days = 2;
	if(CGBStats.compat.localStorage){
		if(typeof localStorage.graph1 === 'undefined') localStorage.graph1 = 'LOOT_VS_TIME';
		if(typeof localStorage.graph2 === 'undefined') localStorage.graph2 = 'TOTAL_VS_TIME';
		if(typeof localStorage.graphDays === 'undefined') localStorage.graphDays = '2';
		g1 = localStorage.graph1;
		g2 = localStorage.graph2;
		days = localStorage.graphDays;
	}
	
	$(".graph1 .graph-type-select").val(g1);
	$(".graph2 .graph-type-select").val(g2);
	$("#graph-interval").val(days);
	
	$(".graph-type-select").on("change", function(){
		if(CGBStats.compat.localStorage) localStorage[$(this).attr("data-for")] = $(this).val();
		CGBStats.graphs.render();
	});
	
	var renderQueueTimeout = -1;
	
	// prevent glitches
	function queueRender(){
		clearTimeout(renderQueueTimeout);
		renderQueueTimeout = setTimeout(function(){
			CGBStats.graphs.render(true);
		}, 250);
	}
	
	$("#graph-interval").on("change", function(){
		if(CGBStats.compat.localStorage) localStorage['graphDays'] = $(this).val();
		queueRender();
	});
	
	$(".dual-graph .refresh").click(function(){
		queueRender();
	});
	
	CGBStats.graphs.render();
	
	// reload for new stats on an interval
	var displayChartInterval = setInterval(function(){
		console.log("Re-displaying charts");
		queueRender();
	}, 1000 * 60 * 5); // 5 min
	
	// clean up
	CGBStats.nav.addShutdownHook(function(){
		clearInterval(displayChartInterval);
		
		delete CGBStats.graphs;
	});
</script>