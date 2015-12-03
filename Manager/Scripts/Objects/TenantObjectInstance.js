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
	this.GlobalIdentifier = null;
}
TenantObjectInstance.GetByAssoc = function(obj)
{
	var item = new TenantObject();
	item.ParentObjectID = obj.ParentObject.ID;
	item.ID = obj.ID;
	item.GlobalIdentifier = obj.GlobalIdentifier;
	return item;
};