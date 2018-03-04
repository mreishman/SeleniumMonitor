var testNumber = new Date().getTime();
var arrayOfTests = new Array();
var maxTests = 3;
var currentTestsRunning = 0;
var currentAjaxRequestNum = 0;
var phpUnitVerify = false;
var pausePoll = false;
var pausePollAjaxDelay = false;
var maxTestsStatic = 1;
var ajaxRequestValue = 3;
var testsPerAjax = 1;
var totalTimeOfAllTests = new Array();

function getFileList()
{
	var urlForSend = '../core/php/returnFileContent.php?format=json';
	var valueForFile = document.getElementById("fileListSelector").value;
	if(valueForFile !== "PLACEHOLDER")
	{
		var data = {file: valueForFile };
		$.ajax(
		{
			url: urlForSend,
			dataType: "json",
			data,
			type: "POST",
			success(data)
			{
				var tests = data['arrayOfGroups'];
				var testsHtml = "<ul class='list'>";
				var groups = Object.keys(tests);
				var lengthOfGroups = groups.length;
				for(var j = 0; j < lengthOfGroups; j++)
				{

					testsHtml += "<li><input  onchange='getTestList();' type='checkbox' name='"+groups[j]+"'>"+groups[j]+" ("+tests[groups[j]]+")</li>";
				}
				testsHtml += "</ul></form>";
				document.getElementById("groupsPlaceHodler").innerHTML = "<a onclick=\"toggleCheckBoxes('groupsIncludeListForm', true);\" class=\"link\">Check All</a> <a onclick=\"toggleCheckBoxes('groupsIncludeListForm', false);\" class=\"link\">Uncheck All</a><form id='groupsIncludeListForm'>"+testsHtml;
				document.getElementById("groupExcludePlaceHolder").innerHTML = "<a onclick=\"toggleCheckBoxes('groupsExcludeListForm', true);\" class=\"link\">Check All</a> <a onclick=\"toggleCheckBoxes('groupsExcludeListForm', false);\" class=\"link\">Uncheck All</a><form  id='groupsExcludeListForm'>"+testsHtml;
				document.getElementById("testsPlaceHolder").innerHTML = "";
				document.getElementById("testCount").innerHTML =  "";
			}
		});
	}
	else
	{
		document.getElementById("groupsPlaceHodler").innerHTML = "";
		document.getElementById("groupExcludePlaceHolder").innerHTML = "";
		document.getElementById("testsPlaceHolder").innerHTML = "";
		document.getElementById("testCount").innerHTML =  "";
	}
}

function getTestList()
{
	var urlForSend = '../core/php/returnListOfTests.php?format=json';
	var groupsInclude = $("#groupsIncludeListForm").serializeArray();
	if(!(groupsInclude.length > 0))
	{
		groupsInclude = "empty array";
	}
	var groupsExclude = $("#groupsExcludeListForm").serializeArray();
	if(!(groupsExclude.length > 0))
	{
		groupsExclude = "empty array";
	}
	var valueForFile = document.getElementById("fileListSelector").value;
	var data = {groupsInclude, groupsExclude, file: valueForFile };
	$.ajax(
	{
		url: urlForSend,
		dataType: "json",
		data,
		type: "POST",
		success(data)
		{
			var tests = data["testList"];
			document.getElementById("testCount").innerHTML =  data["testListCount"]+"/"+data["testListCount"];
			var testsHtml = "";
			if(tests.length > 0)
			{
				testsHtml += "<a class=\"link\" onclick='runTests();'> Run Tests </a> <a onclick=\"toggleCheckBoxes('testsListForm', true); updateCount('testsListForm');\" class=\"link\">Check All</a> <a onclick=\"toggleCheckBoxes('testsListForm', false); updateCount('testsListForm');\" class=\"link\">Uncheck All</a> <br><form id='testsListForm'><ul class='list'>";
				for (var i = tests.length - 1; i >= 0; i--) {
					testsHtml += "<li><input onchange=\"updateCount('testsListForm');\" type='checkbox' checked name='"+tests[i]+"'>"+tests[i]+"</li>";
				}
				testsHtml += "</ul></form>";
			}
			document.getElementById("testsPlaceHolder").innerHTML = testsHtml;
		}
	});
}

function isEmpty(obj) {
    for(var prop in obj) {
        if(obj.hasOwnProperty(prop))
            return false;
    }

    return JSON.stringify(obj) === JSON.stringify({});
}

