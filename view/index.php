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
			max-width: 300px;
			width: 300px;
			height: 300px;
			max-height: 300px;
		}
	</style>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="main">
		
	</div>

	<div id="storage">
		<div class="server">
			<div id="{{id}}" style="width: 300px; height: 300px; display: inline-block; background-color: blue;">
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

	</script>
	<?php readfile('../core/html/popup.html') ?>
	<script src="../core/js/update.js?v=<?php echo $cssVersion?>"></script>
	<script type="text/javascript">
		
		var arrayOfData = new Array();


		$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
		{
			poll(data);
		});

		function poll(data)
		{
			var splitData = data.split("<div class='proxy'>");
			for (var i = 1; i < splitData.length; i++)
			{
				var proxyId = splitData[i].split("proxyid");
				proxyId = proxyId[1].split("http");
				proxyId = proxyId[1].split(",");
				proxyId = "http"+proxyId[0];
				var proxyIdId = proxyId.split('.').join('point');
				proxyIdId = proxyIdId.split(':').join('colon');
				proxyIdId = proxyIdId.split('/').join('forwardSlash');
				if($("#main #"+proxyIdId).length === 0)
				{
					arrayOfData[proxyIdId] = {ip: proxyId, id: proxyIdId};
					var item = $("#storage .server").html();
					item = item.replace(/{{id}}/g, proxyIdId);
					$("#main").append(item);
				}
			}
			pollTwo();
		}

		function pollTwo()
		{
			var servers = Object.keys(arrayOfData);
			var stop = servers.length;
			for(var i = 0; i !== stop; ++i)
			{
				var data = arrayOfData[servers[i]];
				var urlForSend = "../core/php/getMainHostInfo.php?format=json";
				(function(_data){
					$.ajax(
					{
						url: urlForSend,
						dataType: "json",
						data,
						type: "POST",
						success(data)
						{
							filterAndShow(data, _data);
						},
					});
				}(data));
			}
		}
		

		function filterAndShow(data, dataExt)
		{
			var title = data.split("title>");
			title = title[1].split("</title");
			title = title[0];

			var header = data.split("class='container'");
			header = header[1];

			var jumbotron = data.split("class='jumbotron'>");
			jumbotron = jumbotron[1].split(" <!-- jumbotron -->");
			jumbotron = jumbotron[0];

			document.getElementById(dataExt["id"]).innerHTML = jumbotron;
		}

	</script>
</body>