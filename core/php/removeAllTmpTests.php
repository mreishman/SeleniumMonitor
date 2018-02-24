<?php
$dir = "../../tmp/tests/";
$files = array_diff(scandir($dir), array('..', '.'));
foreach($files as $key => $value)
{
	$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
    if(is_file($path))
    {
    	unlink($path);
    }
}
echo json_encode(true);