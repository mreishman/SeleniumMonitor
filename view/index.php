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
	<style type="text/css">
		.img-responsive
		{
			width: 100%;
		}
		.mainBox
		{
			width: 333px;
			height: 450px;
			display: inline-table;
			background-color: #777;
			padding: 10px;
			margin: 20px;
			border: 2px solid white;
			border-radius: 15px;
			box-shadow: 5px 5px 5px black;
		}
		.jumbotron
		{
			border: 1px solid white;
		}
		.videos
		{
			text-decoration: none;
			list-style: none;
			margin: 0;
			padding: 0;
		}
		a
		{
			color: white;
		}
		a:visited
		{
			color: white;
		}
		.menu
		{
			margin: 0;
			padding: 0;
			list-style: none;
			display: inline-block;
			vertical-align: bottom;
			color: white;
		}
		.menu li
		{
			float: left;
			padding: 4px;
			background-color: #555;
			border-radius: 10px 10px 0 0;
			margin: 0 4px 0 4px;
			cursor: pointer;
		}
		.menu .active
		{
			color: black;
			background-color: white;
		}
		.conainerSub
		{
			height: 200px;
			overflow-y: auto;
			word-wrap:break-word; 
			word-break: break-all;
		}
		.busy
		{
			opacity: 0.4;
			filter: alpha(opacity=40);
		}
	</style>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="main">
		
	</div>

	<div id="storage">
		<div class="server">
			<div id="{{id}}" class="mainBox">
				<div>
					<h2 style="font-size: 150%;">{{title}}</h2>
				</div>
				<div id="{{id}}Jumbotron" class="jumbotron">
					<img src="../core/img/loading.gif" style="width: 75px; height: 75px; margin-left: 110px; margin-top: 75px; margin-bottom: 75px;">
				</div>
				<div style="border-bottom: 1px solid white;">
					<ul class="menu">
						<li id="{{id}}StatsMenu" onclick="toggleTab('{{id}}', 'Stats');">
							Stats
						</li>
						<li id="{{id}}VideosMenu" onclick="toggleTab('{{id}}', 'Videos');">
							Videos
						</li>
						<li id="{{id}}ActivityMenu" onclick="toggleTab('{{id}}', 'Activity');" class="active">
							Activity
						</li>
						<li id="{{id}}ConfigMenu" onclick="toggleTab('{{id}}', 'Config');">
							Config
						</li>
						<li id="{{id}}ActionsMenu" onclick="toggleTab('{{id}}', 'Actions');">
							Actions
						</li>
					</ul>
				</div>
				<div class="conainerSub" id="{{id}}Videos" style="display: none;">
				</div>
				<div class="conainerSub" id="{{id}}Stats"  style="display: none;">
				</div>
				<div class="conainerSub" id="{{id}}Activity">
					{{activity}}
				</div>
				<div class="conainerSub" id="{{id}}Config"  style="display: none;">
					{{config}}
				</div>
				<div class="conainerSub" id="{{id}}Actions"  style="display: none;">
					<button>Send Reboot</button>
					<button>Go to 3000</button>
					<button>Go to 4444</button>
					<button>Go to 5555</button>
				</div>
			</div>
		</div>
	</div>
	<form id="settingsInstallUpdate" action="update/updater.php" method="post" style="display: none"></form>
	<script>

		<?php
		echo "var autoCheckUpdate = ".$autoCheckUpdate.";";
		echo "var dateOfLastUpdate = '".$configStatic['lastCheck']."';";
		echo "var daysSinceLastCheck = '".$daysSince."';";
		echo "var daysSetToUpdate = '".$autoCheckDaysUpdate."';";
		echo "var pollingRate = ".$pollingRate.";";
		?>
		var dontNotifyVersion = "<?php echo $dontNotifyVersion;?>";
		var currentVersion = "<?php echo $configStatic['version'];?>";
		var popupSettingsArray = JSON.parse('<?php echo json_encode($popupSettingsArray); ?>');
		var updateNoticeMeter = "<?php echo $updateNoticeMeter;?>";
		var baseUrl = "<?php echo $baseUrl;?>";
		var ipOfMainServer = "<?php echo $mainServerIP;?>";

	</script>
	<?php readfile('../core/html/popup.html') ?>
	<script src="../core/js/update.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/view.js?v=<?php echo $cssVersion?>"></script>
	<script type="text/javascript">
		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			poll();

			setInterval(function(){pollTwo();},30000);
			setInterval(function(){poll();},250);
		});
	</script>
</body>