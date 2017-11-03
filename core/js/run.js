var testNumber = 0;
var arrayOfTests = new Array();
var maxTests = 3;
var currentTestsRunning = 0;


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

	for (var i = groupsExclude.length - 1; i >= 0; i--) {
		listOfNames.push(groupsExclude[i]["name"]);
	}

	for (var i = listOfNames.length - 1; i >= 0; i--) {
		arrayOfTests.push(listOfNames[i]);
		progressBlocksHtml += "<div id='Test"+testNumber+listOfNames[i]+"' class='block blockEmpty'></div>";
	}
 	
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
		if(currentTestsRunning < maxTests)
		{
			document.getElementById("Test"+testNumber+arrayOfTests[0]).classList.remove("blockEmpty");
			document.getElementById("Test"+testNumber+arrayOfTests[0]).classList.add("blockInProgress");
			document.getElementById("Test"+testNumber+arrayOfTests[0]).title = "{ "+arrayOfTests[0]+" Test In Progress}";

			var valueForFile = document.getElementById("Test"+testNumber+"File").value;
			var data = {id: "Test"+testNumber, testName: arrayOfTests[0]};
			var urlForSend = '../core/php/runTest.php?format=json';

			(function(_data){
				$.ajax(
				{
					url: urlForSend,
					dataType: "json",
					data: {filter: arrayOfTests[0], file: valueForFile },
					type: "POST",
					success(data)
					{
						var result = data["Result"];
						document.getElementById(_data["id"]+_data["testName"]).classList.remove("blockInProgress");
						document.getElementById(_data["id"]+_data["testName"]).title = "{"+_data['testName']+" "+data['timeMem']+"}";
						
						if(result === "Passed")
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.add("blockPass");
						}
						else if(result === "Error")
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.add("blockError");
							document.getElementById(_data["id"]+_data["testName"]).title = "{"+_data['testName']+" Errored}";
							console.log(data['output']);
						}
						else if(result === "Failed")
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.add("blockFail");
							document.getElementById(_data["id"]+_data["testName"]).title = "{"+_data['testName']+" Failed}";
							console.log(data['output']);
						}
						else if(result === "Skipped")
						{
							document.getElementById(_data["id"]+_data["testName"]).classList.add("blockSkip");
							document.getElementById(_data["id"]+_data["testName"]).title = "{"+_data['testName']+" Skipped}";
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
			arrayOfTests.shift();
		}
	}
}