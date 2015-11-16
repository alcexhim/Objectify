<?php
	namespace Objectify\WebControls;
	
	use Phast\WebControls\ListView;
	use Phast\WebControls\ListViewColumn;
	use Phast\WebControls\ListViewItem;
	use Phast\WebControls\ListViewItemColumn;
	
	use Objectify\Objects\TenantObject;
	use Phast\System;
	
	class InstanceListView extends ListView
	{
		/**
		 * The ID of the TenantObject for which to view instances.
		 * @var int
		 */
		public $ObjectID;
		/**
		 * The TenantObject for which to view instances.
		 * @var TenantObject
		 */
		public $Object;
		
		protected function RenderContent()
		{
			if ($this->ObjectID != null)
			{
				$this->Object = TenantObject::GetByID(System::ExpandRelativePath($this->ObjectID));
			}
			if ($this->Object == null) return;
			
			$this->Columns = array
			(
				new ListViewColumn("lvcID", "ID")
			);
			
			$insts = $this->Object->GetInstances();
			foreach ($insts as $inst)
			{
				$this->Items[] = new ListViewItem(array
				(
					new ListViewItemColumn("lvcID", $inst->ID)
				));
			}
			
			parent::RenderContent();
		}
	}
?>