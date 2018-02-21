<?php
require_once('../core/php/commonFunctions.php');

$baseUrl = "../core/";
if(file_exists('../local/layout.php'))
{
	$baseUrl = "../local/";
	//there is custom information, use this
	require_once('../local/layout.php');
	$baseUrl .= $currentSelectedTheme."/";
}
if(!file_exists($baseUrl.'conf/config.php'))
{
	$partOfUrl = clean_url($_SERVER['REQUEST_URI']);
	$url = "http://" . $_SERVER['HTTP_HOST'] .substr($partOfUrl,0,5) ."setup/welcome.php";
	header('Location: ' . $url, true, 302);
	exit();
}
require_once($baseUrl.'conf/config.php');
require_once('../core/conf/config.php');
require_once('../core/php/configStatic.php');
require_once('../core/php/loadVars.php');
require_once('../core/php/updateCheck.php');

$daysSince = calcuateDaysSince($configStatic['lastCheck']);

$logTimes = getAllTestLogFileTimes("../tmp/tests/");

$counter = 0;
$logTimeArray = array();
foreach ($logTimes as $key => $value)
{
	if(!isset($logTimeArray[$value]))
	{
		$logTimeArray[$value] = $key;
	}
	else
	{
		$logTimeArray[$value.random_int(1, 256)] = $key;
	}
	$counter++;
}

function genContainer($dataForContainer)
{
	$stringGen = "<div style=\"background-color: white; border: 1px solid black;\" id=\"".$dataForContainer["id"]."\" class=\"scanBar containerMain\">";
	$stringGen .= "<div class=\"fontChange\" style=\"width: 100%; text-align: left;\" id=\"".$dataForContainer["id"]."Title\">";
	$stringGen .= "<h3>".$dataForContainer["logFile"]."</h3>";
	$stringGen .= "<div style=\"font-size: 200%;\">";
	$stringGen .= "<img class=\"imageInHeaderContainer\" onclick=\"removeCompare('".$dataForContainer["id"]."');\" src=\"../core/img/trashCan.png\">";
	$stringGen .= "</div>";
	$stringGen .= "</div>";
	$stringGen .= "<div id=\"".$dataForContainer["id"]."ProgressBlocks\" class=\"containerBox\" style=\"text-align: left;\">";
	$stringGen .= $dataForContainer["ProgressBlocks"];
	$stringGen .= "</div>";
	$stringGen .= "<div class=\"key fontChange\"> Key: <br>";
	$stringGen .= "		<div class=\"block blockKey blockEmpty\"></div> - Waiting";
	$stringGen .= "		<div class=\"block blockKey blockInProgress\"></div> - Running";
	$stringGen .= "		<div class=\"block blockKey blockPass\"></div> - Passed";
	$stringGen .= "		<div class=\"block blockKey blockError\"></div> - Error";
	$stringGen .= "		<div class=\"block blockKey blockFail\"></div> - Fail";
	$stringGen .= "		<div class=\"block blockKey blockSkip\"></div> - Skipped";
	$stringGen .= "		<div class=\"block blockKey blockRisky\"></div> - Risky";
	$stringGen .= "</div>";
	$stringGen .= "<div class=\"fontChange subTitleEF\">";
	$stringGen .= "<span id=\"".$dataForContainer["id"]."FailCount\">".$dataForContainer["failCount"]."</span>/".$dataForContainer["totalCount"]." Fails";
	$stringGen .= "<br>";
	$stringGen .= "<span id=\"".$dataForContainer["id"]."ErrorCount\">".$dataForContainer["errorCount"]."</span>/".$dataForContainer["totalCount"]." Errors";
	$stringGen .= "<br>";
	$stringGen .= $dataForContainer["website"];
	$stringGen .= "<br>";
	$stringGen .= $dataForContainer["file"];
	$stringGen .= "</div>";
	$stringGen .= "</div>";
	return $stringGen;
}

function getCountOfBlockType($info, $typefind)
{
	$count = 0;
	foreach ($info as $value)
	{
		$value = get_object_vars($value);
		$result = $value["result"];
		if(in_array($typefind, $result))
		{
			$count++;
		}
	}
	return $count;
}

