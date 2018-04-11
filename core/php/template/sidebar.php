<div style="position: absolute; width: 300px; top: 0; bottom: 0; right: 0; background-color: white; color: black; border-left: 1px solid black; overflow: auto;">
	<table>
		<tr>
			<th width="50%">
				<canvas id="testCountCanvas" width="150px" height="75px"></canvas>
				<br>
				<span id="currentRunTest" >-</span>/<span id="currentMaxNodeTot">-</span>
			</th>
			<th width="50%">
				<canvas id="nodeCountCanvas" width="150px" height="75px"></canvas>
				<br>
				<span id="currentRunNodes">-</span>/<span id="currentNodeCount">-</span>
			</th>
		</tr>
	</table>
	<canvas class="canvasMonitor" id="useageCanvas" width="280" height="150" ></canvas>
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
		border-left: 1px solid black;
	}

</style>