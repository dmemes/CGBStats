<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
$CGBStats->enableCaching(filemtime(__FILE__));
?>
<div class='container'>
	<p class='logout-container'>Confirm:<br/><button type="button" class='logoutbtn'>Log Out</button></p>
	<style>
		.logout-container {
			text-align: center;
			margin-top: 100px;
		}
	
		button.logoutbtn {
			font-size: 1.5em;
		}
	</style>
	<script>
		$(".logoutbtn").click(function(){
			$.post("/api/logout.php", function(){
				$(".logout-container").html("You have been logged out.");
				$(".account-tab").attr("data-href", "/signup").text("Join");
			});
		});
	</script>
</div>