function generateProgressBlocks($info, $divId)
{

	$progressBlocksHtml = "";
	foreach ($info as $key => $value)
	{
		$value = get_object_vars($value);
		$progressBlocksHtml .= "<div onclick=\"showTestPopup('Test".$divId.$key."popup');\" title='".$value["title"]."' id='Test".$divId.$key."' ";
		$classArray = $value["result"];
		$classArrayLength = count($classArray);
		if($classArrayLength > 0)
		{
			$progressBlocksHtml .= "class = '";
			for($j = 0; $j < $classArrayLength; $j++)
			{
				$progressBlocksHtml .= " ".$classArray[$j]." ";
			}
			$progressBlocksHtml .= "'";
		}
		$progressBlocksHtml .= ">";
		$progressBlocksHtml .= "</div>";
		$progressBlocksHtml .= "<div class=\"testPopupBlock\" id='Test".$divId.$key."popup'> <h3> Test: ".$key." </h3> <br> <span id='Test".$divId.$key."popupSpan' >".$value["notes"]."</span>";
		$progressBlocksHtml .= " </div>";
	}
	return $progressBlocksHtml;
}

?>
<!doctype html>
<head>
	<title>SeleniumMonitor | Tests</title>
	<?php echo loadCSS($baseUrl, $cssVersion);?>
	<link rel="icon" type="image/png" href="<?php echo $baseUrl; ?>img/favicon.png" />
	<link rel="stylesheet" type="text/css" href="../core/template/runcss.css?v=<?php echo $cssVersion; ?>">
	<script src="../core/js/jquery.js"></script>
