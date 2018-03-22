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
$localURL = $baseUrl;
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/loadVars.php');
require_once('../core/php/updateCheck.php');
?>
<!doctype html>
<head>
	<title>Settings | FAQ</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="../core/img/favicon.png" />
	<script src="../core/js/jquery.js"></script>
</head>
<body>
	<?php require_once('header.php'); ?>
	<div id="main">
		<div class="settingsHeader">
			FAQ
		</div>
		<div class="settingsDiv" >
			<ul id="settingsUl">
				<li id="howSetupBaseUrl">
					Q: How do I setup base url?
				</li>
				<li>
					A: In your testing code, before using a get(page), get the base url being passed through by in the global vars $argv and $argc.
					<br>
					Ex: if($argv[$i] === "--exclude-group"){ $baseURL = $argv[$i+1]; }
				</li>
				<li id="howSetupVideoLink">
					Q: How do I link video to test?
				</li>
				<li>
					A: In the testing code, when creating driver add code that shows the driver session id with test name:
					<br>
					Ex: log("SESSION_LINK_FOR_SELENIUM_MONITOR::::: ".$driver->getSessionID()."   :::::   ".$testName);
					Note: testname is from debug_backtrace where function call name includes 'test'
				</li>
			</ul>
		</div>
	</div>
</body>