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
	$stringGen .= "<h3><span id=\"".$dataForContainer["id"]."RenameDisplay\" >".$dataForContainer["logFile"]."</span><span style=\"display: none;\" id=\"".$dataForContainer["id"]."RenameInput\" > <input id=\"".$dataForContainer["id"]."RenameInputValue\" value=".$dataForContainer["logFile"]." ></input> <a onclick=\"renameCompare('".$dataForContainer["id"]."');\" class=\"link\" >Cancel</a> <a onclick=\"actuallyRenameCompare('".$dataForContainer["id"]."');\" class=\"link\" >Save</a> </span></h3>";
	$stringGen .= "<div style=\"font-size: 200%;\">";
	$stringGen .= "<img class=\"imageInHeaderContainer\" onclick=\"removeCompare('".$dataForContainer["id"]."');\" src=\"../core/img/trashCan.png\">";
	$stringGen .= "<img id=\"".$dataForContainer["id"]."RenameIcon\" class=\"imageInHeaderContainer\" onclick=\"renameCompare('".$dataForContainer["id"]."');\" src=\"../core/img/rename.png\">";
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
				$fileLocal = "";
				$websiteLocal = "";
				$infoLocal = array();
				if(isset($dataForTest["file"]))
				{
					$fileLocal = $dataForTest["file"];
				}
				if(isset($dataForTest["website"]))
				{
					$websiteLocal = $dataForTest["website"];
				}
				if(isset($dataForTest["info"]) && !empty((array)$dataForTest["info"]))
				{
					$infoLocal = $dataForTest["info"];
					echo genContainer(array(
						"id"				=>	$value,
						"logFile"			=>	$value,
						"ProgressBlocks"	=>	generateProgressBlocks($infoLocal, $value),
						"failCount"			=>	getCountOfBlockType($infoLocal,"blockFail"),
						"totalCount"		=>	getCountOfBlockType($infoLocal,"block"),
						"errorCount"		=>	getCountOfBlockType($infoLocal,"blockError"),
						"website"			=>	$websiteLocal,
						"file"				=>	$fileLocal
					));
				}
				else
				{
					$stringGen = "<div style=\"background-color: white; border: 1px solid black;\" class=\"scanBar containerMain\">";
					$stringGen .= "<div class=\"fontChange\" style=\"width: 100%; text-align: left;\" >";
					$stringGen .= "<h3> Error with cache for test ".$value." </h3></div></div>";
					echo $stringGen;
				}
			}
			?>
		</div>
		<div id="loadingThing" style="width: 100%; text-align: center;">
			<img src="../core/img/loading.gif" width="50px;">
		</div>
		<div id="noCachedTests" style="width: 100%; text-align: center; background-color: green; padding: 20px; padding-left: 200px; padding-right: 0px; <?php if($counter !== 0){ echo"display: none;";} ?>">
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
	</script> 
	<script src="../core/js/main.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/rend.js?v=<?php echo $cssVersion?>"></script>
	<script src="../core/js/tests.js?v=<?php echo $cssVersion?>"></script>
	<?php readfile('../core/html/popup.html') ?>
</body>