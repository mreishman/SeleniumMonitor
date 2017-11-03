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
		    width: 1000px;
		    margin-left: 2%;
		    margin-top: 2%;
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
			width: 310px;
			height: 150px;
			display: inline-table;
		}
		.testSelectPart select, .testSelectPart input, .testSelectPart button
		{
			width: 100%;
			padding: 0;
			margin-left: 0;
		}
		.title
		{
			text-align: center;
		}
		.newTestPartFive
		{
			margin-top: 30px;
			border-top: 1px solid white;
		}
		.list
		{
			text-decoration: none;
			list-style: none;
		}
	</style>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	
	<div id="main">
		
	</div>
	
	<div id="storage">
		<div class="newTestPopup">
			<div id="{{id}}" class="runNewTest">
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
					<input type="text" value="<?php echo $staticBaseUrl; ?>" name="baseUrl">
					<br>
					<button>Set Base Url</button>
				</div>
				<div class="newTestPartFour testSelectPart">
					<h1 class="title">3.</h1>
					<br>
					Max number of concurrent tests:
					<br>
					<select>
						<option>1</option>
						<option>2</option>
						<option>3</option>
					</select>
				</div>
				<br>
				<div class="newTestPartFive">
					<h1 class="title">5.</h1>
					<br>
					<div style="display: inline-block;">
						<span>Groups:</span>
						<br>
						<div id="groupsPlaceHodler">
						</div>
					</div>
					<div style="display: inline-block;">
						<span>Exclude Groups: </span>
						<br>
						<div id="groupExcludePlaceHolder">
						</div>
					</div>
					<div style="display: inline-block;">
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
					<progress style="color: white; background: #000000; width: 100%;" id="{{id}}Progress" value="0" max="1"></progress>
				</div>
				<div style="color: black; width: 100%; text-align: left;" id="{{id}}Title">
					<h3>
						<span id="{{id}}Folder">{{file}}</span>
						<span id="{{id}}ProgressTxt" >--</span>%
						<div style="float: right;">
							<img onclick="deleteSearch('{{id}}');" src="../core/img/trashCan.png" style="width: 25px; height: 25px; margin-top: -4px; cursor: pointer;">
						</div>
					</h3>
				</div>
				<div id="{{id}}FoundThings" style="background-color: grey; max-height: 400px; border: 1px solid black; margin-top: 10px; overflow-y: scroll; display: none;">

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
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";

		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			showStartTestNewPopup();
		});
	</script>
	<?php readfile('../core/html/popup.html') ?>
</body>