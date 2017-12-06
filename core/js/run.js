var testNumber = 0;
var arrayOfTests = new Array();
var maxTests = 3;
var currentTestsRunning = 0;
var phpUnitVerify = false;
var pausePoll = false;
var pausePollAjaxDelay = false;

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
				for (var i = tests.length - 1; i >= 0; i--) {
					testsHtml += "<li><input  onchange='getTestList();' type='checkbox' name='"+tests[i]+"'>"+tests[i]+"</li>";
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
 	
	//create display for thing 
	var item = $("#storage .container").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	item = item.replace(/{{file}}/g, document.getElementById("fileListSelector").value);
	item = item.replace(/{{baseUrl}}/g, document.getElementById("baseUrlInput").value);
	item = item.replace(/{{totalCount}}/g, innerArrayOfTests.length);
	item = item.replace(/{{ProgressBlocks}}/g, progressBlocksHtml);
	$("#main").append(item);

	//remove add stuff
	$("#Test"+testNumber).remove();
	showStartTestNewPopup();
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
	var maxTestsStatic = getMaxConcurrentTests(data);
	testNumber++;
	var targetWidthMargin = window.innerWidth;
	targetWidthMargin = (targetWidthMargin - 1000)/2;
	var item = $("#storage .newTestPopup").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	item = item.replace(/{{baseUrlInput}}/g, placeholderBaseUrl);
	var maxTestsHtml = "<ul style=\"list-style: none;\">";
	for (var i = 1; i <= maxTestsStatic; i++)
	{
		maxTestsHtml += "<li><input style=\"width: auto;\" ";
		if(i === maxTests)
		{
			maxTestsHtml += " checked ";
		}
		maxTestsHtml += " onclick=\"setMaxNumber("+i+");\" type=\"radio\" name=\"maxTests\" value=\""+i+"\">"+i+"</li>";
	}
	maxTestsHtml += "</ul>";
	item = item.replace(/{{maxTestsNum}}/g, maxTestsHtml);
	$("#main").append(item);
	document.getElementById("Test"+testNumber).style.marginLeft = targetWidthMargin+"px";
}

