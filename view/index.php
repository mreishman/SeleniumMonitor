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
	$url = "http://" . $_SERVER['HTTP_HOST'] .substr($partOfUrl,0,5) ."setup/welcome.php";
	header('Location: ' . $url, true, 302);
	exit();
}
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/loadVars.php');
require_once('../core/php/updateCheck.php');

$daysSince = calcuateDaysSince($configStatic['lastCheck']);

if($pollingRateTypeView == 'Seconds')
{
	$pollingRateView *= 1000;
}
if($backgroundPollingRateType == 'Seconds')
{
	$backgroundPollingRate *= 1000;
}

?>
<!doctype html>
<head>
	<title>SeleniumMonitor | View</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="../core/template/viewcss.css?v=<?php echo $cssVersion; ?>">
	<script src="../core/js/jquery.js"></script>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a class="active">View</a>
		<a href="../run/">Run</a>
		<a href="../settings/"> Settings </a>
		<?php require_once("../core/php/template/otherLinks.php");?>
	</div>


	<div id="main">
		
	</div>

	<div id="storage">
		<div class="server">
			<div id="{{id}}" class="mainBox">
				<div id="{{id}}Title">
					<h2 style="font-size: 150%;">{{title}}</h2>
				</div>
				<div id="{{id}}Jumbotron" class="jumbotron">
					<div class="jumboTronNoVideo" style="display: none;" id="{{id}}Disconnected">No Video Feed / Disconnected</div>
					<span onclick="showPopup('{{id}}')" style="cursor: pointer;" id="{{id}}JumbotronImageSpan">
						<img class='img-responsive' src="../core/img/static.gif">
					</span>
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
					<ul class="linkActions">
						<li>
							<a class="link" onclick="rebootMachine('{{linkOne}}');">Send Reboot</a>
						</li>
						<li>
							<a class="link" href="{{linkOne}}">Go to 3000</a>
						</li>
						<li>
							<a class="link" href="{{linkTwo}}">Go to 4444</a>
						</li>
						<li>
							<a class="link" href="{{linkThree}}">Go to 5555</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="popup">
			<div id="popup" class="mainBoxPopup">
				<table style="width: 100%; height: 100%;">
					<tr>
						<td id="{{id}}JumbotronHolder">
							<div id="{{id}}Jumbotron" class="jumbotron">
								<span id="{{id}}JumbotronImageSpan">
									<img src="../core/img/static.gif">
								</span>
							</div>
						</td>
						<td id="{{id}}" width="240px" style="vertical-align: top;">
							<span id="popupSpanLeftHeight" style="display: block;">
								<div onclick="hidePopupWindow();" class="link" style="width: 100%; text-align: center; margin-bottom: 10px;" >Close Popup</div>
								<br>
								<div  id="{{id}}Title">
									<h2 style="font-size: 150%;">{{title}}</h2>
								</div>
								<div class="jumboTronNoVideo" style="display: none; width:240px; margin-top: 0; position: relative;" id="{{id}}Disconnected">
								No Video Feed / Disconnected
								</div>
								<div id="{{id}}Activity">
									{{activity}}
								</div>
								<div style="border-bottom: 1px solid white;">
									<ul class="menu">
										<li id="{{id}}StatsMenu" onclick="toggleTab('{{id}}', 'Stats');">
											Stats
										</li>
										<li id="{{id}}VideosMenu" onclick="toggleTab('{{id}}', 'Videos');">
											Videos
										</li>
										<li id="{{id}}ConfigMenu" class="active" onclick="toggleTab('{{id}}', 'Config');">
											Config
										</li>
										<li id="{{id}}ActionsMenu" onclick="toggleTab('{{id}}', 'Actions');">
											Actions
										</li>
									</ul>
								</div>
							</span>
							<div class="conainerSub" id="{{id}}Config">
								{{config}}
							</div>
							<div class="conainerSub" id="{{id}}Actions"  style="display: none;">
								{{linkAction}}
							</div>
							<div class="conainerSub" id="{{id}}Videos" style="display: none;">
								{{videos}}
							</div>
							<div class="conainerSub" id="{{id}}Stats"  style="display: none;">
								{{stats}}
							</div>
						</td>
					</tr>
				</table>
			</div>
			<div id="popupBackground" class="popupBackground">
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
		echo "var pollingRateView = ".$pollingRateView.";";
		echo "var backgroundPollingRate = ".$backgroundPollingRate.";";
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

			setInterval(function(){pollTwo();},backgroundPollingRate);
			setInterval(function(){poll();},pollingRateView);
		});
	</script>
</body>