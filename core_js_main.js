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