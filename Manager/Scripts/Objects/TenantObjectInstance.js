function TenantObjectInstanceEventArgs(success, items)
{
	this.Success = success;
	this.Items = items;
}
function TenantObjectInstanceCreatedEventArgs(success, inst)
{
	this.Success = success;
	this.Instance = inst;
}

function TenantObjectInstance()
{
	this.ParentObjectID = null;
	this.GetParentObject = function(callback)
	{
		TenantObject.GetByID(this.ParentObjectID, callback);
	};
	this.ID = null;
	this.DisplayTitle = null;
	this.GlobalIdentifier = null;
	this.GetInstanceID = function()
	{
		return this.ParentObjectID + "$" + this.ID;
	};
	this.ToString = function()
	{
		return this.DisplayTitle;
	};
}
TenantObjectInstance.GetByAssoc = function(obj)
{
	var item = new TenantObjectInstance();
	item.ParentObjectID = obj.ParentObject.ID;
	item.ID = obj.ID;
	item.GlobalIdentifier = obj.GlobalIdentifier;
	item.DisplayTitle = obj.DisplayTitle;
	return item;
};