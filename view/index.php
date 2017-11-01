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
			width: 300px;
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
					<img src="../core/img/loading.gif" style="width: 150px; height: 150px; margin-left: 60px;">
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
	<script type="text/javascript">
		
		var arrayOfData = new Array();
		var heightBase = 0;
		function poll()
		{
			$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
			{
				filterPoll(data);
			});
		}

		function filterPoll(data)
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
				var browsersContentDetail = splitData[i].split("<div type='browsers' class='content_detail'>");
				browsersContentDetail = browsersContentDetail[1].split("</div>");
				browsersContentDetail = browsersContentDetail[0];
				browsersContentDetail = browsersContentDetail.split('/grid/resources/org/openqa/grid/images/').join('../core/img/');
				var browserConfig = splitData[i].split("<div type='config' class='content_detail'>");
				browserConfig = browserConfig[1].split("</div>");
				browserConfig = browserConfig[0];
				if($("#main #"+proxyIdId).length === 0)
				{
					arrayOfData[proxyIdId] = {ip: proxyId, id: proxyIdId};
					var item = $("#storage .server").html();
					item = item.replace(/{{id}}/g, proxyIdId);
					item = item.replace(/{{title}}/g, proxyId);
					item = item.replace(/{{activity}}/g, browsersContentDetail);
					item = item.replace(/{{config}}/g, browserConfig);
					$("#main").append(item);
				}
				else
				{
					document.getElementById(proxyIdId+"Activity").innerHTML = browsersContentDetail;
					document.getElementById(proxyIdId+"Config").innerHTML = browserConfig;
				}
			}
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

			var jumbotron = data.split("src='");
			jumbotron = jumbotron[1].split("'>");
			jumbotron = jumbotron[0];
			var newImg = new Image();

		    newImg.onload = function()
		    {
		    	var idOfNewImg = dataExt["id"]+"Jumbotron";
		    	var ratio = newImg.height/newImg.width;
		    	var marginBottom = 10;
		    	var width = 280;
		    	var height = width*ratio;
		    	if(height > heightBase)
		    	{
		    		heightBase = height;
		    	}
		    	else if(height < heightBase)
		    	{
		    		marginBottom = heightBase - height + marginBottom;
		    	}
		    	document.getElementById(idOfNewImg).setAttribute("style","width:"+width+"px; height:"+height+"px; margin-bottom: "+marginBottom+"px;");
		    }

		    newImg.src = jumbotron; 
		    newImg.id = dataExt["id"];
		    jumbotron = data.split("class='jumbotron'>");
			jumbotron = jumbotron[1].split(" <!-- jumbotron -->");
			jumbotron = jumbotron[0];
		   
			document.getElementById(dataExt["id"]+"Jumbotron").innerHTML = jumbotron;


			var videos = data.split("<ul class='videos'>");
			videos = videos[1].split("</ul>");
			videos = videos[0];
			videos = "<ul class='videos'>"+videos+"</ul>";

			document.getElementById(dataExt["id"]+"Videos").innerHTML = videos;


			var stats = data.split("<!-- videos -->");
			stats = stats[1].split("<div class='col-lg-6'>");
			stats = stats[1].split("</div> ");
			stats = stats[0];

			document.getElementById(dataExt["id"]+"Stats").innerHTML = stats;
		}

		function resize() 
		{
			var targetHeight = window.innerHeight;
			if($("#main").outerHeight() !== targetHeight)
			{
				$("#main").outerHeight(targetHeight);
			}
		}

		function toggleTab(currentId, tabIdToShow)
		{
			$("#"+currentId+" .menu li").removeClass("active");
			$("#"+currentId+" .conainerSub").hide();

			$("#"+currentId+tabIdToShow).show();
			$("#"+currentId+tabIdToShow+"Menu").addClass("active");
		}

		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			poll();
			pollTwo();

			setInterval(function(){pollTwo();},3000);
			setInterval(function(){poll();},1500);
		});

	</script>
</body>