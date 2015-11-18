<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Phast\WebControls\AdditionalDetailWidget;
	
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
				new ListViewColumn("lvcParentObjects", "Parent object(s)"),
				new ListViewColumn("lvcInstances", "Instances")
			);
			
			$objects = TenantObject::Get();
			foreach ($objects as $obj)
			{
				$this->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $obj->ID),
					new ListViewItemColumn("lvcObject", function($col)
					{
						$adw = new ObjectDisplayWidget($col->ExtraData);
						$adw->Render();
					}, $obj->Name, $obj),
					new ListViewItemColumn("lvcParentObjects", function($col)
					{
						$obj = $col->ExtraData;
						$objs = $obj->GetParentObjects();
						foreach ($objs as $obj1)
						{
							$adw = new ObjectDisplayWidget($obj1);
							$adw->Render();
							echo("<br />");
						}
					}, null, $obj),
					new ListViewItemColumn("lvcInstances", $obj->CountInstances())
				));
			}
		}
	}
?>