<?php
error_reporting(E_ALL);
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


$filterBase = $_POST["filter"];
$fileBase = $_POST["file"];
$paramaters = $_POST["paramaters"];
$numberOfTestsToRun = $_POST["numberOfTestsToRun"];

$arrayOfArraysOfArrays = array();

$handleObject = array();
for ($i=0; $i < $numberOfTestsToRun; $i++)
{ 
	$file = $fileBase[$i];
	$filter = $filterBase[$i];
	$command = "cd ".$locationOfSelenium." && phpunit ".$file." --filter ".$filter." --exclude-group ".$paramaters." 2>&1";
	$handleObject[$i] = popen($command, 'r');
}

for ($i=0; $i < $numberOfTestsToRun; $i++)
{
	$output = stream_get_contents($handleObject[$i]);
	$output = explode(PHP_EOL, $output);

	$arrayOfArraysOfArrays[$i]["output"] = $output;
	$arrayOfArraysOfArrays[$i]["timeMem"] = "?";
	if(isset($output[7]))
	{
		$arrayOfArraysOfArrays[$i]["timeMem"] = $output[7];
	}

	$result = "E";
	if(isset($output[5]))
	{
		$result = $output[5];
	}

	$message = "";
	$resultString = "Error";
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
	elseif(substr( $result, 0, 1 ) === "I")
	{
		$resultString = "Skipped";
	}
	elseif(substr( $result, 0, 1 ) === "R")
	{
		$resultString = "Risky";
	}

	$arrayOfArraysOfArrays[$i]["Result"] = $resultString;
}

for ($i=0; $i < $numberOfTestsToRun; $i++)
{ 
	$file = $fileBase[$i];
	$filter = $filterBase[$i];
	pclose($handleObject[$i]);
}

echo json_encode($arrayOfArraysOfArrays);