function runTests()
{
	//get list of tests, add to array
	var groupsExclude = $("#testsListForm").serializeArray();
	var listOfNames = new Array();
	var progressBlocksHtml = "";
	placeholderBaseUrl = document.getElementById("baseUrlInput").value;
	var innerArrayOfTests = new Array();

	for (var i = groupsExclude.length - 1; i >= 0; i--)
	{
		listOfNames.push(groupsExclude[i]["name"]);
	}

	for (var i = listOfNames.length - 1; i >= 0; i--)
	{
		innerArrayOfTests.push(listOfNames[i]);
		progressBlocksHtml += "<div onclick=\"showTestPopup('Test"+testNumber+listOfNames[i]+"popup');\" title='"+listOfNames[i]+"' id='Test"+testNumber+listOfNames[i]+"' class='block blockEmpty'>";
		progressBlocksHtml += "<input type=\"hidden\" value=\""+listOfNames[i]+"\" id='Test"+testNumber+listOfNames[i]+"TestName' >";
		progressBlocksHtml += "</div>";
		progressBlocksHtml += "<div class=\"testPopupBlock\" id='Test"+testNumber+listOfNames[i]+"popup'> <h3> Test: "+listOfNames[i]+" </h3> <br> <span id='Test"+testNumber+listOfNames[i]+"popupSpan' ><p> Pending Start </p></span>";
		progressBlocksHtml += " </div>";
	}

	var arrayForNewTestArray = {
		name: testNumber,
		tests: innerArrayOfTests,
		count: 0,
		startCount: 0,
		passedCount: 0,
		errorCount: 0,
		failCount: 0,
		skipCount: 0,
		riskyCount: 0,
		total: innerArrayOfTests.length
		};

	arrayOfTests.push(arrayForNewTestArray);

	var etaHtml = "ETA: ---";
	if(totalTimeOfAllTests.length > 0)
	{
		etaHtml = "ETA: "+getEta("Test"+testNumber, innerArrayOfTests.length);
	}
 	
	//create display for thing 
	var item = $("#storage .container").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	item = item.replace(/{{file}}/g, document.getElementById("fileListSelector").value);
	item = item.replace(/{{baseUrl}}/g, document.getElementById("baseUrlInput").value);
	item = item.replace(/{{totalCount}}/g, innerArrayOfTests.length);
	item = item.replace(/{{ProgressBlocks}}/g, progressBlocksHtml);
	item = item.replace(/{{eta}}/g, etaHtml);
	$("#main").append(item);

	//remove add stuff
	$("#Test"+testNumber).remove();
	showStartTestNewPopup();
}

function getMeanOfTotalTimeCount()
{
	var total = 0;
	for (var i = totalTimeOfAllTests.length - 1; i >= 0; i--)
	{
		total += totalTimeOfAllTests[i];
	}
	return total/totalTimeOfAllTests.length;
}

function showStartTestNewPopup()
{
	$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
	{
		createNewTestPopup(data);
	});
}

function createNewTestPopup(data)
{
	maxTestsStatic = getMaxConcurrentTests(data);
	var maxRequests = 5;
	if (maxTestsStatic < 5)
	{
		maxRequests = maxTestsStatic;
	}
	testNumber = new Date().getTime();
	var targetWidthMargin = window.innerWidth;
	targetWidthMargin = (targetWidthMargin - 1000)/2;
	var item = $("#storage .newTestPopup").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	item = item.replace(/{{baseUrlInput}}/g, placeholderBaseUrl);
	var maxTestsHtml = "<ul style=\"list-style: none;\">";
	maxTestsHtml += "<li>Number Of Ajax Requests <input id=\"inputForAjaxRequest\" onchange=\"adjustAjaxRequestValueFromInput();\" type=\"text\" value=\""+ajaxRequestValue+"\" style=\"width: 30px;\" > <input onchange=\"adjustAjaxRequestValueFromSlider();\" id=\"sliderForAjaxRequest\" type=\"range\" min=\"1\" max=\""+maxRequests+"\" value=\""+ajaxRequestValue+"\" ></li>";
	maxTestsHtml += "<li>Number Of Tests Per Request <input onchange=\"adjustTestsPerRequestValueFromInput();\" id=\"inputForTestPerRequest\" type=\"text\" value=\""+testsPerAjax+"\"  style=\"width: 30px;\" >  <input onchange=\"adjustTestsPerRequestValueFromSlider();\" id=\"sliderForTestPerRequest\" type=\"range\" min=\"1\" max=\""+((maxTestsStatic-(maxTestsStatic%2))/2)+"\" value=\""+testsPerAjax+"\" ></li>";
	maxTestsHtml += "</ul>";
	item = item.replace(/{{maxTestsNum}}/g, maxTestsHtml);
	$("#main").append(item);
	document.getElementById("Test"+testNumber).style.marginLeft = targetWidthMargin+"px";
}

