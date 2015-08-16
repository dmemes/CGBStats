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
<div class='signature-preview-container'><img src='' /></div>
<div class="container">
	<h1 class='stats-header'><span class='username'><?php echo htmlspecialchars($CGBStats->username); ?></span> <span class='info-userid'>(User #<?php echo $_SESSION['userid']; ?>)</span> <input type='text' class='new-username' maxlength="32" /><button class='edit-username change'></button></h1>
	<script>
		CGBStats.username = <?php echo json_encode($CGBStats->username); ?>;
		
		$(".new-username").keyup(function(e){
			if(e.which == 13) $(".edit-username").trigger("click");
		});
		
		$(".edit-username").click(function(){
			if($(this).hasClass("change")){
				$(".new-username").show().removeAttr("disabled").val(CGBStats.username).select();
				$(this).removeClass("change").addClass("save");
			} else {
				if($(".new-username").val().trim() == "") return;
				$(".new-username").attr("disabled", "disabled");
				$(this).attr("disabled", "disabled");
				$.post("/api/edituser.php", {username: $(".new-username").val()}, function(data){
					if(data.status == "success"){
						$(".edit-username").removeAttr("disabled").removeClass("save").addClass("change");
						$(".new-username").removeAttr("disabled").hide();
						$(".username").text(data.username);
						CGBStats.username = data.username;
					} else {
						$(".new-username").removeAttr("disabled");
						$(".edit-username").removeAttr("disabled");
						alert("Request failed: " + data.message);
					}
				}).fail(function(){
					$(".new-username").removeAttr("disabled");
					$(".edit-username").removeAttr("disabled");
					alert("Request failed. Try again later");
				});
			}
		});
	</script>
	<?php 
	$displaychart_param_user = -1;
	include 'displaychart.php'; ?>
	
	<h2>Modding Your Bot For CGBStats</h2>
	<?php include 'botinstructions.php'; ?>
	
	<h2 class='sig-h2'>Signature</h2>
	<p>Display a graph of your stats in your CGB forums signature. The signature will dynamically update so everyone can see your latest loots.<br/>
	<span style='font-size: 0.8em;'>Note: The images are set to be cached for 30 minutes on your browser. If you don't see updates, wait 30 minutes or clear your browser cache.</span>
	</p>
	<div class='signature-generator'>
		<select class='signature-type-select'></select><span> for past </span><input type="number" id="signature-interval" name="graph-interval" value="2" min="1" max="30" step="1" /><span> days</span>
		<button class='signature-generate'>Generate</button><button class='signature-preview'>Preview</button><br/><br/>
		<textarea class='signature-code'></textarea><div class='signature-code-placeholder'></div>
	</div>
	
	<?php
		$customquery_param_user = -1;
		include 'customquery.php';
	?>
	
	<style>
		.new-username {
			display: none;
		}
		
		.edit-username.change::after{
			content: "Change Username";
		}
		.edit-username.save::after{
			content: "Save";
		}
		
		#signature-interval {
			width: 3em;
			color: black;
		}
	
		.info-userid {
			font-size: 0.6em;
		}
		
		.signature-code {
			height: 0px;
			display: inline-block;
			width: 80%;
			transition: all 0.5s;
			font-family: monospace;
			opacity: 0;
			resize: none;
			box-sizing: border-box;
		}
		
		.signature-code.expanded {
			height: 50px;
			opacity: 1
		}
		
		.signature-code-placeholder {
			height: 50px;
			display: inline-block;
		}
		
		.signature-preview-container {
			display: none;
			position: fixed;
			top: 0px;
			left: 0px;
			width: 100%;
			height: 100%;
			background: rgba(0, 0, 0, 0.5);
			text-align: center;
			z-index: 99;
		}
		
		.signature-generator {
			z-index: 999;
			position: relative;
			transition: all 0.2s;
			padding: 0.5em;
		}
		
		.signature-generator.preview {
			background: rgba(255, 255, 255, 0.5);
			border-radius: 1em;
		}
		
		.signature-preview-container img {
			padding-top: 50%;
			transform: translateY(-50%);
		}
		
		button.signature-generate {
			border-top-right-radius: 0;
			border-bottom-right-radius: 0;
		}
		
		button.signature-preview {
			border-top-left-radius: 0;
			border-bottom-left-radius: 0;
		}
	</style>
	
	<script>
		CGBStats.signature = {};
		CGBStats.signature.types = <?php echo json_encode($CGBStats->images->types); ?>;
		for(var type in CGBStats.signature.types){
			if(CGBStats.signature.types.hasOwnProperty(type)){
				var option = $("<option>");
				option.attr("value", type);
				option.text(CGBStats.signature.types[type]);
				$(".signature-type-select").each(function(){
					$(this).append(option.clone());
				});
			}
		};
		
		CGBStats.signature.typesId = <?php echo json_encode($CGBStats->images->typesId); ?>;
		
		CGBStats.signature.shorten =  function(type, days, userid){
			var url = "https://<?php echo $CGBStats->config->domain; ?>/";
			var days = "" + days;
			if(days.length > 2) days = "02";
			if(days.length < 2) days = "0" + days;
			var userid = "" + userid;
			while(userid.length < 4) userid = "0"  + userid;
			var typeId = CGBStats.signature.typesId[type];
			url += userid + days + typeId;
			return url;
		};
		
		$(".signature-generate").click(function(){
			var type = $(".signature-type-select").val();
			CGBStats.account.getUserId(function(userid){
				var struserid = "" + userid;
				while(struserid.length < 4) struserid = "0"  + struserid;
				var code = "[url=https://<?php echo $CGBStats->config->domain; ?>/" + struserid + "][img]" + CGBStats.signature.shorten(type, $("#signature-interval").val(), userid) + "[/img][/url]";
				$(".signature-code").val(code).addClass("expanded");
			});
		});
		
		$(".signature-code").on("focus", function(){
			this.select();
			this.onmouseup = function() {
				this.onmouseup = null;
				return false;
			};
		});
		
		$(".signature-preview-container").click(function(){
			$(this).fadeOut();
			$(".signature-generator").removeClass("preview");
		});
		
		$(".signature-preview").click(function(){
			$(".tab-content").scrollTop($(".sig-h2").position().top + $(".tab-content").scrollTop())
			$(".signature-generate").trigger("click");
			var type = $(".signature-type-select").val();
			CGBStats.account.getUserId(function(userid){
				var code = "/api/render.php?userid=" + userid
					+ "&signature=true&type=" + type + "&days=" + $("#signature-interval").val() + "&dataurl";
				$(".signature-preview-container img").attr('src', "/static/loading.gif")
				$.get(code, function(data){
					$(".signature-preview-container img").attr('src', data).removeAttr("alt");
					$(".signature-preview-container").fadeIn();
					$(".signature-generator").addClass("preview");
				}).fail(function(){
					$(".signature-preview-container img").attr('src', '').attr("alt", "Image failed to load");
					$(".signature-preview-container").fadeIn();
					$(".signature-generator").addClass("preview");
				});
			});
		});
	</script>
</div>