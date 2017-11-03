var testNumber = 1;

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
				testsHtml += "<form id='testsListForm'><ul class='list'>";
				for (var i = tests.length - 1; i >= 0; i--) {
					testsHtml += "<li><input type='checkbox' checked name='"+tests[i]+"'>"+tests[i]+"</li>";
				}
				testsHtml += "</ul></form><br><button onclick='runTests();'> Run Tests </button>";
			}
			document.getElementById("testsPlaceHolder").innerHTML = testsHtml;
		}
	});
}

function runTests()
{

}

function showStartTestNewPopup()
{
	var item = $("#storage .newTestPopup").html();
	item = item.replace(/{{id}}/g, "Test"+testNumber);
	$("#main").append(item);
}