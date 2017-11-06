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

if(is_file($locationOfTests."baseUrl.php"))
{
	require_once($locationOfTests."baseUrl.php");
}
else
{
	$staticBaseUrl = "";
}

$daysSince = calcuateDaysSince($configStatic['lastCheck']);
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
	<?php require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a href="../view/">View</a>
		<a class="active">Run</a>
		<a href="../settings/"> Settings </a>
		<div onclick="pausePollAction();" class="menuImageDiv">
			<img id="playImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Play.png" style="display: none;" height="30px">
			<img id="pauseImage" class="menuImage" src="<?php echo $baseUrl; ?>img/Pause.png" style="display: inline-block;" height="30px">
		</div>
	</div>

	<div id="main">
		
	</div>
	
	<div id="storage">
		<div class="newTestPopup">
			<div id="{{id}}" class="runNewTest">
				<div class="bannerPHP" style="display: none;">
					PhpUnit id not detected. Please verify that PhpUnit is installed and configured. 
				</div>
				<div class="newTestPartOne testSelectPartBorder testSelectPart">
					<h1 class="title">1.</h1>
					<br>
					<?php if(is_dir($locationOfTests) && !isDirRmpty($locationOfTests)):?>
						<select id="fileListSelector" onchange="getFileList();">
							<option value="PLACEHOLDER">Select A File</option>
							<?php
							$files = array_diff(scandir($locationOfTests), array('..', '.'));
							foreach($files as $key => $value)
							{
								$path = realpath($locationOfTests.DIRECTORY_SEPARATOR.$value);
						        if(!is_dir($path))
						        {
						        	echo "<option value='".$path."'' >".$value."</option>";
						        }
							}?>
						</select>
					<?php else: ?>
						Please specifiy a directory of where test are located on the settings page
					<?php endif;?>
				</div>
				<div class="newTestPartTwo testSelectPart testSelectPartBorder">
					<h1 class="title">2.</h1>
					<br>
					Set Base URL (i)
					<br>
					<input id="{{id}}BaseUrl" type="text" value="{{baseUrl}}" name="baseUrl">
					<br>
					<button onclick="changeBaseUrl('{{id}}BaseUrl')">Set Base Url</button>
				</div>
				<div class="newTestPartFour testSelectPart">
					<h1 class="title">3.</h1>
					<br>
					Max number of concurrent tests:
					<br>
					{{maxTestsNum}}
				</div>
				<br>
				<div class="newTestPartFive">
					<h1 class="title">4.</h1>
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
						<span>Tests to be run: </span>
						<br>
						<div id="testsPlaceHolder">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div style="background-color: white; border: 1px solid black;" id="{{id}}" class="scanBar containerMain">
				<div>
					<progress style="color: white; background: #000000; width: 100%;" id="{{id}}ProgressStart" value="0" max="1"></progress>
				</div>
				<div>
					<progress style="color: white; background: #000000; width: 100%;" id="{{id}}Progress" value="0" max="1"></progress>
				</div>
				<div style="color: black; width: 100%; text-align: left;" id="{{id}}Title">
					<h3>
						<span id="{{id}}Folder">{{file}}</span>
						<span id="{{id}}ProgressTxt" >--</span>
						<div style="float: right;">
							<img onclick="deleteSearch('{{id}}');" src="../core/img/trashCan.png" style="width: 25px; height: 25px; margin-top: -4px; cursor: pointer;">
						</div>
					</h3>
				</div>
				<div id="{{id}}ProgressBlocks" style="background-color: grey; max-height: 400px; border: 1px solid black; margin-top: 10px; overflow-y: scroll;">
					{{ProgressBlocks}}
				</div>
				<div style="display: none;">
					<input type="hidden" id="{{id}}File" value="{{file}}">
				</div>
			</div>
		</div>
	</div>
	
	<form id="settingsInstallUpdate" action="../update/updater.php" method="post" style="display: none"></form>
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/run.js?v=<?php echo $cssVersion?>"></script>
	<script>
		<?php
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		echo "var maxTestsStatic = ".$maxConcurrentTests.";"
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";
		var staticBaseUrl = "<?php echo $staticBaseUrl;?>";

		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			showStartTestNewPopup();

			setInterval(function(){poll();},1000);
		});
	</script>
	<?php readfile('../core/html/popup.html') ?>
</body>