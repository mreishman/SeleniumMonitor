<?php
$baseUrl = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrl = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
$localURL = $baseUrl;
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/updateCheck.php');
require_once('../core/php/loadVars.php');
require_once('../core/php/commonFunctions.php');
?>
<!doctype html>
<head>
	<title>Settings | Test List</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="../core/img/favicon.png" />
	<script src="../core/js/jquery.js"></script>
	<style type="text/css">
		.settingsUl{
			list-style: none;
		}
		.settingsUl li{
			padding: 5px;
		}
	</style>
</head>
<body>

<?php require_once('header.php');?>	

	<div id="main">
		<?php
			require_once("../core/php/template/settingsTestList.php");
		?>
	</div>
	<?php readfile('../core/html/popup.html') ?>	
</body>
<script src="../core/js/settingsWatchlist.js"></script>