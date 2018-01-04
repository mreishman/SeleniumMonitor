<?php

$objectToReturn = array();
if(isset($_POST["serverArray"]))
{
	$serverArray = $_POST["serverArray"];

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


	
	$counter = 0;
	
	foreach ($serverArray as $key => $value)
	{
		$ipAddressSend = $value["ip"];
		if(strpos($ipAddressSend, "5555") !== false)
		{
			$ipAddressSend = str_replace("5555", "", $ipAddressSend);
		}
		$return = null;
		try 
		{

			$user_agent='Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

	        $options = array(

	            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
	            CURLOPT_POST           =>false,        //set to GET
	            CURLOPT_USERAGENT      => $user_agent, //set user agent
	            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
	            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
	            CURLOPT_RETURNTRANSFER => true,     // return web page
	            CURLOPT_HEADER         => false,    // don't return headers
	            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
	            CURLOPT_ENCODING       => "",       // handle all encodings
	            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
	            CURLOPT_CONNECTTIMEOUT => $timeoutMain/count($serverArray),      // timeout on connect
	            CURLOPT_TIMEOUT        => $timeoutMain/count($serverArray),      // timeout on response
	            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
	        );

	        $ch      = curl_init( $ipAddressSend."3000" );
	        curl_setopt_array( $ch, $options );
	        $content = curl_exec( $ch );
	        $err     = curl_errno( $ch );
	        $errmsg  = curl_error( $ch );
	        $header  = curl_getinfo( $ch );
	        curl_close( $ch );

	        $header['errno']   = $err;
	        $header['errmsg']  = $errmsg;
	        $header['content'] = $content;
	        $return = $content;

		}
		catch (Exception $e)
		{

		}
		$objectToReturn[$counter] = $return;
		$counter++;
	}
}

if (extension_loaded('zlib') && !ini_get('zlib.output_compression')){
    header('Content-Encoding: gzip');
    ob_start('ob_gzhandler');
}

echo json_encode($objectToReturn);