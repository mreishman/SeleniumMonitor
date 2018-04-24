function poll()
{
	var urlForSendInner = '../core/php/getFileTimeForAllLogs.php?format=json';
	var dataSend = {};
	$.ajax(
	{
		url: urlForSendInner,
		dataType: "json",
		data: dataSend,
		type: "POST",
		success(data)
		{
			//compare to arrayOfFiles
			var keysInfo = Object.keys(data);
			var keysInfoLength = keysInfo.length;
			for(var i = 0; i < keysInfoLength; i++)
			{
				if(keysInfo[i] in arrayOfFiles)
				{
					//compare
					if(arrayOfFiles[keysInfo[i]] !== data[keysInfo[i]])
					{
						//file is new, update
						arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
						getLogData(keysInfo[i]);
					}
				}
				else
				{
					//not there, add it
					arrayOfFiles[keysInfo[i]] = data[keysInfo[i]];
					getLogData(keysInfo[i]);
				}
			}

			//check oppsite (if any keys are in arrayOfFiles but not data, if so delete from screen and local array)
			keysInfo = Object.keys(arrayOfFiles);
			keysInfoLength = keysInfo.length;
			for(var i = 0; i < keysInfoLength; i++)
			{
				if(!(keysInfo[i] in data))
				{
					//not in data anymore, it was deleted from folder... delete from screen & array
					delete arrayOfFiles[keysInfo[i]];
					if(document.getElementById(keysInfo[i]))
					{
						document.getElementById(keysInfo[i]).outerHTML = "";
					}
					if(document.getElementById(keysInfo[i]+"HotLink"))
					{
						document.getElementById(keysInfo[i]+"HotLink").outerHTML = "";
					}
					if(arrayOfFiles.length === 0)
					{
						document.getElementById("noCachedTests").style.display = "block";
					}
				}
			}
		}
	});
}

function getLogData(path)
{
	var urlForSendInner = '../core/php/getLogInfo.php?format=json';
	var dataSend = {path: "../../tmp/tests/"+path};
	
	(function(_path){
		$.ajax(
		{
			url: urlForSendInner,
			dataType: "json",
			data: dataSend,
			type: "POST",
			success(data)
			{
				renderInfo = JSON.parse(data);
				var item = showRender("subMain", _path, renderInfo, _path, "container");
				if(document.getElementById(_path))
				{
					document.getElementById(_path).outerHTML = item;
				}
				else
				{
					$("#subMain").prepend(item);
					$("#testSidebarUL").prepend("<li id=\""+_path+"HotLink\" style=\"padding: 5px;\"><a href=\"#"+_path+"\" class=\"link\" >"+_path+"</a></li>");
				}
				if(document.getElementById("noCachedTests").style.display !== "none")
				{
					document.getElementById("noCachedTests").style.display = "none";
				}
				resize();
			}
		});
	}(path));

}

function removeCompare(fileName)
{
	//this function removes file from tmp storage
	//show popup first to confirm
	showPopup();
	document.getElementById('popupContentInnerHTMLDiv').innerHTML = "<div class='settingsHeader' >Remove Cache File?</div><br><div style='width:100%;text-align:center;padding-left:10px;padding-right:10px;'>Are you sure you want to remove the file "+fileName+"?</div><div class='link' onclick='actuallyRemoveFile(\""+fileName+"\")' style='margin-left:125px; margin-right:50px;margin-top:25px;'>Yes</div><div onclick='hidePopup();' class='link'>No</div></div>";
}

function actuallyRemoveFile(fileName)
{
	var urlForSendInner = '../core/php/removeFile.php?format=json';
	var dataSend = {file: "../../tmp/tests/"+fileName};
	$.ajax(
		{
			url: urlForSendInner,
			dataType: "json",
			data: dataSend,
			type: "POST",
			success(data)
			{
				poll();
				hidePopup();
			}
		});
}

function renameCompare(fileName)
{
	if(document.getElementById(fileName+"RenameDisplay").style.display === "none")
	{
		document.getElementById(fileName+"RenameDisplay").style.display = "inline-block";
		document.getElementById(fileName+"RenameInput").style.display = "none";
		document.getElementById(fileName+"RenameIcon").style.display = "inline-block";
	}
	else
	{
		document.getElementById(fileName+"RenameDisplay").style.display = "none";
		document.getElementById(fileName+"RenameInput").style.display = "inline-block";
		document.getElementById(fileName+"RenameIcon").style.display = "none";
	}
}

function actuallyRenameCompare(fileName)
{
	displayLoadingPopup();
	var urlForSendInner = '../core/php/renameFile.php?format=json';
	var dataSend = {dir: "../../tmp/tests/", oldName: fileName, newName: document.getElementById(fileName+"RenameInputValue").value};
	$.ajax(
		{
			url: urlForSendInner,
			dataType: "json",
			data: dataSend,
			type: "POST",
			success(data)
			{
				poll();
				hidePopup();
			}
		});
}