function adjustAjaxRequestValueFromSlider()
{
	var sliderValue = document.getElementById("sliderForAjaxRequest").value;
	document.getElementById("inputForAjaxRequest").value = sliderValue;
	adjustAjaxReuqestValueSub(sliderValue);
}

function adjustAjaxRequestValueFromInput()
{
	var sliderValue = document.getElementById("inputForAjaxRequest").value;
	var maxValue = document.getElementById("sliderForAjaxRequest").max;
	if(sliderValue > maxValue)
	{
		sliderValue = maxValue;
		document.getElementById("inputForAjaxRequest").value = maxValue;
	}
	document.getElementById("sliderForAjaxRequest").value = sliderValue;
	adjustAjaxReuqestValueSub(sliderValue);
}

function adjustTestsPerRequestValueFromSlider()
{
	var sliderValue = document.getElementById("sliderForTestPerRequest").value;
	document.getElementById("inputForTestPerRequest").value = sliderValue;
	adjustTestsPerRequestValueSub(sliderValue);
}

function adjustTestsPerRequestValueFromInput()
{
	var sliderValue = document.getElementById("inputForTestPerRequest").value;
	var maxValue = document.getElementById("sliderForTestPerRequest").max;
	if(sliderValue > maxValue)
	{
		sliderValue = maxValue;
		document.getElementById("inputForTestPerRequest").value = maxValue;
	}
	document.getElementById("sliderForTestPerRequest").value = sliderValue;
	adjustTestsPerRequestValueSub(sliderValue);
}

function adjustTestsPerRequestValueSub(sliderValue)
{
	testsPerAjax = sliderValue;
	var ajaxRequestNum = document.getElementById("inputForAjaxRequest").value;
	var ajaxRequestNumStatic = ajaxRequestNum;
	while(!checkIfAjaxRequestTestRequestIsSupported(sliderValue, ajaxRequestNum) && ajaxRequestNum > 1)
	{
		ajaxRequestNum--;
	}
	if(ajaxRequestNum !== ajaxRequestNumStatic)
	{
		document.getElementById("inputForAjaxRequest").value = ajaxRequestNum;
		document.getElementById("sliderForAjaxRequest").value = ajaxRequestNum;
		ajaxRequestValue = ajaxRequestNum;
	}
}

function adjustAjaxReuqestValueSub(sliderValue)
{
	ajaxRequestValue = sliderValue;
	var testRequestValue = document.getElementById("inputForTestPerRequest").value;
	var testRequestValueStatic = testRequestValue;
	while(!checkIfAjaxRequestTestRequestIsSupported(sliderValue, testRequestValue) && testRequestValue > 1)
	{
		testRequestValue--;
	}
	if(testRequestValue !== testRequestValueStatic)
	{
		document.getElementById("inputForTestPerRequest").value = testRequestValue;
		document.getElementById("sliderForTestPerRequest").value = testRequestValue;
		testsPerAjax = testRequestValue;
	}
}

function checkIfAjaxRequestTestRequestIsSupported(ajaxRequestValueCheck, testRequestValueCheck)
{
	if((ajaxRequestValueCheck * testRequestValueCheck) > maxTestsStatic)
	{
		return false;
	}
	return true;
}

