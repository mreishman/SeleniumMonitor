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

$logTimes = getAllTestLogFileTimes("../tmp/tests/");

$logTimeArray = array();
foreach ($logTimes as $key => $value)
{
	if(!isset($logTimeArray[$value]))
	{
		$logTimeArray[$value] = $key;
	}
	else
	{
		$logTimeArray[$value.random_int(1, 256)] = $key;
	}
}

?>
<!doctype html>
<head>
	<title>SeleniumMonitor | Tests</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="../core/template/runcss.css?v=<?php echo $cssVersion; ?>">
	<script src="../core/js/jquery.js"></script>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="20px"> </a>
		<?php require_once("../core/php/template/otherLinks.php");?>
	</div>


	<div id="main" style="background-color: #333;">
		<div id="testSidebar" style="position: fixed; bottom: 0; left: 0; background-color: #CCC; overflow: auto; width: 200px;">
			<ul style="list-style: none; padding: 0;">
				<?php foreach ($logTimeArray as $key => $value)
				{
					echo "<li style=\"padding: 5px;\"><a href=\"#".$value."\" class=\"link\" >".$value."</a></li>";
				}
				?>
			</ul>
		</div>
		<div id="subMain" style="margin-left: 200px;">
			<?php foreach ($logTimeArray as $key => $value)
			{

			}
			?>
		</div>
	</div>

	<div id="storage">
		<div class="container">
			<div style="background-color: white; border: 1px solid black;" id="{{id}}" class="scanBar containerMain">
				<div  class="fontChange" style="width: 100%; text-align: left;" id="{{id}}Title">
					<h3>
						{{logFile}}
					</h3>
					<div style="font-size: 200%;">
						<img class="imageInHeaderContainer" onclick="removeCompare('{{id}}');" src="../core/img/trashCan.png">
					</div>
				</div>
				<div id="{{id}}ProgressBlocks" class="containerBox" style="text-align: left;">
					{{ProgressBlocks}}
				</div>
				<div class="key fontChange">
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
				<div class="fontChange subTitleEF">
					<span id="{{id}}FailCount">{{failCount}}</span>/{{totalCount}} Fails
					<br>
					<span id="{{id}}ErrorCount">{{errorCount}}</span>/{{totalCount}} Errors
					<br>
					{{website}}
					<br>
					{{file}}
				</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function()
		{
			resize();
			window.onresize = resize;

			setInterval(function(){poll();},10000);

		});

		var arrayOfFiles = {};

		function poll()
		{
			var urlForSendInner = '../core/php/getFileTimeForAllLogs.php?format=json';
			var dataSend = {};
			$.ajax(
			{
				url: urlForSendInner,
				dataType: "json",
				data: dataSend,
				type: "POST",
				success(data)
				{
					//compare to arrayOfFiles
					var keysInfo = Object.keys(data);
					var keysInfoLength = keysInfo.length;
					for(var i = 0; i < keysInfoLength; i++)
					{
						if(keysInfo[i] in arrayOfFiles)
						{
							//compare
							if(arrayOfFiles[keysInfo[i]] !== data[keysInfo[i]])
							{
								//file is new, update
								arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
								getLogData(keysInfo[i]);
							}
						}
						else
						{
							//not there, add it
							arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
							getLogData(keysInfo[i]);
						}
					}

					//check oppsite (if any keys are in arrayOfFiles but not data, if so delete from screen and local array)
					keysInfo = Object.keys(arrayOfFiles);
					keysInfoLength = keysInfo.length;
					for(var i = 0; i < keysInfoLength; i++)
					{
						if(!(keysInfo[i] in data))
						{
							//not in data anymore, it was deleted from folder... delete from screen & array
							delete arrayOfFiles.keysInfo[i];
							if(document.getElementById(keysInfo[i]))
							{
								document.getElementById(keysInfo[i]).outerHTML = "";
							}

						}
					}
				}
			});
		}

		function getLogData(path)
		{
			var urlForSendInner = '../core/php/getLogInfo.php?format=json';
			var dataSend = {path: "../../tmp/tests/"+path};
			
			(function(_path){
				$.ajax(
				{
					url: urlForSendInner,
					dataType: "json",
					data: dataSend,
					type: "POST",
					success(data)
					{
						if(document.getElementById(_path))
						{
							document.getElementById(_path).outerHTML = "";
						}
						renderInfo = JSON.parse(data);
						showRender("subMain", _path, renderInfo, _path);
						resize();
					}
				});
			}(path));

		}

	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/rend.js?v=<?php echo $cssVersion?>"></script>
	<?php readfile('../core/html/popup.html') ?>
</body>