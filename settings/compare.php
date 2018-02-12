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


	<div id="main" style="background-color: #333;">
		<table width="100%">
			<tr>
				<th width="50%">
					<h2>Test Results 1 (Master)</h2>
					<br>
					<input type="text"> <button>Render</button>
				</th>
				<th width="50%">
					<h2>Test Results 2 (Changes)</h2>
					<br>
					<input type="text"> <button>Render</button>
				</th>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		$(document).ready(function()
		{
			resize();
			window.onresize = resize;
		});

	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
</body>