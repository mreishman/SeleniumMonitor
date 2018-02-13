<?php
$path = "../../tmp/tests/";
$scannedDir = scandir($path);
if(!is_array($scannedDir))
{
	$scannedDir = array($scannedDir);
}
$files = array_diff($scannedDir, array('..', '.'));

$response = array();
if($files)
{
	foreach($files as $k => $filename)
	{
		$fullPath = $path . DIRECTORY_SEPARATOR . $filename;
		if(is_dir($fullPath))
		{
			//$response = sizeFilesInDir($path, $filter, $response, $shellOrPhp);
		}
		elseif(is_file($fullPath))
		{
			$response[$filename] = filemtime($fullPath);
		}
	}
}

echo json_encode($response);