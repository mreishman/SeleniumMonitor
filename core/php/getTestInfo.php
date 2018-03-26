<?php
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
require_once('../../core/php/loadVars.php');
$returnArray = array();
foreach ($_POST['sessions'] as $session)
{
	$data = file_get_contents("http://".$mainServerIP.":4444/grid/api/testsession?session=".$session);
	$data = json_decode($data);
	$data = get_object_vars($data);
	if(!isset($data["session"]))
	{
		$data["session"] = $session;
	}
	array_push($returnArray,$data);
}
echo json_encode($returnArray);