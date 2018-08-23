<?php

function filePermsDisplay($key)
{
	$info = "u---------";
	if(file_exists($key))
	{
		$info = returnActualFilePerms($key);
	}
	return $info;
}

function returnActualFilePerms($key)
{
	$perms  =  fileperms($key);

	switch ($perms & 0xF000)
	{
	    case 0xC000: // socket
	        $info = 's';
	        break;
	    case 0xA000: // symbolic link
	        $info = 'l';
	        break;
	    case 0x8000: // regular
	        $info = 'f';
	        break;
	    case 0x6000: // block special
	        $info = 'b';
	        break;
	    case 0x4000: // directory
	        $info = 'd';
	        break;
	    case 0x2000: // character special
	        $info = 'c';
	        break;
	    case 0x1000: // FIFO pipe
	        $info = 'p';
	        break;
	    default: // unknown
	        $info = 'u';
	}

	$filePermsArray = array(
		"Owner" => array(
			"Read"		=> array(
				"Boolval"	=> ($perms & 0x0100)
			),
			"Write"		=> array(
				"Boolval"	=>	($perms & 0x0080)
			),
			"Execute"	=> array(
				"Boolval"	=>	($perms & 0x0040),
				"Boolval2"	=>	($perms & 0x0800)
			)
		),
		"Group" => array(
			"Read"		=> array(
				"Boolval"	=> ($perms & 0x0020)
			),
			"Write"		=> array(
				"Boolval"	=>	($perms & 0x0010)
			),
			"Execute"	=> array(
				"Boolval"	=>	($perms & 0x0008),
				"Boolval2"	=>	($perms & 0x0400)
			)
		),
		"Owner" => array(
			"Read"		=> array(
				"Boolval"	=> ($perms & 0x0004)
			),
			"Write"		=> array(
				"Boolval"	=>	($perms & 0x0002)
			),
			"Execute"	=> array(
				"Boolval"	=>	($perms & 0x0001),
				"Boolval2"	=>	($perms & 0x0200)
			)
		),
	);

	foreach ($filePermsArray as $key => $value)
	{
		$info .= evaluateBool(
			$value["Read"]["Boolval"],
			"r",
			"-"
		);
		$info .= evaluateBool(
			$value["Write"]["Boolval"],
			"w",
			"-"
		);
		$info .= evaluateBool(
			$value["Execute"]["Boolval"],
			evaluateBool(
				$value["Execute"]["Boolval2"],
				"s",
				"x"
			),
			evaluateBool(
				$value["Execute"]["Boolval2"],
				"S",
				"-"
			)
		);
	}
	return $info;
}

function evaluateBool($boolVal, $trueVal, $falseVal)
{
	if($boolVal)
	{
		return $trueVal;
	}
	return $falseVal;
}

function loadSentryData($sendCrashInfoJS, $branchSelected)
{
	return "
	<script>

		function eventThrowException(e)
		{
			//this would send errors, but it is disabled
		}

	</script>";
}

function baseURL()
{
	$tmpFuncBaseURL = "";
	$boolBaseURL = file_exists($tmpFuncBaseURL."error.php");
	while(!$boolBaseURL)
	{
		$tmpFuncBaseURL .= "../";
		$boolBaseURL = file_exists($tmpFuncBaseURL."error.php");
	}
	return $tmpFuncBaseURL;
}

function clean_url($url)
{
    $parts = parse_url($url);
    return $parts['path'];
}

function loadCSS($baseUrl, $version)
{
	return "<link rel=\"stylesheet\" type=\"text/css\" href=\"".$baseUrl."template/theme.css?v=".$version."\">";
}

function loadVisibilityJS($baseURL)
{
	return "<script src=\"".$baseURL."core/js/visibility.core.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.fallback.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.js\"></script>
	<script src=\"".$baseURL."core/js/visibility.timers.js\"></script>";
}

function calcuateDaysSince($lastCheck)
{
	$today = date('Y-m-d');
	$old_date = $lastCheck;
	$old_date_array = preg_split("/-/", $old_date);
	$old_date = $old_date_array[2]."-".$old_date_array[0]."-".$old_date_array[1];

	$datetime1 = date_create($old_date_array[2]."-".$old_date_array[0]."-".$old_date_array[1]);
	$datetime2 = date_create($today);
	$interval = date_diff($datetime1, $datetime2);
	return $interval->format('%a');
}

function findUpdateValue($newestVersionCount, $versionCount, $newestVersion, $version)
{
	for($i = 0; $i < $newestVersionCount; $i++)
	{
		if($i < $versionCount)
		{
			if(isset($newestVersion[$i]) && $newestVersion[$i] !== $version[$i])
			{
				if(intval($newestVersion[$i]) > intval($version[$i]))
				{
					$calcuation = 3-$i;
					return max(1, $calcuation);
				}
				break;
			}
			break;
		}
		return 1;
	}
	return 0;
}

function addResetButton($idOfForm)
{
	return "<a onclick=\"resetArrayObject('".$idOfForm."');\" id=\"".$idOfForm."ResetButton\" style=\"display: none;\" class=\"linkSmall\" > Reset Current Changes</a>";
}

function isDirRmpty($dir)
{
	if(!is_readable($dir))
	{
		return NULL;
	}
  	return (count(scandir($dir)) == 2);
}

function filterFunctionName($line)
{
	$line = str_replace("public function", "", $line);
	$line = str_replace("\r\n", "", $line);
	$line = str_replace(" ", "", $line);
	$line = str_replace("()", "", $line);
	$line = str_replace("{", "", $line);
	$line =  trim(preg_replace('/\t+/', "", $line));
	return $line;
}

