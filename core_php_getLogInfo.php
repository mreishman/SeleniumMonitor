<?php
if(isset($_POST["lineCount"]))
{
	$returnData = json_encode(trim(shell_exec('tail -n ' . $_POST["lineCount"] . ' "' . $_POST["path"] . '"')));
}
else
{
	$returnData = file_get_contents($_POST["path"]);
}
echo json_encode($returnData);