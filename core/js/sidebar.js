var dataArrayForGraphBase = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
var dataArrayForGraph = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

function sidebarStuff()
{
	if(typeof sidebarStuffRun === "undefined")
	{
		$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
		{
			sideBarDisplayLogic(data);
		});
	}
	else
	{
		clearInterval(sideBarStuffPoll);
	}
}

function sideBarDisplayLogic(data)
{
	graphLogic(data);
	speedOmLogic(data);
}

function speedOmLogic(data)
{
	var currentMaxTestsStatic = getMaxConcurrentTests(data);
	var currentCurrentRunningTestCount = getCurrentRunningTestCount(data);
	var totalNodes = getCurrentNodeCount(data);
	var nodeInUsage = getCurrentNodeUsage(data);
	$("#currentRunTest").html(currentCurrentRunningTestCount);
	$("#currentMaxNodeTot").html(currentMaxTestsStatic);
	$("#currentNodeCount").html(totalNodes);
	$("#currentRunNodes").html(nodeInUsage);
}

function graphLogic(data)
{
	dataArrayForGraphBase = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
	var currentMaxTestsStatic = getMaxConcurrentTests(data);
	var currentCurrentRunningTestCount = getCurrentRunningTestCount(data);
	dataArrayForGraph.push(currentCurrentRunningTestCount);
	if(dataArrayForGraph.length > 60)
	{
		dataArrayForGraph.shift();
	}
	var dataArrayForGraphLength = dataArrayForGraph.length;
	var newGraph = new Array();
	for(var i = 0; i < dataArrayForGraphLength; i++)
	{
		newGraph.push((dataArrayForGraph[i]/currentMaxTestsStatic)*100).toFixed(1);
	}
	//now draw graph
	var canEl = document.getElementById("useageCanvas");
	var context = canEl.getContext("2d");
	context.clearRect(0, 0, canEl.width, canEl.height);
	fillAreaInChart(newGraph, dataArrayForGraphBase, "black", context , canEl.height , canEl.width,  1);
}

function fillAreaInChart(arrayForFill, bottomArray, color, context, height, width, type)
{
	if(type == 1)
	{
		fillAreaInChartVersionOne(arrayForFill, bottomArray, color, context, height, width);
	}
	else
	{
		//type == 2
		fillAreaInChartVersionTwo(arrayForFill, bottomArray, color, context, height, width);
	}
}

function fillAreaInChartVersionOne(arrayForFill, bottomArray, color, context, height, width)
{
	context.fillStyle = color;
	var totalWidthOfEachElement = width/bottomArray.length;
	for (var i = bottomArray.length - 1; i >= 0; i--) 
	{
		var heightOfElement = height*(arrayForFill[i]/100);
		context.fillRect((totalWidthOfEachElement*(i)),(height-heightOfElement-bottomArray[i]),totalWidthOfEachElement,heightOfElement);
		bottomArray[i] = bottomArray[i]+heightOfElement;
	}
}

function fillAreaInChartVersionTwo(arrayForFill, bottomArray, color, context, height, width)
{
	context.fillStyle = color;
	var totalWidthOfEachElement = width/(bottomArray.length-1);
	var bottomArrayTmp = bottomArray;
	for (var i = bottomArray.length - 1; i >= 0; i--) 
	{
		var heightOfElement = (height*(arrayForFill[i]/100));
		context.beginPath();
		context.moveTo((totalWidthOfEachElement*(i-1)),(height-(height*(arrayForFill[i-1]/100))-bottomArrayTmp[i-1]));
		context.lineTo((totalWidthOfEachElement*(i-1)),(height-bottomArrayTmp[i-1]));
		context.lineTo((totalWidthOfEachElement*(i)),(height-bottomArrayTmp[i]));
		context.lineTo((totalWidthOfEachElement*(i)),(height-heightOfElement-bottomArrayTmp[i]));
		context.closePath();
		context.fill();
		bottomArray[i] = bottomArray[i]+heightOfElement;
	}
}