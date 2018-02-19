var serverArray = new Array();
var heightBase = 0;
var firstLoad = true;
var numberOfPollInnerRequests = 0;
var pollOffset = 1;
var currentPopupWindow = null;
var popupWidthPic = 0;
var popupHeightPic = 0;

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
		browsersContentDetail = browsersContentDetail.split('internet_explore').join('internet-explore');
		var browserConfig = splitData[i].split("<div type='config' class='content_detail'>");
		browserConfig = browserConfig[1].split("</div>");
		browserConfig = browserConfig[0];
		var pollType = "slow";
		if(browsersContentDetail.indexOf("busy") !== -1)
		{
			pollType = "fast";
		}
		if($("#main #"+proxyIdId).length === 0)
		{
			serverArray[proxyIdId] = {ip: proxyId, id: proxyIdId, poll: "slow"};
			var item = $("#storage .server").html();
			item = item.replace(/{{id}}/g, proxyIdId);
			item = item.replace(/{{title}}/g, (proxyId.replace(":5555","")));
			item = item.replace(/{{activity}}/g, browsersContentDetail);
			item = item.replace(/{{config}}/g, browserConfig);
			item = item.replace(/{{linkOne}}/g, (proxyId.replace("5555","3000")));
			item = item.replace(/{{linkTwo}}/g, ((proxyId.replace("5555","4444"))+"/grid/console"));
			item = item.replace(/{{linkThree}}/g, (proxyId+"/wd/hub/static/resource/hub.html"));
			$("#main").append(item);
			firstLoad = true;
		}
		else
		{
			serverArray[proxyIdId]["poll"] = pollType;
			document.getElementById(proxyIdId+"Activity").innerHTML = browsersContentDetail;
			document.getElementById(proxyIdId+"Config").innerHTML = browserConfig;

			if(currentPopupWindow === proxyIdId)
			{
				document.getElementById(proxyIdId+"PopupTitle").innerHTML = "<h2 style=\"font-size: 150%;\">"+proxyId.replace(":5555","")+"</h2>";
				document.getElementById(proxyIdId+"PopupActivity").innerHTML = browsersContentDetail;
				document.getElementById(proxyIdId+"PopupConfig").innerHTML = browserConfig;
			}
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
	pollInner();
}

function pollInner()
{

	var data = {};
	var servers = Object.keys(serverArray);
	var stop = servers.length;
	var half = (stop - (stop % 2))/2;
	var endOffset = pollOffset + half;
	var startOffset = pollOffset;
	if(endOffset > stop)
	{
		endOffset -= stop;
		startOffset = endOffset;
		endOffset = pollOffset;
	}
	var counter = 0;
	for(var i = 0; i !== stop; ++i)
	{
		if((i+1) >= startOffset || (i+1) <= endOffset)
		{
			data[counter] = serverArray[servers[i]];
			counter++;
		}
	}

	pollOffset++;
	if(pollOffset > stop)
	{
		pollOffset = 1;
	}

	if(data !== {} && numberOfPollInnerRequests < 4)
	{
		numberOfPollInnerRequests++;

		var data = {serverArray: data};
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
					for (var i = data.length - 1; i >= 0; i--)
					{
						var idForDisconnectMessage = _data["serverArray"][i]["id"]+"Disconnected";
						var idForJumbotronImage = _data["serverArray"][i]["id"]+"JumbotronImage";
						
						if(data[i])
						{
							if(document.getElementById(idForDisconnectMessage).style.display !== "none")
							{
								document.getElementById(idForDisconnectMessage).style.display = "none";
								document.getElementById(idForJumbotronImage+"Span").classList.remove("jumbotronDisconnect");
								document.getElementById(idForJumbotronImage+"Span").classList.add("jumbotron");

								if(currentPopupWindow === _data["serverArray"][i]["id"])
								{
									document.getElementById(_data["serverArray"][i]["id"]+"PopupDisconnected").style.display = "none";
								}
							}
							filterAndShow(data[i], _data["serverArray"][i]);
						}
						else
						{
							if(document.getElementById(idForDisconnectMessage).style.display !== "block")
							{
								document.getElementById(idForDisconnectMessage).style.marginTop = ""+((heightBase/2)-13)+"px";
								document.getElementById(idForDisconnectMessage).style.display = "block";
								document.getElementById(idForJumbotronImage+"Span").classList.add("jumbotronDisconnect");
								document.getElementById(idForJumbotronImage+"Span").classList.remove("jumbotron");

								if(currentPopupWindow === _data["serverArray"][i]["id"])
								{
									document.getElementById(_data["serverArray"][i]["id"]+"PopupDisconnected").style.display = "block";
								}
							}
						}
					}

					numberOfPollInnerRequests--;
				}
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
	//jumbotron = jumbotron.substring(jumbotron.indexOf(",") + 1);
	//jumbotron = atob(jumbotron);
	var idForImage = dataExt["id"]+"JumbotronImage";
	if(!document.getElementById(idForImage))
	{
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
    }
    else
    {
    	var marginBottom = heightBase - document.getElementById(idForImage).style.height + 10;
    	if(document.getElementById(idForImage).style.marginBottom !== marginBottom)
    	{
    		document.getElementById(idForImage).style.marginBottom =  marginBottom;
    	}
    }
	document.getElementById(dataExt["id"]+"JumbotronImageSpan").innerHTML = "<img id='"+idForImage+"' class='img-responsive' src='"+jumbotron+"'>";


	var videos = data.split("<ul class='videos'>");
	videos = videos[1].split("</ul>");
	videos = videos[0];
	videos = videos.split("<li>");
	var videosInner = "";
	var videosCount = videos.length;
	for(var i = 1; i < videosCount; i++)
	{
		if(videos[i].indexOf(".temp") === -1)
		{
			var videoSave = videos[i];
			var front = videos[i];
			front = front.substring(0, front.indexOf(".mp4") + 4)+"'>";
			videoSave = videoSave.substring(videoSave.indexOf("(") + 1);
			videoSave = videoSave.substring(0, videoSave.indexOf(')'));
			videosInner += "<li style='padding: 3px;' >"+front+videoSave+"</a></li>";
		}
	}
	videos = "<ul class='videos'>"+videosInner+"</ul>";

	document.getElementById(dataExt["id"]+"Videos").innerHTML = videos;


	var stats = data.split("<!-- videos -->");
	stats = stats[1].split("<div class='col-lg-6'>");
	stats = stats[1].split("</div> ");
	stats = stats[0];

	document.getElementById(dataExt["id"]+"Stats").innerHTML = stats;


	if(currentPopupWindow === dataExt["id"])
	{

		document.getElementById(dataExt["id"]+"PopupVideos").innerHTML = videos;
		document.getElementById(dataExt["id"]+"PopupStats").innerHTML = stats;

		popupImageLogic(dataExt["id"], jumbotron);
	}
}

