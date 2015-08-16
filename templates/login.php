<div class="container">
	<p class="login-container">
		<span class='expired'>Your session has expired. Please log in again.<br/></span>
		<span class='sessionerror'>There was a problem logging you in. Enter your key again.<br/></span>
		<input type="password" id="apikey" placeholder="Your API key" autofocus /><br/><br/>
		<button type="button" class="login-button">Log In</button><br/><br/>
		<span class="status"></span><br/><br/>
		Interested in CGBStats? <a href="javascript: void(0);" data-href="/signup">Join here</a>
	</p>
	
	<style>
		.login-container {
			text-align: center;
			margin-top: 100px;
		}
		
		.login-button {
			font-size: 2em;
		}
		
		.status {
			font-size: 0.9em;
			color: red;
		}
		
		input[type=password] {
			width: 250px;
			font-size: 1.1em;
			border-radius: 0.5em;
			padding: 0.2em;
		}
		
		.expired, .sessionerror {
			color: red;
			display: none;
		}
	</style>
	
	<script>
		CGBStats.account.token = <?php echo json_encode($_SESSION['token']); ?>;
		$(".login-button").click(function(){
			$(this).attr("disabled", "disabled").text("Logging in...");
			$.post("/api/login.php", {token: CGBStats.account.token, apikey: $("#apikey").val()}, function(data){
				console.log(data);
				if(data.hasOwnProperty("status") && data.status === "success"){
					$(".account-tab").attr("data-href", "/logout").text("Log Out").removeClass("selected");
					CGBStats.nav.reload();
				} else {
					if(data.hasOwnProperty("extra") && data.extra == "bad_token"){
						CGBStats.persist.sessionError = true;
						CGBStats.nav.reload();
					}
					$(".login-button").removeAttr("disabled").text("Log In");
					$(".status").text(data.message);
				}
			}).fail(function(){
				$(".login-button").removeAttr("disabled").text("Log In");
				$(".status").text("Whoops, we're having some problems right now. Try again later.");
			});
		});
		$("#apikey").on("keydown keyup", function(e){
			if(e.which == 13) $(".login-button").trigger("click");
		});
		
		if(typeof CGBStats.persist.sessionExpired !== 'undefined' && CGBStats.persist.sessionExpired){
			delete CGBStats.persist.sessionExpired;
			$(".account-tab").attr("data-href", "/signup").text("Join");
			$(".expired").show();
		}
		
		if(typeof CGBStats.persist.sessionError !== 'undefined' && CGBStats.persist.sessionError){
			delete CGBStats.persist.sessionError;
			$(".account-tab").attr("data-href", "/signup").text("Join");
			$(".sessionerror").show();
		}
	</script>
</div>