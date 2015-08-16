	<p>How to get your bot to report to CGBStats:<br/>
		<ol>
			<li><a class='noajax' target="_blank" href="https://www.autoitscript.com/cgi-bin/getfile.pl?autoit3/autoit-v3-setup.exe">Download AutoIt</a></li>
			<li>Locate and edit <code>&lt;Bot Root Folder&gt;\COCBot\functions\Attack\AttackReport.au3</code></li>
			<li>Find the API key that you saved when you signed up.</li>
			<li>Add the following code on line <code>141</code> <button class='select-pre'>Select All</button> <span class='ctrl-c'>CTRL-C or CMD-C to copy</span> <pre>
   ; ==================== Begin CGBStats Mod ====================
   SetLog("Sending attack report to <?php echo $CGBStats->config->domain; ?>...", $COLOR_BLUE)
   $MyApiKey = "YOUR API KEY HERE" ; <---- insert api key here
   $sPD = 'apikey=' & $MyApiKey & '&ctrophy=' & $TrophyCount & '&cgold=' & $GoldCount & '&celix=' & $ElixirCount & '&cdelix=' & $DarkCount & '&search=' & $SearchCount & '&gold=' & $lootGold & '&elix=' & $lootElixir & '&delix=' & $lootDarkElixir & '&trophy=' & $lootTrophies & '&bgold=' & $BonusLeagueG & '&belix=' & $BonusLeagueE & '&bdelix=' & $BonusLeagueD & '&stars=' & $starsearned & '&thlevel=' & $iTownHallLevel & '&log='

   $tempLogText = _GuiCtrlRichEdit_GetText($txtLog, True)
   For $i = 1 To StringLen($tempLogText)
	  $acode = Asc(StringMid($tempLogText, $i, 1))
	  Select
		 Case ($acode >= 48 And $acode <= 57) Or _
			   ($acode >= 65 And $acode <= 90) Or _
			   ($acode >= 97 And $acode <= 122)
			$sPD = $sPD & StringMid($tempLogText, $i, 1)
		 Case $acode = 32
			$sPD = $sPD & "+"
		 Case Else
			$sPD = $sPD & "%" & Hex($acode, 2)
	  EndSelect
   Next

   $oHTTP = ObjCreate("winhttp.winhttprequest.5.1")
   $oHTTP.Open("POST", "https://<?php echo $CGBStats->config->domain; ?>/api/log.php", False)
   $oHTTP.SetRequestHeader("Content-Type", "application/x-www-form-urlencoded")

   $oHTTP.Send($sPD)

   $oReceived = $oHTTP.ResponseText
   $oStatusCode = $oHTTP.Status
   SetLog("Report sent. " & $oStatusCode & " " & $oReceived, $COLOR_BLUE)
   ; ===================== End CGBStats Mod =====================
</pre>
			<li>Locate <code>&lt;Bot Root Folder&gt;\GameBot.org.au3</code>, right click the file, and select <code>Compile Script</code></li>
			<li>That's it! Your bot is now ready to work with CGBStats.cf</li>
		</ol>
	</p>
	
		
	<script>
		$(".select-pre").click(function(){
			$(this).next().next().selectText();
			$(this).next().show().delay(5000).fadeOut();
		});
	</script>
	
	<style>
		.ctrl-c {
			display: none;
		}
		
		code, pre {
			font-family: monospace;
			max-width: calc(100% - 50px);
			word-wrap: break-word;
		}
		
		pre {
			background: rgba(0, 0, 0, 0.5);
			color: white;
			border-radius: 0.5em;
			padding: 0.5em;
			border: 1px solid black;
			overflow-y: auto;
			max-height: 5em;
		}
	</style>