function poll()
{
	if(!pausePoll && !pausePollAjaxDelay)
	{
		if(arrayOfTests.length > 0)
		{
			if(arrayOfTests[0]["tests"].length > 0)
			{
				if(currentTestsRunning < maxTests)
				{
					//ajax check
					if(false)
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
	var maxTestsStatic = 10;
	var currentRunningTestCount = 0;
	if(data)
	{
		maxTestsStatic = getMaxConcurrentTests(data);
		currentRunningTestCount = getCurrentRunningTestCount(data);
	}
	if(maxTestsStatic > currentRunningTestCount)
	{
		var testNumberLocal = arrayOfTests[0]["name"];
		if(document.getElementById("Test"+testNumberLocal))
		{
			document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).classList.remove("blockEmpty");
			document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).classList.add("blockInProgress");
			document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).title = arrayOfTests[0]["tests"][0]+" Test In Progress";
			document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]+"popupSpan").innerHTML ="<p>Test In Progress</p>";

			arrayOfTests[0]["startCount"]++;
			updateProgressBarStart("Test"+testNumberLocal, arrayOfTests[0]["startCount"], arrayOfTests[0]["total"]);

			var valueForFile = document.getElementById("Test"+testNumberLocal+"File").value;
			var data = {id: "Test"+testNumberLocal, testName: arrayOfTests[0]["tests"][0]};
			var urlForSend = '../core/php/runTest.php?format=json';

			(function(_data){
				$.ajax(
				{
					url: urlForSend,
					dataType: "json",
					data: {filter: arrayOfTests[0]["tests"][0], file: valueForFile, baseUrl: document.getElementById("Test"+testNumberLocal+"BaseUrl").value},
					type: "POST",
					success(data)
					{
						var result = data["Result"];
						if(document.getElementById(_data["id"]))
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.remove("blockInProgress");
							document.getElementById(_data["id"]+_data["testName"]).title = _data['testName']+" "+data['timeMem'];

							var arrayForOutput = "<table style='width: 100%; border-bottom: 1px solid black;'>";
							var endFound = false;
							if(data['output'].length > 11)
							{
								for (var i = 11; i < data['output'].length; i++)
								{
									if(i === 11)
									{
										data['output'][i] = data['output'][i].substring(3);
									}
									if(!endFound)
									{
										if(data['output'][i] === "FAILURES!" || data['output'][i] === "ERRORS!")
										{
											endFound = true;
										}
									}
									if(!endFound)
									{
										arrayForOutput += "<tr><td>"+(data['output'][i])+"</td><tr>";
									}
								}
							}
							else
							{
								arrayForOutput = "<table style='width: 100%; border-bottom: 1px solid black;'>";
								for (var i = 0; i < data['output'].length; i++)
								{
									arrayForOutput += "<tr><td>"+(data['output'][i])+"</td><tr>";
								}
							}
							arrayForOutput += "</table>";

							var arrayForPopup = "<table>";
							for (var i = 0; i < data['output'].length; i++)
							{
								arrayForPopup += "<tr><td>"+(data['output'][i])+"</td><tr>";
							}
							arrayForPopup += "</table>";

							document.getElementById(_data["id"]+_data["testName"]+"popupSpan").innerHTML = arrayForPopup;

							if(result === "Passed")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockPass");
								document.getElementById(_data["id"]+_data["testName"]).title += " Passed";
								arrayOfTests[0]["passedCount"]++;
							}
							else if(result === "Error")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
								document.getElementById(_data["id"]+_data["testName"]).title += " Errored";
								arrayOfTests[0]["errorCount"]++;
								document.getElementById(_data["id"]+"ErrorCount").innerHTML = arrayOfTests[0]["errorCount"];
								document.getElementById(_data["id"]+"Errors").innerHTML += arrayForOutput;
							}
							else if(result === "Failed")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockFail");
								document.getElementById(_data["id"]+_data["testName"]).title += " Failed";
								arrayOfTests[0]["failCount"]++;
								document.getElementById(_data["id"]+"FailCount").innerHTML = arrayOfTests[0]["failCount"];
								document.getElementById(_data["id"]+"Fails").innerHTML += arrayForOutput;
							}
							else if(result === "Skipped")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockSkip");
								document.getElementById(_data["id"]+_data["testName"]).title += " Skipped";
								arrayOfTests[0]["skipCount"]++;
							}
							else if(result === "Risky")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockRisky");
								document.getElementById(_data["id"]+_data["testName"]).title += " Risky";
								arrayOfTests[0]["riskyCount"]++;
							}
							else
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
							}
						}
					},
					error(xhr, error)
					{
						if(document.getElementById(_data["id"]))
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.remove("blockInProgress");
							document.getElementById(_data["id"]+_data["testName"]).title = _data['testName'];

							var arrayForPopup = "<table>";
							arrayForPopup += "<tr><td>"+JSON.stringify(xhr)+"</td><tr>";
							arrayForPopup += "<tr><td>"+JSON.stringify(error)+"</td><tr>";
							arrayForPopup += "</table>";
							document.getElementById(_data["id"]+_data["testName"]+"popupSpan").innerHTML = arrayForPopup;

							document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
							document.getElementById(_data["id"]+_data["testName"]).title += " Errored";
							arrayOfTests[0]["errorCount"]++;
						}
					},
					complete(data)
					{
						if(document.getElementById(_data["id"]))
						{
							//update percent
							arrayOfTests[0]["count"]++;
							var percentValue = (arrayOfTests[0]["count"]/arrayOfTests[0]["total"]);
							if(percentValue !== 1)
							{
								document.getElementById(_data["id"]+"ProgressTxt").innerHTML = ""+((100*percentValue).toFixed(2))+"%";
								document.getElementById(_data["id"]+"Progress").value = (percentValue.toFixed(5));
							}
							else
							{
								document.getElementById(_data["id"]+"ProgressTxt").innerHTML = "Finished";
								document.getElementById(_data["id"]+"Progress").value = 1;
							}
							
						}
						currentTestsRunning--;
					}
				});
			}(data));

			currentTestsRunning++;
			arrayOfTests[0]["tests"].shift();
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
}

function showTestPopup(idOfTest)
{
	if(document.getElementById(idOfTest).style.display === "block")
	{
		document.getElementById(idOfTest).style.display = "none";
	}
	else
	{
		document.getElementById(idOfTest).style.display = "block";
	}
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
			document.getElementById(idOfTest+"ErrorCount").innerHTML = "";
			document.getElementById(idOfTest+"Errors").innerHTML = 0;
		}
		else if(testReRun[i]["name"] === "blockFail")
		{
			document.getElementById(idOfTest+"FailCount").innerHTML = "";
			document.getElementById(idOfTest+"Fails").innerHTML = 0;
		}
	}

	var newStart = totalTestCount - arrayOfTestsToBeReRun.length;
	//reset percent bar (get number of tests from boxes)

	updateProgressBarStart(idOfTest, newStart, totalTestCount);

	var percentValue = (newStart/totalTestCount);
	document.getElementById(idOfTest+"ProgressTxt").innerHTML = ""+((100*percentValue).toFixed(2))+"%";
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

	hidePopup();

	//hide re-run button, show stop button
	document.getElementById(idOfTest+"StopButton").style.display = "inline-block";
	document.getElementById(idOfTest+"RefreshButton").style.display = "none";
}