function poll()
{
	if(!pausePoll && !pausePollAjaxDelay)
	{
		if(arrayOfTests.length > 0)
		{
			if(arrayOfTests[0]["tests"].length > 0)
			{
				if(currentTestsRunning < (ajaxRequestValue * testsPerAjax))
				{
					if(currentAjaxRequestNum < ajaxRequestValue)
					{
						//ajax check
						if(runCheckCount === "true")
						{
							pausePollAjaxDelay = true;

							$.getJSON("../core/php/getMainServerInfo.php", {}, function(data) 
							{
								pausePollAjaxDelay = false;
								pollInner(data);
							});
						}
						else
						{
							pollInner(false);
						}
					}
				}
			}
			else
			{
				if(currentTestsRunning === 0)
				{
					//end test icon change
					var idOfTest = arrayOfTests[0]["name"];
					if(document.getElementById("Test"+idOfTest+"StopButton"))
					{
						document.getElementById("Test"+idOfTest+"StopButton").style.display = "none";
						document.getElementById("Test"+idOfTest+"RefreshButton").style.display = "inline-block";
					}
					arrayOfTests.shift();
				}
			}
		}
		else
		{
			if(!phpUnitVerify)
			{
				$.getJSON("../core/php/verifyPhpUnit.php", {}, function(data) 
				{
					if(data !== true)
					{
						$(".bannerPHP").show();
					}
					else
					{
						$(".bannerPHP").hide();
						phpUnitVerify = true;
					}
				});
			}
		}
	}
}

function updateProgressBar()
{

}

function updateProgressBarStart(testNumberLocal, firstNum, secondNum)
{
	document.getElementById(testNumberLocal+"ProgressStart").value = ((firstNum/secondNum).toFixed(5));
}