</head>
<body>
	<?php require_once("../core/php/customCSS.php");?>
	<div id="menu">
		<a href="../"> <img class="menuImage" src="<?php echo $baseUrl; ?>img/backArrow.png" style="display: inline-block; cursor: pointer;" height="20px"> </a>
		<?php require_once("../core/php/template/otherLinks.php");?>
	</div>


	<div id="main" style="background-color: #333;">
		<div id="testSidebar" style="display: none; position: fixed; bottom: 0; left: 0; background-color: #CCC; overflow: auto; width: 200px;">
			<ul id="testSidebarUL" style="list-style: none; padding: 0;">
				<?php foreach ($logTimeArray as $key => $value)
				{
					echo "<li id=\"".$value."HotLink\" style=\"padding: 5px;\"><a href=\"#".$value."\" class=\"link\" >".$value."</a></li>";
				}
				?>
			</ul>
		</div>
		<div id="subMain" style="margin-left: 200px; display: none;">
			<?php foreach ($logTimeArray as $key => $value)
			{
				$dataForTest = (array)json_decode(file_get_contents("../tmp/tests/".$value));
				echo genContainer(array(
					"id"				=>	$value,
					"logFile"			=>	$value,
					"ProgressBlocks"	=>	generateProgressBlocks($dataForTest["info"], $value),
					"failCount"			=>	getCountOfBlockType($dataForTest["info"],"blockFail"),
					"totalCount"		=>	getCountOfBlockType($dataForTest["info"],"block"),
					"errorCount"		=>	getCountOfBlockType($dataForTest["info"],"blockError"),
					"website"			=>	$dataForTest["website"],
					"file"				=>	$dataForTest["file"]
				));
			}
			?>
		</div>
		<div id="loadingThing" style="width: 100%; text-align: center;">
			<img src="../core/img/loading.gif" width="50px;">
		</div>
		<div id="noCachedTests" style="width: 100%; text-align: center; <?php if($counter !== 0){ echo"display: none;";} ?>">
			There are no current tests saved in cache.
		</div>
	</div>

	<div id="storage">
		<div class="container">
			<?php
			echo genContainer(array(
					"id"				=>	"{{id}}",
					"logFile"			=>	"{{logFile}}",
					"ProgressBlocks"	=>	"{{ProgressBlocks}}",
					"failCount"			=>	"{{failCount}}",
					"totalCount"		=>	"{{totalCount}}",
					"errorCount"		=>	"{{errorCount}}",
					"website"			=>	"{{website}}",
					"file"				=>	"{{file}}"
				));
			?>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function()
		{
			document.getElementById("testSidebar").style.display = "block";
			document.getElementById("subMain").style.display = "block";
			document.getElementById("loadingThing").style.display = "none";
			resize();
			window.onresize = resize;

			setInterval(function(){poll();},10000);

		});

		var arrayOfFiles = <?php echo json_encode($logTimes);?>;

		function poll()
		{
			var urlForSendInner = '../core/php/getFileTimeForAllLogs.php?format=json';
			var dataSend = {};
			$.ajax(
			{
				url: urlForSendInner,
				dataType: "json",
				data: dataSend,
				type: "POST",
				success(data)
				{
					//compare to arrayOfFiles
					var keysInfo = Object.keys(data);
					var keysInfoLength = keysInfo.length;
					for(var i = 0; i < keysInfoLength; i++)
					{
						if(keysInfo[i] in arrayOfFiles)
						{
							//compare
							if(arrayOfFiles[keysInfo[i]] !== data[keysInfo[i]])
							{
								//file is new, update
								arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
								getLogData(keysInfo[i]);
							}
						}
						else
						{
							//not there, add it
							arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
							getLogData(keysInfo[i]);
						}
					}

					//check oppsite (if any keys are in arrayOfFiles but not data, if so delete from screen and local array)
					keysInfo = Object.keys(arrayOfFiles);
					keysInfoLength = keysInfo.length;
					for(var i = 0; i < keysInfoLength; i++)
					{
						if(!(keysInfo[i] in data))
						{
							//not in data anymore, it was deleted from folder... delete from screen & array
							delete arrayOfFiles[keysInfo[i]];
							if(document.getElementById(keysInfo[i]))
							{
								document.getElementById(keysInfo[i]).outerHTML = "";
							}
							if(document.getElementById(keysInfo[i]+"HotLink"))
							{
								document.getElementById(keysInfo[i]+"HotLink").outerHTML = "";
							}
							if(arrayOfFiles.length === 0)
							{
								document.getElementById("noCachedTests").style.display = "block";
							}
						}
					}
				}
			});
		}

		function getLogData(path)
		{
			var urlForSendInner = '../core/php/getLogInfo.php?format=json';
			var dataSend = {path: "../../tmp/tests/"+path};
			
			(function(_path){
				$.ajax(
				{
					url: urlForSendInner,
					dataType: "json",
					data: dataSend,
					type: "POST",
					success(data)
					{
						renderInfo = JSON.parse(data);
						var item = showRender("subMain", _path, renderInfo, _path);
						if(document.getElementById(_path))
						{
							document.getElementById(_path).outerHTML = item;
						}
						else
						{
							$("#subMain").prepend(item);
							$("#testSidebarUL").prepend("<li id=\""+_path+"HotLink\" style=\"padding: 5px;\"><a href=\"#"+_path+"\" class=\"link\" >"+_path+"</a></li>");
						}
						if(document.getElementById("noCachedTests").style.display !== "none")
						{
							document.getElementById("noCachedTests").style.display = "none";
						}
						resize();
					}
				});
			}(path));

		}

		function removeCompare(fileName)
		{
			//this function removes file from tmp storage
			//show popup first to confirm
			showPopup();
			document.getElementById('popupContentInnerHTMLDiv').innerHTML = "<div class='settingsHeader' >Remove Cache File?</div><br><div style='width:100%;text-align:center;padding-left:10px;padding-right:10px;'>Are you sure you want to remove the file "+fileName+"?</div><div class='link' onclick='actuallyRemoveFile(\""+fileName+"\")' style='margin-left:125px; margin-right:50px;margin-top:25px;'>Yes</div><div onclick='hidePopup();' class='link'>No</div></div>";
		}

		function actuallyRemoveFile(fileName)
		{
			var urlForSendInner = '../core/php/removeFile.php?format=json';
			var dataSend = {file: "../../tmp/tests/"+fileName};
			$.ajax(
				{
					url: urlForSendInner,
					dataType: "json",
					data: dataSend,
					type: "POST",
					success(data)
					{
						hidePopup();
					}
				});
		}

	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/rend.js?v=<?php echo $cssVersion?>"></script>
	<?php readfile('../core/html/popup.html') ?>
</body>