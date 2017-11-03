<?php
require_once("commonFunctions.php");

$baseUrl = "../../core/";
if(file_exists('../../local/layout.php'))
{
	$baseUrl = "../../local/";
	//there is custom information, use this
	require_once('../../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}

require_once($baseUrl.'conf/config.php');
require_once('../../core/conf/config.php');
require_once('../../core/php/configStatic.php');
require_once('../../core/php/loadVars.php');


$filter = $_POST["filter"];
$file = $_POST["file"];

echo json_encode(shell_exec("cd ".$locationOfTests." && phpunit ".$file." --filter ".$filter));