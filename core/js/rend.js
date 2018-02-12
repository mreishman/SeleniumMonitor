function showRender(divId, renderInfo)
{
	var item = $("#storage .container").html();
	item = item.replace(/{{file}}/g, renderInfo["file"]);
	item = item.replace(/{{website}}/g, renderInfo["website"]);
	$("#"+divId).append(item);
}

function generateProgressBlocks()
{

}