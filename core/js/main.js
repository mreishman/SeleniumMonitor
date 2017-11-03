
function resize() 
{
	var targetHeight = window.innerHeight;
	if($("#main").outerHeight() !== targetHeight)
	{
		$("#main").outerHeight(targetHeight);
	}
}