<div style="position: absolute; width: 300px; top: 0; bottom: 0; right: 0; background-color: white; border-left: 1px solid black;">
	<canvas class="canvasMonitor" id="useageCanvas" width="280" height="150" ></canvas>
	<ul style="color: black;">
		<li>
			Current Running Tests
		</li>
		<li>
			Nodes In Use
		</li>
	</ul>
</div>

<script type="text/javascript">
	
	//add poll logic here for pages that don't have it
	var sideBarStuffPoll = setInterval(function(){sidebarStuff();},1000);

</script>

<style type="text/css">
	
	.canvasMonitor
	{
		background-color: white;
		border-bottom: 1px solid black;
		margin-left: 9px;
		margin-top: 10px;
		margin-bottom: 10px;
	}

</style>