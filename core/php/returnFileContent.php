<?php
require_once("commonFunctions.php");
$file = file($_POST['file']);
$arrayOfArrays = array();
$arrayOfGroups = returnArrayOfGroups($file);
$arrayOfTests = returnArrayOfTests($file);

$arrayOfArrays['arrayOfGroups'] = $arrayOfGroups;
$arrayOfArrays['arrayOfTests'] = $arrayOfTests;

echo json_encode($arrayOfArrays);