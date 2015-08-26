function wndCreateTenant_cmdCreate_Click()
{
	var xhr = new XMLHttpRequest();
	xhr.open('POST', System.ExpandRelativePath('~/api/tenant'), true);
	xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			wndCreateTenant.SetLoading(false);

			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				wndCreateTenant.Hide();
				
				// TODO: refresh ListView
			}
			else
			{
				alert(obj.Message);
			}
		}
	};
	
	var txtTenantName = document.getElementById("txtTenantName");
	
	var values =
	[
	 	{
	 		"Name": "Action",
	 		"Value": "Create"
	 	},
	 	{
	 		"Name": "tenant_URL",
	 		"Value": txtTenantName.value
	 	}
	];
	
	var strValues = "";
	for (var i = 0; i < values.length; i++)
	{
		strValues += encodeURI(values[i].Name) + '=' + encodeURI(values[i].Value);
		if (i < values.length - 1) strValues += '&';
	}

	wndCreateTenant.SetLoading(true);
	
	xhr.send(strValues);
	return false;
}
