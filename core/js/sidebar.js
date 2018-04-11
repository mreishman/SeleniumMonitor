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
	drawSpdTests(currentCurrentRunningTestCount, currentMaxTestsStatic);
	drawNodeCount(nodeInUsage, totalNodes);
}

function drawSpdTests(now, base)
{
	drawSpd(now, base, document.getElementById("testCountCanvas"));
}

function drawNodeCount(now, base)
{
	drawSpd(now, base, document.getElementById("nodeCountCanvas"));
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
	fillAreaInChart(newGraph, dataArrayForGraphBase, "black", context , canEl.height , canEl.width,  2);
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

/*
start
@https://github.com/rheh/HTML5-canvas-projects
*/

function degToRad(angle)
{
	// Degrees to radians
	return ((angle * Math.PI) / 180);
}

function radToDeg(angle)
{
	// Radians to degree
	return ((angle * 180) / Math.PI);
}

function drawLine(options, line)
{
	// Draw a line using the line object passed in
	options.ctx.beginPath();

	// Set attributes of open
	options.ctx.globalAlpha = line.alpha;
	options.ctx.lineWidth = line.lineWidth;
	options.ctx.fillStyle = line.fillStyle;
	options.ctx.strokeStyle = line.fillStyle;
	options.ctx.moveTo(line.from.X,
		line.from.Y);

	// Plot the line
	options.ctx.lineTo(
		line.to.X,
		line.to.Y
	);

	options.ctx.stroke();
}

function createLine(fromX, fromY, toX, toY, fillStyle, lineWidth, alpha)
{
	// Create a line object using Javascript object notation
	return {
		from: {
			X: fromX,
			Y: fromY
		},
		to:	{
			X: toX,
			Y: toY
		},
		fillStyle: fillStyle,
		lineWidth: lineWidth,
		alpha: alpha
	};
}

function applyDefaultContextSettings(options)
{
	/* Helper function to revert to gauges
	 * default settings
	 */

	options.ctx.lineWidth = 2;
	options.ctx.globalAlpha = 0.5;
	options.ctx.strokeStyle = "rgb(0, 0, 0)";
	options.ctx.fillStyle = 'rgb(0,0,0)';
}

function drawSmallTickMarks(options)
{
	/* The small tick marks against the coloured
	 * arc drawn every 5 mph from 10 degrees to
	 * 170 degrees.
	 */

	var tickvalue = options.levelRadius - 4,
	    iTick = 0,
	    gaugeOptions = options.gaugeOptions,
	    iTickRad = 0,
	    onArchX,
	    onArchY,
	    innerTickX,
	    innerTickY,
	    fromX,
	    fromY,
	    line,
		toX,
		toY;

	applyDefaultContextSettings(options);
	var numberOfTicks = 9;
	if(options.max < numberOfTicks)
	{
		numberOfTicks = options.max + 1;
	}
	var tickPer = 180/numberOfTicks;
	// Tick every 20 degrees (small ticks)
	for (iTick = 10; iTick < 180; iTick += tickPer)
	{

		iTickRad = degToRad(iTick);

		/* Calculate the X and Y of both ends of the
		 * line I need to draw at angle represented at Tick.
		 * The aim is to draw the a line starting on the 
		 * coloured arc and continueing towards the outer edge
		 * in the direction from the center of the gauge. 
		 */

		onArchX = gaugeOptions.radius - (Math.cos(iTickRad) * tickvalue);
		onArchY = gaugeOptions.radius - (Math.sin(iTickRad) * tickvalue);
		innerTickX = gaugeOptions.radius - (Math.cos(iTickRad) * gaugeOptions.radius);
		innerTickY = gaugeOptions.radius - (Math.sin(iTickRad) * gaugeOptions.radius);

		fromX = (options.center.X - gaugeOptions.radius) + onArchX;
		fromY = (gaugeOptions.center.Y - gaugeOptions.radius) + onArchY;
		toX = (options.center.X - gaugeOptions.radius) + innerTickX;
		toY = (gaugeOptions.center.Y - gaugeOptions.radius) + innerTickY;

		// Create a line expressed in JSON
		line = createLine(fromX, fromY, toX, toY, "rgb(0,0,0)", 2, 0.5);

		// Draw the line
		drawLine(options, line);

	}
}

function drawTextMarkers(options) {
	/* The text labels marks above the coloured
	 * arc drawn every 10 mph from 10 degrees to
	 * 170 degrees.
	 */
	var innerTickX = 0,
	    innerTickY = 0,
        iTick = 0,
        gaugeOptions = options.gaugeOptions,
        iTickToPrint = 0;

	applyDefaultContextSettings(options);

	// Font styling
	options.ctx.font = 'italic 10px sans-serif';
	options.ctx.textBaseline = 'top';

	options.ctx.beginPath();
	var numberOfTicks = 9;
	if(options.max < numberOfTicks)
	{
		numberOfTicks = options.max + 1;
	}
	var tickPer = 180/numberOfTicks;
	// Tick every 20 (small ticks)
	for (iTick = 10; iTick < 180; iTick += tickPer) 
	{
		if(iTick + tickPer >= 180)
		{
			iTickToPrint = options.max;
		}
		innerTickX = gaugeOptions.radius - (Math.cos(degToRad(iTick)) * gaugeOptions.radius);
		innerTickY = gaugeOptions.radius - (Math.sin(degToRad(iTick)) * gaugeOptions.radius);

		// Some cludging to center the values (TODO: Improve)
		if (iTick <= 10)
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY + 5);
		}
		else if (iTick < 50)
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX - 5,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY + 5);
		}
		else if (iTick < 90)
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY);
		}
		else if (iTick === 90)
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 4,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY);
		}
		else if (iTick < 145)
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 10,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY);
		}
		else
		{
			options.ctx.fillText(iTickToPrint, (options.center.X - gaugeOptions.radius - 12) + innerTickX + 15,
					(gaugeOptions.center.Y - gaugeOptions.radius - 12) + innerTickY + 5);
		}

		// MPH increase by 10 every x degrees
		iTickToPrint += Math.round(options.max / numberOfTicks);
	}

    options.ctx.stroke();
}

