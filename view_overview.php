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
	<?php
	require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="20px"> </a>
		<?php require_once("../core/php/template/otherLinks.php");?>
	</div>
	

	<div id="main" style="right: 0; top: 62px;">
		<canvas class="canvasMonitor" id="useageCanvas" width="300" height="300" style="height: 350px; border: 0;" ></canvas>
		<table width="100%">
			<tr>
				<td width="50%">
					<span style="font-size: 200%;" >Instances: <i><span id="currentRunTest" >0</span>/<span id="currentMaxNodeTot" >24</span></i> Nodes: <i><span id="currentRunNodes" >0</span>/<span id="currentNodeCount">6</span></i></span>
					<br>
					<br>
					<span id="browserNodeInfo" ></span>
				</td>
				<td width="50%">
					<span id="logHolder" style="display: block; height: 500px; overflow: auto; background-color: #222;">
					</span>
				</td>
			</tr>
		</table>
	</div>

	<div id="storage">
			
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
		var logData = "";
		var gettingLogData = false;

		

		$(document).ready(function()
		{
			resizeGraph();
			window.onresize = resizeGraph;

			var sideBarStuffPoll = setInterval(function(){overviewStuff();},1000);
			var logStuffPoll = setInterval(function(){getLogData();},3000);
		});

		function resizeGraph()
		{
			document.getElementById("useageCanvas").style.width = ""+window.innerWidth+"px";
		}

		function getLogStuff()
		{
			if(!gettingLogData)
			{
				gettingLogData = true;
				setTimeout(function(){ getLogData(); }, 1000);
			}

		}

		function overviewStuff()
		{
			$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
			{
				if(data)
				{
					overviewDisplayLogic(data);
				}
			});
		}

		function overviewDisplayLogic(data)
		{
			graphLogic(data);
			browserNodeInfoLogicOverview(data);
			 speedOmLogic(data);
		}

		function browserNodeInfoLogicOverview(data)
		{
			var browserList = getListOfBrowsers(data);
			var html = "";
			for(var i = 0; i < browserList.length; i++)
			{
				var browserNow = browserList[i];
				if(browserNow === "internet explorer")
				{
					browserNow = "internet-explorer";
				}
				var currentBrowserCount = getCurrentBrowserCount(data, browserList[i]);
				var totalBrowserCount = getCurrentBrowserCountTotal(data, browserNow);
				var browserSrc = "../core/img/chrome-hr.png";
				if(browserList[i] === "safari")
				{
					browserSrc = "../core/img/safari-hr.png";
				}
				else if(browserList[i] === "internet explorer")
				{
					browserSrc = "../core/img/internet-explorer-hr.png";
				}
				else if(browserList[i] === "firefox")
				{
					browserSrc = "../core/img/firefox-hr.png";
				}
				else if(browserList[i] === "MicrosoftEdge")
				{
					browserSrc = "../core/img/MicrosoftEdge-hr.png";
				}
				else if(browserList[i] === "opera")
				{
					browserSrc = "../core/img/opera-hr.png";
				}
				var counter = 0;
				for(counter; counter < (totalBrowserCount-currentBrowserCount); counter++)
				{
					html += "<img src=\""+browserSrc+"\" width=\"45px\" height=\"45px\" >";
				}

				for(counter; counter < totalBrowserCount; counter++)
				{
					html += "<img style=\"opacity: 0.5;\" src=\""+browserSrc+"\" width=\"45px\" height=\"45px\" >";
				}
				html += "<br>";

			}
			$("#browserNodeInfo").html(html);

		}

		function logDataParse()
		{
			var text = logData.split("\n");
			text = text.join('</div><div>');
			$("#logHolder").html('<div>' + text + '</div>');
			var objDiv = document.getElementById("logHolder");
			objDiv.scrollTop = objDiv.scrollHeight;
		}

	</script>
	<?php readfile('../core/html/popup.html') ?>
	<script src="../core/js/update.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/sidebar.js?v=<?php echo $cssVersion?>"></script>
</body>