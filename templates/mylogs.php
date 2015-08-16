<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
if(!headers_sent()){
	if(isset($_SESSION['lastauthtime'])) {
		$CGBStats->enableCachingWithData(intval($_SESSION['lastauthtime']), isset($_SESSION['userid']) ? $_SESSION['userid']: "");
	} else {
		$CGBStats->disableCaching();
	}
}
if(!isset($_SESSION['userid'])) {
	include 'login.php';
	die();
}
?>
<div class="container">
	<div class='logs-container'>
		<div class='logs-header'>
			<h1>My Logs <span class='info-userid'>(User #<?php echo $_SESSION['userid']; ?>)</span></h1>
			<select class='log-select' tabindex="1"><option disabled>Loading...</option></select><input type="text" tabindex="2" class='log-search' placeholder="Search logs; use tab and enter to navigate results" /><br/>
			<button class='toggle-search-help'>Search Help</button>
			<br style='clear: both;' />
			<div class='search-help'>
				Use <code>word</code> for results that may include <code>word</code><br/>
				Use <code data-insert="+">+word</code> for only results that include <code>word</code><br/>
				Use <code data-insert="-">-word</code> for only results that don't include <code>word</code><br/>
				Use <code data-insert="*">word*</code> for anything that starts with <code>word</code>
			</div>
		</div>
		<div class='logs-content'>
			<div class='log-display' tabindex="999"></div>
		</div>
	</div>
</div>

<div class='search-results'>
	
</div>

<script>
	$(".toggle-search-help").click(function(){
		$(".search-help").slideToggle();
	});
	
	$("[data-insert]").click(function(){
		var ins = $(this).attr("data-insert");
		if(ins == "*")
			$(".log-search").val($(".log-search").val() + ins);
		else 
			$(".log-search").val(ins + $(".log-search").val());
	});
</script>

<style>
	[data-insert] {
		cursor: pointer;
	}
	
	.toggle-search-help {
		float: right;
	}
	
	.search-help {
		display: none;
		text-align: right;
	}
	
	code {
		font-family: monospace;
		background: rgba(255, 255, 255, 0.2);
	}
	
	.info-userid {
		font-size: 0.6em;
	}
	
	.logs-container {
		width: calc(100% - 50px);
	}
	
	.logs-header {
		width: 100%;
		margin-bottom: 1em;
	}
	
	.log-select, .log-search {
		width: calc(50% - 2px);
		box-sizing: border-box;
		margin-left: 0;
		margin-right: 0;
		display: inline-block;
		border-radius: 0.5em;
		border: 1px solid rgb(169, 169, 169);
		height: 2em;
		padding: 0.1em;
	}
	
	.log-select {
		border-top-right-radius: 0 !important;
		border-bottom-right-radius: 0 !important;
		border-right: 0 !important;
	}
	
	.log-search {
		border-top-left-radius: 0 !important;
		border-bottom-left-radius: 0 !important;
	}
	
	.log-display {
		height: 500px;
		resize: none;
		border-radius: 1em;
		width: 100%;
		box-sizing: border-box;
		margin-left: 0;
		margin-right: 0;
		margin-bottom: 50px;
		
		background: rgba(255, 255, 255, 0.7);
		overflow-y: auto;
		font-family: monospace;
		
		padding: 1em;
		word-wrap: break-word;
	}
	
	.log-search, .log-select {
		border-radius: 0.5em;
		padding: 0.2em;
	}
	
	.search-results {
		position: fixed;
		display: none;
		background: white;
		border: 1px solid gray;
		border-bottom-right-radius: 0.5em;
		border-bottom-left-radius: 0.5em;
	}
	
	.search-results div {
		display: block;
		margin: 0;
		background: whitel
		transition: all 0.2s;
		cursor: default;
	}
	
	.search-results div:hover {
		background: rgba(0, 100, 200, 0.5);
	}
	
	.hl {
		background: yellow;
		transition: all 0.2s;
	}
	
	.hl.selected {
		background: orange;
	}
</style>

<script>
	CGBStats.logs = {search:null, hlindex: 0};
	$.get("/api/getlogs.php", function(data){
		CGBStats.logs.dates = data;
		$(".log-select").html("");
		
		if(data.length == 0){
			$("<option>").text("No logs").attr("disabled", "disabled").val("_cgbs_none").appendTo($(".log-select"));
			$(".log-select").val("_cgbs_none");
		} else {
			$("<option>").text("Select a log").attr("disabled", "disabled").val("_cgbs_default").appendTo($(".log-select"));
			$(".log-select").val("_cgbs_default");
		}
		
		for(var i = 0; i < data.length; i++){
			$("<option>").val(data[i].date).text(data[i].dateDisplay).appendTo($(".log-select"));
		}
		
		$(".log-select").on("change", function(){
			CGBStats.account.getUserId(function(userid){
				if(userid === false){
					CGBStats.persist.sessionExpired = true;
					CGBStats.nav.setLocation("/mystats");
				}
			});
			$.get("/api/getlogs.php", {date: $(this).val()}, function(data){
				var log = data[0].log;
				
				log = $("<div>").text(log).html();
				
				if(CGBStats.logs.search !== null){
					var searchStr = CGBStats.logs.search;
					var quotes = searchStr.match(/"[^"]+"/gi);
					searchStr = searchStr.replace(/"[^"]+"/gi, "")
					var searchArr = searchStr.split(" ").map(function(e){
						if(e.startsWith("+") || e.startsWith("-")) e = e.substring(1);
						if(e.endsWith("*")) e = e.substring(0, e.length - 1);
						return e.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
					});
					if(quotes !== null){
						for(var i = 0; i < quotes.length; i++){
							searchArr.push(quotes[i].substring(1, quotes[i].length - 1).replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"));
						}
					}
					
					var searchArr2 = [];
					
					for(var i = 0; i < searchArr.length; i++){
						if(searchArr[i].trim() !== "") searchArr2.push(searchArr[i]);
					}
					
					log = log.replace(new RegExp(searchArr2.join("|"), "gi"), "<span class='hl'>$&</span>");
				}
				
				$(".log-display").html(log.replace(/\n/g, "<br/>")).focus();
				
				CGBStats.logs.hlindex = 0;
				
				if($(".hl").length > 0){
					$(".hl").removeClass("selected");
					$(".log-display").scrollTop($(".hl").eq(CGBStats.logs.hlindex).addClass("selected").position().top + $(".log-display").scrollTop() - 200);
				}
			}).fail(function(){
				$(".log-display").html("Failed to load log");
			});
		});
	}).fail(function(){
		$(".log-display").html("Failed to load logs");
	});
	
	$(".log-display").on("keyup", function(e){
		if(e.which == 13){
			if($(".hl").length > 0){
				CGBStats.logs.hlindex++;
				if(CGBStats.logs.hlindex >= $(".hl").length) CGBStats.logs.hlindex = 0;
				$(".hl").removeClass("selected");
				$(".log-display").scrollTop($(".hl").eq(CGBStats.logs.hlindex).addClass("selected").position().top + $(".log-display").scrollTop() - 200);
			}
		}
	});
	
	$(".log-search").on("keyup", function(e){
		if(e.which == 13 && $(this).val().trim() !== ""){
			CGBStats.logs.search = $(this).val();
			CGBStats.account.getUserId(function(userid){
				if(userid === false){
					CGBStats.persist.sessionExpired = true;
					CGBStats.nav.setLocation("/mystats");
				}
			});
			$.get("/api/getlogs.php", {search: $(this).val()}, function(data){
				$(".search-results").html("");
				if(data.length == 0){
					$(".search-results").html("<div>No results</div>");
					setTimeout(function(){
						$(".search-results").fadeOut();
					}, 1000);
				}
				for(var i = 0; i < data.length; i++){
					$("<div>").attr("data-val", data[i].date).text(data[i].dateDisplay).attr("tabindex", i + 3).click(function(){
						$(".log-select").val($(this).attr("data-val")).trigger("change");
						$(".search-results").fadeOut();
					}).keyup(function(e){
						if(e.which == 13) $(this).trigger("click");
					}).appendTo($(".search-results"));
				}
				var pos = $(".log-search").position();
				var height = $(".log-search").height();
				$(".search-results").show().css({"top":pos.top + height + 6, "left":pos.left});
			}).fail(function(){
				$(".search-results").html("<div>Failed to load results</div>");
				setTimeout(function(){
					$(".search-results").fadeOut();
				}, 1000);
			});
		}
	});
</script>