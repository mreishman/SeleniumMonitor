function resize() 
{
	var offsetHeight = 0;
	if(document.getElementById("menu"))
	{
		offsetHeight += document.getElementById("menu").offsetHeight;
	}
	var targetHeight = window.innerHeight - offsetHeight;
	if($("#main").outerHeight() !== targetHeight)
	{
		$("#main").outerHeight(targetHeight);
	}
	if($("#testSidebar"))
	{
		if($("#testSidebar").outerHeight() !== targetHeight)
	{
		$("#testSidebar").outerHeight(targetHeight);
	}
	}
}

function getMaxConcurrentTests(data)
{
	var maxTestsStaticInner = 0;
	var splitData = data.split("<div class='proxy'>");
	for (var i = 1; i < splitData.length; i++)
	{
		var browserConfig = splitData[i].split("<div type='config' class='content_detail'>");
		browserConfig = browserConfig[1].split("</div>");
		browserConfig = browserConfig[0].split("maxSession:");
		browserConfig = browserConfig[1].split("</p>");
		browserConfig = parseInt(browserConfig[0]);
		maxTestsStaticInner += browserConfig;
	}
	return maxTestsStaticInner;
}

function getListOfPlatforms(data)
{
	var splitData = data.split("platform:");
	var platformList = new Array();
	for (var i = 1; i < splitData.length; i++)
	{
		var platformInner = splitData[i].split(",");
		platformInner = platformInner[0].trim();
		if(platformList.indexOf(platformInner) === -1)
		{
			platformList.push(platformInner);
		}
	}
	return platformList;
}

function getListOfBrowsers(data)
{
	var splitData = data.split("<div class='proxy'>");
	var browserList = new Array();
	for (var i = 1; i < splitData.length; i++)
	{
		var browserInner = splitData[i].split(".png");
		for (var j = 0; j < browserInner.length - 1; j++)
		{
			var browserName = browserInner[j].split("/");
			browserName = browserName[browserName.length - 1];
			if(browserName == "internet_explorer" || browserName == "internet-explorer")
			{
				browserNow === "internet explorer";
			}
			if(browserList.indexOf(browserName) === -1)
			{
				browserList.push(browserName);
			}
		}
	}
	return browserList;
}

function getCurrentBrowserCountTotal(data, browser)
{
	var browserInner = data.split(browser+".png");
	var totmax = 0;
	for (var i = 1; i < browserInner.length; i++)
	{
		totmax++;
	}
	return totmax;
}

function getCurrentBrowserCount(data, browser)
{
	var browserInner = data.split("browserName="+browser);
	var totmax = 0;
	for (var i = 1; i < browserInner.length; i++)
	{
		if(browserInner[i].indexOf("maxInstances=") !== -1)
		{
			var max = browserInner[i].split("maxInstances=");
			max = max[1].split(",")[0];
			totmax += parseInt(max);
		}
	}
	return totmax;
}

function getCurrentNodeCount(data)
{
	var splitData = data.split("<div class='proxy'>");
	return (splitData.length - 1);
}

function getCurrentNodeUsage(data)
{
	var splitData = data.split("<div class='proxy'>");
	var nodeUsage = 0;
	for (var i = 1; i < splitData.length; i++)
	{
		if(splitData[i].indexOf("class='busy'") !== -1)
		{
			nodeUsage++;
		}
	}
	return nodeUsage;
}

function getCurrentRunningTestCount(data)
{
	
	var splitData = data.split("class='busy'");
	return (splitData.length - 1);
}

function showTestPopup(idOfTest)
{
	if(document.getElementById(idOfTest).style.display === "block")
	{
		document.getElementById(idOfTest).style.display = "none";
	}
	else
	{
		document.getElementById(idOfTest).style.display = "block";
	}
}

function copyToClipBoard(whatToCopy)
{
	var $temp = $("<input>");
	$("body").append($temp);
	$temp.val(whatToCopy).select();
	document.execCommand("copy");
	$temp.remove();
}


function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function unEscapeHtml(text) {
  var map = {
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&quot;': '"',
    "&#039;": "'"
  };

  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function getLogData()
{
	
	var urlLog = "../core/php/poll.php";
	data = {};
	$.ajax(
	{
		url: urlLog,
		dataType: "json",
		data,
		type: "POST",
		success(data)
		{
			logData = data;
			logDataParse();
		},
		complete(data)
		{
			gettingLogData = false;
		}
	});
}