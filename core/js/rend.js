function showRender(divId, id, renderInfo, logFile, container)
{
	var failClass = "blockFail";
	var errorClass = "blockError";
	if(container === "containerTwo")
	{
		failClass = "blockPass";
		errorClass = "blockFail";
	}
	var item = $("#storage ."+container).html();
	item = item.replace(/{{id}}/g, id);
	item = item.replace(/{{file}}/g, renderInfo["file"]);
	item = item.replace(/{{website}}/g, renderInfo["website"]);
	item = item.replace(/{{totalCount}}/g, getCountOfBlockType(renderInfo["info"], "block"));
	item = item.replace(/{{failCount}}/g, getCountOfBlockType(renderInfo["info"], failClass));
	item = item.replace(/{{errorCount}}/g, getCountOfBlockType(renderInfo["info"], errorClass));
	item = item.replace(/{{ProgressBlocks}}/g, generateProgressBlocks(renderInfo["info"],divId));
	item = item.replace(/{{logFile}}/g, logFile);
	if(id.indexOf("Test") === 0 && item.indexOf("{{date}}") > -1)
	{
		var newDate = id.replace("Test", "");
		newDate = newDate.replace(".log","");
		newDate = new Date(newDate);
		item = item.replace(/{{date}}/g, newDate);
	}
	return item;
}

function generateProgressBlocks(info, divId)
{
	var keysInfo = Object.keys(info);
	var keysInfoLength = keysInfo.length;
	var progressBlocksHtml = "";
	for (var i = 0; i < keysInfoLength; i++)
	{
		progressBlocksHtml += "<div onclick=\"showTestPopup('Test"+divId+keysInfo[i]+"popup');\" title='"+info[keysInfo[i]]["title"]+"' id='Test"+divId+keysInfo[i]+"' ";
		var classArray = info[keysInfo[i]]["result"];
		var classArrayLength = classArray.length;
		if(info[keysInfo[i]]["result"].length > 0)
		{
			progressBlocksHtml += "class = '";
			for (var j = 0; j < classArrayLength; j++)
			{
				progressBlocksHtml += " "+classArray[j]+" ";
			}
			progressBlocksHtml += "'";
		}
		progressBlocksHtml += ">";
		progressBlocksHtml += "</div>";
		progressBlocksHtml += "<div class=\"testPopupBlock\" id='Test"+divId+keysInfo[i]+"popup'> <h3> Test: "+keysInfo[i]+" </h3> <br> <span id='Test"+divId+keysInfo[i]+"popupSpan' >"+info[keysInfo[i]]["notes"]+"</span>";
		progressBlocksHtml += " </div>";
	}
	return progressBlocksHtml;
}

function getCountOfBlockType(info, typefind)
{
	var keysInfo = Object.keys(info);
	var keysInfoLength = keysInfo.length;
	var count = 0;
	for (var i = 0; i < keysInfoLength; i++)
	{
		if(info[keysInfo[i]]["result"].indexOf(typefind) > -1)
		{
			count++;
		}
	}
	return count;
}