function pollInner(data)
{
	var check = false;
	var currentRunningTestCount = 0;
	if(data)
	{
		check = true;
		maxTestsStatic = getMaxConcurrentTests(data);
		currentRunningTestCount = getCurrentRunningTestCount(data);
	}
	if((maxTestsStatic > currentRunningTestCount && check) || !check)
	{
		var testNumberLocal = arrayOfTests[0]["name"];
		if(document.getElementById("Test"+testNumberLocal))
		{
			var numberOfTestsToRun = testsPerAjax;
			if(testsPerAjax > arrayOfTests[0]["tests"].length)
			{
				numberOfTestsToRun = arrayOfTests[0]["tests"].length;
			}
			var id = {};
			var testName = {};
			var valueForFile = {};
			var timeStart = {};
			for (var i = numberOfTestsToRun - 1; i >= 0; i--)
			{
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][i]).classList.remove("blockEmpty");
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][i]).classList.add("blockInProgress");
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][i]).title = arrayOfTests[0]["tests"][0]+" Test In Progress";
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][i]+"popupSpan").innerHTML ="<p>Test In Progress</p>";

				arrayOfTests[0]["startCount"]++;
				updateProgressBarStart("Test"+testNumberLocal, arrayOfTests[0]["startCount"], arrayOfTests[0]["total"]);

				valueForFile[i] = document.getElementById("Test"+testNumberLocal+"File").value;
				id[i] = "Test"+testNumberLocal;
				testName[i] = arrayOfTests[0]["tests"][i];
				timeStart[i] = performance.now();
			}
			var data = {id , testName , numberOfTestsToRun, timeStart};
			var urlForSend = '../core/php/runTest.php?format=json';

			(function(_data){
				$.ajax(
				{
					url: urlForSend,
					dataType: "json",
					data: {filter: testName, file: valueForFile, baseUrl: document.getElementById("Test"+testNumberLocal+"BaseUrl").value, numberOfTestsToRun},
					type: "POST",
					success(data)
					{
						for (var i = _data["numberOfTestsToRun"] - 1; i >= 0; i--)
						{
							var result = data[i]["Result"];
							if(document.getElementById(_data["id"][i]))
							{
								document.getElementById(_data["id"][i]+_data["testName"][i]).classList.remove("blockInProgress");
								document.getElementById(_data["id"][i]+_data["testName"][i]).title = _data['testName'][i]+" "+data[i]['timeMem'];

								var arrayForOutput = "<table style='width: 100%; border-bottom: 1px solid black;'>";
								var endFound = false;
								if(data[i]['output'].length > 11)
								{
									for (var j = 11; j < data[i]['output'].length; j++)
									{
										if(j === 11)
										{
											data[i]['output'][j] = data[i]['output'][j].substring(3);
										}
										if(!endFound)
										{
											if(data[i]['output'][j] === "FAILURES!" || data[i]['output'][j] === "ERRORS!")
											{
												endFound = true;
											}
										}
										if(!endFound)
										{
											arrayForOutput += "<tr><td>"+(data[i]['output'][j])+"</td><tr>";
										}
									}
								}
								else
								{
									arrayForOutput = "<table style='width: 100%; border-bottom: 1px solid black;'>";
									for (var j = 0; j < data[i]['output'].length; j++)
									{
										arrayForOutput += "<tr><td>"+(data[i]['output'][j])+"</td><tr>";
									}
								}
								arrayForOutput += "</table>";

								var arrayForPopup = "<table>";
								for (var j = 0; j < data[i]['output'].length; j++)
								{
									arrayForPopup += "<tr><td>"+(data[i]['output'][j])+"</td><tr>";
								}
								arrayForPopup += "</table>";

								document.getElementById(_data["id"][i]+_data["testName"][i]+"popupSpan").innerHTML = arrayForPopup;

								if(result === "Passed")
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockPass");
									document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Passed";
									arrayOfTests[0]["passedCount"]++;
								}
								else if(result === "Error")
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockError");
									document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Errored";
									arrayOfTests[0]["errorCount"]++;
									document.getElementById(_data["id"][i]+"ErrorCount").innerHTML = arrayOfTests[0]["errorCount"];
									document.getElementById(_data["id"][i]+"Errors").innerHTML += arrayForOutput;
								}
								else if(result === "Failed")
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockFail");
									document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Failed";
									arrayOfTests[0]["failCount"]++;
									document.getElementById(_data["id"][i]+"FailCount").innerHTML = arrayOfTests[0]["failCount"];
									document.getElementById(_data["id"][i]+"Fails").innerHTML += arrayForOutput;
								}
								else if(result === "Skipped")
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockSkip");
									document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Skipped";
									arrayOfTests[0]["skipCount"]++;
								}
								else if(result === "Risky")
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockRisky");
									document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Risky";
									arrayOfTests[0]["riskyCount"]++;
								}
								else
								{
									document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockError");
								}
							}
						}
					},
					error(xhr, error)
					{
						for (var i = _data["numberOfTestsToRun"] - 1; i >= 0; i--)
						{
							if(document.getElementById(_data["id"][i]))
							{
								document.getElementById(_data["id"][i]+_data["testName"][i]).classList.remove("blockInProgress");
								document.getElementById(_data["id"][i]+_data["testName"][i]).title = _data['testName'][i];

								var arrayForPopup = "<table>";
								arrayForPopup += "<tr><td>"+JSON.stringify(xhr)+"</td><tr>";
								arrayForPopup += "<tr><td>"+JSON.stringify(error)+"</td><tr>";
								arrayForPopup += "</table>";
								document.getElementById(_data["id"][i]+_data["testName"][i]+"popupSpan").innerHTML = arrayForPopup;

								document.getElementById(_data["id"][i]+_data["testName"][i]).classList.add("blockError");
								document.getElementById(_data["id"][i]+_data["testName"][i]).title += " Errored";
								arrayOfTests[0]["errorCount"]++;
							}
						}
					},
					complete(data)
					{
						for (var i = _data["numberOfTestsToRun"] - 1; i >= 0; i--)
						{
							var currentTime = performance.now();
							totalTimeOfAllTests.push(Math.round((currentTime - _data["timeStart"][i])/1000));
							if(document.getElementById(_data["id"][i]))
							{
								//update percent
								arrayOfTests[0]["count"]++;
								var percentValue = (arrayOfTests[0]["count"]/arrayOfTests[0]["total"]);
								if(percentValue !== 1)
								{
									document.getElementById(_data["id"][i]+"ProgressTxt").innerHTML = ""+((100*percentValue).toFixed(2))+"%";
									document.getElementById(_data["id"][i]+"ProgressCount").innerHTML = ""+arrayOfTests[0]["count"]+"/"+arrayOfTests[0]["total"];
									document.getElementById(_data["id"][i]+"Progress").value = (percentValue.toFixed(5));
									document.getElementById(_data["id"][i]+"EtaTxt").innerHTML = "ETA: "+getEta(_data["id"][i], (arrayOfTests[0]["total"]-arrayOfTests[0]["count"]));
									
								}
								else
								{
									document.getElementById(_data["id"][i]+"ProgressTxt").innerHTML = "Finished";
									document.getElementById(_data["id"][i]+"ProgressCount").innerHTML = "Finished";
									document.getElementById(_data["id"][i]+"EtaTxt").innerHTML = "ETA: --";
									document.getElementById(_data["id"][i]+"Progress").value = 1;
								}
								
							}
							currentTestsRunning--;
						}
						currentAjaxRequestNum--;
						//make ajax request to save current data
						if(cacheTestEnable === "true")
						{
							var urlForSendInner = '../core/php/saveTestObject.php?format=json';
							var dataSend = {testName: _data["id"][0], data: JSON.stringify(generateExportInfo(_data["id"][0]))};
							$.ajax(
							{
								url: urlForSendInner,
								dataType: "json",
								data: dataSend,
								type: "POST",
								success(data)
								{

								}
							});
						}
					}
				});
			}(data));

			for (var i = numberOfTestsToRun - 1; i >= 0; i--)
			{
				arrayOfTests[0]["tests"].shift();
				currentTestsRunning++;
			}
			currentAjaxRequestNum++;
		}
		else
		{
			if(currentTestsRunning === 0)
			{
				arrayOfTests.shift();
			}
		}
	}
}

