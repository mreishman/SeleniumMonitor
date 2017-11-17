<?php

$imageToDownload = $_POST["imageSrc"];

$return = null;
try 
{
	$return = 	@file_get_contents($imageToDownload);
} catch (Exception $e) {
	
}

echo json_encode($return);