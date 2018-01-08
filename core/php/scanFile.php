<?php
require_once("commonFunctions.php");
$file = file($_POST['file']);
$arrayOfDuplicates = array();


$arrayOfTests = returnArrayOfTests($file);

foreach ($arrayOfTests as $testName)
{
	$innerArray = array();
	foreach ($arrayOfTests as $testCompare)
	{
		if(strpos($testCompare, $testName) !== false && $testName !== $testCompare)
		{
			if(!isset($arrayOfDuplicates[$testName]))
			{
				$arrayOfDuplicates[$testName] = array($testCompare);
			}
			else
			{
				array_push($arrayOfDuplicates[$testName], $testCompare);
			}
		}
	}
}



if($arrayOfDuplicates === array())
{
	echo json_encode("No Duplicate Tests Found");
	exit();
}

echo json_encode($arrayOfDuplicates);
exit();