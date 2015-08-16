<h2>Custom query</h2>
<p>Use JavaScript and <a href="http://hugoware.net/projects/jlinq" target="_blank" class="noajax">jLinq</a> to make custom queries on the bot data. The code is sandboxed so it's safe to enter anything, even infinite loops. Use arrow keys to recall entries, and tab to autocomplete.</p>
<div class='query-container'>
	<div class='query-table'></div>
	<div class='query-controls'></div>
	<textarea readonly class='query-log'></textarea>
	<input type='text' class='query-input' placeholder='return data.select()' disabled autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" /><br/>
	<div class='suggest'></div>
</div>

<style>
	.query-table table {
		margin-bottom: 10px;
		text-align: center;
		margin-left: auto;
		margin-right: auto;
	}
	
	.query-table table, .query-table tr, .query-table th, .query-table td {
		border: 1px solid black;
		border-collapse: collapse;
	}
	
	.query-table th, .query-table td {
		padding: 0.5em;
	}
	
	.query-table tr:nth-child(odd) {
		background: rgba(200, 200, 200, 0.5);
		color: black;
	}
	
	.query-table tr:nth-child(even) {
		background: rgba(255, 255, 255, 0.5);
		color: black;
	}
	
	.query-controls a {
		border: 1px solid black;
		border-left: 0;
		background: rgba(0, 0, 0, 0.5);
		color: white;
		padding-left: 0.5em;
		padding-right: 0.5em;
	}
	
	.query-controls a:first-child {
		border-left: 1px solid black;
		border-top-left-radius: 0.5em;
	}
	
	.query-controls a:last-child {
		border-top-right-radius: 0.5em;
	}
	
	.query-controls {
		text-align: center;
	}
	
	.query-container {
		margin-bottom: 2em;
	}
	
	.query-log {
		width: calc(100% - 50px);
		height: 200px;
		background: black;
		color: white;
		font-family: monospace;
		margin-bottom: 0;
		resize: none;
		border: 1px solid black;
		padding: 0.5em;
		border-radius: 0.5em;
		box-sizing: border-box;
	}
	
	.query-input {
		margin-top: 0;
		background: black;
		color: white;
		font-family: monospace;
		width: calc(100% - 50px);
		display: block;
		border: 1px solid white;
		padding: 0.5em;
		border-radius: 0.5em;
		box-sizing: border-box;
	}
	
	.suggest {
		height: 6em;
		overflow-y: hidden;
	}
</style>

