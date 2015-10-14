window.addEventListener("load", function()
{
	var cmdCustomPropertiesAdd = document.getElementById("cmdCustomPropertiesAdd");
	cmdCustomPropertiesAdd.addEventListener("click", function(e)
	{
		wndPropertyDetails.ShowDialog();
	});
	
	var wndPropertyDetails_cmdSaveChanges = document.getElementById("wndPropertyDetails_cmdSaveChanges");
	wndPropertyDetails_cmdSaveChanges.addEventListener("click", function(e)
	{
		wndPropertyDetails.Hide();
	});

	var wndPropertyDetails_cmdDiscardChanges = document.getElementById("wndPropertyDetails_cmdDiscardChanges");
	wndPropertyDetails_cmdDiscardChanges.addEventListener("click", function(e)
	{
		wndPropertyDetails.Hide();
	});
	
	var txtPropertyDataType = document.getElementById("txtPropertyDataType").NativeObject;
	txtPropertyDataType.EventHandlers.SelectionChanged.Add(function(sender, e)
	{
		var value = txtPropertyDataType.GetSelectedItems()[0].Value;
		switch (value)
		{
			case "Text":
			{
				break;
			}
			case "TranslatableText":
			{
				break;
			}
			case "SingleInstance":
			{
				break;
			}
			case "MultipleInstance":
			{
				break;
			}
		}
	});
	
	var cmdSaveChanges = document.getElementById("cmdSaveChanges");
	cmdSaveChanges.addEventListener("click", function(e)
	{
		var txtTenantName = document.getElementById("txtTenantName");
		var txtTenantDescription = document.getElementById("txtTenantDescription");

		CurrentTenant.Name = txtTenantName.value;
		CurrentTenant.Description = txtTenantDescription.value;
		CurrentTenant.Update(function(sender, e)
		{
			if (e.Success)
			{
				alert("Changes were saved successfully");
			}
			else
			{
				alert(e.Message);
			}
		});
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	
	var path = window.location.pathname.split('/');
	var tenantName = path[path.length - 1];
	Tenant.GetByURL(tenantName, function(sender, e)
	{
		window.CurrentTenant = e.Tenant;
	});
});