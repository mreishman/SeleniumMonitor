function showRender(divId, renderInfo)
{
	var item = $("#storage .container").html();
	item = item.replace(/{{file}}/g, renderInfo["file"]);
	item = item.replace(/{{website}}/g, renderInfo["website"]);
	item = item.replace(/{{totalCount}}/g, getCountOfBlockType(renderInfo["info"], "block"));
	item = item.replace(/{{failCount}}/g, getCountOfBlockType(renderInfo["info"], "blockFail"));
	item = item.replace(/{{errorCount}}/g, getCountOfBlockType(renderInfo["info"], "blockError"));
	$("#"+divId).append(item);
}

function generateProgressBlocks()
{

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