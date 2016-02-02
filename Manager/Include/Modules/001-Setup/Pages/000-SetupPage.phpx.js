window.addEventListener("load", function(e)
{
	var cmdContinue = document.getElementById("cmdContinue");
	var frmMain = document.getElementById("frmMain");
	frmMain.addEventListener("submit", function(e)
	{
		System.ClassList.Add(document.body, "Loading");
		cmdContinue.setAttribute("disabled", "disabled");
		
		var xhr = new XMLHttpRequest();
		xhr.open("POST", System.ExpandRelativePath("~/setup"), true);
		xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		xhr.onreadystatechange = function()
		{
			if (xhr.readyState == 4)
			{
				var obj = JSON.parse(xhr.responseText);
				if (obj.Result == "Success")
				{
					System.Redirect("~/");
					return;
				}
				else
				{
					System.ClassList.Remove(document.body, "Loading");
					alert(obj.Message);
				}
				cmdContinue.removeAttribute("disabled");
			}
		};
		
		var items =
		[
		 	{
		 		"Name": "Database_ServerName",
		 		"Value": document.getElementById("txtDatabaseServerName").value
		 	},
		 	{
		 		"Name": "Database_DatabaseName",
		 		"Value": document.getElementById("txtDatabaseDatabaseName").value
		 	},
		 	{
		 		"Name": "Database_UserName",
		 		"Value": document.getElementById("txtDatabaseUserName").value
		 	},
		 	{
		 		"Name": "Database_Password",
		 		"Value": document.getElementById("txtDatabasePassword").value
		 	},
		 	{
		 		"Name": "Database_TablePrefix",
		 		"Value": document.getElementById("txtDatabaseTablePrefix").value
		 	},
		 	{
		 		"Name": "Administrator_UserName",
		 		"Value": document.getElementById("txtAdministratorUserName").value
		 	},
		 	{
		 		"Name": "Administrator_Password",
		 		"Value": document.getElementById("txtAdministratorPassword").value
		 	}
		];
		
		var itemsStr = "";
		for (var i = 0; i < items.length; i++)
		{
			itemsStr += items[i].Name + "=" + items[i].Value;
			if (i < items.length - 1) itemsStr += "&";
		}
		itemsStr = encodeURI(itemsStr);
		
		xhr.send(itemsStr);
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	});
});