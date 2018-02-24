var titleOfPage = "Main";
	
function showOrHidePopupSubWindow()
{
	try
	{
		var valueForPopup = document.getElementById("popupSelect");
		var valueForVars = document.getElementById("settingsPopupVars");
		showOrHideSubWindow(valueForPopup, valueForVars);
	}
	catch(e)
	{
		eventThrowException(e);
	}
}
function showOrHideUpdateSubWindow()
{
	try
	{
		var valueForPopup = document.getElementById("settingsSelect");
		var valueForVars = document.getElementById("settingsAutoCheckVars");
		showOrHideSubWindow(valueForPopup, valueForVars);
	}
	catch(e)
	{
		eventThrowException(e);
	}
}
function showOrHideSubWindow(valueForPopupInner, valueForVarsInner)
{
	try
	{
		if((valueForPopupInner.value === "true") || (valueForPopupInner.value === "custom"))
		{
			valueForVarsInner.style.display = "block";
		}
		else
		{
			valueForVarsInner.style.display = "none";
		}
	}
	catch(e)
	{
		eventThrowException(e);
	}
}

function checkIfChanges()
{
	if(	checkForChangesArray(["settingsMainVars","settingsRunVars","settingsViewVars","settingsCacheVars"]))
	{
		return true;
	}
	return false;
}

function clearAllTestCache()
{
	displayLoadingPopup();
	var urlForSendInner = '../core/php/removeAllTmpTests.php?format=json';
	var dataSend = {dir: "../../tmp/tests/"};
	$.ajax(
		{
			url: urlForSendInner,
			dataType: "json",
			data: dataSend,
			type: "POST",
			success(data)
			{
				hidePopup();
			}
		});
}

$( document ).ready(function() 
{
	document.getElementById("popupSelect").addEventListener("change", showOrHidePopupSubWindow, false);
	document.getElementById("settingsSelect").addEventListener("change", showOrHideUpdateSubWindow, false);

	refreshArrayObjectOfArrays(["settingsMainVars","settingsRunVars","settingsViewVars","settingsCacheVars"]);
	setInterval(poll, 100);
});