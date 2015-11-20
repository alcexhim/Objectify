function InstanceDisplayWidget(parentElement)
{
	this.ParentElement = parentElement;
	this.ADWElement = this.ParentElement.children[0];
	this.TextElement = this.ADWElement.children[0];
	this.ButtonElement = this.ADWElement.children[1];
	
	this.ButtonElement;
	this.TextElement;
	
	var odwParent = this;
	
	this.__ShowContextMenu = function(e)
	{
		var menu = new ContextMenu();
		menu.Items.push(new MenuItemCommand("SeeInNewTab", "See in New Tab", function()
		{
			window.open(odwParent.TextElement.href);
		}));
		menu.Items.push(new MenuItemCommand("CopyURL", "Copy URL", function()
		{
			alert(odwParent.TextElement.href);
		}));
		menu.Items.push(new MenuItemCommand("CopyText", "Copy Text", function()
		{
			alert(odwParent.TextElement.innerHTML);
		}));
		menu.Items.push(new MenuItemSeparator());
		menu.Items.push(new MenuItemCommand("CopyInstanceID", "Copy Instance ID", function()
		{
			
		}));
		menu.Items.push(new MenuItemCommand("CopyTextAndInstanceID", "Copy Text and Instance ID", function()
		{
			
		}));
		menu.Items.push(new MenuItemSeparator());
		menu.Items.push(new MenuItemCommand("ModifyInstance", "Modify Instance", function()
		{
			
		}));
		menu.Items.push(new MenuItemCommand("ModifyInstanceInNewWindow", "Modify Instance in New Window", function()
		{
			
		}));
		menu.Items.push(new MenuItemSeparator());
		menu.Items.push(new MenuItemCommand("SearchInstanceID", "Search Instance ID", function()
		{
			
		}));
		menu.Items.push(new MenuItemCommand("SearchInstanceIDInNewWindow", "Search Instance ID in New Window", function()
		{
			
		}));
		menu.Items.push(new MenuItemSeparator());
		menu.Items.push(new MenuItemCommand("ViewPrintableVersion", "View Printable Version", function()
		{
			
		}));
		menu.Items.push(new MenuItemCommand("ExportToSpreadsheet", "Export to Spreadsheet", function()
		{
			
		}));
		
		if (typeof(ZeroClipboard) === 'undefined')
		{
			console.warn("ZeroClipboard not found - cut/copy menu items will be unavailable");
			menu.Items[1].Visible = false;
			menu.Items[2].Visible = false;
			menu.Items[3].Visible = false;
			menu.Items[4].Visible = false;
			menu.Items[5].Visible = false;
		}
		
		
		menu.Show(e.clientX, e.clientY, odwParent.ParentElement);
		
		e.preventDefault();
		return false;
	};
	this.ButtonElement.addEventListener("contextmenu", this.__ShowContextMenu);
	this.TextElement.addEventListener("contextmenu", this.__ShowContextMenu);
}

window.addEventListener("load", function(e)
{
	items = document.getElementsByClassName("InstanceDisplayWidget");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject = new InstanceDisplayWidget(items[i]);
	}
});