function getEta(idOfTest, testsLeft)
{
	var currentTimePerTest = getMeanOfTotalTimeCount();
	var currentTestsAtATime = testsPerAjax * ajaxRequestValue;
	var timeLeft = currentTimePerTest * testsLeft / currentTestsAtATime;
	return convertSecToCorrectFormat(idOfTest, timeLeft, "EtaSec");
}

function convertSecToCorrectFormat(idOfTest, timeLeft, type)
{
	if(document.getElementById(idOfTest+type))
	{
		document.getElementById(idOfTest+type).value = timeLeft;
	}
	var days = 0;
	while(timeLeft > 86400)
	{
		days++;
		timeLeft -= 86400;
	}
	var date = new Date(null);
	date.setSeconds(timeLeft);
	var result = date.toISOString().substr(11, 8);
	if(days > 0)
	{
		result = ""+days+":"+result;
	}
	return result;
}

function setMaxNumber(newValue)
{
	maxTests = newValue;
}

function pausePollAction()
{
	if(pausePoll)
	{
		document.getElementById("playImage").style.display = "none";
		document.getElementById("pauseImage").style.display = "inline-block";
	}
	else
	{
		document.getElementById("pauseImage").style.display = "none";
		document.getElementById("playImage").style.display = "inline-block";
	}
	pausePoll = !pausePoll;
}

function deleteTests(idForTest)
{
	document.getElementById(idForTest).remove();
}

function toggleSubtitleEF(idForTest)
{
	if(document.getElementById(idForTest).style.display !== "none")
	{
		document.getElementById(idForTest).style.display = "none";
		document.getElementById(idForTest+"Contract").style.display = "none";
		document.getElementById(idForTest+"Expand").style.display = "block";
	}
	else
	{
		document.getElementById(idForTest).style.display = "block";
		document.getElementById(idForTest+"Contract").style.display = "block";
		document.getElementById(idForTest+"Expand").style.display = "none";
	}
}

function toggleCheckBoxes(formid, showOrHide)
{
	$("#"+formid+" input:checkbox").each(function()
	{
		if(showOrHide)
		{
			$(this).prop('checked', true);
		}
		else
		{
			$(this).prop('checked', false);
		}
	});
	if(formid !== "testsListForm")
	{
		getTestList();
	}
}

function updateCount(formid)
{
	var count = 0;
	var totalCount = 0;
	$("#"+formid+" input:checkbox").each(function()
	{
		if($(this).is(':checked'))
		{
			count++;
		}
		totalCount++;
	});
	document.getElementById("testCount").innerHTML = ""+count+"/"+totalCount;
}

function stopTest(testNumber)
{
	for (var i = arrayOfTests[testNumber]["tests"].length - 1; i >= 0; i--)
	{
		document.getElementById("Test"+arrayOfTests[testNumber]["name"]+arrayOfTests[testNumber]["tests"][i]).classList.remove("blockEmpty");
		document.getElementById("Test"+arrayOfTests[testNumber]["name"]+arrayOfTests[testNumber]["tests"][i]).classList.add("blockSkip");
		document.getElementById("Test"+arrayOfTests[testNumber]["name"]+arrayOfTests[testNumber]["tests"][i]).title = arrayOfTests[testNumber]["tests"][i]+" Skipped";
	}
	arrayOfTests[testNumber]["tests"] = [];
}

