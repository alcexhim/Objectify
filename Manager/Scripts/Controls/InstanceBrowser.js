function InstanceBrowser(parentElement)
{
	this.ParentElement = parentElement;
	this.TextBoxObject = this.ParentElement.NativeObject;
	
	this.TextBoxObject.EventHandlers.DropDownOpening.Add(function(sender, e)
	{
		sender.Items.Clear();
		
		var validObjects = sender.ParentElement.getAttribute("data-valid-objects");
		if (validObjects != null)
		{
			var splt = validObjects.split(',');
			for (var i = 0; i < splt.length; i++)
			{
				TenantObject.GetByID(splt[i], function(sender1, e1)
				{
					e1.Items[0].GetInstances(function(sender2, e2)
					{
						for (var j = 0; j < e2.Items.length; j++)
						{
							sender.Items.Add({ "Title": e2.Items[j].GlobalIdentifier, "Value": e2.Items[j].ID });
						}
					});
				});
			}
		}
	});
}

window.addEventListener("load", function()
{
	var items = document.getElementsByClassName("InstanceBrowser");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject_InstanceBrowser = new InstanceBrowser(items[i]);
	}
});