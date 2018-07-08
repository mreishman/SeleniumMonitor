<form id="settingsRunVars" action="../core/php/settingsSave.php" method="post">
<div class="settingsHeader">
Run Settings 
<div class="settingsHeaderButtons">
	<?php echo addResetButton("settingsRunVars");
	if ($setupProcess == "preStart" || $setupProcess == "finished"): ?>
		<a class="linkSmall" onclick="saveAndVerifyMain('settingsRunVars');" >Save Changes</a>
	<?php else: ?>
		<button  onclick="displayLoadingPopup();">Save Changes</button>
	<?php endif; ?>
</div>
</div>
<div class="settingsDiv" >
<ul id="settingsUl">
	<li>
		<span class="settingsBuffer" > Polling Rate: </span>  <input type="text" name="pollingRate" value="<?php echo $pollingRate;?>" >
		<div class="selectDiv">
			<select name="pollingRateType">
				<option <?php if($pollingRateType == 'Milliseconds'){echo "selected";} ?> value="Milliseconds">Milliseconds</option>
				<option <?php if($pollingRateType == 'Seconds'){echo "selected";} ?> value="Seconds">Seconds</option>
			</select>
		</div>
	</li>
	<li>
		<span class="settingsBuffer" > Test Folder: </span>  <input style="width: 600px;" type="text" name="locationOfTests" value="<?php echo $locationOfTests;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Selenium Folder: </span>  <input style="width: 600px;" type="text" name="locationOfSelenium" value="<?php echo $locationOfSelenium;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Log File: </span>  <input style="width: 600px;" type="text" name="logFileLocation" value="<?php echo $logFileLocation;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Default Base URL: </span>  <input style="width: 600px;" type="text" name="defaultBaseUrl" value="<?php echo $defaultBaseUrl;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Max Error Rate: </span>  <input style="width: 600px;" type="text" name="defaultErrorRate" value="<?php echo $defaultErrorRate;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Max Fail Rate: </span>  <input style="width: 600px;" type="text" name="defaultFailRate" value="<?php echo $defaultFailRate;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Max Combined Rate: </span>  <input style="width: 600px;" type="text" name="defaultCombinedRate" value="<?php echo $defaultCombinedRate;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Max Retries: </span>  <input style="width: 600px;" type="text" name="defaultNumRetry" value="<?php echo $defaultNumRetry;?>" >
	</li>
	<li>
		<span class="settingsBuffer" > Enable Network Check: </span>
		<select name="runCheckCount">
			<option <?php if($runCheckCount == 'true'){echo "selected";} ?> value="true">True</option>
			<option <?php if($runCheckCount == 'false'){echo "selected";} ?> value="false">False</option>
		</select>
		<br>
		* This could limit the ammount of tests running at a time depending of the speed of the network / main node
	</li>
	<li>
		<span class="settingsBuffer" > Default progress indicator: </span>
		<select name="defaultShowProgressType">
			<option <?php if($defaultShowProgressType == 'percent'){echo "selected";} ?> value="percent">Percent</option>
			<option <?php if($defaultShowProgressType == 'fraction'){echo "selected";} ?> value="fraction">Fraction</option>
		</select>
	</li>
	<li>
		<span class="settingsBuffer" > Default ETA: </span>
		<select name="defaultShowEta">
			<option <?php if($defaultShowEta == 'eta'){echo "selected";} ?> value="eta">ETA</option>
			<option <?php if($defaultShowEta == 'elapsed'){echo "selected";} ?> value="elapsed">Elapsed</option>
		</select>
	</li>
	<li>
		<span class="settingsBuffer" > Show Subfolder Files: </span>
		<select name="showSubFolderTests">
			<option <?php if($showSubFolderTests == 'true'){echo "selected";} ?> value="true">True</option>
			<option <?php if($showSubFolderTests == 'false'){echo "selected";} ?> value="false">False</option>
		</select>
	</li>
</ul>
</div>
</form>