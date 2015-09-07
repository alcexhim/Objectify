function Tenant()
{
	this.ID = null;
	this.Name = null;
	this.Description = null;
	
	this.Update = function(callback)
	{
		var xhr = new XMLHttpRequest();
		var url = "~/api/tenant";
		xhr.open("POST", System.ExpandRelativePath(url));
		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4)
			{
				var obj = JSON.parse(xhr.responseText);
				if (obj.Result == "Success")
				{
					callback(xhr, new TenantEventArgs(true, this));
				}
			}
		}
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		
		var params =
		[
		 	{ "Name": "tenant_ID", "Value": this.ID },
		 	{ "Name": "tenant_Name", "Value": this.Name },
		 	{ "Name": "tenant_Description", "Value": this.Description }
		];
		
		var strParams = "";
		for (var i = 0; i < params.length; i++)
		{
			strParams += encodeURIComponent(params[i].Name);
			strParams += "=";
			strParams += encodeURIComponent(params[i].Value);
			
			if (i < params.length - 1) strParams += "&";
		}
		
		xhr.send(strParams);
	};
}

function TenantEventArgs(success, item)
{
	this.Success = success;
	this.Tenant = item;
}

Tenant.GetByAssoc = function(obj)
{
	var item = new Tenant();
	item.ID = obj.ID;
	item.Name = obj.URL;
	item.Description = obj.Description;
	return item;
};
Tenant.GetByID = function(tenant_ID, callback)
{
	var xhr = new XMLHttpRequest();
	var url = "~/api/tenant?action=retrieve&tenant_ID=" + tenant_ID;
	xhr.open("GET", System.ExpandRelativePath(url));
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				var item = Tenant.GetByAssoc(obj.Items[0]);
				callback(xhr, new TenantEventArgs(true, item));
			}
			else
			{
				callback(xhr, new TenantEventArgs(false));
			}
		}
	};
	xhr.send(null);
};
Tenant.GetByURL = function(tenant_URL, callback)
{
	var xhr = new XMLHttpRequest();
	var url = "~/api/tenant?action=retrieve&tenant_URL=" + tenant_URL;
	xhr.open("GET", System.ExpandRelativePath(url));
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				var item = Tenant.GetByAssoc(obj.Items[0]);
				callback(xhr, new TenantEventArgs(true, item));
			}
			else
			{
				callback(xhr, new TenantEventArgs(false));
			}
		}
	};
	xhr.send(null);
};