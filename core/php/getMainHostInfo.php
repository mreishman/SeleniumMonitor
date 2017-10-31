<?php
$ipAddressSend = $_POST["ip"];
if(strpos($ipAddressSend, "5555") !== false)
{
	$ipAddressSend = str_replace("5555", "", $ipAddressSend);
}
echo json_encode(file_get_contents($ipAddressSend."3000"));