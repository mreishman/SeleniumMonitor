<?php
$ipAddressSend = $_POST["ip"];

require_once('../../core/php/commonFunctions.php');

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

$timeoutMain = $defaultConfig['timeoutViewMain'];
if(isset($config['timeoutViewMain']))
{
	$timeoutMain = $config['timeoutViewMain'];
}

$ctx = stream_context_create(array('http'=>
    array(
        'timeout' => $timeoutMain,
    )
));

if(strpos($ipAddressSend, "5555") !== false)
{
	$ipAddressSend = str_replace("5555", "", $ipAddressSend);
}
$return = null;
try 
{
	$return = 	@file_get_contents($ipAddressSend."3000", false, $ctx);
} catch (Exception $e) {
	
}
echo json_encode($return);