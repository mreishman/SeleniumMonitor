<?php
require_once("commonFunctions.php");
//$file = file($_POST['file']);
$file = file("/var/www/html/app/code/local/Goed/Phpyre/Phpunit/Model/Tests/Selenium/AddToCartTest.php");
$arrayOfArrays = array();
$arrayOfGroups = array();
$arrayOfTests = array();


for ($i=0; $i < count($file); $i++)
{ 
	if(strpos($file[$i], "@group") !== false)
	{
		$line = filterGroupname($file[$i]);
		if(!in_array($line, $arrayOfGroups))
		{
			array_push($arrayOfGroups, $line);
		}
	}
}

for ($i=0; $i < count($file); $i++)
{ 
	if(strpos($file[$i], "public function") !== false)
	{
		$line = filterFunctionName($file[$i]);
		if(!in_array($line, $arrayOfTests))
		{
			array_push($arrayOfTests, $line);
		}
	}
}

$arrayOfArrays['arrayOfGroups'] = $arrayOfGroups;
$arrayOfArrays['arrayOfTests'] = $arrayOfTests;

echo json_encode($arrayOfArrays);