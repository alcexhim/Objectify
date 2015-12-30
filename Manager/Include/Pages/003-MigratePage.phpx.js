window.addEventListener("load", function(e)
{
	var FormView_fv_txtSource_SpecificPackages_Packages = document.getElementById("FormView_fv_txtSource_SpecificPackages_Packages");
	var FormView_fv_txtSource_EntireTenant_TenantName = document.getElementById("FormView_fv_txtSource_EntireTenant_TenantName");
	var FormView_fv_txtDestination_NewTenant_TenantName = document.getElementById("FormView_fv_txtDestination_NewTenant_TenantName");
	var FormView_fv_txtDestination_ExistingTenant_TenantName = document.getElementById("FormView_fv_txtDestination_ExistingTenant_TenantName");
	
	FormView_fv_txtSource_SpecificPackages_Packages.style.display = "none";
	FormView_fv_txtSource_EntireTenant_TenantName.style.display = "none";
	FormView_fv_txtDestination_NewTenant_TenantName.style.display = "none";
	FormView_fv_txtDestination_ExistingTenant_TenantName.style.display = "none";
	
	var cboSource = document.getElementById("cboSource").NativeObject;
	cboSource.EventHandlers.SelectionChanged.Add(function(sender)
	{
		var item = sender.GetSelectedItems()[0];
		switch (item.Value)
		{
			case "1":
			{
				FormView_fv_txtSource_SpecificPackages_Packages.style.display = "table-row";
				FormView_fv_txtSource_EntireTenant_TenantName.style.display = "none";
				break;
			}
			case "2":
			{
				FormView_fv_txtSource_SpecificPackages_Packages.style.display = "none";
				FormView_fv_txtSource_EntireTenant_TenantName.style.display = "table-row";
				break;
			}
		}
	});
	
	var cboDestination = document.getElementById("cboDestination").NativeObject;
	cboDestination.EventHandlers.SelectionChanged.Add(function(sender)
	{
		var item = sender.GetSelectedItems()[0];
		switch (item.Value)
		{
			case "1":
			{
				FormView_fv_txtDestination_NewTenant_TenantName.style.display = "table-row";
				FormView_fv_txtDestination_ExistingTenant_TenantName.style.display = "none";
				break;
			}
			case "2":
			{
				FormView_fv_txtDestination_NewTenant_TenantName.style.display = "none";
				FormView_fv_txtDestination_ExistingTenant_TenantName.style.display = "table-row";
				break;
			}
		}
	});
});