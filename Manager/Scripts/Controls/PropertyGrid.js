function PropertyGridProperty(parentElement)
{
	this.ParentElement = parentElement;
	this.TitleElement = this.ParentElement.children[0];
	this.ValueElement = this.ParentElement.children[1];

	var ecid = this.TitleElement.getAttribute("data-instance-id");
	
	this.__ShowContextMenu = function(e)
	{
		var menu = new ContextMenu();
		menu.Items.push(new MenuItemCommand("ShowFieldProperties", "Show Field Properties", function()
		{
		}));
		menu.Items.push(new MenuItemCommand("ShowFieldEC", "Show Field EC", function()
		{
			window.open(System.ExpandRelativePath("~/instances/modify/" + ecid));
		}));
		
		menu.Show(e.clientX, e.clientY, this.ParentElement);
		
		e.preventDefault();
		e.stopPropagation();
		return false;
	};
	
	this.TitleElement.addEventListener("contextmenu", this.__ShowContextMenu);
}
function PropertyGrid(parentElement)
{
	this.ParentElement = parentElement;
	
	for (var i = 0; i < this.ParentElement.children.length; i++)
	{
		this.ParentElement.children[i].NativeObject = new PropertyGridProperty(this.ParentElement.children[i]);
	}
}

window.addEventListener("load", function(e)
{
	items = document.getElementsByClassName("PropertyGrid");
	for (var i = 0; i < items.length; i++)
	{
		items[i].NativeObject = new PropertyGrid(items[i]);
	}
});