<form id="settingsCacheVars" action="../core/php/settingsSave.php" method="post">
<div class="settingsHeader">
Cache Settings 
<div class="settingsHeaderButtons">
	<?php echo addResetButton("settingsCacheVars");
	if ($setupProcess == "preStart" || $setupProcess == "finished"): ?>
		<a class="linkSmall" onclick="saveAndVerifyMain('settingsCacheVars');" >Save Changes</a>
	<?php else: ?>
		<button  onclick="displayLoadingPopup();">Save Changes</button>
	<?php endif; ?>
</div>
</div>
<div class="settingsDiv" >
<ul id="settingsUl">
	<li>
		<span class="settingsBuffer" > Enable Cache Tests: </span>
		<div class="selectDiv">
			<select name="cacheTestEnable">
				<option <?php if($cacheTestEnable == 'true'){echo "selected";} ?> value="true">True</option>
				<option <?php if($cacheTestEnable == 'false'){echo "selected";} ?> value="false">False</option>
			</select>
		</div>
	</li>
	<li>
		<span class="settingsBuffer" > Clear TMP folder: </span>
		<a onclick="clearAllTestCache();" class="link">Clear</a>
	</li>
</ul>
</div>
</form>