function stopAllTests()
{
	for (var i = arrayOfTests.length - 1; i >= 0; i--)
	{
		stopTest(i);
	}
	$(".stopButtonClass").hide();
}

function stopTestById(idOfTest)
{
	for (var i = arrayOfTests.length - 1; i >= 0; i--)
	{
		if("Test"+arrayOfTests[i]["name"] === idOfTest)
		{
			stopTest(i);
			break;
		}
	}
	document.getElementById(idOfTest+"StopButton").style.display = "none";
	document.getElementById(idOfTest+"EtaTxt").innerHTML = "ETA: --";
	document.getElementById(idOfTest+"EtaSec").value = 0;
	document.getElementById(idOfTest+"RefreshButton").style.display = "inline-block";
}

function reRunTestsPopup(idOfTest)
{
	//show popup with checkbox of types
	showPopup();
	document.getElementById('popupContentInnerHTMLDiv').innerHTML = "<div class='settingsHeader' >Re-run tests?</div><br><div style='width:100%;text-align:left;padding-left:10px;padding-right:10px;'>Select the following test groups to re-run<form id='testFormResetForm' ><input type='checkbox' name='blockPass'> Passed  | <input type='checkbox' name='blockError'> Error | <input type='checkbox' name='blockFail'> Fail  <br> <input type='checkbox' name='blockSkip'> Skipped | <input type='checkbox' name='blockRisky'> Risky </form></div><div class='link' onclick='reRunTests(\""+idOfTest+"\");' style='margin-left:125px; margin-top: 8px; margin-right:50px;'>Run</div><div onclick='hidePopup();' class='link'>Cancel</div></div>";
}

function reRunTests(idOfTest)
{

	//schedule tests to re-run with applied filters
	var testReRun = $("#testFormResetForm").serializeArray();
	var arrayOfTestsToBeReRun = new Array();
	var testProgressBlocks = idOfTest+"ProgressBlocks";
	var totalTestCount = $("#"+testProgressBlocks+" .block").length;

	//default vars
	var objectCount = {
		blockPass: $("#"+testProgressBlocks+" .blockPass").length,
		blockError: $("#"+testProgressBlocks+" .blockError").length,
		blockFail: $("#"+testProgressBlocks+" .blockFail").length,
		blockSkip: $("#"+testProgressBlocks+" .blockSkip").length,
		blockRisky: $("#"+testProgressBlocks+" .blockRisky").length
	}

	for (var i = testReRun.length - 1; i >= 0; i--)
	{
		objectCount[testReRun[i]["name"]] = 0;
		var testArray = $("#"+testProgressBlocks+" ."+testReRun[i]["name"]+" input");
		var testArrayLength = testArray.length;
		for (var j = 0; j < testArrayLength; j++)
		{
			arrayOfTestsToBeReRun.push(testArray[j].value);
		}

		testArray = $("#"+testProgressBlocks+" ."+testReRun[i]["name"]);
		for (var j = testArray.length - 1; j >= 0; j--)
		{
			testArray[j].classList.remove(testReRun[i]["name"]);
			testArray[j].classList.add("blockEmpty");
			document.getElementById(testArray[j].id+"popupSpan").innerHTML = "<p> Pending Re-Start </p>";
		}

		if(testReRun[i]["name"] === "blockError")
		{
			document.getElementById(idOfTest+"ErrorCount").innerHTML = 0;
			document.getElementById(idOfTest+"Errors").innerHTML = "";
		}
		else if(testReRun[i]["name"] === "blockFail")
		{
			document.getElementById(idOfTest+"FailCount").innerHTML = 0;
			document.getElementById(idOfTest+"Fails").innerHTML = "";
		}
	}

	var newStart = totalTestCount - arrayOfTestsToBeReRun.length;
	//reset percent bar (get number of tests from boxes)

	updateProgressBarStart(idOfTest, newStart, totalTestCount);

	var percentValue = (newStart/totalTestCount);
	document.getElementById(idOfTest+"ProgressTxt").innerHTML = ""+((100*percentValue).toFixed(2))+"%";
	document.getElementById(idOfTest+"ProgressCount").innerHTML = ""+newStart+"/"+totalTestCount;
	document.getElementById(idOfTest+"Progress").value = (percentValue.toFixed(5));

	var arrayForNewTestArray = {
		name: idOfTest.substring(4),
		tests: arrayOfTestsToBeReRun,
		count: newStart,
		startCount: newStart,
		passedCount: objectCount["blockPass"],  
		errorCount: objectCount["blockError"],
		failCount: objectCount["blockFail"],
		skipCount: objectCount["blockSkip"],
		riskyCount: objectCount["blockRisky"],
		total: totalTestCount
		};

	arrayOfTests.push(arrayForNewTestArray);

	var etaHtml = "ETA: ---";
	if(totalTimeOfAllTests.length > 0)
	{
		etaHtml = "ETA: "+getEta(idOfTest, arrayOfTestsToBeReRun.length);
	}
	document.getElementById(idOfTest+"EtaTxt").innerHTML = etaHtml;

	hidePopup();

	//hide re-run button, show stop button
	document.getElementById(idOfTest+"StopButton").style.display = "inline-block";
	document.getElementById(idOfTest+"RefreshButton").style.display = "none";
}