function filterGroupname($line)
{
	$line = str_replace("@group", "", $line);
	$line = str_replace("\r\n", "", $line);
	$line = str_replace(" ", "", $line);
	$line = str_replace("*", "", $line);
	$line =  trim(preg_replace('/\t+/', "", $line));
	return $line;
}

function getAllTestsFromGroup($fileName, $groupNameArray)
{
	$file = file($fileName);
	$arrayOfTests = array();
	foreach ($groupNameArray as $groupName)
	{
		for ($i=0; $i < count($file); $i++)
		{ 
			if(strpos($file[$i], "@group") !== false)
			{
				if(strpos($file[$i], "//") === false)
				{
					if(strpos($file[$i], $groupName) !== false)
					{
						//find next public function
						$j = 1;
						$stayInLoop = true;
						while ($stayInLoop)
						{
							if(isset($file[$i+$j]))
							{
								$lineCheckForFunction = $file[$i+$j];
								if(strpos($lineCheckForFunction, "public function") !== false)
								{
									if(strpos($lineCheckForFunction, "test") !== false)
									{
										if(strpos($lineCheckForFunction, "//") === false)
										{
											$line = filterFunctionName($lineCheckForFunction);
											if(!isset($arrayOfTests[$fileName."_".$line]))
											{
												$arrayOfTests[$fileName."_".$line] = array(
													"file"				=> $fileName,
													"name" 				=> $line
												);
											}
										}
									}
									$stayInLoop = false;
								}
								$j++;
							}
							else
							{
								$stayInLoop = false;
							}
						}
					}
				}
			}
		}
	}
	return $arrayOfTests;
}

function checkPhpUnit()
{
	$commandResult = shell_exec("phpunit --version");
	if(strpos(strtolower($commandResult), "command not found") === false && $commandResult !== null)
	{
		return true;
	}
	return false;
}

function returnArrayOfGroups($file)
{
	$arrayOfGroups = array();
	for ($i=0; $i < count($file); $i++)
	{ 
		if(strpos($file[$i], "@group") !== false)
		{
			if(strpos($file[$i], "//") === false)
			{
				$line = filterGroupname($file[$i]);
				if(!isset($arrayOfGroups[$line]))
				{
					$arrayOfGroups[$line] = 1;
				}
				else
				{
					$arrayOfGroups[$line]++;
				}
			}
		}
	}
	return $arrayOfGroups;
}

function scanDirForTests($dir, $showSubFolderTests)
{
	$stuffToReturn = "<ul style=\"list-style: none;\" >No Files Found In Directory";
	$files = array_diff(scandir($dir), array('..', '.'));
	if($files !== array())
	{
		$stuffToReturn = "<ul style=\"list-style: none;\" >";
		foreach($files as $key => $value)
		{
			$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
	        if(is_file($path) && returnArrayOfTests(file($path), $path) !== array())
	        {
	        	$stuffToReturn .= "<li><input onchange=\"getFileList();\" type='checkbox' name=\"".$path."\">".$value."</li>";
	        }
	        elseif(is_dir($path) && $showSubFolderTests)
	        {
	        	$stuffToReturn .= scanDirForTests($path, $showSubFolderTests);
	        }
		}
	}
	$stuffToReturn .= "</ul>";
	return $stuffToReturn;
}

function returnArrayOfTests($file, $fileName)
{
	$arrayOfTests = array();
	for ($i=0; $i < count($file); $i++)
	{ 
		if(strpos($file[$i], "public function") !== false)
		{
			if(strpos($file[$i], "test") !== false)
			{
				if(strpos($file[$i], "//") === false)
				{
					$line = filterFunctionName($file[$i]);
					if(!in_array($line, $arrayOfTests))
					{
						$arrayOfTests[$fileName.$line] = array(
							"test"						=>	$line,
							"file"						=>	$fileName
						);
					}
				}
			}
		}
	}
	return $arrayOfTests;
}

function getAllTestLogFileTimes($path = "../../tmp/tests/")
{
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
	return $response;
}

function getListOfFiles($data)
{
	$path = $data["path"];
	$filter = $data["filter"];
	$response = $data["response"];
	$recursive = $data["recursive"];
	$fileData = array();
	if(isset($data["data"]))
	{
		$fileData = $data["data"];
	}

	$path = preg_replace('/\/$/', '', $path);
	if(file_exists($path))
	{
		$scannedDir = scandir($path);
		if(!is_array($scannedDir))
		{
			$scannedDir = array($scannedDir);
		}
		$files = array_diff($scannedDir, array('..', '.'));
		if($files)
		{
			foreach($files as $k => $filename)
			{
				$fullPath = $path . DIRECTORY_SEPARATOR . $filename;
				if(is_dir($fullPath) && $recursive === "true")
				{
					$response = sizeFilesInDir(array(
						"path" 			=> $fullPath,
						"filter"		=> $filter,
						"response"		=> $response,
						"recursive"		=> "true",
						"data"			=> $fileData

					));
				}
				elseif(preg_match('/' . $filter . '/S', $filename) && is_file($fullPath))
				{
					$boolCheck = true;
					if(isset($fileData[$fullPath]))
					{
						$dataToUse = get_object_vars($fileData[$fullPath]);
						if($dataToUse["Include"] === "false")
						{
							$boolCheck = false;
						}
					}
					if($boolCheck)
					{
						array_push($response, $fullPath);
					}
				}
			}
		}
	}
	return $response;
}