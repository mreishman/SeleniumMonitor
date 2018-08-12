<?php
require_once("commonFunctions.php");
$files = $_POST['files'];
$arrayOfArrays = array(
	"arrayOfGroups"	=> array(),
	"arrayOfTests"	=>	array()
);

foreach ($files as $file)
{
	$file = file($file["name"]);
	$arrayOfGroups = returnArrayOfGroups($file);
	$arrayOfTests = returnArrayOfTests($file, $file["name"]);

	$arrayOfArrays['arrayOfGroups'] = array_merge($arrayOfGroups, $arrayOfArrays['arrayOfGroups']);
	$arrayOfArrays['arrayOfTests'] = array_merge($arrayOfTests, $arrayOfArrays['arrayOfTests']);
}

echo json_encode($arrayOfArrays);