function TenantObjectEventArgs(success, items)
{
	this.Success = success;
	this.Items = items;
}

function TenantObject()
{
	this.ID = null;
	this.Name = null;
	this.Description = null;
	
	this.CreateInstance = function(parameters, callback)
	{
		var xhr = new XMLHttpRequest();
		var url = "~/API/TenantObjectInstance?Action=Create&ParentObjectID=" + this.ID;
		xhr.open("POST", System.ExpandRelativePath(url));
		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4)
			{
				var obj = JSON.parse(xhr.responseText);
				if (obj.Result == "Success")
				{
					if (typeof(callback) === "function")
					{
						callback(xhr, new TenantObjectInstanceCreatedEventArgs(true, null));
					}
					else
					{
						console.warn("TenantObject->CreateInstance(): no callback specified for asynchronous method call");
					}
				}
				else
				{
					if (typeof(callback) === "function")
					{
						callback(xhr, new TenantObjectInstanceCreatedEventArgs(false, null));
					}
					else
					{
						console.warn("TenantObject->CreateInstance(): no callback specified for asynchronous method call");
					}
				}
			}
		};
		
		var paramstr = "";
		if (typeof(parameters) !== "undefined")
		{
			paramstr += "ParamCount=" + parameters.length.toString();
			for (var i = 0; i < parameters.length; i++)
			{
				paramstr += "&";
				paramstr += "Param" + i.toString() + "Name=" + parameters[i].Name + "&";
				paramstr += "Param"+ i.toString() + "Value=" + parameters[i].Value;
			}
		}
		
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.send(paramstr);
	};
	this.GetInstances = function(callback)
	{
		var xhr = new XMLHttpRequest();
		var url = "~/API/TenantObjectInstance?Action=Retrieve&ParentObjectID=" + this.ID;
		xhr.open("GET", System.ExpandRelativePath(url));
		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4)
			{
				var obj = JSON.parse(xhr.responseText);
				if (obj.Result == "Success")
				{
					var items = new Array();
					for (var i = 0; i < obj.Items.length; i++)
					{
						var item = TenantObjectInstance.GetByAssoc(obj.Items[i]);
						items.push(item);
					}
					callback(xhr, new TenantObjectInstanceEventArgs(true, items));
				}
				else
				{
					callback(xhr, new TenantObjectInstanceEventArgs(false));
				}
			}
		};
	};
	
	this.Update = function(callback)
	{
		var xhr = new XMLHttpRequest();
		var url = "~/API/TenantObject";
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

TenantObject.GetByAssoc = function(obj)
{
	var item = new TenantObject();
	item.ID = obj.ID;
	item.Name = obj.Name;
	return item;
};
TenantObject.Get = function(callback)
{
	var xhr = new XMLHttpRequest();
	var url = "~/API/TenantObject?Action=Retrieve";
	xhr.open("GET", System.ExpandRelativePath(url));
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				var items = new Array();
				for (var i = 0; i < obj.Items.length; i++)
				{
					var item = TenantObject.GetByAssoc(obj.Items[i]);
					items.push(item);
				}
				callback(xhr, new TenantObjectEventArgs(true, items));
			}
			else
			{
				callback(xhr, new TenantObjectEventArgs(false));
			}
		}
	};
	xhr.send(null);
};
TenantObject.GetByID = function(id, callback)
{
	var xhr = new XMLHttpRequest();
	var url = "~/API/TenantObject?Action=Retrieve&ID=" + id;
	xhr.open("GET", System.ExpandRelativePath(url));
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				var item = TenantObject.GetByAssoc(obj.Items[0]);
				callback(xhr, new TenantObjectEventArgs(true, [item]));
			}
			else
			{
				callback(xhr, new TenantObjectEventArgs(false));
			}
		}
	};
	xhr.send(null);
};
TenantObject.GetByName = function(name, callback)
{
	var xhr = new XMLHttpRequest();
	var url = "~/API/TenantObject?Action=Retrieve&Name=" + name;
	xhr.open("GET", System.ExpandRelativePath(url));
	xhr.onreadystatechange = function()
	{
		if (xhr.readyState == 4)
		{
			var obj = JSON.parse(xhr.responseText);
			if (obj.Result == "Success")
			{
				var item = TenantObject.GetByAssoc(obj.Items[0]);
				callback(xhr, new TenantObjectEventArgs(true, [item]));
			}
			else
			{
				callback(xhr, new TenantObjectEventArgs(false));
			}
		}
	};
	xhr.send(null);
};