<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/include/include.php';
$CGBStats->enableCaching(filemtime(__FILE__));
?>
<div class="container">
	<?php if(isset($_SESSION['userid'])) { ?>
	<h1>You are already logged in!</h1>
	<p><a href="javascript:void(0)" data-href="/logout">Log Out</a></p>
	<?php } else { ?>
	
	<?php if(isset($_COOKIE['CGBStats_signed_up'])) { ?>
	<p>Warning: You have already signed up once. You should use your old account instead of creating a new one.</p>
	<?php } ?>
	<p class="join-cgbstats">
		<button type="button" class="join-button">Join CGBStats</button><br/>
		You will receive an API key that you can use with CGB to post stats here. Keep that key safe!<br/>
		<span class="status"></span>
	</p>
	<div class='instructions'>
		<hr />
		<?php include 'botinstructions.php'; ?>
	</div>
	
	<style>
		.instructions {
			display: none;
		}
		
		.join-cgbstats {
			text-align: center;
			margin-top: 100px;
		}
	
		button.join-button {
			font-size: 3em;
		}
		
		.status {
			font-size: 0.9em;
			color: red;
		}
		
		input[type=text] {
			width: 250px;
		}
	</style>
	
	<script>
		$(".join-button").click(function(){
			$(this).attr("disabled", "disabled").text("Joining...");
			$.get("/api/token.php", function(){
				$.post("/api/signup.php", function(data){
					console.log(data);
					if(data.hasOwnProperty("userid") && data.hasOwnProperty("apikey")){
						$(".join-cgbstats").html("You've joined CGBStats.<br/>Your user ID is: <strong>" + data.userid + "</strong><br/>Your API key is: <input type='text' value='"
							+ data.apikey + "' /></strong><br/>Copy and save your API key somewhere safe. <strong>You'll need it to access your stats.</strong> Don't share your key with anyone.");
						$(".account-tab").attr("data-href", "/logout").text("Log Out").removeClass("selected");
						$(".instructions").slideDown();
						$("input[type=text]").on("focus", function(){
							this.select();
							this.onmouseup = function() {
								this.onmouseup = null;
								return false;
							};
						});
					} else {
						if(data.hasOwnProperty("extra") && data.extra == "bad_token") location.reload(true);
						$(".join-button").removeAttr("disabled").text("Join CGBStats");
						$(".status").text("Whoops, we're having some problems right now. Try again later.");
					}
				}).fail(function(){
					$(".join-button").removeAttr("disabled").text("Join CGBStats");
					$(".status").text("Whoops, we're having some problems right now. Try again later.");
				});
			}).fail(function(){
				$(".join-button").removeAttr("disabled").text("Join CGBStats");
				$(".status").text("Whoops, we're having some problems right now. Try again later.");
			});
		});
	</script>
	<?php } ?>
</div>