function popupImageLogic(idForLogic, src)
{
	var heightOrg = document.getElementById(idForLogic+"Jumbotron").style.height;
	var widthOrg = document.getElementById(idForLogic+"Jumbotron").style.width;
	heightOrg = parseInt(heightOrg.substring(0, heightOrg.length - 2));
	widthOrg = parseInt(widthOrg.substring(0, widthOrg.length - 2));
	var newWidth = 0;
	var newHeight = 0;
	if(widthOrg > heightOrg)
	{
		//base new size off width
		newWidth = popupWidthPic;
		newHeight = newWidth * (heightOrg/widthOrg);

		if(newHeight > popupHeightPic)
		{
			newHeight = popupHeightPic;
			newWidth = newHeight * (widthOrg/heightOrg);
		}
	}
	else
	{
		//base new size off height
		newHeight = popupHeightPic;
		newWidth = newHeight * (widthOrg/heightOrg);

		if(newWidth > popupWidthPic)
		{
			newWidth = popupWidthPic;
			newHeight = newWidth * (heightOrg/widthOrg);
		}
	}
	document.getElementById(idForLogic+"PopupJumbotronImageSpan").innerHTML = "<img id='"+idForLogic+"JumbotronImagePopup' width=\""+newWidth+"px\" height=\""+newHeight+"px\"  src='"+src+"'>";
}

function showPopup(id)
{
	currentPopupWindow = id;
	var item = $("#storage .popup").html();
	item = item.replace(/{{id}}/g, id+"Popup");
	item = item.replace(/{{title}}/g, $("#"+id+"Title").text());
	item = item.replace(/{{activity}}/g, $("#"+id+"Activity").html());
	item = item.replace(/{{config}}/g, $("#"+id+"Config").html());
	item = item.replace(/{{linkAction}}/g, $("#"+id+"Actions").html());
	item = item.replace(/{{videos}}/g, $("#"+id+"Videos").html());
	item = item.replace(/{{stats}}/g, $("#"+id+"Stats").html());

	$("#main").append(item);
	popupWidthPic = parseInt(document.getElementById(id+"PopupJumbotronHolder").offsetWidth);
	popupHeightPic = parseInt(document.getElementById(id+"PopupJumbotronHolder").offsetHeight);
	var containerHeight = parseInt(document.getElementById(id+"Popup").offsetHeight) - (parseInt(document.getElementById("popupSpanLeftHeight").offsetHeight));
	document.getElementById(id+"PopupActions").style.height = containerHeight+"px";
	document.getElementById(id+"PopupVideos").style.height = containerHeight+"px";
	document.getElementById(id+"PopupStats").style.height = containerHeight+"px";
	document.getElementById(id+"PopupConfig").style.height = containerHeight+"px";
	if(document.getElementById(id+"JumbotronImage"))
	{
		popupImageLogic(id, document.getElementById(id+"JumbotronImage").src);
	}
}

function hidePopupWindow()
{
	currentPopupWindow = null;
	$("#popup").remove();
	$("#popupBackground").remove();
	popupWidthPic = 0;
	popupHeightPic = 0;
}

function toggleTab(currentId, tabIdToShow)
{
	$("#"+currentId+" .menu li").removeClass("active");
	$("#"+currentId+" .conainerSub").hide();

	$("#"+currentId+tabIdToShow).show();
	$("#"+currentId+tabIdToShow+"Menu").addClass("active");
}

function rebootMachine(ipAddress)
{
	var data = {ipAddress};
	var urlForSend = "../core/php/sendRebootCommand.php?format=json";
	$.ajax(
	{
		url: urlForSend,
		dataType: "json",
		data,
		type: "POST",
		success(data)
		{
			showPopup();
			document.getElementById('popupContentInnerHTMLDiv').innerHTML = "<div class='settingsHeader' >Reboot sent...</div><br><br><div style='width:100%;text-align:center;'>"+data+" </div>";
		}
	});
	
}