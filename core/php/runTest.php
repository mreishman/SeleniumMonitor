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

$arrayOfArrays = array();

$output = shell_exec("cd ".$locationOfSelenium." && phpunit ".$file." --filter ".$filter);
$output = explode(PHP_EOL, $output);

$arrayOfArrays["output"] = $output;

$result = $output[5];
$resultString = "?";
if(substr( $result, 0, 1 ) === ".")
{
	$resultString = "Passed";
}
elseif(substr( $result, 0, 1 ) === "E")
{
	$resultString = "Error";
}
elseif(substr( $result, 0, 1 ) === "F")
{
	$resultString = "Failed";
}

$arrayOfArrays["Result"] = $resultString;


echo json_encode($arrayOfArrays);