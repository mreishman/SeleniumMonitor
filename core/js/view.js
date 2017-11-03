var arrayOfData = new Array();
var heightBase = 0;
var firstLoad = true;

function poll()
{
	$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
	{
		filterPoll(data);
	});
}

function filterPoll(data)
{
	var splitData = data.split("<div class='proxy'>");
	for (var i = 1; i < splitData.length; i++)
	{
		var proxyId = splitData[i].split("proxyid");
		proxyId = proxyId[1].split("http");
		proxyId = proxyId[1].split(",");
		proxyId = "http"+proxyId[0];
		var proxyIdId = proxyId.split('.').join('point');
		proxyIdId = proxyIdId.split(':').join('colon');
		proxyIdId = proxyIdId.split('/').join('forwardSlash');
		var browsersContentDetail = splitData[i].split("<div type='browsers' class='content_detail'>");
		browsersContentDetail = browsersContentDetail[1].split("</div>");
		browsersContentDetail = browsersContentDetail[0];
		browsersContentDetail = browsersContentDetail.split('/grid/resources/org/openqa/grid/images/').join('../core/img/');
		var browserConfig = splitData[i].split("<div type='config' class='content_detail'>");
		browserConfig = browserConfig[1].split("</div>");
		browserConfig = browserConfig[0];
		if($("#main #"+proxyIdId).length === 0)
		{
			arrayOfData[proxyIdId] = {ip: proxyId, id: proxyIdId};
			var item = $("#storage .server").html();
			item = item.replace(/{{id}}/g, proxyIdId);
			item = item.replace(/{{title}}/g, proxyId);
			item = item.replace(/{{activity}}/g, browsersContentDetail);
			item = item.replace(/{{config}}/g, browserConfig);
			$("#main").append(item);
			firstLoad = true;
		}
		else
		{
			document.getElementById(proxyIdId+"Activity").innerHTML = browsersContentDetail;
			document.getElementById(proxyIdId+"Config").innerHTML = browserConfig;
		}
	}

	if(firstLoad)
	{
		firstLoad = false;
		pollTwo();
	}
}


function pollTwo()
{
	var servers = Object.keys(arrayOfData);
	var stop = servers.length;
	for(var i = 0; i !== stop; ++i)
	{
		var data = arrayOfData[servers[i]];
		var urlForSend = "../core/php/getMainHostInfo.php?format=json";
		(function(_data){
			$.ajax(
			{
				url: urlForSend,
				dataType: "json",
				data,
				type: "POST",
				success(data)
				{
					filterAndShow(data, _data);
				},
			});
		}(data));
	}
}


function filterAndShow(data, dataExt)
{
	var title = data.split("title>");
	title = title[1].split("</title");
	title = title[0];

	var header = data.split("class='container'");
	header = header[1];

	var jumbotron = data.split("src='");
	jumbotron = jumbotron[1].split("'>");
	jumbotron = jumbotron[0];
	var newImg = new Image();

    newImg.onload = function()
    {
    	var idOfNewImg = dataExt["id"]+"Jumbotron";
    	var ratio = newImg.height/newImg.width;
    	var marginBottom = 10;
    	var width = 312;
    	var height = width*ratio;
    	if(height > heightBase)
    	{
    		heightBase = height;
    	}
    	else if(height < heightBase)
    	{
    		marginBottom = heightBase - height + marginBottom;
    	}
    	document.getElementById(idOfNewImg).setAttribute("style","width:"+width+"px; height:"+height+"px; margin-bottom: "+marginBottom+"px;");
    }

    newImg.src = jumbotron; 
    newImg.id = dataExt["id"];
    jumbotron = data.split("class='jumbotron'>");
	jumbotron = jumbotron[1].split(" <!-- jumbotron -->");
	jumbotron = jumbotron[0];
   
	document.getElementById(dataExt["id"]+"Jumbotron").innerHTML = jumbotron;


	var videos = data.split("<ul class='videos'>");
	videos = videos[1].split("</ul>");
	videos = videos[0];
	videos = "<ul class='videos'>"+videos+"</ul>";

	document.getElementById(dataExt["id"]+"Videos").innerHTML = videos;


	var stats = data.split("<!-- videos -->");
	stats = stats[1].split("<div class='col-lg-6'>");
	stats = stats[1].split("</div> ");
	stats = stats[0];

	document.getElementById(dataExt["id"]+"Stats").innerHTML = stats;
}

function toggleTab(currentId, tabIdToShow)
{
	$("#"+currentId+" .menu li").removeClass("active");
	$("#"+currentId+" .conainerSub").hide();

	$("#"+currentId+tabIdToShow).show();
	$("#"+currentId+tabIdToShow+"Menu").addClass("active");
}