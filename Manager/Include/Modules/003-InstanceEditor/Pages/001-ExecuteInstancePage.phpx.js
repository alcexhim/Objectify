window.addEventListener("load", function()
{
	var cmdSubmit = document.getElementById("cmdSubmit");
	var cmdCancel = document.getElementById("cmdCancel");
	
	cmdSubmit.addEventListener("click", function(e)
	{
		var pathParts = Page.Path.GetParts();
		if (pathParts.length == 3)
		{
			var url = window.location.href;
			if (System.StringMethods.Contains(url, "#"))
			{
				var i = url.indexOf('#');
				url = url.substring(0, i);
			}
			
			var paramStr = "{";
			
			paramStr += "\"Parameters\": [";
			var fv = document.getElementById("fvPrompts");
			

			var requiredFields = [];
			for (var i = 0; i < fv.children.length; i++)
			{
				var title = fv.children[i].children[0].children[0].innerHTML;
				if (fv.children[i].getAttribute("data-required") == "true")
				{
					var txt = fv.children[i].children[1];
					if (!txt.selectedItems)
					{
						requiredFields.push(title);
						continue;
					}
				}
			}
			
			if (requiredFields.length > 0)
			{
				var reqFieldText = "<ul>";
				for (var i = 0; i < requiredFields.length; i++)
				{
					reqFieldText += "<li>" + requiredFields[i] + "</li>";
				}
				reqFieldText += "</ul>";
				
				Window.ShowDialog("<p>The following fields are required and must have a value:</p>" + reqFieldText, "Required parameter(s) missing", null, null, null, "Warning");
				return;
			}
			
			for (var i = 0; i < fv.children.length; i++)
			{
				var p = "{\"Name\": \"" + fv.children[i].id + "\", \"Value\":";
				
				var txt = fv.children[i].children[1];
				if (txt.selectedItems)
				{
					p += "[";
					for (var j = 0; j < txt.selectedItems.length; j++)
					{
						p += "\"" + txt.selectedItems[j].Value + "\"";
						if (j < txt.selectedItems.length - 1)
						{
							p += ",";
						}
					}
					p += "]";
				}
				else
				{
					p += "null";
				}
				p += "}";
				
				if (i < fv.children.length - 1)
				{
					p += ",";
				}
				paramStr += p;
			}
			paramStr += "]";
			
			paramStr += "}";
	
			url += "/" + Base64.encode(paramStr);
			
			window.location.href = url;
		}
		else
		{
			var paramStr = pathParts[3];
			var jsonstr = Base64.decode(paramStr);
			var json = JSON.parse(jsonstr);
		}
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	cmdCancel.addEventListener("click", function(e)
	{
		history.back();
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
	

	var pathParts = Page.Path.GetParts();
	if (pathParts.length > 3)
	{
		var paramStr = pathParts[3];
		var jsonstr = Base64.decode(paramStr);
		var json = JSON.parse(jsonstr);
	}
});