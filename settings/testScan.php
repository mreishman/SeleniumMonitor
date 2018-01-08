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
	<title>SeleniumMonitor | Scan</title>
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


	<div id="main">
		<div class="newTestPopup">
			<div id="{{id}}" class="runNewTest">
				<h2>Select a file to scan for duplicate test names </h2>
				<br>
				<?php if(is_dir($locationOfTests) && !isDirRmpty($locationOfTests)):?>
					<select id="fileListSelector" onchange="scanFile();">
						<option value="PLACEHOLDER">Select A File</option>
						<?php
						$files = array_diff(scandir($locationOfTests), array('..', '.'));
						foreach($files as $key => $value)
						{
							$path = realpath($locationOfTests.DIRECTORY_SEPARATOR.$value);
					        if(!is_dir($path) && returnArrayOfTests(file($path)) !== array())
					        {
					        	echo "<option value='".$path."'' >".$value."</option>";
					        }
						}?>
					</select>
				<?php else: ?>
					Please specifiy a directory of where test are located on the settings page
				<?php endif;?>
				<br>
				<div id="scanResults">

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

		function scanFile()
		{
			var urlForSend = '../core/php/scanFile.php?format=json';
			var valueForFile = document.getElementById("fileListSelector").value;
			if(valueForFile !== "PLACEHOLDER")
			{
				var data = {file: valueForFile };
				$.ajax(
				{
					url: urlForSend,
					dataType: "json",
					data,
					type: "POST",
					success(data)
					{
						if(data === "No Duplicate Tests Found")
						{
							document.getElementById("scanResults").innerHTML = data;
						}
						else
						{
							var htmlForOutput = "";
							var files = Object.keys(data);
							var stop = files.length;
							for(var i = 0; i !== stop; i++)
							{
								htmlForOutput += "<h2>"+files[i]+"</h2> <ul>"
								var stopInner = data[files[i]].length;
								for(var j = 0; j !== stopInner; j++)
								{
									htmlForOutput += "<li>"+data[files[i]][j]+"</li>";
								}
								htmlForOutput += "</ul>";
							}
							document.getElementById("scanResults").innerHTML = htmlForOutput;
						}
					}
				});
			}
			else
			{
				document.getElementById("scanResults").innerHTML = "";
			}
		}

	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
</body>