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

?>
<!doctype html>
<head>
	<title>SeleniumMonitor | Compare</title>
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

	<?php
	$path = "../tmp/tests";
	$scannedDir = scandir($path);
	if(!is_array($scannedDir))
	{
		$scannedDir = array($scannedDir);
	}
	$files = array_diff($scannedDir, array('..', '.'));
	if(!$files)
	{
		$files = array();
	}
	?>


	<div id="main" style="background-color: #333;">
		<table width="100%">
			<tr>
				<th width="50%">
					<h2>Test Results 1 (Master)</h2>
					<br>
					<input type="text" id="testResultInputOne"> <button onclick="showRenderStart('testResultDisplayOne','testResultInputOne');">Render</button>
					<br>
					OR
					<br>
					<select id="testResultSelectOne">
						<?php
						foreach ($files as $k => $fileName)
						{
							echo "<option value=\"".$path.DIRECTORY_SEPARATOR.$fileName."\" >".$fileName."</option>";
						}
						?>
					</select>
					<button onclick="showRenderFromFile('testResultDisplayOne','testResultSelectOne')" >Render</button>
				</th>
				<th width="50%">
					<h2>Test Results 2 (Changes)</h2>
					<br>
					<input type="text" id="testResultInputTwo"> <button  onclick="showRenderStart('testResultDisplayTwo','testResultInputTwo');">Render</button>
					<br>
					OR
					<br>
					<select id="testResultSelectTwo" >
						<?php
						foreach ($files as $k => $fileName)
						{
							echo "<option value=\"".$path.DIRECTORY_SEPARATOR.$fileName."\" >".$fileName."</option>";
						}
						?>
					</select>
					<button onclick="showRenderFromFile('testResultDisplayTwo','testResultSelectTwo')" >Render</button>
				</th>
			</tr>
			<tr>
				<th style="vertical-align: top;" id="testResultDisplayOne">
				</th>
				<th style="vertical-align: top;" id="testResultDisplayTwo">
				</th>
			</tr>
		</table>
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
		});

		function showRenderStart(divId, renderId)
		{
			displayLoadingPopup();
			var renderInfo = document.getElementById(renderId).value;
			renderInfo = JSON.parse(renderInfo);
			showRender(divId, divId, renderInfo, "" );
			document.getElementById(renderId).value = "";
			hidePopup();
		}

		function showRenderFromFile(divId, renderId)
		{
			displayLoadingPopup();
			var urlForSendInner = '../core/php/getLogInfo.php?format=json';
			var dataSend = {path: "../"+document.getElementById(renderId).value};
			$.ajax(
			{
				url: urlForSendInner,
				dataType: "json",
				data: dataSend,
				type: "POST",
				success(data)
				{
					renderInfo = JSON.parse(data);
					showRender(divId, divId ,renderInfo, document.getElementById(renderId).value);
					hidePopup();
				}
			});
		}

		function removeCompare(id)
		{
			document.getElementById(id).innerHTML = "";
		}

	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/rend.js?v=<?php echo $cssVersion?>"></script>
	<?php readfile('../core/html/popup.html') ?>
</body>