<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	
	class ObjectListView extends ListView
	{
		public function __construct()
		{
			parent::__construct();
			
			$this->Columns = array
			(
				new ListViewColumn("lvcID", "ID"),
				new ListViewColumn("lvcObject", "Object"),
				new ListViewColumn("lvcInstances", "Instances")
			);
			
			$objects = TenantObject::Get();
			foreach ($objects as $obj)
			{
				$this->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $obj->ID),
					new ListViewItemColumn("lvcObject", "<a href=\"/objects/modify/" . $obj->ID . "\">" . $obj->Name . "</a>", $obj->Name),
					new ListViewItemColumn("lvcInstances", $obj->CountInstances())
				));
			}
		}
	}
?>