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
	$url = "http://" . $_SERVER['HTTP_HOST'] .$partOfUrl ."setup/welcome.php";
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
if($backgroundPollingRateType == 'Seconds')
{
	$backgroundPollingRate *= 1000;
}

?>
<!doctype html>
<head>
	<title>Log Hog | Index</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<script src="../core/js/jquery.js"></script>
	<?php
		echo loadSentryData($sendCrashInfoJS, $branchSelected);
		echo loadVisibilityJS(baseURL());
	?>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="main">
		
	</div>
	
	<form id="settingsInstallUpdate" action="update/updater.php" method="post" style="display: none"></form>
	<script>

		<?php
		echo "var colorArrayLength = ".count($currentSelectedThemeColorValues).";";
		echo "var pausePollOnNotFocus = ".$pauseOnNotFocus.";";
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var flashTitleUpdateLog = ".$flashTitleUpdateLog.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		echo "var pollingRate = ".$pollingRate.";";
		echo "var backgroundPollingRate = ".$backgroundPollingRate.";";
		echo "var pausePollFromFile = ".$pausePoll.";";
		echo "var groupByColorEnabled = ".$groupByColorEnabled.";";
		echo "var pollForceTrue = ".$pollForceTrue.";";
		echo "var pollRefreshAll = ".$pollRefreshAll.";";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var enablePollTimeLogging = "<?php echo $enablePollTimeLogging;?>";
		var enableLogging = "<?php echo $enableLogging; ?>";
		var groupByType = "<?php echo $groupByType; ?>";
		var hideEmptyLog = "<?php echo $hideEmptyLog; ?>";
		var currentFolderColorTheme = "<?php echo $currentFolderColorTheme; ?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var pollRefreshAllBool = "<?php echo $pollRefreshAllBool;?>";
		var pollForceTrueBool = "<?php echo $pollRefreshAllBool;?>";
		var baseUrl = "<?php echo $baseUrl;?>";

	</script>
	<?php readfile('../core/html/popup.html') ?>
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/update.js?v=<?php echo $cssVersion?>"></script>
</body>