function drawSpeedometerPart(options, alphaValue, strokeStyle, startPos)
{
	/* Draw part of the arc that represents
	* the colour speedometer arc
	*/

	options.ctx.beginPath();

	options.ctx.globalAlpha = alphaValue;
	options.ctx.lineWidth = 3;
	options.ctx.strokeStyle = strokeStyle;

	options.ctx.arc(options.center.X,
		options.center.Y,
		options.levelRadius,
		Math.PI + (Math.PI / 360 * startPos),
		0 - (Math.PI / 360 * 10),
		false);

	options.ctx.stroke();
}

function drawSpeedometerColourArc(options)
{
	/* Draws the colour arc.  Three different colours
	 * used here; thus, same arc drawn 3 times with
	 * different colours.
	 * TODO: Gradient possible?
	 */

	var startOfGreen = 10,
	    endOfGreen = 200,
	    endOfOrange = 280;

	drawSpeedometerPart(options, 1.0, "rgb(82, 240, 55)", startOfGreen);
	drawSpeedometerPart(options, 0.9, "rgb(198, 111, 0)", endOfGreen);
	drawSpeedometerPart(options, 0.9, "rgb(255, 0, 0)", endOfOrange);

}

function drawNeedleDial(options, alphaValue, strokeStyle, fillStyle)
{
	/* Draws the metallic dial that covers the base of the
	* needle.
	*/
    var i = 0;

	options.ctx.globalAlpha = alphaValue;
	options.ctx.lineWidth = 3;
	options.ctx.strokeStyle = strokeStyle;
	options.ctx.fillStyle = fillStyle;

	// Draw several transparent circles with alpha
	for (i = 0; i < 15; i++)
	{

		options.ctx.beginPath();
		options.ctx.arc(options.center.X,
			options.center.Y,
			i,
			0,
			Math.PI,
			true);

		options.ctx.fill();
		options.ctx.stroke();
	}
}

function convertSpeedToAngle(options)
{
	/* Helper function to convert a speed to the 
	* equivelant angle.
	*/
	var iSpeed = ((options.speed / options.max)*8).toFixed(2),
	    iSpeedAsAngle = ((iSpeed * 20)+10) % 180;

	// Ensure the angle is within range
	if (iSpeedAsAngle > 180) {
        iSpeedAsAngle = iSpeedAsAngle - 180;
    } else if (iSpeedAsAngle < 0) {
        iSpeedAsAngle = iSpeedAsAngle + 180;
    }

	return iSpeedAsAngle;
}

function drawNeedle(options)
{
	/* Draw the needle in a nice read colour at the
	* angle that represents the options.speed value.
	*/

	var iSpeedAsAngle = convertSpeedToAngle(options),
	    iSpeedAsAngleRad = degToRad(iSpeedAsAngle),
        gaugeOptions = options.gaugeOptions,
        innerTickX = gaugeOptions.radius - (Math.cos(iSpeedAsAngleRad) * 20),
        innerTickY = gaugeOptions.radius - (Math.sin(iSpeedAsAngleRad) * 20),
        fromX = gaugeOptions.center.X,
        fromY = gaugeOptions.center.Y,
        endNeedleX = gaugeOptions.radius - (Math.cos(iSpeedAsAngleRad) * gaugeOptions.radius),
        endNeedleY = gaugeOptions.radius - (Math.sin(iSpeedAsAngleRad) * gaugeOptions.radius),
        toX = (options.center.X - gaugeOptions.radius) + endNeedleX,
        toY = (gaugeOptions.center.Y - gaugeOptions.radius) + endNeedleY,
        line = createLine(fromX, fromY, toX, toY, "rgb(0,0,0)", 3, 1);

	drawLine(options, line);

	// Two circle to draw the dial at the base
	drawNeedleDial(options, 1, "rgb(0, 0, 0)", "rgb(0,0,0)");

}

function buildOptionsAsJSON(canvas, iSpeed, maxSpeed)
{
	/* Setting for the speedometer 
	* Alter these to modify its look and feel
	*/

	var centerX = (canvas.width/2),
	    centerY = (canvas.width/2),
        radius = (canvas.width/3),
        outerRadius = (canvas.width/2);

	// Create a speedometer object using Javascript object notation
	return {
		ctx: canvas.getContext('2d'),
		speed: iSpeed,
		center:	{
			X: centerX,
			Y: centerY
		},
		levelRadius: radius - 10,
		gaugeOptions: {
			center:	{
				X: centerX,
				Y: centerY
			},
			radius: radius
		},
		radius: outerRadius,
		max: maxSpeed
	};
}

function clearCanvas(options)
{
	options.ctx.clearRect(0, 0, 150, 75);
	applyDefaultContextSettings(options);
}

function drawSpd(current, base, canvas)
{
	/* Main entry point for drawing the speedometer */

	if (canvas !== null && canvas.getContext)
	{
		var options = buildOptionsAsJSON(canvas, current, base);

	    // Clear canvas
	    clearCanvas(options);

		// Draw tick marks
		drawSmallTickMarks(options);

		// Draw labels on markers
		drawTextMarkers(options);

		// Draw speeometer colour arc
		drawSpeedometerColourArc(options);

		// Draw the needle and base
		drawNeedle(options);
		
	}	
}

/*
@https://github.com/rheh/HTML5-canvas-projects
end
*/