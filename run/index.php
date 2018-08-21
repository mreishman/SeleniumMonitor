<?php
require_once('../core/php/commonFunctions.php');

$baseUrl = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrl = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
if(!file_exists($baseUrl.'conf/config.php'))
{
	$partOfUrl = clean_url($_SERVER['REQUEST_URI']);
	$url = "http://" . $_SERVER['HTTP_HOST'] .substr($partOfUrl,0,4) ."setup/welcome.php";
	header('Location: ' . $url, true, 302);
	exit();
}
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/loadVars.php');
require_once('../core/php/updateCheck.php');

$daysSince = calcuateDaysSince($configStatic['lastCheck']);

if($pollingRateType == 'Seconds')
{
	$pollingRate *= 1000;
}

?>
<!doctype html>
<head>
	<title>SeleniumMonitor | Run</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="../core/template/runcss.css?v=<?php echo $cssVersion; ?>">
	<script src="../core/js/jquery.js"></script>
	<?php
		echo loadSentryData($sendCrashInfoJS, $branchSelected);
	?>
</head>
<body>
	<?php
	$thisVarForShowSideBar = true;
	require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="20px"> </a>
		<div onclick="pausePollAction();" class="menuImageDiv">
			<img id="playImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Play.png" style="display: none;" height="30px">
			<img id="pauseImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Pause.png" style="display: inline-block;" height="30px">
		</div>
		<img class="menuImage" src="<?php echo $baseUrl; ?>img/stopSignLight.png" onclick="stopAllTests();" style="display: inline-block; cursor: pointer;" height="30px">
		<a onclick="toggleAjaxLinks();" class="link" style="float: right; margin-left: 10px;">Ajax Settings</a>
		<?php require_once("../core/php/template/otherLinks.php");?>
		<div style="display: none;" id="ajaxLinks">
		<ul style="color: black;">
			<li>
				Set Base URL: <input id="baseUrlInput" type="text" value="<?php echo $defaultBaseUrl; ?>" placeholder="https://test.website.com/" name="baseUrl">
			</li>
			<span id="maxTestsNum" ></span>
			<li id="browserSelect">
			</li>
			<li id="osSelect">
			</li>
			<li>
				Allowed Error Rate: <input id="errorRate" type="text" value="<?php echo $defaultErrorRate; ?>" name="errorRate">
			</li>
			<li>
				Allowed Fail Rate: <input id="failRate" type="text" value="<?php echo $defaultFailRate; ?>" name="failRate">
			</li>
			<li>
				Combined Rate: <input id="combinedRate" type="text" value="<?php echo $defaultCombinedRate; ?>" name="combinedRate"> 
			</li>
			<li>
				Max Retries: <input id="NumRetry" type="text" value="<?php echo $defaultNumRetry; ?>" name="NumRetry"> 
			</li>
			
			<a class="link" onclick="refreshAjaxSettingsData();" >Refresh</a>
		</ul>
	</div>
	</div>
	<?php require_once('../core/php/template/sidebar.php'); ?>
	<div id="main">
		
	</div>
	
	<div id="storage">
		<div class="newTestPopup">
			<div id="{{id}}" class="runNewTest" >
				<div class="bannerPHP" style="display: none;">
					PhpUnit is not detected. Please verify that PhpUnit is installed and configured. 
				</div>
				<div class="newTestPartOne">
					<h1 class="title">1.</h1>
					<br>
					<?php if(is_dir($locationOfTests) && !isDirRmpty($locationOfTests)):?>
					<form id="fileSelectListForm" >
						<?php echo scanDirForTests($locationOfTests, $showSubFolderTests); ?>
					</form>
					<?php else: ?>
						Please specifiy a directory of where test are located on the settings page
					<?php endif;?>
				</div>
				<br>
				<div class="newTestPartFive">
					<h1 class="title">2.</h1>
					<br>
					<div class="partFiveContainer">
						<span>Groups:</span>
						<br>
						<div id="groupsPlaceHodler">
						</div>
					</div>
					<div class="partFiveContainer">
						<span>Exclude Groups: </span>
						<br>
						<div id="groupExcludePlaceHolder">
						</div>
					</div>
					<div class="partFiveContainer">
						<span>Tests to be run<b>(<span id="testCount"></span>)</b>: </span>
						<br>
						<div id="testsPlaceHolder">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div style="background-color: white; border: 1px solid black; " id="{{id}}" class="scanBar containerMain">
				<div id="{{id}}ProgressBG" class="progressBG" style="width: 100%; background-color: darkgrey; height: 15px; border-top: 1px solid black; border-bottom: 1px solid black;">
					<div id="{{id}}ProgressRisky" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockRisky"></div>
					<div id="{{id}}ProgressSkip" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockSkip"></div>
					<div id="{{id}}ProgressFail" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockFail"></div>
					<div id="{{id}}ProgressError" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockError"></div>
					<div id="{{id}}ProgressPass" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockPass"></div>
					<div id="{{id}}ProgressRunning" style="border: 0; width: 0; height: 13px; display: inline-block; margin: 0; padding: 0; float: left;" class="blockInProgress"></div>
				</div>
				<div  class="fontChange" style="width: 100%; text-align: left;" id="{{id}}Title">
					<h3>
						<div style="float: right;">
							<img class="imageInHeaderContainer" onclick="exportResults('{{id}}');" src="../core/img/save.png">
							<img class="imageInHeaderContainer" onclick="deleteTests('{{id}}');" src="../core/img/trashCan.png">
							<img id="{{id}}StopButton" class="imageInHeaderContainer stopButtonClass" src="../core/img/stopSignDark.png" onclick="stopTestById('{{id}}');">
							<img style="display: none;" id="{{id}}RefreshButton" class="imageInHeaderContainer" onclick="reRunTestsPopup('{{id}}');" src="../core/img/Refresh.png">
						</div>
					</h3>
					<div style="font-size: 200%;">
						<span onclick="togglePercent('{{id}}');" class="infoBox"  <?php if($defaultShowProgressType !== "percent"): ?> style="display: none;" <?php endif; ?>  id="{{id}}ProgressTxt" >--</span>
						<span onclick="togglePercent('{{id}}');" class="infoBox" <?php if($defaultShowProgressType !== "fraction"): ?> style="display: none;" <?php endif; ?> id="{{id}}ProgressCount" >--</span>
						<span onclick="toggleEta('{{id}}');"  class="infoBox"  <?php if($defaultShowEta !== "eta"): ?> style="display: none;" <?php endif; ?>   id="{{id}}EtaTxt" >{{eta}}</span>
						<input type="hidden" name="etaSec" id="{{id}}EtaSec" value="0" >
						<span onclick="toggleEta('{{id}}');" class="infoBox"  <?php if($defaultShowEta !== "elapsed"): ?> style="display: none;" <?php endif; ?>   id="{{id}}ElapsedTxt" >{{eta}}</span>
						<input type="hidden" name="etaSec" id="{{id}}ElapsedSec" value="0" >
						<span class="infoBox">
							<a href="../view/tests.php#{{id}}.log" style="color: black;" >{{id}}.log</a>
						</span>
					</div>
				</div>
				<div id="{{id}}ProgressBlocks" class="containerBox" style="max-height: 500px; overflow: auto;">
					{{ProgressBlocks}}
				</div>
				<div class="key fontChange" style="margin-bottom: 10px; margin-top: 10px;" >
					Key:
					<br>
					<div class="block blockKey blockEmpty"></div> - Waiting
					<div class="block blockKey blockInProgress"></div> - Running
					<div class="block blockKey blockPass"></div> - Passed
					<div class="block blockKey blockError"></div> - Error
					<div class="block blockKey blockFail"></div> - Fail
					<div class="block blockKey blockSkip"></div> - Skipped
					<div class="block blockKey blockRisky"></div> - Risky
				</div>
				<div id="{{id}}MainMenu" >
					<div style="border-bottom: 1px solid black;">
						<ul class="menu">
							<li id="{{id}}MainMenuStatsMenu" onclick="toggleTab('{{id}}MainMenu', 'Stats');"  class="active">
								Stats
							</li>
							<li id="{{id}}MainMenuErrorsMenu" onclick="toggleTab('{{id}}MainMenu', 'Errors');">
								Errors
							</li>
							<li id="{{id}}MainMenuFailsMenu" onclick="toggleTab('{{id}}MainMenu', 'Fails');">
								Fails
							</li>
							<li id="{{id}}MainMenuConfigMenu" onclick="toggleTab('{{id}}MainMenu', 'Config');">
								Config
							</li>
						</ul>
					</div>
					<div class="conainerSub containerSubBG" id="{{id}}MainMenuStats">
						<div class="fontChange subTitleEF">
							<span id="{{id}}FailCount">0</span>/{{totalCount}} Fails
						</div>
						<br>
						<div  class="fontChange subTitleEF">
							<span id="{{id}}ErrorCount">0</span>/{{totalCount}} Errors
						</div>
					</div>
					<div class="conainerSub containerSubBG" style="display: none;" id="{{id}}MainMenuFails">
						<div class="containerBox">
							<span id="{{id}}Fails">
							</span>
						</div>
					</div>
					<div class="conainerSub containerSubBG" style="display: none;" id="{{id}}MainMenuErrors" >
						<div class="containerBox">
							<span  id="{{id}}Errors">
							</span>
						</div>
					</div>
					<div class="fontChange conainerSub containerSubBG" id="{{id}}MainMenuConfig" style="display: none;" >
						<br>
						<input style="width: 75%; display: block;" type="text" id="{{id}}BaseUrl" value="{{baseUrl}}">
						<br>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<form id="settingsInstallUpdate" action="../update/updater.php" method="post" style="display: none"></form>
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/run.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/sidebar.js?v=<?php echo $cssVersion?>"></script>
	<script>
		<?php
		$popupSettingsArrayEncode = json_encode($popupSettingsArray);
		if($popupSettingsArrayEncode == "")
		{
			$popupSettingsArrayEncode = "{}";
		}
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		echo "var maxTestsStatic = ".$maxConcurrentTests.";";
		echo "var pollingRate = ".$pollingRate.";";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var popupSettingsArray = JSON.parse('<?php echo $popupSettingsArrayEncode; ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";
		var placeholderBaseUrl = "<?php echo $defaultBaseUrl; ?>";
		var runCheckCount = "<?php echo $runCheckCount; ?>";
		var cacheTestEnable = "<?php echo $cacheTestEnable; ?>";
		var urlForSendTests = "<?php echo $mainServerIP; ?>";
		var browserStackUsername = "<?php echo $browserStackUsername; ?>";
		var browserStackAccessKey = "<?php echo $browserStackAccessKey; ?>";
		var browserValue = "any";
		var platformValue = "any";
		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			showStartTestNewPopup();

			setInterval(function(){poll();},pollingRate);

			setInterval(function(){timerStuff();},1000);
		});
	</script>
	<?php readfile('../core/html/popup.html') ?>
</body>