<script>
	CGBStats.query = {};
	CGBStats.query.getData = function(cb){
		var url = ("/api/dump.php" <?php if(isset($customquery_param_user) && $customquery_param_user > -1) echo ' + "?userid=' . $customquery_param_user . '"'; ?>);
		var xhr = new XMLHttpRequest(); xhr.onload = function(){ cb(xhr.responseText); }; xhr.open('GET', url, true); xhr.send();
	};
	
	CGBStats.query.log = function(item){
		$(".query-log").val($(".query-log").val() + "\n> " + item);
		$(".query-log").get(0).scrollTop = $(".query-log").get(0).scrollHeight
	};
	
	CGBStats.query.storedQueries = [];
	CGBStats.query.storedQueriesIndex = -1;
	
	CGBStats.query.jlinqSource = null;
	
	CGBStats.query.jlinqFuncs = ["ignoreCase","reverse","useCase","each","attach","join","assign","sort","equals","orEquals","orNotEquals","andEquals","andNotEquals","notEquals","starts","orStarts","orNotStarts","andStarts","andNotStarts","notStarts","ends","orEnds","orNotEnds","andEnds","andNotEnds","notEnds","contains","orContains","orNotContains","andContains","andNotContains","notContains","match","orMatch","orNotMatch","andMatch","andNotMatch","notMatch","type","orType","orNotType","andType","andNotType","notType","greater","orGreater","orNotGreater","andGreater","andNotGreater","notGreater","greaterEquals","orGreaterEquals","orNotGreaterEquals","andGreaterEquals","andNotGreaterEquals","notGreaterEquals","less","orLess","orNotLess","andLess","andNotLess","notLess","lessEquals","orLessEquals","orNotLessEquals","andLessEquals","andNotLessEquals","notLessEquals","between","orBetween","orNotBetween","andBetween","andNotBetween","notBetween","betweenEquals","orBetweenEquals","orNotBetweenEquals","andBetweenEquals","andNotBetweenEquals","notBetweenEquals","empty","orEmpty","orNotEmpty","andEmpty","andNotEmpty","notEmpty","is","orIs","orNotIs","andIs","andNotIs","notIs","min","max","sum","average","skip","take","skipTake","select","distinct","group","define","any","none","all","first","last","at","count","removed","where","or","and","not","andNot","orNot"];
	
	CGBStats.query.tableVals = ["bdelix", "belix", "bgold", "cdelix", "celix", "cgold", "ctrophy", "date", "delix", "elix", "gold", "search", "stars", "thlevel", "trophy", "userid"];
	
	$.ajax({url:"/static/vendor/jlinq/jlinq-beta.js",dataType:"text"}).done(function(data){
		CGBStats.query.jlinqSource = data;
		$(".query-input").removeAttr("disabled");
		
		if(typeof window.Worker === 'undefined'){
			$(".query-input").val("You are using an unsupported browser").attr("disabled", "disabled");
		}
	});
	
	$(".query-input").on("keydown", function(e){
		if(e.which == 9 || e.which == 38 || e.which == 40 || e.which == 13) e.preventDefault();
	});
	
	$(".query-input").on("keyup", function(e){
		var query = $(this).val();
		
		var suggestions = [];
		var func = null;
		
		var dataIndexOf = query.lastIndexOf("data.");
		var dataIndexOf2 = query.lastIndexOf(").");
		var q1IndexOf = query.lastIndexOf("\"");
		var q2IndexOf = query.lastIndexOf("\'");
		var suggest = 0;
		if(dataIndexOf > -1){
			var func2 = query.substring(dataIndexOf + 5);
			if(func2.match(/^[a-zA-Z0-9]+$/) !== null || func2 == ""){
				suggest = 1;
				func = func2;
			}
		}
		
		if(dataIndexOf2 > -1 && dataIndexOf2 > dataIndexOf){
			var func2 = query.substring(dataIndexOf2 + 2);
			if(func2.match(/^[a-zA-Z0-9]+$/) !== null || func2 == ""){
				suggest = 1;
				func = func2;
			}
		}
		
		if(q1IndexOf > -1 && q1IndexOf > dataIndexOf){
			var func2 = query.substring(q1IndexOf + 1);
			if(func2.match(/^[a-zA-Z0-9]+$/) !== null || func2 == ""){
				suggest = 2;
				func = func2;
			}
		}
		
		if(q2IndexOf > -1 && q2IndexOf > q1IndexOf){
			var func2 = query.substring(q2IndexOf + 1);
			if(func2.match(/^[a-zA-Z0-9]+$/) !== null || func2 == ""){
				suggest = 2;
				func = func2;
			}
		}
		
		if(suggest === 1){
			$(".suggest").html("");
			var dummy = CGBStats.query.jlinqFuncs;
			for(var i = 0; i < dummy.length; i++){
				if(dummy[i].startsWith(func) || func == ""){
					suggestions.push(dummy[i]);
					$(".suggest").append(dummy[i] + "() ");
				}
			}
		} else if(suggest === 2) {
			$(".suggest").html("");
			var dummy = CGBStats.query.tableVals;
			for(var i = 0; i < dummy.length; i++){
				if(dummy[i].startsWith(func) || func == ""){
					suggestions.push(dummy[i]);
					$(".suggest").append(dummy[i] + " ");
				}
			}
		} else {
			$(".suggest").html("");
		}
		
		if(e.which == 9 || e.which == 38 || e.which == 40 || e.which == 13) e.preventDefault();
		
		if(e.which == 9){
			if(func != null && suggestions.length >= 1){
				query = query.substring(0, query.length - func.length) + suggestions[0];
				$(this).val(query);
			}
			if($(this).val() == "") $(this).val("return data.select();");
		} else if(e.which == 38){
			CGBStats.query.storedQueriesIndex--;
			if(CGBStats.query.storedQueriesIndex < 0) CGBStats.query.storedQueriesIndex = 0;
			if(CGBStats.query.storedQueriesIndex > CGBStats.query.storedQueries.length - 1) CGBStats.query.storedQueriesIndex = CGBStats.query.storedQueries.length - 1;
			if(CGBStats.query.storedQueriesIndex > -1){
				$(this).val(CGBStats.query.storedQueries[CGBStats.query.storedQueriesIndex]);
			}
		} else if(e.which == 40){
			CGBStats.query.storedQueriesIndex++;
			if(CGBStats.query.storedQueriesIndex < 0) CGBStats.query.storedQueriesIndex = 0;
			if(CGBStats.query.storedQueriesIndex > CGBStats.query.storedQueries.length - 1) CGBStats.query.storedQueriesIndex = CGBStats.query.storedQueries.length - 1;
			if(CGBStats.query.storedQueriesIndex > -1){
				$(this).val(CGBStats.query.storedQueries[CGBStats.query.storedQueriesIndex]);
			}
		} else if(e.which == 13 && query.trim() !== ""){
			$(".query-input").attr("disabled", "disabled");
			CGBStats.query.log("function(data){" + query + "}");
			CGBStats.query.storedQueries.push(query);
			CGBStats.query.storedQueriesIndex = CGBStats.query.storedQueries.length;
			
			try {
				new Function("data", "jlinq", query);
			} catch(error){
				var extra = "";
				if(error.hasOwnProperty("lineNumber")) extra += error.lineNumber;
				if(error.hasOwnProperty("columnNumber")) extra += ":" + error.columnNumber;
				CGBStats.query.log(("" + error) + "\n" + extra);
				$(".query-input").removeAttr("disabled").select();
				return;
			}
			
			var code = "var console = {log: application.remote.log, error: application.remote.log};" + CGBStats.query.jlinqSource + "; function executeQuery(textData){var data = jlinq.from(JSON.parse(textData)); try { " + query + "; } catch(e){ if(e.hasOwnProperty('name')) return {_cgbs_error: true, content: e.name + ': ' + e.message}; else return {_cgbs_error: true, content: e + ''} } }; var api = { exec: function(cb){ application.remote.getData(function(textData){ cb(executeQuery(textData)); }); }}; application.setInterface(api);";
			
			var api = {
				getData: CGBStats.query.getData,
				log: CGBStats.query.log
			};

			jailed.setChromeHttpsWorkaroundOn(true);
			var plugin = new jailed.DynamicPlugin(code, api);
			
			var pluginTimeout = setTimeout(function(){
				plugin.disconnect();
				clearTimeout(pluginTimeout);
				clearTimeout(pluginTimeout2);
				clearTimeout(pluginTimeout3);
				$(".query-input").removeAttr("disabled").select();
				CGBStats.query.log("Your code took more than 10 seconds to execute and was terminated");
			}, 10000);
			
			var pluginTimeout2 = setTimeout(function(){
				CGBStats.query.log("Waiting for code to complete...");
			}, 2000);
			
			var pluginTimeout3 = setTimeout(function(){
				CGBStats.query.log("Still waiting...");
			}, 5000);
			plugin.whenConnected(function(){
				plugin.remote.exec(function(resp){
					plugin.disconnect();
					clearTimeout(pluginTimeout);
					clearTimeout(pluginTimeout2);
					clearTimeout(pluginTimeout3);
					
					$(".query-table").html("");
					$(".query-controls").html("");
					
					if(typeof resp === "undefined"){
						CGBStats.query.log("undefined");
						$(".query-input").val("").removeAttr("disabled").select();
						return;
					}
					if(resp === null){
						CGBStats.query.log("null");
						$(".query-input").val("").removeAttr("disabled").select();
						return;
					}
					if(resp === "Could not transfer object"){
						CGBStats.query.log(resp);
						$(".query-input").removeAttr("disabled").select();
						return;
					}
					
					if(resp.constructor !== Array){
						if(resp.hasOwnProperty("_cgbs_error") || resp._cgbs_error){
							CGBStats.query.log(resp.content);
							$(".query-input").removeAttr("disabled").select();
							return;
						}
						
						resp = [{"return": JSON.stringify(resp)}];
					}
					
					$(".query-input").val("").removeAttr("disabled").select();
					
					var keys = [];
					if(resp.length === 0){
						CGBStats.query.log("No data");
						return;
					}
					for(key in resp[0]){
						if(resp[0].hasOwnProperty(key)) keys.push(key);
					}
						
					var output = "<table><tr>";
						
					for(var i = 0; i < keys.length; i++){
						output += "<th>" + keys[i] + "</th>";
					}
					output += "</tr>";
					
					for(var j = 0; j < resp.length; j++){
						output += "<tr class='query-page query-page-" + Math.floor(j / 5) + "'>";
						for(var i = 0; i < keys.length; i++){
							output += "<td>" + resp[j][keys[i]] + "</td>";
						}
						output += "</tr>";
					}
					output += "</table>";
					$(".query-table").html(output);
					
					var numPages = Math.ceil(resp.length / 5);
					if(numPages > 1){
						for(var k = 0; k < numPages; k++){
							$("<a>").text(k + 1).attr("href", "javascript:void(0)").attr("data-page", k).click(function(){
								$(".query-page").hide();
								$(".query-page-" + $(this).attr("data-page")).show();
							}).appendTo($(".query-controls"));
						}
					}
					
					$(".query-page").hide();
					$(".query-page-0").show();
				});
			});
			plugin.whenFailed(function(){
				CGBStats.query.log("Sandbox error");
				clearTimeout(pluginTimeout);
				clearTimeout(pluginTimeout2);
				clearTimeout(pluginTimeout3);
				$(".query-input").removeAttr("disabled").select();
			});
		}
	});
	
	CGBStats.nav.addShutdownHook(function(){
		delete CGBStats.query;
	});
</script>