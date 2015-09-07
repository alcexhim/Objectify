window.addEventListener("load", function()
{
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