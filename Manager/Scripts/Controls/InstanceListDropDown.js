function InstanceListDropDown(parentElement)
{
	this.ParentElement = parentElement;
	this.ParentElement.addEventListener("click", function(e)
	{
		var rowInstID = this.getAttribute("data-row-instance-id");
		var fieldInstID = this.getAttribute("data-field-instance-id");
		
		TenantObjectInstance.GetByInstanceID(rowInstID, function(sender, e)
		{
			TenantObject.GetByGlobalIdentifier(e.Items[0].GlobalIdentifier, function(sender1, e1)
			{
				var obj = e1.Items[0];
				
				obj.GetInstances(function(sender2, e2)
				{
					alert("TODO: make this display a popup with " + e2.Items.length + " instances in the list");
				});
			});
		});
		
		e.preventDefault();
		e.stopPropagation();
	});
}

window.addEventListener("load", function()
{
	var items = document.getElementsByClassName("InstanceListDropDown");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject = new InstanceListDropDown(items[i]);
	}
});