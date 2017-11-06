var testNumber = 0;
var arrayOfTests = new Array();
var maxTests = 3;
var currentTestsRunning = 0;
var phpUnitVerify = false;

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
				document.getElementById("groupsPlaceHodler").innerHTML = "<form id='groupsIncludeListForm'>"+testsHtml;
				document.getElementById("groupExcludePlaceHolder").innerHTML = "<form  id='groupsExcludeListForm'>"+testsHtml;
				document.getElementById("testsPlaceHolder").innerHTML = "";
				
			}
		});
	}
	else
	{
		document.getElementById("groupsPlaceHodler").innerHTML = "";
		document.getElementById("groupExcludePlaceHolder").innerHTML = "";
		document.getElementById("testsPlaceHolder").innerHTML = "";
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
			var testsHtml = "";
			if(tests.length > 0)
			{
				testsHtml += "<button onclick='runTests();'> Run Tests </button><br><form id='testsListForm'><ul class='list'>";
				for (var i = tests.length - 1; i >= 0; i--) {
					testsHtml += "<li><input type='checkbox' checked name='"+tests[i]+"'>"+tests[i]+"</li>";
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

	var innerArrayOfTests = new Array();

	for (var i = groupsExclude.length - 1; i >= 0; i--) {
		listOfNames.push(groupsExclude[i]["name"]);
	}

	for (var i = listOfNames.length - 1; i >= 0; i--) {
		innerArrayOfTests.push(listOfNames[i]);
		progressBlocksHtml += "<div id='Test"+testNumber+listOfNames[i]+"' class='block blockEmpty'></div>";
	}

	var arrayForNewTestArray = {
		name: testNumber,
		tests: innerArrayOfTests,
		count: 0,
		startCount: 0,
		total: innerArrayOfTests.length
		};

	arrayOfTests.push(arrayForNewTestArray)
 	
	//create display for thing 
	var item = $("#storage .container").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	item = item.replace(/{{file}}/g, document.getElementById("fileListSelector").value);
	item = item.replace(/{{ProgressBlocks}}/g, progressBlocksHtml);
	$("#main").append(item);

	//remove add stuff
	$("#Test"+testNumber).remove();
}

function showStartTestNewPopup()
{
	testNumber++;
	var targetWidthMargin = window.innerWidth;
	targetWidthMargin = (targetWidthMargin - 1000)/2;
	var item = $("#storage .newTestPopup").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	$("#main").append(item);
	document.getElementById("Test"+testNumber).style.marginLeft = targetWidthMargin+"px";
}

function poll()
{
	if(arrayOfTests.length > 0)
	{
		if(arrayOfTests[0]["tests"].length > 0)
		{
			if(currentTestsRunning < maxTests)
			{
				var testNumberLocal = arrayOfTests[0]["name"];
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).classList.remove("blockEmpty");
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).classList.add("blockInProgress");
				document.getElementById("Test"+testNumberLocal+arrayOfTests[0]["tests"][0]).title = "{ "+arrayOfTests[0]["tests"][0]+" Test In Progress}";

				arrayOfTests[0]["startCount"]++;
				document.getElementById("Test"+testNumberLocal+"ProgressStart").value = ((arrayOfTests[0]["startCount"]/arrayOfTests[0]["total"]).toFixed(2));

				var valueForFile = document.getElementById("Test"+testNumberLocal+"File").value;
				var data = {id: "Test"+testNumberLocal, testName: arrayOfTests[0]["tests"][0]};
				var urlForSend = '../core/php/runTest.php?format=json';

				(function(_data){
					$.ajax(
					{
						url: urlForSend,
						dataType: "json",
						data: {filter: arrayOfTests[0]["tests"][0], file: valueForFile },
						type: "POST",
						success(data)
						{
							var result = data["Result"];
							document.getElementById(_data["id"]+_data["testName"]).classList.remove("blockInProgress");
							document.getElementById(_data["id"]+_data["testName"]).title = _data['testName']+" "+data['timeMem'];

							//update percent
							arrayOfTests[0]["count"]++;
							var percentValue = (arrayOfTests[0]["count"]/arrayOfTests[0]["total"]);
							if(percentValue !== 1)
							{
								document.getElementById(_data["id"]+"ProgressTxt").innerHTML = ""+((100*percentValue).toFixed(2))+"%";
								document.getElementById(_data["id"]+"Progress").value = (percentValue.toFixed(2));
							}
							else
							{
								document.getElementById(_data["id"]+"ProgressTxt").innerHTML = "Finished";
								document.getElementById(_data["id"]+"Progress").value = 1;
							}

							if(result === "Passed")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockPass");
								document.getElementById(_data["id"]+_data["testName"]).title += " Passed";
							}
							else if(result === "Error")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
								document.getElementById(_data["id"]+_data["testName"]).title += " Errored";
								console.log(data['output']);
							}
							else if(result === "Failed")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockFail");
								document.getElementById(_data["id"]+_data["testName"]).title += " Failed";
								console.log(data['output']);
							}
							else if(result === "Skipped")
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockSkip");
								document.getElementById(_data["id"]+_data["testName"]).title += " Skipped";
								console.log(data['output']);
							}
							else
							{
								document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
							}
							currentTestsRunning--;
						}
					});
				}(data));

				currentTestsRunning++;
				arrayOfTests[0]["tests"].shift();
			}
		}
		else
		{
			if(currentTestsRunning === 0)
			{
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
				if(!data)
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