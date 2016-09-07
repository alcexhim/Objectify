window.addEventListener("load", function(e)
{
	var FormView_fv_txtSource_SpecificPackages_Packages = document.getElementById("FormView_fv_txtSource_SpecificPackages_Packages");
	
	FormView_fv_txtSource_SpecificPackages_Packages.style.display = "none";
	
	var cboSource = document.getElementById("cboSource").NativeObject;
	cboSource.EventHandlers.SelectionChanged.Add(function(sender)
	{
		var item = sender.GetSelectedItems()[0];
		switch (item.Value)
		{
			case "1":
			{
				FormView_fv_txtSource_SpecificPackages_Packages.style.display = "table-row";
				break;
			}
			case "2":
			{
				FormView_fv_txtSource_SpecificPackages_Packages.style.display = "none";
				break;
			}
		}
	});
	
	var txtDestination = document.getElementById("txtDestination");
	
	var cmdOK = document.getElementById("cmdOK");
	cmdOK.addEventListener("click", function()
	{
		var reqmissing = [];
		if (cboSource.GetSelectedItems().length == 0)
		{
			reqmissing.push("Source");
		}
		if (txtDestination.value == "")
		{
			reqmissing.push("Destination Tenant Name");
		}
		
		if (reqmissing.length > 0)
		{
			var strreqmissing = "<ul>";
			for (var i = 0; i < reqmissing.length; i++)
			{
				strreqmissing += "<li>" + reqmissing[i] + "</li>";
			}
			strreqmissing += "</ul>";
			
			Notification.Show(strreqmissing, "Required information missing", "Danger");
			
			e.preventDefault();
			e.stopPropagation();
			return false;
		}
		
		var mainForm = document.getElementById("mainForm");
		
		System.ClassList.Add(document.body, "Loading");
		mainForm.submit();
	});
});