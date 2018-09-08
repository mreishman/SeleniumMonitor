<?php
require_once("commonFunctions.php");

$arrayOfArrays = array();
$arrayOfFinalTests = array();
$filterInclude = array();
$filterExclude = array();

$groupsIncludeArray = $_POST['groupsInclude'];
if($groupsIncludeArray == "empty array")
{
	$groupsIncludeArray = array();
}
$groupsExcludeArray = $_POST['groupsExclude'];
if($groupsExcludeArray == "empty array")
{
	$groupsExcludeArray = array();
}
$files = $_POST['files'];

foreach ($groupsIncludeArray as $key => $value)
{
	array_push($filterInclude, $value["name"]);
}
foreach ($groupsExcludeArray as $key => $value)
{
	array_push($filterExclude, $value["name"]);
}

$arrayOfArrays = array(
	"arrayOfInclude"	=> array(),
	"arrayOfExclude"	=>	array()
);

foreach ($files as $file)
{
	$fileLoaded = file($file["name"]);
	$arrayOfInclude = getAllTestsFromGroup($file["name"], $filterInclude);
	$arrayOfExclude = getAllTestsFromGroup($file["name"], $filterExclude);

	$arrayOfArrays['arrayOfInclude'] = array_merge($arrayOfInclude, $arrayOfArrays['arrayOfInclude']);
	$arrayOfArrays['arrayOfExclude'] = array_merge($arrayOfExclude, $arrayOfArrays['arrayOfExclude']);
}

$count = 0;

foreach ($arrayOfArrays['arrayOfInclude'] as $testKey => $test)
{
	if(!isset($arrayOfArrays['arrayOfExclude'][$testKey]))
	{
		$arrayOfFinalTests[$testKey] = $test;
		$count++;
	}
}
$arrayOfArrays["testList"] = $arrayOfFinalTests;
$arrayOfArrays["testListCount"] = $count;

echo json_encode($arrayOfArrays);