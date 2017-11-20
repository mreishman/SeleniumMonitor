<?php
$ipAddressSend = $_POST["ip"];

$ctx = stream_context_create(array('http'=>
    array(
        'timeout' => 6,
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