function togglePercent(idOfTest)
{
	toggleBase(idOfTest+"ProgressTxt", idOfTest+"ProgressCount");
}

function toggleEta(idOfTest)
{
	toggleBase(idOfTest+"EtaTxt", idOfTest+"ElapsedTxt");
}

function toggleBase(hideOne, showOther)
{
	if(document.getElementById(hideOne).style.display === "none")
	{
		document.getElementById(hideOne).style.display = "inline-block";
		document.getElementById(showOther).style.display = "none";
	}
	else
	{
		document.getElementById(hideOne).style.display = "none";
		document.getElementById(showOther).style.display = "inline-block";
	}
}

function timerStuff()
{
	if(arrayOfTests.length > 0 && currentTestsRunning > 0)
	{
		var idOfTest = "Test"+arrayOfTests[0]["name"];
		if(document.getElementById(idOfTest))
		{
			decreaseEtaByOne(idOfTest);
			increaseElapsedTimeByOne(idOfTest);
		}
	}
}

function decreaseEtaByOne(idOfTest)
{
	valueOfInput = parseInt(document.getElementById(idOfTest+"EtaSec").value);
	if(valueOfInput > 1)
	{
		var etaHtml = "ETA: "+convertSecToCorrectFormat(idOfTest, valueOfInput - 1, "EtaSec");
		document.getElementById(idOfTest+"EtaTxt").innerHTML = etaHtml;
	}
}

function increaseElapsedTimeByOne(idOfTest)
{
	var idOfTest = "Test"+arrayOfTests[0]["name"];
	valueOfInput = parseInt(document.getElementById(idOfTest+"ElapsedSec").value);
	var elapsedHtml = "Time Elapsed: "+convertSecToCorrectFormat(idOfTest, valueOfInput + 1, "ElapsedSec");
	document.getElementById(idOfTest+"ElapsedTxt").innerHTML = elapsedHtml;
}

function exportResults(idOfTest)
{
	var stuff = JSON.stringify(generateExportInfo(idOfTest));
	showPopup();
	document.getElementById('popupContentInnerHTMLDiv').innerHTML = "<div class='settingsHeader' >Success</div><br><br><div style='width:100%;text-align:center;'> <img src='../core/img/greenCheck.png' height='50' width='50'> <br> <input type='text' value='"+escapeHtml(stuff)+"' >  </div>";

}

function generateExportInfo(idOfTest)
{
	var testArray = $("#"+idOfTest+"ProgressBlocks input");
	var resultArray = $("#"+idOfTest+" .testPopupBlock span");
	var blockArray = $("#"+idOfTest+" .block");
	/* test: {result: ____, notes: ____, title: ____} */
	var exportInfo = {
		file: $("#"+idOfTest+"Folder").html(),
		website: $("#"+idOfTest+"BaseUrl").val(),
		info: {}
	};
	var lengthOfArray = testArray.length;
	for (var i = 0; i < lengthOfArray; i++)
	{
		var testName = testArray[i].id.replace(idOfTest, "");
		var result = blockArray[i].className.split(/\s+/);
		var title = blockArray[i].title;
		var notes = "Error in generating export of notes";
		if(typeof(resultArray[i]) !== "undefined")
		{
			notes = resultArray[i].innerHTML;
		}
		exportInfo["info"][testName] = {
			result: result,
			notes: notes,
			title: title
		}
	}
	return exportInfo;
}