<?php
	include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
	
	$CGBStats->enableCaching(filemtime(__FILE__));
?>
<div class="container">
	<p class="find-container">
		<input type="text" id="finduser" placeholder="User ID or username" autofocus /><br/><br/>
		<button type="button" class="find-button">Find</button><br/><br/>
		<div class='results'>
			
		</div>
	</p>
	
	<style>
		.results {
			text-align: center;
			border-top: 1px solid gray;
			width: calc(100% - 50px);
		}
		
		.results button {
			font-size: 1.2em;
			margin: 0.2em;
		}
		
		.find-container {
			text-align: center;
			margin-top: 100px;
			width: calc(100% - 50px);
		}
		
		.find-button {
			font-size: 2em;
		}
		
		#finduser {
			width: 250px;
			font-size: 1.1em;
			border-radius: 0.5em;
			padding: 0.2em;
		}
	</style>
	<script>
		function openUserTab(userid){
			CGBStats.nav.openTab("/userstats?userid=" + userid, "User #" + userid);
			CGBStats.nav.setLocation("/userstats?userid=" + userid);
		}
		
		$(".find-button").click(function(){
			if($("#finduser").val().match(/^[0-9]+$/) !== null)
				openUserTab($("#finduser").val());
			else {
				$(".results").html("Loading...");
				$.get("/api/searchuser.php", {username: $("#finduser").val()}, function(data){
					if(data.length == 0){
						$(".results").html("No results");
					} else {
						$(".results").html("");
						for(var i=0; i<data.length; i++){
							$(".results").append($("<button>").text(data[i].username + " (#" + data[i].userid + ")").attr("data-userid",  data[i].userid).click(function(){
								openUserTab($(this).attr("data-userid"));
							})).append("<br/>");
						}
					}
				}).fail(function(){
					$(".results").html("Failed to load search. Try again later.");
				});
			}
		});
		
		$("#finduser").on("keyup", function(e){
			if(e.which == 13) $(".find-button").trigger("click");
		});
	</script>
</div>