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
?>
<!doctype html>
<head>
	<title>Log Hog | Index</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<script src="../core/js/jquery.js"></script>
	<?php
		echo loadSentryData($sendCrashInfoJS, $branchSelected);
	?>
	<style type="text/css">
		.runNewTest
		{
			display: inline-block;
		    width: 660px;
		    margin-left: 25%;
		    margin-top: 10%;
		    background: rgba(0,0,0,.6);
		    border: 1px solid white;
		    padding: 25px;
		    border-radius: 20px;
		}
		.testSelectPartBorder
		{
			border-right: 1px solid white;
		}
		.testSelectPart
		{
			padding: 5px;
			width: 197px;
			height: 150px;
		}
		.title
		{
			text-align: center;
		}
	</style>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	
	<div id="main">
		
	</div>
	
	<!-- <div id="storage"> -->
		<div class="newTestPopup">
			<div class="runNewTest">
				<div class="newTestPartOne testSelectPartBorder testSelectPart" style="display: inline-block;">
					<h1 class="title">1.</h1>
					<br>
					<?php if(is_dir($locationOfTests) && !isDirRmpty($locationOfTests)):?>
						<select>
							<option>Select A File</option>
							<?php
							$files = array_diff(scandir($locationOfTests), array('..', '.'));
							foreach($files as $key => $value)
							{
								$path = realpath($locationOfTests.DIRECTORY_SEPARATOR.$value);
						        if(!is_dir($path))
						        {
						        	echo "<option>".$value."</option>";
						        }
							}?>
						</select>
					<?php else: ?>
						Please specifiy a directory of where test are located on the settings page
					<?php endif;?>
				</div>
				<div class="newTestPartTwo testSelectPart testSelectPartBorder" style="display: inline-block;">
					<h1 class="title">2.</h1>
					<br>
					Set Base URL (i)
					<br>
					<input type="text" name="baseUrl">
				</div>
				<div class="newTestPartTwo testSelectPart" style="display: inline-block;">
					<h1 class="title">3.</h1>
					<br>
					Run tests by:
					<br>
					<select>
						<option>Select</option>
						<option>Group</option>
						<option>Custom</option>
					</select>
				</div>
			</div>
		</div>
	<!-- </div> -->
	
	<form id="settingsInstallUpdate" action="../update/updater.php" method="post" style="display: none"></form>
	<script>
		<?php
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";
	</script>
	<?php readfile('../core/html/popup.html') ?>
</body>